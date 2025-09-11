<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @package   FME_CurrencySwitcher
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */ 
namespace FME\CurrencySwitcher\Controller\Adminhtml\Import;

/**
 * Class Import
 */
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Import extends Action
{
    /**
     * @param Context $context
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \FME\CurrencySwitcher\Helper\Data $helper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @return void
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \FME\CurrencySwitcher\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->_geoipdefaultHelper = $helper;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_resource = $resource;
        $this->_pageFactory = $pageFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }
    /**
     * @return $resultRedirect | void
     */
    public function execute()
    {
        $resultPage = $this->_pageFactory->create();
        $resultPage->setActiveMenu('FME_CurrencySwitcher::main_menu');
        $resource = $this->_resource;
        $csvFilePath = $this->_geoipdefaultHelper->prepareCsv('GeoLite2-Country-Blocks-IPv4');
        $CsvPathCountry = $this->_geoipdefaultHelper->prepareCsv('GeoLite2-Country-Locations-en');
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // check if file exists
            if (!file_exists($csvFilePath)) {
                $this->messageManager->addError(__('File GeoLite2-Country-Blocks-IPv4.csv does not exist!'));
                $resultRedirect->setUrl($this->_url->getUrl('adminhtml/system_config/edit/section/geo'));
                return $resultRedirect;
            }

            if (!file_exists($CsvPathCountry)) {
                $this->messageManager->addError(__('File GeoLite2-Country-Locations-en.csv does not exist!'));
                $resultRedirect->setUrl('adminhtml/system_config/edit/section/geo');
                return $resultRedirect;
            }
            if (($handle = fopen($csvFilePath, 'r')) !== false) {
                while (($row = fgetcsv($handle, 328888, ",")) !== false) {
                    $sql[] = $row[0] . ',' . $row[1];
                    unset($row);
                }
                fclose($handle);
            }
            if (($handle = fopen($CsvPathCountry, 'r')) !== false) {
                while (($row = fgetcsv($handle, 253, ",")) !== false) {
                    $sqll[] = $row[0] . ',' . $row[4]. ',' . $row[5];
                    unset($row);
                }
                fclose($handle);
            }
            for($i=1; $i<count($sql); $i++){
                $sqlArry[]=explode(',',$sql[$i]);
            }
            for($j=1; $j<count($sqll); $j++){
                $sqllArray[]=explode(',',$sqll[$j]);
            }
            for($i=0; $i<count($sqlArry); $i++){
                for($j=0; $j<count($sqllArray); $j++){
                    if($sqlArry[$i][1] == $sqllArray[$j][0]){
                        $newArray[]=$sqlArry[$i][0].','.$sqllArray[$j][1].','.$sqllArray[$j][2];
                    }
                }
            }
            for($i=0; $i<count($newArray); $i++){
                $bc=explode(',', $newArray[$i]);
                $ac=explode('/', $bc[0]);
                $ip_from= long2ip(ip2long($ac[0])& (-1<<(32-$ac[1])));
                $ip_to= long2ip(ip2long($ac[0])| (~(-1<<(32-$ac[1]))));
                $finlArry[]="('".$ip_from."','".$ip_to."','".ip2long($ip_from)."','".ip2long($ip_to)."','".$bc[1]."','".$bc[2]."')";   
            }
            $splitArray=array_chunk($finlArry, 1000, true);
            $write = $resource->getConnection('core_write');
            $write->truncateTable($resource->getTableName('geoip_csv'));
            $write->truncateTable($resource->getTableName('geoip_cl'));
            $write->truncateTable($resource->getTableName('geoip_ip'));
            try {
                for ($i=0; $i<count($splitArray); $i++) {
                    $write->beginTransaction();
                    $query = "INSERT INTO " . $resource->getTableName('geoip_csv')
                                            . " (start_ip, end_ip, start, end, cc, cn) "
                                            . " VALUES ". implode(',', $splitArray[$i]);
                    $write->query($query);
                    $write->commit();
                }
            } catch (\Exception $e) {
                $write->rollBack();
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setUrl($this->_url->getUrl('adminhtml/system_config/edit/section/geo'));
                return $resultRedirect;
            }

            try {
                $write->beginTransaction();
                $write->query("
                    INSERT INTO " . $resource->getTableName('geoip_cl')
                            . " SELECT DISTINCT NULL, cc, cn FROM " . $resource->getTableName('geoip_csv'));

                $write->query("
                    INSERT INTO " . $resource->getTableName('geoip_ip')
                            . " SELECT start, end, ci FROM " . $resource->getTableName('geoip_csv')
                            . " NATURAL JOIN " . $resource->getTableName('geoip_cl') . ";
                    ");
                $write->commit();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $write->rollBack();
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setUrl($this->_url->getUrl('adminhtml/system_config/edit/section/geo'));
                return $resultRedirect;
            }
            $this->messageManager->addSuccess(__('Number of imported records: ' . count($newArray)));
            $resultRedirect->setUrl($this->_url->getUrl('adminhtml/system_config/edit/section/geo'));
            return $resultRedirect;
        }catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $resultRedirect->setUrl($this->_url->getUrl('adminhtml/system_config/edit/section/geo'));
            return $resultRedirect;
        }
    }
}
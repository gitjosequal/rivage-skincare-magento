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
* @category FME
* @package FME_CurrencySwitcher
* @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
* @license https://fmeextensions.com/LICENSE.txt
*/
namespace FME\CurrencySwitcher\Observer;
/**
 * Class DispatchObserver
 */
class DispatchObserver implements \Magento\Framework\Event\ObserverInterface
{
    private \FME\CurrencySwitcher\Helper\Data $_geoipHelper;
    private \Magento\Store\Model\StoreManagerInterface $_storeManager;
    private \Magento\Store\Model\Group $_activeGroup;
    private \Magento\Framework\View\Result\LayoutFactory $_layoutFactory;
    private \Magento\Framework\App\Http\Context $_httpContext;
    private \Magento\Framework\Session\SessionManagerInterface $_coreSession;

    /**
     * __construct
     * @param \FME\CurrencySwitcher\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Result\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @return void
     */
    public function __construct(
        \FME\CurrencySwitcher\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\LayoutFactory $layoutFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Http\Context $httpContext
    )
    {
        $this->_geoipHelper = $helper;
        $this->_storeManager = $storeManager;
        $this->_activeGroup = $this->_storeManager->getGroup();
        $this->_layoutFactory = $layoutFactory;
        $this->_httpContext = $httpContext;
        $this->_coreSession = $coreSession;
    }
    /**
     * execute
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //$isEnabledCloudFlare=$this->_geoipHelper->isEnabledCloudFlare();
        $this->_coreSession->start();
        if ($this->_geoipHelper->isEnabled() != 1) {
            return;
        } else {
            $selectedCurrency=$this->_geoipHelper->currencyCountry();
            //$isEnabledRoundPrices=$this->_geoipHelper->isEnabledRoundPrices();
            //$isEnabledBaseRoundPrices=$this->_geoipHelper->isEnabledBaseRoundPrices();
            //$isRoundPricesAlgo=$this->_geoipHelper->isRoundPricesAlgo();
            //$isDisableBot=$this->_geoipHelper->isDisableBot();
            if ($this->_coreSession->getCountVariable() == 'visted') {
                return;
            }
            $request = $observer->getRequest();
            $remoteAddress = new \Magento\Framework\Http\PhpEnvironment\RemoteAddress($request);
            $visitorIp = $remoteAddress->getRemoteAddress();
            if (in_array($visitorIp, $this->getPaypalIpList())) {
                return;
            }
            if ($this->_geoipHelper->isWebCrawler($request)) {
                return;
            }
            $infoByIp = $this->_geoipHelper->getInfoByIp($visitorIp);
            if (is_array($infoByIp) && array_key_exists($infoByIp['cc'], $selectedCurrency)) {
                $this->_coreSession->setCountVariable('visted');
                $this->_storeManager->getStore()->setCurrentCurrencyCode($selectedCurrency[$infoByIp['cc']]);
            } else {
                return;
            }
        }
    }
    /**
     * get Paypal Ip List
     * @return Array
     */
    public function getPaypalIpList()
    {
        return [
            '173.0.81.1', // notify.paypal.com
            '173.0.81.33',
            '66.211.170.66',
            '173.0.84.8', //ipnpb.paypal.com
            '173.0.84.40',
            '173.0.88.8',
            '173.0.88.40',
            '173.0.92.8',
            '173.0.93.8',
            '64.4.249.8',
            '64.4.248.8',
            '173.0.88.66', //api.paypal.com
            '173.0.88.98',
            '173.0.84.66',
            '173.0.84.98',
            '66.211.168.91',
            '173.0.92.23',
            '173.0.93.23',
            '64.4.249.23',
            '64.4.248.23',
            '66.211.168.93', //reports.paypal.com
            '173.0.84.161',
            '173.0.84.198',
            '173.0.88.161',
            '173.0.88.198',
            '173.0.84.178', //mobile.paypal.com
            '173.0.84.212',
            '173.0.88.178',
            '173.0.88.212',
            '173.0.88.203', //m.paypal.com
            '173.0.84.171',
            '173.0.84.203',
            '173.0.88.171',
        ];
    }
}

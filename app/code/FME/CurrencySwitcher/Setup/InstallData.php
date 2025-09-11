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
namespace FME\CurrencySwitcher\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface {
    protected $_resource;
    protected $_geoipdefaultHelper;
    /**
     * __construct
     * @param \FME\CurrencySwitcher\Helper\Data $helper
     * @return void
     */
    public function __construct(
        \FME\CurrencySwitcher\Helper\Data $helper
    ) {
        $this->_geoipdefaultHelper = $helper;
    }
    /**
     * install
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void|false
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $csvFilePath = $this->_geoipdefaultHelper->prepareCsv('GeoLite2-Country-Blocks-IPv4');
        $CsvPathCountry = $this->_geoipdefaultHelper->prepareCsv('GeoLite2-Country-Locations-en');
        if (!file_exists($csvFilePath)) {
            return false;
        }
        if (!file_exists($CsvPathCountry)) {
            return false;
        }

        ini_set('max_execution_time', 9000);
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
        if ($finlArry) {
            $write = $setup->getConnection('core_write');
            $write->truncateTable('geoip_csv');
            $write->truncateTable('geoip_cl');
            $write->truncateTable('geoip_ip');
            $splitArray=array_chunk($finlArry, 1000, true);
            for ($i=0; $i<count($splitArray); $i++) {
                $write->beginTransaction();
                $query = "INSERT INTO geoip_csv (start_ip, end_ip, start, end, cc, cn) VALUES ". implode(',', $splitArray[$i]);
                $write->query($query);
                $write->commit();
            }
            $write->beginTransaction();
            $write->query("INSERT INTO geoip_cl SELECT DISTINCT NULL, cc, cn FROM geoip_csv");
            $write->query("INSERT INTO geoip_ip SELECT start, end, ci FROM geoip_csv NATURAL JOIN geoip_cl");
            $write->commit();
        }    
    }
}

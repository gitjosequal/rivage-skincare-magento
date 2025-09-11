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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * install
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void|false
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $geoipCl = $installer->getTable('geoip_cl');
        if (!$installer->getConnection()->isTableExists($geoipCl)) {
            $tableGeoipCl = $installer->getConnection()
                    ->newTable($geoipCl)
                    ->addColumn('ci', Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                            ], 'ID')
                    ->addColumn('cc', Table::TYPE_TEXT, null, [
                        'nullable' => false,
                    ], 'country code')
                    ->addColumn('cn', Table::TYPE_TEXT, null, [
                        'nullable' => false,
                    ], 'country name');
            $installer->getConnection()->createTable($tableGeoipCl);
        }
        $geoipIp = $installer->getTable('geoip_ip');
        if (!$installer->getConnection()->isTableExists($geoipIp)) {
            $tableGeoipIp = $installer->getConnection()
                    ->newTable($geoipIp)
                    ->addColumn('start', Table::TYPE_INTEGER, null, [
                        'nullable' => false,
                    ], 'start')
                    ->addColumn('end', Table::TYPE_INTEGER, null, [
                        'nullable' => false,
                    ], 'end')
                    ->addColumn('ci', Table::TYPE_INTEGER, null, [
                        'nullable' => false,
                    ], 'ci ');
            $installer->getConnection()->createTable($tableGeoipIp);
        }
        $geoipCsv = $installer->getTable('geoip_csv');
        if (!$installer->getConnection()->isTableExists($geoipCsv)) {
            $tabelGeoipCsv = $installer->getConnection()
                    ->newTable($geoipCsv)
                    ->addColumn('start_ip', Table::TYPE_TEXT, null, [
                        'nullable' => false,
                    ], 'start IP')
                    ->addColumn('end_ip', Table::TYPE_TEXT, null, [
                        'nullable' => false,
                    ], 'end IP')
                    ->addColumn('start', Table::TYPE_INTEGER, null, [
                        'nullable' => false,
                    ], 'start')
                    ->addColumn('end', Table::TYPE_INTEGER, null, [
                        'nullable' => false,
                    ], 'end')
                    ->addColumn('cc', Table::TYPE_TEXT, null, [
                        'nullable' => false,
                    ], 'country code ')
                    ->addColumn('cn', Table::TYPE_TEXT, null, [
                        'nullable' => false,
                    ], 'country name ');

            $installer->getConnection()
                    ->createTable($tabelGeoipCsv);
        }
        $installer->endSetup();
    }
}

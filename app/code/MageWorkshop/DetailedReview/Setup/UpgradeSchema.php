<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Incorrect version comparison that has already been delivered. Should be 1.0.1.3
        // So need tom leave column validation here (
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $tableName = $setup->getTable('mageworkshop_detailedreview_details_entity_attribute');
            $connection = $setup->getConnection();
            if ($connection->isTableExists($tableName) === true
                && !$connection->tableColumnExists($tableName, 'attribute_placement')
            ) {
                $connection->addColumn(
                    $tableName,
                    'attribute_placement',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        'nullable' => false,
                        'default' => 0,
                        'comment' => 'Attribute Placement',
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $tableName = $setup->getTable('mageworkshop_detailedreview_details_entity_attribute');
            $connection = $setup->getConnection();
            if ($connection->isTableExists($tableName) === true
                && !$connection->tableColumnExists($tableName, 'attribute_visual_settings')
            ) {
                $connection->addColumn(
                    $tableName,
                    'attribute_visual_settings',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                        'length' => 4096,
                        'comment' => 'Attribute visual settings',
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}

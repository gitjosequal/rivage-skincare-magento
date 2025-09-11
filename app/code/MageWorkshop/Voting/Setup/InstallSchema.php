<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use MageWorkshop\Voting\Model\Vote;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable(Vote::TABLE_NAME)
        )->addColumn(
            Vote::KEY_VOTE_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Vote Id'
        )->addColumn(
            Vote::KEY_CUSTOMER_ID,
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true],
            'Customer Id who voted'
        )->addColumn(
            Vote::KEY_GUEST_TOKEN,
            Table::TYPE_TEXT,
            40,
            [],
            'Token to identify guests'
        )->addColumn(
            Vote::KEY_PRODUCT_ID,
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true],
            'Product Id'
        )->addColumn(
            Vote::KEY_REVIEW_ID,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => true],
            'Review Id'
        )->addColumn(
            Vote::KEY_VOTE,
            Table::TYPE_SMALLINT,
            255,
            [],
            'Vote value'
        )->addForeignKey(
            $setup->getFkName(
                Vote::TABLE_NAME,
                Vote::KEY_CUSTOMER_ID,
                'customer_entity',
                'entity_id'
            ),
            Vote::KEY_CUSTOMER_ID,
            $setup->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                Vote::TABLE_NAME,
                Vote::KEY_PRODUCT_ID,
                'catalog_product_entity',
                'entity_id'
            ),
            Vote::KEY_PRODUCT_ID,
            $setup->getTable('catalog_product_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
        $setup->getFkName(
            Vote::TABLE_NAME,
            Vote::KEY_CUSTOMER_ID,
            'review',
            Vote::KEY_REVIEW_ID
        ),
            Vote::KEY_REVIEW_ID,
            $setup->getTable('review'),
            'review_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Votes Table'
        );

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}

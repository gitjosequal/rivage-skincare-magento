<?php
/**
 * Copyright © 2015 Emizentech. All rights reserved.
 */

namespace Emizentech\ShopByBrand\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;


class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('emizentech_shopbybrand_items'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Name'
            )
            ->addColumn(
                'aname',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Arbic Name'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_INTEGER,
                null,
                ['default' => null , 'unique' => true],
                'attribute_id'
            )
            ->addIndex(
            $installer->getIdxName(
					'attribute_id',
					['attribute_id'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
				),
				['attribute_id'],
				['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
			)
            ->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                ['default' => 0],
                'Sort Order'
            )
            ->addColumn(
                'url_key',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Url Key'
            )
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Description'
            )
            ->addColumn(
                'adescription',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Arbic Description'
            )
            ->addColumn(
                'logo',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'logo'
            )
            ->addColumn(
                'mainimage',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Main Image'
            )
            ->addColumn(
                    'is_active',
                    Table::TYPE_SMALLINT,
                    null,
                    [],
                    'Active Status'
            )
			->setComment(
				'Brand Table'
			)
            ;
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}

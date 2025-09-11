<?php

namespace Tatvam\Wisernotify\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install Table.
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Create table 'TatvamSettings'.
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('tatvam_wisernotify_settings'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'ID'
            )->addColumn(
                'key',
                Table::TYPE_TEXT,
                50,
                [
                    'nullable' => false,
                    'default' => '',
                ],
                'Key'
            )->addColumn(
                'ti',
                Table::TYPE_TEXT,
                25,
                ['nullable' => true],
                'ti'
            )->addColumn(
                'pt',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Value'
            );
        $setup->getConnection()->createTable($table);
    }
}

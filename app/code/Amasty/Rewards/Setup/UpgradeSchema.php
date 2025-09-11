<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1', '<')) {
            $this->addTimestampInitToHistoryTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * add current timestamp as default
     *
     * @param SchemaSetupInterface $installer
     */
    private function addTimestampInitToHistoryTable(SchemaSetupInterface $installer)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable('amasty_rewards_history'))) {
            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_rewards_history'),
                'date',
                'date',
                ['TYPE'     => Table::TYPE_TIMESTAMP,
                 'nullable' => false,
                 'default'  => Table::TIMESTAMP_INIT
                ]
            );
        }
    }
}

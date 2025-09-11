<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetaData;

    /**
     * @var \Amasty\Base\Setup\SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    /**
     * UpgradeData constructor.
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetaData
     * @param \Amasty\Base\Setup\SerializedFieldDataConverter $fieldDataConverter
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        \Amasty\Base\Setup\SerializedFieldDataConverter $fieldDataConverter
    ) {
        $this->productMetaData = $productMetaData;
        $this->fieldDataConverter = $fieldDataConverter;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.3', '<')
            && $this->productMetaData->getVersion() >= "2.2.0"
        ) {
            $table = $setup->getTable('amasty_rewards_rule');
            $this->fieldDataConverter->convertSerializedDataToJson($table, 'rule_id', 'conditions_serialized');
        }

        $setup->endSetup();
    }
}
<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\ImageLoader\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     */
    private $attributeHelper;

    /**
     * UpgradeData constructor.
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     */
    public function __construct(
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
    ) {
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            foreach ($this->attributeHelper->getAttributesCollection() as $attribute) {
                if ($attribute->getFrontendInput() === 'media_image') {
                    $attribute->setFrontendInput('image');
                    $attribute->save();
                }
            }
        }
    }
}

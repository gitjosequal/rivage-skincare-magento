<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Category;
use MageWorkshop\DetailedReview\Helper\Attribute;
use MageWorkshop\DetailedReview\Model\Entity\Attribute\Source\ReviewForm;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /** @var \MageWorkshop\DetailedReview\Setup\DetailsSetup */
    protected $entitySetup;

    /**
     * @param DetailsSetup $entitySetup
     */
    public function __construct(
        \MageWorkshop\DetailedReview\Setup\DetailsSetup $entitySetup
    ) {
        $this->entitySetup = $entitySetup;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->entitySetup->installEntities();
        $this->installCategoryAttributes();
        $setup->endSetup();
    }

    /**
     * Install category "review_form" attribute that provides ability to choose review form per product
     */
    protected function installCategoryAttributes()
    {

        $this->entitySetup->addAttribute(
            Category::ENTITY,
            Attribute::REVIEW_FORM_ATTRIBUTE_CODE,
            [
                'label'        => 'Review Form',
                'input'        => 'select',
                'source'       => ReviewForm::class,
                'sort_order'   => 50,
                'global'       => ScopedAttributeInterface::SCOPE_STORE,
                'group'        => 'Display Settings',
            ]
        );
    }
}

<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Attribute\Edit;

use Magento\Backend\Block\Template\Context;
use MageWorkshop\DetailedReview\Model\Details;
use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigInterface;
use MageWorkshop\DetailedReview\Api\Data\EntityConfigInterface;

class Js extends \Magento\Backend\Block\Template
{
    /** @var EntityConfigInterface $entityConfig */
    protected $entityConfig;

    /**
     * Js constructor.
     * @param Context $context
     * @param EntityConfigInterface $entityConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        EntityConfigInterface $entityConfig,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->entityConfig = $entityConfig;
    }

    /**
     * Get option input type ("radio" or "checkbox") for each frontend input type
     *
     * @return array
     */
    public function getOptionTypes()
    {
        $optionTypes = [];
        /** @var AttributeConfigInterface $attributeConfig */
        foreach ($this->entityConfig->getEntityAttributesConfig(Details::ENTITY) as $attributeConfig) {
            if ($optionType = $attributeConfig->getOptionType()) {
                $optionTypes[$attributeConfig->getFrontendInput()] = $optionType;
            }
        }
        return $optionTypes;
    }
}

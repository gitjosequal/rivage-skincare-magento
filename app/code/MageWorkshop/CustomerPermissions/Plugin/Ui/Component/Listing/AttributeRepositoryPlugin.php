<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Plugin\Ui\Component\Listing;

use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;
use MageWorkshop\CustomerPermissions\Model\Module\DetailsData;

class AttributeRepositoryPlugin
{
    /** 'banned_till' field id - needs to be hidden if this module is switched off  */
    const BANNED_TILL_FIELD_ID = 'banned_till';

    /** @var \MageWorkshop\Core\Helper\Data */
    protected $coreHelper;

    /**
     * @var array
     */
    protected $additionalActionsArray = [];

    /**
     * AttributeRepositoryPlugin constructor.
     *
     * @param \MageWorkshop\Core\Helper\Data $coreHelper
     */
    public function __construct(
        \MageWorkshop\Core\Helper\Data $coreHelper
    ) {
        $this->coreHelper = $coreHelper;
    }

    /**
     * Additional actions are added to the configuration array
     *
     * @param AttributeRepository $attributeRepository
     * @param array $result
     * @return mixed
     */
    public function afterGetList(AttributeRepository $attributeRepository, &$result)
    {
        // Don't add Permission's mass actions if the DR module is disabled
        if ($this->coreHelper->isModuleEnabledInDetailedReviewSection(DetailsData::MODULE_CODE)) {
            return $result;
        }

        if (isset($result[self::BANNED_TILL_FIELD_ID])) {
            $result[self::BANNED_TILL_FIELD_ID][EavAttributeInterface::IS_VISIBLE_IN_GRID] = 0;
            $result[self::BANNED_TILL_FIELD_ID][EavAttributeInterface::IS_USED_IN_GRID] = 0;
        }

        return $result;
    }
}
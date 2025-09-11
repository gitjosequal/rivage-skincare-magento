<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Plugin\Ui\Model;

use Magento\Ui\Model\Manager;
use MageWorkshop\CustomerPermissions\Model\Module\DetailsData;

class ManagerPlugin
{
    // Array path to configuration item which is going to be removed
    const BANNED_TILL_ITEM_CONFIGURATION_PATH = 'customer_listing/children/customer_columns/children/banned_till';

    const CUSTOMER_LISTING_CONFIG_KEY = 'customer_listing';

    /** @var \MageWorkshop\Core\Helper\Data */
    protected $coreHelper;

    public function __construct(
        \MageWorkshop\Core\Helper\Data $coreHelper
    ) {
        $this->coreHelper = $coreHelper;
    }

    /**
     * @param Manager $manager
     * @param array $result
     * @return mixed
     * @internal param AttributeRepository $attributeRepository
     */
    public function afterGetData(Manager $manager, $result)
    {
        if (
            !$this->coreHelper->isModuleEnabledInDetailedReviewSection(DetailsData::MODULE_CODE)
            && array_key_exists(self::CUSTOMER_LISTING_CONFIG_KEY, $result)
        ) {
            $this->unsetGridColumnConfigItemByPath(
                $result,
                self::BANNED_TILL_ITEM_CONFIGURATION_PATH
            );
        }

        return $result;
    }

    /**
     * Removes the array key_specified by path in the array
     *
     * @param $configurationPartArray
     * @param $path
     * @internal param $configurationArray
     */
    protected function unsetGridColumnConfigItemByPath(&$configurationPartArray, $path) {
        $pathItemsArray = explode('/', $path);

        $depth = count($pathItemsArray) - 1;

        foreach ($pathItemsArray as $arrayPathItem) {
            if (!$depth) {
                unset($configurationPartArray[$arrayPathItem]);
            } else if (array_key_exists($arrayPathItem, $configurationPartArray)) {
                $configurationPartArray = &$configurationPartArray[$arrayPathItem];
                $depth--;
            } else {
                break;
            }
        }
    }
}
<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\AdminResponse\Block;

use Magento\Review\Model\Review;
use MageWorkshop\AdminResponse\Model\AdminResponse as AdminResponseModel;

/**
 * Class AdminResponse
 * @method Review getReview()
 */
class AdminResponse extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string $adminResponseTitle
     */
    private static $adminResponseTitle;

    /**
     * @var string $adminResponseTooltip
     */
    private static $adminResponseTooltip;

    /**
     * @return string
     */
    public function getAdminResponse()
    {
        if (!$review = $this->getReview()) {
            return '';
        }

        return (string) $review->getData(AdminResponseModel::FIELD_NAME);
    }

    /**
     * @return string
     */
    public function getAdminResponseTitle()
    {
        if (self::$adminResponseTitle === null) {
            self::$adminResponseTitle = (string) $this->_scopeConfig
                ->getValue(AdminResponseModel::XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_ADMIN_RESPONSE_TITLE);
        }

        return self::$adminResponseTitle;
    }

    /**
     * @return string
     */
    public function getAdminResponseTooltipText()
    {
        if (self::$adminResponseTooltip === null) {
            self::$adminResponseTooltip = $this->_scopeConfig
                    ->isSetFlag(AdminResponseModel::XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_ADMIN_RESPONSE_SHOW_TOOLTIP)
                ? (string) $this->_scopeConfig
                    ->getValue(AdminResponseModel::XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_ADMIN_RESPONSE_TOOLTIP_TEXT)
                : '';
        }

        return self::$adminResponseTooltip;
    }
}

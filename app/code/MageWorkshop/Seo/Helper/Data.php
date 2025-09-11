<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Seo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_PATH_MAGEWORKSHOP_SEO_HIDE_REVIEW_BY = 'mageworkshop_detailedreview/mageworkshop_seo/hide_review_by';
    /**
     * @return string
     */
    public function getHideStyle()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAGEWORKSHOP_SEO_HIDE_REVIEW_BY);
    }
}
<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\SocialSharing\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * SocialSharing helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config path for "Enable social sharing" adminhtml option
     */
    const XML_PATH_SOCIAL_SHARING_ENABLED = 'mageworkshop_detailedreview/socialsharing/enabled';

    /**
     * Returns true if the review social sharing is enabled in admin
     *
     * @return boolean
     */
    public function isSocialSharingEnabled()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_SOCIAL_SHARING_ENABLED, ScopeInterface::SCOPE_STORE);
    }
}

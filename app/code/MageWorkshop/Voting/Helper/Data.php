<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Voting helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config path for "Enable voting" adminhtml option
     */
    const XML_PATH_VOTING_ENABLED = 'mageworkshop_detailedreview/voting/enabled';

    /**
     * Config path for "Enable voting" adminhtml option
     */
    const XML_PATH_GUESTS_VOTING_ALLOWED = 'mageworkshop_detailedreview/voting/allow_guest';

    /**
     * Returns true if the review voting is enabled in admin
     *
     * @return boolean
     */
    public function isVotingEnabled()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_VOTING_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns true if the guests are allowed to vote
     *
     * @return boolean
     */
    public function isGuestsVotingAllowed()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_GUESTS_VOTING_ALLOWED, ScopeInterface::SCOPE_STORE);
    }
}
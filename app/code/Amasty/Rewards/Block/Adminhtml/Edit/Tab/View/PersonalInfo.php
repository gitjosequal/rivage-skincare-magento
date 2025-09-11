<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\Edit\Tab\View;

class PersonalInfo extends \Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo
{
    public function getStatistic()
    {
        return $this->coreRegistry->registry('current_amasty_rewards_statistic');
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Controller\Adminhtml\Rewards;

class Index extends \Amasty\Rewards\Controller\Adminhtml\Rewards
{

    public function execute()
    {
        $customerId = $this->initCurrentCustomer();

        /** @var \Amasty\Rewards\Model\Rewards $model */
        $model = $this->rewardsFactory->create();
        $statistic = $model->getStatistic($customerId);

        $this->_coreRegistry->register('current_amasty_rewards_statistic', $statistic);

        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}

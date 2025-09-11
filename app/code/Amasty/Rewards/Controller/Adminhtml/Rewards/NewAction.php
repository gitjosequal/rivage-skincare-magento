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

class NewAction extends \Amasty\Rewards\Controller\Adminhtml\Rewards
{
    public function execute()
    {
        /**
         * @var $model \Amasty\Rewards\Model\Rewards
         */
        $model = $this->rewardsFactory->create();

        $amount = $this->getRequest()->getParam('amount');
        $action = $this->_scopeConfig->getValue('amrewards/general/adminaction');
        $customerId = $this->getRequest()->getParam('customer_id');
        $comment = $this->getRequest()->getParam('comment');

        $model->addPoints($amount, $action, $customerId, $comment);

        $this->_coreRegistry->register('current_amasty_rewards_rewards', $model);
    }
}

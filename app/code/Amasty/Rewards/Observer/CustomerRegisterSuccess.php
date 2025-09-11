<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Observer;

class CustomerRegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_registry     = $registry;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var $customer \Magento\Customer\Model\Data\Customer
         */
        $customer = $observer->getCustomer();
        $customerId = $customer->getId();
        $customerWebsite = $customer->getWebsiteId();
        $customerGroupId = $customer->getGroupId();

        /**
         * @var $historyModel \Amasty\Rewards\Model\History
         */
        $historyModel = $this->_objectManager->create('Amasty\Rewards\Model\History');

        $registrationAction = \Amasty\Rewards\Helper\Data::REGISTRATION_ACTION;

        $appliedActions = $historyModel->getAppliedActionsId($customerId);

        /**
         * @var $ruleCollection \Amasty\Rewards\Model\ResourceModel\Rule\Collection
         */
        $ruleCollection = $this->_objectManager->create('Amasty\Rewards\Model\ResourceModel\Rule\Collection');

        $ruleCollection->addWebsiteGroupActionFilter($customerWebsite, $customerGroupId, $registrationAction);
        /**
         * @var $rewardsModel \Amasty\Rewards\Model\Rewards
         */
        $rewardsModel = $this->_objectManager->create('Amasty\Rewards\Model\Rewards');

        $rules = $ruleCollection->load();

        foreach ($rules->getItems() as $rule) {
            if (!isset($appliedActions[$rule->getId()])) {
                $rewardsModel->addRegistrationReward($rule, $customerId);
            }
        }
    }
}
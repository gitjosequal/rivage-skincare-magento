<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Plugin\Newsletter\Model;

use Magento\Newsletter\Model\Subscriber as SourceSubscriber;

class Subscriber
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Amasty\Rewards\Model\History
     */
    private $history;

    /**
     * @var \Amasty\Rewards\Model\Rewards
     */
    private $rewardsFactory;

    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Amasty\Rewards\Model\History $history,
        \Amasty\Rewards\Model\RewardsFactory $rewardsFactory,
        \Amasty\Rewards\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
    ) {
        $this->_registry             = $registry;
        $this->_storeManager         = $storeManager;
        $this->_customerSession      = $customerSession;
        $this->history               = $history;
        $this->rewardsFactory        = $rewardsFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    public function afterSave(
        SourceSubscriber $subject,
        $result
    ) {
        if ($subject->getCustomerId()
            && $subject->getSubscriberStatus() === SourceSubscriber::STATUS_SUBSCRIBED
        ) {
            $this->addRewardPoints($subject->getCustomerId());
        }

        return $result;
    }

    protected function addRewardPoints($customerId)
    {
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $customerGroupId = $this->_customerSession->getCustomerGroupId();

        if (!$websiteId || !$customerId) {
            return;
        }

        // fix new customers group ID
        if ($customerGroupId === 0) {
            $customerGroupId = 1;
        }

        $subscriptionAction = \Amasty\Rewards\Helper\Data::SUBSCRIPTION_ACTION;
        $appliedActions = $this->history->getAppliedActionsId($customerId);

        /** @var $ruleCollection \Amasty\Rewards\Model\ResourceModel\Rule\Collection */
        $ruleCollection = $this->ruleCollectionFactory->create();

        $ruleCollection->addWebsiteGroupActionFilter($websiteId, $customerGroupId, $subscriptionAction);

        $rules = $ruleCollection->load();

        foreach ($rules->getItems() as $rule) {
            if (!isset($appliedActions[$rule->getId()])) {
                /** @var \Amasty\Rewards\Model\Rewards $reward */
                $reward = $this->rewardsFactory->create();
                $reward->addNewsletterReward($rule, $customerId);
            }
        }
    }
}

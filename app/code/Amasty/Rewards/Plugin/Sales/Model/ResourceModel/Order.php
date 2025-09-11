<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Plugin\Sales\Model\ResourceModel;

class Order
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
     * @var \Amasty\Rewards\Model\Rewards
     */
    private $rewardsFactory;
    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var \Amasty\Rewards\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Quote
     */
    private $rewardQuoteResource;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Amasty\Rewards\Model\RewardsFactory $rewardsFactory,
        \Amasty\Rewards\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Amasty\Rewards\Helper\Data $dataHelper,
        \Amasty\Rewards\Model\ResourceModel\Quote $rewardQuoteResource
    ) {
        $this->_registry              = $registry;
        $this->_storeManager          = $storeManager;
        $this->_customerSession       = $customerSession;
        $this->rewardsFactory         = $rewardsFactory;
        $this->ruleCollectionFactory  = $ruleCollectionFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->dataHelper             = $dataHelper;
        $this->rewardQuoteResource = $rewardQuoteResource;
    }


    public function aroundSave(
        \Magento\Sales\Model\ResourceModel\Order\Interceptor $subject,
        \Closure $closure,
        \Magento\Sales\Model\Order $order
    ) {
        $result = $closure($order);
        $websiteId = $this->_storeManager->getWebsite()->getId();
        $customerGroupId = $order->getCustomerGroupId();
        $disableAddRewards = $this->dataHelper->getIsDisableRewards($websiteId);
        $orderAction = \Amasty\Rewards\Helper\Data::ORDER_COMPLETED_ACTION;
        $spentAction = \Amasty\Rewards\Helper\Data::MONEY_SPENT_ACTION;
        $quoteHistory = $this->rewardQuoteResource->loadByQuoteId($order->getQuoteId());

        if ($order->getStatus() === \Magento\Sales\Model\Order::STATE_COMPLETE
            && !($disableAddRewards && $this->getQuotePaidByRewards($quoteHistory))
        ) {
            /**
             * @var $address \Magento\Quote\Model\Quote\Address
             */
            $address = $this->getAddress($order);
            $orderRules = $this->getRulesByAction($websiteId, $customerGroupId, $orderAction);
            $spentRules = $this->getRulesByAction($websiteId, $customerGroupId, $spentAction);

            /**
             * @var $rule \Amasty\Rewards\Model\Rule
             */
            foreach ($orderRules->getItems() as $rule) {
                if ($rule->validate($address)) {
                    $this->rewardsFactory->create()->addRuleReward($rule, $address, $order->getId());
                }
            }

            foreach ($spentRules->getItems() as $rule) {
                if ($rule->validate($address)) {
                    $this->rewardsFactory->create()->addRuleReward($rule, $address, $order->getId());
                }
            }
        }

        return $result;
    }

    /**
     * @param $quoteHistory
     * @return bool
     */
    private function getQuotePaidByRewards($quoteHistory)
    {
        return $quoteHistory && isset($quoteHistory['reward_points']);
    }

    /**
     * @param $websiteId
     * @param $customerGroupId
     * @param $action
     * @return $this
     */
    protected function getRulesByAction($websiteId, $customerGroupId, $action)
    {
        /**
         * @var $ruleCollection \Amasty\Rewards\Model\ResourceModel\Rule\Collection
         */
        $ruleCollection = $this->ruleCollectionFactory->create();
        $ruleCollection->addWebsiteGroupActionFilter($websiteId, $customerGroupId, $action);
        $rules = $ruleCollection->load();

        return $rules;
    }

    /**
     * @param $order
     * @return \Magento\Quote\Model\Quote\Address
     */
    protected function getAddress($order)
    {
        $quote = $this->loadQuote($order->getQuoteId());

        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        return $address;
    }

    /**
     * Load quote by ID
     *
     * IMPORTANT: Do not use \Magento\Quote\Api\CartRepositoryInterface because
     * cart repository can load only quotes from current store
     *
     * @param $id
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function loadQuote($id)
    {
        /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $collection */
        $collection = $this->quoteCollectionFactory->create();

        $collection
            ->addFieldToFilter('entity_id', $id)
            ->setPageSize(1);

        return $collection->getFirstItem();
    }
}

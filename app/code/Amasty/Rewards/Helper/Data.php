<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ORDER_COMPLETED_ACTION = 'ordercompleted';

    const SUBSCRIPTION_ACTION = 'subscription';

    const BIRTHDAY_ACTION = 'birthday';

    const MONEY_SPENT_ACTION = 'moneyspent';

    const REGISTRATION_ACTION = 'registration';

    const DISABLE_REWARD_CONFIG_PATH = 'points/disable_reward';

    const MINIMUM_POINTS_CONFIG_PATH = 'points/minimum_reward';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Amasty\Rewards\Model\RewardsFactory
     */
    private $rewardsFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Amasty\Rewards\Model\RewardsFactory $rewardsFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Amasty\Rewards\Model\RewardsFactory $rewardsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $context->getScopeConfig();
        $this->coreRegistry = $registry;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
        $this->rewardsFactory = $rewardsFactory;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->orderRepository = $orderRepository;
    }

    public function getAllGroups()
    {
        $customerGroups = $this->objectManager->create('Magento\Customer\Model\ResourceModel\Group\Collection')
            ->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value'] == 0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, ['value' => 0, 'label' => __('NOT LOGGED IN')]);
        }

        return $customerGroups;
    }

    public function getStatuses()
    {
        return [
            '1' => __('Active'),
            '0' => __('Inactive'),
        ];
    }

    /**
     * @param int|string $orderId
     * @return null|string
     */
    public function getOrderIncrementIdById($orderId)
    {
        $order = $this->orderRepository->get($orderId);

        return $order->getIncrementId();
    }

    public function getActions()
    {
        $actions = [
            self::ORDER_COMPLETED_ACTION => __('Order Completed'),
            self::SUBSCRIPTION_ACTION    => __('Newsletter subscription'),
            self::BIRTHDAY_ACTION        => __('Customer birthday'),
            self::MONEY_SPENT_ACTION     => __('For every $X spent'),
            self::REGISTRATION_ACTION    => __('Registration')
        ];
        return $actions;
    }

    public function getStoreConfig($path, $store = null)
    {
        return $this->scopeConfig->getValue(
            'amrewards/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $store
        );
    }

    /**
     * Rate cannot be null
     *
     * @param null $store
     *
     * @return int
     */
    public function getPointsRate($store = null)
    {
        return max((int)$this->getStoreConfig('points/rate', $store), 1);
    }

    /**
     * @param null $store
     * @return bool
     */
    public function getIsDisableRewards($store = null)
    {
        return (boolean)$this->getStoreConfig(self::DISABLE_REWARD_CONFIG_PATH, $store);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getMinimumPointsValue($store = null)
    {
        return $this->getStoreConfig(self::MINIMUM_POINTS_CONFIG_PATH, $store);
    }

    /**
     * @param int|float $points
     *
     * @return int
     */
    public function roundPoints($points)
    {
        $roundRule = $this->getStoreConfig('points/round_rule');

        if ($roundRule == 'up') {
            return ceil($points);
        }

        return floor($points);
    }

    /**
     * @return array
     */
    public function getRewardsData()
    {
        $pointsLeft = false;
        $customerId = $this->customerSession->getCustomerId();
        $rewardModel = $this->rewardsFactory->create();

        if ($rewardModel) {
            $pointsLeft = $rewardModel->getPoints($customerId);
        }

        return [
            'customerId' => $customerId,
            'pointsUsed' => $this->checkoutSession->getQuote()->getData('amrewards_point'),
            'pointsLeft' => $pointsLeft,
            'pointsRate' => $this->getCurrencyPointsRate(),
            'currentCurrencyCode' => $this->storeManager->getStore()->getCurrentCurrency()->getCurrencyCode(),
            'rateForCurrency' => $this->getPointsRate(),
            'applyUrl' => $this->urlBuilder->getUrl('amrewards/index/rewardPost'),
            'cancelUrl' => $this->urlBuilder->getUrl(
                'amrewards/index/rewardPost',
                [
                    'remove' => 1,
                ]
            ),
        ];
    }

    /**
     * @return float
     */
    public function getCurrencyPointsRate()
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency();
        $baseCurrency = $this->storeManager->getStore()->getBaseCurrencyCode();
        $rates = round(1 / $currentCurrency->getAnyRate($baseCurrency), 3);

        return $rates;
    }
}

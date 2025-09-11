<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Rewards
 *
 * @method \Amasty\Rewards\Model\ResourceModel\Rewards getResource()
 * @method \Amasty\Rewards\Model\ResourceModel\Rewards _getResource()
 */
class Rewards extends AbstractModel
{

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $helper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Amasty\Rewards\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManagerInterface;
        $this->priceCurrency = $priceCurrency;
        $this->helper = $helper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rewards\Model\ResourceModel\Rewards');
    }

    public function getPoints($customerId)
    {
        return $this->getResource()->loadPointsByCustomerId($customerId);
    }

    public function getStatistic($customerId)
    {
        $select = $this->getResource()->getConnection()->select()->from(
            $this->getResource()->getTable('amasty_rewards_rewards'),
            [
                'SUM(CASE WHEN amount<0 THEN amount ELSE 0 END) as negativeTotal',
                'SUM(CASE WHEN amount>=0 THEN amount ELSE 0 END) as positiveTotal'
            ]
        )->where(
            'customer_id = ?',
            (int)$customerId
        );

        $result = $this->getResource()->getConnection()->fetchRow($select);

        return $result;
    }

    public function getCustomerRewards($customerId)
    {
        $rewardsCollection = $this->getResourceCollection();
        $rewardsCollection->addFieldToFilter('customer_id', $customerId);  //addCustomerIdFilter($customerId);
        $rewardsCollection->addOrder('id', 'DESC');
        return $rewardsCollection;
    }

    public function createComment($rule, $address = null, $orderId = null)
    {
        $amount = $rule->getAmount();
        $comment = '';
        if ($amount < 0) {
            $comment = __('Order %1 paid', $orderId);
        } else {
            switch ($rule->getAction()) {
                case \Amasty\Rewards\Helper\Data::SUBSCRIPTION_ACTION:
                    $comment = __('Newsletter subscription bonus');
                    break;
                case \Amasty\Rewards\Helper\Data::REGISTRATION_ACTION:
                    $comment = __('Registration bonus');
                    break;
                case \Amasty\Rewards\Helper\Data::BIRTHDAY_ACTION:
                    $comment = __('Happy Birthday!');
                    break;
                case \Amasty\Rewards\Helper\Data::MONEY_SPENT_ACTION:
                case \Amasty\Rewards\Helper\Data::ORDER_COMPLETED_ACTION:
                    $ruleLabel = $rule->getStoreLabel($address->getQuote()->getStore());
                    $ruleLabel !='' ? $add = $ruleLabel : $add = $rule->getName();
                    $comment = __(' %1 bonus for order %2', $add, $this->helper->getOrderIncrementIdById($orderId));
            }
        }
        return $comment;
    }

    /**
     * @param \Amasty\Rewards\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param int $orderId
     */
    public function addRuleReward($rule, $address, $orderId)
    {
        /**
         * @var $address \Magento\Quote\Model\Quote\Address
         */
        $action = $rule->getAction();

        if ($action===\Amasty\Rewards\Helper\Data::MONEY_SPENT_ACTION) {
            $rewardAmount = $this->calculateSpentReward($address, $rule);
        } else {
            $rewardAmount = $rule->getAmount();
        }

        $customerId = $address->getCustomerId();
        $comment = $this->createComment($rule, $address, $orderId);

        $this->addPoints($rewardAmount, $action, $customerId, $comment);

        /**
         * @var $historyModel \Amasty\Rewards\Model\History
         */
        $historyModel = $this->_objectManager->create('Amasty\Rewards\Model\History');

        $historyModel->saveInHistory($customerId, $rule->getId());

    }

    public function addNewsletterReward($rule, $customerId)
    {
        $action = $rule->getAction();
        $rewardAmount = $rule->getAmount();
        $comment = $this->createComment($rule);
        $this->addPoints($rewardAmount, $action, $customerId, $comment);
        /**
         * @var $historyModel \Amasty\Rewards\Model\History
         */
        $historyModel = $this->_objectManager->create('Amasty\Rewards\Model\History');
        $historyModel->saveInHistory($customerId, $rule->getId());
    }

    public function addBirthdayReward($rule, $customerId)
    {
        $action = $rule->getAction();
        $rewardAmount = $rule->getAmount();
        $comment = $this->createComment($rule);
        $this->addPoints($rewardAmount, $action, $customerId, $comment);
        /**
         * @var $historyModel \Amasty\Rewards\Model\History
         */
        $historyModel = $this->_objectManager->create('Amasty\Rewards\Model\History');
        $historyModel->saveInHistory($customerId, $rule->getId());
    }

    public function addRegistrationReward($rule, $customerId)
    {
        $action = $rule->getAction();
        $rewardAmount = $rule->getAmount();
        $comment = $this->createComment($rule);
        $this->addPoints($rewardAmount, $action, $customerId, $comment);
        /**
         * @var $historyModel \Amasty\Rewards\Model\History
         */
        $historyModel = $this->_objectManager->create('Amasty\Rewards\Model\History');
        $historyModel->saveInHistory($customerId, $rule->getId());
    }

    public function addPoints($amount, $action, $customerId, $comment='')
    {
        if ($amount != 0) {
            $this->addData([
                'amount'      => $amount,
                'action'      => $action,
                'comment'     => $comment,
                'customer_id' => $customerId
            ]);
            $this->save();
        }
    }

    /**
     * @param $address
     * @param $rule
     *
     * @return float
     */
    public function calculateSpentReward($address, $rule)
    {
        $spentAmount = $rule->getSpentAmount();
        $rewardAmount = $rule->getAmount();
        $cartAmount = $address->getBaseGrandTotal();
        $result = floor($cartAmount / $spentAmount) * $rewardAmount;
        return $result;
    }

    /**
     * @param $items
     * @param $total
     * @param $points
     *
     * @return int
     */
    public function calculateDiscount($items, $total, $points)
    {
        $allCartPrice = 0;

        $rate = $this->helper->getPointsRate();

        usort($items, [$this, 'sortItems']);

        $basePoints = $points/$rate;
        $itemCount = 0;
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $allCartPrice += $this->_getRealItemPrice($item);
            $itemCount++;
        }

        if ($allCartPrice < $basePoints) {
            $roundRule = $this->helper->getStoreConfig('points/round_rule');
            if ($roundRule == 'down') {
                $basePoints = floor($allCartPrice);
            } else {
                $basePoints = $allCartPrice;
            }
        }

        $itemDiscount = [];
        $normalDiscount = $basePoints / $itemCount;
        if (!$normalDiscount) {
            return 0;
        }
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $itemPrice = $this->_getRealItemPrice($item);
            if ($itemPrice < $normalDiscount) {
                $itemDiscount[$item->getId()] = $itemPrice;
                $normalDiscount = ($basePoints - $itemPrice) / ($itemCount - 1);
            } else {
                $itemDiscount[$item->getId()] = $normalDiscount;
            }
        }

        $discountValue = 0;
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $this->discountItem($item, $total, $itemDiscount[$item->getId()]);
            $discountValue += $itemDiscount[$item->getId()];
        }

        return $discountValue * $rate;
    }

    protected function _getRealItemPrice($item)
    {
        $realPrice = $item->getBasePrice() * $item->getQty() - $item->getBaseDiscountAmount();
        return max(0, $realPrice);
    }

    /**
     * Sorting items before apply reward points
     * cheapest should go first
     *
     * @param \Magento\Quote\Model\Quote\Item $itemA
     * @param \Magento\Quote\Model\Quote\Item $itemB
     *
     * @return int
     */
    private function sortItems($itemA, $itemB)
    {
        if ($this->_getRealItemPrice($itemA) > $this->_getRealItemPrice($itemB)) {
            return 1;
        }
        if ($this->_getRealItemPrice($itemA) < $this->_getRealItemPrice($itemB)) {
            return -1;
        }

        return 0;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item          $item
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param int|float                                $discount
     */
    protected function discountItem($item, $total, $discount)
    {
        $item->setBaseDiscountAmount($item->getBaseDiscountAmount() + $discount);
        $discountAmount =  $this->priceCurrency->convert($discount, $this->_storeManager->getStore());
        $item->setDiscountAmount($item->getDiscountAmount() + $discountAmount);
        $total->addTotalAmount('discount', -$discountAmount);
        $total->addBaseTotalAmount('discount', -$discount);
    }

    public function getAction()
    {
        $action = $this->getData('action');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $hlp = $objectManager->get('Amasty\Rewards\Helper\Data');
        $actionList = $hlp->getActions();

        if (array_key_exists($action, $actionList)) {
            $result = $actionList[$action];
        } else {
            $result = $action;
        }
        return $result;
    }

}

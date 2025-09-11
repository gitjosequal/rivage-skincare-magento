<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Cron;

use Amasty\Rewards\Helper\Data as Helper;

class HappyBirthday
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Amasty\Rewards\Model\HistoryFactory
     */
    private $historyFactory;

    /**
     * @var \Amasty\Rewards\Model\RewardsFactory
     */
    private $rewardsFactory;

    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Amasty\Rewards\Model\HistoryFactory $historyFactory,
        \Amasty\Rewards\Model\RewardsFactory $rewardsFactory,
        \Amasty\Rewards\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
    ) {
        $this->scopeConfig               = $scopeConfig;
        $this->historyFactory            = $historyFactory;
        $this->rewardsFactory            = $rewardsFactory;
        $this->ruleCollectionFactory     = $ruleCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->dateTime                  = $dateTime;
    }

    /**
     * Clear expired persistent sessions
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Cron\Model\Schedule $schedule)
    {
        $days = (int)$this->scopeConfig->getValue('amrewards/general/days');
        $time = $this->dateTime->timestamp();
        if ($days < 0) {
            // after birthday
            $time = $this->dateTime->timestamp("-$days days");
        } elseif ($days > 0) {
            $days = abs($days);
            $time = $this->dateTime->timestamp("+$days days");
        }

        $collection = $this->_getCollection();

        $collection->getSelect()->where(
            new \Zend_Db_Expr(
                "DATE_FORMAT(`e`.`dob`, '%m-%d') = '" . $this->dateTime->date('m-d', $time) . "'"
            )
        );
        /** @var \Magento\Customer\Model\Customer $customer */
        foreach ($collection->getItems() as $customer) {
            $customerId      = $customer->getEntityId();

            /** @var $historyModel \Amasty\Rewards\Model\History */
            $historyModel       = $this->historyFactory->create();
            $appliedActions     = $historyModel->getLastYearActionsId($customerId, $time);

            /** @var $rewardsModel \Amasty\Rewards\Model\Rewards */
            $rewardsModel = $this->rewardsFactory->create();

            /** @var $ruleCollection \Amasty\Rewards\Model\ResourceModel\Rule\Collection */
            $ruleCollection = $this->ruleCollectionFactory->create();
            $ruleCollection->addWebsiteGroupActionFilter(
                $customer->getWebsiteId(),
                $customer->getGroupId(),
                Helper::BIRTHDAY_ACTION
            );

            foreach ($ruleCollection->getItems() as $rule) {
                if (!isset($appliedActions[$rule->getId()])) {
                    $rewardsModel->addBirthdayReward($rule, $customerId);
                }
            }
        }

        return $this;
    }

    /**
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    protected function _getCollection()
    {
        /** @var $customerCollection \Magento\Customer\Model\ResourceModel\Customer\Collection */
        $customerCollection = $this->customerCollectionFactory->create();

        $collection = $customerCollection
            ->addNameToSelect()
            ->addAttributeToSelect('email');

        return $collection;
    }
}

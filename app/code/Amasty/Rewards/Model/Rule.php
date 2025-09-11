<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Model;

use \Amasty\Rewards\Api\Data\RuleInterface;

/**
 * @method ResourceModel\Rule getResource()
 * @method ResourceModel\Rule _getResource()
 */
class Rule extends \Magento\Rule\Model\AbstractModel implements RuleInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\CombineFactory
     */
    private $combineFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    private $actionFactory;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $combineFactory
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $actionFactory
     * @param \Amasty\Base\Model\Serializer $serializer
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $actionFactory,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
        $this->combineFactory = $combineFactory;
        $this->actionFactory = $actionFactory;
        $this->serializer = $serializer;
    }

    protected function _construct()
    {
        $this->_init('Amasty\Rewards\Model\ResourceModel\Rule');
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsInstance()
    {
        return $this->actionFactory->create();
    }

    /**
     * Get Rule label by specified store
     *
     * @param \Magento\Store\Model\Store|int|bool|null $store
     * @return string|bool
     */
    public function getStoreLabel($store = null)
    {
        $storeId = $this->storeManager->getStore($store)->getId();
        $labels = (array)$this->getStoreLabels();

        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        } elseif (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        }

        return false;
    }

    /**
     * Set if not yet and retrieve rule store labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }

    public function activate()
    {
        $this->setIsActive(1);
        $this->save();
        return $this;
    }

    public function inactivate()
    {
        $this->setIsActive(0);
        $this->save();
        return $this;
    }

    public function loadByAction($action)
    {
        $this->addData($this->getResource()->loadByAction($action));
        return $this;
    }

    public function addDiscountDescription($address, $pointsUsed)
    {
        $description = $address->getDiscountDescriptionArray();
        $description['amrewards'] = __('Used %1 reward points', $pointsUsed);

        $address->setDiscountDescriptionArray($description);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->_getData(RuleInterface::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($ruleId)
    {
        $this->setData(RuleInterface::RULE_ID, $ruleId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->_getData(RuleInterface::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        $this->setData(RuleInterface::IS_ACTIVE, $isActive);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_getData(RuleInterface::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->setData(RuleInterface::NAME, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsSerialized()
    {
        return $this->_getData(RuleInterface::CONDITIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        $this->setData(RuleInterface::CONDITIONS_SERIALIZED, $conditionsSerialized);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->_getData(RuleInterface::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($action)
    {
        $this->setData(RuleInterface::ACTION, $action);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->_getData(RuleInterface::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        $this->setData(RuleInterface::AMOUNT, $amount);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpentAmount()
    {
        return $this->_getData(RuleInterface::SPENT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSpentAmount($spentAmount)
    {
        $this->setData(RuleInterface::SPENT_AMOUNT, $spentAmount);

        return $this;
    }
}

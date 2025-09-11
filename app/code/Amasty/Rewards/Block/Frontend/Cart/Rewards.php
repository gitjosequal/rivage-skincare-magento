<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Frontend\Cart;

/**
 * Product View block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Rewards extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Rewards\Helper\Data
     */
    private $helper;

    /**
     * @var array
     */
    private $rewardsData;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Rewards\Helper\Data $helper,
        array $data
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve customer data object
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getRewardsData()['customerId'];
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->getRewardsData()['pointsLeft'];
    }

    /**
     * @return mixed
     */
    public function getUsedPoints()
    {
        return $this->getRewardsData()['pointsUsed'];
    }

    /**
     * @return float
     */
    public function getPointsRate()
    {
        return $this->getRewardsData()['pointsRate'];
    }

    /**
     * @return mixed
     */
    public function getCurrentCurrencyCode()
    {
        $currentCurrency = $this->_storeManager->getStore()->getCurrentCurrency();

        return $currentCurrency->getCurrencyCode();
    }

    /**
     * @return int
     */
    public function getRateForCurrency()
    {
        return $this->getRewardsData()['rateForCurrency'];
    }

    /**
     * @return array
     */
    private function getRewardsData() {
        if (!isset($this->rewardsData)) {
            $this->rewardsData = $this->helper->getRewardsData();
        }

        return $this->rewardsData;
    }

    /**
     * @return int
     */
    public function getMinimumRewardsBalance()
    {
        return (int)$this->helper->getMinimumPointsValue($this->_storeManager->getStore()->getId());
    }
}
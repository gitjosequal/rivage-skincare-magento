<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Frontend;


class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'index.phtml';

    protected $rewards;

    /**
     * @var \Amasty\Rewards\Model\Rewards
     */
    protected $_rewardsModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Rewards\Model\Rewards $rewards,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->_rewardsModel = $rewards;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Rewards'));
    }

    /**
     * @return bool|\Amasty\Rewards\Model\Rewards
     */
    public function getRewards()
    {
        if (!($customerId = $this->getCustomerId())) {
            return false;
        }

        if (!$this->rewards) {
            $this->rewards = $this->_rewardsModel->getCustomerRewards($customerId);
        }
        return $this->rewards;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $rewards = $this->getRewards();

        if ($rewards->getSize()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.rewards.history.pager'
            )->setCollection(
                $rewards
            );
            $this->setChild('pager', $pager);
            $rewards->load();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    public function getStatistic()
    {
        return $this->coreRegistry->registry('current_amasty_rewards_statistic');
    }

    public function getCustomerId()
    {
        $customerId = $this->coreRegistry->registry('current_amasty_customer');
        return $customerId;
    }
}

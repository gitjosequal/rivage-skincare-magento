<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Controller\Index;

use Magento\Customer\Model\Session as CustomerSession;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Amasty\Rewards\Model\RewardsFactory
     */
    protected $rewardsFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        CustomerSession $customerSession,
        \Amasty\Rewards\Model\RewardsFactory $rewardsFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
        $this->rewardsFactory = $rewardsFactory;
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $customerId = $this->getCustomerId();

        if ($customerId) {
            /** @var \Amasty\Rewards\Model\Rewards $model */
            $model = $this->rewardsFactory->create();

            $this->_coreRegistry->register('current_amasty_customer', $customerId);
            $statistic = $model->getStatistic($customerId);

            $this->_coreRegistry->register('current_amasty_rewards_statistic', $statistic);

            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $this->_view->renderLayout();
        } else {
            return $this->_redirect('customer/account/login');
        }
    }

    /**
     * Retrieve customer data object
     *
     * @return int
     */
    protected function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }
}

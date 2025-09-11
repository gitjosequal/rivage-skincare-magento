<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Controller\Adminhtml;

abstract class Rule extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Amasty\Rewards\Model\RuleFactory
     */
    protected $rewardsRuleFactory;

    /**
     * @var \Amasty\Rewards\Api\RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\Rewards\Model\RuleFactory $rewardsRuleFactory,
        \Amasty\Rewards\Api\RuleRepositoryInterface $ruleRepository
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->rewardsRuleFactory = $rewardsRuleFactory;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Amasty_Rewards::rule')->_addBreadcrumb(__('Rewards'), __('Rewards'));
        return $this;
    }

    /**
     * Determine if authorized to perform group action.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Rewards::rule');
    }
}

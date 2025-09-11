<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Controller\Adminhtml\Rule;

class Edit extends \Amasty\Rewards\Controller\Adminhtml\Rule
{
    /**
     * Dispatch request
     *
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = $this->rewardsRuleFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_getSession()->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        } else {
            $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        }
        $this->_coreRegistry->register('current_amasty_rewards_rule', $model);
        $this->_initAction();
        if ($model->getId()) {
            $title = __('Edit Reward Rule `%1`', $model->getName());
        } else {
            $title = __("Add new Reward Rule");
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_view->renderLayout();
    }
}

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

class Duplicate extends \Amasty\Rewards\Controller\Adminhtml\Rule
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('rule_id');
        if (!$id) {
            $this->messageManager->addErrorMessage(__('Please select a rule to duplicate.'));
            return $this->_redirect('*/*');
        }
        try {
            $rule = clone $this->ruleRepository->get($id);
            $rule->setIsActive(0);
            $rule->setId(null);
            $this->ruleRepository->save($rule);

            $this->messageManager
                ->addSuccessMessage(__('The rule has been duplicated. Please feel free to activate it.'));

            return $this->_redirect('*/*/edit', ['id' => $rule->getId()]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('*/*');
            return false;
        }
    }
}

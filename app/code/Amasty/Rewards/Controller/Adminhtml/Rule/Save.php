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

class Save extends \Amasty\Rewards\Controller\Adminhtml\Rule
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();
            $id = (int)$this->getRequest()->getParam('id');
            try {
                $model = $this->rewardsRuleFactory->create();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                unset($data['rule']);

                $model->setId($id);
                $model->addData($data);
                $model->loadPost($data);

                $this->_getSession()->setPageData($model->getData());
                $this->ruleRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the item.'));
                $this->_getSession()->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_getSession()->setPageData($data);
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($id) {
                    $this->_redirect('*/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('*/*/new');
                }
                return;
            }
        }
        $this->_redirect('*/*/');
    }
}

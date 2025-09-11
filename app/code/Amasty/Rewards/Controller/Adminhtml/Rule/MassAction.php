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


class MassAction extends \Amasty\Rewards\Controller\Adminhtml\Rule
{
    public function execute()
    {
        $ids = $this->getRequest()->getParam('rules');
        $action = $this->getRequest()->getParam('action');
        if ($ids && in_array($action, ['activate', 'inactivate', 'delete'])) {
            try {
                /**
                 * @var $collection \Amasty\Rewards\Model\ResourceModel\Rule\Collection
                 */
                $collection = $this->_objectManager->create('Amasty\Rewards\Model\ResourceModel\Rule\Collection');

                $collection->addFieldToFilter('rule_id', array('in'=>$ids));
                $collection->walk($action);
                switch($action) {
                    case 'activate':
                        $messageSuccess = __('You activated the rule(s).');
                        break;
                    case 'inactivate':
                        $messageSuccess = __('You inactivated the rule(s).');
                        break;
                    default:
                        $messageSuccess = __('You deleted the rule(s).');
                        break;
                }
                $this->messageManager->addSuccessMessage($messageSuccess);
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete rule(s) right now. Please review the log and try again.').$e->getMessage()
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule(s) to delete.'));
        $this->_redirect('*/*/');
    }
}
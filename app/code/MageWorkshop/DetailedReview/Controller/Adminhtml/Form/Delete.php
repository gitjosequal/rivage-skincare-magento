<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml\Form;

class Delete extends \MageWorkshop\DetailedReview\Controller\Adminhtml\AbstractForm
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        // Default attribute set can not be deleted. This is validate in the _beforeSave method
        // of the attribute set model

        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('form_id');
        if ($attributeSet = $this->attributeHelper->getAttributeSet($id)) {
            if ($attributeSet->getEntityTypeId() != $this->getEntityTypeId()) {
                $this->messageManager->addErrorMessage(__(self::INVALID_ENTITY_TYPE_EXCEPTION));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $attributeSet->delete();
                $this->messageManager->addSuccessMessage(__('Review form was successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['attribute_id' => $this->getRequest()->getParam('attribute_id')]
                );
            }
        }
        $this->messageManager->addErrorMessage(__(self::FORM_NO_LONGER_EXISTS_EXCEPTION));
        return $resultRedirect->setPath('*/*/');
    }
}

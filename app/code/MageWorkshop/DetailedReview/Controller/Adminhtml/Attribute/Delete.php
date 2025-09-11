<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml\Attribute;

class Delete extends \MageWorkshop\DetailedReview\Controller\Adminhtml\AbstractAttribute
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('attribute_id')) {
            /** @var \MageWorkshop\DetailedReview\Model\Attribute $attribute */
            $attribute = $this->attributeFactory->create();

            // entity type check
            $attribute->load($id);
            if ($attribute->getEntityTypeId() != $this->getEntityTypeId()) {
                $this->messageManager->addErrorMessage(__('This is not a review attribute. It can not be deleted.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $attribute->delete();
                $this->messageManager->addSuccessMessage(__('Review attribute was successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['attribute_id' => $this->getRequest()->getParam('attribute_id')]
                );
            }
        }
        $this->messageManager->addErrorMessage(__(self::ATTRIBUTE_NO_LONGER_EXISTS_EXCEPTION));
        return $resultRedirect->setPath('*/*/');
    }
}

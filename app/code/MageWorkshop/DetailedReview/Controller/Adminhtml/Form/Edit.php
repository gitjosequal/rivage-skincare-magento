<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml\Form;

class Edit extends \MageWorkshop\DetailedReview\Controller\Adminhtml\AbstractForm
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('form_id');
        if ($attributeSet = $this->attributeHelper->getAttributeSet($id)) {
            if (!$attributeSet->getId() || $attributeSet->getEntityTypeId() != $this->getEntityTypeId()) {
                $this->messageManager->addErrorMessage(__(self::FORM_NO_LONGER_EXISTS_EXCEPTION));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getData(self::REGISTRY_KEY, true);
        if (!empty($data)) {
            $attributeSet->addData($data);
        }
        $attributeData = $this->getRequest()->getParam(self::REGISTRY_KEY);
        if (!empty($attributeData) && $id) {
            $attributeSet->addData($attributeData);
        }

        $this->coreRegistry->register(self::REGISTRY_KEY, $attributeSet);

        $item = $id ? __('Edit Review Form') : __('Add New Review Form');

        $resultPage = $this->createActionPage($item);
        $resultPage->getConfig()->getTitle()->prepend(
            $id ? $attributeSet->getAttributeSetName() : __('Add New Review Form')
        );
        return $resultPage;
    }
}

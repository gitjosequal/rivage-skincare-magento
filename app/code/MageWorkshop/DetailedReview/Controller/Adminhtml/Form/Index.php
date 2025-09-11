<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml\Form;

class Index extends \MageWorkshop\DetailedReview\Controller\Adminhtml\AbstractForm
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->createActionPage();
        // We may want to use this approach instead of the layout if the functionality is moved into the EAV module
        // $resultPage->addContent(
        //     $resultPage->getLayout()->createBlock('MageWorkshop\DetailedReview\Block\Adminhtml\Attribute')
        // );
        return $resultPage;
    }
}

<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Review;

/**
 * This form is used to emulate real review form rendering so that our plugin can inject additional fields in it
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form. Unfortunately, we need to make a public method because the form fields are rendered during the
     * AJAX calls and we need to render each element separately, but not to render the whole form
     */
    public function prepareForm()
    {
        $this->_prepareForm();
        $this->_initFormValues();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        // Depends on the mode - creating new review or creating an existing one. See form id in:
        // \Magento\Review\Block\Adminhtml\Add\Form at line #62
        // \Magento\Review\Block\Adminhtml\Edit\Form at line #65
        $reviewFormId = $this->getRequest()->getParam('review_id')
            ? 'review_details'
            : 'add_review_form';
        $form->addFieldset($reviewFormId, []);

        $this->setForm($form);
        $form->setValues([]);
        return parent::_prepareForm();
    }
}

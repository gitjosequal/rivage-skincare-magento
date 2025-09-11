<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml\Form;

use MageWorkshop\DetailedReview\Helper\Attribute;

class Save extends \MageWorkshop\DetailedReview\Controller\Adminhtml\AbstractForm
{
    const UNACCEPTABLE_ATTRIBUTE_CODE_EXCEPTION =
        'Attribute code "%1" is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.';

    const SUCCESS_MESSAGE = 'Review form was saved.';
    /**
     * @var \MageWorkshop\DetailedReview\Model\Indexer\Eav\Processor
     */
    private $eavIndexProcessor;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     * @param \MageWorkshop\DetailedReview\Model\Indexer\Eav\Processor $eavIndexProcessor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory,
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper,
        \MageWorkshop\DetailedReview\Model\Indexer\Eav\Processor $eavIndexProcessor
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $resultPageFactory,
            $resultJsonFactory,
            $forwardFactory,
            $attributeHelper
        );
        $this->eavIndexProcessor = $eavIndexProcessor;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        try {
            $attributeSet = $this->attributeHelper->createAttributeSet(
                (int) $request->getParam('form_id'),
                $request->getParam('attribute_set_name'),
                (array) $request->getParam(Attribute::INCLUDED_FIELDS),
                (array) $request->getParam(Attribute::AVAILABLE_FIELDS)
            );

            $this->_getSession()->setReviewFormData(false);
            // Refresh flat tables scheme because it can become invalid in case the attributes are added/removed
            $this->eavIndexProcessor->reindexAll();
            $this->messageManager->addSuccessMessage(__(self::SUCCESS_MESSAGE));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        if ($request->getParam('back', false) && isset($attributeSet)) {
            $resultRedirect->setPath('*/*/edit', ['form_id' => $attributeSet->getId(), '_current' => true]);
        } else {
            $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect;
    }
}

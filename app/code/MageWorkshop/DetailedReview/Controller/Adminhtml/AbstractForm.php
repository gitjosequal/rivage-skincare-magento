<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml;

abstract class AbstractForm extends \Magento\Backend\App\Action
{
    /**
     * ACL resource. Should be overwritten in the child classes once this is moved to the EAV module
     *
     * @var string $resource
     */
    const RESOURCE = 'mageworkshop_detailedreview::review_form';

    const FORM_NO_LONGER_EXISTS_EXCEPTION = 'This review form no longer exists.';

    const FORM_NAME_EXISTS_EXCEPTION = 'Review form with the same name already exists.';

    const FORM_NAME_MISSED_EXCEPTION = 'Form name is missed.';

    const FORM_FIELDS_INVALID_EXCEPTION = 'The following form fields do not exist or are invalid: %1';

    const INVALID_ENTITY_TYPE_EXCEPTION
        = 'This attribute set does not belong to the Review entity and can not be edited here.';

    const REGISTRY_KEY = 'review_form_data';

    /** @var int $entityTypeId */
    protected $entityTypeId;

    /** @var \Magento\Framework\Registry $coreRegistry */
    protected $coreRegistry;

    /** @var \Magento\Framework\View\Result\PageFactory $resultPageFactory */
    protected $resultPageFactory;

    /** @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory */
    protected $resultJsonFactory;

    /** @var \Magento\Backend\Model\View\Result\ForwardFactory */
    protected $resultForwardFactory;

    /** @var \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper */
    protected $attributeHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory,
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultForwardFactory = $forwardFactory;
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * @param \Magento\Framework\Phrase|null $title
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Review Forms'), __('Review Forms'))
            ->addBreadcrumb(__('Manage Review Forms'), __('Manage Review Forms'))
            ->setActiveMenu(self::RESOURCE);
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }
        $resultPage->getConfig()->getTitle()->prepend(__('Review Forms'));
        return $resultPage;
    }

    /**
     * @return int
     */
    public function getEntityTypeId()
    {
        return $this->attributeHelper->getEntityTypeId();
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::RESOURCE);
    }
}

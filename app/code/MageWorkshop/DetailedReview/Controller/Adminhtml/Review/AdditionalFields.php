<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml\Review;

use MageWorkshop\DetailedReview\Model\Details;
use MageWorkshop\DetailedReview\Plugin\Review\Block\Adminhtml\Review\FormPlugin;
use MageWorkshop\DetailedReview\Block\Adminhtml\Review\Form as ReviewForm;

class AdditionalFields extends \Magento\Backend\App\Action
{
    const RESOURCE = 'Magento_Review::reviews_all';

    /** @var string $entityType */
    protected $entityType = Details::ENTITY;

    /** @var int $entityTypeId */
    protected $entityTypeId;

    /** @var \Magento\Framework\Cache\FrontendInterface $attributeLabelCache */
    protected $attributeLabelCache;

    /** @var \Magento\Framework\Registry $coreRegistry */
    protected $coreRegistry;

    /** @var \Magento\Framework\View\Result\PageFactory $resultPageFactory */
    protected $resultPageFactory;

    /** @var \Magento\Framework\Filter\FilterManager $filter */
    protected $filter;

    /** @var \MageWorkshop\DetailedReview\Model\AttributeFactory $attributeFactory */
    protected $attributeFactory;

    /** @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory */
    protected $resultJsonFactory;

    /** @var \Magento\Framework\View\LayoutFactory */
    protected $layoutFactory;

    /** @var \Magento\Backend\Model\View\Result\ForwardFactory */
    protected $resultForwardFactory;

    /** @var \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper */
    protected $attributeHelper;

    /** @var \Magento\Review\Model\ReviewFactory $reviewFactory */
    protected $reviewFactory;

    /**
     * FieldsList constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->reviewFactory = $reviewFactory;
    }

    public function execute()
    {
//        if ($this->getRequest()->getParam($this->_objectId)) {
//            $reviewData = $this->_reviewFactory->create()->load($this->getRequest()->getParam($this->_objectId));
//            $this->_coreRegistry->register('review_data', $reviewData);
//        }
//
//
//        $resultPage->setActiveMenu('Magento_Review::catalog_reviews_ratings_reviews_all');
//        $resultPage->getConfig()->getTitle()->prepend(__('Customer Reviews'));
//        $resultPage->getConfig()->getTitle()->prepend(__('Edit Review'));
//        $resultPage->addContent($resultPage->getLayout()->createBlock('Magento\Review\Block\Adminhtml\Edit'));


        // @TODO: may need to fake review data in case of editing the review?
        // Though may not do this, but just hide the unneeded fields
        // data should be deleted ( So of course better to reload data then to hide fields
        // but note that
        $selectedStores = explode(',', $this->getRequest()->getParam(FormPlugin::SELECT_STORES, ''));
        $this->coreRegistry->register(FormPlugin::SELECT_STORES, $selectedStores);

        if ($reviewId = (int) $this->getRequest()->getParam('review_id')) {
            $reviewData = $this->reviewFactory->create()->load($reviewId);
            $this->coreRegistry->register('review_data', $reviewData);
        }

        if ($productId = (int) $this->getRequest()->getParam('product_id')) {
            $this->coreRegistry->register(FormPlugin::PRODUCT_ID, $productId);
        }

        $layout = $this->layoutFactory->create();
        /** @var ReviewForm $reviewFormWidget */
        $reviewFormWidget = $layout->createBlock(ReviewForm::class);
        $reviewFormWidget->prepareForm();

        if (isset($reviewData)) {
            $targetFieldSetName = 'review_details';
        } else {
            $targetFieldSetName = 'add_review_form';
        }

        $targetFieldSet = false;
        foreach ($reviewFormWidget->getForm()->getElements() as $element) {
            if ($element->getId() == $targetFieldSetName) {
                $targetFieldSet = $element;
                break;
            }
        }

        $additionalElementsHtml = [];
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $targetFieldSet */
        if ($targetFieldSet) {
            /** @var \Magento\Framework\Data\Form\Element\AbstractElement $element */
            foreach ($targetFieldSet->getElements() as $element) {
                $additionalElementsHtml[$element->getId()] = $element->toHtml();
            }
        }

        return $this->resultJsonFactory->create()->setData([
            'fields' => $additionalElementsHtml
        ]);
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

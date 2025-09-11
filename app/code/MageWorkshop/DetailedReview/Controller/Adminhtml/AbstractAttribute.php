<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml;

use MageWorkshop\DetailedReview\Model\Details;

abstract class AbstractAttribute extends \Magento\Backend\App\Action
{
    /**
     * ACL resource. Should be overwritten in the child classes once this is moved to the EAV module
     *
     * @var string $resource
     */
    const RESOURCE = 'mageworkshop_detailedreview::review_attribute';

    const ATTRIBUTE_NO_LONGER_EXISTS_EXCEPTION = 'This attribute no longer exists.';

    const INVALID_ENTITY_TYPE_EXCEPTION = 'This attribute belongs to another entity and can not be edited here.';

    /** @var string $entityType */
    protected $entityType = Details::ENTITY;

    /** @var int $entityTypeId */
    protected $entityTypeId;

    /** @var \Magento\Framework\Cache\FrontendInterface $attributeLabelCache */
    protected $attributeLabelCache;

    /** @var \Magento\Framework\Registry $coreRegistry */
    protected $coreRegistry;

    /** @var \MageWorkshop\DetailedReview\Api\Data\EntityConfigInterface $entityConfig */
    protected $entityConfig;

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

    /* @var \MageWorkshop\Core\Helper\Serializer */
    protected $serializer;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Cache\FrontendInterface $attributeLabelCache
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \MageWorkshop\DetailedReview\Api\Data\EntityConfigInterface $entityConfig
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \MageWorkshop\DetailedReview\Model\AttributeFactory $attributeFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Cache\FrontendInterface $attributeLabelCache,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MageWorkshop\DetailedReview\Api\Data\EntityConfigInterface $entityConfig,
        \Magento\Framework\Filter\FilterManager $filter,
        \MageWorkshop\DetailedReview\Model\AttributeFactory $attributeFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $forwardFactory,
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper,
        \MageWorkshop\Core\Helper\Serializer $serializer
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->attributeLabelCache = $attributeLabelCache;
        $this->resultPageFactory = $resultPageFactory;
        $this->entityConfig = $entityConfig;
        $this->filter = $filter;
        $this->attributeFactory = $attributeFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->resultForwardFactory = $forwardFactory;
        $this->attributeHelper = $attributeHelper;
        $this->serializer = $serializer;
    }

    /**
     * @param \Magento\Framework\Phrase|null $title
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Review Fields'), __('Review Fields'))
            ->addBreadcrumb(__('Manage Review Fields'), __('Manage Review Fields'))
            ->setActiveMenu(self::RESOURCE);
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }
        $resultPage->getConfig()->getTitle()->prepend(__('Review Fields'));
        return $resultPage;
    }

    /**
     * Generate code from label
     *
     * @param string $label
     * @return string
     */
    protected function generateCode($label)
    {
        $code = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                $this->filter->translitUrl($label)
            ),
            0,
            30
        );
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ? $code : substr(md5(time()), 0, 8));
        }
        return $code;
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

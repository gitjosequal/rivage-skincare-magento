<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Controller\Adminhtml\Set;

use Magento\Framework\Controller\ResultFactory;

class BanByReview extends \Magento\Backend\App\Action
{
    /**
     * @var \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper
     */
    private $banHelper;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * BanByReview constructor.
     * @param \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->banHelper = $banHelper;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $reviewsIds = $this->getRequest()->getParam('reviews');
        $banPeriod  = $this->getRequest()->getParam('ban');

        if (!is_array($reviewsIds) || !$banPeriod) {
            $this->messageManager->addErrorMessage(__('Please select review(s).'));
        } else {
            $reviewCollection = $this->reviewCollectionFactory->create();
            $reviewCollection->addFieldToFilter('main_table.review_id', ['in' => $reviewsIds]);

            try {
                $this->banHelper->banByReviews($reviewCollection, $banPeriod);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('review/product/index');
        return $resultRedirect;
    }
}

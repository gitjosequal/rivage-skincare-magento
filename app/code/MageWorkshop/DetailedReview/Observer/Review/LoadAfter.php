<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Observer\Review;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class LoadAfter implements ObserverInterface
{
    /** @var \MageWorkshop\DetailedReview\Helper\Review $reviewHelper */
    private $reviewHelper;

    /** @var \MageWorkshop\DetailedReview\Model\ResourceModel\Details\CollectionFactory $detailsFactory */
    private $detailsFactory;

    /**
     * LoadAfter constructor.
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \MageWorkshop\DetailedReview\Model\ResourceModel\Details\CollectionFactory $detailsFactory
     */
    public function __construct(
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \MageWorkshop\DetailedReview\Model\ResourceModel\Details\CollectionFactory $detailsFactory
    ) {
        $this->reviewHelper = $reviewHelper;
        $this->detailsFactory = $detailsFactory;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Review\Model\Review $review */
        $review = $observer->getEvent()->getData('object');
        if (!$this->reviewHelper->isProductReview($review)) {
            return;
        }

        /** @var \MageWorkshop\DetailedReview\Model\ResourceModel\Details\Collection $collection */
        // Can not use $details->load($review->getId(), 'review_id');
        // This won't work because review_id is ignored in \Magento\Eav\Model\Entity\AbstractEntity::load()
        $collection = $this->detailsFactory->create();
        $collection->addFieldToFilter('review_id', $review->getId())
            ->addAttributeToSelect('*');
        $collection->getSelect()
            ->limit(1);
        /** @var \MageWorkshop\DetailedReview\Model\Details $details */
        $details = $collection->getFirstItem();

        // @TODO: option value and swatch value are not injected here, so maybe we need to use flat table anyway
        if ($details->getId()) {
            /** @var \MageWorkshop\DetailedReview\Model\ResourceModel\Details $resource */
            $resource = $details->getResource();
            $defaultAttributes = $resource->getDefaultAttributes();

            foreach ((array) $details->getData() as $attribute => $value) {
                if (!in_array($attribute, $defaultAttributes, true)) {
                    $review->setData($attribute, $value);
                }
            }
        }
    }
}

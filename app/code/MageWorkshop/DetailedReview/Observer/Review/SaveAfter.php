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
use Magento\Framework\Exception\LocalizedException;
use MageWorkshop\DetailedReview\Model\Details;
use Magento\Review\Model\Review;

class SaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Eav\Model\Config $eavConfig
     */
    private $eavConfig;

    /**
     * @var \MageWorkshop\DetailedReview\Model\DetailsFactory $detailsFactory
     */
    private $detailsFactory;

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     */
    private $reviewHelper;

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     */
    private $attributeHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    private $date;

    /**
     * @var \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor
     */
    private $detailsFlatProcessor;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \MageWorkshop\DetailedReview\Model\DetailsFactory $detailsFactory
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \MageWorkshop\DetailedReview\Model\DetailsFactory $detailsFactory,
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor
    ) {
        $this->eavConfig = $eavConfig;
        $this->detailsFactory = $detailsFactory;
        $this->reviewHelper = $reviewHelper;
        $this->attributeHelper = $attributeHelper;
        $this->date = $date;
        $this->detailsFlatProcessor = $detailsFlatProcessor;
    }

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        try {
            // Should check if the entity is installed. This will not work while installing Magento_ReviewSampleData
            // because modules load sequence is not correct and there is no good way to change it.
            // Our entity does not exist at that moment and this is the way to validate data
            $this->eavConfig->getEntityType(Details::ENTITY);
        } catch (LocalizedException $e) {
            return;
        }

        /** @var Review $review */
        $review = $observer->getEvent()->getData('object');

        if (!$this->reviewHelper->isProductReview($review)) {
            return;
        }

        $attributeCollection = $this->attributeHelper->getVisibleAttributesCollection();
        $attributeCollection->addFieldToFilter('is_user_defined', 1);

        // $details->load($review->getId(), 'review_id');
        // This won't work because review_id is ignored in /Magento/Eav/Model/Entity/AbstractEntity::load()

        /** @var \MageWorkshop\DetailedReview\Model\Details $details */
        $details = $this->detailsFactory->create();
        $details->loadByReviewId($review->getId());

        try {
            $hasData = false;
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            foreach ($attributeCollection as $attribute) {
                $attributeCode = $attribute->getAttributeCode();
                if ($review->hasData($attributeCode)) {
                    $hasData = true;
                    $reviewData = $review->getData($attributeCode);
                    if (is_array($reviewData)) {
                        $reviewData = implode(',', $reviewData);
                    }
                    if ($reviewData != $details->getData($attributeCode)) {
                        $details->setData($attributeCode, $reviewData);
                    }
                } else {
                    $details->unsetData($attributeCode);
                }
            }

            if ($hasData) {
                // $details->hasDataChanges() will return false after save, so we need to remember the state here
                if ($hasDetailsDataChanges = $details->hasDataChanges()) {
                    if ($details->isObjectNew()) {
                        $details->setData('review_id', $review->getId());
                    }

                    $details->setUpdatedAt($this->date->gmtTimestamp());
                    $details->save();
                }
                // invalidate if details data or review status has changed
                $this->invalidateIndexer($review, $hasDetailsDataChanges);
            } elseif ($details->getId()) {
                $details->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Review $review
     * @param bool $hasDetailsDataChanges
     */
    private function invalidateIndexer(Review $review, $hasDetailsDataChanges)
    {
        // If the status have changed OR object is new and status is approved - invalidate the item
        $newStatusId = (int) $review->getStatusId();
        $origStatusId = (int) $review->getOrigData('status_id');

        $objectIsNew = (null === $origStatusId);
        $statusHasChanged = ($newStatusId === Review::STATUS_APPROVED xor $origStatusId === Review::STATUS_APPROVED);

        $shouldInvalidateIndex = false;

        if ($objectIsNew && ($newStatusId === Review::STATUS_APPROVED)) {
            $shouldInvalidateIndex = true;
        }

        if (!$objectIsNew && ($hasDetailsDataChanges || $statusHasChanged)) {
            $shouldInvalidateIndex = true;
        }

        if ($shouldInvalidateIndex) {
            $this->detailsFlatProcessor->markIndexerAsInvalid();
        }
    }
}

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

class SaveCommitAfter implements ObserverInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review
     */
    private $reviewHelper;

    /**
     * @var \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor
     */
    private $detailsFlatProcessor;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor
    ) {
        $this->detailsFlatProcessor = $detailsFlatProcessor;
        $this->eavConfig = $eavConfig;
        $this->reviewHelper = $reviewHelper;
    }

    /**
     * @param Observer $observer
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

        try {
            $this->detailsFlatProcessor->reindexRow((int) $review->getId());
        } catch (\Exception $e) {
            return;
        }
    }
}

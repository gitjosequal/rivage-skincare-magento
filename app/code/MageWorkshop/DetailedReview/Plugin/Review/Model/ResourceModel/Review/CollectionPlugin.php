<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Plugin\Review\Model\ResourceModel\Review;

use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Framework\Db\Select;
use MageWorkshop\DetailedReview\Model\Details;
use Magento\Review\Model\Review;
use Magento\Review\Model\Rating\Option\Vote;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection as VoteCollection;
use Magento\Framework\Data\Collection;

class CollectionPlugin
{
    /**
     * @var \MageWorkshop\DetailedReview\Model\Indexer\TableBuilder $tableBuilder
     */
    private $tableBuilder;

    /**
     * @var \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $voteCollectionFactory
     */
    private $voteCollectionFactory;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    private $dataCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * Rating votes cache. This data is used in multiple places and the load is not optimized. Caching them per request
     * to eliminate additional load in loop when addRatingVotes() is called
     * @var array $ratirgCache
     */
    private static $ratingVotesCache = [];

    /**
     * CollectionPlugin constructor.
     * @param \MageWorkshop\DetailedReview\Model\Indexer\TableBuilder $tableBuilder
     * @param \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $voteFactory
     * @param \Magento\Framework\Data\CollectionFactory $dataCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageWorkshop\DetailedReview\Model\Indexer\TableBuilder $tableBuilder,
        \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $voteFactory,
        \Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->tableBuilder = $tableBuilder;
        $this->voteCollectionFactory = $voteFactory;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Inject additional review fields
     * @param ReviewCollection $subject
     * @param Select $result
     * @return Select
     * @throws \Zend_Db_Select_Exception
     */
    public function afterGetSelect(ReviewCollection $subject, Select $result)
    {
        $resource = $subject->getResource();
        $additionalDetailsTable = $resource->getTable($this->tableBuilder->getFlatTable(Details::ENTITY));

        if ($resource->getConnection()->isTableExists($additionalDetailsTable)) {
            $shouldJoinAdditionalTable = false;

            foreach ($result->getPart(Select::FROM) as $from) {
                if ($from['tableName'] === $additionalDetailsTable) {
                    $shouldJoinAdditionalTable = false;
                    break;
                }
                $shouldJoinAdditionalTable = true;
            }

            if ($shouldJoinAdditionalTable) {
                $columns = $resource->getConnection()->describeTable($additionalDetailsTable);
                unset($columns['review_id']);

                $result->joinLeft(
                    ['flat' => $additionalDetailsTable],
                    'flat.review_id = main_table.review_id',
                    array_keys($columns)
                );
            }
        }

        return $result;
    }

    /**
     * Optimize the original method to load all data at once instead of loading votes for every review
     * This leads to a huge performance degradation if there are 100+ reviews for the product
     * This leads to problems when:
     * - average rating is calculated
     * - SEO extension outputs all reviews on a Product Page for bots
     * @param Collection $subject
     * @return Collection
     */
    public function aroundAddRateVotes(Collection $subject)
    {
        $reviewIds = [];

        /** @var Review $review */
        foreach ($subject->getItems() as $review) {
            $reviewId = (int) $review->getId();

            if (!isset(self::$ratingVotesCache[$reviewId])) {
                $reviewIds[] = $reviewId;
            }
        }

        if (!empty($reviewIds)) {
            /** @var VoteCollection $voteCollection */
            $voteCollection = $this->voteCollectionFactory->create();
            $voteCollection->setStoreFilter($this->storeManager->getStore()->getId())
                ->addRatingInfo($this->storeManager->getStore()->getId())
                ->addFieldToFilter('main_table.review_id', ['in' => $reviewIds]);

            // Review may not have votes, so need to set all keys first and then assign items if available
            foreach ($reviewIds as $reviewId) {
                self::$ratingVotesCache[$reviewId] = $this->dataCollectionFactory->create();
            }

            /** @var Vote $vote */
            foreach ($voteCollection as $vote) {
                self::$ratingVotesCache[(int) $vote->getReviewId()]->addItem($vote);
            }
        }

        /** @var Review $review */
        foreach ($subject as $review) {
            $reviewId = (int) $review->getId();
            /** @var Review $review */
            $review = $subject->getItemById($reviewId);
            $review->setRatingVotes(self::$ratingVotesCache[$reviewId]);
        }

        return $subject;
    }
}

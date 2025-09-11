<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Model;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use MageWorkshop\Voting\Api\Data\VoteStatisticsSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorkshop\Voting\Api\VoteStatisticsRepositoryInterface;
use MageWorkshop\Voting\Model\ResourceModel\Vote\Collection;
use MageWorkshop\Voting\Model\ResourceModel\Vote\Collection as VotesCollection;
use MageWorkshop\Voting\Model\Data\ReviewVotesStatistics;

class VoteStatisticsRepository implements VoteStatisticsRepositoryInterface
{
    /**
     * @var \MageWorkshop\Voting\Model\VoteFactory
     */
    protected $voteFactory;

    /**
     * @var \MageWorkshop\Voting\Model\ResourceModel\Vote
     */
    protected $resourceModel;

    /**
     * @var \MageWorkshop\Voting\Model\ResourceModel\Vote\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageWorkshop\Voting\Model\Data\VoteStatisticsSearchResultsFactory;
     */
    protected $voteStatisticsSearchResultsFactory;

    /**
     * @var \MageWorkshop\Voting\Model\Data\ReviewVotesStatisticsFactory
     */
    protected $reviewVotesStatisticsFactory;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * VoteStatisticsRepository constructor.
     * @param \MageWorkshop\Voting\Model\VoteFactory $voteFactory
     * @param \MageWorkshop\Voting\Model\ResourceModel\Vote $resourceModel
     * @param \MageWorkshop\Voting\Model\ResourceModel\Vote\CollectionFactory $collectionFactory
     * @param \MageWorkshop\Voting\Model\Data\VoteStatisticsSearchResultsFactory $voteStatisticsSearchResultsFactory
     * @param \MageWorkshop\Voting\Model\Data\ReviewVotesStatisticsFactory $reviewVotesStatisticsFactory
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \MageWorkshop\Voting\Model\VoteFactory $voteFactory,
        \MageWorkshop\Voting\Model\ResourceModel\Vote $resourceModel,
        \MageWorkshop\Voting\Model\ResourceModel\Vote\CollectionFactory $collectionFactory,
        \MageWorkshop\Voting\Model\Data\VoteStatisticsSearchResultsFactory $voteStatisticsSearchResultsFactory,
        \MageWorkshop\Voting\Model\Data\ReviewVotesStatisticsFactory $reviewVotesStatisticsFactory,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->voteFactory = $voteFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->voteStatisticsSearchResultsFactory = $voteStatisticsSearchResultsFactory;
        $this->reviewVotesStatisticsFactory = $reviewVotesStatisticsFactory;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get vote statistics objects list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return VoteStatisticsSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var VotesCollection $votesCollection */
        $votesCollection = $this->collectionFactory->create();

        $votesCollection->addExpressionFieldToSelect('positives_count', 'sum(vote > 0)', 'vote');
        $votesCollection->addExpressionFieldToSelect('negatives_count', 'sum(vote < 0)', 'vote');
        $votesCollection->getSelect()->group('review_id');


        $this->addFilterGroupToCollection($searchCriteria, $votesCollection);

        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $votesCollection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() === SortOrder::SORT_ASC) ? Collection::SORT_ORDER_ASC : Collection::SORT_ORDER_DESC
                );
            }
        }
        $votesCollection->setCurPage($searchCriteria->getCurrentPage());
        $votesCollection->setPageSize($searchCriteria->getPageSize());

        $items = [];
        /** @var Vote $voteItem */
        foreach ($votesCollection->getItems() as $voteItem) {
            /** @var ReviewVotesStatistics $voteStatisticsItem */
            $voteStatisticsItem = $this->reviewVotesStatisticsFactory->create();
            $voteStatisticsItem->setReviewId($voteItem->getReviewId())
                ->setPositivesCount((int) $voteItem->getPositivesCount())
                ->setNegativesCount((int) $voteItem->getNegativesCount())
                ->setCurrentCustomerVote((int) $voteItem->getCurrentCustomerVote());

            $items[$voteItem->getId()] = $voteStatisticsItem;
        }

        /** @var VoteStatisticsSearchResultsInterface $voteSearchResults */
        $voteSearchResults = $this->voteStatisticsSearchResultsFactory->create();
        $voteSearchResults->setItems($items);

        return $voteSearchResults;
    }

    /**
     * Apply the filters from the search criteria to the collection
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection $votesCollection
     */
    protected function addFilterGroupToCollection(SearchCriteriaInterface $searchCriteria, Collection $votesCollection)
    {
        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            /** @var Filter $filter */
            foreach ($filterGroup->getFilters() as $filter) {
                if (in_array($filter->getField(), [Vote::KEY_CUSTOMER_ID, Vote::KEY_GUEST_TOKEN], true)) {
                    $votesCollection->addExpressionFieldToSelect(
                        'current_customer_vote',
                        'sum(({{customer_id_field}} = "{{customer_id_value}}") * vote)',
                        [
                            'customer_id_field' => $filter->getField(),
                            'customer_id_value' => $filter->getValue()
                        ]
                    );
                } else {
                    $votesCollection->addFieldToFilter($filter->getField(), [$filter->getConditionType() => $filter->getValue()]);
                }
            }
        }
    }
}
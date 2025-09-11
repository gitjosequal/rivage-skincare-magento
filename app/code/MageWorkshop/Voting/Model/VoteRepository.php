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
use MageWorkshop\Voting\Model\Exception\NotFoundException;
use MageWorkshop\Voting\Api\VoteRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorkshop\Voting\Api\Data\VoteSearchResultsInterface;
use MageWorkshop\Voting\Model\Data\VoteSearchResults;
use MageWorkshop\Voting\Model\ResourceModel\Vote\Collection as VotesCollection;

class VoteRepository implements VoteRepositoryInterface
{
    const EXCEPTION_VOTE_DOES_NOT_EXIST = 'Vote item with ID %1$d doesn\'t exist.';

    /** @var \MageWorkshop\Voting\Model\Data\VoteSearchResults[] */
    protected $voteSearchResultsByProductId = [];
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
     * @var \MageWorkshop\Voting\Model\Data\VoteSearchResultsFactory
     */
    protected $voteSearchResultsFactory;

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
     * VoteRepository constructor.
     * @param VoteFactory $voteFactory
     * @param ResourceModel\Vote $resourceModel
     * @param ResourceModel\Vote\CollectionFactory $collectionFactory
     * @param Data\VoteSearchResultsFactory $voteSearchResultsFactory
     * @param Data\ReviewVotesStatisticsFactory $reviewVotesStatisticsFactory
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \MageWorkshop\Voting\Model\VoteFactory $voteFactory,
        \MageWorkshop\Voting\Model\ResourceModel\Vote $resourceModel,
        \MageWorkshop\Voting\Model\ResourceModel\Vote\CollectionFactory $collectionFactory,
        \MageWorkshop\Voting\Model\Data\VoteSearchResultsFactory $voteSearchResultsFactory,
        \MageWorkshop\Voting\Model\Data\ReviewVotesStatisticsFactory $reviewVotesStatisticsFactory,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->voteFactory = $voteFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->voteSearchResultsFactory = $voteSearchResultsFactory;
        $this->reviewVotesStatisticsFactory = $reviewVotesStatisticsFactory;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param Vote $vote
     * @return Vote
     * @throws \Exception
     */
    public function save(Vote $vote)
    {
        $vote->save();

        return $vote;
    }

    /**
     * @param int $id
     * @return Vote
     * @throws NotFoundException
     */
    public function getById($id)
    {
        /** @var Vote $voteModel */
        $voteModel = $this->voteFactory->create();
        $voteModel->load($id);

        if (!$voteModel->getId()) {
            throw new NotFoundException(sprintf(self::EXCEPTION_VOTE_DOES_NOT_EXIST, (int) $id));
        }

        return $voteModel;
    }

    /**
     * @param Vote $vote
     * @return void
     * @throws \Exception
     */
    public function delete(Vote $vote)
    {
        $vote->delete();
    }

    /**
     * @param $id
     * @return $this
     * @throws \Exception
     * @throws NotFoundException
     */
    public function deleteById($id)
    {
        /** @var Vote $voteModel */
        $voteModel = $this->voteFactory->create();
        $voteModel->setId($id)->delete();

        return $this;
    }

    /**
     * Get vote objects list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return VoteSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var VotesCollection $votesCollection */
        $votesCollection = $this->collectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $votesCollection);
        }

        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $votesCollection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() === SortOrder::SORT_ASC) ? VotesCollection::SORT_ORDER_ASC : VotesCollection::SORT_ORDER_DESC
                );
            }
        }
        $votesCollection->setCurPage($searchCriteria->getCurrentPage());
        $votesCollection->setPageSize($searchCriteria->getPageSize());

        /** @var VoteSearchResults $voteSearchResults */
        $voteSearchResults = $this->voteSearchResultsFactory->create();
        $voteSearchResults->setItems($votesCollection->getItems());

        return $voteSearchResults;
    }

    /**
     * @param FilterGroup $filterGroup
     * @param VotesCollection $collection
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, VotesCollection $collection)
    {
        /** @var Filter $filter */
        foreach ($filterGroup->getFilters() as $filter) {
            $collection->addFieldToFilter($filter->getField(), [$filter->getConditionType() => $filter->getValue()]);
        }
    }
}
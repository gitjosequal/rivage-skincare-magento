<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Block\Review;

use Magento\Review\Model\Review;
use MageWorkshop\Voting\Api\Data\VoteStatisticsSearchResultsInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use MageWorkshop\Voting\Model\Vote as VoteModel;
use MageWorkshop\Voting\Helper\Data as VotingHelper;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewsCollection;
use MageWorkshop\Voting\Model\Data\ReviewVotesStatistics;
use Magento\Store\Model\ScopeInterface;

class Vote extends \Magento\Framework\View\Element\Template
{
    /** Route path to send ajax requests */
    const AJAX_ROUTE_PATH = 'mageworkshop_voting/vote/ajax';

    const XML_PATH_VOTING_MESSAGE = 'mageworkshop_detailedreview/voting/voting_message';
    const XML_PATH_VOTING_HELPFUL_LABEL = 'mageworkshop_detailedreview/voting/helpful_label';
    const XML_PATH_VOTING_UNHELPFUL_LABEL = 'mageworkshop_detailedreview/voting/unhelpful_label';
    const XML_PATH_VOTING_ALERT_TITLE = 'mageworkshop_detailedreview/voting/alert_title';
    const XML_PATH_VOTING_ALERT_TIMEOUT = 'mageworkshop_detailedreview/voting/alert_timeout';

    /**
     * @var \MageWorkshop\Voting\Model\VoteStatisticsRepository
     */
    protected $voteStatisticsRepository;

    /**
     * \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Array of ReviewVotesStatistics objects with review ids as keys
     *
     * @var \MageWorkshop\Voting\Model\Data\ReviewVotesStatistics[]
     */
    protected $votesByReview = [];

    /**
     * @var \MageWorkshop\Voting\Helper\Data
     */
    protected $votingHelper;

    /**
     * @var \MageWorkshop\Voting\Helper\Criteria
     */
    protected $criteriaHelper;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\Collection
     */
    protected $reviewsCollection;

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review
     */
    protected $reviewHelper;

    /**
     * @var \MageWorkshop\DetailedReview\Logger\Logger
     */
    protected $logger;

    /**
     * @var \MageWorkshop\Voting\Model\Data\ReviewVotesStatisticsFactory
     */
    protected $reviewVoteStatisticsFactory;

    /**
     * Vote constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorkshop\Voting\Model\VoteStatisticsRepository $voteStatisticsRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \MageWorkshop\Voting\Helper\Data $votingHelper
     * @param \MageWorkshop\Voting\Helper\Criteria $criteriaHelper
     * @param \MageWorkshop\Voting\Model\Data\ReviewVotesStatisticsFactory $reviewVotesStatisticsFactory
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \MageWorkshop\DetailedReview\Logger\Logger $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageWorkshop\Voting\Model\VoteStatisticsRepository $voteStatisticsRepository,
        \Magento\Customer\Model\Session $customerSession,
        \MageWorkshop\Voting\Helper\Data $votingHelper,
        \MageWorkshop\Voting\Helper\Criteria $criteriaHelper,
        \MageWorkshop\Voting\Model\Data\ReviewVotesStatisticsFactory $reviewVotesStatisticsFactory,
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \MageWorkshop\DetailedReview\Logger\Logger $logger,
        array $data = []
    ) {
        $this->voteStatisticsRepository = $voteStatisticsRepository;
        $this->customerSession = $customerSession;
        $this->votingHelper = $votingHelper;
        $this->criteriaHelper = $criteriaHelper;
        $this->reviewVoteStatisticsFactory = $reviewVotesStatisticsFactory;
        $this->reviewHelper = $reviewHelper;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    /**
     * @var Review
     */
    protected $review;

    /**
     * @var int
     */
    protected $productId = 0;

    /**
     * @param Review $review
     * @return $this
     */
    public function setReview(Review $review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * @return Review
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @param ReviewsCollection $reviewsCollection
     */
    public function setReviewsCollection(ReviewsCollection $reviewsCollection)
    {
        $this->reviewsCollection = $reviewsCollection;
    }

    /**
     * Loads all the reviews for the product id from the review is not yet loaded.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function loadReviewVotesStatistics()
    {
        $reviewId = $this->getReviewId();

        if (isset($this->votesByReview[$reviewId])) {
            return;
        }

        $criteriaData = [];

        if ($customerId = (int) $this->customerSession->getCustomer()->getId()) {
            $criteriaData[VoteModel::KEY_CUSTOMER_ID] = $customerId;
        } else {
            // if the customer is a guest - use session hash for customer identification
            $criteriaData[VoteModel::KEY_GUEST_TOKEN] = $this->customerSession->getSessionId();
        }

        $criteriaData[VoteModel::KEY_PRODUCT_ID] = $this->getProductId();

        $reviewIdsFromPagination = $this->reviewsCollection->getColumnValues('review_id');

        if (!in_array($reviewId, $reviewIdsFromPagination, false)) {
            $reviewIdsFromPagination[] = $reviewId;
        }

        if ($this->reviewsCollection) {
            $criteriaData[VoteModel::KEY_REVIEW_ID] = $reviewIdsFromPagination;
        }

        /** @var SearchCriteria $criteria */
        $criteria = $this->criteriaHelper->createCriteriaByParamsArray($criteriaData);

        /** @var VoteStatisticsSearchResultsInterface $results */
        $results = $this->voteStatisticsRepository->getList($criteria);

        foreach ($results->getItems() as $item) {
            $this->votesByReview[$item->getReviewId()] = $item;
        }
        // if there is no votes for review - cache empty statistics objects for these items
        $reviewsWithoutStatistics = array_diff($reviewIdsFromPagination, array_keys($this->votesByReview));

        foreach ($reviewsWithoutStatistics as $reviewId) {
            /** @var ReviewVotesStatistics $newStatisticsItem */
            $newStatisticsItem = $this->reviewVoteStatisticsFactory->create();
            $newStatisticsItem->setReviewId($reviewId);
            $this->votesByReview[$reviewId] = $newStatisticsItem;
        }
    }

    /**
     * Return the review id
     * @return int
     */
    public function getReviewId()
    {
        return $this->review->getId();
    }

    /**
     * Returns the number positive votes for the review item
     * @return int
     * @throws LocalizedException
     */
    public function getPositiveVotesCount()
    {
        return $this->getReviewStatistics()->getPositivesCount();
    }

    /**
     * Returns the number positive votes for the review item
     * @return int
     * @throws LocalizedException
     */
    public function getNegativeVotesCount()
    {
        return $this->getReviewStatistics()->getNegativesCount();
    }

    /**
     * Returns the number positive votes for the review item
     * @return int
     * @throws LocalizedException
     */
    public function getCurrentCustomerVote()
    {
        return $this->getReviewStatistics()->getCurrentCustomerVote();
    }

    /**
     * @return ReviewVotesStatistics
     * @throws LocalizedException
     */
    protected function getReviewStatistics()
    {
        $this->loadReviewVotesStatistics();
        return $this->votesByReview[$this->getReviewId()];
    }

    /**
     * @return VotingHelper
     */
    public function getVotingHelper()
    {
        return $this->votingHelper;
    }

    /**
     * Returns the url for sending ajax requests
     * @return string
     * @throws LocalizedException
     */
    public function getAjaxUrl()
    {
        return $this->getUrl(self::AJAX_ROUTE_PATH, [
            'product_id' => $this->getProductId(),
            'review_id' => $this->getReviewId(),
        ]);
    }

    /**
     * Returns the product id from the current review object
     * @return int
     * @throws LocalizedException
     */
    public function getProductId()
    {
        try {
            return $this->reviewHelper->getReviewProductId($this->getReview());
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new LocalizedException(__($exception->getMessage()), $exception);
        }
    }

    /**
     * @return string
     */
    public function getVotingMessage()
    {
        return (string) $this->escapeHtml($this->_scopeConfig->getValue(self::XML_PATH_VOTING_MESSAGE, ScopeInterface::SCOPE_STORES));
    }

    /**
     * @return string
     */
    public function getHelpfulLabel()
    {
        return (string) $this->escapeHtml($this->_scopeConfig->getValue(self::XML_PATH_VOTING_HELPFUL_LABEL, ScopeInterface::SCOPE_STORES));
    }

    /**
     * @return string
     */
    public function getUnhelpfulLabel()
    {
        return (string) $this->escapeHtml($this->_scopeConfig->getValue(self::XML_PATH_VOTING_UNHELPFUL_LABEL, ScopeInterface::SCOPE_STORES));
    }

    /**
     * @return string
     */
    public function getAlertTitle()
    {
        return (string) $this->escapeHtml($this->_scopeConfig->getValue(self::XML_PATH_VOTING_ALERT_TITLE, ScopeInterface::SCOPE_STORES));
    }

    /**
     * @return int
     */
    public function getAlertTimeout()
    {
        return (int) $this->_scopeConfig->getValue(self::XML_PATH_VOTING_ALERT_TIMEOUT, ScopeInterface::SCOPE_WEBSITES);
    }
}

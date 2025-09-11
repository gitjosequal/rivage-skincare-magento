<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Controller\Vote;

use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use MageWorkshop\Voting\Model\Vote;
use MageWorkshop\Voting\Model\Data\ReviewVotesStatistics;
use MageWorkshop\Voting\Model\Data\VoteSearchResults;
use Magento\Framework\Controller\Result\Json;
use MageWorkshop\Voting\Controller\Exception\AccessDeniedException;
use MageWorkshop\Voting\Model\Exception\NotFoundException;

class Ajax extends \Magento\Framework\App\Action\Action
{
    /** constants for exception messages */
    const EXCEPTION_MUST_LOG_IN             = 'You must log in to vote';
    const EXCEPTION_VOTING_DISABLED         = 'Voting is disabled';
    const EXCEPTION_SOMETHING_WENT_WRONG    = 'Something went wrong.';
    const EXCEPTION_NO_VOTE_STATISTICS      = 'No vote statistics results found for product id %1$d and review id %2$d.';

    /** success messages */
    const MESSAGE_VOTE_ADDED    = 'Your vote has been added';
    const MESSAGE_VOTE_REMOVED  = 'Your vote has been removed';
    const MESSAGE_VOTE_CHANGED  = 'You changed your vote for this review';
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \MageWorkshop\Voting\Model\VoteRepository
     */
    protected $voteRepository;

    /**
     * @var \MageWorkshop\Voting\Model\VoteStatisticsRepository
     */
    protected $voteStatisticsRepository;

    /**
     * @var \MageWorkshop\Voting\Helper\Criteria
     */
    protected $criteriaHelper;

    /**
     * @var \MageWorkshop\Voting\Helper\Data
     */
    protected $votingHelper;

    /**
     * @var \MageWorkshop\Voting\Model\VoteFactory
     */
    protected $voteFactory;

    /**
     * @var \MageWorkshop\DetailedReview\Logger\Logger
     */
    protected $logger;

    /**
     * Ajax constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \MageWorkshop\Voting\Model\VoteRepository $voteRepository
     * @param \MageWorkshop\Voting\Model\VoteStatisticsRepository $voteStatisticsRepository
     * @param \MageWorkshop\Voting\Helper\Criteria $criteriaHelper
     * @param \MageWorkshop\Voting\Helper\Data $votingHelper
     * @param \MageWorkshop\Voting\Model\VoteFactory $voteFactory
     * @param \MageWorkshop\DetailedReview\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \MageWorkshop\Voting\Model\VoteRepository $voteRepository,
        \MageWorkshop\Voting\Model\VoteStatisticsRepository $voteStatisticsRepository,
        \MageWorkshop\Voting\Helper\Criteria $criteriaHelper,
        \MageWorkshop\Voting\Helper\Data $votingHelper,
        \MageWorkshop\Voting\Model\VoteFactory $voteFactory,
        \MageWorkshop\DetailedReview\Logger\Logger $logger
    ) {
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->voteRepository = $voteRepository;
        $this->voteStatisticsRepository = $voteStatisticsRepository;
        $this->criteriaHelper = $criteriaHelper;
        $this->votingHelper = $votingHelper;
        $this->voteFactory = $voteFactory;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return Json
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        try {
            if (!$this->votingHelper->isVotingEnabled()) {
                throw new AccessDeniedException(self::EXCEPTION_VOTING_DISABLED);
            }

            $customer = $this->customerSession->getCustomer();
            $guestToken = '';

            $criteriaData = [];

            if ($customer->getId()) {
                $criteriaData[Vote::KEY_CUSTOMER_ID] = $customer->getId();
            } elseif($this->votingHelper->isGuestsVotingAllowed()) {
                $guestToken = $this->customerSession->getSessionId();
                $criteriaData[Vote::KEY_GUEST_TOKEN] = $guestToken;
            } else {
                throw new AccessDeniedException(self::EXCEPTION_MUST_LOG_IN);
            }

            $reviewId = (int) $this->getRequest()->getParam('review_id');
            $productId = (int) $this->getRequest()->getParam('product_id');
            $voteValue = (int) $this->getRequest()->getParam('vote_value');

            if (!in_array($voteValue, [-1, 0, 1], true)) {
                $this->logger->error('Wrong vote value. Should be 1, 0 or -1', [
                    'criteria' => $criteriaData,
                    'review_id' => $reviewId,
                    'product_id' => $productId,
                    'vote_value' => $voteValue,
                ]);
                throw new LocalizedException(new Phrase(self::EXCEPTION_SOMETHING_WENT_WRONG));
            }

            $criteriaData[Vote::KEY_REVIEW_ID] = $reviewId;

            /** @var SearchCriteria $voteCriteria */
            $voteCriteria = $this->criteriaHelper->createCriteriaByParamsArray($criteriaData);

            try {
                /** @var VoteSearchResults $voteResults */
                $voteResults = $this->voteRepository->getList($voteCriteria);
                $voteObject = $voteResults->getFirstItem();
            } catch (NotFoundException $exception) {
                // if no vote object found - create one
                /** @var Vote $voteObject */
                $voteObject = $this->voteFactory->create();
                $voteObject->setReviewId($reviewId);

                if ($customer->getId()) {
                    $voteObject->setCustomerId($customer->getId());
                }

                if ($guestToken) {
                    $voteObject->setGuestToken($guestToken);
                }
                $voteObject->setProductId($productId);
            }

            if (abs($voteValue) === abs((int) $voteObject->getVote())) {
                $message = __(self::MESSAGE_VOTE_CHANGED);
            } elseif (abs($voteValue) > 0) {
                $message = __(self::MESSAGE_VOTE_ADDED);
            } else {
                $message = __(self::MESSAGE_VOTE_REMOVED);
            }

            $voteObject->setVote($voteValue);
            $this->voteRepository->save($voteObject);
            $reviewStatisticsObject = $this->getReviewStatistics($productId, $reviewId);

            $resultJson->setData([
                'message' => $message,
                'currentCustomerVote' => $voteValue,
                'helpfulCount' => $reviewStatisticsObject->getPositivesCount(),
                'unhelpfulCount' => $reviewStatisticsObject->getNegativesCount(),
            ]);
        } catch (AccessDeniedException $exception) {
            $resultJson->setData([
                'error' => $exception->getMessage(),
                'errorCode' => 403,
            ]);
            $resultJson->setHttpResponseCode(403);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            $resultJson->setData([
                'error' => __(self::EXCEPTION_SOMETHING_WENT_WRONG),
                'errorCode' => 500,
            ]);
            $resultJson->setHttpResponseCode(500);
        }

        return $resultJson;
    }

    /**
     * @param int $productId
     * @param int $reviewId
     * @return ReviewVotesStatistics
     * @throws NotFoundException
     */
    protected function getReviewStatistics($productId, $reviewId)
    {
        $criteriaData = [
            Vote::KEY_PRODUCT_ID => $productId,
            Vote::KEY_REVIEW_ID => $reviewId,
        ];

        /** @var SearchCriteria $voteCriteria */
        $voteCriteria = $this->criteriaHelper->createCriteriaByParamsArray($criteriaData);

        $voteStatisticsResult = $this->voteStatisticsRepository->getList($voteCriteria);

        if ($reviewItemsArray = $voteStatisticsResult->getItems()) {
            return array_values($reviewItemsArray)[0];
        } else {
            throw new NotFoundException(sprintf(self::EXCEPTION_NO_VOTE_STATISTICS, (int) $productId, (int) $reviewId));
        }
    }
}
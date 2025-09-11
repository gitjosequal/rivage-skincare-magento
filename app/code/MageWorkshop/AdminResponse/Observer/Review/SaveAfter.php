<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\AdminResponse\Observer\Review;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Review\Model\Review;
use MageWorkshop\AdminResponse\Model\AdminResponse;

class SaveAfter implements ObserverInterface
{
    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     */
    private $reviewHelper;

    /**
     * @var \MageWorkshop\AdminResponse\Model\AdminResponse $adminResponse
     */
    private $adminResponse;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $config;

    /**
     * @var bool $canSaveResponse
     */
    private static $canSaveResponse = true;

    /**
     * @var array $responses
     */
    private static $responses = [];

    /**
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \MageWorkshop\AdminResponse\Model\AdminResponse $adminResponse
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \MageWorkshop\AdminResponse\Model\AdminResponse $adminResponse,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->reviewHelper = $reviewHelper;
        $this->adminResponse = $adminResponse;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if ($observer->getEvent()->getName() === 'mageworkshop_import_start') {
            self::$canSaveResponse = false;
            return;
        }

        if ($observer->getEvent()->getName() === 'mageworkshop_import_completed') {
            self::$canSaveResponse = true;
            $this->flush();
            return;
        }

        /** @var Review $review */
        $review = $observer->getEvent()->getData('object');

        if (!$this->reviewHelper->isProductReview($review)) {
            return;
        }

        $adminResponse = $this->adminResponse->getAdminResponseByReview($review);
        $adminResponseDetail = (string) $review->getData(AdminResponse::FIELD_NAME);

        if (!$adminResponseDetail && $adminResponse->getId()) {
            $adminResponse->delete();
            return;
        }

        if ($adminResponseDetail !== (string) $review->getOrigData(AdminResponse::FIELD_NAME)) {
            $adminResponseTitle = $this->config->getValue(AdminResponse::XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_ADMIN_RESPONSE_TITLE);
            $adminResponse->addData([
                'entity_id'       => $this->reviewHelper->getReviewEntityIdByCode(AdminResponse::REVIEW_ENTITY_CODE),
                'entity_pk_value' => $review->getId(),
                'status_id'       => Review::STATUS_APPROVED,
                'title'           => $adminResponseTitle,
                'detail'          => $adminResponseDetail,
                'nickname'        => $adminResponseTitle
            ]);

            if (self::$canSaveResponse) {
                $adminResponse->save();
            } else {
                $this->persist($adminResponse);
            }
        }
    }

    /**
     * Collect responses to save after import is completed so that we do not overwrite existing IDs
     * @param Review $review
     */
    private function persist(Review $review)
    {
        self::$responses[$review->getEntityPkValue()] = $review;
    }

    /**
     * Save collected data
     */
    private function flush()
    {
        array_walk(self::$responses, function(Review $response) {
            $response->save();
        });

        self::$responses = [];
    }
}

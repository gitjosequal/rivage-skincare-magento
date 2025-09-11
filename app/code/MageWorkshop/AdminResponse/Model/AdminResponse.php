<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\AdminResponse\Model;

use Magento\Review\Model\Review;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

class AdminResponse
{
    const REVIEW_ENTITY_CODE = 'response';

    const TABLE_ALIAS = 'admin_response';

    const FIELD_NAME = 'admin_response';

    const XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_ADMIN_RESPONSE_TITLE
        = 'mageworkshop_detailedreview/admin_response/title';

    const XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_ADMIN_RESPONSE_SHOW_TOOLTIP
        = 'mageworkshop_detailedreview/admin_response/show_tooltip';

    const XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_ADMIN_RESPONSE_TOOLTIP_TEXT
        = 'mageworkshop_detailedreview/admin_response/tooltip_text';

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review  $reviewHelper
     */
    private $reviewHelper;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewsCollectionFactory
     */
    private $reviewsCollectionFactory;

    /**
     * LoadAfter constructor.
     *
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewsCollectionFactory
     */
    public function __construct(
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewsCollectionFactory
    ) {
        $this->reviewHelper = $reviewHelper;
        $this->reviewsCollectionFactory = $reviewsCollectionFactory;
    }

    /**
     * @param Review $review
     * @return Review
     */
    public function getAdminResponseByReview(Review $review)
    {
        /** @var ReviewCollection $reviewCollection */
        $reviewCollection = $this->reviewsCollectionFactory->create();

        if (!$this->reviewHelper->isProductReview($review)) {
            return $reviewCollection->getNewEmptyItem();
        }

        $reviewCollection->addEntityFilter(self::REVIEW_ENTITY_CODE, $review->getId());

        return $reviewCollection
            ->setPageSize(1)
            ->getFirstItem();
    }
}

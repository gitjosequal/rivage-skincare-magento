<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Review\Product\View\Rating;

use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

class AbstractRating extends \Magento\Review\Block\Product\View
{
    /** Maximum number of stars for rating */
    const QUANTITY_STARTS = 5;

    /** Max available rating */
    const BEST_RATING = 100;

    /**
     * @var ReviewCollection $_cachedReviewsCollection - static collection of all product reviews used by other blocks
     */
    protected static $_cachedReviewsCollection;

    /**
     * Note: method is used in the class \MageWorkshop\CustomerPermissions\Block\Check\Verify
     * So if we need to filter this collection than probably need to clone it there or cleanup
     * if we decide to reload averages when filters are applied
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     */
    public function getReviewsCollection()
    {
        if (null === self::$_cachedReviewsCollection) {
            $reviewsCollection = parent::getReviewsCollection();
            $reviewsCollection->load()->addRateVotes();
            self::$_cachedReviewsCollection = $reviewsCollection;
        }

        return self::$_cachedReviewsCollection;
    }

    /**
     * Don't render this block if no reviews id present
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        $product = $this->getProduct();

        if ($product && $this->getReviewsCollection()->getSize()) {
            $html = parent::_toHtml();
        }

        return $html;
    }
}

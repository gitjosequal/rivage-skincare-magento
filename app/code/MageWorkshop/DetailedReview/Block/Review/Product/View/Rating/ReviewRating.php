<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Review\Product\View\Rating;

use Magento\Framework\DataObject;
use Magento\Review\Model\Review;
use Magento\Catalog\Block\Product\AbstractProduct;

class ReviewRating extends AbstractRating
{
    /**
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAverageRating()
    {
        return (float) $this->getRatingSummary() * self::QUANTITY_STARTS / self::BEST_RATING;
    }

    /**
     * Must call grandpa because there is no block 'product_review_list.count' in layout
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        return AbstractProduct::getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
    }

    /**
     * @return DataObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getRatingSummary()
    {
        $product = $this->getProduct();

        if (!$product->getRatingSummary()) {
            /** @var Review $review */
            $review = $this->getReviewsCollection()->getNewEmptyItem();
            $review->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        }
        
        if(is_string($product->getRatingSummary())){
            return $product->getRatingSummary();
            
        }
        
        return $product->getRatingSummary()->getRatingSummary();
    }
}

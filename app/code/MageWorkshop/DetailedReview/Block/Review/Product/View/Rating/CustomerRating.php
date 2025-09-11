<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Review\Product\View\Rating;

use Magento\Review\Model\Rating\Option\Vote;
use Magento\Review\Model\Review;

class CustomerRating extends AbstractRating
{
    /**
     * @return array
     */
    public function getCustomerRating()
    {
        $votesByValue = array_fill(1, self::QUANTITY_STARTS, 0);

        /** @var Review $review */
        foreach ($this->getReviewsCollection() as $review) {
            /** @var Vote $vote */
            foreach ($review->getRatingVotes() as $vote) {
                ++$votesByValue[(int) $vote->getValue()];
            }
        }

        $customerRating = [];
        $votesCount = array_sum($votesByValue);

        for ($i = self::QUANTITY_STARTS; $i >= 1; $i--) {
            $customerRating[$i] = [
                'quantity' => $votesByValue[$i],
                'percent'  => $votesCount ? $votesByValue[$i] / $votesCount * self::BEST_RATING : 0
            ];
        }

        return $customerRating;
    }
}

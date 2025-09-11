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

class AverageRating extends AbstractRating
{
    /**
     * @return array
     */
    public function getAverageRating()
    {
        $averageRating = [];

        /** @var Review $review */
        foreach ($this->getReviewsCollection() as $review) {
            /** @var Vote $vote */
            foreach ($review->getRatingVotes() as $vote) {
                $ratingCode = $vote->getRatingCode();

                if (!isset($averageRating[$ratingCode])) {
                    $averageRating[$ratingCode] = [
                        'values' => [],
                        'values_count' => 0
                    ];
                }

                $averageRating[$ratingCode]['values'][] = (int) $vote->getValue();
                ++$averageRating[$ratingCode]['values_count'];
            }
        }

        foreach ($averageRating as $ratingCode => $data) {
            $averageRating[$ratingCode]['value'] = number_format(round(array_sum($data['values']) / $data['values_count'], 2), 2);
            $averageRating[$ratingCode]['percent'] = $averageRating[$ratingCode]['value'] * self::BEST_RATING / self::QUANTITY_STARTS;
        }

        return $averageRating;
    }
}

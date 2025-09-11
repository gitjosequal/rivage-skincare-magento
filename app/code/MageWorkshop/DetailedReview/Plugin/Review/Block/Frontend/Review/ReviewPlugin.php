<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Plugin\Review\Block\Frontend\Review;

use Magento\Review\Model\Review;

class ReviewPlugin extends \MageWorkshop\DetailedReview\Plugin\Review\AbstractReview
{
    /**
     * @param Review $review
     * @param array $errors
     * @return array
     */
    public function afterValidate(Review $review, $errors)
    {
        if ($this->dataHelper->isModuleEnabled(\MageWorkshop\DetailedReview\Model\Module\DetailsData::MODULE_CODE)) {
            return $this->checkValidation($review, $errors);
        }

        return $errors;
    }
}

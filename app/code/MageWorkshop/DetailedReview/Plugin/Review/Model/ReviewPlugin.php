<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Plugin\Review\Model;

use Magento\Review\Model\Review;

class ReviewPlugin extends \MageWorkshop\DetailedReview\Plugin\Review\AbstractReview
{
    /**
     * @param Review $review
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave(Review $review)
    {
        $validate = $this->checkValidation($review);
        if (is_array($validate) && !empty($validate)) {
            foreach ($validate as $errorMessage) {
                $this->messageManager->addErrorMessage($errorMessage);
            }
        }
    }
}

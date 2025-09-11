<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\AdminResponse\Plugin\Review\Model;

use Magento\Review\Model\Review;

class ReviewPlugin
{
    /**
     * @var \MageWorkshop\AdminResponse\Model\AdminResponse $adminResponse
     */
    private $adminResponse;

    /**
     * @param \MageWorkshop\AdminResponse\Model\AdminResponse $adminResponse
     */
    public function __construct(
        \MageWorkshop\AdminResponse\Model\AdminResponse $adminResponse
    ) {
        $this->adminResponse = $adminResponse;
    }

    /**
     * @param Review $subject
     * @param Review $result
     * @return Review
     * @throws \Exception
     */
    public function afterAfterDeleteCommit(Review $subject, Review $result)
    {
        $adminResponse = $this->adminResponse->getAdminResponseByReview($subject);

        if ($subject->isDeleted() && $adminResponse->getId()) {
            $adminResponse->delete();
        }

        return $result;
    }
}

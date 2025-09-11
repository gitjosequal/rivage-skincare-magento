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

class LoadAfter implements ObserverInterface
{
    /**
     * @var \MageWorkshop\AdminResponse\Model\AdminResponse $adminResponse
     */
    private $adminResponse;

    /**
     * LoadAfter constructor.
     *
     * @param \MageWorkshop\AdminResponse\Model\AdminResponse $adminResponse
     */
    public function __construct(
        \MageWorkshop\AdminResponse\Model\AdminResponse $adminResponse
    ) {
        $this->adminResponse = $adminResponse;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Review $review */
        $review = $observer->getEvent()->getData('object');
        $adminResponse = $this->adminResponse->getAdminResponseByReview($review);

        if ($adminResponse->getId()) {
            $review->setData(AdminResponse::FIELD_NAME, $adminResponse->getDetail());
        }
    }
}

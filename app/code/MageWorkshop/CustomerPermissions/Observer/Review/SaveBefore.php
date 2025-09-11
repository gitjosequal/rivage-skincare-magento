<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Observer\Review;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\Review;

class SaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorkshop\CustomerPermissions\Helper\VerifiedHelper $verifiedHelper
     */
    private $verifiedHelper;

    /**
     * SaveBefore constructor.
     * @param \MageWorkshop\CustomerPermissions\Helper\VerifiedHelper $verifiedHelper
     */
    public function __construct(\MageWorkshop\CustomerPermissions\Helper\VerifiedHelper $verifiedHelper)
    {
        $this->verifiedHelper  = $verifiedHelper;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        if ($this->verifiedHelper->isAutoApproveEnabled()) {
            $review = $observer->getEvent()->getData('object');

            if (!$this->verifiedHelper->allowToPostReviewForCurrentUser($review->getEntityPkValue())) {
                throw new LocalizedException(__('Is not allowed for current customer'));
            }

            if ($this->verifiedHelper->isAutoApproveAvailable()) {
                $review->setStatusId(Review::STATUS_APPROVED);
            }
        }
    }
}

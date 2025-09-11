<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\ImageLoader\Observer;

use Magento\Framework\Event\ObserverInterface;
use MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\Collection;

class PrepareReviewFieldsConfiguration implements ObserverInterface
{
    /**
     * @var \MageWorkshop\ImageLoader\Helper\Data $imageLoaderHelper
     */
    private $imageLoaderHelper;

    /**
     * ReviewImagePlugin constructor.
     * @param \MageWorkshop\ImageLoader\Helper\Data $imageLoaderHelper
     */
    public function __construct(
        \MageWorkshop\ImageLoader\Helper\Data $imageLoaderHelper
    ) {
        $this->imageLoaderHelper = $imageLoaderHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->imageLoaderHelper->isImageLoaderEnabled()) {
            /** @var Collection $reviewFormAttributes */
            $reviewFormAttributes = $observer->getEvent()->getData('review_form_attributes');
            $reviewFormAttributes->addFieldToFilter('frontend_input', ['neq' => 'image']);
        }
    }
}

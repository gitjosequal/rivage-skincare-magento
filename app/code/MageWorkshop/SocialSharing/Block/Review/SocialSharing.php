<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\SocialSharing\Block\Review;

use Magento\Catalog\Model\Product;
use Magento\Review\Model\Review;
use MageWorkshop\SocialSharing\Helper\Data as SocialSharingHelper;

class SocialSharing extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Review
     */
    private $review;

    /**
     * @var \MageWorkshop\SocialSharing\Helper\Data
     */
    private $socialSharingHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * SocialSharing constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorkshop\SocialSharing\Helper\Data $socialSharingHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageWorkshop\SocialSharing\Helper\Data $socialSharingHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->socialSharingHelper = $socialSharingHelper;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @param Review $review
     * @return $this
     */
    public function setReview(Review $review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * @return Review
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReview()
    {
        if (!$this->review) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Failed to initialize review'));
        }

        return $this->review;
    }

    /**
     * Return the review id
     * @return int
     */
    public function getReviewId()
    {
        return $this->getReview()->getId();
    }

    /**
     * @return SocialSharingHelper
     */
    public function getSocialSharingHelper()
    {
        return $this->socialSharingHelper;
    }

    /**
     * @return \Magento\Catalog\Model\Product|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProduct()
    {

        if (!$this->product) {
            $this->product = $this->registry->registry('product');

            if (!$this->product->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Failed to initialize product'));
            }
        }

        return $this->product;
    }
}

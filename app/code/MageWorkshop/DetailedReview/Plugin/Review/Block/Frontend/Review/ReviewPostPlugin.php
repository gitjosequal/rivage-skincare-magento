<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Plugin\Review\Block\Frontend\Review;

use Magento\Framework\Controller\Result\Redirect;
use Magento\Review\Controller\Product\Post;

class ReviewPostPlugin
{
    const PRODUCT_REVIEW_TAB_ID = '#reviews';

    /** @var \Magento\Framework\App\Response\RedirectInterface $redirect */
    private $redirect;

    /**
     * ReviewPostPlugin constructor.
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     */
    public function __construct(\Magento\Framework\App\Response\RedirectInterface $redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * @param Post $review
     * @param Redirect $resultRedirect
     * @return Redirect $resultRedirect
     */
    public function afterExecute(Post $review, $resultRedirect)
    {
        return $resultRedirect->setUrl($this->redirect->getRedirectUrl() . self::PRODUCT_REVIEW_TAB_ID);
    }
}
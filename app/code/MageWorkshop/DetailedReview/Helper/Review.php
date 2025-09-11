<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Helper;

use Magento\Catalog\Model\Product;
use Magento\Review\Model\Review as ReviewModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class Review extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Review\Model\ResourceModel\ReviewFactory $reviewResource */
    protected $reviewResourceFactory;

    /** @var \Magento\Framework\Registry */
    protected $registry;

    /** @var array $reviewEntityIdCodes */
    protected static $reviewEntityIdCodes = [];

    /** @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory */
    protected $reviewsCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Review constructor.
     * @param \Magento\Review\Model\ResourceModel\ReviewFactory $reviewResourceFactory
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewsCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Review\Model\ResourceModel\ReviewFactory $reviewResourceFactory,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewsCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->reviewResourceFactory = $reviewResourceFactory;
        $this->reviewsCollectionFactory = $reviewsCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * @param ReviewModel $review
     * @return bool
     */
    public function isProductReview(ReviewModel $review)
    {
        return ((int) $review->getEntityId() === (int) $this->getReviewEntityIdByCode());
    }

    /**
     * @param ReviewModel $review
     * @return Product
     * @throws \Exception
     */
    public function getProductByReview(ReviewModel $review)
    {
        /** @var Collection $productCollection */
        $productCollection = $this->productCollectionFactory->create();

        if ($productId = $this->getReviewProductId($review)) {
            $productCollection->addIdFilter($productId)
                ->setPageSize(1);
            return $productCollection->getFirstItem();
        }

        return $productCollection->getNewEmptyItem();
    }

    /**
     * Returns the product id from the review object
     * If the review is not for product - getting the product id from the registry
     * @param ReviewModel $review
     * @return int
     */
    public function getReviewProductId(ReviewModel $review)
    {
        $productId = 0;

        if ($review && $this->isProductReview($review)) {
            $productId = (int) $review->getEntityPkValue();
        } elseif ($product = $this->registry->registry('current_product')) {
            /** @var Product $product */
            $productId = (int) $product->getId();
        }

        return $productId;
    }

    /**
     * @param string $reviewEntityCode
     * @return array
     */
    public function getReviewEntityIdByCode($reviewEntityCode = ReviewModel::ENTITY_PRODUCT_CODE)
    {
        if (!isset(self::$reviewEntityIdCodes[$reviewEntityCode])) {
            /** @var \Magento\Review\Model\ResourceModel\Review $reviewResource */
            $reviewResource = $this->reviewResourceFactory->create();
            self::$reviewEntityIdCodes[$reviewEntityCode] =
                (int) $reviewResource->getEntityIdByCode($reviewEntityCode);
        }
        return self::$reviewEntityIdCodes[$reviewEntityCode];
    }

    /**
     * Get approved reviews count
     *
     * @param Product $product
     * @return int
     */
    public function getProductApprovedReviewsCount(Product $product)
    {
        return $this->reviewsCollectionFactory->create()->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $product->getId()
        )->getSize();
    }
}

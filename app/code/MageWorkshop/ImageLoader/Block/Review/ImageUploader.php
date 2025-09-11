<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\ImageLoader\Block\Review;

use Magento\Review\Model\Review;
use MageWorkshop\DetailedReview\Helper\AbstractAttribute;
use MageWorkshop\ImageLoader\Helper\Data as ImageLoaderHelper;

class ImageUploader extends \Magento\Framework\View\Element\Template
{
    const FRONTEND_INPUT_IMAGE = 'image';

    /**
     * @var \MageWorkshop\ImageLoader\Helper\Data
     */
    private $imageLoaderHelper;

    /**
     * @var \MageWorkshop\ImageLoader\Helper\Media
     */
    private $mediaHelper;

    /**
     * @var AbstractAttribute[] $attributes
     */
    private $attributes = [];

    /**
     * @var Review $review
     */
    private $review;

    /**
     * ImageUploader constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ImageLoaderHelper $imageLoaderHelper
     * @param \MageWorkshop\ImageLoader\Helper\Media $mediaHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageWorkshop\ImageLoader\Helper\Data $imageLoaderHelper,
        \MageWorkshop\ImageLoader\Helper\Media $mediaHelper,
        array $data = []
    ) {
        $this->imageLoaderHelper = $imageLoaderHelper;
        $this->mediaHelper = $mediaHelper;
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
     */
    public function getReview()
    {
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
     * @return ImageLoaderHelper
     */
    public function getImageLoaderHelper()
    {
        return $this->imageLoaderHelper;
    }

    /**
     * @param AbstractAttribute[] $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return AbstractAttribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function getImageAttributeValue()
    {
        $allImages = [];
        /** @var AbstractAttribute $attribute */
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getFrontendInput() === self::FRONTEND_INPUT_IMAGE) {
                if (empty($value = (string) $this->getReview()->getData($attribute->getAttributeCode()))) {
                    continue;
                }
                $images = array_filter(explode(',', $value));
                $imageSource = $arrayImages = [];

                foreach ($images as $fileName) {
                    $imageSource['id'] = $attribute->getId();
                    $imageSource['value'] = $fileName;
                    $imageSource['label'] = $fileName;
                    $imageSource['src'] = $this->mediaHelper->getTmpMediaFullPathToImages(trim($fileName));
                    $arrayImages[] = $imageSource;
                }
                $allImages[$attribute->getFrontendLabel()] = $arrayImages;
            }
        }

        return $allImages;
    }
}

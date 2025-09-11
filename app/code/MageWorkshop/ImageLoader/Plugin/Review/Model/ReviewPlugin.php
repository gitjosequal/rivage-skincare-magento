<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\ImageLoader\Plugin\Review\Model;

use Magento\Review\Model\Review;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class ReviewPlugin
{
    const EXCEPTION_IMAGE_UPLOADER_DISABLED = 'Image Uploader is disabled';

    /** @var \MageWorkshop\ImageLoader\Helper\Data $imageLoaderHelper */
    private $imageLoaderHelper;

    /** @var \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory */
    private $uploaderFactory;

    /** @var \Magento\Framework\Image\AdapterFactory $adapterFactory */
    private $adapterFactory;

    /** @var \Magento\Framework\Filesystem $filesystem */
    private $filesystem;

    /**
     * @var \MageWorkshop\ImageLoader\Helper\Media $mediaHelper
     */
    private $mediaHelper;

    /** @var array $allowedExtensions */
    private $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Attribute
     */
    private $attributeHelper;

    /**
     * ReviewImagePlugin constructor.
     * @param \MageWorkshop\ImageLoader\Helper\Data $imageLoaderHelper
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Image\AdapterFactory $adapterFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \MageWorkshop\ImageLoader\Helper\Media $mediaHelper
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     */
    public function __construct(
        \MageWorkshop\ImageLoader\Helper\Data $imageLoaderHelper,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Filesystem $filesystem,
        \MageWorkshop\ImageLoader\Helper\Media $mediaHelper,
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
    ) {
        $this->imageLoaderHelper = $imageLoaderHelper;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->mediaHelper = $mediaHelper;
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * @param Review $review
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave(Review $review)
    {
        if (!$this->imageLoaderHelper->isImageLoaderEnabled()
            || !$files = $this->getSuperglobalVariableAsFiles()
        ) {
            return;
        }

        try {
            $imageAttributes = [];

            foreach ($this->attributeHelper->getReviewFormAttributesConfigurationByReview($review) as $attributeConfiguration) {
                if ($attributeConfiguration['frontend_input'] === 'image') {
                    $imageAttributes[] = $attributeConfiguration['attribute_code'];
                }
            }

            foreach ($files as $attributeCode => $file) {
                if (!in_array($attributeCode, $imageAttributes, true)) {
                    return;
                }

                $savedImages = [];

                if (is_array($file['name'])) {
                    foreach ($file['name'] as $index => $fileName) {
                        if (!empty($fileName)) {
                            $savedImages[] = $this->upload($attributeCode, $fileName, $index);
                        }
                    }
                } else {
                    $savedImages[] = $this->upload($attributeCode, $file['name']);
                }

                $savedImages = $this->mediaHelper->setImageDispersionPath($savedImages);
                $reviewImages = !is_array($review->getData($attributeCode))
                    ? explode(',', $review->getData($attributeCode))
                    : $review->getData($attributeCode);

                $reviewImages = array_filter($reviewImages);
                $reviewImages = array_unique(array_merge($reviewImages, $savedImages));
                $review->setData($attributeCode, implode(',', $reviewImages));
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    private function upload($attributeCode, $fileName, $index = 0)
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $attributeCode . "[$index]"]);
        $uploader->setAllowedExtensions($this->getAllowedImagesExtensions());
        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
        $imageAdapter = $this->adapterFactory->create();
        $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(true);
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $uploader->save($mediaDirectory->getAbsolutePath($this->mediaHelper->getImageMediaPath()));

        return $fileName;
    }

    /**
     * @return array|string
     */
    private function getAllowedImagesExtensions()
    {
        return !empty($this->imageLoaderHelper->getImageTypes())
            ? $this->imageLoaderHelper->getImageTypes()
            : $this->allowedExtensions;
    }

    /**
     * @return array
     */
    public function getSuperglobalVariableAsFiles()
    {
        $_files = &${'_FILES'};

        return is_array($_files) && count($_files) ? $_files : [];
    }
}

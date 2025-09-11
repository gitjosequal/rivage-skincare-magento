<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\ImageLoader\Helper;

use Magento\Framework\File\Uploader;

class Media
{
    const IMAGE_MEDIA_PATH = 'mageworkshop/imageloader';

    /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
    private $storeManager;

    /**
     * Media constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getImageMediaPath()
    {
        return self::IMAGE_MEDIA_PATH;
    }

    /**
     * @param array $images
     * @return array
     */
    public function setImageDispersionPath(array $images)
    {
        $imagePaths = [];

        foreach ($images as $image) {
            $imagePaths[] = Uploader::getDispretionPath($image) . '/' . Uploader::getCorrectFileName($image);
        }
        return $imagePaths;
    }

    /**
     * @param $fileName
     * @return string
     */
    public function getTmpMediaFullPathToImages($fileName)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return (string) $mediaUrl . $this->getImageMediaPath() . strtolower($fileName);
    }
}

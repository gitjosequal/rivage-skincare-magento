<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\ImageLoader\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * ImageLoader helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config path for "Enable image loader" adminhtml option
     */
    const XML_PATH_IMAGE_LOADER_ENABLED = 'mageworkshop_detailedreview/imageloader/enabled';
    const XML_PATH_NUMBER_OF_IMAGES_TO_SHOW = 'mageworkshop_detailedreview/imageloader/number_of_images_to_show';
    const XML_PATH_IMAGE_SIZE = 'mageworkshop_detailedreview/imageloader/image_size';
    const XML_PATH_IMAGE_TYPES = 'mageworkshop_detailedreview/imageloader/image_types';
    const XML_PATH_IMAGE_WIDTH = 'mageworkshop_detailedreview/imageloader/image_width';
    const XML_PATH_IMAGE_HEIGHT = 'mageworkshop_detailedreview/imageloader/image_height';

    /**
     * Returns true if the review image loader is enabled in admin
     *
     * @return boolean
     */
    public function isImageLoaderEnabled()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_IMAGE_LOADER_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getNumberOfImagesToShow()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_NUMBER_OF_IMAGES_TO_SHOW, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getImageSize()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_IMAGE_SIZE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return array
     */
    public function getImageTypes()
    {
        return array_filter(explode(',', $this->scopeConfig->getValue(self::XML_PATH_IMAGE_TYPES, ScopeInterface::SCOPE_STORE)));
    }

    /**
     * @return int
     */
    public function getImageWidth()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_IMAGE_WIDTH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getImageHeight()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_IMAGE_HEIGHT, ScopeInterface::SCOPE_STORE);
    }
}
<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\ImageLoader\Model\Config\Source;

/**
 * Class ImageTypes
 * @package MageWorkshop\ImageLoader\Model\Config\Source
 */
class ImageTypes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'jpg', 'label' => __('jpg')],
            ['value' => 'jpeg', 'label' => __('jpeg')],
            ['value' => 'gif', 'label' => __('gif')],
            ['value' => 'png', 'label' => __('png')]
        ];
    }
}
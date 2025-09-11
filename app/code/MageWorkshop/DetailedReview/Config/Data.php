<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Config;

class Data extends \Magento\Framework\Config\Data
{
    const CACHE_ID = 'mageworkshop_eav_attributes_config';

    /**
     * @var \Magento\Framework\Mview\View\State\CollectionInterface
     */
    protected $stateCollection;

    /**
     * @param \MageWorkshop\DetailedReview\Config\Reader $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     */
    public function __construct(
        \MageWorkshop\DetailedReview\Config\Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, self::CACHE_ID);
    }
}

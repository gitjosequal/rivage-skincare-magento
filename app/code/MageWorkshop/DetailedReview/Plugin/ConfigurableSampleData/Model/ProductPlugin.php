<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Plugin\ConfigurableSampleData\Model;

use MageWorkshop\DetailedReview\Model\Indexer\Flat as FlatIndexer;

class ProductPlugin
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * ProductPlugin constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    public function beforeInstall()
    {
        $this->registry->register(FlatIndexer::CHECK_STATE_IN_DATABASE, true);
    }

    public function afterInstall()
    {
        $this->registry->unregister(FlatIndexer::CHECK_STATE_IN_DATABASE);
    }
}

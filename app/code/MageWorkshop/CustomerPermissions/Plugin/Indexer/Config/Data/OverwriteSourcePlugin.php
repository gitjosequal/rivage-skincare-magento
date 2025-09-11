<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Plugin\Indexer\Config\Data;

use MageWorkshop\CustomerPermissions\Model\Rewrite\Customer\Model\Indexer\Source;

class OverwriteSourcePlugin
{
    protected $arrayManager;

    public function __construct(\Magento\Framework\Stdlib\ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    public function afterGet($subject, $result, $argument1 = null)
    {
        $indexerConfigurationArray = $result;

        if (
            !class_exists('\Magento\Customer\Model\Indexer\Source')
            || !is_array($indexerConfigurationArray)
        ) {
            return $result;
        }

        if (
            $argument1 === 'customer_grid'
            && $this->arrayManager->exists('fieldsets/0/source', $indexerConfigurationArray)
        ) {
            $indexerConfigurationArray['fieldsets'][0]['source'] = Source::class;
        } else if ($this->arrayManager->exists('customer_grid/fieldsets/0/source', $indexerConfigurationArray)) {
            $indexerConfigurationArray['customer_grid']['fieldsets'][0]['source'] = Source::class;
        }

        return $indexerConfigurationArray;
    }
}
<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

/**
 * DESCRIPTION!
 * Class `Magento\Customer\Model\Indexer\Source` does not exist in Magento 2.1.x
 * But compiler scans for all classes and fails to compile the code
 * This is why we do this :(
 */
namespace MageWorkshop\CustomerPermissions\Model\Rewrite\Customer\Model\Indexer;

if (!class_exists('\Magento\Customer\Model\Indexer\Source')) {
    class Source {}
} else {
    require_once 'Source.phtml';
}

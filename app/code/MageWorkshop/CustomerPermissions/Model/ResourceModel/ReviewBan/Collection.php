<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Model\ResourceModel\ReviewBan;

use MageWorkshop\CustomerPermissions\Model\ReviewBan as ReviewBanModel;
use MageWorkshop\CustomerPermissions\Model\ResourceModel\ReviewBan as ReviewBanResource;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ReviewBanModel::class, ReviewBanResource::class);
    }
}

<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Model\Module;

class DetailsData extends \MageWorkshop\Core\Model\Module\AbstractDetailsData
{
    const MODULE_CODE = 'MageWorkshop_CustomerPermissions';

    /** @var string $publicName */
    protected $publicName = 'DR_Customer Permissions';

    /** @var bool $isPaid */
    protected $isPaid = true;
}
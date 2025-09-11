<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Core\Model\Module;

abstract class AbstractDetailsData implements DetailsDataInterface
{
    const MODULE_CODE = '';

    /** @var string $publicName */
    protected $publicName = '';

    /**
     * {@inheritdoc}
     */
    public function getModuleName()
    {
        return $this->publicName;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleCode()
    {
        // We can not use "self::" here, because it refers to the constant value in the abstract class
        /** @var AbstractDetailsData $className */
        $className = get_class($this);
        return constant($className . '::MODULE_CODE');
    }
}

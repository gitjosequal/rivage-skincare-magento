<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Api\Data\Entity;

/**
 * @api
 */
interface EntityTypeConfigInterface
{
    /**
     * @return array
     */
    public function getConfig();

    /**
     * @return string
     */
    public function getEntityIdField();

    /**
     * @return string
     */
    public function getAttributeModel();

    /**
     * @return string
     */
    public function getEntityModel();

    /**
     * @return string
     */
    public function getIncrementModel();

    /**
     * @return string
     */
    public function getAdditionalAttributeTable();

    /**
     * @return string
     */
    public function getEntityAttributeCollection();

    /**
     * @return AttributeConfigCollectionInterface
     * @throws \Exception
     */
    public function getAttributesConfigCollection();

    /**
     * @return string
     */
    public function getAttributeFactoryClass();
}

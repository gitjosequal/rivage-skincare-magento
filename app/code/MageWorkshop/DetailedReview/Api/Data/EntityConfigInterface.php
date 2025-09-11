<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Api\Data;

use MageWorkshop\DetailedReview\Api\Data\Entity\EntityTypeConfigInterface;
use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigCollectionInterface;

/**
 * @api
 */
interface EntityConfigInterface
{
    const ENTITY_CONFIG_NOT_FOUND_EXCEPTION = 'Entity config was not found for %1 entity type';

    /**
     * @param string $entityType
     * @return EntityTypeConfigInterface
     */
    public function getEntityTypeConfig($entityType);

    /**
     * @param string $entityType
     * @return AttributeConfigCollectionInterface
     */
    public function getEntityAttributesConfig($entityType);
}

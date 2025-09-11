<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\Config;

use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigInterface;
use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigCollectionInterface;

abstract class AbstractAttributeCollection implements AttributeConfigCollectionInterface
{
    const DUPLICATED_ATTRIBUTE_DEFINITION_EXCEPTION = 'Attribute configuration with the same type already exists';

    const MISSED_ATTRIBUTE_CONFIGURATION_EXCEPTION = 'No attribute configuration found for input type %s';

    /** @var array $items */
    protected $items = [];

    /**
     * {@inheritdoc}
     */
    public function addItem(AttributeConfigInterface $attributeConfig)
    {
        if (isset($items[$attributeConfig->getFrontendInput()])) {
            throw new \LogicException(self::DUPLICATED_ATTRIBUTE_DEFINITION_EXCEPTION);
        }

        $this->items[$attributeConfig->getFrontendInput()] = $attributeConfig;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($attributeType)
    {
        if (!isset($this->items[$attributeType])) {
            throw new \DomainException(sprintf(self::MISSED_ATTRIBUTE_CONFIGURATION_EXCEPTION, $attributeType));
        }
        return $this->items[$attributeType];
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}

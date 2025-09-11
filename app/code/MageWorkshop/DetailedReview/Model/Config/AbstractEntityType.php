<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\Config;

use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigCollectionInterface;
use MageWorkshop\DetailedReview\Api\Data\Entity\EntityTypeConfigInterface;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\VersionControl\AbstractEntity;
use Magento\Eav\Model\Entity\Increment\NumericValue;
use Magento\Eav\Model\Entity\Collection\VersionControl\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;

class AbstractEntityType extends AbstractConfig implements EntityTypeConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntityIdField()
    {
        return $this->getDataOrDefault('entity_id_field', 'entity_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeModel()
    {
        return $this->getDataOrDefault('attribute_model', Attribute::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityModel()
    {
        return $this->getDataOrDefault('entity_model', AbstractEntity::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIncrementModel()
    {
        return $this->getDataOrDefault('increment_model', NumericValue::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalAttributeTable()
    {
        return $this->getData('entity_type') . '_attribute';
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityAttributeCollection()
    {
        return $this->getDataOrDefault(
            'entity_attribute_collection',
            AbstractCollection::class
        );
    }

    /**
     * @return AttributeConfigCollectionInterface
     * @throws \Exception
     */
    public function getAttributesConfigCollection()
    {
        $attributes = $this->getData('attributes');
        if (!is_object($attributes) || !($attributes instanceof AttributeConfigCollectionInterface)) {
            throw new LocalizedException(__('No attributes configuration found for EAV entity'));
        }
        return $attributes;
    }

    public function getAttributeFactoryClass()
    {
        return $this->getAttributeModel() . 'Factory';
    }
}

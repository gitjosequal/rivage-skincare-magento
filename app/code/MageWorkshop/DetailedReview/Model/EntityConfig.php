<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model;

use Magento\Framework\ObjectManagerInterface;
use MageWorkshop\DetailedReview\Api\Data\Entity\EntityTypeConfigInterface;
use MageWorkshop\DetailedReview\Api\Data\EntityConfigInterface;
use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigCollectionInterface;
use MageWorkshop\DetailedReview\Model\Config\EntityTypeFactory;
use MageWorkshop\DetailedReview\Model\Config\AttributeCollectionFactory;
use MageWorkshop\DetailedReview\Config\Data;
use Magento\Framework\Exception\LocalizedException;

class EntityConfig implements EntityConfigInterface
{
    /** @var ObjectManagerInterface $objectManager */
    protected $objectManager;

    /** @var Data $configData */
    protected $configData;

    /** @var AttributeCollectionFactory $entityTypeFactory */
    protected $entityTypeFactory;

    /** @var AttributeFactory $attributeFactory */
    protected $attributeFactory;

    /** @var AttributeCollectionFactory $attributeCollectionFactory */
    protected $attributeCollectionFactory;

    protected static $entityTypeConfig = [];

    /**
     * EntityConfig constructor.
     * @param \MageWorkshop\DetailedReview\Config\Data  $configData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \MageWorkshop\DetailedReview\Model\Config\EntityTypeFactory $entityTypeFactory
     * @param \MageWorkshop\DetailedReview\Model\Config\AttributeFactory $attributeFactory
     * @param \MageWorkshop\DetailedReview\Model\Config\AttributeCollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        \MageWorkshop\DetailedReview\Config\Data $configData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MageWorkshop\DetailedReview\Model\Config\EntityTypeFactory $entityTypeFactory,
        \MageWorkshop\DetailedReview\Model\Config\AttributeFactory $attributeFactory,
        \MageWorkshop\DetailedReview\Model\Config\AttributeCollectionFactory $attributeCollectionFactory
    ) {
        $this->configData = $configData;
        $this->objectManager = $objectManager;
        $this->entityTypeFactory = $entityTypeFactory;
        $this->attributeFactory = $attributeFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @param string $entityType
     * @return EntityTypeConfigInterface
     * @throws \Exception
     */
    public function getEntityTypeConfig($entityType)
    {
        $entities = $this->getEntities();
        if (!isset($entities[$entityType])) {
            throw new LocalizedException(__(EntityConfigInterface::ENTITY_CONFIG_NOT_FOUND_EXCEPTION, $entityType));
        }

        return $entities[$entityType];
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function getEntityAttributesConfig($entityType)
    {
        $entityConfig = $this->getEntityTypeConfig($entityType);
        return $entityConfig->getAttributesConfigCollection();
    }

    protected function getEntities()
    {
        if (empty(self::$entityTypeConfig)) {
            foreach ($this->configData->get() as $entityType => $entityConfigData) {
                $entityTypeConfig = $this->entityTypeFactory->create();
                $entityTypeConfig->setData('entity_type', $entityType);
                /** @var AttributeConfigCollectionInterface $collection */
                $attributeCollection = $this->attributeCollectionFactory->create();

                foreach ($entityConfigData as $key => $value) {
                    if ($key === 'attributes') {
                        $this->prepareAttributesConfig($attributeCollection, $value);
                    } else {
                        $entityTypeConfig->setData($key, $value);
                    }
                }
                $entityTypeConfig->setData('attributes', $attributeCollection);
                self::$entityTypeConfig[$entityType] = $entityTypeConfig;
            }
        }
        return self::$entityTypeConfig;
    }

    /**
     * @param AttributeConfigCollectionInterface $attributeCollection
     * @param array $attributes
     * @throws \Exception
     */
    protected function prepareAttributesConfig($attributeCollection, $attributes)
    {
        foreach ($attributes as $inputType => $data) {
            $attribute = $this->attributeFactory->create();
            $attribute->populateFromConfig($data);
            $attributeCollection->addItem($attribute);
        }
    }
}

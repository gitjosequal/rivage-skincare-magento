<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model;

use Magento\Eav\Model\ResourceModel\Entity\Attribute as AttributeResourceModel;

/**
 * Class Attribute
 * @package MageWorkshop\DetailedReview\Model
 *
 * This class is extended from catalog EAV attribute in order to be able to use built-in functionality related to
 * managing attributes and attribute swatches. Otherwise we fail because of class casting in some methods and
 * class constructors
 *
 * @method string getAdditionalData()
 * @method Attribute setAdditionalData(string $additionalData)
 * @method string getAttributeVisualSettings()
 * @method Attribute setAttributeVisualSettings(string $attributeVisualSettings)
 * @method Attribute setAttributePlacement(int $attributePlacement)
 */
// Quite a confusing fact, but \Magento\Catalog\Model\ResourceModel\Eav\Attribute is a Model, not a ResourceModel
class Attribute extends \Magento\Catalog\Model\ResourceModel\Eav\Attribute
{
    const MODULE_NAME = 'MageWorkshop_DetailedReview';

    const ENTITY = Details::ENTITY;

    /** @var \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor */
    protected $detailsFlatProcessor;

    /** @var \MageWorkshop\DetailedReview\Model\Indexer\Eav\Processor $eavIndexProcessor */
    protected $eavIndexProcessor;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionDataFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Catalog\Model\Product\ReservedAttributeList $reservedAttributeList
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter
     * @param \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor
     * @param \MageWorkshop\DetailedReview\Model\Indexer\Eav\Processor $eavIndexProcessor
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionDataFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Catalog\Model\Product\ReservedAttributeList $reservedAttributeList,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor,
        \MageWorkshop\DetailedReview\Model\Indexer\Eav\Processor $eavIndexProcessor,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->detailsFlatProcessor = $detailsFlatProcessor;
        $this->eavIndexProcessor = $eavIndexProcessor;
        \Magento\Eav\Model\Entity\Attribute::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $eavConfig,
            $eavTypeFactory,
            $storeManager,
            $resourceHelper,
            $universalFactory,
            $optionDataFactory,
            $dataObjectProcessor,
            $dataObjectHelper,
            $localeDate,
            $reservedAttributeList,
            $localeResolver,
            $dateTimeFormatter,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(AttributeResourceModel::class);
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function beforeSave()
    {
        return \Magento\Eav\Model\Entity\Attribute::beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterSave()
    {
        $this->_eavConfig->clear();
        $result = \Magento\Eav\Model\Entity\Attribute::afterSave();
        $this->_getResource()->addCommitCallback([$this, 'reindex']);

        return $result;
    }

    /**
     * Register indexing event before delete catalog eav attribute
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        return \Magento\Eav\Model\Entity\Attribute::beforeDelete();
    }

    /**
     * Init indexing process after catalog eav attribute delete commit
     *
     * @return $this
     */
    public function afterDeleteCommit()
    {
        \Magento\Eav\Model\Entity\Attribute::afterDeleteCommit();
        $this->reindex();
        return $this;
    }

    public function getEntityTypeId()
    {
        if (!\Magento\Eav\Model\Entity\Attribute::getEntityTypeId()) {
            $entityType = $this->_eavTypeFactory->create()->loadByCode(Details::ENTITY);
            $this->setEntityTypeId($entityType->getId());
        }
        return \Magento\Eav\Model\Entity\Attribute::getEntityTypeId();
    }

    /**
     * Init indexing process after product save
     *
     * @return void
     */
    public function reindex()
    {
        $indexer = $this->eavIndexProcessor->getIndexer();
        if (!$indexer->isScheduled()) {
            $indexer->reindexRow($this->getId());
        }
    }

    /**
     * @return int
     */
    public function getAttributePlacement()
    {
        return (int) $this->getData('attribute_placement');
    }
}

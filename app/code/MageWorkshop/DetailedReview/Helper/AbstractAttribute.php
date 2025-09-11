<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Helper;

use Magento\Eav\Api\Data\AttributeSetSearchResultsInterface;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Framework\Exception\LocalizedException;
use MageWorkshop\DetailedReview\Controller\Adminhtml\AbstractForm;
use MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\Collection;

class AbstractAttribute extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $requiredAttributes = [];

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute $resourceAttribute */
    protected $resourceAttribute;

    /** @var \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository */
    protected $attributeSetRepository;

    /** @var \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory */
    protected $attributeSetFactory;

    /** @var \Magento\Eav\Model\Entity\Attribute\GroupFactory $attributeGroupFactory */
    protected $attributeGroupFactory;

    /** @var \Magento\Eav\Model\Config $eavConfig */
    protected $eavConfig;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var \Magento\Framework\Api\FilterBuilder $filterBuilder */
    protected $filterBuilder;

    /** @var \Magento\Framework\Filter\FilterManager $filterManager */
    protected $filterManager;

    /** @var \Magento\Framework\ObjectManagerInterface $objectManager */
    protected $objectManager;

    /** @var string $entityTypeCode */
    protected $entityTypeCode = '';

    /** @var array $attributeSetsByEntity */
    protected $attributeSetsByEntity = [];

    /**
     * Attribute constructor.
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $resourceAttribute
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Eav\Model\Entity\Attribute\GroupFactory $attributeGroupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $resourceAttribute,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory,
        \Magento\Eav\Model\Entity\Attribute\GroupFactory $attributeGroupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->resourceAttribute = $resourceAttribute;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->eavConfig = $eavConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterManager = $filterManager;
        $this->objectManager = $objectManager;

        parent::__construct($context);
    }

    /**
     * Get entity type ID to manage attributes for
     *
     * @return int
     * @throws LocalizedException
     */
    public function getEntityTypeId()
    {
        $entityType = $this->eavConfig->getEntityType($this->getEntityCode());
        return $entityType->getEntityTypeId();
    }

    /**
     * @return string
     */
    public function getEntityCode()
    {
        return $this->entityTypeCode;
    }

    /**
     * @param string $entityTypeCode
     * @return $this
     */
    public function setEntityCode($entityTypeCode)
    {
        $this->entityTypeCode = $entityTypeCode;
        return $this;
    }

    /**
     * @param int $id
     * @return bool|AttributeSet
     */
    public function getAttributeSet($id)
    {
        $result = false;
        try {
            $attributeSet = $this->attributeSetRepository->get($id);
            $result = $attributeSet;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        }
        return $result;
    }

    /**
     * @param string $name
     * @return AttributeSetSearchResultsInterface[]
     */
    public function getAttributeSetsByName($name)
    {
        $additionalFilters = [
            $this->filterBuilder
                ->setField('attribute_set_name')
                ->setValue($name)
                ->create()
        ];
        return $this->getAttributeSets($additionalFilters);
    }

    /**
     * @param array $additionalFilters
     * @return AttributeSetSearchResultsInterface[]
     */
    public function getAttributeSets(array $additionalFilters = [])
    {
        $entityCode = $this->getEntityCode();
        if (empty($additionalFilters) && isset($this->attributeSetsByEntity[$entityCode])) {
            return $this->attributeSetsByEntity[$entityCode];
        }

        $result = [];
        $filters = [
            $this->filterBuilder
                ->setField('entity_type_code')
                ->setValue($entityCode)
                ->create()
        ];
        $filters = array_merge($filters, $additionalFilters);
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilters($filters)
                ->create();

            $attributeSet = $this->attributeSetRepository->getList($searchCriteria);
            $result = $attributeSet->getItems();

            if (empty($additionalFilters)) {
                $this->attributeSetsByEntity[$entityCode] = $result;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {}
        return $result;
    }

    /**
     * Get entity attributes
     * We can not work with the repo here because it requires attribute to be present at least in one attribute set
     * in the Magento\Eav\Model\AttributeRepository::getList method
     * @return Collection
     * @throws LocalizedException
     */
    public function getAttributesCollection()
    {
        $entityType = $this->eavConfig->getEntityType($this->getEntityCode());
        // unfortunately, there is a bug in the $entityType->getAttributeCollection()
        // and default EAV collection instance is always returned (
        /** @var Collection $attributeCollection */
        $attributeCollection = $this->objectManager->create($entityType->getEntityAttributeCollection());
        $attributeCollection->getSelect()->distinct();
        return $attributeCollection;
    }

    /**
     * Get entity attributes
     * We can not work with the repo here because it requires attribute to be present at least in one attribute set
     * in the Magento\Eav\Model\AttributeRepository::getList method
     * @return Collection
     * @throws LocalizedException
     */
    public function getVisibleAttributesCollection()
    {
        $attributeCollection = $this->getAttributesCollection();
        $attributeCollection->addFieldToFilter('is_visible_on_front', 1);
        return $attributeCollection;
    }

    /**
     * Get indexable attributes without static ones because static fields and their values are copied frm the main table
     *
     * @return Collection
     * @throws LocalizedException
     */
    public function getIndexableDynamicAttributeCollection()
    {
        $attributeCollection = $this->getVisibleAttributesCollection();
        $attributeCollection->addFieldToFilter('backend_type', ['neq' => 'static'])
            ->getSelect()
            ->join(
                ['ea' =>  $attributeCollection->getTable('eav_entity_attribute')],
                'main_table.attribute_id = ea.attribute_id',
                []
            );

        return $attributeCollection;
    }

    /**
     * @return array
     */
    public function getRequiredAttributes()
    {
        return $this->requiredAttributes;
    }

    public function getRequiredAttributeIds()
    {
        $requiredAttributeIds = [];
        $attributeCollection = $this->getVisibleAttributesCollection();
        $attributeCollection->addFieldToFilter('attribute_code', ['in' => $this->getRequiredAttributes()]);
        /** @var \Magento\Eav\Model\Attribute $attribute */
        foreach ($attributeCollection as $attribute) {
            $requiredAttributeIds[$attribute->getAttributeCode()] = $attribute->getId();
        }
        return $requiredAttributeIds;
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function getDefaultAttributeSetId()
    {
        if (!isset($this->defaultAttributeSetId)) {
            $this->defaultAttributeSetId = (int) $this->eavConfig->getEntityType($this->getEntityTypeId())
                ->getDefaultAttributeSetId();
        }
        return $this->defaultAttributeSetId;
    }

    /**
     * @param int $attributeSetId
     * @param string $name
     * @param array $includedAttributes
     * @param array $excludedAttributes
     * @return AttributeSet
     * @throws \InvalidArgumentException
     * @throws LocalizedException
     * @throws \Exception
     */
    public function createAttributeSet($attributeSetId, $name, $includedAttributes, $excludedAttributes)
    {
        // 1. Get the set entity if it exists, validate set nam
        $attributeSet = $this->getAttributeSet($attributeSetId);
        if ($attributeSetId && !$attributeSet) {
            throw new \InvalidArgumentException(__(AbstractForm::FORM_NO_LONGER_EXISTS_EXCEPTION));
        }

        if (!$attributeSetId) {
            /** @var AttributeSet $attributeSet */
            $attributeSet = $this->attributeSetFactory->create();
        }

        // 2. Init data
        /** @var \Magento\Framework\Filter\FilterManager $filterManager */
        $name = $this->filterManager->stripTags($name);
        $attributeSet->setEntityTypeId($this->getEntityTypeId())
            ->setAttributeSetName(trim($name));

        $excludedAttributes = array_flip($excludedAttributes);
        if ($attributeSet->getId()) {
            $visibleAttributesCollection = $this->getVisibleAttributesCollection();
            $visibleAttributesCollection->addAttributeSetInfo($attributeSet->getId());
            $visibleAttributes = $visibleAttributesCollection->getItems();
            foreach ($excludedAttributes as $attributeId => $index) {
                if (!isset($visibleAttributes[$attributeId])) {
                    unset($excludedAttributes[$attributeId]);
                    continue;
                }

                /** @var \Magento\Eav\Model\Attribute $attribute */
                $attribute = $visibleAttributes[$attributeId];
                if ($entityAttributeId = (int) $attribute->getData('entity_attribute_id')) {
                    $excludedAttributes[$attributeId] = $entityAttributeId;
                } else {
                    unset($excludedAttributes[$attributeId]);
                }
            }
        }

        $attributeSet->validate();
        if (!$attributeSet->getId()) {
            $attributeSet->save();
            $attributeGroup = $this->attributeGroupFactory->create();
            $attributeGroup->setAttributeGroupName('General')
                ->setAttributeSetId($attributeSet->getId())
                ->setDefaultId(1)
                ->save();
        }

        // 3. Check if all required attributes are present. UI does not allow excluding them,
        // but better to be on the save side
        foreach ($this->getRequiredAttributeIds() as $attributeCode => $attributeId) {
            if (!in_array($attributeId, $includedAttributes, false)) {
                array_unshift($includedAttributes, $attributeId);
            }

            $occurrence = array_search($attributeId, $excludedAttributes);
            if ($occurrence !== false) {
                unset($excludedAttributes[$occurrence]);
            }
        }

        /* See /misc/apps/drm2_magento_ce_210/vendor/magento/module-eav/Model/Entity/Attribute/Set.php as line #226
        * there is the following code there:
        *     $entityAttribute = $this->_resourceAttribute->getEntityAttribute($entityAttributeId);
        *     if (!$entityAttribute) {
        *     }
        * This is why we need to remove all non-entity attributes from the $excludedAttributes array.
        * attribute is supposed to be "non entity attribute" if it is not included in any attribute set
        */
        $entityAttributes = $this->getEntityAttributes($excludedAttributes);
        foreach ($excludedAttributes as $index => $attributeId) {
            if (!in_array($attributeId, $entityAttributes)) {
                unset($excludedAttributes[$index]);
            }
        }

        // Each element should be an array in "\Magento\Eav\Model\Entity\Attribute\Set::organizeData()"
        $defaultGroupId = $attributeSet->getDefaultGroupId();
        foreach ($includedAttributes as $index => $attributeId) {
            // id, groupId, sort order
            $includedAttributes[$index] = [
                (int) $attributeId,
                $defaultGroupId,
                10 + $index * 10
            ];
        }

        // 4. Organize data
        $data = [
            'attribute_set_name' => $name,
            'groups'             => [[$defaultGroupId, 'General', 0]],
            'removeGroups'       => [],
            'attributes'         => $includedAttributes,
            'not_attributes'     => $excludedAttributes
        ];

        $attributeSet->organizeData($data);
        $attributeSet->save();
        return $attributeSet;
    }

    /**
     * Optimized version of the \Magento\Eav\Model\ResourceModel\Entity\Attribute::getEntityAttribute
     * This method was added in Magento 2.1, so we need this for back compatibility
     *
     * @param array $attributeIds
     * @return array
     */
    protected function getEntityAttributes($attributeIds)
    {
        $select = $this->resourceAttribute->getConnection()
            ->select()
            ->distinct()
            ->from(
                $this->resourceAttribute->getTable('eav_entity_attribute'),
                'entity_attribute_id'
            )->where(
                'entity_attribute_id IN(?)',
                $attributeIds
            );
        $entityAttributeIds = $this->resourceAttribute->getConnection()->fetchCol($select);
        return $entityAttributeIds;
    }
}

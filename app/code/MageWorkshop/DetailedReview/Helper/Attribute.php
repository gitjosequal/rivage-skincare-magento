<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Helper;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\Review as ReviewModel;
use MageWorkshop\DetailedReview\Model\Attribute as AttributeModel;
use MageWorkshop\DetailedReview\Model\Details;

class Attribute extends \MageWorkshop\DetailedReview\Helper\AbstractAttribute
{
    const INCLUDED_FIELDS = 'included-fields';

    const AVAILABLE_FIELDS = 'available-fields';

    const BOOLEAN_YES_OPTION = 2;

    const BOOLEAN_NO_OPTION = 1;

    const EVENT_MAGEWORSHOP_DETAILEDREVIEW_GET_FORM_FIELDS = 'mageworkshop_detailedreview_get_form_fields';

    const EVENT_MAGEWORSHOP_DETAILEDREVIEW_PREPARE_REVIEW_FIELDS_CONFIGURATION
        = 'mageworkshop_detailedreview_prepare_review_fields_configuration';

    const REVIEW_FORM_ATTRIBUTE_CODE = 'review_form';

    const VALIDATION_RULE_MAXIMUM_LENGTH = 'maximum-length';

    const VALIDATION_RULE_MINIMUM_LENGTH = 'minimum-length';

    const VALIDATION_RULE_VALIDATE_LENGTH = 'validate-length';

    const VALIDATION_RULE_CUSTOM_VALIDATE_LENGTH = 'custom-validate-length';

    const WIDTH_FIELD_FOR_DESKTOP = 'width_field_for_desktop';

    const WIDTH_FIELD_FOR_TABLE = 'width_field_for_table';

    const WIDTH_FIELD_FOR_MOBILE = 'width_field_for_mobile';

    const LAST_FIELD_IN_LINE = 'last_field_in_line';

    const HORIZONTAL_LINE = 'horizontal_line';

    /** @var string $entityType */
    protected $entityTypeCode = Details::ENTITY;

    /** @var array $attributes */
    protected $attributeCollections = [];

    protected $attributeConfigurations = [];

    protected $requiredAttributes = [
        'nickname',
        'title',
        'detail'
    ];

    /** @var \Magento\Framework\DataObject $dataObject */
    protected $dataObject;

    /** @var \Magento\Swatches\Helper\Data $swatchHelper */
    protected $swatchHelper;

    /** @var \MageWorkshop\DetailedReview\Helper\Media $mediaHelper */
    protected $mediaHelper;

    /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
    protected $storeManager;

    /**
     * List of attribute properties that are not needed on the frontend
     *
     * @var array $excessAttributeDataProperties
     */
    protected $excessAttributeDataProperties = [
        'entity_type_id',
        'attribute_model',
        'backend_model',
        'backend_type',
        'backend_table',
        'frontend_model',
        'source_model',
        'entity_attribute_id',
        'attribute_set_id',
        'attribute_group_id',
        'is_unique'
    ];

    /** @var array $attributeOptions */
    protected $attributeOptions = [];
    /**
     * @var \MageWorkshop\Core\Helper\Serializer
     */
    private $serializer;

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review
     */
    private $reviewHelper;

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
     * @param DataObject $dataObject
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param Media $mediaHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorkshop\Core\Helper\Serializer $serializer
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
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
        \Magento\Framework\DataObject $dataObject,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \MageWorkshop\DetailedReview\Helper\Media $mediaHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorkshop\Core\Helper\Serializer $serializer,
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct(
            $resourceAttribute,
            $attributeSetRepository,
            $attributeSetFactory,
            $attributeGroupFactory,
            $eavConfig,
            $searchCriteriaBuilder,
            $filterBuilder,
            $filterManager,
            $objectManager,
            $context
        );
        $this->dataObject = $dataObject;
        $this->swatchHelper = $swatchHelper;
        $this->mediaHelper = $mediaHelper;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        $this->reviewHelper = $reviewHelper;
    }

    /**
     * @param Product $product
     * @param array $storeIds - leave empty for current store only
     * @return array
     * @throws \InvalidArgumentException
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function getReviewFormAttributesConfiguration($product, array $storeIds = [])
    {
        $productId = (int) $product->getId();
        $cacheKey = $this->getCacheKey($productId, $storeIds);

        if (!isset($this->attributeConfigurations[$cacheKey])) {
            $config = [];
            $excessAttributeDataProperties = array_flip($this->excessAttributeDataProperties);
            $reviewFormAttributes = $this->getReviewFormAttributes($product, $storeIds);
            $this->_eventManager->dispatch(
                self::EVENT_MAGEWORSHOP_DETAILEDREVIEW_PREPARE_REVIEW_FIELDS_CONFIGURATION,
                ['review_form_attributes' => $reviewFormAttributes]
            );

            /** @var AttributeModel $attribute */
            foreach ($reviewFormAttributes as $attribute) {
                $this->unpackValuesForVisualSettingsFields($attribute);
                $data = array_diff_key($attribute->getData(), $excessAttributeDataProperties);

                // Static (they are also system) attributes (nickname, title, summary)
                // can not be translated via admin panel
                if ($attribute->getBackendType() === AttributeModel::TYPE_STATIC) {
                    $data['store_label'] = isset($data['store_label'])
                        ? __($data['store_label'])
                        : __($data['frontend_label']);
                }

                $data['additional_data'] = (isset($data['additional_data']) && !empty($data['additional_data']))
                    ? $this->serializer->unserialize($data['additional_data'])
                    : [];

                if ($this->isVisualSwatch($attribute)) {
                    $data['frontend_input'] = 'swatch';
                }

                if (!empty($data['is_required'])) {
                    $data['validation'] = ['required' => true];
                }

                $validationRules = is_array($data['validate_rules'])
                    ? $data['validate_rules']
                    : $this->serializer->unserialize((string) $data['validate_rules']);

                $data['validation_class'] = implode(' ', $this->getValidationClassesArray($validationRules));

                $data['default_value'] = explode(',', $data['default_value']);
                $data['options'] = array_values($this->getAttributeOptionValues($attribute));

                $config[] = $data;
            }

            $this->attributeConfigurations[$cacheKey] = $config;
        }
        return $this->attributeConfigurations[$cacheKey];
    }

    /**
     * Get review form fields by the product id that is in the review's entity_pk_value
     * Use this method only when there is no other way to get product information
     * for example during import when the Product is not available
     * Note! Method execution is very heavy because the product is loaded (from collection)!
     *
     * @param ReviewModel $review
     * @param array $storeIds
     * @return array
     * @throws \InvalidArgumentException
     * @throws LocalizedException
     * @throws \Exception
     */
    public function getReviewFormAttributesConfigurationByReview(ReviewModel $review, array $storeIds = [])
    {
        // Review has no product id when this is a new entity, so take the current_product
        if (!$productId = $this->reviewHelper->getReviewProductId($review)) {
            return [];
        }

        $cacheKey = $this->getCacheKey($productId, $storeIds);

        if (isset($this->attributeConfigurations[$cacheKey])) {
            return $this->attributeConfigurations[$cacheKey];
        }

        $product = $this->reviewHelper->getProductByReview($review);

        return $product->getId()
            ? $this->getReviewFormAttributesConfiguration($product, $storeIds)
            : [];
    }

    /**
     * @param array $validationRules
     * @return array
     */
    private function getValidationClassesArray($validationRules)
    {
        $validationClasses = [];

        if (!is_array($validationRules) || empty($validationRules)) {
            $validationRules = [];
        }
        foreach ($validationRules as $rule) {
            $type = isset($rule['type']) ? $rule['type'] : '';
            $value = isset($rule['value']) ? $rule['value'] : '';

            // use-case for general validation rules without additional options
            if (!in_array(
                $type,
                [self::VALIDATION_RULE_MAXIMUM_LENGTH, self::VALIDATION_RULE_MINIMUM_LENGTH],
                false
            )) {
                $validationClasses[] = $type;
                continue;
            }

            // The rule does not have required additional params and must be skipped
            // Need to show error message for admin here
            if (!$value = (int) $value) {
                continue;
            }

            if (!in_array(self::VALIDATION_RULE_VALIDATE_LENGTH, $validationClasses, true)) {
                $validationClasses[] = self::VALIDATION_RULE_VALIDATE_LENGTH;
                $validationClasses[] = self::VALIDATION_RULE_CUSTOM_VALIDATE_LENGTH;
            }

            $validationClasses[] =  $type . '-' . $value;
        }

        return $validationClasses;
    }

    /**
     * @param Product $product
     * @param array $storeIds - leave empty for current store only
     * @return \MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\Collection
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function getReviewFormAttributes($product, array $storeIds = [])
    {
        $productId = (int) $product->getId();
        $cacheKey = $this->getCacheKey($productId, $storeIds);

        if (!isset($this->attributeCollections[$cacheKey])) {
            // find the category of the lowest level that has custom settings
            /** @var array[\Magento\Eav\Model\Entity\Attribute\Set] $attributeSets */
            $attributeSets = $this->getAttributeSets();

            // We do not want to affect the original object, so better to clone it to be on the safe side
            // Select object is cloned inside the __clone() magic method
            /** @var CategoryCollection $categoryCollection */
            $categoryCollection = clone $product->getCategoryCollection();
            $categoryCollection->getSelect()
                ->join(
                    // NOTE! later this table name may be changed to the one found by
                    // $this->resolveCategoryProductIndexTableName()  depending on the Magento version
                    ['cat_index' => $categoryCollection->getTable('catalog_category_product_index')],
                    sprintf(
                        'cat_index.category_id = e.%s ',
                        $categoryCollection->getIdFieldName()
                    )
                )
                ->order([
                    'level ' . Collection::SORT_ORDER_DESC
                ])
                ->limit(1);

            $attributeSetIds = [];
            $currentStoreId = $this->storeManager->getStore()->getId();

            if (empty($storeIds)) {
                $storeIds = [$currentStoreId];
            }

            // Need to get attribute sets in all stores one by one (
            // This happens only in Admin Panel because one review can belong to the multiple stores.
            // This can not be optimized because we'll get duplicates in the Category Collection if we join the
            // catalog_category_product_index table or we'll have too use extremely low-level custom logic.
            // Good side of the things is that only very simple and fast queries are performed.
            foreach ($storeIds as $storeId) {
                /** @var \Magento\Catalog\Model\Category $category */
                $categoryCollection->clear();
                // Start the code to fix the
                $this->useProperSegmentationTable($categoryCollection, $storeId);

                $categoryCollection->setStoreId($storeId);
                
                
                $categoryCollection->getSelect()
                    ->reset(Select::WHERE)
                    ->where('cat_index.store_id = ?', (int) $storeId);

                if ($productId) {
                    $categoryCollection->getSelect()->where('cat_index.product_id = ?', $productId);
                }

                $category = $categoryCollection->getFirstItem();
                $reviewFormId = $category->getData(self::REVIEW_FORM_ATTRIBUTE_CODE);

                if (isset($attributeSets[$reviewFormId])) {
                    /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
                    $attributeSet = $attributeSets[$reviewFormId];
                    $attributeSetIds[] = $attributeSet->getId();
                } else {
                    $attributeSetIds[] = $this->eavConfig->getEntityType(Details::ENTITY)->getDefaultAttributeSetId();
                }
            }

            $attributeSetIds = array_unique($attributeSetIds);
            // $attributeCollection->setAttributeSetFilter() works both with arrays and int/string
            // but uses "equal" comparison instead of array
            if (count($attributeSetIds) === 1) {
                $attributeSetIds = $attributeSetIds[0];
            }
            $attributeCollection = $this->getVisibleAttributesCollection();
            $attributeCollection->setAttributeSetFilter($attributeSetIds);

            // Need to load frontend labels for store views and default ones for Admin Panel
            // This equals to loading data for the current store
            $attributeCollection->addStoreLabel($currentStoreId);
            $this->_eventManager->dispatch(
                self::EVENT_MAGEWORSHOP_DETAILEDREVIEW_GET_FORM_FIELDS,
                ['collection' => $attributeCollection]
            );

            $this->attributeCollections[$cacheKey] = $attributeCollection;
            // $sql = (string) $attributeCollection->getSelect();
            // $items = $attributeCollection->getItems();
        }

        // Better to return the clone so it is possible to perform any additional operations
        // or apply more filters on the collection.
        return clone $this->attributeCollections[$cacheKey];
    }

    /**
     * Handle using segmentation tables like `catalog_category_product_index_store1`
     * There is plugin \Magento\Catalog\Model\Indexer\Category\Product\Plugin\TableResolver in Magento
     * But it won't handle stores in Admin area, so we can do this in a straightforward way
     * And leave using `catalog_category_product_index` for back-compatibility with Magento 2.1
     *
     * @param CategoryCollection $categoryCollection
     * @param $storeId
     * @throws \Zend_Db_Select_Exception
     */
    private function useProperSegmentationTable(CategoryCollection $categoryCollection, $storeId)
    {
        $connection = $categoryCollection->getResource()->getConnection();
        $categoryProductsIndexTable = $categoryCollection->getTable('catalog_category_product_index');
        $segmentationTable = $this->resolveCategoryProductIndexTableName(
            $categoryProductsIndexTable,
            $connection,
            $storeId
        );

        $fromPart = [];

        foreach ($categoryCollection->getSelect()->getPart(Select::FROM) as $alias => $from) {
            if ($alias === 'cat_index') {
                $from['tableName'] = $segmentationTable;
            }

            $fromPart[$alias] = $from;
        }

        $categoryCollection->getSelect()
            ->reset(Select::FROM)
            ->setPart(Select::FROM, $fromPart);
    }

    /**
     * @param string $table
     * @param AdapterInterface $connection
     * @param int $storeId
     * @return string
     */
    private function resolveCategoryProductIndexTableName($table, AdapterInterface $connection, $storeId)
    {
        $indexTableSuffix = "_store$storeId";
        $segmentationIndexTable = $table . $indexTableSuffix;

        $indexTable = $connection->isTableExists($segmentationIndexTable)
            ? $segmentationIndexTable
            : $table;

        return $indexTable;
    }

    /**
     * @param $productId
     * @param array $storeIds
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCacheKey($productId, array $storeIds = [])
    {
        if (empty($storeIds)) {
            $storeIds = [$this->storeManager->getStore()->getId()];
        }

        return implode(',', $storeIds) . '/' . $productId;
    }

    /**
     * @param AttributeModel $attribute
     * @return array
     */
    public function getAttributeOptionValues(AttributeModel $attribute)
    {
        $attributeId = $attribute->getId();

        if (!isset($this->attributeOptions[$attributeId])) {
            $options = $attribute->getOptions();

            if ($attribute->getFrontendInput() === 'boolean') {
                $options = $this->getBooleanOptions();
            }

            $optionsData = [];

            if (in_array($attribute->getData('frontend_input'), ['boolean', 'select'], true)
                && ($attribute->getData('swatch_input_type') !== 'visual')
            ) {
                $optionsData[] = [
                    'label' => (string) __('-- Please Select --'),
                    'value' => 'none',
                ];
            }

            // 1. First option is an empty one
            // 2. Values are already ordered by 'sort_order' in the ascending direction
            // 3. See $data['default_value'] for default option id
            foreach ($options as $option) {
                if ($option->getValue() == '') {
                    continue;
                }

                $optionsData[$option->getValue()] = [
                    'label' => $option->getLabel(),
                    'value' => (int) $option->getValue(),
                ];
            }

            if ($this->isVisualSwatch($attribute)) {
                $swatches = $this->swatchHelper->getSwatchesByOptionsId(array_keys($optionsData));
                foreach ($swatches as $swatch) {
                    if (isset($swatch['value'])) {
                        $imageSrc = $this->mediaHelper->getSwatchAttributeImage(
                            Media::SWATCH_THUMBNAIL_NAME,
                            $swatch['value']
                        );
                    } else {
                        $imageSrc = '';
                    }
                    $optionsData[$swatch['option_id']]['src'] = $imageSrc;
                }
            }

            $this->attributeOptions[$attributeId] = $optionsData;
        }
        return $this->attributeOptions[$attributeId];
    }

    /**
     * @param AttributeModel $attribute
     * @return bool
     */
    public function isVisualSwatch(AttributeModel $attribute)
    {
        return $this->swatchHelper->isVisualSwatch($attribute);
    }

    /**
     * @return DataObject[]
     */
    public function getBooleanOptions()
    {
        $yes = clone $this->dataObject;
        $no  = clone $this->dataObject;
        $yes->setData(
            [
                'label' => (string) __('Yes'),
                'value' => self::BOOLEAN_YES_OPTION
            ]
        );
        $no->setData(
            [
                'label' => (string) __('No'),
                'value' => self::BOOLEAN_NO_OPTION
            ]
        );

        return [$yes, $no];
    }

    /**
     * @return array
     */
    public function getFieldForVisualSettings()
    {
        return [
            self::WIDTH_FIELD_FOR_DESKTOP,
            self::WIDTH_FIELD_FOR_TABLE,
            self::WIDTH_FIELD_FOR_MOBILE,
            self::LAST_FIELD_IN_LINE,
            self::HORIZONTAL_LINE
        ];
    }

    /**
     * @param $data
     * @return string
     */
    public function processVisualSettings($data)
    {
        $attributeVisualSettings = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $this->getFieldForVisualSettings())) {
                $attributeVisualSettings[$key] = $value;
            }
        }

        return $this->serializer->serialize($attributeVisualSettings);
    }

    /**
     * @param $attribute
     * @throws \InvalidArgumentException
     */
    public function unpackValuesForVisualSettingsFields(AttributeModel $attribute)
    {
        if ($values = $attribute->getAttributeVisualSettings()) {
            $attributeVisualSettings = $this->serializer->unserialize($values);

            foreach ($attributeVisualSettings as $key => $value) {
                $attribute->setData($key, $value);
            }
        }
    }
}

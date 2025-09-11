<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Setup;

use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigInterface;
use MageWorkshop\DetailedReview\Api\Data\Entity\EntityTypeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Abstract EAV installer class with rich features:
 * - automatically create EAV entity DB scheme
 * - can create Foreign Keys and indexes
 * - install EAV entity, attributes and attribute options
 * - all configurations are pulled from the mageworkshop_eav_attributes.xml file
 *
 * See example usage in the MageWorkshop\DetailedReview\Setup\DetailsSetup class
 * Class is injected via DI in the schema and data installation scripts
 *
 * Class AbstractEavSetup
 * @package MageWorkshop\DetailedReview\Setup
 */
abstract class AbstractEavSetup extends \Magento\Eav\Setup\EavSetup
{
    const ATTRIBUTE_TYPE_DATA_CONFIG_NOT_FOUND_EXCEPTION
        = 'Unable to find the data type configuration of the %1 attribute type';

    const MISSED_ENTITY_INSTALLATION_INFO_EXCEPTION = 'Wrong EAV entity configuration detected';

    const INCORRECT_COLUMN_DEFINITION_EXCEPTION
        = 'Column definition should implement MageWorkshop\DetailedReview\Setup\ColumnConfigInterface';

    const INCORRECT_FOREIGN_KEY_DEFINITION_EXCEPTION
        = 'Foreign Key definition should implement MageWorkshop\DetailedReview\Setup\ForeignKeyConfigInterface';

    const INCORRECT_INDEX_DEFINITION_EXCEPTION
        = 'Index definition should implement MageWorkshop\DetailedReview\Setup\IndexConfigInterface';

    /** @var \MageWorkshop\DetailedReview\Api\Data\EntityConfigInterface $entityConfig */
    protected $entityConfig;

    /** @var \Magento\Eav\Model\AttributeRepository $attributeRepository */
    protected $attributeRepository;

    /** @var \Magento\Framework\ObjectManagerInterface $objectManager */
    protected $objectManager;

    /** @var \MageWorkshop\DetailedReview\Setup\ColumnConfigFactory $columnConfigFactory */
    protected $columnConfigFactory;

    /** @var \MageWorkshop\DetailedReview\Setup\ForeignKeyConfigFactory $foreignKEyConfigFactory */
    protected $foreignKeyConfigFactory;

    /** @var \MageWorkshop\DetailedReview\Setup\IndexConfigFactory $indexConfigFactory */
    protected $indexConfigFactory;

    /** @var \Magento\Eav\Model\Config $eavConfig */
    protected $eavConfig;

    /** @var \MageWorkshop\DetailedReview\Setup\VisualSwatchInstaller $visualSwatchInstaller */
    protected $visualSwatchInstaller;
    /**
     * @var \MageWorkshop\Core\Helper\Serializer
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \MageWorkshop\DetailedReview\Api\Data\EntityConfigInterface $entityConfig
     * @param \Magento\Eav\Model\AttributeRepository $attributeRepository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \MageWorkshop\DetailedReview\Setup\ColumnConfigFactory $columnConfigFactory
     * @param \MageWorkshop\DetailedReview\Setup\ForeignKeyConfigFactory $foreignKeyConfigFactory
     * @param \MageWorkshop\DetailedReview\Setup\IndexConfigFactory $indexConfigFactory
     * @param \MageWorkshop\DetailedReview\Setup\VisualSwatchInstaller $visualSwatchInstaller
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \MageWorkshop\Core\Helper\Serializer $serializer
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Eav\Model\Entity\Setup\Context $context,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \MageWorkshop\DetailedReview\Api\Data\EntityConfigInterface $entityConfig,
        \Magento\Eav\Model\AttributeRepository $attributeRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MageWorkshop\DetailedReview\Setup\ColumnConfigFactory $columnConfigFactory,
        \MageWorkshop\DetailedReview\Setup\ForeignKeyConfigFactory $foreignKeyConfigFactory,
        \MageWorkshop\DetailedReview\Setup\IndexConfigFactory $indexConfigFactory,
        \MageWorkshop\DetailedReview\Setup\VisualSwatchInstaller $visualSwatchInstaller,
        \Magento\Eav\Model\Config $eavConfig,
        \MageWorkshop\Core\Helper\Serializer $serializer,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
        $this->entityConfig = $entityConfig;
        $this->attributeRepository = $attributeRepository;
        $this->objectManager = $objectManager;
        $this->columnConfigFactory = $columnConfigFactory;
        $this->foreignKeyConfigFactory = $foreignKeyConfigFactory;
        $this->indexConfigFactory = $indexConfigFactory;
        $this->visualSwatchInstaller = $visualSwatchInstaller;
        $this->eavConfig = $eavConfig;
        $this->serializer = $serializer;
        $this->registry = $registry;
    }

    abstract protected function getEntityCode();

    /**
     * Get array of ColumnConfigInterface that allows installing static attribute columns in the main entity table
     * Also it can be used to add any fields including Foreign Keys to the main entity
     * See example usage in the DetailedReview module
     *
     * @return array
     */
    abstract protected function getAdditionalColumns();

    /**
     * Get array of IndexInterface that allows installing indexes for main table. See example usage in the
     * DetailedReview module. Note that only indexes for columns from the getAdditionalColumns() should be defined here
     * Other (default) indexes are created automatically
     *
     * @return array
     */
    abstract protected function getIndexes();

    /**
     * Create additional attributes table. Unfortunately, this table is required for any EAV entity
     *
     * @return $this
     */
    abstract protected function createAdditionalAttributeTable();

    /**
     * Get basic config for entity attributes. Additional data is pulled from the mageworkshop_eav_attributes.xml file
     *
     * @return array
     */
    abstract protected function getEntityAttributes();

    /**
     * Create EAV entity schema based on the data from the mageworkshop_eav_attributes.xml file
     * Must be used to install scheme (InstallScheme.php, UpgradeScheme.php)
     *
     * @throws LocalizedException
     */
    public function installEntitySchema()
    {
        $this->createEntityMainTable();
        foreach ($this->getEntityAttributeBackendTypes() as $backendType) {
            $this->createEntityBackendTable($backendType);
        }
        $this->createAdditionalAttributeTable();
    }

    /**
     * Create basic entity table without any additional columns.
     *
     * @throws LocalizedException
     * @throws \Zend_Db_Exception
     */
    protected function createEntityMainTable()
    {
        $setup = $this->getSetup();

        $table = $setup->getConnection()
            ->newTable($setup->getTable($this->getEntityCode()))
            ->addColumn(
                $this->getEntityIdField(),
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Entity Id'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default ' => Table::TIMESTAMP_INIT
                ],
                'Created At'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default'  => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Updated At'
            )->setComment(
                ucfirst($this->getEntityCode()) . ' Main Details'
            );

        $foreignKeys = [];
        /** @var ColumnConfigInterface $attribute */
        foreach ($this->getAdditionalColumns() as $attributeColumnConfig) {
            if (!is_object($attributeColumnConfig) || !($attributeColumnConfig instanceof ColumnConfigInterface)) {
                throw new LocalizedException(__(self::INCORRECT_COLUMN_DEFINITION_EXCEPTION));
            }

            $table->addColumn(
                $attributeColumnConfig->getName(),
                $attributeColumnConfig->getType(),
                $attributeColumnConfig->getLength(),
                $attributeColumnConfig->getOptions(),
                $attributeColumnConfig->getComment()
            );

            if ($foreignKeyConfig = $attributeColumnConfig->getForeignKeyConfig()) {
                if (!($foreignKeyConfig instanceof ForeignKeyConfigInterface)) {
                    throw new LocalizedException(__(self::INCORRECT_FOREIGN_KEY_DEFINITION_EXCEPTION));
                }
                $foreignKeys[] = $foreignKeyConfig;
            }
        }

        /** @var IndexConfigInterface $indexConfig */
        foreach ($this->getIndexes() as $indexConfig) {
            if (!($indexConfig instanceof IndexConfigInterface)) {
                throw new LocalizedException(__(self::INCORRECT_INDEX_DEFINITION_EXCEPTION));
            }

            $table->addIndex(
                $indexConfig->getName(),
                $indexConfig->getColumn(),
                $indexConfig->getOptions()
            );
        }

        /** @var ForeignKeyConfigInterface $foreignKeyConfig */
        foreach ($foreignKeys as $foreignKeyConfig) {
            $table->addForeignKey(
                $foreignKeyConfig->getFkName(),
                $foreignKeyConfig->getColumn(),
                $setup->getTable($foreignKeyConfig->getReferenceTable()),
                $foreignKeyConfig->getReferenceColumn(),
                $foreignKeyConfig->getOnDelete()
            );
        }

        $this->getSetup()->getConnection()->createTable($table);
    }

    /**
     * Install basic entity data via the default functionality
     * {@inheritdoc}
     */
    public function getDefaultEntities()
    {
        $entityCode = $this->getEntityCode();
        $entityConfig = $this->getEntityTypeConfig();
        $entityAttributes = $this->getEntityAttributes();

        // Populate attributes data with additional configs from the mageworkshop_eav_attributes.xml
        foreach ($entityAttributes as $attributeCode => &$attributeConfig) {
            $attributeConfigObject = $this->getAttributeConfig($entityCode, $attributeConfig['input']);
            /** @var \MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigInterface $attributeConfigObject */
            $attributeConfig = array_merge($attributeConfigObject->getConfig(), $attributeConfig);
            // For swatch attributes

            if ($inputType = $attributeConfigObject->getInputType()) {
                $attributeConfig['input'] = $inputType;
            }
        }

        $entityAttributes = array_merge(
            [
                'created_at' => [
                    'type'       => 'static',
                    'label'      => 'Created At',
                    'input'      => 'date',
                    'required'   => false,
                    'sort_order' => 100,
                    'visible_on_front' => false
                ],
                'updated_at' => [
                    'type'       => 'static',
                    'label'      => 'Updated At',
                    'input'      => 'date',
                    'required'   => false,
                    'sort_order' => 110,
                    'visible_on_front' => false
                ]
            ],
            $entityAttributes
        );

        $attributeFactory = $this->objectManager->get($entityConfig->getAttributeFactoryClass());
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        $attribute = $attributeFactory->create();

        foreach ($entityAttributes as $attributeCode => &$attributeConfig) {
            if (!isset($attributeConfig['type'])) {
                $attributeConfig['type'] = $attribute->getBackendTypeByInput($attributeConfig['input']);
            }
        }

        $entities = [
            $entityCode => [
                'entity_model'    => $entityConfig->getEntityModel(),
                'attribute_model' => $entityConfig->getAttributeModel(),
                'id_field'        => $this->getEntityIdField(),
                'table'           => $entityCode,
                'increment_model' => $entityConfig->getIncrementModel(),
                'additional_attribute_table' => $entityConfig->getAdditionalAttributeTable(),
                'entity_attribute_collection' => $entityConfig->getEntityAttributeCollection(),
                'attributes' => $entityAttributes
            ]
        ];
        return $entities;
    }

    /**
     * @param string $entityType
     * @param string $frontendInput
     * @return false|AttributeConfigInterface
     * @throws \DomainException
     */
    protected function getAttributeConfig($entityType, $frontendInput)
    {
        return $this->entityConfig
            ->getEntityAttributesConfig($entityType)
            ->getItem($frontendInput);
    }

    /**
     * @param string $backendType
     * @throws \Zend_Db_Exception
     */
    protected function createEntityBackendTable($backendType)
    {
        $setup = $this->getSetup();
        $connection = $setup->getConnection();
        if ($setup->getConnection()->isTableExists($backendType)) {
            return;
        }

        $entityCode = $this->getEntityCode();
        $idFieldName = $this->getEntityIdField();
        $attributeTableName = $entityCode . '_' . $backendType;
        $valueColumnConfig = $this->getValueColumnConfigByBackendType($backendType);

        $table = $setup->getConnection()
            ->newTable($setup->getTable($attributeTableName)
            )->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value Id'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute Id'
            )->addColumn(
                $idFieldName,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity Id'
            )->addColumn(
                $valueColumnConfig->getName(),
                $valueColumnConfig->getType(),
                $valueColumnConfig->getLength(),
                $valueColumnConfig->getOptions(),
                $valueColumnConfig->getComment()
            )->addIndex(
                $connection->getIndexName(
                    $attributeTableName,
                    [$idFieldName, 'attribute_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [$idFieldName, 'attribute_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $connection->getIndexName($attributeTableName, ['attribute_id']),
                ['attribute_id']
            )->addForeignKey(
                $connection->getForeignKeyName($attributeTableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $connection->getForeignKeyName($attributeTableName, $idFieldName, $entityCode, $idFieldName),
                $idFieldName,
                $setup->getTable($entityCode),
                $idFieldName,
                Table::ACTION_CASCADE
            )->setComment(
                sprintf('Entity %s Attributes', $backendType)
            );

        if ($valueColumnConfig->getType() != Table::TYPE_TEXT) {
            $table->addIndex(
                $connection->getIndexName($attributeTableName, [$idFieldName, 'attribute_id', 'value']),
                [$idFieldName, 'attribute_id', 'value']
            );
        }

        $setup->getConnection()->createTable($table);
    }

    /**
     * @param string $backendType
     * @return ColumnConfigInterface
     * @throws LocalizedException
     */
    protected function getValueColumnConfigByBackendType($backendType)
    {
        /** @var ColumnConfigInterface $columnConfig */
        $columnConfig = $this->columnConfigFactory->create();
        $columnConfig->setName('value')
            ->setComment('Value');
        switch ($backendType) {
            case 'varchar':
                $columnConfig->setType(Table::TYPE_TEXT)
                    ->setLength(255);
                break;
            case 'int':
                $columnConfig->setType(Table::TYPE_INTEGER);
                break;
            case 'text':
                $columnConfig->setType(Table::TYPE_TEXT)
                    ->setLength('64k');
                break;
            default:
                throw new LocalizedException(__(self::ATTRIBUTE_TYPE_DATA_CONFIG_NOT_FOUND_EXCEPTION, $backendType));
        }

        return $columnConfig;
    }

    protected function getEntityAttributeBackendTypes()
    {
        $entityCode = $this->getEntityCode();
        $entities = $this->getDefaultEntities();
        if (!isset($entities[$entityCode])) {
            throw new LocalizedException(__(self::MISSED_ENTITY_INSTALLATION_INFO_EXCEPTION));
        }

        $entityConfig = $this->getEntityTypeConfig();
        $attributeFactory = $this->objectManager->get($entityConfig->getAttributeFactoryClass());
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        $attribute = $attributeFactory->create();

        $attributeBackendTypes = [];
        /** @var AttributeConfigInterface $attributeConfig */
        foreach ($entityConfig->getAttributesConfigCollection() as $attributeConfig) {
            $attributeBackendTypes[] = $attribute->getBackendTypeByInput($attributeConfig->getFrontendInput()) ? $attribute->getBackendTypeByInput($attributeConfig->getFrontendInput()) : 'int';
        }
       
        return array_unique($attributeBackendTypes);
    }

    /**
     * @return EntityTypeConfigInterface
     */
    protected function getEntityTypeConfig()
    {
        return $this->entityConfig->getEntityTypeConfig($this->getEntityCode());
    }

    /**
     * Get PK field name.
     * Custom name should be used if you link the entity with other Magento entities
     * "entity_id" column name may be ambiguous in many cases, so we need to use some other name
     *
     * @return string
     */
    protected function getEntityIdField()
    {
        return $this->getEntityTypeConfig()->getEntityIdField();
    }
}

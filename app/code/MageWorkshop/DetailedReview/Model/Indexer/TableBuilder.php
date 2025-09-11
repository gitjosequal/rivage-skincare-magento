<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\Indexer;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\Entity\Attribute as AttributeModel;

/**
 * Table builder class for creating/refreshing flat tables
 * Builder has ability to insert swatch value columns, but this is not needed for now because
 * all swatches have the same value in all stores
 *
 * Class TableBuilder
 * @package MageWorkshop\DetailedReview\Model\Indexer
 */
class TableBuilder
{
    const FLAT_TABLE_SUFFIX = '_flat';

    const TEMP_INDEXER_TABLE_SUFFIX = '_tmp';

    const VALUE_COLUMN_SUFFIX = '_value';

    const SWATCH_VALUE_COLUMN_SUFFIX = '_swatch';

    const DEFAULT_TEXT_SIZE = 65536;

    const DEFAULT_INTEGER_SIZE = 11;

    const EXTRA_ENTITY_COLUMNS = [
        'created_at',
        'updated_at'
    ];

    /** @var array $tablesRegistry */
    protected $tablesRegistry = [];

    /** @var \Magento\Framework\App\ResourceConnection $resourceConnection */
    protected $resourceConnection;

    /** @var \Magento\Swatches\Helper\Data $swatchHelper */
    protected $swatchHelper;

    protected $storeId = 0;

    /**
     * param \Magento\Catalog\Helper\Product\Flat\Indexer $productIndexerHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Swatches\Helper\Data $swatchHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * Create flat table and refresh it's structure by adding columns or removing columns that are not needed any more.
     * This method has a small overhead because main entity table is copied and then some columns are added/deleted,
     * but the whole table structure and foreign keys remain identical
     *
     * @param \Magento\Eav\Model\ResourceModel\Attribute\Collection $attributeCollection
     * @param string $entityTable
     * @param bool $isTemporaryTable
     * @return string
     * @throws \DomainException
     * @throws \Zend_Db_Exception
     */
    public function refreshFlatTableSchema(
        \Magento\Eav\Model\ResourceModel\Attribute\Collection $attributeCollection,
        $entityTable,
        $isTemporaryTable = false
    ) {
        $this->getConnection()->disallowDdlCache();
        $entityTableName = $this->resourceConnection->getTableName($entityTable);

        $newTable = $isTemporaryTable
            ? $this->getTemporaryTable($entityTable)
            : $this->getFlatTable($entityTable);
        $newTableName = $this->resourceConnection->getTableName($newTable);

        if (isset($this->tablesRegistry[$newTableName])) {
            $this->getConnection()->allowDdlCache();
            return $newTableName;
        }

        // 1. Create table is not exists
        // The same functionality should be used to create temp table if it does not exist
        $this->createTableIfNotExists($entityTableName, $newTableName, $isTemporaryTable);

        // 2. drop old columns in case if attribute does not exist any more
        $attributes = $attributeCollection->getItems();
        $this->dropObsoleteColumns($attributes, $newTableName, $isTemporaryTable, $entityTableName);

        // 3. add new columns and column values
        $this->addAttributeColumns($attributes, $newTableName);

        $this->tablesRegistry[$newTableName] = true;
        $this->getConnection()->allowDdlCache();
        $this->getConnection()->resetDdlCache($newTableName);

        return $newTableName;
    }

    /**
     * @param string $entityTableName
     * @param string $newTableName
     * @param bool $isTemporaryTable
     * @return $this
     * @throws \DomainException
     * @throws \Zend_Db_Exception
     */
    protected function createTableIfNotExists($entityTableName, $newTableName, $isTemporaryTable)
    {
        $connection = $this->getConnection();
        if (!$connection->isTableExists($newTableName)) {
            $table = $this->getFlatTableBaseStructure($entityTableName, $newTableName);
            if (!$isTemporaryTable) {
                $connection->createTable($table);
                $this->addForeignKeys($entityTableName, $newTableName);
            } else {
                $connection->createTemporaryTable($table);
            }
        }
        return $this;
    }

    /**
     * @param string $entityTableName
     * @param string $newTableName
     * @return bool|Table
     * @throws \Zend_Db_Exception
     */
    protected function getFlatTableBaseStructure($entityTableName, $newTableName)
    {
        $connection = $this->getConnection();
        $describe = $connection->describeTable($entityTableName);
        $table = $connection->newTable($newTableName)
            ->setComment('Aggregated Flat Table');

        foreach ($describe as $columnData) {
            if (!in_array($columnData['COLUMN_NAME'], self::EXTRA_ENTITY_COLUMNS)) {
                $columnInfo = $connection->getColumnCreateByDescribe($columnData);

                $table->addColumn(
                    $columnInfo['name'],
                    $columnInfo['type'],
                    $columnInfo['length'],
                    $columnInfo['options'],
                    $columnInfo['comment']
                );
            }
        }

        $indexes = $connection->getIndexList($entityTableName);
        foreach ($indexes as $indexData) {
            /**
             * Do not create primary index - it is created with identity column.
             * For reliability check both name and type, because these values can start to differ in future.
             */
            if (
                ($indexData['KEY_NAME'] == 'PRIMARY')
                || ($indexData['INDEX_TYPE'] == AdapterInterface::INDEX_TYPE_PRIMARY)
            ) {
                continue;
            }

            $fields = $indexData['COLUMNS_LIST'];
            $options = ['type' => $indexData['INDEX_TYPE']];
            $table->addIndex($connection->getIndexName($newTableName, $fields, $indexData['INDEX_TYPE']),
                $fields, $options);
        }

        // Set additional options
        $tableData = $connection->showTableStatus($entityTableName);
        $table->setOption('type', $tableData['Engine']);
        return $table;
    }

    /**
     * @param string $entityTableName
     * @param string $newTableName
     * @return $this
     */
    public function addForeignKeys($entityTableName, $newTableName)
    {
        $connection = $this->getConnection();
        $foreignKeys = $connection->getForeignKeys($entityTableName);
        foreach ($foreignKeys as $keyData) {
            $fkName = $connection->getForeignKeyName(
                $newTableName,
                $keyData['COLUMN_NAME'],
                $keyData['REF_TABLE_NAME'],
                $keyData['REF_COLUMN_NAME']
            );

            $connection->addForeignKey(
                $fkName,
                $newTableName,
                $keyData['COLUMN_NAME'],
                $keyData['REF_TABLE_NAME'],
                $keyData['REF_COLUMN_NAME']
            );
        }
        return $this;
    }

    /**
     * @param array $attributes
     * @param string $tableName
     * @param bool $isTemporaryTable
     * @param string $entityTableName
     * @return $this
     * @throws \DomainException
     */
    protected function dropObsoleteColumns($attributes, $tableName, $isTemporaryTable = false, $entityTableName = '')
    {
        $columnsToDrop = $this->getNormalizedTableDescription($tableName);

        /** @var AttributeModel $attribute */
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            // no need to drop id column structure is without changes
            if (isset($columnsToDrop[$attributeCode])) {
                $currentDefinition = $columnsToDrop[$attributeCode];
                $columnDefinition = $this->getFlatColumnsDdlDefinition($attribute);

                if (
                    ($currentDefinition['DATA_TYPE'] == $columnDefinition['type'])
                    && ($currentDefinition['LENGTH'] == $columnDefinition['length'])
                ) {
                    unset($columnsToDrop[$attributeCode]);
                }

//                if ($attribute->usesSource() && ($attribute->getFrontendInput() !== 'multiselect')) {
//                    unset($columnsToDrop[$this->getValueColumnName($attributeCode)]);
//                }

//                if ($this->swatchHelper->isSwatchAttribute($attribute)) {
//                    unset($columnsToDrop[$this->getSwatchValueColumnName($attributeCode)]);
//                }
            }
        }

        foreach ($columnsToDrop as $columnName => $definition) {
            if ($definition['PRIMARY']) {
                unset($columnsToDrop[$columnName]);
            }
        }

        // Temp memory tables do not have foreign keys
        $connection = $this->getConnection();
        $foreignKeys = $isTemporaryTable
            ? $connection->getForeignKeys($entityTableName)
            : $connection->getForeignKeys($tableName);

        foreach ($foreignKeys as $definition) {
            unset($columnsToDrop[$definition['COLUMN_NAME']]);
        }

        if (!empty($columnsToDrop)) {
            foreach ($columnsToDrop as $columnName => $definition) {
                $connection->dropColumn($tableName, $columnName);
            }
        }
        return $this;
    }

    /**
     * @param array $attributes
     * @param string $tableName
     */
    protected function addAttributeColumns($attributes, $tableName)
    {
        $connection = $this->getConnection();
        $description = $connection->describeTable($tableName);
        /** @var AttributeModel $attribute */
        foreach ($attributes as $attribute) {
            if (!$attribute->getIsVisibleOnFront() || ($attribute->getBackendType() === 'static')) {
                continue;
            }

            $attributeCode = $attribute->getAttributeCode();
//            $valueColumnName = $this->getValueColumnName($attributeCode);
//            $swatchValueColumnName = $this->getSwatchValueColumnName($attributeCode);

            if (!isset($description[$attributeCode])) {
                $columnDefinition = $this->getFlatColumnsDdlDefinition($attribute);
                $columnDefinition['comment'] = $attributeCode;
                $connection->addColumn(
                    $tableName,
                    $attributeCode,
                    $columnDefinition
                );
            }
            // Attribute option values are not inserted into the flat table. Most likely we'll have to
            // create flat table per each attribute and insert values there
            // but this will be important only fo filterable / searchable attributes if they are ever added
            /*
            if ($attribute->usesSource() && !isset($description[$valueColumnName])) {
                $type = ($attribute->getFrontendInput() == 'select') ? Table::TYPE_TEXT : Table::TYPE_INTEGER;
                $connection->addColumn(
                    $tableName,
                    $valueColumnName,
                    [
                        'type'    => $type,
                        'comment' => $attribute->getName() . ' Value'
                    ]
                );
            }
            */

//            if ($this->swatchHelper->isSwatchAttribute($attribute) && !isset($description[$swatchValueColumnName])) {
//                $connection->addColumn(
//                    $tableName,
//                    $swatchValueColumnName,
//                    [
//                        'type'    => Table::TYPE_TEXT,
//                        'comment' => $attribute->getName() . ' Swatch Value'
//                    ]
//                );
//            }
        }
    }

    /**
     * @param AttributeModel $attribute
     * @return array
     */
    protected function getFlatColumnsDdlDefinition(AttributeModel $attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        $columnDefinition = $attribute->_getFlatColumnsDdlDefinition();
        $columnDefinition = $columnDefinition[$attributeCode];

        if (isset($columnDefinition['length']) && ($columnDefinition['length'] > self::DEFAULT_TEXT_SIZE)) {
            $columnDefinition['length'] = self::DEFAULT_TEXT_SIZE;
        }

        /*
        if (
            ($columnDefinition['type'] == Table::TYPE_TEXT)
            && isset($columnDefinition['length'])
            && ($columnDefinition['length'] <= 255)
        ) {
            $columnDefinition['type'] = 'varchar';
        }
        */

        if (!isset($columnDefinition['length']) && ($columnDefinition['type'] === Table::TYPE_INTEGER)) {
            $columnDefinition['length'] = self::DEFAULT_INTEGER_SIZE;
        }

        return $columnDefinition;
    }

    /**
     * @param string $tableName
     * @return array
     * @throws \DomainException
     */
    protected function getNormalizedTableDescription($tableName)
    {
        $description = $this->getConnection()->describeTable($tableName);
        // normalize data
        foreach ($description as &$columnDefinition) {
            switch ($columnDefinition['DATA_TYPE']) {
                case Table::TYPE_TEXT:
                    $columnDefinition['LENGTH'] = self::DEFAULT_TEXT_SIZE;
                    break;
                case 'int':
                    $columnDefinition['DATA_TYPE'] = Table::TYPE_INTEGER;
                    $columnDefinition['LENGTH'] = self::DEFAULT_INTEGER_SIZE;
                    break;
                case 'varchar':
                    $columnDefinition['DATA_TYPE'] = Table::TYPE_TEXT;
                    break;
            }
        }
        return $description;
    }

    /**
     * @param string $entityTableName
     * @return string
     */
    public function getFlatTable($entityTableName)
    {
        return $entityTableName . self::FLAT_TABLE_SUFFIX;
    }

    /**
     * @param string $entityTableName
     * @return string
     */
    public function getTemporaryTable($entityTableName)
    {
        return $this->getFlatTable($entityTableName) . self::TEMP_INDEXER_TABLE_SUFFIX;
    }

    /**
     * @param string $columnName
     * @return string
     */
    protected function getValueColumnName($columnName)
    {
        return $columnName . self::VALUE_COLUMN_SUFFIX;
    }

    /**
     * @param string $columnName
     * @return bool
     */
    protected function getSwatchValueColumnName($columnName)
    {
        return $columnName . self::SWATCH_VALUE_COLUMN_SUFFIX;
    }

    /**
     * @return AdapterInterface
     * @throws \DomainException
     */
    protected function getConnection()
    {
        return $this->resourceConnection->getConnection();
    }
}

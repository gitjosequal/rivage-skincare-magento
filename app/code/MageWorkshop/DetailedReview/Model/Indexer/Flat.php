<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\Indexer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\StateInterface;
use MageWorkshop\DetailedReview\Model\Details;
use Magento\Review\Model\Review;
use MageWorkshop\DetailedReview\Model\Indexer\Eav\Processor as EavProcessor;
use Magento\Indexer\Model\Indexer;
use MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor;

/**
 * Class AbstractFlatIndexer
 * Note: for now this flat indexer does not take care about the deleted data as we assume that main entity table
 * contains a foreign key that refers to the entity we want to extend. That is why update/delete operation will be done
 * in the cascade mode - TableBuilder copies all indexes and foreign keys for us
 *
 * @package MageWorkshop\DetailedReview\Model\Indexer
 */
class Flat extends \MageWorkshop\DetailedReview\Model\Indexer\AbstractIndexer
{
    const CHECK_STATE_IN_DATABASE = 'check_state_in_database';

    /**
     * @var bool $isTemporaryTable
     */
    protected $isTemporaryTable = true;

    /**
     * @var string
     */
    protected $entityCode = Details::ENTITY;

    /**
     * @var \MageWorkshop\DetailedReview\Model\ResourceModel\Details\CollectionFactory $collectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Swatches\Helper\Data $swatchHelper
     */
    private $swatchHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection
     */
    private $connection;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory $indexerFactory
     */
    private $indexerFactory;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * AbstractFlatIndexer constructor.
     * @param \Monolog\Logger $logger
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \MageWorkshop\DetailedReview\Helper\AbstractAttribute $attributeHelper
     * @param \MageWorkshop\DetailedReview\Model\Indexer\TableBuilder $tableBuilder
     * @param \MageWorkshop\DetailedReview\Model\ResourceModel\Details\CollectionFactory $collectionFactory
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Monolog\Logger $logger,
        \Magento\Eav\Model\Config $eavConfig,
        \MageWorkshop\DetailedReview\Helper\AbstractAttribute $attributeHelper,
        \MageWorkshop\DetailedReview\Model\Indexer\TableBuilder $tableBuilder,
        \MageWorkshop\DetailedReview\Model\ResourceModel\Details\CollectionFactory $collectionFactory,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Framework\Registry $registry
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->swatchHelper = $swatchHelper;
        $this->resourceConnection = $resourceConnection;
        $this->connection = $this->resourceConnection->getConnection();
        $this->indexerFactory = $indexerFactory;
        $this->indexerRegistry = $indexerRegistry;
        $this->registry = $registry;
        parent::__construct($logger, $eavConfig, $attributeHelper, $tableBuilder);
    }

    /**
     * @param null|array $changedIds
     * @return $this|void
     * @throws \InvalidArgumentException
     * @throws LocalizedException
     * @throws \Zend_Db_Exception
     */
    protected function reindex($changedIds = null)
    {
        $this->logger->addDebug('Flat reindex started');

        /** @var Indexer $eavIndexer */
        $eavIndexer = $this->indexerRegistry->get(EavProcessor::INDEXER_ID);

        if (!$eavIndexer->isValid() &&
            ($this->registry->registry(self::CHECK_STATE_IN_DATABASE) || $this->registry->registry('setup-mode-enabled'))
        ) {
            // Need to take data from the DB, not from the registry, because we can incorrect state
            // during installation with sample data when ConfigurableSampleData executes reindex
            $eavIndexer = $this->indexerFactory->create();
            $eavIndexer->load(EavProcessor::INDEXER_ID);
        }

        if (!$eavIndexer->isValid()) {
            $this->logger->addDebug('Eav indexer is either invalid or working. Exiting for now.');

            if (PHP_SAPI === 'cli') {
                throw new LocalizedException(__('Eav indexer is either invalid or working. Exiting for now.'));
            }

            return;
        }

        // $changedIds = [1];
        $temporaryTableName = $this->tableBuilder->getTemporaryTable($this->getEntityCode());
        $temporaryTableName = $this->resourceConnection->getTableName($temporaryTableName);
        $this->connection->resetDdlCache($temporaryTableName);

        $collection = $this->getDetailsCollection();
        $idFieldName = $collection->getIdFieldName();

        $this->removeNotApprovedReviews($idFieldName);

        $columns = array_intersect(
            array_keys($this->connection->describeTable($temporaryTableName)),
            array_keys($this->connection->describeTable($collection->getMainTable()))
        );

        $collection->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns($columns);
        $collection->addReviewStatusFilter(Review::STATUS_APPROVED);

        if (null !== $changedIds) {
            $collection->addAttributeToFilter('review_id', ['in' => $changedIds]);
        }

        // Insert entity columns data (incl. static attributes)
        $this->insert($collection->getSelect(), $temporaryTableName, $columns);

        // Copy all IDs to the temporary table because we can not use temporary table as a source and insert target at the same time:
        // http://dev.mysql.com/doc/refman/5.7/en/temporary-table-problems.html
        $idsTemporaryTableName = $this->moveIdsToTemporaryTable($temporaryTableName, $idFieldName);

        // Populate temporary table with attributes data
        /** @var \MageWorkshop\DetailedReview\Model\Attribute $attribute */
        foreach ($this->attributeHelper->getIndexableDynamicAttributeCollection() as $attribute) {
            $fieldsToInsert = [
                $idFieldName,
                $attribute->getAttributeCode()
            ];
            // currently custom backend tables are not supported, so you're probably a lucky one if this works
            $select = $this->connection->select();
            $select->from(
                ['e' => $attribute->getBackendTable()],
                [
                    $idFieldName => "e.$idFieldName",
                    $attribute->getAttributeCode() => 'e.value'
                ]
            )
                // no need to filter by $changedIds because we can just use JOIN here
                ->join(
                    ['ids' => $idsTemporaryTableName],
                    "e.$idFieldName = ids.$idFieldName",
                    []
                )
                ->where('e.attribute_id = ?', (int) $attribute->getId());

            // Attribute option values are not inserted into the flat table. Most likely we'll have to
            // create flat table per each attribute and insert values there
            // but this will be important only fo filterable / searchable attributes if they are ever added
            /*
            if ($attribute->usesSource() && ($attribute->getFrontendInput() !== 'multiselect')) {
                $fieldsToInsert[] = "{$attribute->getAttributeCode()}_value";
                $optionValueTable = $this->swatchHelper->isSwatchAttribute($attribute)
                    ? 'eav_attribute_option_swatch'
                    : 'eav_attribute_option_value';
                $select->join(
                    ['aov' => $collection->getTable($optionValueTable)],
                    'aov.option_id = e.value',
                    ["{$attribute->getAttributeCode()}_value" => 'value']
                );
            }
            */
            $this->insert($select, $temporaryTableName, $fieldsToInsert);
        }

        $this->connection->dropTable($idsTemporaryTableName);

        $flatTableName = $this->resourceConnection->getTableName(
            $this->tableBuilder->getFlatTable($this->getEntityCode())
        );

        $select = $this->connection->select();
        $select->from($temporaryTableName);
        $this->logger->addDebug('Ready to insert data');

        // Need to pass columns to preserve the fields order in the insert query. Otherwise if the
        // columns order in the flat and temporary table do not match the data will be inserted in the wrong columns
        $this->insert(
            $select,
            $flatTableName,
            array_keys($this->connection->describeTable($temporaryTableName))
        );
        $this->connection->delete($temporaryTableName);
        $this->logger->addDebug('finished writing data');

        if (!empty($changedIds)) {
            $flatIndexer = $this->indexerFactory->create();
            $flatIndexer->load(Processor::INDEXER_ID);

            if (!$flatIndexer->isValid()) {
                /** @var \Magento\Framework\Indexer\StateInterface $state */
                $state = $flatIndexer->getState();
                $state->setStatus(StateInterface::STATUS_VALID);
                $state->save();
            }
        }
    }

    /**
     * Remove all non-approved reviews from the flat table to decrease the general table size.
     * There is no need to analyze data during this step
     *
     * @param $idFieldName
     * @return $this
     */
    protected function removeNotApprovedReviews($idFieldName)
    {
        $flatTable = $this->tableBuilder->getFlatTable($this->getEntityCode());
        $select = $this->connection->select();
        $select->from(
            ['e' => $this->resourceConnection->getTableName($flatTable)],
            [$idFieldName, 'review_id']
        )
            ->join(
                ['r' => $this->resourceConnection->getTableName('review')],
                'e.review_id = r.review_id',
                []
            )
            ->where('r.status_id != ?', Review::STATUS_APPROVED);
        // $sql = (string) $select->deleteFromSelect('e');
        $this->connection->query($select->deleteFromSelect('e'));
        return $this;
    }

    /**
     * Copy all IDs to the temporary table because we can not use temporary table as a source and insert target
     * at the same time: http://dev.mysql.com/doc/refman/5.7/en/temporary-table-problems.html
     *
     * @param string $temporaryTableName
     * @param string $idFieldName
     * @return string
     * @throws \Zend_Db_Exception
     */
    protected function moveIdsToTemporaryTable($temporaryTableName, $idFieldName)
    {
        $idsTemporaryTableName = $temporaryTableName . '_ids';
        $table = $this->connection->newTable($idsTemporaryTableName);

        foreach ($this->connection->describeTable($temporaryTableName) as $description) {
            if ($description['PRIMARY']) {
                $columnInfo = $this->connection->getColumnCreateByDescribe($description);
                $table->addColumn(
                    $columnInfo['name'],
                    $columnInfo['type'],
                    $columnInfo['length'],
                    $columnInfo['options'],
                    $columnInfo['comment']
                );
                $this->connection->createTemporaryTable($table);
                break;
            }
        }

        $select = $this->connection->select();
        $select->from(['e' => $temporaryTableName], [$idFieldName]);
        $this->insert($select, $idsTemporaryTableName);
        return $idsTemporaryTableName;
    }

    protected function getDetailsCollection()
    {
        /** @var \MageWorkshop\DetailedReview\Model\ResourceModel\Details\Collection $collection */
        return $this->collectionFactory->create();
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param string $table
     * @param array $columns
     * @return $this
     */
    protected function insert(\Magento\Framework\DB\Select $select, $table, $columns = [])
    {
        $this->connection->query($select->insertFromSelect($table, $columns));
        return $this;
    }
}

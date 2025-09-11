<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\Indexer;

use MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\Collection as AttributeCollection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractEavIndexer
 * Refresh flat table structure. There is no need to care about attributes because unchanged attribute columns
 * won't be modified by the TableBuilder
 *
 * @package MageWorkshop\DetailedReview\Model\Indexer
 */
abstract class AbstractIndexer
    implements \Magento\Framework\Indexer\ActionInterface,
               \Magento\Framework\Mview\ActionInterface
{
    const FULL_REINDEX_NOT_AVAILABLE_EXCEPTION = 'Unable to execute full reindex because $1 is not installed';

    protected $entityCode = '';

    /** @var \Monolog\Logger $logger */
    protected $logger;

    /** @var \MageWorkshop\DetailedReview\Helper\AbstractAttribute $attributeHelper */
    protected $attributeHelper;

    /** @var \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory */
    protected $eavTypeFactory;

    /** @var \MageWorkshop\DetailedReview\Model\Indexer\TableBuilder $tableBuilder */
    protected $tableBuilder;

    /** @var int $entityTypeId */
    protected $entityTypeId;

    /** @var \Magento\Eav\Model\Config $eavConfig */
    protected $eavConfig;

    /** @var bool $isTemporaryTable */
    protected $isTemporaryTable = false;

    /**
     * @param \Monolog\Logger $logger
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \MageWorkshop\DetailedReview\Helper\AbstractAttribute $attributeHelper
     * @param \MageWorkshop\DetailedReview\Model\Indexer\TableBuilder $tableBuilder
     */
    public function __construct(
        \Monolog\Logger $logger,
        \Magento\Eav\Model\Config $eavConfig,
        \MageWorkshop\DetailedReview\Helper\AbstractAttribute $attributeHelper,
        \MageWorkshop\DetailedReview\Model\Indexer\TableBuilder $tableBuilder
    ) {
        $this->logger = $logger;
        $this->eavConfig = $eavConfig;
        $attributeHelper->setEntityCode($this->getEntityCode());
        $this->attributeHelper = $attributeHelper;
        $this->tableBuilder = $tableBuilder;
    }

    /**
     * @param int $id
     * @throws \Zend_Db_Exception
     */
    public function executeRow($id)
    {
        $this->logger->addDebug('Execute row: ' . $id);
        $this->refreshFlatTableSchema();
        $this->reindex([$id]);
    }

    /**
     * Full reindex is called while installing ConfigurableSampleData, so we need to skip reindex at this moment
     * Our module's data can be not installed yet
     * @throws \Zend_Db_Exception
     */
    public function executeFull()
    {
        try {
            $this->eavConfig->getEntityType($this->getEntityCode());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->addWarning(__(self::FULL_REINDEX_NOT_AVAILABLE_EXCEPTION, $this->getEntityCode()));
            return;
        }

        $this->logger->addDebug('Execute full');
        $this->refreshFlatTableSchema();
        $this->reindex();
    }

    /**
     * @param \int[] $ids
     * @throws \Zend_Db_Exception
     */
    public function executeList(array $ids)
    {
        $this->logger->addDebug('Execute list: ' . implode($ids, ','));
        $this->refreshFlatTableSchema();
        $this->reindex($ids);
    }

    /**
     * @param \int[] $ids
     * @throws \Zend_Db_Exception
     */
    public function execute($ids)
    {
        $this->logger->addDebug('Execute mview: ' . implode(', ', $ids));
        $this->refreshFlatTableSchema();
        $this->reindex((array) $ids);
    }

    /**
     * Process data reindex if needed
     * @param array $changedIds
     * @return $this
     */
    protected function reindex($changedIds = null)
    {
        return $this;
    }

    /**
     * @return string
     */
    protected function getEntityCode()
    {
        return $this->entityCode;
    }

    /**
     * Get the list of attributes to rebuild the index for
     *
     * @return AttributeCollection
     * @throws LocalizedException
     */
    protected function getAttributesCollection()
    {
        /** @var AttributeCollection $collection */
        $collection = $this->attributeHelper->getIndexableDynamicAttributeCollection();
//        if (!empty($ids)) {
//            $collection->addFieldToFilter('attribute_id', ['in' => $ids]);
//        }
        return $collection;
    }

    /**
     * @return string
     * @throws \Zend_Db_Exception
     */
    protected function refreshFlatTableSchema()
    {
        return $this->tableBuilder->refreshFlatTableSchema(
            $this->getAttributesCollection(),
            $this->getEntityCode(),
            $this->isTemporaryTable
        );
    }
}

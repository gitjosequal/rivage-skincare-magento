<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model;

/**
 * Class Details
 * @package MageWorkshop\DetailedReview\Model
 *
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt($updatedAt)
 */
class Details extends \Magento\Framework\Model\AbstractModel
{
    const ENTITY = 'mageworkshop_detailedreview_details_entity';

    /**
     * Prefix of model events
     *
     * @var string
     */
    protected $_eventPrefix = self::ENTITY;

    /**
     * Name of event object
     *
     * @var string
     */
    protected $_eventObject = self::ENTITY;

    /** @var Indexer\Flat\Processor $detailsFlatProcessor */
    protected $detailsFlatProcessor;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorkshop\DetailedReview\Model\Indexer\Flat\Processor $detailsFlatProcessor,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->detailsFlatProcessor = $detailsFlatProcessor;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(\MageWorkshop\DetailedReview\Model\ResourceModel\Details::class);
        parent::_construct();
    }

    public function afterSave()
    {
        $result = parent::afterSave();
        $this->_getResource()->addCommitCallback([$this, 'reindex']);
        return $result;
    }

    /**
     * Init indexing process after catalog eav attribute delete commit
     *
     * @return $this
     */
    public function afterDeleteCommit()
    {
        parent::afterDeleteCommit();
        $this->reindex();
        return $this;
    }

    /**
     * Init indexing process after product save
     *
     * @return void
     */
    public function reindex()
    {
        $indexer = $this->detailsFlatProcessor->getIndexer();
        if (!$indexer->isScheduled()) {
            $indexer->reindexRow($this->getId());
        }
    }

    /**
     * Load review details by ID. Please, be careful with observers, because we're not loading
     * the data by primary key. You may need to check this after load.
     *
     * @param $reviewId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByReviewId($reviewId)
    {
        $this->_getResource()->loadByReviewId($this, $reviewId);
        return $this;
    }
}

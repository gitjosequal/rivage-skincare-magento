<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Attribute;

use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;
use MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Backend\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

class Grid extends AbstractGrid
{
    const EVENT_PREFIX = 'mageworkshop_detailedreview_attribute';

    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    protected $_module = 'mageworkshop_detailedreview';

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Prepare Review Fields grid collection object
     *
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var \MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(AbstractAttribute::BACKEND_TYPE, ['neq' => AbstractAttribute::TYPE_STATIC]);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare Review Fields grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
//        $this->addColumn(
//            'is_searchable',
//            [
//                'header' => __('Searchable'),
//                'sortable' => true,
//                'index' => 'is_searchable',
//                'type' => 'options',
//                'options' => ['1' => __('Yes'), '0' => __('No')],
//                'align' => 'center'
//            ],
//            'is_user_defined'
//        );
        $this->_eventManager->dispatch(self::EVENT_PREFIX . '_grid_build', ['grid' => $this]);
        return $this;
    }
}

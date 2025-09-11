<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Form;

//use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;
//use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Set;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    const EVENT_PREFIX = 'mageworkshop_detailedreview_form';

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory */
    protected $collectionFactory;

    protected $module = 'mageworkshop_detailedreview';

    /** @var \MageWorkshop\DetailedReview\Model\AttributeFactory $attributeFactory */
    protected $attributeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory
     * @param \MageWorkshop\DetailedReview\Model\AttributeFactory $attributeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory,
        \MageWorkshop\DetailedReview\Model\AttributeFactory $attributeFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->attributeFactory = $attributeFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('formGrid');
        $this->setDefaultSort(Set::KEY_ATTRIBUTE_SET_ID);
        $this->setDefaultDir('ASC');
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $collection */
        $collection = $this->collectionFactory->create();
        /** @var \MageWorkshop\DetailedReview\Model\Attribute $attribute */
        $attribute = $this->attributeFactory->create();
        $collection->setEntityTypeFilter($attribute->getEntityTypeId());
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

        $this->addColumn(
            Set::KEY_ATTRIBUTE_SET_NAME,
            [
                'header'           => __('Form Name'),
                'sortable'         => true,
                'index'            => Set::KEY_ATTRIBUTE_SET_NAME,
                'header_css_class' => 'col-form-name',
                'column_css_class' => 'col-form-name'
            ]
        );

        $this->_eventManager->dispatch(self::EVENT_PREFIX . '_grid_build', ['grid' => $this]);
        return $this;
    }

    /**
     * Return url of given row
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl($this->module . '/*/edit', ['form_id' => $row->getAttributeSetId()]);
    }
}

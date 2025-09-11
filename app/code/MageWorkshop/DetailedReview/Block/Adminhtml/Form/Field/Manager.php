<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Form\Field;

use MageWorkshop\DetailedReview\Helper\Attribute;

class Manager
    extends \Magento\Backend\Block\Template
    implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /** @var \Magento\Framework\Data\Form\Element\AbstractElement $element */
    protected $element;

    /** @var \MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\CollectionFactory $attributeCollectionFactory */
    protected $attributeCollectionFactory;

    /** @var \Magento\Framework\Json\Helper\Data $jsonHelper */
    protected $jsonHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->element = $element;
        return $this->toHtml();
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\Set;
     */
    protected function getAttributeSet()
    {
        return $this->element->getValue();
    }

    public function getCurrentFieldsConfigurationJson()
    {
        /** @var \MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeSet = $this->getAttributeSet();

        if ($attributeSet && ($attributeSetId = $attributeSet->getAttributeSetId())) {
            $attributeCollection->addAttributeSetInfo($attributeSetId);
            $attributeCollection->setOrder('sort_order', $attributeCollection::SORT_ORDER_ASC);
        }
        $attributeCollection->addFieldToFilter('is_visible_on_front', 1);
        $attributeCollection->setOrder('attribute_code', $attributeCollection::SORT_ORDER_ASC);

        $attributesInfo = [
            Attribute::INCLUDED_FIELDS  => [
                'title' => __('Included Fields'),
                'id'    => Attribute::INCLUDED_FIELDS,
                'items' => []
            ],
            Attribute::AVAILABLE_FIELDS => [
                'title' => __('Available Fields'),
                'id'    => Attribute::AVAILABLE_FIELDS,
                'items' => []
            ]
        ];

        /** @var \MageWorkshop\DetailedReview\Model\Attribute $attribute */
        foreach ($attributeCollection as $attribute) {
            $data = [
                'label' => $attribute->getDefaultFrontendLabel(),
                'code'  => $attribute->getAttributeCode(),
                'id'    => $attribute->getId(),
                'class' => !$attribute->getIsUserDefined() ? 'ui-state-disabled' : ''
            ];

            // System attributes should always be included in the form - they are the placeholders for the default fields
            $list = ((bool) $attribute->getAttributeSetId() || !$attribute->getIsUserDefined())
                ? Attribute::INCLUDED_FIELDS
                : Attribute::AVAILABLE_FIELDS;
            $attributesInfo[$list]['items'][] = $data;
        }

        return $this->jsonHelper->jsonEncode($attributesInfo);
    }
}

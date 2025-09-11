<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;
use MageWorkshop\DetailedReview\Model\Details;
use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigInterface;
use MageWorkshop\DetailedReview\Api\Data\EntityConfigInterface;
use Magento\Framework\DataObject;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Eav\Helper\Data;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Main extends AbstractMain
{
    /** @var EntityConfigInterface $entityConfig */
    protected $entityConfig;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $eavData,
        YesnoFactory $yesnoFactory,
        InputtypeFactory $inputTypeFactory,
        PropertyLocker $propertyLocker,
        EntityConfigInterface $entityConfig,
        array $data
    ) {
        $this->entityConfig = $entityConfig;
        parent::__construct($context, $registry, $formFactory, $eavData, $yesnoFactory, $inputTypeFactory, $propertyLocker, $data);
    }

    /**
     * Adding product form elements for editing attribute
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeObject */
//        $attributeObject = $this->getAttributeObject();
        /* @var \Magento\Framework\Data\Form $form */
        $form = $this->getForm();
        /* @var \Magento\Framework\Data\Form\Element\Fieldset $fieldSet */
        $fieldSet = $form->getElement('base_fieldset');
        $fieldSet->setData('legend', __('Field Properties'));

        // 1. Remove unnecessary attributes from the list
        $fieldsToRemove = [
            'attribute_code',
            'is_unique',
            'frontend_class'
        ];
        /** @var \Magento\Framework\Data\Form\Element\AbstractElement $element */
        foreach ($fieldSet->getElements() as $element) {
            if (substr($element->getId(), 0, strlen('default_value')) == 'default_value') {
                $fieldsToRemove[] = $element->getId();
            }
        }
        foreach ($fieldsToRemove as $id) {
            $fieldSet->removeField($id);
        }

        // 2. Collect frontend input types and reset default values
        // Any additional field types should be define in the "mageworkshop_eav_attributes.xml" file of the module
        /** @var \Magento\Framework\Data\Form\Element\Select $frontendInputElement */
        $frontendInputElement = $form->getElement('frontend_input');
        $entityConfig = $this->entityConfig->getEntityTypeConfig(Details::ENTITY);
        $attributeTypes = [];
        /** @var AttributeConfigInterface $attributeConfig */
        foreach ($entityConfig->getAttributesConfigCollection() as $attributeConfig) {
            $attributeTypes[] = [
                'value' => $attributeConfig->getFrontendInput(),
                'label' => __($attributeConfig->getLabel())
            ];
        }
        $frontendInputElement->setData('values', $attributeTypes);
        /** @var \Magento\Framework\Phrase $frontendInputElementLabel */
        if ($frontendInputElementLabel = $frontendInputElement->getData('label')) {
            $newLabel = new \Magento\Framework\Phrase(__('Input Type'), $frontendInputElementLabel->getArguments());
            $frontendInputElement->setData('label', $newLabel);
        }

        // 3. Get additional
        $response = new DataObject;
        $response->setData('types', []);
        $this->_eventManager->dispatch('adminhtml_review_attribute_types', ['response' => $response]);
        $_hiddenFields = [];
        foreach ($response->getData('types') as $type) {
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
        }
        $this->_coreRegistry->register('attribute_type_hidden_fields', $_hiddenFields);
        $this->_eventManager->dispatch('review_attribute_form_build_main_tab', ['form' => $form]);

        return $this;
    }
}

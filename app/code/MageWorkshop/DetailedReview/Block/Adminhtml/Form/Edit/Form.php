<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Form\Edit;

use MageWorkshop\DetailedReview\Controller\Adminhtml\AbstractForm;
use Magento\Eav\Model\Entity\Attribute\Set;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id'     => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
        ]);

        $attributeSetObject = $this->getAttributeSetObject();
        $fieldSet = $form->addFieldset('base_fieldset', ['legend' => __('Form Configuration')]);

        if ($attributeSetObject && $attributeSetObject->getAttributeSetId()) {
            $fieldSet->addField('attribute_set_id', 'hidden', ['name' => 'attribute_id']);
        }

        $this->_addElementTypes($fieldSet);

        $validateClass = sprintf(
            'validate-alphanum-with-space validate-length maximum-length-%d',
            \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );
        $fieldSet->addField(
            Set::KEY_ATTRIBUTE_SET_NAME,
            'text',
            [
                'name'  => Set::KEY_ATTRIBUTE_SET_NAME,
                'label' => __('Form Name'),
                'title' => __('Form Name'),
                'note'  => __(
                    'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                    \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'class'    => $validateClass,
                'required' => true,
                'value'    => $attributeSetObject ? $attributeSetObject->getAttributeSetName() : ''
            ]
        );

        $fieldSet = $form->addFieldset('form_fields_manager', [
            'legend' => __('Form Fields'),
            'attribute_set'   => $attributeSetObject
        ]);

        $element = $fieldSet->addField(
            'form_fields',
            'text',
            [
                'name'  => Set::KEY_ATTRIBUTE_SET_NAME,
                'label' => __('Form Fields'),
                'title' => __('Form Fields'),
                'required' => true,
                'value'    => $attributeSetObject

            ]
        );

        /** @var \MageWorkshop\DetailedReview\Block\Adminhtml\Form\Field\Manager $fieldsManager */
        $fieldsManager = $this->getChildBlock('fields_manager');
        $element->setRenderer($fieldsManager);

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     */
    protected function getAttributeSetObject()
    {
        return $this->_coreRegistry->registry(AbstractForm::REGISTRY_KEY);
    }
}

<?php

namespace Rivaz\Expertadvice\Block\Adminhtml\Items\View\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;



class Main extends Generic implements TabInterface
{

    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_rivaz_expertadvice_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'fname',
            'text',
            ['name' => 'fname', 'label' => __('First Name'), 'title' => __('First Name'), 'required' => true, 'readonly' =>true]
        );
        $fieldset->addField(
            'lname',
            'text',
            ['name' => 'lname', 'label' => __('Last Name'), 'title' => __('Last Name'), 'required' => true, 'readonly' =>true]
        );
		$fieldset->addField(
            'email',
            'text',
            ['name' => 'email', 'label' => __('Email'), 'title' => __('Email'), 'required' => true, 'readonly' =>true]
        );
		$fieldset->addField(
            'country',
            'text',
            ['name' => 'country', 'label' => __('Country'), 'title' => __('Country'), 'required' => true, 'readonly' =>true]
        );
		$fieldset->addField(
            'rmember',
            'text',
            ['name' => 'rmember', 'label' => __('Member'), 'title' => __('Member'), 'required' => true, 'readonly' =>true]
        );
		$fieldset->addField(
            'advise',
            'text',
            ['name' => 'advise', 'label' => __('Advise'), 'title' => __('Advise'), 'required' => true, 'readonly' =>true]
        );
		$fieldset->addField(
            'mskin',
            'text',
            ['name' => 'mskin', 'label' => __('My Skin'), 'title' => __('My Skin'), 'required' => true, 'readonly' =>true]
        );
		$fieldset->addField(
            'mhair',
            'text',
            ['name' => 'mhair', 'label' => __('My Hair'), 'title' => __('My Hair'), 'required' => true, 'readonly' =>true]
        );
		$fieldset->addField(
            'iam',
            'text',
            ['name' => 'iam', 'label' => __('I am'), 'title' => __('I am'), 'required' => true, 'readonly' =>true]
        );
		$fieldset->addField(
            'message',
            'textarea',
            ['name' => 'message', 'label' => __('Message'), 'title' => __('Message'), 'required' => true, 'readonly' =>true]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

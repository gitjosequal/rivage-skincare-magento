<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

/**
 * Attribute add/edit form options tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Attribute\Edit\Tab\Validation;

use Magento\Framework\DataObject;

class OptionsValidationRules extends \Magento\Backend\Block\Template
{
    const NAME_VALIDATION_RULES_OPTION = 'validation_rules_option';

    //Equals to the number of columns(Validation Rules, Rule Parameters) + drag-n-drop + Delete button
    const QUANTITY_COLUMNS = 4;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /** @var \MageWorkshop\DetailedReview\Helper\ValidationRulesListHelper $validationRulesListHelper */
    protected $validationRulesListHelper;

    /**
     * @var \MageWorkshop\Core\Helper\Serializer
     */
    private $serializer;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorkshop\DetailedReview\Helper\ValidationRulesListHelper $validationRulesListHelper
     * @param \MageWorkshop\Core\Helper\Serializer $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorkshop\DetailedReview\Helper\ValidationRulesListHelper $validationRulesListHelper,
        \MageWorkshop\Core\Helper\Serializer $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->validationRulesListHelper = $validationRulesListHelper;
        $this->serializer = $serializer;
    }

    /**
     * Retrieve options field name prefix
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->getFieldId();
    }

    /**
     * Retrieve options field id prefix
     *
     * @return string
     */
    public function getFieldId()
    {
        return self::NAME_VALIDATION_RULES_OPTION;
    }

    /**
     * @return int
     */
    public function getCountColumns()
    {
        return self::QUANTITY_COLUMNS;
    }

    /**
     * @return \Magento\Framework\DataObject[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getOptionValues()
    {
        if (!isset($this->_configuredRules)) {
            $configuredRules = [];

            $attribute = $this->getAttributeObject();
            // Deserialization failure mean we have a problem somewhere,
            // so better if this code generates an error message
            $validationRulesData = [];
            if ($savedValidationRulesData = $attribute->getData('validate_rules')) {
                $validationRulesData = $this->serializer->unserialize($savedValidationRulesData);
            }

            foreach ($validationRulesData as $optionId => $optionValue) {
                $value = [
                    'type'       => $optionValue['type'],
                    'label'      => $this->validationRulesListHelper->getRuleLabel($optionValue['type']),
                    'value'      => $optionValue['value'],
                ];
                $configuredRules[] = new DataObject($value);
            }
            $this->_configuredRules = $configuredRules;
        }

        return $this->_configuredRules;
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @codeCoverageIgnore
     */
    protected function getAttributeObject()
    {
        return $this->registry->registry('entity_attribute');
    }

    /**
     * @return string
     */
    public function getAllValidationOptions()
    {
        return json_encode($this->validationRulesListHelper->getAllValidationOptions());
    }
}

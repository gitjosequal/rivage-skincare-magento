<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml\Attribute;

use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigInterface;
use Magento\Swatches\Model\Swatch;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \MageWorkshop\DetailedReview\Controller\Adminhtml\AbstractAttribute
{
    const UNACCEPTABLE_ATTRIBUTE_CODE_EXCEPTION =
        'Attribute code "%1" is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.';

    const SUCCESS_MESSAGE = 'Review attribute was saved.';

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();
        $resultRedirect = $this->resultRedirectFactory->create();

        // All validation logic can be moved there
        if ($data = $request->getPostValue()) {
            $this->setProperFrontendInput($data);
            // Validate attribute code to ensure it does not contain invalid characters
            $attributeId = $request->getParam('attribute_id');
            if ($attributeCode = $request->getParam('attribute_code')) {
                if ($this->getRequest()->getParam('attribute_code') !== '') {
                    if (!preg_match('/^[a-z][a-z_0-9]{0,30}$/', $attributeCode)) {
                        $this->messageManager->addErrorMessage(
                            __(self::UNACCEPTABLE_ATTRIBUTE_CODE_EXCEPTION, $attributeCode)
                        );
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            ['attribute_id' => $attributeId, '_current' => true]
                        );
                    }
                }
                $data['attribute_code'] = $attributeCode;
            }

            try {
                /* @var \Magento\Eav\Model\Entity\Attribute $attribute */
                $attribute = $this->attributeFactory->create();

                if ($attributeId) {
                    $attribute->load($attributeId);
                    if (!$attribute->getId()) {
                        $this->messageManager->addErrorMessage(__(self::ATTRIBUTE_NO_LONGER_EXISTS_EXCEPTION));
                        return $resultRedirect->setPath('*/*/');
                    }
                    // Entity type validation
                    if ($attribute->getEntityTypeId() != $this->getEntityTypeId()) {
                        $this->messageManager->addErrorMessage(__(self::INVALID_ENTITY_TYPE_EXCEPTION));
                        $this->_getSession()->setAttributeData($data);
                        return $resultRedirect->setPath('*/*/');
                    }

                    /** @var AttributeConfigInterface $attributeConfig */
                    $attributeConfig = $this->entityConfig->getEntityAttributesConfig($this->entityType)
                        ->getItem($attribute->getFrontendInput());
                } else {
                    /** @var AttributeConfigInterface $attributeConfig */
                    $attributeConfig = $this->entityConfig->getEntityAttributesConfig($this->entityType)
                        ->getItem($request->getParam('frontend_input'));
                    $data['source_model'] = $attributeConfig->getSourceModel();
                    $data['backend_model'] = $attributeConfig->getBackendModel();
                    $data['validate_rules'] = $attributeConfig->getValidateRules();
                }

                $frontendInput = isset($data['frontend_input'])
                    ? $data['frontend_input']
                    : $attributeConfig->getFrontendInput();
                $data['backend_type'] = $attribute->getBackendTypeByInput($frontendInput);
                // Attribute types are hardcoded during module setup
                // some types do not match backendTypeByInput, for example image attribute has
                // varchar type, but getBackendTypeByInput would return text.
                // To support saving attributes we have to check first that we do not overwrite
                // existing type.
                if ($attribute->hasData('backend_type') && $attribute->getBackendType() != $data['backend_type']) {
                    $data['backend_type'] = $attribute->getBackendType();
                }

                if ($defaultValueField = $attribute->getDefaultValueByInput($attributeConfig->getFrontendInput())) {
                    $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
                }

                if ($submittedValidationRules = (array) $request->getParam('validation_rules_option')) {
                    foreach ($submittedValidationRules as $index => $ruleData) {
                        if (isset($ruleData['is_delete']) && $ruleData['is_delete']) {
                            unset($submittedValidationRules[$index]);
                        }
                    }
                }
                $data['validate_rules'] = $this->serializer->serialize($submittedValidationRules);
                $data['attribute_visual_settings'] = $this->attributeHelper->processVisualSettings($data);

                $attribute->addData($data);

                if (!$attributeId) {
                    $attribute->setEntityTypeId($this->getEntityTypeId());
                    $attribute->setIsUserDefined(1);
                }

                $attribute->save();
                $this->messageManager->addSuccessMessage(__(self::SUCCESS_MESSAGE));

                $this->attributeLabelCache->clean();
                $this->_getSession()->setAttributeData(false);

                if ($request->getParam('back', false)) {
                    $resultRedirect->setPath('*/*/edit', ['attribute_id' => $attribute->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('*/*/');
                }
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                return $resultRedirect->setPath('*/*/edit', ['attribute_id' => $attributeId, '_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Based on the \Magento\Swatches\Controller\Adminhtml\Product\Attribute\Plugin\Save
     * Set frontend input so that Swatches module is able to identify that this is a swatch attribute
     *
     * @param array $data
     * @return void
     */
    protected function setProperFrontendInput(&$data)
    {
        if (isset($data['frontend_input'])) {
            switch ($data['frontend_input']) {
                case 'swatch_visual':
                    $data[Swatch::SWATCH_INPUT_TYPE_KEY] = Swatch::SWATCH_INPUT_TYPE_VISUAL;
                    $data['frontend_input'] = 'select';
                    break;
                case 'swatch_text':
                    $data[Swatch::SWATCH_INPUT_TYPE_KEY] = Swatch::SWATCH_INPUT_TYPE_TEXT;
                    $data['use_product_image_for_swatch'] = 0;
                    $data['frontend_input'] = 'select';
                    break;
                case 'select':
                    $data[Swatch::SWATCH_INPUT_TYPE_KEY] = Swatch::SWATCH_INPUT_TYPE_DROPDOWN;
                    $data['frontend_input'] = 'select';
                    break;
            }
        }
    }
}

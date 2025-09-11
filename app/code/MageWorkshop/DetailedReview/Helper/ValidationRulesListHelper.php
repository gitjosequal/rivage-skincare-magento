<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Helper;

class ValidationRulesListHelper
{
    /**
     * @return array
     */
    public function getAllValidationOptions()
    {
        return [
            'maximum-length' => [
                'label' => 'Maximum length',
                'has_params' => 1,
                'params_additional_class' => 'required-entry validate-number',
                'applicable_for' => ['text', 'textarea']
            ],
            'minimum-length' => [
                'label' => 'Minimum length',
                'has_params' => 1,
                'params_additional_class' => 'required-entry validate-number',
                'applicable_for' => ['text', 'textarea']
            ],
            'validate-number' => [
                'label' => 'Validate number',
                'has_params' => 0,
                'applicable_for' => ['text']
            ],
            'validate-url' => [
                'label' => 'Validate url',
                'has_params' => 0,
                'applicable_for' => ['text', 'textarea']
            ],
            // test data
//            'url3' => [
//                'label' => 'Url3',
//                'has_params' => 0,
//                'applicable_for' => ['text', 'select', 'multiselect']
//            ],
//            'url4' => [
//                'label' => 'Url4',
//                'has_params' => 0,
//                'applicable_for' => ['text', 'swatch_visual']
//            ],
        ];
    }

    /**
     * @param string $rule
     * @return string
     */
    public function getRuleLabel($rule)
    {
        return ucfirst(str_replace('-', ' ', $rule));
    }
}

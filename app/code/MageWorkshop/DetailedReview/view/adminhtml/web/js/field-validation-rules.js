/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'mage/template'
], function ($, mageTemplate) {
    'use strict';

    if (!$.hasOwnProperty('mageWorkshop')) {
        $.mageWorkshop = {};
    }

    $.widget('mageWorkshop.detailedReview_fieldValidationRules', {
        /**
         * @property {array} options.attributesData
         * @property {array} options.optionsParam
         * @property {object} options.inputValidationRules
         */
        options: {
            $frontendInput: $('#frontend_input'),
            $validationOptionsPanel: $('#validation-options-panel'),
            $addNewValidationRule: $('#add_new_validation_option_button'),
            counter: 0
        },

        _create: function () {
            $.each(this.options.attributesData, this.renderTemplate.bind(this));

            // Handle Input Type change
            this.options.$frontendInput.change(this.toggleValidationOptions.bind(this));
            // Handle Add Option button click
            this.options.$addNewValidationRule.click(this.addNewValidationRule.bind(this));
            // Handle Delete Option button click
            this.options.$validationOptionsPanel.on('click', '.delete-option', this.removeValidationRule.bind(this));
        },

        // add new select
        addNewValidationRule: function () {
            if (!this.checkSelects()) {
                return false;
            }
            this.renderTemplate();
            this.addOptionToSelect();
        },

        getValidationRulesForInputType: function () {
            var inputType = this.getInputType(),
                availableValidationRules = [];

            if (typeof this.options.inputValidationRules === 'undefined') {
                return availableValidationRules;
            }

            for (var i in this.options.inputValidationRules) {
                if (this.options.inputValidationRules[i]['applicable_for'].indexOf(inputType) != -1) {
                    availableValidationRules.push(i);
                }
            }

            return availableValidationRules;
        },

        addOptionToSelect: function () {
            var $existingRules = this.element.find('select:visible'),
                $select = $existingRules.last(),
                existingSelects = [],
                that = this;

            if ($existingRules && $existingRules.val() != '') {
                $existingRules.each(function (index, data) {
                    if (data.value) {
                        existingSelects.push(data.value);
                    }
                });
            }

            $.each(this.getValidationRulesForInputType(), function (index, rule) {
                if (existingSelects.indexOf(rule) < 0) {
                    $select.append('<option value=' + rule + '>' + that.options.inputValidationRules[rule].label + '</option>');
                }
            });
            $existingRules.change(this.setParametersToOption.bind(this));
        },

        // Check if there are selects with no validation rule chosen
        checkSelects: function () {
            var hasValue = true,
                $existingRules = this.element.find('select:visible');
            if ($existingRules.length == this.getValidationRulesForInputType().length) {
                return false;
            }

            $existingRules.each(function () {
                if (!this.value) {
                    hasValue = false;
                    return false;
                }
            });

            return hasValue;
        },

        setParametersToOption: function (event) {
            var rule = event.currentTarget.value,
                fieldRuleParameters = this.element.find('.rule-parameters').last();
            if (this.options.inputValidationRules.hasOwnProperty(rule)) {
                this.element.children().last().attr(
                    'data-option-input-types',
                    this.options.inputValidationRules[rule].applicable_for.join(' ')
                );
                if (this.options.inputValidationRules[rule].has_params == 1) {
                    fieldRuleParameters.addClass(this.options.inputValidationRules[rule].params_additional_class);
                } else {
                    fieldRuleParameters.hide();
                }
            }
        },

        renderTemplate: function (index, data) {
            if (typeof data === "undefined") {
                data = {};
            }

            data.counter = this.options.counter++;
            this.template = mageTemplate(this.options.template);
            this.element.append(this.template({
                data: data
            }));
        },

        removeValidationRule: function (event) {
            var element = event.currentTarget.parentNode.parentElement;
            if (element) {
                element.remove();
            }
        },

        // hide section "Field Validation Rules" if there are no validation rules available
        toggleValidationOptions: function () {
            var $currentFieldset = this.element.parents('fieldset'),
                inputType = this.getInputType();

            if (typeof this.getValidationRulesForInputType() != 'undefined' && this.getValidationRulesForInputType().length > 0) {
                this.options.$validationOptionsPanel.show();
                $currentFieldset.find('p').addClass('hidden');
                this.options.$validationOptionsPanel.find('tr[data-option-input-types]').each(function () {
                    if (this.getAttribute('data-option-input-types').include(inputType)) {
                        this.show();
                    } else {
                        this.hide();
                    }
                });
            } else {
                this.options.$validationOptionsPanel.hide();
                $currentFieldset.find('p').removeClass('hidden');
            }
        },

        getInputType: function () {
            return this.options.$frontendInput.val();
        }
    });

    return $.mageWorkshop.detailedReview_fieldValidationRules;
});

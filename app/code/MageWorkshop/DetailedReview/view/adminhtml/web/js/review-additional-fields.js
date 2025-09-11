/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

/*jshint browser:true*/
define([
    'jquery'
], function ($) {
    'use strict';

    if (!$.hasOwnProperty('mageWorkshop')) {
        $.mageWorkshop = {};
    }

    $.widget('mageWorkshop.newReviewFieldsLoader', {
        // avoid making unnecessary AJAX calls
        selectedStoresFieldsCache: [],
        $saveButton: $('#save_button'),

        _create: function () {
            this.element.on({
                change: this.loadReviewFields.bind(this)
            });
        },

        loadReviewFields: function () {
            var productId = (this.options.productId != 0)
                ? this.options.productId
                : $('#product_id').val();

            this.$saveButton.disabled = true;

            var selectedStores = $('#select_stores').val().join();

            if (typeof this.selectedStoresFieldsCache[selectedStores] !== 'undefined') {
                this.renderReviewFields(this.selectedStoresFieldsCache[selectedStores]);
            } else {
                var params = {
                    select_stores: selectedStores,
                    product_id: productId,
                    isAjax: 'true',
                    form_key: FORM_KEY
                };

                $.ajax({
                    url: this.options.url,
                    type: 'get',
                    data: params,
                    success: function (data) {
                        this.selectedStoresFieldsCache[selectedStores] = data.fields;
                        this.renderReviewFields(data.fields);
                    }.bind(this)
                });
            }
            this.$saveButton.disabled = false;
        },

        renderReviewFields: function (fields) {
            var $renderedFields = $('#additional_fields_delimiter').nextAll('div.field');

            var fieldSelectors = [];
            for (var property in fields) {
                if (fields.hasOwnProperty(property)) {
                    fieldSelectors.push('#' + property);
                }
            }
            fieldSelectors = fieldSelectors.join(',');

            // show all existing fields
            var $reviewField = {};
            // need to build a new array because "fields" is passed by the reference and we shouldn't modify it
            var skipRendering = [];
            $.each($renderedFields, function (index, element) {
                $reviewField = $(element).find(fieldSelectors);

                if ($reviewField.length) {
                    // show existing field
                    $(element).show();
                    skipRendering.push($reviewField.attr('id'));
                } else {
                    // hide the field if it exists, but is not needed. In this case we won't loose the data
                    // if the user changes the selected stores again
                    $(element).hide();
                }
                // do not render the field if it already exists
            });

            $.each(fields, function (fieldName, html) {
                if (skipRendering.indexOf(fieldName) === -1) {
                    this.element.closest('fieldset').append(html);
                }
            }.bind(this));
        }
    });

    return $.mageWorkshop.newReviewFieldsLoader;
});

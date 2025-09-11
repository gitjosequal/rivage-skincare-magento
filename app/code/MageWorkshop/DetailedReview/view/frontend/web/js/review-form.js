/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    if (!$.hasOwnProperty('mageWorkshop')) {
        $.mageWorkshop = {};
    }

    $.widget('mageWorkshop.mageWorkshop_detailedReview_reviewForm', {
        options: {
            reviewForm: '#review-form',
            multiSelect: '.field-block select[multiple="multiple"]',
            $productInfo: $('.items[role="tablist"]'),
            allReviewBlock: '#product-review-container',
            filtersReviewBlock: '#review-search-form',
            containerReviewRating: '.container-review-rating',
            backButton: '.button-back',
            submitButton: $('button.submit.primary'),
            tabReview: '#tab-label-reviews'
        },

        _create: function () {
            if ($(this.options.filtersReviewBlock).length) {
                $(this.options.reviewForm).on('click', this.options.backButton, this.backButton.bind(this));
            } else {
                $(this.options.backButton).hide();
            }
            $(this.options.reviewForm).on('change', this.options.multiSelect, this.checkMultiSelect.bind(this));
            if (window.location.hash === '#review-form') {
                $(this.options.tabReview).trigger('click');
            }
            $(this.options.reviewForm).on('submit', this.options.submitButton, this.disableSubmitButton.bind(this));
        },

        checkMultiSelect: function (event) {
            var that = this,
                $selectedOptions = $(event.currentTarget.selectedOptions),
                currentSelectName = event.currentTarget.name,
                arraySelectOptions = [];

            // @TODO: test this. Multiselects may not be connected
            $selectedOptions.each(function (index, element) {
                if (typeof (element) !== 'undefined') {
                    arraySelectOptions.push(element.label);
                    $(that.element.find('.field-block select[multiple="multiple"]')).each(function (index, element) {
                        if (element.name !== currentSelectName) {
                            $(element.options).each(function (index, option) {
                                option.disabled = arraySelectOptions.indexOf(option.label) !== -1;
                            })
                        }
                    });
                }
            });
        },

        backButton: function (event) {
            event.preventDefault();
            $(this.options.allReviewBlock).show();
            $(this.options.filtersReviewBlock).show();
            $(this.options.containerReviewRating).show();
            $(this.element.parent()).show();
            $(this.options.reviewForm).hide();
        },

        disableSubmitButton: function () {
            if ($(this.options.reviewForm).valid()) {
                this.options.submitButton.attr('disabled', true);
            }
        }
    });

    return $.mageWorkshop.mageWorkshop_detailedReview_reviewForm;
});

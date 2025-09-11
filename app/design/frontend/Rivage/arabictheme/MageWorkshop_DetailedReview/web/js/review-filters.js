/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    if (!$.hasOwnProperty('mageWorkshop')) {
        $.mageWorkshop = {};
    }

    $.widget('mageWorkshop.detailedReview_reviewFilters', {

        _create: function () {
            this.$filterByDate = $(this.options.filterByDate);
            this.$sortDirection = $(this.options.sortDirection);
            this.$search = $(this.options.search);

            this.defaultFilterByDate = this.$filterByDate.val();
            this.defaultSortDirection = this.$sortDirection.val();

            this.toggleSearchButton();
            this.element.submit(this.submitForm.bind(this));
            $(this.options.clearAllFilters).click(this.clearAllFilters.bind(this));
            this.bindChangeEvents();
        },

        toggleSearchButton:function () {
            var $button = this.element.find('button[type=submit]');
            $button.attr('disabled', 'true');

            $(this.$search).on('keyup',function() {
                var charCount = $(this).val().replace(/\s/g, '').length;

                if (charCount >= 3) {
                    $button.removeAttr('disabled');
                } else {
                    $button.attr('disabled', 'disabled');
                }
            });
        },

        bindChangeEvents: function() {
            this.$filterByDate.on('change', this.submitForm.bind(this));
            this.$sortDirection.on('change', this.submitForm.bind(this));
            this.$search.on('change', this.submitForm.bind(this));
        },

        clearAllFilters: function () {
            this.$search.val('');
            this.$filterByDate.val(this.defaultFilterByDate);
            this.$sortDirection.val(this.defaultSortDirection);
            this.submitForm();
        },

        /**
         * SubmitURL is set when the user clicks on the pagination links. In this case there is no need to collect data
         * from the form, because the link already contains all currently selected parameters!
         *
         * @param {object} event
         * @param {string} submitUrl
         * @returns {boolean}
         */
        submitForm: function (event, submitUrl) {
            if (this.isInProgress) {
                return false;
            }

            this.isInProgress = true;

            var searchMessage = "<div id='search-message'>" +
                "<hr><p class='notice-msg'>" + $t('Sorry, no reviews matched your criteria.') + "</p>" +
                "</div>",
                that = this,
                data = {};

            if  (typeof submitUrl === "undefined") {
                submitUrl = this.element.attr('action');

                $(this.element.serializeArray()).each(function (index, element) {
                    data[element.name] = element.value;
                });

                if (!$(this.element).valid()) {
                    // we will show validation error - just will not send the input data
                    data[this.$search.attr('name')] = '';
                }
            }

            $.ajax({
                url: submitUrl,
                data: data,
                dataType: 'html',
                showLoader: true,
                success: (function (result) {
                    if (typeof (result) !== 'undefined' && result && result.length !== 0) {
                        $('#product-review-container').html(result);
                    } else {
                        $('#product-review-container').html(searchMessage);
                    }
                    $('body').trigger('contentUpdated');

                    // Here we again bind the click events in the same way as in the "process-reviews.js"
                    // because the pagination click is bind to the exact elements
                    $('[data-role="product-review"] .pages a').each(function (index, element) {
                        $(element).click(that.submitWithPagination.bind(that));
                    });
                }),
                complete: function () {
                    that.isInProgress = false;
                }
            });

            return false;
        },

        submitWithPagination: function (event) {
            this.submitForm(event, $(event.currentTarget).attr('href'));
            // animation from the Magento core
            $('html, body').animate({
                scrollTop: $('#reviews').offset().top - 50
            }, 300);
            event.preventDefault();
        }
    });

    return $.mageWorkshop.detailedReview_reviewFilters;
});
/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'jquery/ui',
    'mageWorkshop_detailedReview_reviewRating',
    'Magento_Review/js/process-reviews'
], function($) {
    'use strict';

    if (!$.hasOwnProperty('mageWorkshop')) {
        $.mageWorkshop = {};
    }

    $.widget('mageWorkshop.customerPermissions_checkAllRules', {

        options: {
            reviewForm: '#review-form',
            containerReviewRating: '.container-review-rating',
            allReviewBlock: '#product-review-container',
            events: {
                showReviewForm: 'show-review-form',
                hideReviewForm: 'hide-review-form',
                showReviewRating: 'show-review-rating',
                showReviewsBlock: 'show-reviews-block',
            },
        },

        _create: function () {
            var self = this;

            $.ajax({
                url: this.options.url,
                data: {
                    productId: $('input[name="product"]').val(),
                    isAjax: true
                },
                dataType: 'html',

                success: function(res) {
                    if (res.length) {
                        self.element.html(res);
                        self.hideReviewForm();
                        self.element.trigger('contentUpdated');
                    } else {
                        self.element.html('');
                        self.showAddReviewButton();
                    }
                }
            });

            this.element.on(this.options.events.showReviewForm, this.showReviewForm.bind(this));
            this.element.on(this.options.events.hideReviewForm, this.hideReviewForm.bind(this));
            this.element.on(this.options.events.showReviewRating, this.showReviewRating.bind(this));
            this.element.on(this.options.events.showReviewsBlock, this.showReviewsBlock.bind(this));
        },

        showAddReviewButton: function () {
            $(this.options.addReviewButton).show();
        },

        showReviewForm: function () {
            $(this.options.reviewForm).show();
        },

        showReviewRating: function () {
            $(this.options.containerReviewRating).show();
            $(this.options.addReviewButton).show();
        },

        showReviewsBlock: function () {
            $(this.options.allReviewBlock).show();
        },

        hideReviewForm: function () {
            $(this.options.reviewForm).hide();
        },

    });

    return $.mageWorkshop.customerPermissions_checkAllRules;
});

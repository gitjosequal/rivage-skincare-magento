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

    $.widget('mageWorkshop.mageWorkshop_detailedReview_reviewRating', {
        options: {
            addReviewButton: '.review-average-info a.add,.product-reviews-summary a.add',
            allReviewBlock: '#product-review-container',
            filtersReviewBlock: '#review-search-form',
            createReviewForm: '#review-form'
        },

        _create: function () {
            $(this.options.createReviewForm).hide();
            $(this.options.addReviewButton).click(this.createReview.bind(this));
        },

        createReview: function (event) {
            event.preventDefault();
            $(this.options.allReviewBlock).hide();
            $(this.options.filtersReviewBlock).hide();
            $(this.element.parent()).hide();
            $(this.options.createReviewForm).show();
        }
    });

    return $.mageWorkshop.mageWorkshop_detailedReview_reviewRating;
});
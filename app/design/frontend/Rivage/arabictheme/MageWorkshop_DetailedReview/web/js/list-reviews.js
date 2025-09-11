/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery'
], function ($) {
    'use strict';

    if (!$.hasOwnProperty('mageWorkshop')) {
        $.mageWorkshop = {};
    }

    $.widget('mageWorkshop.mageWorkshop_detailedReview_listReviews', {
        _create: function () {
            $(".moreLink").toggle(function () {
                $(this).text($(this).data('less'))
                    .siblings(".completeDescription")
                    .show();
                $(this).siblings(".teaser")
                    .hide();
            }, function () {
                $(this).text($(this).data('more'))
                    .siblings(".completeDescription")
                    .hide();
                $(this).siblings(".teaser")
                    .show();
            });
        }
    });

    return $.mageWorkshop.mageWorkshop_detailedReview_listReviews;
});
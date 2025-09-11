/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    $.widget('mageWorkshop.customerPermissions_banned', {

        options: {
            addReviewButton: '.review-average-info a.add,.product-reviews-summary a.add',
            resultBlock: '#permissions-block'
        },

        _create: function () {
            $(this.options.addReviewButton).on('click', this.alert.bind(this));

            if ($('#customer-reviews').length) {
                $('.banned-customer-message').hide();
            }
        },

        alert: function () {
            alert({
                title: '',
                content: this.options.message,
            });

            $(this.options.resultBlock).trigger('hide-review-form');
            $(this.options.resultBlock).trigger('show-reviews-block');
            $(this.options.resultBlock).trigger('show-review-rating');
        }
    });

    return $.mageWorkshop.customerPermissions_banned;
});
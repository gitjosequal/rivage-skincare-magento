/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'jquery/ui',
    'mage/validation',
], function ($) {
    'use strict';

    $.widget('mageWorkshop.customerPermissions_verifyBuyer', {

        options: {
            processStart: 'processStart',
            processStop: 'processStop',
            verifyButton: '#verify-button',
            addReviewButton: '.review-average-info a.add,.product-reviews-summary a.add',
            canCancelVerification: true,
            cancelVerification: '#cancel-verification',
            notVerifyMessage: '.not-verify-user',
            resultBlock: '#permissions-block',
            emailElementId: '#verify-email',
            isVerificationRequest: false
        },

        _create: function () {
            this._bindClick();

            // There is nothing to show on the page if product has no reviews. The form must be visible.
            if (this.options.canCancelVerification && !this.options.isVerificationRequest) {
                this.element.hide();
            }

            if (!this.options.isVerificationRequest) {
                $(this.options.notVerifyMessage).hide();
            }
        },

        _bindClick: function () {
            var self = this;

            $(this.options.verifyButton).on('click.verifyBuyer', function (e) {
                if ($(self.element).validation() && $(self.element).validation('isValid')) {
                    e.preventDefault();
                    self.ajaxSubmit($(self.options.emailElementId).val());
                }
            });

            $(this.options.cancelVerification).on('click.verifyBuyer', function () {
                self.cancelVerification();
            });

            $(this.options.addReviewButton).on('click.verifyBuyer', function () {
                this.element.show();
                $(this.options.resultBlock).trigger('hide-review-form');
            }.bind(this));
        },

        _destroy: function () {
            $(this.options.verifyButton).off('click.verifyBuyer');
            $(this.options.cancelVerification).off('click.verifyBuyer');
            $(this.options.addReviewButton).off('click.verifyBuyer');
        },

        isLoaderEnabled: function () {
            return this.options.processStart && this.options.processStop;
        },

        ajaxSubmit: function (email) {
            var self = this;
            $.ajax({
                url: self.options.submitUrl,
                data: {
                    "verify-email": email,
                    productId: $('input[name="product"]').val(),
                    isAjax: true
                },
                dataType: 'html',
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function (res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }
                    if (res.length) {
                        self.userNotVerified(res);
                    } else {
                        self.userVerified();
                    }
                    // Destroy current instance because new one may be initialized depending on what comes in the response
                    // If not than this means customer is verified and there is no need to hide the review form
                    self.destroy();
                }
            });
        },

        userVerified: function () {
            $(this.options.resultBlock).html('');
            $(this.options.resultBlock).trigger('show-review-form');
        },

        userNotVerified: function (res) {
            $(this.options.resultBlock).html(res);
            $(this.options.resultBlock).trigger('contentUpdated');
        },

        cancelVerification: function () {
            this.element.hide();
            $(this.options.resultBlock).trigger('show-reviews-block');
            $(this.options.resultBlock).trigger('show-review-rating');
        }
    });

    return $.mageWorkshop.customerPermissions_verifyBuyer;
});
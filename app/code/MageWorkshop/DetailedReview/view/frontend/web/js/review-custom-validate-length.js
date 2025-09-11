/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    'use strict';

    if (!$.hasOwnProperty('mageWorkshop')) {
        $.mageWorkshop = {};
    }

    $.widget('mageWorkshop.detailedReview_reviewCustomValidateLength', {

        _create: function () {
           this.reviewCustomValidateLength();
        },

        reviewCustomValidateLength: function () {
            $.validator.addMethod('custom-validate-length',
                function (v, elm) {
                    var reMax = /^maximum-length-[0-9]+$/,
                        reMin = /^minimum-length-[0-9]+$/,
                        validator = this,
                        result = true,
                        length = 0;
                    $.each(elm.className.split(' '), function (index, name) {
                        if (name.match(reMax) && result) {
                            length = name.split('-')[2];
                            validator.attrLength = length;
                            result = (v.length <= length);
                        }
                        if (name.match(reMin) && result) {
                            length = name.split('-')[2];
                            validator.attrLength = length;
                            result = v.length >= length;
                        }
                    });

                    return result;
                }, function () {
                    return $.mage.__("Minimum length of this field must be equal or more than %1 symbols.")
                        .replace('%1', this.attrLength);
                }
            )
        }
    });

    return $.mageWorkshop.detailedReview_reviewCustomValidateLength;
});

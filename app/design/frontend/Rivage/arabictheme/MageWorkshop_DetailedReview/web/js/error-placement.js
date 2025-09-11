/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'mage/mage'
], function ($) {
    'use strict';

    return function (config, element) {
        $(element).mage('validation', {
            errorPlacement: function (error, element) {
                var parent = element.parents('.field-block');
                if (parent.length) {
                    parent.after(error);
                } else {
                    if (element.parents('.review-control-vote').length) {
                        parent = element.parents('.review-control-vote');
                        parent.after(error);
                    } else {
                        element.after(error);
                    }
                }
            }
        });
    };
});
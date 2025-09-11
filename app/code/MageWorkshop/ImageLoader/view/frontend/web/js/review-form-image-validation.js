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

    $.widget('mageWorkshop.mageWorkshop_imageLoader_reviewFormImageValidation', {
        options: {
            maxSize: 1,
            extensions: []
        },

        _create: function () {
            this.validateFileExtensions();
            this.validateFilesize();
        },

        //Validate Image Extensions
        validateFileExtensions: function () {
            var that = this;
            var allowedType = that.options.extensions.join(", ");
            allowedType = allowedType.toUpperCase();
            $.validator.addMethod(
                'validate-file-extensions', function (v, elm) {

                    var value = elm.value;
                    if (!v) {
                        return true;
                    }
                    if (value !== undefined) {
                        var ext = value.substring(value.lastIndexOf('.') + 1);
                        for (var i = 0; i < that.options.extensions.length; i++) {
                            if (ext === that.options.extensions[i]) {
                                return true;
                            }
                        }
                    }

                    return false;
                }, $.mage.__('Uploaded file is not a valid image. Only %1 files are allowed.').replace('%1', allowedType));
        },

        //Validate Image FileSize
        validateFilesize: function () {
            var that = this;
            $.validator.addMethod(
                'validate-file-size', function (v, elm) {
                    var maxSize = that.options.maxSize * 1024 * 1024;
                    if (navigator.appName === "Microsoft Internet Explorer") {
                        if (elm.value) {
                            var oas = new ActiveXObject("Scripting.FileSystemObject");
                            var e = oas.getFile(elm.value);
                            var size = e.size;
                        }
                    } else {
                        if (elm.files[0] !== undefined) {
                            size = elm.files[0].size;
                        }
                    }

                    return !(size !== undefined && size > maxSize);
            }, $.mage.__('The file size should not exceed ' + this.options.maxSize + 'MB'));
        }
});

    return $.mageWorkshop.mageWorkshop_imageLoader_reviewFormImageValidation;
});

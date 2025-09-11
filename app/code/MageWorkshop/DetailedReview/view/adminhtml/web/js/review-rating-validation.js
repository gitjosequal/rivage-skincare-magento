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

    $.widget('mageWorkshop.detailedReview_newReviewRatingValidation', {
        options: {
            editForm: $('#edit_form'),
            $inputFile: $('input[type="file"]'),
            prevImages: [],
            images: [],
            parentDiv: ''
        },

        _create: function () {
            this.options.editForm.on('change', 'select[multiple="multiple"]', this.checkMultiSelect.bind(this));
            this.ratingValidation();
            this.imageInitials();
            $('.images-block').on('click', '.delete-option', this.removeImage.bind(this));

        },

        imageInitials: function () {
            var that = this,
                images = [],
                imagesArray = [];
            this.options.$inputFile.each(function (i, v) {
                $(v).attr('multiple', 'multiple');

                that.options.parentDiv = v.id;
                var imagesSourse = v.attributes['value'].value.split(',');

                $(imagesSourse).each(function (i, v) {
                    var image = v.slice(v.lastIndexOf('/') - 4);
                    if (v && v.length > 0) {
                        that.element.find('#' + that.options.parentDiv).after(
                            "<div class='images-block' style='width: 200px;'>" +
                            "<a href='" + v + "'><img id='" + that.options.parentDiv + "[]' height='50' width='50' src='" + v + "'></a>" +
                            "<input id='" + that.options.parentDiv + "' name='" + that.options.parentDiv + "[]' type='hidden'  value='" + image + "'>" +
                            "<button style='float: right;' title='Delete' type='button' class='action- scalable delete delete-option'><span>Delete</span></button>" +
                            "</div>"
                        );

                        images.push(image);
                    }
                });
                imagesArray[that.options.parentDiv] = images;
                images = [];
            });

            this.options.images = imagesArray;
        },

        removeImage: function (event) {
            var element = event.currentTarget.parentNode,
                deleteElement = event.currentTarget.previousElementSibling.value,
                currentImageAttribute = event.currentTarget.previousElementSibling.id;

            if (element) {
                element.remove();
            }

            for (var i = this.options.images[currentImageAttribute].length - 1; i >= 0; i--) {
                if (this.options.images[currentImageAttribute][i] === deleteElement) {
                    this.options.images[currentImageAttribute].splice(i, 1);
                }
            }
        },

        ratingValidation: function () {
            $.validator.addMethod('validate-rating',
                function () {
                    var ratings = $('#rating_detail').parent().find('.admin__field-rating'),
                        noError = true;

                    ratings.each(function (index, rating) {
                        noError = noError && $(rating).find('input:checked').length > 0;
                    });
                    return noError;
                },
                $.mage.__('Please select one of each ratings above.')
            )
        },

        checkMultiSelect: function (event) {
            var that = this,
                $selectedOptions = $(event.currentTarget.selectedOptions),
                currentSelectName = $(event.currentTarget.title).selector,
                arraySelectOptions = [];

            $selectedOptions.each(function (index, element) {
                if (typeof (element) !== 'undefined') {
                    arraySelectOptions.push(element.label);
                    $(that.element.find('select[multiple="multiple"]')).each(function (index, element) {
                        if (element.title !== currentSelectName) {
                            $(element.options).each(function (index, option) {
                                option.disabled = arraySelectOptions.indexOf(option.label) !== -1;
                            })
                        }
                    });
                }
            });
        }
    });

    return $.mageWorkshop.detailedReview_newReviewRatingValidation;
});

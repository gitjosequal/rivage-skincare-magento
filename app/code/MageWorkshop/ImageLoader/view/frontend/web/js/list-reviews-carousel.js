/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'mageWorkshop_imageLoader_owlCarousel',
    'mageWorkshop_imageLoader_fancyBox'
], function ($) {
    'use strict';

    if (!$.hasOwnProperty('mageWorkshop')) {
        $.mageWorkshop = {};
    }

    $.widget('mageWorkshop.mageWorkshop_imageLoader_listReviewsCarousel', {

        _create: function () {
            this.carousel();
            this.setCarouselAttributes();
        },

        carousel: function () {
            $(this.element).owlCarousel({
                nav: true,
                lazyLoad: true,
                responsiveClass: true,
                responsive:{
                    0:{
                        items: 1,
                        nav: true
                    },
                    500:{
                        items: 2,
                        nav: true
                    },
                    600:{
                        items: 3,
                        nav: true
                    },
                    768:{
                        items: 2,
                        nav: true
                    },
                    1024:{
                        items: 3,
                        nav: true,
                        touchDrag: false,
                        freeDrag: false
                    }
                }
            });
        },

        setCarouselAttributes: function () {
            $('.owl-carousel img').width(
                this.options.imageWidth
            ).height(
                this.options.imageHeight
            );
        }
    });

    return $.mageWorkshop.mageWorkshop_imageLoader_listReviewsCarousel;
});
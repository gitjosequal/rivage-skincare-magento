/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
        paths: {
            aos:                    'Magento_Theme/js/aos',
            owlcarouselslider:      'Magento_Theme/js/owl.carousel.min',
            spintowinfront:         'Knowband_Spinandwin::js/front_script.js',
            spintowinfrontframework:'Knowband_Spinandwin::js/jquery.fireworks.js',
            spintowinouibounce:     'Knowband_Spinandwin::js/ouibounce.js',
            slider_jquery_ui:       'Magento_Theme/js/slider',
            lazyLoad_rivage:        'Magento_Theme/js/lazyload.min'
        },
        shim: {
            'aos': {
                deps: ['jquery']
            },
            'owlcarouselslider': {
                deps: ['jquery']
            },
            'owlcarouselslidercustom':
            {
                deps: ['jquery']
            },
            'spintowinfront':
            {
                deps: ['jquery']
            },
            'spintowinfrontframework':
            {
                deps: ['jquery','spintowinfront']
            },
            'spintowinouibounce':
            {
                deps: ['jquery','spintowinfront','spintowinfrontframework']
            },
            'slider_jquery_ui':
            {
                deps: ['jquery','jquery/ui']
            },
            'lazyLoad_rivage': {
                deps: ['jquery']
            }
        },
};

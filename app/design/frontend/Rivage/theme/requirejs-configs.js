var config = {
    shim: {
        jquery: {
            exports: '$'
        },
        'Magento_Theme/js/owl.carousel.min':
            {
                deps: ['jquery']
            },
    }
};
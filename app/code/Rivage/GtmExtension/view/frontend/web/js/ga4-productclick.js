define([
    'jquery',
], function ($) {
    'use strict';

    var productClickDataArray = {};

    return function (config) {

        var productElements = $('.product-item');

        var productClickDataJson = config.productClickData;

        productClickDataArray[config.productId] = productClickDataJson;

        if(!productElements.hasClass('event-fired'))
        {
            productElements.addClass('event-fired');

            productElements.on('click', function () {
                
                var clickedProductId = config.productId;
                
                //if (window.dataLayer && productClickDataArray[clickedProductId]) {
                    
                    var clickedProductData = productClickDataArray[config.productId];
                    
                    if (window.dataLayer && clickedProductData) {
                        window.dataLayer.push(clickedProductData); // Parse the JSON data
                        console.log('Product Click:', clickedProductData);
                    }
                //}
            }); 
        }
    }
       
});


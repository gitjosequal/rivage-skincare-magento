define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    // Function to get customer information from customer-data.js
    var getCustomerInfo = function () {
        return customerData.get('customer')();
    };
    
    // Function to get customer group
    var getCustomerGroup = function () {
        return customerData.get('customer')().customer_group_id;
    };

    // Function to check if the customer is logged in
    var isLoggedIn = function () {
        var customerInfo = getCustomerInfo();
        return customerInfo && customerInfo.firstname;
    };

    return function (config) {
        // Get logged in customer ID and customer group ID
        var logged_in_customer_id = getCustomerInfo() ? getCustomerInfo().customer_id : 0;
        var customer_group_id = getCustomerGroup(); 
        var dataLayerObject = config.dataLayerObject;
        
        // Initialize the dataLayer
        window.dataLayer = window.dataLayer || [];

        // Push custom dataLayer objects from the configuration
        if (dataLayerObject !== '[[]]') {
            var dl4Objects = dataLayerObject;
            for (var i in dl4Objects) {
                if (dl4Objects.hasOwnProperty(i)) {
                    var dlObject = dl4Objects[i];
                    if (Array.isArray(dlObject)) {
                        for (var j = 0; j < dlObject.length; j++) {
                            window.dataLayer.push(dlObject[j]);
                        }
                    } else {
                        window.dataLayer.push(dlObject);
                    }
                }
            }
        }

        // Push customer group information to the dataLayer
        window.dataLayer.push({customerGroup: isLoggedIn() ? getCustomerGroup() : 'NOT LOGGED IN' });

        // Push customer ID to the dataLayer if logged in
        if (isLoggedIn()) {
            window.dataLayer.push({ CustomerID: logged_in_customer_id });
        }
        
        // Handle promotion events if promotion is enabled
        if (config.ispromotionenabled) {
           // console.log("hello");
            
            // Code for View Promotion Event
            var promotionEventData = [];
            if ($('[data-cz-promotion-event]').length) {
                $('[data-cz-promotion-event]').each(function () {
                    promotionEventData.push({
                        'promotion_id': $(this).data('promotion-id'),
                        'promotion_name': $(this).data('promotion-name'),
                        'creative_name': $(this).data('promotion-creative-name'),
                        'creative_slot': $(this).data('promotion-creative-slot')
                    });
                });
                if (promotionEventData.length) {
                    window.dataLayer.push({ecommerce: null});
                    window.dataLayer.push({
                        'event': 'view_promotion',
                        'ecommerce': {
                            'promoView': {
                                'promotions': promotionEventData
                            }
                        }
                    });
                }
            }

            // Code for Select Promotion Event
            var promotionClickEventData = [];
            if($('[data-cz-promotion-event]').length){
                $('[data-cz-promotion-event]').click(function() {
                    promotionClickEventData.push({
                        'promotion_id': $(this).data('promotion-id'),
                        'promotion_name': $(this).data('promotion-name'),
                        'creative_name': $(this).data('promotion-creative-name'),
                        'creative_slot': $(this).data('promotion-creative-slot')
                    });

                    if (promotionClickEventData.length) {
                        window.dataLayer.push({ecommerce: null});
                        window.dataLayer.push({
                            'event': 'select_promotion',
                            'ecommerce': {
                                'promoClick': {
                                    'promotions': promotionClickEventData
                                }
                            }
                        });
                    }
                });
                
            }
        }
    };
});


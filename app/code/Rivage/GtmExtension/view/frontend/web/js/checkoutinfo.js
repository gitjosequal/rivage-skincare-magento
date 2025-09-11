define([
    'jquery',
    'Magento_Checkout/js/model/quote'
], function ($, quote) {
    'use strict';

    // Subscribe to changes in the shipping method
    return function () {
        quote.shippingMethod.subscribe(function (shippingMethod) {

            // Remove any existing 'add_shipping_info' event from dataLayer
            for (var i = 0; i < window.dataLayer.length; i++) {
                if (window.dataLayer[i].event === 'add_shipping_info') {
                    window.dataLayer.splice(i, 1);
                    break;
                }
            }

            // Create shipping tier title using carrier and method
            if(shippingMethod){
                var shiptitle = shippingMethod['carrier_title'] + ' - ' + shippingMethod['method_title'];
            }else{
                shiptitle = '';
            }
            
            // Get cart total and currency code
            var cartTotal = quote.totals().base_grand_total;
            var currencyCode = quote.totals().quote_currency_code;

            // Prepare shipping info for dataLayer
            var checkoutinfo = {
                event: 'add_shipping_info',
                currency: currencyCode,
                value: cartTotal,
                ecommerce: {
                    actions: {
                        items: []
                    }
                },
                shipping_tier: shiptitle
            };

            // Prepare shipping data from quote items
            var shippingData = [];
            quote.getItems().forEach(function (item) {
                var product = item.product;
                var productDetail = {
                    currency: currencyCode,
                    item_name: item.name,
                    item_id: product.sku,
                    price: item.price_incl_tax,
                    quantity: item.qty,
                    item_list_id: product.category_ids && product.category_ids.length ? product.category_ids[0] : '',
                };
                shippingData.push(productDetail);
            });

            checkoutinfo.ecommerce.actions.items = shippingData;

            // Push checkoutinfo to dataLayer
            window.dataLayer.push(checkoutinfo);
        });


        // Subscribe to changes in the payment method
        quote.paymentMethod.subscribe(function (paymentMethod) {

            // Remove any existing 'add_payment_info' event from dataLayer
            for (var i = 0; i < window.dataLayer.length; i++) {
                if (window.dataLayer[i].event === 'add_payment_info') {
                    window.dataLayer.splice(i, 1);
                    break;
                }
            }

            // Get payment method title, cart total, and currency code
            var paymentMethodTitle = paymentMethod['method'];
            var cartTotal = quote.totals().base_grand_total;
            var currencyCode = quote.totals().quote_currency_code;

            // Prepare payment info for dataLayer
            var paymentInfo = {
                event: 'add_payment_info',
                currency: currencyCode,
                payment_type: paymentMethodTitle,
                value: cartTotal,
                ecommerce: {
                    actions: {
                        items: []
                    }
                }
            };

            // Prepare payment data from quote items
            var paymentData = [];
            quote.getItems().forEach(function (item) {
                var product = item.product;
                var productDetail = {
                    item_name: item.name,
                    item_id: product.sku,
                    price: item.price_incl_tax,
                    quantity: item.qty,
                    currency: currencyCode,
                };

                paymentData.push(productDetail);
            });

            paymentInfo.ecommerce.actions.items = paymentData;

            // Push paymentInfo to dataLayer
            window.dataLayer.push(paymentInfo);
        });
    };
});

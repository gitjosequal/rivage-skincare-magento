/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'Magento_Checkout/js/model/quote',
    ],
    function (
        $,
        Component, 
        urlBuilder,
        url,
        quote) {
        'use strict';

        var self;

        return Component.extend({
            redirectAfterPlaceOrder: false,

            defaults: {
                template: 'TD_MasterCard/payment/form'
            },

            initialize: function() {
                this._super();
                self = this;
            },

            getCode: function() {
                return 'mastercard_gateway';
            },

            getData: function() {
                return {
                    'method': this.item.method
                };
            },

            afterPlaceOrder: function () {
                window.location.replace(url.build('mastercard/checkout/index'));
            },

            /*
             * This same validation is done server-side in InitializationRequest.validateQuote()
             */
            validate: function() {
                var billingAddress = quote.billingAddress();
                var shippingAddress = quote.shippingAddress();
                self.messageContainer.clear();

                if (!billingAddress) {
                    self.messageContainer.addErrorMessage({'message': 'Please enter your billing address'});
                    return false;
                }

                if (!billingAddress.firstname || 
                    !billingAddress.lastname ||
                    !billingAddress.street ||
                    !billingAddress.city ||
                    billingAddress.firstname.length == 0 ||
                    billingAddress.lastname.length == 0 ||
                    billingAddress.street.length == 0 ||
                    billingAddress.city.length == 0 
					) {
                    self.messageContainer.addErrorMessage({'message': 'Please enter your billing address details'});
                    return false;
                }

                return true;
            },

            getTitle: function() {
                return window.checkoutConfig.payment.mastercard_gateway.title;
            },

            getDescription: function() {
                return window.checkoutConfig.payment.mastercard_gateway.description;
            },
            
            getMasterCardLogo:function(){
                var logo = window.checkoutConfig.payment.mastercard_gateway.logo;
                return logo;
            }

        });
    }
);
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
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'mastercard_gateway',
                component: 'TD_MasterCard/js/view/payment/method-renderer/mastercard_gateway'
            },
            {
                type: 'mastercard_api',
                component: 'TD_MasterCard/js/view/payment/method-renderer/mastercard_api'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);

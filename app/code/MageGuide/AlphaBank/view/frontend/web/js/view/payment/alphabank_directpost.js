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
                type: 'alphabank_directpost',
                component: 'MageGuide_AlphaBank/js/view/payment/method-renderer/alphabank_directpost'
            }
        );
        return Component.extend({});
    }
);


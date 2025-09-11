(function(require){
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            directoryRegionUpdater: 'Magento_Directory/js/region-updater'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    waitSeconds: 0,
    map: {
        '*': {
            'ko': 'knockoutjs/knockout',
            'knockout': 'knockoutjs/knockout',
            'mageUtils': 'mage/utils/main',
            'rjsResolver': 'mage/requirejs/resolver',
            'jquery-ui-modules/core': 'jquery/ui-modules/core',
            'jquery-ui-modules/accordion': 'jquery/ui-modules/widgets/accordion',
            'jquery-ui-modules/autocomplete': 'jquery/ui-modules/widgets/autocomplete',
            'jquery-ui-modules/button': 'jquery/ui-modules/widgets/button',
            'jquery-ui-modules/datepicker': 'jquery/ui-modules/widgets/datepicker',
            'jquery-ui-modules/dialog': 'jquery/ui-modules/widgets/dialog',
            'jquery-ui-modules/draggable': 'jquery/ui-modules/widgets/draggable',
            'jquery-ui-modules/droppable': 'jquery/ui-modules/widgets/droppable',
            'jquery-ui-modules/effect-blind': 'jquery/ui-modules/effects/effect-blind',
            'jquery-ui-modules/effect-bounce': 'jquery/ui-modules/effects/effect-bounce',
            'jquery-ui-modules/effect-clip': 'jquery/ui-modules/effects/effect-clip',
            'jquery-ui-modules/effect-drop': 'jquery/ui-modules/effects/effect-drop',
            'jquery-ui-modules/effect-explode': 'jquery/ui-modules/effects/effect-explode',
            'jquery-ui-modules/effect-fade': 'jquery/ui-modules/effects/effect-fade',
            'jquery-ui-modules/effect-fold': 'jquery/ui-modules/effects/effect-fold',
            'jquery-ui-modules/effect-highlight': 'jquery/ui-modules/effects/effect-highlight',
            'jquery-ui-modules/effect-scale': 'jquery/ui-modules/effects/effect-scale',
            'jquery-ui-modules/effect-pulsate': 'jquery/ui-modules/effects/effect-pulsate',
            'jquery-ui-modules/effect-shake': 'jquery/ui-modules/effects/effect-shake',
            'jquery-ui-modules/effect-slide': 'jquery/ui-modules/effects/effect-slide',
            'jquery-ui-modules/effect-transfer': 'jquery/ui-modules/effects/effect-transfer',
            'jquery-ui-modules/effect': 'jquery/ui-modules/effect',
            'jquery-ui-modules/menu': 'jquery/ui-modules/widgets/menu',
            'jquery-ui-modules/mouse': 'jquery/ui-modules/widgets/mouse',
            'jquery-ui-modules/position': 'jquery/ui-modules/position',
            'jquery-ui-modules/progressbar': 'jquery/ui-modules/widgets/progressbar',
            'jquery-ui-modules/resizable': 'jquery/ui-modules/widgets/resizable',
            'jquery-ui-modules/selectable': 'jquery/ui-modules/widgets/selectable',
            'jquery-ui-modules/slider': 'jquery/ui-modules/widgets/selectmenu',
            'jquery-ui-modules/sortable': 'jquery/ui-modules/widgets/sortable',
            'jquery-ui-modules/spinner': 'jquery/ui-modules/widgets/spinner',
            'jquery-ui-modules/tabs': 'jquery/ui-modules/widgets/tabs',
            'jquery-ui-modules/tooltip': 'jquery/ui-modules/widgets/tooltip',
            'jquery-ui-modules/widget': 'jquery/ui-modules/widget',
            'jquery-ui-modules/timepicker': 'jquery/timepicker'
        }
    },
    shim: {
        'jquery/jquery-migrate': ['jquery'],
        'mage/adminhtml/backup': ['prototype'],
        'mage/captcha': ['prototype'],
        'mage/new-gallery': ['jquery'],
        'jquery/ui': ['jquery'],
        'matchMedia': {
            'exports': 'mediaCheck'
        },
        'magnifier/magnifier': ['jquery']
    },
    paths: {
        'jquery/validate': 'jquery/jquery.validate',
        'jquery/file-uploader': 'jquery/fileUploader/jquery.fileuploader',
        'prototype': 'legacy-build.min',
        'jquery/jquery-storageapi': 'jquery/jquery.storageapi.min',
        'text': 'mage/requirejs/text',
        'domReady': 'requirejs/domReady',
        'spectrum': 'jquery/spectrum/spectrum',
        'tinycolor': 'jquery/spectrum/tinycolor',
        'jquery-ui-modules': 'jquery/ui-modules'
    },
    deps: [
        'jquery/jquery-migrate'
    ],
    config: {
        text: {
            'headers': {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    }
};

require(['jquery'], function ($) {
    'use strict';

    $.noConflict();
});

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'rowBuilder':             'Magento_Theme/js/row-builder',
            'toggleAdvanced':         'mage/toggle',
            'translateInline':        'mage/translate-inline',
            'sticky':                 'mage/sticky',
            'tabs':                   'mage/tabs',
            'collapsible':            'mage/collapsible',
            'dropdownDialog':         'mage/dropdown',
            'dropdown':               'mage/dropdowns',
            'accordion':              'mage/accordion',
            'loader':                 'mage/loader',
            'tooltip':                'mage/tooltip',
            'deletableItem':          'mage/deletable-item',
            'itemTable':              'mage/item-table',
            'fieldsetControls':       'mage/fieldset-controls',
            'fieldsetResetControl':   'mage/fieldset-controls',
            'redirectUrl':            'mage/redirect-url',
            'loaderAjax':             'mage/loader',
            'menu':                   'mage/menu',
            'popupWindow':            'mage/popup-window',
            'validation':             'mage/validation/validation',
            'breadcrumbs':            'Magento_Theme/js/view/breadcrumbs',
            'jquery/ui':              'jquery/compat',
            'cookieStatus':           'Magento_Theme/js/cookie-status'
        }
    },
    deps: [
        'mage/common',
        'mage/dataPost',
        'mage/bootstrap'
    ],
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Magento_Theme/js/view/add-home-breadcrumb': true
            },
            'jquery/ui-modules/widgets/sortable': {
                'jquery/patches/jquery-ui-sortable': true
            }
        }
    }
};

/* eslint-disable max-depth */
/**
 * Adds polyfills only for browser contexts which prevents bundlers from including them.
 */
if (typeof window !== 'undefined' && window.document) {
    /**
     * Polyfill localStorage and sessionStorage for browsers that do not support them.
     */
    try {
        if (!window.localStorage || !window.sessionStorage) {
            throw new Error();
        }

        localStorage.setItem('storage_test', 1);
        localStorage.removeItem('storage_test');
    } catch (e) {
        config.deps.push('mage/polyfill');
    }
}
/* eslint-enable max-depth */

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            checkoutBalance:    'Magento_Customer/js/checkout-balance',
            address:            'Magento_Customer/js/address',
            changeEmailPassword: 'Magento_Customer/js/change-email-password',
            passwordStrengthIndicator: 'Magento_Customer/js/password-strength-indicator',
            zxcvbn: 'Magento_Customer/js/zxcvbn',
            addressValidation: 'Magento_Customer/js/addressValidation',
            showPassword: 'Magento_Customer/js/show-password',
            'Magento_Customer/address': 'Magento_Customer/js/address',
            'Magento_Customer/change-email-password': 'Magento_Customer/js/change-email-password'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            escaper: 'Magento_Security/js/escaper'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            quickSearch: 'Magento_Search/js/form-mini',
            'Magento_Search/form-mini': 'Magento_Search/js/form-mini'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            compareList:            'Magento_Catalog/js/list',
            relatedProducts:        'Magento_Catalog/js/related-products',
            upsellProducts:         'Magento_Catalog/js/upsell-products',
            productListToolbarForm: 'Magento_Catalog/js/product/list/toolbar',
            catalogGallery:         'Magento_Catalog/js/gallery',
            catalogAddToCart:       'Magento_Catalog/js/catalog-add-to-cart'
        }
    },
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Magento_Catalog/js/product/breadcrumbs': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            priceBox:             'Magento_Catalog/js/price-box',
            priceOptionDate:      'Magento_Catalog/js/price-option-date',
            priceOptionFile:      'Magento_Catalog/js/price-option-file',
            priceOptions:         'Magento_Catalog/js/price-options',
            priceUtils:           'Magento_Catalog/js/price-utils'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            addToCart: 'Magento_Msrp/js/msrp'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            catalogSearch: 'Magento_CatalogSearch/form-mini'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            creditCardType: 'Magento_Payment/js/cc-type',
            'Magento_Payment/cc-type': 'Magento_Payment/js/cc-type'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftMessage:    'Magento_Sales/js/gift-message',
            ordersReturns:  'Magento_Sales/js/orders-returns',
            'Magento_Sales/gift-message':    'Magento_Sales/js/gift-message',
            'Magento_Sales/orders-returns':  'Magento_Sales/js/orders-returns'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            discountCode:           'Magento_Checkout/js/discount-codes',
            shoppingCart:           'Magento_Checkout/js/shopping-cart',
            regionUpdater:          'Magento_Checkout/js/region-updater',
            sidebar:                'Magento_Checkout/js/sidebar',
            checkoutLoader:         'Magento_Checkout/js/checkout-loader',
            checkoutData:           'Magento_Checkout/js/checkout-data',
            proceedToCheckout:      'Magento_Checkout/js/proceed-to-checkout',
            catalogAddToCart:       'Magento_Catalog/js/catalog-add-to-cart'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            requireCookie: 'Magento_Cookie/js/require-cookie',
            cookieNotices: 'Magento_Cookie/js/notices'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    paths: {
        'jquery/jquery-storageapi': 'Magento_Cookie/js/jquery.storageapi.extended'
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            downloadable: 'Magento_Downloadable/js/downloadable',
            'Magento_Downloadable/downloadable': 'Magento_Downloadable/js/downloadable'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            bundleOption:   'Magento_Bundle/bundle',
            priceBundle:    'Magento_Bundle/js/price-bundle',
            slide:          'Magento_Bundle/js/slide',
            productSummary: 'Magento_Bundle/js/product-summary'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftOptions:    'Magento_GiftMessage/js/gift-options',
            'Magento_GiftMessage/gift-options':    'Magento_GiftMessage/js/gift-options'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [],
    shim: {
        'chartjs/chartjs-adapter-moment': ['moment'],
        'tiny_mce_5/tinymce.min': {
            exports: 'tinyMCE'
        }
    },
    paths: {
        'ui/template': 'Magento_Ui/templates'
    },
    map: {
        '*': {
            uiElement:      'Magento_Ui/js/lib/core/element/element',
            uiCollection:   'Magento_Ui/js/lib/core/collection',
            uiComponent:    'Magento_Ui/js/lib/core/collection',
            uiClass:        'Magento_Ui/js/lib/core/class',
            uiEvents:       'Magento_Ui/js/lib/core/events',
            uiRegistry:     'Magento_Ui/js/lib/registry/registry',
            consoleLogger:  'Magento_Ui/js/lib/logger/console-logger',
            uiLayout:       'Magento_Ui/js/core/renderer/layout',
            buttonAdapter:  'Magento_Ui/js/form/button-adapter',
            chartJs:        'chartjs/Chart.min',
            'chart.js':     'chartjs/Chart.min',
            tinymce:        'tiny_mce_5/tinymce.min',
            wysiwygAdapter: 'mage/adminhtml/wysiwyg/tiny_mce/tinymce5Adapter'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            pageCache:  'Magento_PageCache/js/page-cache'
        }
    },
    deps: ['Magento_PageCache/js/form-key-provider']
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            captcha: 'Magento_Captcha/js/captcha',
            'Magento_Captcha/captcha': 'Magento_Captcha/js/captcha'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            configurable: 'Magento_ConfigurableProduct/js/configurable'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Magento_ConfigurableProduct/js/catalog-add-to-cart-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            multiShipping: 'Magento_Multishipping/js/multi-shipping',
            orderOverview: 'Magento_Multishipping/js/overview',
            payment: 'Magento_Multishipping/js/payment',
            billingLoader: 'Magento_Checkout/js/checkout-loader',
            cartUpdate: 'Magento_Checkout/js/action/update-shopping-cart',
            multiShippingBalance: 'Magento_Multishipping/js/multi-shipping-balance'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            recentlyViewedProducts: 'Magento_Reports/js/recently-viewed'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/quote': {
                'Magento_InventoryInStorePickupFrontend/js/model/quote-ext': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'Magento_InventoryInStorePickupFrontend/js/view/shipping-information-ext': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Magento_InventoryInStorePickupFrontend/js/model/checkout-data-resolver-ext': true
            },
            'Magento_Checkout/js/checkout-data': {
                'Magento_InventoryInStorePickupFrontend/js/checkout-data-ext': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Magento_InventorySwatchesFrontendUi/js/swatch-renderer': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            subscriptionStatusResolver: 'Magento_Newsletter/js/subscription-status-resolver',
            newsletterSignUp:  'Magento_Newsletter/js/newsletter-sign-up'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-payment-method': {
                'Magento_SalesRule/js/action/select-payment-method-mixin': true
            },
            'Magento_Checkout/js/model/shipping-save-processor': {
                'Magento_SalesRule/js/model/shipping-save-processor-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Magento_SalesRule/js/model/place-order-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'slick': 'Magento_PageBuilder/js/resource/slick/slick',
            'jarallax': 'Magento_PageBuilder/js/resource/jarallax/jarallax',
            'jarallaxVideo': 'Magento_PageBuilder/js/resource/jarallax/jarallax-video',
            'Magento_PageBuilder/js/resource/vimeo/player': 'vimeo/player',
            'Magento_PageBuilder/js/resource/vimeo/vimeo-wrapper': 'vimeo/vimeo-wrapper',
            'jarallax-wrapper': 'Magento_PageBuilder/js/resource/jarallax/jarallax-wrapper'
        }
    },
    shim: {
        'Magento_PageBuilder/js/resource/slick/slick': {
            deps: ['jquery']
        },
        'Magento_PageBuilder/js/resource/jarallax/jarallax-video': {
            deps: ['jarallax-wrapper', 'vimeoWrapper']
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    shim: {
        cardinaljs: {
            exports: 'Cardinal'
        },
        cardinaljsSandbox: {
            exports: 'Cardinal'
        }
    },
    paths: {
        cardinaljsSandbox: 'https://includestest.ccdc02.com/cardinalcruise/v1/songbird',
        cardinaljs: 'https://songbird.cardinalcommerce.com/edge/v1/songbird'
    }
};


require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Customer/js/customer-data': {
                'Magento_Persistent/js/view/customer-data-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            loadPlayer: 'Magento_ProductVideo/js/load-player',
            fotoramaVideoEvents: 'Magento_ProductVideo/js/fotorama-add-video-events'
        }
    },
    shim: {
        vimeoAPI: {}
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Magento_CheckoutAgreements/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Magento_CheckoutAgreements/js/model/set-payment-information-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'Magento_ReCaptchaCheckout/js/model/place-order-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/*eslint strict: ["error", "global"]*/

'use strict';

var config = {
    config: {
        mixins: {
            'Magento_Ui/js/view/messages': {
                'Magento_ReCaptchaFrontendUi/js/ui-messages-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method': {
                'Magento_ReCaptchaPaypal/js/payflowpro-method-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'jquery': {
                'Magento_ReCaptchaWebapiUi/js/jquery-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            mageTranslationDictionary: 'Magento_Translation/js/mage-translation-dictionary'
        }
    },
    deps: [
        'mageTranslationDictionary'
    ]
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            editTrigger: 'mage/edit-trigger',
            addClass: 'Magento_Translation/js/add-class',
            'Magento_Translation/add-class': 'Magento_Translation/js/add-class'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            configurableVariationQty: 'Magento_InventoryConfigurableProductFrontendUi/js/configurable-variation-qty'
        }
    },
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Magento_InventoryConfigurableProductFrontendUi/js/configurable': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/payment/list': {
                'Magento_PaypalCaptcha/js/view/payment/list-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method': {
                'Magento_PaypalCaptcha/js/view/payment/method-renderer/payflowpro-method-mixin': true
            },
            'Magento_Captcha/js/view/checkout/defaultCaptcha': {
                'Magento_PaypalCaptcha/js/view/checkout/defaultCaptcha-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'taxToggle': 'Magento_Weee/js/tax-toggle',
            'Magento_Weee/tax-toggle': 'Magento_Weee/js/tax-toggle'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            wishlist:       'Magento_Wishlist/js/wishlist',
            addToWishlist:  'Magento_Wishlist/js/add-to-wishlist',
            wishlistSearch: 'Magento_Wishlist/js/search'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'Amasty_Conditions/js/model/shipping-rates-validation-rules-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/*jshint browser:true jquery:true*/
var amasty_mixin_enabled = !window.amasty_checkout_disabled,
    config;

config = {
    "map": {"*": {}},
    config: {
        mixins: {
            'Magento_Checkout/js/model/new-customer-address': {
                'Amasty_Checkout/js/model/new-customer-address-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/action/get-payment-information': {
                'Amasty_Checkout/js/action/get-payment-information-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/payment/list': {
                'Amasty_Checkout/js/view/payment/list': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Amasty_Checkout/js/view/summary/abstract-total': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/step-navigator': {
                'Amasty_Checkout/js/model/step-navigator-mixin': amasty_mixin_enabled
            },
            'Magento_Paypal/js/action/set-payment-method': {
                'Amasty_Checkout/js/action/set-payment-method-mixin': amasty_mixin_enabled
            },
            'Magento_CheckoutAgreements/js/model/agreements-assigner': {
                'Amasty_Checkout/js/model/agreements-assigner-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary': {
                'Amasty_Checkout/js/view/summary-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/shipping': {
                'Amasty_Checkout/js/view/shipping-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary/cart-items': {
                'Amasty_Checkout/js/view/summary/cart-items-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/payment/additional-validators': {
                'Amasty_Checkout/js/model/payment-validators/additional-validators-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Amasty_Checkout/js/model/checkout-data-resolver-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/shipping-rates-validator': {
                'Amasty_Checkout/js/model/shipping-rates-validator-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Amasty_Checkout/js/action/set-shipping-information-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/full-screen-loader': {
                'Amasty_Checkout/js/model/full-screen-loader-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/payment': {
                'Amasty_Checkout/js/view/payment-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/payment-service': {
                'Amasty_Checkout/js/model/payment-service-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/address-converter': {
                'Amasty_Checkout/js/model/address-converter-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/action/select-shipping-address': {
                'Amasty_Checkout/js/action/select-shipping-address-mixin': amasty_mixin_enabled
            },
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'Amasty_Checkout/js/view/payment/method-renderer/in-context/checkout-express-mixin':
                    amasty_mixin_enabled
            },
            'Magento_Braintree/js/view/payment/method-renderer/paypal': {
                'Amasty_Checkout/js/view/payment/method-renderer/braintree/paypal-mixin':
                    amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/billing-address': {
                'Amasty_Checkout/js/view/billing-address-mixin': amasty_mixin_enabled
            }
        }
    }
};
if (amasty_mixin_enabled) {
    config["map"]["*"] = {
        checkoutCollapsibleSteps: 'Amasty_Checkout/js/view/checkout/design/collapsible-steps',
        summaryWidget: 'Amasty_Checkout/js/view/summary/summary-widget',
        stickyWidget: 'Amasty_Checkout/js/view/summary/sticky-widget',
        "Magento_Checkout/template/payment-methods/list.html": 'Amasty_Checkout/template/payment-methods/list.html',
        "Magento_Checkout/template/billing-address/details.html":
            'Amasty_Checkout/template/onepage/billing-address/details.html',
        "Magento_Checkout/template/shipping-address/address-renderer/default.html":
            'Amasty_Checkout/template/onepage/shipping/shipping-address/address-renderer/default.html'
    };
}

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/summary/item/details/thumbnail': {  // Target module
                'Amasty_Promo/js/checkout/sidebar-image-update': true  // Extender module
            },
            'Magento_Checkout/js/view/summary/cart-items': {
                'Amasty_Promo/js/checkout/cart-items-counter-update': true
            },
            'Magento_Theme/js/view/messages' : {
                'Amasty_Promo/js/view/messages' : true
            }
        }
    },
    map: {
        '*': {
            configurable: 'Amasty_Promo/js/type/configurable'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Josequal_CheckoutSteps/js/order/set-shipping-information-mixin': true
            }
        }
    }
};
require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            'Magento_Catalog/js/price-utils' : 'Lillik_PriceDecimal/js/price-utils'
        }
    }
};
require.config(config);
})();
(function() {
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 var config = {
 	map: {
 		'*': {
 			lofallOwlCarousel: 'Lof_All/lib/owl.carousel/owl.carousel.min',
 			lofallBootstrap: 'Lof_All/lib/bootstrap/js/bootstrap.min',
 			lofallColorbox: 'Lof_All/lib/colorbox/jquery.colorbox.min',
 			lofallFancybox: 'Lof_All/lib/fancybox/jquery.fancybox.pack',
 			lofallFancyboxMouseWheel: 'Lof_All/lib/fancybox/jquery.mousewheel-3.0.6.pack'
 		}
 	},
 	shim: {
        'Lof_All/lib/bootstrap/js/bootstrap.min': {
            'deps': ['jquery']
        },
        'Lof_All/lib/bootstrap/js/bootstrap': {
            'deps': ['jquery']
        },
        'Lof_All/lib/owl.carousel/owl.carousel': {
            'deps': ['jquery']
        },
        'Lof_All/lib/owl.carousel/owl.carousel.min': {
        	'deps': ['jquery']
        },
        'Lof_All/lib/fancybox/jquery.fancybox': {
            'deps': ['jquery']  
        },
        'Lof_All/lib/fancybox/jquery.fancybox.pack': {
            'deps': ['jquery']  
        },
        'Lof_All/lib/colorbox/jquery.colorbox': {
            'deps': ['jquery']  
        },
        'Lof_All/lib/colorbox/jquery.colorbox.min': {
            'deps': ['jquery']  
        }
    }
 };
require.config(config);
})();
(function() {
/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

var config = {
    paths: {
        mageWorkshop_detailedReview_reviewFilters: 'MageWorkshop_DetailedReview/js/review-filters',
        mageWorkshop_detailedReview_reviewRating: 'MageWorkshop_DetailedReview/js/review-rating',
        mageWorkshop_detailedReview_listReviews: 'MageWorkshop_DetailedReview/js/list-reviews',
        mageWorkshop_detailedReview_reviewForm: 'MageWorkshop_DetailedReview/js/review-form',
        mageWorkshop_detailedReview_reviewCustomValidateLength: 'MageWorkshop_DetailedReview/js/review-custom-validate-length'
    },
    config: {
        mixins: {
            'Magento_Review/js/process-reviews': {
                'MageWorkshop_DetailedReview/js/process-reviews-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

var config = {
    paths: {
        checkAllRules: 'MageWorkshop_CustomerPermissions/js/check-all-rules',
        mageWorkshop_customerPermissions_verifyBuyer: 'MageWorkshop_CustomerPermissions/js/verify-buyer',
        mageWorkshop_customerPermissions_banned: 'MageWorkshop_CustomerPermissions/js/banned'
    }
};
require.config(config);
})();
(function() {
/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

var config = {
    paths: {
        mageWorkshop_imageLoader_reviewFormImageValidation: 'MageWorkshop_ImageLoader/js/review-form-image-validation',
        mageWorkshop_imageLoader_listReviewsCarousel:       'MageWorkshop_ImageLoader/js/list-reviews-carousel',
        mageWorkshop_imageLoader_owlCarousel:               'MageWorkshop_ImageLoader/js/libs/owl.carousel',
        mageWorkshop_imageLoader_fancyBox:                  'MageWorkshop_ImageLoader/js/libs/jquery.fancybox'
    }
};

require.config(config);
})();
(function() {
/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

var config = {
    paths: {
        MageWorkshop_Seo : "MageWorkshop_Seo/js/mageworkshop-seo"
    }
};
require.config(config);
})();
(function() {
/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

var config = {
    map: {
        '*': {

        }
    }
};

require.config(config);
})();
(function() {
/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

var config = {
    map: {
        '*': {
            mageWorkshop_voting_voting: 'MageWorkshop_Voting/js/voting'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    paths: {
         "magebeesFlipper": "Magebees_ImageFlipper/js/magebees_flipper"
    },
    shim: {
        'magebeesFlipper': {
            deps: ['jquery']
        }
        
        
    }
};




require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Core
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        'mageplaza/core/jquery/popup': 'Mageplaza_Core/js/jquery.magnific-popup.min',
        'mageplaza/core/owl.carousel': 'Mageplaza_Core/js/owl.carousel.min',
        'mageplaza/core/bootstrap': 'Mageplaza_Core/js/bootstrap.min',
        mpIonRangeSlider: 'Mageplaza_Core/js/ion.rangeSlider.min',
        touchPunch: 'Mageplaza_Core/js/jquery.ui.touch-punch.min',
        mpDevbridgeAutocomplete: 'Mageplaza_Core/js/jquery.autocomplete.min'
    },
    shim: {
        "mageplaza/core/jquery/popup": ["jquery"],
        "mageplaza/core/owl.carousel": ["jquery"],
        "mageplaza/core/bootstrap": ["jquery"],
        mpIonRangeSlider: ["jquery"],
        mpDevbridgeAutocomplete: ["jquery"],
        touchPunch: ['jquery', 'jquery-ui-modules/core', 'jquery-ui-modules/mouse', 'jquery-ui-modules/widget']
    }
};

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterPopup
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        mpPopup: 'Mageplaza_BetterPopup/js/popup',
        bioEp: 'Mageplaza_BetterPopup/js/lib/bioep.min',
        fireworks: 'Mageplaza_BetterPopup/js/lib/fireworks.min'
    }
};

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_InstagramFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        'mageplaza/instagram': 'Mageplaza_InstagramFeed/js/instagram'
    }
};

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Reports
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        gridstack: 'Mageplaza_Reports/js/lib/gridstack',
        gridstackJqueryUi: 'Mageplaza_Reports/js/lib/gridstack.jQueryUI',
        daterangepicker: 'Mageplaza_Reports/js/lib/daterangepicker.min',
        chartBundle: 'Mageplaza_Reports/js/lib/Chart.bundle.min',
        initDateRange: 'Mageplaza_Reports/js/dashboard/initDateRange',
        initGridStack: 'Mageplaza_Reports/js/dashboard/initGridStack',
        initChart: 'Mageplaza_Reports/js/dashboard/initChart',
    },
    map: {
        gridstack: {
            'lodash': 'underscore'
        },
        gridstackJqueryUi: {
            'lodash': 'underscore'
        },
        '*': {
            moment: 'Mageplaza_Reports/js/lib/moment.min'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Smtp
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
var config = {};
if (typeof window.AVADA_EM !== 'undefined') {
    config = {
        config: {
            mixins: {
                'Magento_Checkout/js/view/billing-address': {
                    'Mageplaza_Smtp/js/view/billing-address-mixins' : true
                },
                'Magento_Checkout/js/view/shipping': {
                    'Mageplaza_Smtp/js/view/shipping-mixins' : true
                }
            }
        }
    };
}

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_SocialLogin
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        socialProvider: 'Mageplaza_SocialLogin/js/provider',
        socialPopupForm: 'Mageplaza_SocialLogin/js/popup'
    },
    map: {
        '*': {
            'Magento_Checkout/js/proceed-to-checkout': 'Mageplaza_SocialLogin/js/proceed-to-checkout'
        }
    }
};

require.config(config);
})();
(function() {
var config = {

	map: {
		'*': {
			'easing': 'magepow/easing',
			'slick': 'magepow/slick',
			'gridSlider': 'magepow/grid-slider',
		},
	},

	paths: {
		'magepow/easing'		: 'Magepow_Core/js/plugin/jquery.easing.min',
		'magepow/slick'			: 'Magepow_Core/js/plugin/slick.min',
		'magepow/grid-slider'	: 'Magepow_Core/js/grid-slider',
	},

	shim: {
		'magepow/easing': {
			deps: ['jquery']
		},
		'magepow/slick': {
			deps: ['jquery']
		}
	}

};

require.config(config);
})();
(function() {
var config = {

	paths: {
		'magepow/lazyload'			: 'Magepow_SpeedOptimizer/js/plugin/lazyload.min',
	},

	shim: {
		'magepow/lazyload': {
			deps: ['jquery']
		}
	}

};

require.config(config);
})();
(function() {
var config = {
    "map": {
        "*": {
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': 'Mageside_SubscribeAtCheckout/js/model/shipping-save-processor/payload-extender-override'
        }
    }
};
require.config(config);
})();
(function() {
var config = {
	map: {
		'*': {
			'magestore/note': 'Magestore_Bannerslider/js/jquery/slider/jquery-ads-note',
			'magestore/impress': 'Magestore_Bannerslider/js/report/impress',
			'magestore/clickbanner': 'Magestore_Bannerslider/js/report/clickbanner',
		},
	},
	paths: {
		'magestore/flexslider': 'Magestore_Bannerslider/js/jquery/slider/jquery-flexslider-min',
		'magestore/evolutionslider': 'Magestore_Bannerslider/js/jquery/slider/jquery-slider-min',
		'magestore/popup': 'Magestore_Bannerslider/js/jquery.bpopup.min',
	},
	shim: {
		'magestore/flexslider': {
			deps: ['jquery']
		},
		'magestore/evolutionslider': {
			deps: ['jquery']
		},
		'magestore/zebra-tooltips': {
			deps: ['jquery']
		},
	}
};

require.config(config);
})();
(function() {

var config = {
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'MagicToolbox_MagicZoomPlus/js/configurable': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'MagicToolbox_MagicZoomPlus/js/swatch-renderer': true
            },
            /* NOTE: for Magento v2.0.x */
            'Magento_Swatches/js/SwatchRenderer': {
                'MagicToolbox_MagicZoomPlus/js/swatch-renderer': true
            }
        }
    },
    map: {
        '*': {
            magicToolboxThumbSwitcher: 'MagicToolbox_MagicZoomPlus/js/thumb-switcher',
            loadPlayer: 'MagicToolbox_MagicZoomPlus/js/load-player',
            vimeoPlayer: 'https://player.vimeo.com/api/player.js'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    paths: {
        "intlTelInput": 'MaxMage_InternationalTelephoneInput/js/intlTelInput',
        "intlTelInputUtils": 'MaxMage_InternationalTelephoneInput/js/utils',
        "internationalTelephoneInput": 'MaxMage_InternationalTelephoneInput/js/internationalTelephoneInput'
    },

    shim: {
        'intlTelInput': {
            'deps':['jquery', 'knockout']
        },
        'internationalTelephoneInput': {
            'deps':['jquery', 'intlTelInput']
        }
    }
};
require.config(config);
})();
(function() {
var config = {
    "map": {
        '*': {
            'nwdthemes/jquery.easing' : 'Nwdthemes_Base/js/jquery.easing',
            'nwdthemes/owl.carousel' : 'Nwdthemes_Base/js/owl.carousel',
            'nwdthemes/owl.carousel.min' : 'Nwdthemes_Base/js/owl.carousel.min'
        }
    },
    "shim": {
        "nwdthemes/owl.carousel": ["jquery"],
        "nwdthemes/owl.carousel.min": ["jquery"]
    }
};
require.config(config);
})();
(function() {
var config = {
    paths: {
        revolutionTools:        'Nwdthemes_Revslider/public/assets/js/rbtools.min',
        rs6:                    'Nwdthemes_Revslider/public/assets/js/rs6.min',
        vimeoPlayer:            'Nwdthemes_Revslider/public/assets/js/vimeo.player.min'
    },
    shim: {
        revolutionTools: {
            deps: ['jquery']
        },
        rs6: {
            deps: ['jquery', 'revolutionTools']
        }
    }
};
require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/step-navigator': {
                'PayPal_Braintree/js/model/step-navigator-mixin': true
            },
            'PayPal_Braintree/js/view/payment/method-renderer/cc-form': {
                'PayPal_Braintree/js/reCaptcha/braintree-cc-method-mixin': true
            }
        }
    },
    map: {
        '*': {
            braintreeCheckoutPayPalAdapter: 'PayPal_Braintree/js/view/payment/adapter'
        }
    },
};

require.config(config);
})();
(function() {
/**
 * Config to pull in all the relevant Braintree JS SDKs
 * @type {{paths: {braintreePayPalInContextCheckout: string, braintreePayPalCheckout: string, braintreeVenmo: string, braintreeHostedFields: string, braintreeDataCollector: string, braintreeThreeDSecure: string, braintreeGooglePay: string, braintreeApplePay: string, braintreeAch: string, braintreeLpm: string, googlePayLibrary: string}, map: {"*": {braintree: string}}}}
 */
var config = {
    map: {
        '*': {
            braintree: 'https://js.braintreegateway.com/web/3.79.1/js/client.min.js',
        }
    },

    paths: {
        "braintreePayPalCheckout": "https://js.braintreegateway.com/web/3.79.1/js/paypal-checkout.min",
        "braintreeHostedFields": "https://js.braintreegateway.com/web/3.79.1/js/hosted-fields.min",
        "braintreeDataCollector": "https://js.braintreegateway.com/web/3.79.1/js/data-collector.min",
        "braintreeThreeDSecure": "https://js.braintreegateway.com/web/3.79.1/js/three-d-secure.min",
        "braintreeApplePay": 'https://js.braintreegateway.com/web/3.79.1/js/apple-pay.min',
        "braintreeGooglePay": 'https://js.braintreegateway.com/web/3.79.1/js/google-payment.min',
        "braintreeVenmo": 'https://js.braintreegateway.com/web/3.79.1/js/venmo.min',
        "braintreeAch": "https://js.braintreegateway.com/web/3.79.1/js/us-bank-account.min",
        "braintreeLpm": "https://js.braintreegateway.com/web/3.79.1/js/local-payment.min",
        "googlePayLibrary": "https://pay.google.com/gp/p/js/pay",
        "braintreePayPalInContextCheckout": "https://www.paypalobjects.com/api/checkout"
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            checkoutjs: 'Rivage_GtmExtension/js/checkoutinfo',
            ga4_gtmextension_data: 'Rivage_GtmExtension/js/ga4_gtmextension_data'
        }
    }

};
require.config(config);
})();
(function() {
/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    map: {
        '*': {
            'stripejs': 'https://js.stripe.com/v3/',
            'stripe_payments': 'StripeIntegration_Payments/js/stripe_payments',
            'stripe_payments_express': 'StripeIntegration_Payments/js/stripe_payments_express',
            'klarnapi': 'https://x.klarnacdn.net/kp/lib/v1/api.js'
        }
    },
    config: {
        mixins: {
            'Magento_Ui/js/view/messages': {
                'StripeIntegration_Payments/js/messages-mixin': true
            },
            'MSP_ReCaptcha/js/ui-messages-mixin': {
                'StripeIntegration_Payments/js/messages-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    map: {
        '*': {
            'stripejs': 'https://js.stripe.com/v3/',
            'stripe_payments': 'StripeIntegration_Payments/js/stripe_payments',
            'klarnapi': 'https://x.klarnacdn.net/kp/lib/v1/api.js'
        }
    },
    config: {
        mixins: {
            'Magento_Tax/js/view/checkout/summary/grand-total': {
                'StripeIntegration_Payments/js/mixins/checkout/summary/grand_total': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            stripejs: 'https://js.stripe.com/v3/'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Tabby_Checkout/js/mixin/swatch-renderer': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Yireo_Webp2/js/swatch-renderer-mixin': true
            },
            'mage/gallery/gallery': {
                'Yireo_Webp2/js/gallery-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [
        'Magento_Theme/js/theme'
    ]
};

require.config(config);
})();
(function() {
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

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            checkoutBalance:    'Magento_Customer/js/checkout-balance',
            address:            'Magento_Customer/js/address',
            changeEmailPassword: 'Magento_Customer/js/change-email-password',
            passwordStrengthIndicator: 'Magento_Customer/js/password-strength-indicator',
            zxcvbn: 'Magento_Customer/js/zxcvbn',
            addressValidation: 'Magento_Customer/js/addressValidation'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            quickSearch: 'Magento_Search/js/form-mini',
            'Magento_Search/form-mini': 'Magento_Search/js/form-mini'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            compareList:            'Magento_Catalog/js/list',
            relatedProducts:        'Magento_Catalog/js/related-products',
            upsellProducts:         'Magento_Catalog/js/upsell-products',
            productListToolbarForm: 'Magento_Catalog/js/product/list/toolbar',
            catalogGallery:         'Magento_Catalog/js/gallery',
            catalogAddToCart:       'Magento_Catalog/js/catalog-add-to-cart'
        }
    },
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Magento_Catalog/js/product/breadcrumbs': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftMessage:    'Magento_Sales/js/gift-message',
            ordersReturns:  'Magento_Sales/js/orders-returns',
            'Magento_Sales/gift-message':    'Magento_Sales/js/gift-message',
            'Magento_Sales/orders-returns':  'Magento_Sales/js/orders-returns'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            discountCode:           'Magento_Checkout/js/discount-codes',
            shoppingCart:           'Magento_Checkout/js/shopping-cart',
            regionUpdater:          'Magento_Checkout/js/region-updater',
            sidebar:                'Magento_Checkout/js/sidebar',
            checkoutLoader:         'Magento_Checkout/js/checkout-loader',
            checkoutData:           'Magento_Checkout/js/checkout-data',
            proceedToCheckout:      'Magento_Checkout/js/proceed-to-checkout',
            catalogAddToCart:       'Magento_Catalog/js/catalog-add-to-cart'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            multiShipping: 'Magento_Multishipping/js/multi-shipping',
            orderOverview: 'Magento_Multishipping/js/overview',
            payment: 'Magento_Multishipping/js/payment',
            billingLoader: 'Magento_Checkout/js/checkout-loader',
            cartUpdate: 'Magento_Checkout/js/action/update-shopping-cart'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            subscriptionStatusResolver: 'Magento_Newsletter/js/subscription-status-resolver',
            newsletterSignUp:  'Magento_Newsletter/js/newsletter-sign-up'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-payment-method': {
                'Magento_SalesRule/js/action/select-payment-method-mixin': true
            },
            'Magento_Checkout/js/model/shipping-save-processor': {
                'Magento_SalesRule/js/model/shipping-save-processor-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Magento_SalesRule/js/model/place-order-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'taxToggle': 'Magento_Weee/js/tax-toggle',
            'Magento_Weee/tax-toggle': 'Magento_Weee/js/tax-toggle'
        }
    }
};

require.config(config);
})();
(function() {
/*jshint browser:true jquery:true*/
var amasty_mixin_enabled = !window.amasty_checkout_disabled,
    config;

config = {
    "map": {"*": {}},
    config: {
        mixins: {
            'Magento_Checkout/js/model/new-customer-address': {
                'Amasty_Checkout/js/model/new-customer-address-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/action/get-payment-information': {
                'Amasty_Checkout/js/action/get-payment-information-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/payment/list': {
                'Amasty_Checkout/js/view/payment/list': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Amasty_Checkout/js/view/summary/abstract-total': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/step-navigator': {
                'Amasty_Checkout/js/model/step-navigator-mixin': amasty_mixin_enabled
            },
            'Magento_Paypal/js/action/set-payment-method': {
                'Amasty_Checkout/js/action/set-payment-method-mixin': amasty_mixin_enabled
            },
            'Magento_CheckoutAgreements/js/model/agreements-assigner': {
                'Amasty_Checkout/js/model/agreements-assigner-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary': {
                'Amasty_Checkout/js/view/summary-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/shipping': {
                'Amasty_Checkout/js/view/shipping-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary/cart-items': {
                'Amasty_Checkout/js/view/summary/cart-items-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/payment/additional-validators': {
                'Amasty_Checkout/js/model/payment-validators/additional-validators-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Amasty_Checkout/js/model/checkout-data-resolver-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/shipping-rates-validator': {
                'Amasty_Checkout/js/model/shipping-rates-validator-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Amasty_Checkout/js/action/set-shipping-information-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/full-screen-loader': {
                'Amasty_Checkout/js/model/full-screen-loader-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/payment': {
                'Amasty_Checkout/js/view/payment-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/payment-service': {
                'Amasty_Checkout/js/model/payment-service-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/address-converter': {
                'Amasty_Checkout/js/model/address-converter-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/action/select-shipping-address': {
                'Amasty_Checkout/js/action/select-shipping-address-mixin': amasty_mixin_enabled
            },
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'Amasty_Checkout/js/view/payment/method-renderer/in-context/checkout-express-mixin':
                    amasty_mixin_enabled
            },
            'Magento_Braintree/js/view/payment/method-renderer/paypal': {
                'Amasty_Checkout/js/view/payment/method-renderer/braintree/paypal-mixin':
                    amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/billing-address': {
                'Amasty_Checkout/js/view/billing-address-mixin': amasty_mixin_enabled
            }
        }
    }
};
if (amasty_mixin_enabled) {
    config["map"]["*"] = {
        checkoutCollapsibleSteps: 'Amasty_Checkout/js/view/checkout/design/collapsible-steps',
        summaryWidget: 'Amasty_Checkout/js/view/summary/summary-widget',
        stickyWidget: 'Amasty_Checkout/js/view/summary/sticky-widget',
        "Magento_Checkout/template/payment-methods/list.html": 'Amasty_Checkout/template/payment-methods/list.html',
        "Magento_Checkout/template/billing-address/details.html":
            'Amasty_Checkout/template/onepage/billing-address/details.html',
        "Magento_Checkout/template/shipping-address/address-renderer/default.html":
            'Amasty_Checkout/template/onepage/shipping/shipping-address/address-renderer/default.html'
    };
}

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterPopup
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        mpPopup: 'Mageplaza_BetterPopup/js/popup',
        bioEp: 'Mageplaza_BetterPopup/js/lib/bioep.min',
        fireworks: 'Mageplaza_BetterPopup/js/lib/fireworks.min'
    }
};

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_InstagramFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        'mageplaza/instagram': 'Mageplaza_InstagramFeed/js/instagram'
    },
    shim: {
        'mageplaza/instagram': {
            deps: ['jquery','lazyLoad_rivage']
        }
    },
};

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_SocialLogin
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        socialProvider: 'Mageplaza_SocialLogin/js/provider',
        socialPopupForm: 'Mageplaza_SocialLogin/js/popup'
    },
    map: {
        '*': {
            'Magento_Checkout/js/proceed-to-checkout': 'Mageplaza_SocialLogin/js/proceed-to-checkout'
        }
    }
};

require.config(config);
})();



})(require);
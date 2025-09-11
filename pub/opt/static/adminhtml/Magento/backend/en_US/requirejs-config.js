(function(require){
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_ReleaseNotification/js/modal/component': {
                'Magento_AdminAnalytics/js/release-notification/modal/component-mixin': true
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
            systemMessageDialog: 'Magento_AdminNotification/system/notification',
            toolbarEntry:   'Magento_AdminNotification/toolbar_entry'
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
    'shim': {
        'extjs/ext-tree': [
            'prototype'
        ],
        'extjs/ext-tree-checkbox': [
            'extjs/ext-tree',
            'extjs/defaults'
        ],
        'jquery/editableMultiselect/js/jquery.editable': [
            'jquery'
        ]
    },
    'bundles': {
        'js/theme': [
            'globalNavigation',
            'globalSearch',
            'modalPopup',
            'useDefault',
            'loadingPopup',
            'collapsable'
        ]
    },
    'map': {
        '*': {
            'translateInline':                    'mage/translate-inline',
            'form':                               'mage/backend/form',
            'button':                             'mage/backend/button',
            'accordion':                          'mage/accordion',
            'actionLink':                         'mage/backend/action-link',
            'validation':                         'mage/backend/validation',
            'notification':                       'mage/backend/notification',
            'loader':                             'mage/loader_old',
            'loaderAjax':                         'mage/loader_old',
            'floatingHeader':                     'mage/backend/floating-header',
            'suggest':                            'mage/backend/suggest',
            'mediabrowser':                       'jquery/jstree/jquery.jstree',
            'tabs':                               'mage/backend/tabs',
            'treeSuggest':                        'mage/backend/tree-suggest',
            'calendar':                           'mage/calendar',
            'dropdown':                           'mage/dropdown_old',
            'collapsible':                        'mage/collapsible',
            'menu':                               'mage/backend/menu',
            'jstree':                             'jquery/jstree/jquery.jstree',
            'jquery-ui-modules/widget':           'jquery/ui',
            'jquery-ui-modules/core':             'jquery/ui',
            'jquery-ui-modules/accordion':        'jquery/ui',
            'jquery-ui-modules/autocomplete':     'jquery/ui',
            'jquery-ui-modules/button':           'jquery/ui',
            'jquery-ui-modules/datepicker':       'jquery/ui',
            'jquery-ui-modules/dialog':           'jquery/ui',
            'jquery-ui-modules/draggable':        'jquery/ui',
            'jquery-ui-modules/droppable':        'jquery/ui',
            'jquery-ui-modules/effect-blind':     'jquery/ui',
            'jquery-ui-modules/effect-bounce':    'jquery/ui',
            'jquery-ui-modules/effect-clip':      'jquery/ui',
            'jquery-ui-modules/effect-drop':      'jquery/ui',
            'jquery-ui-modules/effect-explode':   'jquery/ui',
            'jquery-ui-modules/effect-fade':      'jquery/ui',
            'jquery-ui-modules/effect-fold':      'jquery/ui',
            'jquery-ui-modules/effect-highlight': 'jquery/ui',
            'jquery-ui-modules/effect-scale':     'jquery/ui',
            'jquery-ui-modules/effect-pulsate':   'jquery/ui',
            'jquery-ui-modules/effect-shake':     'jquery/ui',
            'jquery-ui-modules/effect-slide':     'jquery/ui',
            'jquery-ui-modules/effect-transfer':  'jquery/ui',
            'jquery-ui-modules/effect':           'jquery/ui',
            'jquery-ui-modules/menu':             'jquery/ui',
            'jquery-ui-modules/mouse':            'jquery/ui',
            'jquery-ui-modules/position':         'jquery/ui',
            'jquery-ui-modules/progressbar':      'jquery/ui',
            'jquery-ui-modules/resizable':        'jquery/ui',
            'jquery-ui-modules/selectable':       'jquery/ui',
            'jquery-ui-modules/slider':           'jquery/ui',
            'jquery-ui-modules/sortable':         'jquery/ui',
            'jquery-ui-modules/spinner':          'jquery/ui',
            'jquery-ui-modules/tabs':             'jquery/ui',
            'jquery-ui-modules/tooltip':          'jquery/ui'
        }
    },
    'deps': [
        'js/theme',
        'mage/backend/bootstrap',
        'mage/adminhtml/globals'
    ],
    config: {
        mixins: {
            'jquery/jquery-ui': {
                'jquery/patches/jquery-ui-sortable': true
            }
        }
    },
    'paths': {
        'jquery/ui': 'jquery/jquery-ui'
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
            'mediaUploader':  'Magento_Backend/js/media-uploader'
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
            eavInputTypes: 'Magento_Eav/js/input-types'
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
        'customer/template': 'Magento_Customer/templates'
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
            rolesTree: 'Magento_User/js/roles-tree',
            deleteUserAccount: 'Magento_User/js/delete-user-account'
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
            folderTree: 'Magento_Cms/js/folder-tree'
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
            popupWindow:            'mage/popup-window',
            confirmRedirect:        'Magento_Security/js/confirm-redirect'
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
            categoryForm:         'Magento_Catalog/catalog/category/form',
            newCategoryDialog:    'Magento_Catalog/js/new-category-dialog',
            categoryTree:         'Magento_Catalog/js/category-tree',
            productGallery:       'Magento_Catalog/js/product-gallery',
            baseImage:            'Magento_Catalog/catalog/base-image-uploader',
            productAttributes:    'Magento_Catalog/catalog/product-attributes',
            categoryCheckboxTree: 'Magento_Catalog/js/category-checkbox-tree'
        }
    },
    deps: [
        'Magento_Catalog/catalog/product'
    ],
    config: {
        mixins: {
            'Magento_Catalog/js/components/use-parent-settings/select': {
                'Magento_Catalog/js/components/use-parent-settings/toggle-disabled-mixin': true
            },
            'Magento_Catalog/js/components/use-parent-settings/textarea': {
                'Magento_Catalog/js/components/use-parent-settings/toggle-disabled-mixin': true
            },
            'Magento_Catalog/js/components/use-parent-settings/single-checkbox': {
                'Magento_Catalog/js/components/use-parent-settings/toggle-disabled-mixin': true
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
            integration: 'Magento_Integration/js/integration'
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
            orderEditDialog: 'Magento_Sales/order/edit/message'
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
            testConnection: 'Magento_AdvancedSearch/js/testconnection'
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
            groupedProduct: 'Magento_GroupedProduct/js/grouped-product'
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
            /* Include our Knockout Sortable wrapper */
            'pagebuilder/ko-dropzone': 'Magento_PageBuilder/js/resource/dropzone/knockout-dropzone',

            /* Utilities */
            'google-map': 'Magento_PageBuilder/js/utils/map',
            'object-path': 'Magento_PageBuilder/js/resource/object-path',
            'html2canvas': 'Magento_PageBuilder/js/resource/html2canvas/html2canvas.min',
            'csso': 'Magento_PageBuilder/js/resource/csso/csso'
        }
    },
    shim: {
        'pagebuilder/ko-sortable': {
            deps: ['jquery', 'jquery/ui', 'Magento_PageBuilder/js/resource/jquery-ui/jquery.ui.touch-punch']
        },
        'Magento_PageBuilder/js/resource/jquery/ui/jquery.ui.touch-punch': {
            deps: ['jquery/ui']
        }
    },
    config: {
        mixins: {
            'Magento_Ui/js/form/element/abstract': {
                'Magento_PageBuilder/js/form/element/conditional-disable-mixin': true,
                'Magento_PageBuilder/js/form/element/dependent-value-mixin': true
            },
            'Magento_Ui/js/lib/validation/validator': {
                'Magento_PageBuilder/js/form/element/validator-rules-mixin': true
            },
            'mage/validation': {
                'Magento_PageBuilder/js/system/config/validator-rules-mixin': true
            },
            'Magento_Ui/js/form/form': {
                'Magento_PageBuilder/js/form/form-mixin': true
            },
            'Magento_PageBuilder/js/content-type/row/appearance/default/widget': {
                'Magento_PageBuilder/js/content-type/row/appearance/default/widget-mixin': true
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
    map: {
        '*': {
            newVideoDialog:  'Magento_ProductVideo/js/new-video-dialog',
            openVideoModal:  'Magento_ProductVideo/js/video-modal'
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
            'Magento_ConfigurableProduct/js/components/dynamic-rows-configurable': {
                'Magento_InventoryConfigurableProductAdminUi/js/dynamic-rows-configurable-mixin': true
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
            'Magento_PageBuilder/js/events': {
                'Magento_PageBuilderAdminAnalytics/js/page-builder/events-mixin': true
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
        map: {
            '*': {
                triggerShippingMethodUpdate: 'Magento_InventoryInStorePickupSalesAdminUi/order/create/trigger-shipping-method-update' //eslint-disable-line max-len
            }
        },
        mixins: {
            'Magento_Sales/order/create/scripts': {
                'Magento_InventoryInStorePickupSalesAdminUi/order/create/scripts-mixin': true
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
            swatchesProductAttributes: 'Magento_Swatches/js/product-attributes'
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
var config = {
    map: {
        '*': {
            loadIcons: 'Magento_AdminAdobeIms/js/loadicons',
            adobeImsReauth: 'Magento_AdminAdobeIms/js/adobe-ims-reauth'
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
            fptAttribute: 'Magento_Weee/js/fpt-attribute'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            amastyLegacyFormFieldEnabler: 'Amasty_Affiliate/js/form/element/legacy-field-enabler'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'js/theme': {
                'Amasty_Base/js/theme': true
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
        "*": {
            amastyDraggableFieldArray:   'Amasty_Checkout/js/draggable-field-array',
            amastySectionsRate:   'Amasty_Checkout/js/reports/sections-rate',
            amCharts: 'Amasty_Checkout/vendor/amcharts/amcharts',
            amChartsSerial: 'Amasty_Checkout/vendor/amcharts/serial'
        }
    },
    shim: {
        'Amasty_Checkout/vendor/amcharts/serial': ['Amasty_Checkout/vendor/amcharts/amcharts']
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            newPointsDialog:  'Amasty_Rewards/js/add-points-dialog'
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
 		"*": {
 			lofallOwlCarousel: "Lof_All/lib/owl.carousel/owl.carousel.min",
 			lofallBootstrap: "Lof_All/lib/bootstrap/js/bootstrap.min",
 			lofallColorbox: "Lof_All/lib/colorbox/jquery.colorbox.min",
 			lofallFancybox: "Lof_All/lib/fancybox/jquery.fancybox.pack",
 			lofallFancyboxMouseWheel: "Lof_All/lib/fancybox/jquery.mousewheel-3.0.6.pack"
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
            mageWorkshop_detailedReview_formFieldsManager: 'MageWorkshop_DetailedReview/js/form/fields-manager',
            mageWorkshop_detailedReview_swatchesTypeChange: 'MageWorkshop_DetailedReview/js/type-change',
            mageWorkshop_detailedReview_reviewAdditionalFields: 'MageWorkshop_DetailedReview/js/review-additional-fields',
            mageWorkshop_detailedReview_newReviewRatingValidation: 'MageWorkshop_DetailedReview/js/review-rating-validation',
            mageWorkshop_detailedReview_fieldValidationRules: 'MageWorkshop_DetailedReview/js/field-validation-rules'
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
        'mageplaza/templates': 'Mageplaza_BetterPopup/js/templates'
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
 * @package     Mageplaza_AbandonedCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        daterangepicker: 'Mageplaza_AbandonedCart/js/lib/daterangepicker.min',
        abnChartBundle: 'Mageplaza_AbandonedCart/js/lib/Chart.bundle.min',
        twix: 'Mageplaza_AbandonedCart/js/lib/twix.min',
        moment: 'Mageplaza_AbandonedCart/js/lib/moment.min'
    },
    map: {
        daterangepicker: {
            'moment': 'Mageplaza_AbandonedCart/js/lib/moment.min'
        },
        '*': {
            moment: 'Mageplaza_AbandonedCart/js/lib/moment.min'
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
 * @package     Mageplaza_InstagramFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        'mageplaza/auth': 'Mageplaza_InstagramFeed/js/auth'
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
 * @package     Mageplaza_Seo
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    config: {
        mixins: {
            'mage/validation': {
                'Mageplaza_Seo/js/admin-config/validator-rules-mixin': true
            }
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
var config = {
    paths: {
        'mageplaza/testemail': 'Mageplaza_Smtp/js/testemail',
        'mageplaza/provider': 'Mageplaza_Smtp/js/provider'
    }
};

require.config(config);
})();
(function() {
var config = {

	paths: {
		'magepow/feed'	: 'Magepow_Core/js/feed',
	},

	shim: {
		'magepow/feed': {
			deps: ['jquery', 'magepow/slick']
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
    map: {
        '*': {
            'magestore/note': 'Magestore_Bannerslider/js/jquery/slider/jquery-ads-note',
        },
    },
    paths: {
        'magestore/flexslider': 'Magestore_Bannerslider/js/jquery/slider/jquery-flexslider-min',
        'magestore/evolutionslider': 'Magestore_Bannerslider/js/jquery/slider/jquery-slider-min',
        'magestore/zebra-tooltips': 'Magestore_Bannerslider/js/jquery/ui/zebra-tooltips',
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
    map: {
        '*': {
            magiczoomplus: 'MagicToolbox_MagicZoomPlus/js/script'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
	paths: {
		"colorpicker" : "Netbaseteam_Megamenu/js/colorpicker/js/colorpicker"
    },
    shim: {
		"colorpicker": {
			deps: ["jquery"]
		}
	}
};


require.config(config);
})();
(function() {
var config = {
    paths: {
        jscolor: 'Nwdthemes_Base/js/jscolor.min'
    },
    shim: {
        jscolor: {
            exports: 'jscolor'
        }
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
    paths: {
        revsliderAdmin:         'Nwdthemes_Revslider/admin/assets/js/admin.min',
        revsliderEditor:        'Nwdthemes_Revslider/admin/assets/js/editor.min',
        help:                   'Nwdthemes_Revslider/admin/assets/js/modules/help',
        overview:               'Nwdthemes_Revslider/admin/assets/js/modules/overview',
        tooltip:                'Nwdthemes_Revslider/admin/assets/js/modules/tooltip',
        clipboard:              'Nwdthemes_Revslider/admin/assets/js/plugins/clipboard.min',
        codemirror:             'Nwdthemes_Revslider/admin/assets/js/plugins/codemirror',
        pennerEasing:           'Nwdthemes_Revslider/admin/assets/js/plugins/penner-easing',
        select2RS:              'Nwdthemes_Revslider/admin/assets/js/plugins/select2RS.full.min',
        wavesurfer:             'Nwdthemes_Revslider/admin/assets/js/plugins/wavesurfer',
        colorPicker:            'Nwdthemes_Revslider/framework/js/color-picker.min',
        galleryBrowser:         'Nwdthemes_Revslider/framework/js/browser',
        iris:                   'Nwdthemes_Revslider/framework/js/iris.min',
        jQueryUI:               'Nwdthemes_Revslider/framework/js/jquery-ui.min',
        loading:                'Nwdthemes_Revslider/framework/js/loading',
        wpUtil:                 'Nwdthemes_Revslider/framework/js/wp-util.min',
        'jquery/file-uploader': 'jquery/fileUploader/jquery.fileupload-fp',
        prototype:              'legacy-build.min'
    },
    shim: {
        galleryBrowser: {
            deps: ['Magento_Variable/variables']
        },
        colorPicker: {
            deps: ['jQueryUI', 'iris']
        },
        iris: {
            deps: ['jQueryUI']
        },
        jQueryUI: {
            deps: ['jquery']
        },
        loading: {
            deps: ['jquery', 'revolutionTools'],
            exports: 'showWaitAMinute'
        },
        wpUtil: {
            deps: ['jquery', 'underscore']
        }
    }
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
            'mage/validation': {
                'Tabby_Checkout/js/admin-config/validator-rules-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {

var config = {
    map: {
        "*": {
            "pdfWidget":"Vnecoms_PdfPro/js/widget",
            "easypdf_plugin":"Vnecoms_PdfPro/js/editor_plugin",
            "pdfTinyMCE":"Vnecoms_PdfPro/js/tinymce",
            "vnecoms.Popular":"Vnecoms_PdfPro/js/popular",
            "vnecomsEasypdf": "Vnecoms_PdfPro/js/easypdf",
            "owlCarousel": "Vnecoms_PdfPro/jquery-owlcarousel/owl.carousel.min",
            "pdfVariables": "Vnecoms_PdfPro/variables"
        }
    },
    "shims": {
        "jquery/filer": ["jquery"],
        "jquery/owlcarousel":["jquery"],
        "owlCarousel":["jquery"]
    },
    "paths": {
        "jquery/filer": "Vnecoms_PdfPro/jquery-filer/js/jquery-filer",
        "jquery/owlcarousel": "Vnecoms_PdfPro/jquery-owlcarousel/owl.carousel.min"
    },
    "deps": [
        "vnecoms.Popular"
    ]

};


require.config(config);
})();



})(require);
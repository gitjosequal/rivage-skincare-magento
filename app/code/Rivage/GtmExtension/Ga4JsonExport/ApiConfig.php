<?php
/**
 * Copyright © Rivage(info@rivage.com) All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Ga4JsonExport;

use Magento\Framework\DataObject;
use Rivage\GtmExtension\Model\Enum;

/**
 * Class \Rivage\GtmExtension\Ga4JsonExport\ApiConfig
 */
class ApiConfig extends DataObject
{
    /**
     * Return list of variables for api creation
     *
     * @param string $measurementId
     * @return array
     */
    private function _getVariables($measurementId)
    {
        $variables = [
            Enum::GA4_MEASUREMENT_ID => [
                'name' => Enum::GA4_MEASUREMENT_ID,
                'type' => Enum::GA4_CONSTANT_TYPE,
                'parameter' => [
                    [
                        'type' => 'template',
                        'key' => 'value',
                        'value' => $measurementId
                    ]
                ]
            ],
            Enum::GA4_PAGE_TYPE => [
                'name' => Enum::GA4_PAGE_TYPE,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'pageType'
                    ]
                ]
            ],
            Enum::GA4_ECOMMERCE_ITEMS => [
                'name' => Enum::GA4_ECOMMERCE_ITEMS,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.items'
                    ]
                ]
            ],
            Enum::GA4_ECOMMERCE_PURCHASE_ITEMS => [
                'name' => Enum::GA4_ECOMMERCE_PURCHASE_ITEMS,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.items'
                    ]
                ]
            ],
            Enum::GA4_ECOMMERCE_ACTION_ITEMS => [
                'name' => Enum::GA4_ECOMMERCE_ACTION_ITEMS,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.action.items'
                    ]
                ]
            ],
            Enum::GA4_CUSTOMER_ID => [
                'name' => Enum::GA4_CUSTOMER_ID,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'customerId'
                    ]
                ]
            ],
            Enum::GA4_CUSTOMER_GROUP => [
                'name' => Enum::GA4_CUSTOMER_GROUP,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'customerGroup'
                    ]
                ]
            ],
            Enum::GA4_TRANSACTION_ID => [
                'name' => Enum::GA4_TRANSACTION_ID,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.transaction_id'
                    ]
                ]
            ],
            Enum::GA4_COUPON => [
                'name' => Enum::GA4_COUPON,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.coupon'
                    ]
                ]
            ],
            Enum::GA4_TAX => [
                'name' => Enum::GA4_TAX,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.tax'
                    ]
                ]
            ],
            Enum::GA4_SHIPPING => [
                'name' => Enum::GA4_SHIPPING,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.shipping'
                    ]
                ]
            ],
            Enum::GA4_CURRENCY => [
                'name' => Enum::GA4_CURRENCY,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.currency'
                    ]
                ]
            ],
            Enum::GA4_AFFILIATION => [
                'name' => Enum::GA4_AFFILIATION,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.affiliation'
                    ]
                ]
            ],
            Enum::GA4_ORDER_VALUE => [
                'name' => Enum::GA4_ORDER_VALUE,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'value'
                    ]
                ]
            ],
            Enum::GA4_CUSTOMER_TOTAL_ORDER_COUNT => [
                'name' => Enum::GA4_CUSTOMER_TOTAL_ORDER_COUNT,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.total_order_count'
                    ]
                ]
            ],
            Enum::GA4_CUSTOMER_TOTAL_LIFETIME_VALUE => [
                'name' => Enum::GA4_CUSTOMER_TOTAL_LIFETIME_VALUE,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.total_lifetime_value'
                    ]
                ]
            ],
            Enum::GA4_PURCHASE_VALUE => [
                'name' => Enum::GA4_PURCHASE_VALUE,
                'type' => Enum::GA4_DATALAYER_TYPE,
                'parameter' => [
                    [
                        'type' => 'integer',
                        'key' => 'dataLayerVersion',
                        'value' => "2"
                    ],
                    [
                        'type' => 'boolean',
                        'key' => 'setDefaultValue',
                        'value' => "false"
                    ],
                    [
                        'type' => 'template',
                        'key' => 'name',
                        'value' => 'ecommerce.purchase.value'
                    ]
                ]
            ]
        ];
        return $variables;
    }

    /**
     * Return list of triggers for api creation
     *
     * @return array
     */
    private function _getTriggers()
    {
        $triggers = [
            Enum::GA4_GTM_DOM_TRIGGER => [
                'name' => Enum::GA4_GTM_DOM_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'gtm.dom'
                            ]
                        ]
                    ]
                ],
                'filter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{Event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'gtm.dom'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_SELECT_ITEM_TRIGGER => [
                'name' => Enum::GA4_SELECT_ITEM_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'select_item'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_ADD_TO_CART_TRIGGER => [
                'name' => Enum::GA4_ADD_TO_CART_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'add_to_cart'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_REMOVE_FROM_CART_TRIGGER => [
                'name' => Enum::GA4_REMOVE_FROM_CART_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'remove_from_cart'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_SELECT_PROMOTION_TRIGGER => [
                'name' => Enum::GA4_SELECT_PROMOTION_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'select_promotion'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_BEGIN_CHECKOUT_TRIGGER => [
                'name' => Enum::GA4_BEGIN_CHECKOUT_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'begin_checkout'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_VIEW_ITEM_LIST_TRIGGER => [
                'name' => Enum::GA4_VIEW_ITEM_LIST_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'view_item_list'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_VIEW_ITEM_TRIGGER => [
                'name' => Enum::GA4_VIEW_ITEM_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'view_item'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_VIEW_CART_TRIGGER => [
                'name' => Enum::GA4_VIEW_CART_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'view_cart'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_VIEW_PROMOTION_TRIGGER => [
                'name' => Enum::GA4_VIEW_PROMOTION_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'view_promotion'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_PURCHASE_TRIGGER => [
                'name' => Enum::GA4_PURCHASE_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'purchase'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_SHIPPING_INFO_TRIGGER => [
                'name' => Enum::GA4_SHIPPING_INFO_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'add_shipping_info'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_PAYMENT_INFO_TRIGGER => [
                'name' => Enum::GA4_PAYMENT_INFO_TRIGGER,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'add_payment_info'
                            ]
                        ]
                    ]
                ]
            ],
            Enum::GA4_ADD_TO_WISHLIST_TRIGGER => [
                'name' => Enum::GA4_ADD_TO_WISHLIST_TRIGGER ,
                'type' => Enum::GA4_CUSTOM_EVENT_TRIGGER_TYPE,
                'customEventFilter' => [
                    [
                        'type' => 'equals',
                        'parameter' => [
                            [
                                'type' => 'template',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'template',
                                'key' => 'arg1',
                                'value' => 'add_to_wishlist'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $triggers;
    }

    /**
     * Return list of tags for api creation
     *
     * @param array $triggers
     * @return array
     */
    private function _getTags($triggers)
    {
        $tags = [
            Enum::GA4_MEASUREMENT_ID_TAG => [
                'name' => Enum::GA4_MEASUREMENT_ID_TAG,
                'firingTriggerId' => [
                    Enum::GA4_ALL_PAGES_ID_TRIGGER
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWC_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'boolean',
                        'key' => 'sendPageView',
                        'value' => "true"
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'measurementId',
                        'value' => '{{' . Enum::GA4_MEASUREMENT_ID . '}}'
                    ]
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_ITEM_LIST_VIEWS_TAG => [
                'name' => Enum::GA4_ITEM_LIST_VIEWS_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_VIEW_ITEM_LIST_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'view_item_list'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_PRODUCT_ITEM_LIST_CLICKS_TAG => [
                'name' => Enum::GA4_PRODUCT_ITEM_LIST_CLICKS_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_SELECT_ITEM_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'select_item'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ACTION_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_ITEM_ADD_TO_CART_TAG => [
                'name' => Enum::GA4_ITEM_ADD_TO_CART_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_ADD_TO_CART_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'add_to_cart'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ACTION_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_ITEM_REMOVE_FROM_CART_TAG => [
                'name' => Enum::GA4_ITEM_REMOVE_FROM_CART_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_REMOVE_FROM_CART_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'remove_from_cart'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ACTION_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_ITEM_VIEWS_TAG => [
                'name' => Enum::GA4_ITEM_VIEWS_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_VIEW_ITEM_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'view_item'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_ITEM_CART_VIEWS_TAG => [
                'name' => Enum::GA4_ITEM_CART_VIEWS_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_VIEW_CART_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'view_cart'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_PROMOTION_VIEW_TAG => [
                'name' => Enum::GA4_PROMOTION_VIEW_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_VIEW_PROMOTION_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'view_promotion'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_PROMOTION_CLICK_TAG => [
                'name' => Enum::GA4_PROMOTION_CLICK_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_SELECT_PROMOTION_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'select_promotion'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_BEGIN_CHECKOUT_TAG => [
                'name' => Enum::GA4_BEGIN_CHECKOUT_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_BEGIN_CHECKOUT_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'begin_checkout'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_PURCHASE_TAG => [
                'name' => Enum::GA4_PURCHASE_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_PURCHASE_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'total_order_count'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_TOTAL_ORDER_COUNT . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'total_lifetime_value'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_TOTAL_LIFETIME_VALUE . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'purchase'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_PURCHASE_ITEMS . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'transaction_id'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_TRANSACTION_ID . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'affiliation'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_AFFILIATION . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'tax'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_TAX . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'shipping'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_SHIPPING . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'currency'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CURRENCY . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'coupon'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_COUPON . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'value'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_PURCHASE_VALUE . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_ADD_SHIPPING_INFO_TAG => [
                'name' => Enum::GA4_ADD_SHIPPING_INFO_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_SHIPPING_INFO_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'sendEcommerceData',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'add_shipping_info'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_ADD_PAYMENT_INFO_TAG => [
                'name' => Enum::GA4_ADD_PAYMENT_INFO_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_PAYMENT_INFO_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'sendEcommerceData',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'add_payment_info'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ],
            Enum::GA4_ADD_TO_WISHLIST_TAG => [
                'name' => Enum::GA4_ADD_TO_WISHLIST_TAG,
                'firingTriggerId' => [
                    $triggers[Enum::GA4_ADD_TO_WISHLIST_TRIGGER]
                ],
                'tagFiringOption' => 'oncePerEvent',
                'type' => Enum::GA4_GAAWE_TAG_TYPE,
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'sendEcommerceData',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'userProperties',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerGroup'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_GROUP . '}}'
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'customerId'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_CUSTOMER_ID . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => 'add_to_wishlist'
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'eventParameters',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'name',
                                        'value' => 'items'
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{' . Enum::GA4_ECOMMERCE_ITEMS . '}}'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'TAG_REFERENCE',
                        'key' => 'measurementId',
                        'value' => Enum::GA4_MEASUREMENT_ID_TAG
                    ],
                ],
                'monitoringMetadata' => [
                    'type' => "MAP"
                ]
            ]
        ];

        return $tags;
    }

    /**
     * Get Variable List
     *
     * @param string $measurementId
     * @return array
     */
    public function getVariablesList($measurementId)
    {
        return $this->_getVariables($measurementId);
    }

    /**
     * Get Trigger List
     *
     * @return array
     */
    public function getTriggersList()
    {
        return $this->_getTriggers();
    }

    /**
     * Get Tag List
     *
     * @param array $triggersMapping
     * @return array
     */
    public function getTagsList($triggersMapping)
    {
        return $this->_getTags($triggersMapping);
    }
}

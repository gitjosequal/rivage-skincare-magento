<?php
/**
 * Copyright © Rivage(info@rivage.com) All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Model;

use Magento\Framework\DataObject;

/**
 * Class \Rivage\GtmExtension\Model\Enum
 */
class Enum extends DataObject
{
    // @codingStandardsIgnoreStart

    /**
     * Tag names
     */
    const GA4_MEASUREMENT_ID_TAG = 'GA4 - GtmExtension';
    const GA4_ITEM_LIST_VIEWS_TAG = 'GA4 - GtmExtension - Item List View';
    const GA4_ITEM_CART_VIEWS_TAG = 'GA4 - GtmExtension - Item Cart Views';
    const GA4_PRODUCT_ITEM_LIST_CLICKS_TAG = 'GA4 - GtmExtension - Item List Clicks';
    const GA4_ITEM_ADD_TO_CART_TAG = 'GA4 - GtmExtension - Add To Cart';
    const GA4_ITEM_REMOVE_FROM_CART_TAG = 'GA4 - GtmExtension - Remove From Cart';
    const GA4_ITEM_VIEWS_TAG = 'GA4 - GtmExtension - Item Views';
    const GA4_PROMOTION_VIEW_TAG = 'GA4 - GtmExtension - Promotion View';
    const GA4_PROMOTION_CLICK_TAG = 'GA4 - GtmExtension - Promotion Click';
    const GA4_BEGIN_CHECKOUT_TAG = 'GA4 - GtmExtension - Begin Checkout';
    const GA4_PURCHASE_TAG = 'GA4 - GtmExtension - Purchase';
    const GA4_ADD_SHIPPING_INFO_TAG = 'GA4 - GtmExtension - Add Shipping Info';
    const GA4_ADD_PAYMENT_INFO_TAG = 'GA4 - GtmExtension - Add Payment Info';
    const GA4_ADD_TO_WISHLIST_TAG = 'GA4 - GtmExtension - Add To Wishlist';

    /**
     * Variable names
     */
    const GA4_MEASUREMENT_ID = 'GA4 - MEASUREMENT ID';
    const GA4_CUSTOMER_ID = 'GA4 - GtmExtension - customerId';
    const GA4_CUSTOMER_GROUP = 'GA4 - GtmExtension - customerGroup';
    const GA4_PAGE_TYPE = 'GA4 - GtmExtension - Page Type';
    const GA4_ECOMMERCE_ITEMS = 'GA4 - GtmExtension - ecommerce.items';
    const GA4_ECOMMERCE_PURCHASE_ITEMS = 'GA4 - GtmExtension - ecommerce.purchase.items';
    const GA4_ECOMMERCE_ACTION_ITEMS = 'GA4 - GtmExtension - ecommerce.action.items';
    const GA4_TRANSACTION_ID = 'GA4 - GtmExtension - transaction_id';
    const GA4_COUPON = 'GA4 - GtmExtension - coupon';
    const GA4_TAX = 'GA4 - GtmExtension - tax';
    const GA4_SHIPPING = 'GA4 - GtmExtension - shipping';
    const GA4_CURRENCY = 'GA4 - GtmExtension - currency';
    const GA4_AFFILIATION = 'GA4 - GtmExtension - affiliation';
    const GA4_ORDER_VALUE = 'GA4 - GtmExtension - Order Value';
    const GA4_CUSTOMER_TOTAL_ORDER_COUNT = 'GA4 - GtmExtension - Customer - total_order_count';
    const GA4_CUSTOMER_TOTAL_LIFETIME_VALUE = 'GA4 - GtmExtension - Customer - total_lifetime_value';
    const GA4_PURCHASE_VALUE = 'GA4 - GtmExtension - Purchase Value';

    /**
     * Item types
     */
    const GA4_DATALAYER_TYPE = 'v';
    const GA4_CONSTANT_TYPE = 'c';
    const GA4_CUSTOM_EVENT_TRIGGER_TYPE = 'customEvent';
    const GA4_GAAWC_TAG_TYPE = 'gaawc';
    const GA4_GAAWE_TAG_TYPE = 'gaawe';

    /**
     * Trigger names
     */
    const GA4_SELECT_ITEM_TRIGGER = 'GA4 - GtmExtension - select_item';
    const GA4_GTM_DOM_TRIGGER = 'GA4 - GtmExtension - gtm.dom';
    const GA4_ADD_TO_CART_TRIGGER = 'GA4 - GtmExtension - add_to_cart';
    const GA4_REMOVE_FROM_CART_TRIGGER = 'GA4 - GtmExtension - remove_from_cart';
    const GA4_VIEW_ITEM_TRIGGER = 'GA4 - GtmExtension - view_item';
    const GA4_VIEW_CART_TRIGGER = 'GA4 - GtmExtension - view_cart';
    const GA4_VIEW_ITEM_LIST_TRIGGER = 'GA4 - GtmExtension - view_item_list';
    const GA4_SELECT_PROMOTION_TRIGGER = 'GA4 - GtmExtension - select_promotion';
    const GA4_VIEW_PROMOTION_TRIGGER = 'GA4 - GtmExtension - view_promotion';
    const GA4_BEGIN_CHECKOUT_TRIGGER = 'GA4 - GtmExtension - begin_checkout';
    const GA4_PURCHASE_TRIGGER = 'GA4 - GtmExtension - purchase';
    const GA4_SHIPPING_INFO_TRIGGER = 'GA4 - GtmExtension - add_shipping_info';
    const GA4_PAYMENT_INFO_TRIGGER = 'GA4 - GtmExtension - add_payment_info';
    const GA4_ADD_TO_WISHLIST_TRIGGER = 'GA4 - GtmExtension - add_to_wishlist';
    const GA4_ALL_PAGES_ID_TRIGGER = '2147479553';

    /**
     * page type
     */
    const PAGETYPE_DATA = [

        'cms_index_index' => 'home',
        'catalog_category_view' => 'category',
        'catalog_product_view' => 'product',
        'checkout_cart_index' => 'cart',
        'checkout_index_index' => 'checkout'
    ];
    // @codingStandardsIgnoreEnd
}

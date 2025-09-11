/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    // Need to initialize tabs and all containers first!
    'jquery',
    'mage/utils/wrapper',
    'tabs'
], function ($, wrapper) {
    // Original method sends the HTML request to the ListAjax controller. Cookies are sent together with this
    // request BEFORE the mage-messages cookie is cleared. The script "Magento_Theme/js/view/messages" is
    // the app component that is initialized AFTER the reviews block. This is why we must at least wait a little :(
    // Otherwise messages are always shown on the Product page. This refers to all messages, not only to the reviews
    return function (processReviews) {
        return wrapper.wrap(processReviews, function (originalProcessReviews, config) {
            setTimeout(originalProcessReviews, 1000);
            var $formLink = $('.product-info-main .reviews-actions a.add');
            if (window.location.hash === '#review-form' && $formLink.length) {
                $formLink.trigger('click');
            }
        });
    };
});

/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

/**
 * This script was moved here because it was renamed between Magento 2.0 and 2.1, so we can not rely on the default file name
 */
require([
    'jquery'
], function ($) {
    'use strict';

    $(function () {

        // disabled select only
        $('select#frontend_input:disabled').each(function () {
            var select = $(this),
                currentValue = select.find('option:selected').val(),
                enabledTypes = ['select', 'swatch_visual', 'swatch_text'],
                warning = $('<label>')
                    .hide()
                    .text($.mage.__('These changes affect all related products.'))
                    .addClass('mage-error')
                    .attr({
                        generated: true, for: select.attr('id')
                    }),

                /**
                 * Toggle hint about changes types
                 */
                toggleWarning = function () {
                    if (select.find('option:selected').val() === currentValue) {
                        warning.hide();
                    } else {
                        warning.show();
                    }
                },

                /**
                 * Remove unsupported options
                 */
                removeOption = function () {
                    if (!~enabledTypes.indexOf($(this).val())) {
                        $(this).remove();
                    }
                };

            // Check current type (allow only: select, swatch_visual, swatch_text)
            if (!~enabledTypes.indexOf(currentValue)) {
                return;
            }

            // Enable select and keep only available options (all other will be removed)
            select.removeAttr('disabled').find('option').each(removeOption);

            // Add warning on page and event for show/hide it
            select.after(warning).on('change', toggleWarning);
        });
    });
});

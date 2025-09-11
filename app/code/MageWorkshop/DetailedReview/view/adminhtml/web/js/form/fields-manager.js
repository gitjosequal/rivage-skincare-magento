/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

define([
    'jquery',
    'mage/template',
    'jquery/ui'
], function ($, mageTemplate) {
    'use strict';

    if (!$.hasOwnProperty('mageWorkshop')) {
        $.mageWorkshop = {};
    }

    $.widget('mageWorkshop.detailedReview_formFieldsManager', {

        _create: function () {
            this.renderTemplate()
                .initSortable();

        },

        renderTemplate: function () {
            this.template = mageTemplate(this.options.template);
            for (var property in this.options.attributes) {
                if (this.options.attributes.hasOwnProperty(property)) {
                    this.element.append(
                        this.template(this.options.attributes[property])
                    );
                }
            }
            return this;
        },

        initSortable: function () {
            var listIds = [];

            for (var property in this.options.attributes) {
                if (this.options.attributes.hasOwnProperty(property)) {
                    listIds.push('#' + property);
                }
            }

            $(listIds.join(',')).sortable({
                connectWith: this.options.connectWith,
                receive: function (event, ui) {
                    if (ui.item.hasClass('ui-state-disabled')) {
                        ui.sender.sortable('cancel');
                    } else {
                        ui.item.find('input').attr('name', this.id + '[]')
                    }
                }
            }).disableSelection();
        }
    });

    return $.mageWorkshop.detailedReview_formFieldsManager;
});
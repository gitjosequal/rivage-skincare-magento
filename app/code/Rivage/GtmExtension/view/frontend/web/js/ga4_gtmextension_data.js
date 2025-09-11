define([
    'jquery',
    'ga4_gtmextension_data', // A module containing GtmExtension data
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    // Listen for AJAX completion event
    $(document).ajaxComplete(function (event, xhr, settings) {

        // Check if the AJAX URL contains '/customer/section/load/'
        if (settings.url.search('/customer/section/load/') > 0) {

            // Parse the response JSON from the AJAX request
            var datajson = xhr.responseJSON;

            // Check if window.dataLayer is defined and if ga4_ gtmextension_data is present in the response
            if (window.dataLayer && datajson.ga4_gtmextension_data) {

                // Parse the JSON data from ga4_ gtmextension_data and push it to dataLayer
                var dataLayerData = $.parseJSON(datajson.ga4_gtmextension_data.datalayer);
                for (var data in dataLayerData) {
                    window.dataLayer.push(dataLayerData[data]);
                }
            }
        }
    });
});

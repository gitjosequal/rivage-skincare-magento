/**
 * Color picker component
 */
define([
    "jquery",
    "jquery/colorpicker/js/colorpicker",
    "domReady!"
], function ($){
    "use strict";

    return function (config, element) {
        let el = $(element);
        if (config.color === undefined || config.color.trim() === "") {
            config.color = "#ffffff";
        }
        el.css("backgroundColor", config.color);
        el.ColorPicker({
           color: config.color,
           onChange: (hsb, hex, rgb) => {
               el.css("backgroundColor", "#" + hex).val("#" + hex);
           }
        });
    }
});

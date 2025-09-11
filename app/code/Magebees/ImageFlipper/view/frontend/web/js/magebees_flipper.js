define([
    "jquery",
    "jquery/ui"
], function ($,ui) {

    $.widget('magebees.magebeesFlipper', {
        options: {},

        _create: function (options) {
            this._initialize();
        },
        _initialize: function () {
            var self = this;
            var options=self.options;
            var mainclass=options['mainclass'];
            var imageclass=options['imageclass'];
            $(document).on({
             mouseenter: function () {
                var base = $(this).find(imageclass).attr('src');
                var flipper = $(this).find("#magebees_fliper_img").text();
                if (flipper != '' && flipper != 'https://rivagecare.com/pub/media/catalog/productno_selection') {
                    $(this).find("#magebees_fliper_img").text(base);
                    
                    $(this).find(imageclass).attr('src', flipper);
                }
    },
    mouseleave: function () {
       var base = $(this).find(imageclass).attr('src');
                var flipper = $(this).find("#magebees_fliper_img").text();
                if (flipper != '' && flipper != 'https://rivagecare.com/pub/media/catalog/productno_selection') {
                   $(this).find("#magebees_fliper_img").text(base);
                   $(this).find(imageclass).attr('src', flipper);
                }
    },
				  touchstart: function () {
					   var base = $(this).find(imageclass).attr('src');
                var flipper = $(this).find("#magebees_fliper_img").text();
                if (flipper != '' && flipper != 'https://rivagecare.com/pub/media/catalog/productno_selection') {
                    $(this).find("#magebees_fliper_img").text(base);
                    
                    $(this).find(imageclass).attr('src', flipper);
                }
				  },
				 touchend: function () {
					 var base = $(this).find(imageclass).attr('src');
                var flipper = $(this).find("#magebees_fliper_img").text();
                if (flipper != '' && flipper != 'https://rivagecare.com/pub/media/catalog/productno_selection') {
                   $(this).find("#magebees_fliper_img").text(base);
                   $(this).find(imageclass).attr('src', flipper);
                }
				 },
}, mainclass);
        }
    });
    return $.magebees.magebeesFlipper;
});

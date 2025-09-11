require(['jquery', 'aos', 'owlcarouselslider','lazyLoad_rivage'], function ($, AOS) {
    $(document).ready(function ($) {
        AOS.init({
            startEvent: 'load',
            disableMutationObserver: false,
        });
        AOS.refresh(true); 
        setTimeout(function(){
            window.dispatchEvent(new Event('resize'));
        },500);

        $('.owl-carousel.best,.owl-carousel.arrivals').owlCarousel({
            loop: true,
            margin: 10,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                    nav: true
                },
                600: {
                    items: 2,
                    nav: false
                },
                1000: {
                    items: 3,
                    nav: true,
                    loop: false
                }
            }
        });


        $('.mobile-menu h5').click(function () {
            $(this).next('ul').toggle();
            $(this).toggleClass('mobile-show');
        });


        $('.owl-carousel.top-bar').owlCarousel({
            items: 1,
            autoplay: true,
            nav: false,
            dots: false,
            loop: true,
            margin: 10,
            responsiveClass: true,
            smartSpeed: 500
        });


        $(window).scroll(function () {
            if ($(document).scrollTop() > 100) {
                $('body.cms-index-index header.page-header').addClass('white');
            } else {
                $('body.cms-index-index header.page-header').removeClass('white');
            }
            if ($(document).scrollTop() > 1) {
                $('body:not(.cms-index-index) header.page-header').addClass('shift');
            } else {
                $('body:not(.cms-index-index) header.page-header').removeClass('shift');
            }
        });

        $(".search-link a").click(function (e) {
            e.preventDefault();
            $(".block-search").toggleClass("show");
        });

        if($('.cookies').length){
            var height = $('.cookies').height();
            $(".cookies").height(height);
            $('.cookies-buttons a').click(function (e) {
                e.preventDefault();
                $(".cookies").animate({
                    height: 0,
                    padding: 0
                }, 50);
            });
        }
       

        $('.nav-link').on('click', function (evt) {
            evt.preventDefault();
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            var sel = this.getAttribute('data-toggle-target');
            $('.tab-pane').removeClass('show active').filter(sel).addClass('show active');
        });

        $(".close").click(function () {
            $(".popup").addClass('hide');
        });

        $(".navbar-toggler").click(function () {
            $('#nb_mn_mobile').toggleClass('show');
        });

        $("#nb_mn_mobile .nav-exploded.explodedmenu > .menu.nbtype-7 > a").click(function (e) {
            e.preventDefault();
            $(this).next('.explodedmenu-menu-popup').toggleClass('popup-show');
            $(this).toggleClass('toggle-show');
        });

        if($("#nb_mn_mobile .navigation_mgmn").length){
            $(document).mouseup(function (e) {
                var container = $("#nb_mn_mobile .navigation_mgmn");
    
                // if the target of the click isn't the container nor a descendant of the container
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    $('#nb_mn_mobile').removeClass('show');
                }
            });
        }

        $('.action.nav-toggle').click(function () {
            $('html').toggleClass('nav-before-open nav-open');
        });

        $('.mobile-menu-close').click(function (e) {
            e.preventDefault();
            $('#nb_mn_mobile').removeClass('show');
            $('html').removeClass('nav-before-open nav-open');
        });

        var screenWidth = window.matchMedia("(max-width: 767px)");
        if (!screenWidth.matches) {
            $(".action.showcart").click(function () {
                window.location.href = $(this).attr('href');
            });
            $('.minicart-wrapper').hover(function () {
                $('[data-role="dropdownDialog"]').dropdownDialog("open");
            }, function () {
                $('[data-role="dropdownDialog"]').dropdownDialog("close");
            });
        }


        miniCart = $('[data-block=\'minicart\']');

        miniCart.on('dropdowndialogopen', function () {
            if (screenWidth.matches) {
                $('body').addClass('unscrollable');
            }
        });

        miniCart.on('dropdowndialogclose', function () {
            if (screenWidth.matches) {
                $('body').removeClass('unscrollable');
            }
        });

        var isloading = true;

        $('[data-block="minicart"]').on('contentUpdated', function () {
            if (!isloading) {
                isloading = true;
                $($('[data-role="dropdownDialog"]')[0]).dropdownDialog("open");
                setTimeout(function () {
                    $('[data-role="dropdownDialog"]').dropdownDialog("close");
                    isloading = true;
                }, 3000);
            }
        });

        $(".tocart").click(function () { isloading = false; });

        if($('.mpinstagramfeed-photo').length > 12){
            var open = false;
            $('.insta-more').click(function (e) {
                e.preventDefault();
                if (open) {
                    $('.mpinstagramfeed-container .shuffle .mpinstagramfeed-photo:nth-child(n+13)').fadeOut();
                    open = false;
                    $(this).text('Load More');
                } else {
                    $('.mpinstagramfeed-container .shuffle .mpinstagramfeed-photo:nth-child(n+13)').fadeIn();
                    open = true;
                    $(this).text('Show Less');
                }
            });
        }else{
            $('.insta-more').remove();
        }

        $(document).on('click', '.filter-options-title', function () {
            $(this).next('.filter-options-content').toggleClass('show');
            if ($(this).next('.filter-options-content').hasClass('show')) {
                $(this).next('.filter-options-content').stop();
                $(this).next('.filter-options-content').fadeOut();
            } else {
                $(this).next('.filter-options-content').stop();
                $(this).next('.filter-options-content').fadeIn();
            }
        });

        $(document).on('click', '.block-title.filter-title', function () {
            $(this).toggleClass('active');
            if ($(this).hasClass('active')) {
                $(this).next('.filter-content').stop();
                $(this).next('.filter-content').fadeIn();
            } else {
                $(this).next('.filter-content').stop();
                $(this).next('.filter-content').fadeOut();
            }
        });


        $(document).on('click', '.filter-options-item', function () {
            $(this).toggleClass('active');
            if ($(this).hasClass('active')) {
                $(this).find('.filter-options-content').stop();
                $(this).find('.filter-options-content').fadeIn();
            } else {
                $(this).find('.filter-options-content').stop();
                $(this).find('.filter-options-content').fadeOut();
            }
        });

        $(document).on('submit', 'form.review-form', function () {
            $(this).find('input[type="text"]').each(function () {
                if ($(this).val() == '') {
                    $(this).val('N/A');
                }
            })
            $(this).find('textarea').each(function () {
                if ($(this).val() == '') {
                    $(this).val('N/A');
                }
            })
        });

        $('.owl-carousel5').owlCarousel({
            loop:true,
            margin:15,
            autoplay: false,
            responsiveClass:true,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                480:{
                    items:1,
                    nav:true

                },
                1000:{
                    items:4,
                    nav:true,
                    loop:false
                }
            }
        });


        $(".owl-carousel2").owlCarousel({
            loop:true,
            margin:15,
            autoplay: false,
            responsiveClass:true,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                375:{
                    items:1,
                    nav:true
                },
                480:{
                    items:2,
                    nav:true

                },
                799:{
                    items:3,
                    nav:true

                },
                1000:{
                    items:4,
                    nav:true,
                    loop:false
                }
            }
        });

        if($('.owl-carousel3').length){
            $(window).resize(function(){
                $('#carousel').addClass('owl-carousel3');
                if($(window).width()>767){
                    $('.owl-carousel3').trigger('destroy.owl.carousel3');
                }else{
                    $('.owl-carousel3').owlCarousel({
                        loop:true,
                        margin:10,
                        nav:true,
                        responsive:{
                            0:{
                                items:1
                            },
                            600:{
                                items:1
                            },
                            1000:{
                                items:3
                            }
                        }
                    });
                }
            }).trigger('resize');
        }
        
    });

    $(".horizontal-menu .inner-cms-block").each(function(){
        if(!$(this).hasClass('row-static-cate-grid') && !$(this).hasClass('row-product-list')){
            var block_left = $(this).find(".block-left").innerWidth();
            var block_cate = $(this).find(".block-cate").innerWidth();
            var block_product = $(this).find(".block-product").innerWidth();
            var block_right = $(this).find(".block-right").innerWidth();   
            var inner_cms_block = block_left + block_cate + block_product + block_right; 
            
            if($(this).hasClass('nb-repare-width')){
                inner_cms_block += 110;
            }else if($(this).hasClass('block-full')){
                inner_cms_block +=25;
            }else{
                inner_cms_block += 60; 
            } 
            $(this).css('width',inner_cms_block); 
        } 
    });

    $(".horizontal-menu li.menu").each(function(){
        var w_container = $("#header-nav").width(); 
        var w_parent = $(this).width(); 
        var w_child = w_container - w_parent; 
        $(this).find(".explodedmenu-menu-popup .row-productgrid, .explodedmenu-menu-popup .my-contact-form, .explodedmenu-menu-popup .row-cate-grid,.explodedmenu-menu-popup ._row-product-list, .explodedmenu-menu-popup ._row-static-cate-grid, .explodedmenu-menu-popup .product_list_by_sub_cat").css('width', w_child);
        $(this).find('.product_list_by_sub_cat .inner-cms-block').css('width', '100%');
    });

    $('li.subcate_list').on('click', function(){
        $('li.subcate_list').removeClass('active');
        $('.subcate_content').removeClass('active');
        $(this).addClass('active');
        $('.subcate_content#' + $(this).data('toggle')).addClass('active');
    });

    $(document).on("mouseover", ".custom1,.custom2,.custom3,.custom4,.custom5", function(ev) {
        $('body').addClass("menuover");
        return false;
    });
    $(document).mouseout(".custom1,.custom2,.custom3,.custom4,.custom5", function(ev) {
        $('body').removeClass("menuover");
    });

    $(".vertical-menu .inner-cms-block").each(function(){
        var block_left = $(this).find(".block-left").innerWidth();
        var block_cate = $(this).find(".block-cate").innerWidth();
        var block_product = $(this).find(".block-product").innerWidth();
        var block_right = $(this).find(".block-right").innerWidth();  
        var inner_cms_block = block_left + block_cate + block_product + block_right; 
        if($(this).hasClass('nb-repare-width')){
            inner_cms_block += 110;
        }else if($(this).hasClass('block-full')){
            inner_cms_block +=25;
        }else{
            inner_cms_block += 30; 
        } 
        
        if(!$(this).hasClass('row-static-cate-grid') && !$(this).hasClass('row-product-list')){
            $(this).css('width',inner_cms_block); 
        } 
    });

    $('.wrap-vertical-menu .btn-menu').on('click',function(){
        if($('.vertical-menu').hasClass('show')){
            $(".vertical-menu").removeClass('show');
        }else{
            $(".vertical-menu").addClass('show');
        } 
        $(".vertical-menu li.menu").each(function(){
            var w_container = $("#header-nav").width(); 
            var w_parent = $(this).width(); 
            var w_child = w_container - w_parent; 
            $(this).find(".explodedmenu-menu-popup .row-productgrid, .explodedmenu-menu-popup .my-contact-form, .explodedmenu-menu-popup .row-cate-grid,.explodedmenu-menu-popup ._row-product-list, .explodedmenu-menu-popup ._row-static-cate-grid, .explodedmenu-menu-popup .product_list_by_sub_cat").css('width', w_child);
            $(this).find('.product_list_by_sub_cat .inner-cms-block').css('width', '100%');
        });
    });

    $(".owl-banner1").owlCarousel({
        loop: true,
        nav: true,
        margin: 15,
        autoplay: false,
        responsiveClass: true,
        responsive:{
            0:{
                items: 1
            },
            1000:{
                items: 5
            }
        }
    });

    $(".owl-banner2,.owl-banner3,.owl-banner4,.owl-banner5,.owl-banner6,.owl-banner7").owlCarousel({
		loop: true,
		margin: 15,
		autoplay: false,
		thumbs: true,
		items: 1,
		nav: true
	});

    var sync1 = $("#sync1");
    var sync2 = $("#sync2");

    sync1.owlCarousel({
        items : 1,
        singleItem : true,
        slideSpeed : 2000,
        navigation: true,
        pagination:false,
        responsiveRefreshRate : 200
    });

    sync1.on('changed.owl.carousel', function(event) {
        setTimeout(() => {
            $("#sync2 .owl-item").eq($('#sync1 .owl-item.active').index()).addClass("active-item").siblings().removeClass("active-item");
        },600);
    });

    sync2.owlCarousel({
        items : 11,
        pagination: false,
        margin: 13,
        responsiveRefreshRate : 100,
         stagePadding: 45,
        onInitialized: (elm) => {
            $("#sync2 .owl-item").eq(0).addClass("active-item").siblings().removeClass("active-item");
        },
        responsiveClass: true,
        responsive:{
            0:{
                items: 3,
                pagination: false,
            },
            768: {
                items: 4,
                pagination: false,
            },
            1000:{
                items: 11,
                pagination: false,
            }
        }
    });

    sync2.on("click", ".owl-item", function(e){
        e.preventDefault();
        sync1.trigger("to.owl.carousel",$(this).index());
    });

    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
    
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                var headerHeight = 0;
                if($(window).width()>767){
                    if($('.nav-sections').hasClass('fixed-menu')){
                        headerHeight = 42;
                    }else {
                        headerHeight = $('.nav-sections').height() + 78;
                    }
                }
                $('html, body').animate({
                    scrollTop: target.offset().top - headerHeight
                }, 800);
            }
        }
    });

    if($(window.location.hash.replace('section-','')).length){
        setTimeout(() => {
            $('html, body').animate({
                scrollTop: $(window.location.hash.replace('section-','')).offset().top
            }, 1000);
        }, 100);
    }

    $('.filter_char').click( function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        var id = $(this).attr('id');
        $('.featured_listing').html();

        $.ajax({
            type: "POST",
            url: href,
            datatype: "json",
            data: {'id':id},
            showLoader: true,
            success: function(response)
            {
                var json = $.parseJSON(response);
                if(json.status === 'success'){
                    var scroll = $(window).scrollTop();
                    $('.popup_main_content').css('top',scroll+'px');
                    $('.featured_listing').html(json.html);
                }
            }
        });
    });

    $(".owl-carouselupsell").owlCarousel({
        loop: true,
        margin: 15,
        autoplay: false,
        responsiveClass: true,
        nav:true,
        responsive:{
            0:{
                items:1
            },
            767:{
                items:3,
            },
            799:{
                items:4,
            },
            1000:{
                items: 5,
                loop:false
            }
        }
    });

    $('.field.qty select').change(function(){
        $(".update").trigger("click");   
    });

    lazyload();
});
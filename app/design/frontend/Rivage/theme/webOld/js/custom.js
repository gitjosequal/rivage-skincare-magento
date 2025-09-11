require(['jquery','aos', 'owl'], function($,AOS){

    'use strict';

    var $= $.noConflict();
    $('.owl-carousel.best').owlCarousel({
        loop:true,
        margin:10,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            600:{
                items:2,
                nav:false
            },
            1000:{
                items:3,
                nav:true,
                loop:false
            }
        }
    });


    $('.owl-carousel.arrivals').owlCarousel({
        loop:true,
        margin:10,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            600:{
                items:2,
                nav:false
            },
            1000:{
                items:3,
                nav:true,
                loop:false
            }
        }
    });



    $('.owl-carousel.top-bar').owlCarousel({
        items:1,
        autoplay: true,
        nav:false,
        dots:false,
        loop:true,
        margin:10,
        responsiveClass:true,
        smartSpeed: 500
    });


    $(window).scroll(function() {
        if ($(document).scrollTop() > 100) {
            $('header.page-header').addClass('white');
        }
        else {
            $('header.page-header').removeClass('white');
        }
    });

    var height = $('.cookies').height();
    $(".cookies").height(height);
    $('.cookies-buttons a').click(function(e){
        e.preventDefault();
        $(".cookies").animate({
            height: 0,
            padding: 0
        },50);
    });


    $(".close").click(function(){
        $(".popup").addClass('hide');
    });


    AOS.init();

});

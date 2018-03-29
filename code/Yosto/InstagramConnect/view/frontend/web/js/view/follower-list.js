define([
    'jquery',
    'yostocoreowlcarousel'], function($){

    return function (config, element) {
        $(element).owlCarousel({

            loop: false,
            autoplay: true,
            responsiveClass: true,
            responsive: {
                320: {
                    items: config.config.items360,
                    nav: false
                },
                480: {
                    items: config.config.items480,
                    nav: false
                },
                640: {
                    items: config.config.items640,
                    nav: false
                },
                768: {
                    items: config.config.items768,
                    nav: false
                },
                1024: {
                    items: config.config.items1024,
                    nav: false
                },
                1440: {
                    items: config.config.items1440,
                    nav: false
                }
            }
        });
    };
});
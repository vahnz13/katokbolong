/*
 * Copyright (c) 2013 kentooz
 * Kentooz Theme Custom Javascript
 */
jQuery(document).ready(function() {
    // JS TOOLTIPS
    jQuery('ul.ktz-socialicon a, ul.sharethis a').tooltip({
        placement: 'top'
    });
    jQuery('ul.icon-author a').tooltip({
        placement: 'bottom'
    });
    jQuery('a[rel=tooltip]').tooltip();

    // JS TABS - Select first tab in shortcode
    jQuery('#ktztab a:first, #kentooz-comment a:first').tab('show');

    jQuery('.js-activated').dropdownHover().dropdown();

    // JS POPOVER

    jQuery(".ktzcarousel-little").owlCarousel({
        navigation: true,
        responsive: true,
        responsiveRefreshRate: 200,
        navigationText: [
            "<span class='fontawesome ktzfo-double-angle-left'></span>",
            "<span class='fontawesome ktzfo-double-angle-right'></span>"
        ],
        pagination: false,
        items: 3
    });

    // GALLERY
    jQuery('.ktzcarousel-little .item img').click(function() {
        oriSrc = jQuery(this).attr('data-ori');
        cropSrc = jQuery(this).attr('data-crop');
        titleImg = jQuery(this).attr('data-title');
        jQuery(this).closest('.box_gallery').find('#inner_box_gallery a.data-original-link').attr('href', oriSrc);
        jQuery(this).closest('.box_gallery').find('#inner_box_gallery img.data-original').attr('src', cropSrc);
        jQuery(this).closest('.box_gallery').find('#inner_box_gallery a.data-original-link').attr('data-title', titleImg);
    });
    // Lazy load
    jQuery('img.ktz-lazyload').unveil(200);

    // Back to top	
    var jQueryscrolltotop = jQuery("#ktz-backtotop");
    jQueryscrolltotop.css('display', 'none');
    jQuery(function() {
        jQuery(window).scroll(function() {
            if (jQuery(this).scrollTop() > 100) {
                jQueryscrolltotop.slideDown('fast');
            } else {
                jQueryscrolltotop.slideUp('fast');
            }
        });
        jQueryscrolltotop.click(function() {
            jQuery('body,html').animate({
                scrollTop: 0
            }, 'fast');
            return false;
        });
    });

    // Responsive FB comment
    setTimeout(function() {
        resizeFacebookComments();
    }, 1000);
    jQuery(window).on('resize', function() {
        resizeFacebookComments();
    });

    function resizeFacebookComments() {
        var src = jQuery('.fb-comments iframe').attr('src');
        src = src ? src.split('width=') : '';
        var width = jQuery('.wrapcomment').width();
        jQuery('.fb-comments iframe').attr('src', src[0] + 'width=' + width);
    }
    (function(w, d, s) {
        function go() {
            var js, fjs = d.getElementsByTagName(s)[0],
                load = function(url, id) {
                    if (d.getElementById(id)) {
                        return;
                    }
                    js = d.createElement(s);
                    js.src = url;
                    js.id = id;
                    fjs.parentNode.insertBefore(js, fjs);
                };
            load('//connect.facebook.net/en_US/all.js#xfbml=1', 'fbjssdk');
        }
        if (w.addEventListener) {
            w.addEventListener("load", go, false);
        } else if (w.attachEvent) {
            w.attachEvent("onload", go);
        }
    }(window, document, 'script'));
});
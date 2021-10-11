jQuery(function($){
    if ($('.single .player').length) {
        $('.single .player audio').mediaelementplayer();
    }

    var name_width = $('#header .name').width(),
        menu_width = $('#header nav').width(),
        social_width = $('#header .social').width();

    $(window).on('resize', function() {
        var container_width = $('.container').width();

        if ((container_width - (menu_width + social_width + 32)) < name_width) {
            $('#header').addClass('mobile'); 
        } else {
            $('#header').removeClass('mobile toggled'); 
            $('#header .toggle-menu').removeClass('toggled'); 
        }
    });

    $(window).trigger('resize');

    $('#header .left .toggle-menu').on('click', function() {
        if ($(this).hasClass('toggled')) {
            $('#header.mobile').removeClass('toggled');
            $(this).removeClass('toggled');
        } else {
            $('#header.mobile').addClass('toggled');
            $(this).addClass('toggled');
        }
    });
});

var scrolling = function() {
    lazy_images();
};

window.onload = function() {
    scrolling();
    addEventListener('scroll',scrolling);
};

function lazy_images()
{
    var images = document.getElementsByTagName('img');

    for (var i = 0 ; i < images.length; i++) {
        if (element_in_viewport(images[i])) {
            if ( ! images[i].classList.contains('lazy-loaded') && images[i].getAttribute('data-src')) {
                var default_src = images[i].getAttribute('src'),
                    error_src = '';

                if (images[i].getAttribute('data-error')) {
                    error_src = images[i].getAttribute('data-error');
                }

                images[i].classList.add('lazy-hide');
                images[i].setAttribute('src',images[i].getAttribute('data-src'));
                images[i].onload = function() {
                    this.classList.add('lazy-show', 'lazy-loaded');
                    this.onload = null;
                };
                images[i].onerror = function() {
                    this.setAttribute('src', (error_src ? error_src : default_src));
                    this.classList.add('lazy-show', 'lazy-loaded');
                };
            }
        }
    }
}

function element_in_viewport(el)
{
    const rect = el.getBoundingClientRect();
    const windowHeight = (window.innerHeight || document.documentElement.clientHeight);
    const windowWidth = (window.innerWidth || document.documentElement.clientWidth);
    const vertInView = (rect.top <= windowHeight) && ((rect.top + rect.height) >= 0);
    const horInView = (rect.left <= windowWidth) && ((rect.left + rect.width) >= 0);

    return (vertInView && horInView);
}
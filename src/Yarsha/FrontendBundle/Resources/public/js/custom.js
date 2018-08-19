// carousel slider js start
$(document).ready(function () {
    $('#myCarousel').carousel({
        interval: 3000
    })
});

// stick top nav bar script
function sticky_relocate() {
    var window_top = $(window).scrollTop();
    var div_top = $('#sticky-anchor').offset().top;
    if (window_top > div_top) {
        $('#sticky').addClass('stick');
        $('#sticky-anchor').height($('#sticky').outerHeight());
    } else {
        $('#sticky').removeClass('stick');
        $('#sticky-anchor').height(0);
    }
}
$(function () {
    $(window).scroll(sticky_relocate);
    sticky_relocate();
});
var dir = 1;
var MIN_TOP = 200;
var MAX_TOP = 350;
function autoscroll() {
    var window_top = $(window).scrollTop() + dir;
    if (window_top >= MAX_TOP) {
        window_top = MAX_TOP;
        dir = -1;
    } else if (window_top <= MIN_TOP) {
        window_top = MIN_TOP;
        dir = 1;
    }
    $(window).scrollTop(window_top);
    window.setTimeout(autoscroll, 100);
}

// test scroll script
(function ($) {

    $.fn.scroller = function (options) {

        // Options
        var settings = $.extend({
            direction: 'right',
            columns: 4,
            pauseOnHover: false
        }, options);

        // Loop through each element
        $(this).each(function () {

            // Variables
            var innerWidth = 0,
                outerWidth = $(this).outerWidth(),
                columnWidth = outerWidth / settings.columns;

            // Wrap the inner elements in our full-width container
            $(this)
                .wrapInner("<div class='inner'></div>")
                .children(".inner")
                .children()
                .each(function () {
                    $(this).width(columnWidth);
                    innerWidth = innerWidth + $(this).width();
                })
                .closest(".inner")
                .css({
                    'width': innerWidth
                });

            // If pauseOnHover is set, then apply the class to the scroller
            // element to apply the effect
            if (settings.pauseOnHover) {
                $(this).addClass('pauseHover');
            }

            // Copy the inner div and attach it to the beginning of the
            // original.  This fills the space in while the original .inner
            // slides.
            if (settings.direction === 'right') {
                $(this)
                    .children(".inner")
                    .clone()
                    .addClass('clone')
                    .insertBefore(".inner")
                    .css({
                        "left": (innerWidth * -1)
                    });
            }
            else if (settings.direction === 'left') {
                $(this)
                    .children(".inner")
                    .addClass('scrollLeft')
                    .clone()
                    .addClass('clone')
                    .insertAfter(".inner")
                    .css({
                        "left": (innerWidth * 1)
                    });
            }

        });
    }

    /*$(window).resize(function() {
     if(this.resizeTO) clearTimeout(this.resizeTO);
     this.resizeTO = setTimeout(function() {
     $(this).trigger('resizeEnd');
     }, 500);
     });*/

    // Run the the scroller init on page resize or load
    /*$(window).bind('resizeEnd load', function() {
     $('.scroller').scroller({
     direction: 'left',
     columns: 5,
     pauseOnHover: true
     });
     });*/

})(jQuery);

$('.scroller').scroller({
    direction: 'left',
    columns: 5,
    pauseOnHover: true
});


$(document).ready(function () {
    if ($(window).width() <= 767) {
        // $('.job-category-tab-sec').addClass('small-device-tab');
        // $('.job-category-tab-sec').find('li').removeClass('active');
        // $('#jobsbyfunction,#jobsbyindustry').removeClass('active');
        // $('#jobsbyfunction,#jobsbyindustry').attr('aria-expanded', false);
        //
        // $('#jobsbyfunction,#jobsbyindustry').on('click', function () {
        //     $('.job-category-tab-sec').removeClass('small-device-tab');
        // });

        var jbf = $('#jobsbyfunction').find('ul li');
        var jbi = $('#jobsbyindustry').find('ul li');
        var iconDown = '<i class="fa fa-arrow-down" aria-hidden="true"></i> &nbsp;';
        var iconUp = '<i class="fa fa-arrow-up" aria-hidden="true"></i> &nbsp;';
        var seeMore = iconDown + 'see more';
        var seeLess = iconUp + 'see less';

        function showLessJb(obj) {
            for (var i = 0; i < obj.length; i++) {
                if (i >= 5) {
                    $(obj[i]).addClass('xtra-small');
                }
            }
            if (obj.length > 5) {
                if (obj == jbf) {
                    $('#seeMoreJbf').html(seeMore);
                } else {
                    $('#seeMoreJbi').html(seeMore);
                }
            }
        }

        showLessJb(jbf);
        showLessJb(jbi);

        $('#seeMoreJbf').on('click', function (e) {
            expanded = $(this).attr('data-expanded');
            if (expanded == 0) {
                $(jbf).siblings().removeClass('xtra-small');
                $(this).attr('data-expanded', '1');
                $(this).html() == seeMore.trim() ? $(this).html(seeLess) : $(this).html(seeMore);
            } else {
                $(this).attr('data-expanded', '0');
                showLessJb(jbf);
            }

        });

        $('#seeMoreJbi').on('click', function (e) {
            expanded = $(this).attr('data-expanded');
            if (expanded == 0) {
                $(this).attr('data-expanded', '1');
                $(jbi).siblings().removeClass('xtra-small');
                $(this).html() == seeMore.trim() ? $(this).html(seeLess) : $(this).html(seeMore);
            } else {
                $(this).attr('data-expanded', '0');
                showLessJb(jbi);
            }

        });
    }
});


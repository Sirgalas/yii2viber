$(window).ready(function() {
    if ($('html').hasClass('desktop')) {
        $('#stuck_container').TMStickUp({
        })
    }

    $('#camera_wrap').camera({
        height: '34.58333333333333%',
        thumbnails: false,
        pagination: true,
        fx: 'simpleFade',
        loader: 'none',
        hover: false,
        navigation: false,
        playPause: false,
        minHeight: "139px",
    });

    $(".owl-carousel").owlCarousel({
        navigation: true,
        pagination: false,
        items : 3,
        itemsDesktop : [1199,3],
        itemsDesktopSmall : [979,3],
        itemsTablet: [750,1],
        itemsMobile : [479,1],
        navigationText: false
    });

    if ($('html').hasClass('desktop')) {
        $.stellar({
            horizontalScrolling: false,
            verticalOffset: 20,
            resposive: true,
            hideDistantElements: true,
        });
    }

});

$(function () {
    $().UItoTop({ easingType: 'easeOutQuart' });
});

$(function () {
    if ($('html').hasClass('desktop')) {
        $.srSmoothscroll({
            step:150,
            speed:800
        });
    }
});

var currentYear = (new Date).getFullYear();
$(document).ready(function() {
    $("#copyright-year").text( (new Date).getFullYear() );
});

$(function(){
// IPad/IPhone
    var viewportmeta = document.querySelector && document.querySelector('meta[name="viewport"]'),
        ua = navigator.userAgent,

        gestureStart = function () {viewportmeta.content = "width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0";},

        scaleFix = function () {
            if (viewportmeta && /iPhone|iPad/.test(ua) && !/Opera Mini/.test(ua)) {
                viewportmeta.content = "width=device-width, minimum-scale=1.0, maximum-scale=1.0";
                document.addEventListener("gesturestart", gestureStart, false);
            }
        };

    scaleFix();
    // Menu Android
    if(window.orientation!=undefined){
        var regM = /ipod|ipad|iphone/gi,
            result = ua.match(regM)
        if(!result) {
            $('.sf-menu li').each(function(){
                if($(">ul", this)[0]){
                    $(">a", this).toggle(
                        function(){
                            return false;
                        },
                        function(){
                            window.location.href = $(this).attr("href");
                        }
                    );
                }
            })
        }
    }
});
var ua=navigator.userAgent.toLocaleLowerCase(),
    regV = /ipod|ipad|iphone/gi,
    result = ua.match(regV),
    userScale="";
if(!result){
    userScale=",user-scalable=0"
}
document.write('<meta name="viewport" content="width=device-width,initial-scale=1.0'+userScale+'">')

$('.btn').each(function(){
    var title = $(this).html();
    $(this).addClass('custom_hover');
    $(this).html('<span><span>'+title+'</span><strong>'+title+'</strong></span>');
});



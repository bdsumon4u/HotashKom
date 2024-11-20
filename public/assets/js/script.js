(function($) {
    "use strict";
    $(".mega-menu-container.menu-content").css("display", "none");
     $(".header-search").click(function(){
        $(".search-full").addClass("open");
    });

    $(".close-search").click(function(){
        $(".search-full").removeClass("open");
        $("body").removeClass("offcanvas");
    });    

    $(".mobile-toggle").click(function(){
        $(".nav-menus").toggleClass("open");
    });
    $(".mobile-toggle-left").click(function(){
        $(".left-header").toggleClass("open");
    });
   //  $(".header-search").click(function(){
   //     $(".form-control-plaintext").toggleClass("open");
   // });
    $(".bookmark-search").click(function(){
        $(".form-control-search").toggleClass("open");
    })
    $(".filter-toggle").click(function(){
        $(".product-sidebar").toggleClass("open");
    });
    $(".toggle-data").click(function(){
        $(".product-wrapper").toggleClass("sidebaron");
    });

    $(".form-control-search input").keyup(function(e){
        if(e.target.value) {
            $(".page-wrapper").addClass("offcanvas-bookmark");
        } else {
            $(".page-wrapper").removeClass("offcanvas-bookmark");
        }
    });





    $(".search-full input").keyup(function(e){
        console.log(e.target.value);
        if(e.target.value) {
            $("body").addClass("offcanvas");
        } else {
            $("body").removeClass("offcanvas");
        }
    });    

    $('body').keydown(function(e){
        if(e.keyCode == 27) {

            $('.search-full input').val('');
            $('.form-control-search input').val('');
            $('.page-wrapper').removeClass('offcanvas-bookmark');
            $('.search-full').removeClass('open');
            $('.search-form .form-control-search').removeClass('open');
            $("body").removeClass("offcanvas");
        }
    });

     $(".mode").on("click", function () {
        $('.mode i').toggleClass("fa-moon-o").toggleClass("fa-lightbulb-o");
        $('body').toggleClass("dark-only");
    });       
     
    // $(".search-full input").focus(function(e){
    //     $("body").addClass("offcanvas");
    // });



})(jQuery);

$('.loader-wrapper').fadeOut('slow', function() {
    $(this).remove();
});

$(window).on('scroll', function() {
    if ($(this).scrollTop() > 600) {
        $('.tap-top').fadeIn();
    } else {
        $('.tap-top').fadeOut();
    }
});

$('.tap-top').click( function() {
    $("html, body").animate({
        scrollTop: 0
    }, 600);
    return false;
});

function toggleFullScreen() {
    if ((document.fullScreenElement && document.fullScreenElement !== null) ||
        (!document.mozFullScreen && !document.webkitIsFullScreen)) {
        if (document.documentElement.requestFullScreen) {
            document.documentElement.requestFullScreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullScreen) {
            document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
}
(function($, window, document, undefined) {
    "use strict";
    var $ripple = $(".js-ripple");
    $ripple.on("click.ui.ripple", function(e) {
        var $this = $(this);
        var $offset = $this.parent().offset();
        var $circle = $this.find(".c-ripple__circle");
        var x = e.pageX - $offset.left;
        var y = e.pageY - $offset.top;
        $circle.css({
            top: y + "px",
            left: x + "px"
        });
        $this.addClass("is-active");
    });
    $ripple.on(
        "animationend webkitAnimationEnd oanimationend MSAnimationEnd",
        function(e) {
            $(this).removeClass("is-active");
        });
})(jQuery, window, document);


// active link

$(".chat-menu-icons .toogle-bar").click(function(){
    $(".chat-menu").toggleClass("show");
});




$(".mobile-title svg").click(function(){
    $(".mega-menu-container").toggleClass("d-block");
});

$(".onhover-dropdown").on("click", function(){
    $(this).children('.onhover-show-div').toggleClass("active");
});

// $(".bg-overlay").on("click", function(){
//     $(".bg-overlay").toggleClass("active");
//     console.log("test");
// });

// if ($(window).width() < 991){
//     $('<div class="bg-overlay"></div>').appendTo($('body'));
// }


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
})

$(document).on('click', '[data-action="delete"]', function (e) {
    e.preventDefault();
    
    if (! confirm('Are you sure to delete?')) {
        return;
    }

    $(this).text('Deleting..').addClass('disabled');
    var table = $(this).parents('table');

    $.ajax({
        url: $(this).attr('href'),
        type: 'DELETE',
        dataType: 'json',
    }).always(function (data) {
        data.danger && $('.alert-box').html(`
            <div class="alert alert-danger">${data.danger}</div>
        `);
        data.success && $('.alert-box').html(`
            <div class="alert alert-success">${data.success}</div>
        `);
        table.DataTable().draw(false);
    });
})
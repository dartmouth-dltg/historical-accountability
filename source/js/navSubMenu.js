$(document).ready(function(){
    // toggle sub menu on desktop header nav

    $('.navigation > li').mouseover( function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
        $(this).children().show();
    });

    $('#primary-content').mouseover(function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
    });

    $(window).resize(function() {
        $(".navigation > li > ul").css("top", $("header").height() + 10);
    });
    
    $(window).trigger('resize');

});
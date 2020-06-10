$(document).ready(function(){
    // toggle sub menu on desktop header nav

    $('.navigation > li').mouseover( function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
        $(this).children().show();
    });

    $('nav.desktop-nav > .navigation > li > ul > li').mouseover( function() {
        $("nav.desktop-nav > .navigation > li > ul > li > ul").hide();
        $(this).children().show();
    });

    $('nav.desktop-nav > .navigation > li > ul > li > a').mouseover( function() {
        $('nav.desktop-nav > .navigation > li > ul > li > a').css("color", "#1d2c27");
        $(this).css("color", "grey");
    });

    $('#primary-content').mouseover(function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
    });
    
    $('#splash').mouseover(function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
    });
    
    
    $(window).resize(function() {
        
        if ( $('#user-bar').length !== 0 ) {
            $(".navigation > li > ul").css("top", $("header").height() + 43);
        }
        else {
            $(".navigation > li > ul").css("top", $("header").height() + 5);
        }

    });
    if ( $('#user-bar').length !== 0 ) {
        $(".navigation > li > ul").css("top", $("header").height() + 43);
    }
    else {
        $(".navigation > li > ul").css("top", $("header").height() + 5);
    }
    $(window).trigger('resize');

});
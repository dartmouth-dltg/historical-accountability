$(document).ready(function(){
    // toggle sub menu on desktop header nav
    $(document).on("click", ".submenu-arrow > span", function(e) {
        $("nav.desktop-nav > .navigation > li > ul").hide();
        $(".submenu-arrow > span").css("border-top", "10px solid white");
        $(this).css("border-top", "10px solid #F2E55B");
        var parent = $(this).parent();
        parent.children().slideDown("ul");
    });
    $('header').click( function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
    });
    $('#primary-content').click(function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
    });
    
    $('#splash').click(function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
    });
    

    // add class to menu items with subnav to dislay down arrow
    var withMenutags = $(".navigation > li").filter(function() {
        return $(this).children("ul").length !== 0;
    });
    withMenutags.addClass("submenu-arrow");
    withMenutags.append("<span></span>");
   

    //get subnav to display properly without admin bar
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
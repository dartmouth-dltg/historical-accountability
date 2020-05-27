$(document).ready(function(){
    // toggle mobile/accessibility menu
    $(".access-button").click(function(){
        $(".access-dropdown").toggle();
        $(this).toggleClass('up-arrow-toggle'); //change direction of arrow
    });
    $(".menu-access-button").click(function(){
        $(".access-dropdown-mobile").toggle();
        $(this).toggleClass('up-arrow-toggle');
    });

    function darkOn(){
        $("body").addClass('dark');
        $('.dark-switch > input[type="checkbox"]').prop("checked", true); //sync mobile and desktop btns
        $("#huronia-splash-logo").attr("src","./../../../themes/history/asset/img/svg/marks/recount_badge_white.svg");
        $(".down-arrow img").attr("src","./../../../themes/history/asset/img/svg/icons/down_chevron_white.svg");
        $(".flame img").attr("src","./../../../themes/history/asset/img/svg/paths/curve_left_flame_dark.svg");
    }

    function darkOff(){
        $('.dark-switch > input[type="checkbox"]').prop("checked", false); //sync mobile and desktop btns
        $("body").removeClass('dark');
        $("#huronia-splash-logo").attr("src","./../../../themes/history/asset/img/svg/icons/splash.home.logo.svg");
        $(".down-arrow img").attr("src","./../../../themes/history/asset/img/svg/icons/down_chevron_fire.svg");
        $(".flame img").attr("src","./../../../themes/history/asset/img/svg/paths/curve_left_flame.svg");
    }
    
    if(localStorage.getItem("darkmode1") === "on"){
        darkOn();
    }
    else if(localStorage.getItem("darkmode1") === "off"){
        darkOff();
    }

    $('.dark-switch > input[type="checkbox"]').click(function(){ //dark mode checkbox check
        if($(this).prop("checked") == true){
            localStorage.setItem("darkmode1", "on");
            darkOn();
        }
        else if($(this).prop("checked") == false){
            localStorage.setItem("darkmode1", "off");
            darkOff();
        }
    });
});
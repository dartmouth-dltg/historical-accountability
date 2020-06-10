$(document).ready(function(){
    //fix to prevent vacant splash area to produce blank space on window resize

    $(window).resize(function() {
        if ($('#splash').is(':empty')) {
            $("#splash").css("display", "none");
        }
    });
  
    $(window).trigger('resize');

});
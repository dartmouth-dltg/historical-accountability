/**
 * mobileMenu.js
 * 
 */
 
 $(document).ready(function(){
   
    var underlay = $('<div>').attr('id','nav-underlay').css({
      position: 'absolute',
      display: 'none',
      top: 0,
      left: 0,
      bottom: 0,
      right: 0,
      opacity: 0.8,
      'z-index': 998,
      'background-color': window.colours.black
    });
    
    $("body").append(underlay);

    $(".menu-access-button").click(function(){        
        $(".access-dropdown-mobile").slideToggle(window.animation.heartbeat);
        $('#nav-underlay').fadeToggle(window.animation.heartbeat);
        $(this).toggleClass('up-arrow-toggle');
    });
});

$(window).on('resize',function(){
  $('#nav-underlay').hide();
});
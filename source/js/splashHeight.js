(function($) {
  $(document).ready(function() {
    $('#splash').hide();
    
    calculateSplashHeight();
  
  });
  
  $(window).resize(window.jQuery.debounce( 250, calculateSplashHeight ));
  
  function calculateSplashHeight() {
    return
    var splash = $('#splash');
        
    if (splash.length == 0 || $(window).width() < breakpoint_stack) {
      if (!splash.is(':empty')) {
        splash.attr('style',''); // Unset previous setting on resize
      }
      return;
    }
       
    var clearance = 0;
     
    if ($('#user-bar').length > 0) {
       clearance += $('#user-bar').outerHeight(true);
    } 
     
    if ($('body > header').length > 0) {
       clearance += $('body > header').outerHeight(true);
    }
     
    splash.css('height','calc(500vh - ' + clearance + 'px)');
    
    if (splash.is(':empty')) {
      splash.hide();
    }


  }
  
})(jQuery);

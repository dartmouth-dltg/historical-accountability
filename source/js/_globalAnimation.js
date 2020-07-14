(function($) {    
  $(document).ready(function() {
    var style = getComputedStyle(document.body);
    
    window.animation = {};    
    window.animation.heartbeat = style.getPropertyValue('--animation-heartbeat');
    window.animation.slideTransition = style.getPropertyValue('--animation-slidetransition');
    
  });  
})(jQuery);
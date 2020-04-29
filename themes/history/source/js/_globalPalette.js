/**
 * globalPalette.js
 * Sets global values for colours based on CSS root variables provided by 20_colour SASS component. Must come early in the load order.
 */

(function($) {    
  $(document).ready(function() {
    var style = getComputedStyle(document.body);
        
    window.colours = {};
    window.colours.colour = {};
    window.colours.map = {};
    window.colours.neutrals = {};
    
    var colourGridKeys = ['shade','primary','tint','fade','watermark'];

    for(var i=1; i<10; i++) {
      var neutralKey = i * 10;
      window.colours.colour[i] = style.getPropertyValue('--colour--' + i);
            
      window.colours.neutrals[neutralKey] = style.getPropertyValue('--colour--neutral--' + neutralKey);
      
      window.colours.map[i] = {};
      
      colourGridKeys.forEach(function(key){
        window.colours.map[i][key] = style.getPropertyValue('--colour--' + i + '--' + key);
      });
    }
        
    window.colours.black = style.getPropertyValue('--colour--black');
    window.colours.white = style.getPropertyValue('--colour--white');    
    window.colours.impact = style.getPropertyValue('--colour--impact');    
    window.colours.cta = style.getPropertyValue('--colour--cta'); 
    
    // console.log(window.colours);   
    
  });  
})(jQuery);

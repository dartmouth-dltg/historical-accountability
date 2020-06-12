/**
 *  @file imageRatio.js
 *
 *  Audits page images and adds a class indicating their width to height ratio.
 *
 *  Requires: imagesloaded and ev-emitter packages (load via NPM).
 *
 */

jQuery(document).ready(function() {
  
  $('main').imagesLoaded(function(){
    
    $(this.images).each(function(i,o) {
      var img = $(o.img);
            
      var h = o.img.naturalHeight;
      var w = o.img.naturalWidth
      
      var aspectClass = 'square';
      
      if (w > h) {
        aspectClass = 'landscape';
      } else if (h > w) {
        aspectClass = 'portrait';
      }
          
      img.addClass('img-' + aspectClass);
      
      img.closest('figure').addClass('figure-' + aspectClass);
      
      if (img.parent('a').length > 0) {
        img.parent('a').addClass('a-' + aspectClass);
      }
    });
        
  });

});


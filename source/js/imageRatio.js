/**
 *  @file imageRatio.js
 *
 *  Audits page images and adds a class indicating their width to height ratio.
 *
 *  Requires: imagesloaded and ev-emitter packages (load via NPM).
 *
 */

jQuery(document).ready(function() {
  
  // An instance has been found where the imagesLoaded function is returning undefined.
  // This is not consistent – so far it only appears when the Universal Viewer is loaded.
  // @todo: Continue to investigate. In the meantime perform the required checks.
    
  if ($('main').length > 0 && typeof $('main').imagesLoaded === 'function') {  
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
  }

});


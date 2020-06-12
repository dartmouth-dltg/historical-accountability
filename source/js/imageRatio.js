/**
 *  @file imageRatio.js
 *
 *  Audits page images and adds a class indicating their width to height ratio.
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
      
      console.log(img.attr('src'));
      console.log("w: " + w);
      console.log("h: " + h);
      
      img.addClass('img-' + aspectClass);
      
      img.closest('figure').addClass('figure-' + aspectClass);
      
      if (img.parent('a').length > 0) {
        img.parent('a').addClass('a-' + aspectClass);
      }
    });
        
  });

});


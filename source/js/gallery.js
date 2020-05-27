/**
 *  @file gallery.js
 *  @description Drives gutenberg slider.
 */
 
(function($) {  
  $(document).ready(function() {
    
    var i=0
    $('.gallery').each(function() {
      var gallery = $(this);
      gallery.find('.lightbox').attr('rel',"gallery" + i);
      i++;
      
    });
    
    $('.lightbox').each(function(){
      $(this).colorbox({
        className: 'full', // Important: Required by grid system.
        slideShow: function() { return $(this).parents('.gallery').length > 1 ? true : false; },
        slideshowAuto: false,
        width: '100%',
        height: '100%',
        close: 'Close',
        title: function() {
          var title = $(this).attr('title').length != 0 ? $(this).attr('title') : null;
          var caption = $(this).attr('data-caption').length != 0 ? $(this).attr('data-caption') : null;
          return (title ? '<h3>' + title + '</h3>' : '') + (caption ? "<div class='lightbox--caption'>" + caption + "</div>" : '');
        }
      });
    });
    
  });
})(jQuery);

// If the following elements are first in the content stack it will add regional classes to them.
// This allows a mechanism to allow regional assignments to third-party code.
/*(function($) {
    $(document).document(function() {
      var first_splash_elements = ['.tl-timeline'];
      $('main > div').children().each(function(){
        for(var i=0; i<first_splash_elements.length; i++) {
          var testClass = first_splash_elements[i];
          
          if (typeof $(this).attr('id') != 'undefined') {
            console.log($(this).attr('id'));
          }
          
          if ($(this).hasClass(testClass)) {
            $(this).addClass('.region').attr('data-target-region-id');
          }
          
        }
      });
    });
})(jQuery);*/
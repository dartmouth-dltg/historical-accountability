(function($) {
  //Get the button:
  $(document).ready(function(){
    topButton = $("#back-to-top-btn");
    
    topButton.click(function(){
      $("html, body").animate({ scrollTop: 0 }, "slow");
      return false;
    });
    
    $(window).on('load scroll', function() {
      console.log('foo');
      console.log($('body').scrollTop());
      if ($(window).scrollTop() > 400) {
        topButton.show();
      }
      else topButton.hide();
    });
  });
})(jQuery);
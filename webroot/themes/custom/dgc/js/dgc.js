(function ($, Drupal) {

  Drupal.behaviors.foundation = {
    attach: function (context, settings) {
      $('body').once('foundation').each(function (e) {
        $(document).foundation();
      });
    }
  };

  Drupal.behaviors.scrollTop = {
    attach: function(context, settings) {
      $('.back-to-top', context).once('scroll-processed').each(function () {
        var $top = $(this);

    		// Store the initial position of the carat button
    		$(window).scroll(function() {
    			if ($(document).scrollTop() > 100) {
    				// if so, ad the fixed class
    				$top.fadeIn(100);
    				$top.addClass('show');
    			}
    			else {
    				// otherwise remove it
    				$top.fadeOut(200);
    				$top.removeClass('show');
    			}
    		});
  
    		$top.click(function() {
    			$('html, body').animate({
    				scrollTop: $('html, body').offset().top
    			}, 500);
    		});
      });
    }
  };

})(jQuery, Drupal);

(function ($, Drupal) {
  Drupal.behaviors.dgcSlickSettings = {
    attach: function (context, settings) {
      $(".process-items", context).slick({
        infinite: false,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
          {
            breakpoint: 860,
            settings: {
              slidesToShow: 2,
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
            }
          },
        ]
    });
    }
  };
} (jQuery, Drupal));

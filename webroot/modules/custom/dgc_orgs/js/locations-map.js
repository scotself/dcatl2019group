(function ($, Drupal) {

  Drupal.behaviors.locationsMap = {
    attach: function(context, settings) {
      $('.view-locations-map.view-display-id-list .views-row a', context).once('map-marker').each(function (index) {
        $(this).click(function() {
					google.maps.event.trigger(Drupal.geolocation.maps[0].mapMarkers[index], 'click');
					return false;
				});
      });
      $('.view-locations-map.view-display-id-map .attachment-before', context).once('equal-height').each(function (index) {
        $(this).scrollbar();
      });
    }
  };

})(jQuery, Drupal);

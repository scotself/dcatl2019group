(function ($, Drupal) {

  Drupal.behaviors.teamListHeight = {
    attach: function(context, settings) {
      $('.paragraph--type--about-team', context).once('equal-height').each(function (index) {
        var height = $('.paragraph--type--about-team-member.paragraph--view-mode--map-team-w-images').height();
        var $membersList = $('.field--name-field-about-team-member', this);
        $membersList.css('max-height', height);
        $membersList.scrollbar({
          "onInit": function(){
            $membersList.css('max-height', height);
          }
        });
      });
    }
  };

})(jQuery, Drupal);

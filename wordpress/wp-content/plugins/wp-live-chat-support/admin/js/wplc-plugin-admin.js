(function($) {
  'use strict';

  $(function() {
    $('#show_all_pages').change(function() {
      if (this.checked) {
        $('.wplc_pages_scrollbox').addClass('wplc_box_disabled');
        $('.wplc_pages_scrollbox input').click(function() { return false; });
      } else {
        $('.wplc_pages_scrollbox').removeClass('wplc_box_disabled');
        $('.wplc_pages_scrollbox input').off('click');
      }
    });

    if ($('#show_all_pages').is(':checked')) {
      $('.wplc_pages_scrollbox input').click(function() { return false; });
    }

  });

})(jQuery);
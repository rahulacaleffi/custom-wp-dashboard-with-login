/**
 * Color Picker Initialization Script
 *
 * Initializes the WordPress color picker for custom dashboard settings.
 * This script is loaded only on the plugin's settings page.
 *
 * @package Custom_WP_Dashboard
 * @since   1.0.0
 */
(function ($) {
  "use strict";

  $(function () {
    // Initialize color picker for all inputs with the class custom-dashboard-color-picker
    $(".custom-dashboard-color-picker").wpColorPicker();
  });
})(jQuery);

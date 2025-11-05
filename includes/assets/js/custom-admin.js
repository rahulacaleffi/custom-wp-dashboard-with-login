jQuery(document).ready(function ($) {
  console.log("Custom dashboard loaded");

  // Admin bar customization
  $("#wpadminbar").css("opacity", "1");
  $("#wp-admin-bar-root-default").children().not("#wp-admin-bar-new-content").hide();
  $("#wp-admin-bar-new-content a span.ab-label").html("Add new");
  $("#wpadminbar .quicklinks ul#wp-admin-bar-top-secondary > li:first-child > a").html("User information");

  var newDiv = $("<div>").addClass("custom-quicklinks-container");
  $("#wpadminbar .quicklinks ul#wp-admin-bar-top-secondary").appendTo(newDiv);

  // Add support link if enabled
  if (typeof customDashboardSettings !== "undefined" && customDashboardSettings.enableSupport) {
    var supportLink =
      '<div class="custom-support-link"><a href="' +
      customDashboardSettings.supportUrl +
      '" target="_blank" title="' +
      customDashboardSettings.supportText +
      '">' +
      customDashboardSettings.supportText +
      "</a></div>";
    $(newDiv).append(supportLink);
  }

  $("#wpadminbar .quicklinks").append(newDiv);

  // Add copyright if set
  if (typeof customDashboardSettings !== "undefined" && customDashboardSettings.copyrightText) {
    var copyrightText = customDashboardSettings.copyrightText.replace("{year}", customDashboardSettings.currentYear);
    var copyrightMessage = '<div class="custom-copyright">' + copyrightText + "</div>";
    $("body").append(copyrightMessage);
  }
});

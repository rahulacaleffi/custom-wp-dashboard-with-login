jQuery(document).ready(function ($) {
  // Handle Upload/Select Image button click
  $(".custom-dashboard-upload-image").on("click", function (e) {
    e.preventDefault();

    var button = $(this);
    var targetId = button.data("target");
    var targetInput = $("#" + targetId);
    var previewContainer = button.siblings(".custom-dashboard-image-preview");

    // Create a new media uploader for each button click
    var mediaUploader = wp.media({
      title: "Select or Upload Image",
      button: {
        text: "Use this image",
      },
      multiple: false,
      library: {
        type: "image",
      },
    });

    // When an image is selected
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();

      // Set the URL in the input field
      targetInput.val(attachment.url);

      // If preview container doesn't exist, create it
      if (previewContainer.length === 0) {
        previewContainer = $('<div class="custom-dashboard-image-preview"></div>');
        button.after(previewContainer);
      }

      // Update the preview
      previewContainer.html('<img src="' + attachment.url + '" alt="Preview" />' + '<a href="#" class="custom-dashboard-remove-image" data-target="' + targetId + '">Remove image</a>');
    });

    // Open the media uploader
    mediaUploader.open();
  });

  // Handle Remove Image link click
  $(document).on("click", ".custom-dashboard-remove-image", function (e) {
    e.preventDefault();

    var link = $(this);
    var targetId = link.data("target");
    var targetInput = $("#" + targetId);
    var previewContainer = link.closest(".custom-dashboard-image-preview");

    // Clear the input field
    targetInput.val("");

    // Remove the preview
    previewContainer.remove();
  });
});

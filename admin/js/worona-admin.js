console.log("worona-admin.js");
jQuery(document).on('ready', function () {
    //Show & Hide "Insert AppID form"
    jQuery('#insert-app-id').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery('#app-id-form').toggle();
    });

    //Create App via AJAX
    jQuery('#create-worona-app').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery.ajax({
          url: ajaxurl,
          method: "POST",
          data: {
              action: 'worona_create_app',
          },
          success: function (response) {

          },
          error: function () {
  
          }
      });
    });
});

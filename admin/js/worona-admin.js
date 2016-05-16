jQuery(document).on('ready', function () {
    //disabling # links
    jQuery('a[href^="#"]').click(function(e) {
      e.preventDefault();
    });

    //Show & Hide "Insert AppID form"
    jQuery('#insert-app-id').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery('#lateral-change-appid').toggle();
    });

    jQuery('#open-change-appid').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery('#lateral-change-appid').toggle();
    });

    //Create App via AJAX
    jQuery('#create-worona-app').on('click', function (e) {
      jQuery('#create-worona-app').addClass('is-loading');
      e.preventDefault();
      e.stopPropagation();
      jQuery.ajax({
          url: ajaxurl,
          method: "POST",
          data: {
              action: 'worona_create_app',
          },
          success: function (response) {
            if (response.hasOwnProperty('status') && response.status == 'ok' ) {
              jQuery('#label-create-buttons').toggle();
              jQuery('#label-created').toggle();
              jQuery('progress')[0].value = 100;
              jQuery('#step-message').text('You are on step 4/4');
              jQuery('#lateral-info-box').append('<hr><h2>Worona App ID:</h2>'+response.appId);
            }
          },
          error: function () {

          }
      });
    });

    //Change App ID via ajax
    jQuery('#change-app-id').on('click', function(e) {
      jQuery('#change-app-id').addClass('is-loading');
      e.preventDefault();
      e.stopPropagation();
      jQuery.ajax({
        url: ajaxurl,
        method: "POST",
        data: {
            action: 'worona_change_appid',
            appId: jQuery('input#worona-appid').val()
        },
        success: function (response) {
          if (response.hasOwnProperty('status') && response.status == 'ok' ) {
            jQuery('#lateral-change-appid').hide();
            jQuery('#label-create-buttons').hide(); //they can be hidden already
            jQuery('#label-created').show(); //it can be displayed already
            jQuery('progress')[0].value = 100;
            jQuery('#step-message').text('You are on step 4/4');
            jQuery('span#worona-appid-info').text(jQuery('input#worona-appid').val()) //DOESN'T WORK WHEN COMING FROM STEP 3
            //jQuery('#lateral-info-box').append('<hr><h2>Worona App ID:</h2>'+response.id);
          } else if( response.hasOwnProperty('status') && response.status == 'error') {

          }
        },
        error: function (response) {

        }
      });
    });
});

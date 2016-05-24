function validateEmail(email) {
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}

jQuery(document).on('ready', function () {
    //disabling # links
    jQuery('a[href^="#"]').click(function(e) {
      e.preventDefault();
    });

    //Show "Insert AppID form"
    jQuery('.open-change-appid').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery('#lateral-change-appid').show();
    });

    jQuery('.close-change-appid').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery('#lateral-change-appid').hide();
      jQuery('#lateral-error-appid').hide();
    });

    jQuery('.close-error-appid').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      jQuery('#lateral-error-appid').hide();
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
              jQuery('#worona-appid-lateral').show();
              jQuery('span#worona-appid-span').text(response.appId);
              jQuery('input#worona-appid').val(response.appId);
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
      var id = jQuery('input#worona-appid').val();

      if ( id.length !=17 || id.includes(' ')){
        jQuery('#lateral-error-appid').show();
        jQuery('#appid-error-message').text("Invalid App ID");
        jQuery('#change-app-id').removeClass('is-loading');
      } else {
        jQuery.ajax({
          url: ajaxurl,
          method: "POST",
          data: {
              action: 'worona_change_appid',
              appId: jQuery('input#worona-appid').val()
          },
          success: function (response) {
            if (response.hasOwnProperty('status') && response.status == 'ok' ) {
              jQuery('#change-app-id').removeClass('is-loading');
              jQuery('#lateral-error-appid').hide();
              jQuery('#lateral-change-appid').hide();
              jQuery('#label-create-buttons').hide(); //they can be hidden already
              jQuery('#label-created').show(); //it can be displayed already
              jQuery('progress')[0].value = 100;
              jQuery('#step-message').text('You are on step 4/4');
              jQuery('#worona-appid-lateral').show();
              jQuery('span#worona-appid-span').text(jQuery('input#worona-appid').val());
            } else if( response.hasOwnProperty('status') && response.status == 'error') {
              jQuery('#lateral-error-appid').show();
              jQuery('#appid-error-message').text(response.reason);
              jQuery('#change-app-id').removeClass('is-loading');
            }
          },
          error: function (response) {
            jQuery('#lateral-error-appid').show();
            jQuery('#appid-error-message').text("The App ID couldn't be modified. Please try again.");
            jQuery('#change-app-id').removeClass('is-loading');
          }
        });
      }
    });

    /*support emails*/
    //enable & disable change email button
    jQuery('#support-email').on('input', function(){
      var newEmail = jQuery('#support-email').val();
      var currentEmail = jQuery('#current-support-email').val();

      if ((newEmail != currentEmail) && validateEmail(newEmail)){
        jQuery('#change-support-email').removeClass('disabled');
      } else {
        jQuery('#change-support-email').addClass('disabled');
      }
    });

    //change email
    jQuery('#change-support-email').on('click',function(){

    });

    //unsubscribe / subscribe email support
    jQuery('#receive-support-emails').on('change',function(){
      if(jQuery('#receive-support-emails').attr('checked')){
        var newEmail = jQuery('#support-email').val();
        var currentEmail = jQuery('#current-support-email').val();

        if ( (newEmail != currentEmail) && validateEmail(newEmail)){
          jQuery('#change-support-email').removeClass('disabled');
        }
        
        jQuery('#support-email').attr('disabled',false);
      } else {
        jQuery('#support-email').attr('disabled',true);
        jQuery('#change-support-email').addClass('disabled');
      }
    });
});

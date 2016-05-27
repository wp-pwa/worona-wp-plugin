function validateEmail(email) {
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}

jQuery(document).on('ready', function () {
  jQuery('#contact-form').on('submit',function(e){
    e.preventDefault();
    e.stopPropagation();

    var name = jQuery('#form-name').val();
    var email = jQuery('#form-email').val();
    var message = jQuery('#form-message').val();
    var subject = jQuery('#form-subject option:selected').val();
    var errors = false;

    if(!validateEmail(email)){
      jQuery('#form-email').addClass('is-danger');
      errors = true;
    }
    if(message.length<0){
      jQuery('#form-message').addClass('is-danger');
      errors = true;
    }

    if( !errors ) {
      jQuery('#form-email').removeClass('is-danger');
      jQuery('#form-message').removeClass('is-danger');

      jQuery('#submit-button').addClass('disabled');
      jQuery('#form-email').attr('disabled',true);
      jQuery('#form-message').attr('disabled',true);
      jQuery('#form-subject').attr('disabled',true);
      jQuery('#form-name').attr('disabled',true);

      jQuery.ajax({
          url: ajaxurl,
          method: "POST",
          data: {
              email : email,
              message: message,
              name : name,
              subject : subject,
              action : 'worona_send_contact_form'
          },
          success: function (response) {
            jQuery('#submit-button').addClass('disabled');
            jQuery('#form-email').attr('disabled',true);
            jQuery('#form-message').attr('disabled',true);
            jQuery('#form-subject').attr('disabled',true);
            jQuery('#form-name').attr('disabled',true);

            if (response.hasOwnProperty('status') && response.status == 'ok' ) {

            } else if (response.hasOwnProperty('status') && response.status == 'error') {
              
            }
          },
          error: function () {
            jQuery('#submit-button').addClass('disabled');
            jQuery('#form-email').attr('disabled',true);
            jQuery('#form-message').attr('disabled',true);
            jQuery('#form-subject').attr('disabled',true);
            jQuery('#form-name').attr('disabled',true);
          }
      });
    }

  });

  jQuery('#plugin-review').on('click',function(){
    window.open('https://wordpress.org/support/view/plugin-reviews/worona?filter=5','_blank');
  });

});

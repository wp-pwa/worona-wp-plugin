<?php
  $settings = get_option('worona_settings');
  $support_email = $settings["worona_support_email"];

  $current_user = wp_get_current_user();

  if(!empty($current_user->display_name)) {
    $name = $current_user->display_name;
  } else if (!empty($current_user->user_firstname)) {
    $name = $current_user->user_firstname;
  } else {
    $name = $current_user->user_login;
  }
?>
<div class="wrap">
  <span class="title is-2">Worona</span><span class="subtitle is-4">Contact & Help</span>
  <div class="section">
    <div class="columns">
      <div class="is-half column">
        <div class="box">
          <p class="has-text-centered">
            <span class="title is-4 ">Contact us</span>
          </p>
          <hr>
          <div id="contact-form-sent" style="display:none;">
          
          </div>
          <form id="contact-form">
            <div class="control is-horizontal">
              <div class="control-label">
                <label class="label">From</label>
              </div>
              <div class="control is-grouped">
                <input id="form-name" class="input" type="text" placeholder="name" value="<?php echo $name;?>">
                <input id="form-email" class="input" type="email" placeholder="email" value="<?php echo $support_email; ?>">
              </div>
            </div>
            <div class="control is-horizontal">
              <div class="control-label">
                <label class="label">Subject</label>
              </div>
              <div class="control">
                <div class="select is-fullwidth">
                  <select id="form-subject">
                    <option>General enquiry</option>
                    <option>Technical Support</option>
                    <option>Sales enquiry</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="control is-horizontal">
              <div class="control-label">
                <label class="label">Message</label>
              </div>
              <div class="control">
                <textarea id="form-message" class="textarea" placeholder="Explain how we can help you"></textarea>
              </div>
            </div>
            <div class="control">
              <div class="has-text-centered">
                <input id="submit-button" class="button is-medium" type="submit" value="Send"/>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="is-half column">
        <div class="box">
          <p class="has-text-centered">
            <span class="title is-4 ">Support us</span>
          </p>
          <hr>
          <p>
            If you are happy with Worona we will truly appreciate a <strong>positive review</strong> of the plugin.
            It help us a lot, because more people will discover us.
          </p>
          <br>
          <p>

              <div class="has-text-centered">
                <input id="plugin-review" class="button is-medium is-info" type="submit" value="Review"/>
              </div>

            <br>
            <div class="has-text-centered">It will take you only 2 minutes ☺</div>
          </p>
        </div>
        <div class="columns has-text-centered">
          <div class="column is-third">
              <a href="https://wordpress.org/support/plugin/worona" target="_blank">
                Support forum <span class="icon is-small"><i class="fa fa-external-link" aria-hidden="true"></i></span>
              </a>
          </div>
          <div class="column is-third">
              <a href="https://www.worona.org" target="_blank">
                Worona.org <span class="icon is-small"><i class="fa fa-external-link" aria-hidden="true"></i></span>
              </a>
          </div>
          <div class="column is-third">
            <a href="https://twitter.com/getworona" class="twitter-follow-button" data-show-count="false">Follow @getworona</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
          </div>
        </div>
      </div>
    </div> <!-- columns -->
  </div><!-- section -->
</div><!-- wrap -->

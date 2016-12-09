<?php
	global $worona;

	$settings = get_option('worona_settings');
	//var_dump($settings);
	//delete_option('worona_settings');
	$current_user = wp_get_current_user();

	if (isset($settings["synced_with_worona"])) {
		$synced_with_worona = $settings["synced_with_worona"];
	} else {
		$synced_with_worona = false;
	}

	/* step, progress & GTM events */
	$progress = 0;
	$step = 0;
	$wp_version = get_bloginfo('version');
	$rest_api_installed = $worona->rest_api_installed;
	$rest_api_active = $worona->rest_api_active;
	$rest_api_compatible = true;

	if (version_compare($wp_version, '4.7', '>=')) { //From WP 4.7, the REST API is already installed.
		$rest_api_installed = true;
		$rest_api_active = true;
	}

	if (version_compare($wp_version, '4.4', '<')) { //REST API Plugin is only compatible from WP 4.4 ahead
		$rest_api_compatible = false;
		$gtm_event = "rest-api-not-compatible";
	} else if (!$rest_api_installed) {
		$step = 1;
		$gtm_event = "plugin-active";
	} else if ($rest_api_installed && !$rest_api_active) {
		$step = 2;
		$progress = 33;
		$gtm_event = "rest-api-installed";
	} else if ( $rest_api_installed && $rest_api_active && !$synced_with_worona) {
		$step = 3;
		$progress = 66;
		$gtm_event = "rest-api-active";
	} else if ( $rest_api_installed && $rest_api_active && $synced_with_worona) {
		$step = 4;
		$progress = 100;
		$gtm_event = "plugin-configured";
	}
?>
<div class="wrap">
	<p class="title is-2">Worona</p>
	<div class="columns">
		<div class="column is-half">
			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">1. Install REST API Plugin</p>
					</div>
					<div class="level-right">
						<?php echo ( $rest_api_installed ? '<span class="tag is-success">Installed&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-check-circle" aria-hidden="true"></i></span></span>':'');?>
						<?php echo ( !$rest_api_compatible ? '<span class="tag is-danger">Error&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span></span>':'');?>
					</div>
				</nav>
				<?php if(!$rest_api_compatible):?>
		 		 <article class="message is-danger">
		 		   <div class="message-body">
		 		     <strong>Attention!</strong> The REST API Plugin requires WordPress 4.4 or higher, your WordPress version is <?php echo get_bloginfo('version');?>
		 		   </div>
		 		 </article>
		 		<?php elseif ($step==1): ?>
				<div class="content">
					<p>
						Worona uses the <a href="http://v2.wp-api.org/" target="_blank">REST API</a> plugin to send the content from your site to the App.
						<?php
							if($rest_api_installed) {
									$install_api_href ="#";
							} else {
								$install_api_href = get_site_url() . '/wp-admin/plugin-install.php?tab=plugin-information&plugin=rest-api';
							}
						?>
					</p>
					<p>
						<a href="<?php echo $install_api_href; ?>" class="button button-lg button-primary" <?php echo ($step<=1 ? 'style="color:white;"' : 'style="display:none;"'); ?>>Download Plugin</a>
					</p>
				</div>
				<?php endif;?>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">2. Activate REST API Plugin</p>
					</div>
					<div class="level-right">
						<?php echo ( $rest_api_active  ? '<span class="tag is-success">Active&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-check-circle" aria-hidden="true"></i></span></span>':'');?>
					</div>
				</nav>
				<?php if ($step<=2 && $rest_api_compatible): ?>
				<div class="content">
					<p>
						Remember to activate the WP REST API Plugin
					</p>
					<p>
						<?php
							if($rest_api_installed || $rest_api_active ) {
									$activate_api_href =$worona->get_activate_wp_rest_api_plugin_url();
									$activate_class = "button button-lg button-primary";
							} else {
								$activate_api_href = "#";
								$activate_class = "button button-lg disabled";
							}
						?>
						<a href="<?php echo $activate_api_href; ?>" class="<?php echo $activate_class; ?>" style="color:white">Activate REST API Plugin</a>
					</p>
				</div>
				<?php endif;?>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">3. Register in Worona</p>
					</div>
					<div id='label-created' class="level-right" <?php echo ( $step > 3 ? '':'style="display:none;"');?>>
						<span class="tag is-success">Registered&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-check-circle" aria-hidden="true"></i></span></span>
					</div>
				</nav>
					<?php if ($step==3): ?>
					<div class="content">
						<p>
							Create an account in the Worona dashboard, and add this site.
						</p>
						<?php
							/*
								Params accepted by https://dashboard.worona.org/register
									?name
									?email
									?siteURL
									?siteName
									?siteId
							*/
							$name = "";
							$email = "";
							$siteURL = get_site_url();
							$siteName = get_bloginfo( 'name' );
							$siteId = $settings["worona_siteid"];

							$current_user = wp_get_current_user();
							if ($current_user instanceof WP_User) {
									$name = $current_user->user_firstname;
									if($name == '') {
										$name = $current_user->display_name;
									}
									$email = $current_user->user_email;
							}
						?>
						<input id="param-name" type="hidden" value="<?php echo $name; ?>">
						<input id="param-email" type="hidden" value="<?php echo $email; ?>">
						<input id="param-siteURL" type="hidden" value="<?php echo $siteURL; ?>">
						<input id="param-siteName" type="hidden" value="<?php echo $siteName; ?>">
						<input id="param-siteId" type="hidden" value="<?php echo $siteId; ?>">

						<p id="label-create-buttons">
							<a href="#" id="sync-with-worona" class="button button-hero button-primary">Register</a>
							or <a href="#" class="open-change-siteid">insert an existing Site ID</a>
						</p>
					</div>

					<?php elseif($step<3):?>
					<div class="content">
						<p>
							Create an account in the Worona dashboard, and add this site.
						</p>
						<p>
							<a href="#" class="button button-hero disabled">Register</a>
							or <span style="text-decoration: underline;">insert an existing Site ID</span>
						</p>
					</div>
					<?php endif;?>

			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">4. Configure your site</p>
					</div>
				</nav>
				<div class="content">
					<p>
						Go to the Worona Dashboard to preview your App, configure it and publish it to the stores.
					</p>
					<p>
						<?php
							$worona_dashboard_url = "https://dashboard.worona.org/site/" . $settings["worona_siteid"];

							if ($step==4) {
								$button_disabled = false;
							} else {
								$worona_dashboard_url = "#";
								$button_disabled = true;
							}
						?>
						<a id="dashboard-button" href="<?php echo $worona_dashboard_url ?>" target="_blank" style="color:white" class="button button-lg <?php echo ($button_disabled ? 'disabled button-hero' : 'button-primary button-hero'); ?>">Configure</a>
					</p>
				</div>
			</div>
	 </div><!-- column is-one-third -->
	 <div class="column">
	 </div>
	 <div class="column is-one-third">
		 <article class="message is-info">
			<div class="message-header">
			  Follow the 4 steps to configure the plugin
			</div>
			<div id="#lateral-info-box"class="message-body">
				<progress class="progress is-info is-medium" value="<?php echo $progress;?>" max="100"></progress>
				<p id="step-message">
					You are on <strong>step <?php echo $step;?>/4.</strong>
				</p>
				<?php if ($rest_api_active):?>
				<hr>
				<p>
					<h2>REST API URL:</h2>
					<?php print(rest_url()); ?>
				</p>
			  <?php endif;?>
				<div id="worona-siteid-lateral" <?php echo ($synced_with_worona?'':'style="display:none;"');?>>
				<p>
					<hr>
					<h2>Worona Site Id:</h2>
					<span id="worona-siteid-span"><?php echo $settings['worona_siteid'];?></span> <a class="open-change-siteid" href="#">(change)</a>
				</p>
				</div>
			</div>
		 </article>
		 <article class="message">
			 <div class="message-body">
				 <p class="control">
					 <strong>Worona Support</strong><br><br>
					 <label class="checkbox">
						 <input id="checkbox-plugin-support" type="checkbox" <?php echo ($settings['plugin_support'] ? 'checked' :''); ?>>
						 Do you want to receive support emails from our team?
					 </label>
				 </p>
			 </div>
		</article>
		 <article id="lateral-change-siteid" class="message is-warning" style="display:none;">
			 <div class="message-header">
					<nav class="level">
						<div class="level-left">
							<strong> Change Site Id</strong>
						</div>
						<div class="level-right">
							<a href="#" class="close-change-siteid" style="color:inherit"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
						</div>
					</nav>
			  </div>
			  <div class="message-body">
					<p>
						<strong>Warning!</strong> Changing your Site Id can create conflicts with the Dashboard and the App.
					</p>
					<br>
					<p>
						<article id="lateral-error-siteid" class="message is-danger" style="display:none;">
							<div class="message-body">
								<nav class="level">
									<div id="siteid-error-message" class="level-left">
										The siteid is not valid
									</div>
									<div class="level-right">
										<a href="#" class="close-error-siteid" style="color:inherit"><strong>x</strong></a>
									</div>
								</nav>
							</div>
						</article>
					</p>
					<table class="form-table">
						<tr>
							<th scope="row">Site Id</th>
							<td>
									<fieldset>
											<label>
													<input type="text" id="worona-siteid" value="<?php echo ($settings['synced_with_worona']) ? $settings['worona_siteid'] : ''; ?>"/>
													<br />
													<span class="description">Enter a valid Site Id</span>
											</label>
									</fieldset>
							</td>
						</tr>
					</table>
					<p>
						<a href="#" id="change-siteid"class="button button-lg">Change</a>
					</p>
			  </div>
		 </article>
	 </div><!-- column one-third-->
	</div><!-- columns -->
</div><!-- wrap -->
<!-- GTM iframe -->

<input type="hidden" name="wp-version" value="<?php echo get_bloginfo('version'); ?>">
<input type="hidden" name="wp-url" value="<?php echo get_bloginfo('wpurl'); ?>">
<input type="hidden" name="site-name" value="<?php echo get_bloginfo('name'); ?>">
<input type="hidden" name="user-name" value="<?php echo $current_user->user_firstname; ?>">
<input type="hidden" name="email" value="<?php echo $current_user->user_email; ?>">
<input type="hidden" name="wp-lan" value="<?php echo get_bloginfo('language'); ?>">
<input type="hidden" name="worona-version" value="<?php echo $worona->plugin_version; ?>">
<input type="hidden" name="worona-siteid" value="<?php echo $settings['worona_siteid']; ?>">

<?php if ($settings['plugin_support']) : ?>
	<?php
		$wp_version = "&wp-version=" . get_bloginfo('version');
		$wp_url = "&wp-url=" . get_bloginfo('wpurl');
		$site_name = "&site-name=" .get_bloginfo('name');
		$user_name = "&user-name=" . $current_user->user_firstname;
		$email = "&email=" . $current_user->user_email;
		$wp_lan = "&wp-lan=" . get_bloginfo('language');
		$worona_version = "&worona-version=" . $worona->plugin_version ;
		$worona_siteid = "&worona-siteid=" . $settings['worona_siteid'];

		$gtm_url = "https://plugin.worona.org/?event=" . $gtm_event . $wp_version . $site_name . $user_name .  $wp_url . $email . $wp_lan . $worona_version . $worona_siteid;
	 ?>

	<iframe id="gtm-iframe" src="<?php echo $gtm_url; ?>" width="1" height="1"></iframe>
<?php endif;?>

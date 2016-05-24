<?php

	//ONLY IN DEV
	//delete_option('worona_settings');
	//
	global $worona;

	$progress = 0;
	$step = 0;

	$current_user = wp_get_current_user();

	$rest_api_installed = $worona->rest_api_installed;
	$rest_api_active = $worona->rest_api_active;
	$settings = get_option('worona_settings');

	if (isset($settings["worona_app_created"])) {
		$worona_app_created = $settings["worona_app_created"];
	} else {
		$worona_app_created = false;
	}


	//Progress
	if ($rest_api_installed) {
		$progress = 33;
	}
	if ($rest_api_active) {
		$progress = 66;
	}
	if ($worona_app_created) {
		$progress = 100;
	}

	//step
	if (!$rest_api_installed ) {
		$step = 1;
	} else if ($rest_api_installed && !$rest_api_active) {
		$step = 2;
	} else if ( $rest_api_installed && $rest_api_active && !$worona_app_created) {
		$step = 3;
	} else if ( $rest_api_installed && $rest_api_active && $worona_app_created) {
		$step = 4;
	}
?>
<div class="wrap">
	<p class="title is-2">Worona</p>
	<div class="columns">
		<div class="column is-half">
			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">1. Install WP-API</p>
					</div>
					<div class="level-right">
						<?php echo ( $rest_api_installed ? '<span class="tag is-success">Installed</span>':'');?>
					</div>
				</nav>
				<div class="content">
					<p>
						Worona uses the <a href="http://v2.wp-api.org/" target="_blank">WP-API</a> plugin to send the content from your site to the App.
						<?php
							if($rest_api_installed) {
									$install_api_href ="#";
									$install_api_target="";
							} else {
								$install_api_href = get_site_url() . '/wp-admin/plugin-install.php?tab=plugin-information&plugin=rest-api';
								$install_api_target="_blank";
							}
						?>
					</p>
					<p>
						<a href="<?php echo $install_api_href; ?>" class="button button-lg" <?php echo ($step<=1 ? '' : 'style="display:none;"'); ?> target="<?php echo $install_api_target;?>">Download Plugin</a>
					</p>
				</div>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">2. Activate WP-API</p>
					</div>
					<div class="level-right">
						<?php echo ( $rest_api_active ? '<span class="tag is-success">Active</span>':'');?>
					</div>
				</nav>
				<div class="content">
					<p>
						This is why we installed the WP-API plugin.
					</p>
					<p <?php echo ($step<=2 ? '' : 'style="display:none;"');?>>
						<?php
							if($rest_api_active ) {
									$activate_api_href ="#";
									$activate_api_target="";
							} else {
								$activate_api_href = $worona->get_activate_wp_rest_api_plugin_url();
								$activate_api_target="_blank";
							}
						?>
						<a href="<?php echo $activate_api_href; ?>" target="<?php echo $activate_api_target;?>" class="button button-lg <?php echo ($step==2 ? '' : 'disabled'); ?>">Activate WP-API Plugin</a>
					</p>
				</div>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">3. Create App</p>
					</div>
					<div id='label-created' class="level-right" <?php echo ( $worona_app_created ? '':'style="display:none;"');?>>
						<span class="tag is-success">Created</span>
					</div>
				</nav>
				<div class="content">
					<p>
						This will create a Worona App ID, it will link your WordPress with the Worona App.
					</p>
					<p id="label-create-buttons"<?php echo ($step<=3 ? '' : 'style="display:none;"');?>>
						<a href="#" id="create-worona-app" class="button button-lg <?php echo ($step==3 ? '' : 'disabled'); ?>">Create App</a>
						or <a href="#" class="open-change-appid">insert an existing App ID</a>
					</p>
				</div>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">4. Go to the Dashboard</p>
					</div>
				</nav>
				<div class="content">
					<p>
						Go to the Worona Dashboard to preview your App, configure it and publish it to the stores.
					</p>
					<p>
						<?php

							 /**
								* @example Safe usage: $current_user = wp_get_current_user();
								* if ( !($current_user instanceof WP_User) )
								*     return;
								*/
							/*
							 echo 'Username: ' . $current_user->user_login . '<br />';
							 echo 'User email: ' . $current_user->user_email . '<br />';
							 echo 'User first name: ' . $current_user->user_firstname . '<br />';
							 echo 'User last name: ' . $current_user->user_lastname . '<br />';
							 echo 'User display name: ' . $current_user->display_name . '<br />';
							 echo 'User ID: ' . $current_user->ID . '<br />';
							 */
							if ($worona_app_created) {
								$worona_dashboard_url = "https://dashboard.worona.org";
								$worona_dashboard_target = "_blank";
							} else {
								$worona_dashboard_url = "#";
								$worona_dashboard_target = "";
							}
						?>
						<a href="<?php echo $worona_dashboard_url; ?>" target="<?php echo $worona_dashboard_target;?>" class="button button-lg <?php echo ($step==4 ? '' : 'disabled'); ?>">Dashboard</a>
					</p>
				</div>
			</div>
	 </div><!-- column is-one-third -->
	 <div class="column">
	 </div>
	 <div class="column is-one-third">
		 <? if($step < 3):?>
		 <article class="message is-warning">
		   <div class="message-body">
		     <strong>Attention!</strong> Some steps require to refresh this page.
		   </div>
		 </article>
		 <? endif;?>
		 <article class="message is-info">
			<div class="message-header">
			  Follow the steps to create the App
			</div>
			<div id="#lateral-info-box"class="message-body">
				<progress class="progress is-info is-medium" value="<?php echo $progress;?>" max="100"></progress>
				<p id="step-message">
					You are on step <?php echo $step;?>/4.
				</p>
				<? if ($rest_api_active):?>
				<hr>
				<p>
					<h2>WP-API URL:</h2>
					<?php print(rest_url()); ?>
				</p>
			  <? endif;?>
				<div id="worona-appid-lateral" <?php echo ($worona_app_created?'':'style="display:none;"');?>>
				<p>
					<hr>
					<h2>Worona App ID:</h2>
					<span id="worona-appid-span"><?php echo $settings['worona_appId'];?></span> <a class="open-change-appid" href="#">(change)</a>
				</p>
				</div>
			</div>
		 </article>
		 <article id="lateral-change-appid" class="message is-warning" style="display:none;">
			 <div class="message-header">
					<nav class="level">
						<div class="level-left">
							<strong>Change Worona APP ID</strong>
						</div>
						<div class="level-right">
							<a href="#" class="close-change-appid" style="color:inherit"><strong>x</strong></a>
						</div>
					</nav>
			  </div>
			  <div class="message-body">
					<p>
						<strong>Warning!</strong> Changing your App ID can create conflicts with the Dashboard and the App.
					</p>
					<br>
					<p>
						<article id="lateral-error-appid" class="message is-danger" style="display:none;">
							<div class="message-body">
								<nav class="level">
									<div id="appid-error-message" class="level-left">
										The AppID is not valid
									</div>
									<div class="level-right">
										<a href="#" class="close-error-appid" style="color:inherit"><strong>x</strong></a>
									</div>
								</nav>
							</div>
						</article>
					</p>
					<table class="form-table">
						<tr>
							<th scope="row">App ID</th>
							<td>
									<fieldset>
											<label>
													<input type="text" id="worona-appid" value="<?php echo (isset($settings['worona_appId'])) ? $settings['worona_appId'] : ''; ?>"/>
													<br />
													<span class="description">Enter a valid App ID</span>
											</label>
									</fieldset>
							</td>
						</tr>
					</table>
					<p>
						<a href="#" id="change-app-id"class="button button-lg">Change</a>
					</p>
			  </div>
		 </article>
		 <div class="box">
			 <nav class="level">
				 <div class="level-left">
					 <p class="title is-5">Support emails</p>
				 </div>
			 </nav>
			 <div class="content">
				 <p>
					 We will send support emails to the following address:
				 </p>
				 <nav class="level">
					 <div class="level-left">
						 <input type="text" id="support-email" value="<?php echo $current_user->user_email; ?>" />
						 <input type="hidden" id="current-support-email" value="<?php echo $current_user->user_email; ?>" />
					 </div>
					 <div class="level-right">
						 <a id="change-support-email" class="button disabled">Change</a>
					 </div>
				 </nav>
				 <p class="control">
  			 	<label class="checkbox">
    				<input id="receive-support-emails" type="checkbox" checked>
    					Receive support emails
  				</label>
				</p>
			 </div>
		 </div>
	 </div><!-- column one-third-->
	</div><!-- columns -->
</div><!-- wrap -->

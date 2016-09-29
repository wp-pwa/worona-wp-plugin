<?php
	global $worona;

	$progress = 0;
	$step = 0;

	$current_user = wp_get_current_user();

	$rest_api_installed = $worona->rest_api_installed;
	$rest_api_active = $worona->rest_api_active;
	$settings = get_option('worona_settings');

	if (isset($settings["worona_siteid_created"])) {
		$worona_siteid_created = $settings["worona_siteid_created"];
	} else {
		$worona_siteid_created = false;
	}

	$support = $settings["worona_support"];
	$support_email = $settings["worona_support_email"];

	//Progress
	if ($rest_api_installed) {
		$progress = 33;
	}
	if ($rest_api_active) {
		$progress = 66;
	}
	if ($worona_siteid_created) {
		$progress = 100;
	}

	//step
	if (!$rest_api_installed ) {
		$step = 1;
	} else if ($rest_api_installed && !$rest_api_active) {
		$step = 2;
	} else if ( $rest_api_installed && $rest_api_active && !$worona_siteid_created) {
		$step = 3;
	} else if ( $rest_api_installed && $rest_api_active && $worona_siteid_created) {
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
						<?php echo ( $rest_api_installed ? '<span class="tag is-success">Installed&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-check-circle" aria-hidden="true"></i></span></span>':'');?>
					</div>
				</nav>
				<? if ($step==1): ?>
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
				<?endif;?>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">2. Activate WP-API</p>
					</div>
					<div class="level-right">
						<?php echo ( $rest_api_active ? '<span class="tag is-success">Active&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-check-circle" aria-hidden="true"></i></span></span>':'');?>
					</div>
				</nav>
				<? if ($step<=2): ?>
				<div class="content">
					<p>
						This is why we installed the WP-API plugin.
					</p>
					<p>
						<?php
							if($rest_api_active ) {
									$activate_api_href ="#";
									$activate_api_target="";
							} else {
								$activate_api_href = $worona->get_activate_wp_rest_api_plugin_url();
								$activate_api_target="_blank";
							}
						?>
						<a href="<?php echo $activate_api_href; ?>" target="<?php echo $activate_api_target;?>" class="button button-lg">Activate WP-API Plugin</a>
					</p>
				</div>
				<? endif;?>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">3. Sync with Worona Dashboard</p>
					</div>
					<div id='label-created' class="level-right" <?php echo ( $worona_siteid_created ? '':'style="display:none;"');?>>
						<span class="tag is-success">Synced&nbsp;&nbsp;<span class="icon is-small"><i class="fa fa-check-circle" aria-hidden="true"></i></span></span>
					</div>
				</nav>
				<? if ($step<=3): ?>
				<div class="content">
					<p>
						This will generate a Worona Site ID, it will link your WordPress with the Worona Dashboard.
					</p>
					<p id="label-create-buttons">
						<a href="#" id="sync-with-worona" class="button button-lg">Sync site</a>
						or <a href="#" class="open-change-siteid">insert an existing Site ID</a>
					</p>
				</div>
				<?endif;?>
			</div>

			<div class="box">
				<nav class="level">
					<div class="level-left">
						<p class="title is-5">4. Go to Worona Dashboard</p>
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
							if ($worona_siteid_created) {
								$worona_dashboard_url = "https://dashboard.worona.org";
								$worona_dashboard_target = "_blank";
								$button_disabled = false;
							} else {
								$worona_dashboard_url = "#";
								$worona_dashboard_target = "";
								$button_disabled = true;
							}
						?>
						<a id="dashboard-button" href="<?php echo $worona_dashboard_url; ?>" target="<?php echo $worona_dashboard_target;?>" style="color:white" class="button button-lg <?php echo ($button_disabled ? 'disabled' : 'button-primary button-large'); ?>">Dashboard</a>
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
					You are on <strong>step <?php echo $step;?>/4.</strong>
				</p>
				<? if ($rest_api_active):?>
				<hr>
				<p>
					<h2>WP-API URL:</h2>
					<?php print(rest_url()); ?>
				</p>
			  <? endif;?>
				<div id="worona-siteid-lateral" <?php echo ($worona_siteid_created?'':'style="display:none;"');?>>
				<p>
					<hr>
					<h2>Worona Site Id:</h2>
					<span id="worona-siteid-span"><?php echo $settings['worona_siteid'];?></span> <a class="open-change-siteid" href="#">(change)</a>
				</p>
				</div>
			</div>
		 </article>
		 <article id="lateral-change-siteid" class="message is-warning" style="display:none;">
			 <div class="message-header">
					<nav class="level">
						<div class="level-left">
							<strong> Change Worona Site Id</strong>
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
													<input type="text" id="worona-siteid" value="<?php echo (isset($settings['worona_siteid'])) ? $settings['worona_siteid'] : ''; ?>"/>
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
		 <div class="box">
			 <nav class="level">
				 <div class="level-left">
					 <p class="title is-5">Support emails</p>
				 </div>
				 <div class="level-right">
					 <span id="support-saving" style="display:none" class="tag is-danger">saving</span>
				 </div>
			 </nav>
			 <div class="content">
				 <p>
					 We will send support emails to the following address:
				 </p>

						 <p class="control is-grouped">
							 <input class="input" type="text" id="support-email" value="<?php echo $support_email; ?>" <?php echo ($support)?'':'disabled'; ?>/>
							  <a id="change-support-email" class="button disabled">Change</a>
						 </p>
						 <input type="hidden" id="current-support-email" value="<?php echo $support_email; ?>" />




				 <p class="control">
  			 	<label class="checkbox">
    				<input id="receive-support-emails" type="checkbox" <?php echo ($support)?'checked':'';?>>
						<input type="hidden" id="current-toggle-support" value="<?php echo ($support)?'true':'false'; ?>" >
    					Receive support emails
  				</label>
				</p>
			 </div>
		 </div>
	 </div><!-- column one-third-->
	</div><!-- columns -->
</div><!-- wrap -->

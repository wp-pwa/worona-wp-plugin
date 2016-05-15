<?php
	global $worona;

	$progress = 0;
	$step = 0;

	$rest_api_installed = $worona->rest_api_installed;
	$rest_api_active = $worona->rest_api_active;
	$settings = get_option('worona_settings');
	$worona_app_created = $settings["worona_app_created"];

	if ($rest_api_installed) {
		$progress = 33;
	}
	if ($rest_api_active) {
		$progress = 66;
	}
	if ($worona_app_created) {
		$progress = 100;
	}
?>

<div class="wrap">

	<h1>SETUP PAGE</h1>

	<progress class="progress is-info is-medium" value="<?php echo $progress;?>" max="100"></progress>
	<?php
		if (!$rest_api_installed ) {
			$step = 1;
		}
	?>
	<div class="box">
		<?php echo ( $rest_api_installed ? '<span class="tag is-success">Installed</span>':'');?>
		<h1 class="<?php echo ($step==1 ? '' : 'subtitle'); ?>">1. Install WP-API</h1>
		<div class="content">
			<p>
				<br>
				<?php
					if($rest_api_installed ) {
							$install_api_href ="#";
							$install_api_target="";
					} else {
						$install_api_href = get_site_url() . '/wp-admin/plugin-install.php?tab=plugin-information&plugin=rest-api';
						$install_api_target="_blank";
					}
				?>
				<a href="<?php echo $install_api_href; ?>" class="button button-lg <?php echo ($step==1 ? '' : 'disabled'); ?>" target="<?php echo $install_api_target;?>">Download Plugin</a>
			</p>
		</div>
	</div>

	<?php
		if ($rest_api_installed && !$rest_api_active) {
			$step = 2;
		}
	?>
	<div class="box">
		<?php echo ( $rest_api_installed ? '<span class="tag is-success">Active</span>':'');?>
		<h1 class="<?php echo ($step==2 ? '' : 'subtitle'); ?>">2. Activate WP-API</h1>
		<div class="content">
			<p>
				<br>
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

	<?php
		if ( $rest_api_installed && $rest_api_active && !$worona_app_created) {
			$step = 3;
		}
	?>
	<div class="box">
		<?php echo ( $worona_app_created ? '<span class="tag is-success">created</span>':'');?>
		<h1 class="<?php echo ($step==3 ? '' : 'subtitle'); ?>">3. Create App</h1>
		<div class="content">
			<p>
				<br>
				<a href="#" id="create-worona-app" class="button button-lg <?php echo ($step==3 ? '' : 'disabled'); ?>">Create App</a>
				or <a href="#" id="insert-app-id">insert an existing App ID</a>
			</p>
			<div id="app-id-form" style="display: none;">
				<form action="options.php" method="post">
					<?php
						settings_fields( 'worona_settings' );
				    do_settings_sections( __FILE__ );

				    //get the older values, wont work the first time
				    $options = get_option( 'worona_settings' );
					?>
					<table class="form-table">
            <tr>
            	<th scope="row">App ID</th>
              <td>
                  <fieldset>
                      <label>
                          <input name="worona_settings[worona_appId]" type="text" id="worona_appId" value="<?php echo (isset($options['worona_appId']) && $options['worona_appId'] != '') ? $options['worona_appId'] : ''; ?>"/>
                          <br />
                          <span class="description">Please enter a valid App ID.</span>
                      </label>
                  </fieldset>
              </td>
			      </tr>
			    </table>
					<input style="display: none;" name="worona_settings[worona_app_created]" type="checkbox" id="worona_app_created" value="true" checked />
			  	<input type="submit" value="Change" />
		    </form>
			</div>
		</div>
	</div>

	<?php
		if ( $rest_api_installed && $rest_api_active && $worona_app_created) {
			$step = 4;
		}
	?>
	<div class="box">
		<h1 class="<?php echo ($step==4 ? '' : 'subtitle'); ?>">4. Go to the Dashboard</h1>
		<div class="content">
			<p>
				<br>
				<?php
					if ($worona_app_created) {
						$worona_dashboard_url = "";
					} else {
						$worona_dashboard_url = "#";
					}
				?>
				<a href="<?php echo $worona_dashboard_url; ?>" target="_blank" class="button button-lg <?php echo ($step==4 ? '' : 'disabled'); ?>">Dashboard</a>
			</p>
		</div>
	</div>

<br>
Your App id is: <?php $settings = get_option('worona_settings'); echo $settings["worona_appId"];  ?>
<br>Worona App Created: <?php echo var_dump($worona_app_created);  ?>

<br><br>
<br><h2> WP API URL:</h2>
 <?php print(rest_url()); ?>
</div>

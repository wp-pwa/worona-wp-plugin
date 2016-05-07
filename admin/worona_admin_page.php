<?php global $worona;//include_once("worona_check_plugin_functions.php");?>

<div class="wrap">
<h1>SETUP PAGE</h1>

<h2>WP-API</h2>

<?php
	if(! $worona->rest_api_installed) {
?>
		<a href="<?php echo get_site_url() . '/wp-admin/plugin-install.php?tab=plugin-information&plugin=rest-api'; ?>" class="button green button-lg" target="_blank" role="button">Download Plugin</a>
<?php
	} else if (! $worona->rest_api_active){
?>
		<a href="<?php echo $worona->get_activate_wp_rest_api_plugin_url() ?>" target="_blank" class="button green button-lg" id="activate-rest-api-button">Activate WP-API Plugin</a>
<?php
	} else {
?>
		<p>The <a href="https://github.com/WP-API/WP-API" target="_blank">WP REST API</a> is working correctly. Worona uses it to read your site's content.</p>
<?php
	}
?>
<br>
<br><h2>Create APP</h2>
Your App id is: <?php $settings = get_option('worona_settings'); echo $settings["worona_appId"];  ?>
<br><br>
<br><h2> Change App ID</h2>
<form action="options.php" method="post"><?php
        settings_fields( 'worona_settings' );
        do_settings_sections( __FILE__ );

        //get the older values, wont work the first time
        $options = get_option( 'worona_settings' );?>
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
        <input type="submit" value="Change" />
    </form>
<?php //delete_option("worona_settings");?>
<br><br>
<br><h2> WP API URL:</h2>
 <?php print(rest_url()); ?>
</div>

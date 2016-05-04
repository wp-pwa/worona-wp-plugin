<?php global $worona;//include_once("worona_check_plugin_functions.php");?>

<div class="wrap">
<h1>SETUP PAGE</h1>

<h2>WP-API</h2>
<p>Worona will use the WP REST API plugin to take the content from your site.</p>

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
		print("WORKING");
<?php		
	}
?>
<br><br>
3) Generate an SiteID<br>
<br>
4) Change siteID<br>
<br>
5) WP API URL: <?php print(rest_url()); ?>
</div>

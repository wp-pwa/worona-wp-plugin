<?php

define('HOST_DEV', 'localhost:3000');
define('HOST_PROD', 'prepwa.worona.org');

if (is_home()) {
  $wpType = 'latest';
  $wpId = 0;
} elseif (is_single()) {
  $wpType = 'p';
  $wpId = get_queried_object()->ID;
} elseif (is_page()) {
  $wpType = 'page_id';
  $wpId = get_queried_object()->ID;
} elseif (is_category()) {
  $wpType = 'cat';
  $wpId = get_queried_object()->term_id;
} elseif (is_tag()) {
  $wpType = 'tag';
  $wpId = get_queried_object()->term_id;
} elseif (is_author()) {
  $wpType = 'author';
  $wpId = get_queried_object()->ID;
} elseif (is_search()) {
  $wpType = 's';
  $wpId = get_query_var('s');
} elseif (is_attachment()) {
  $wpType = 'attachment_id';
  $wpId = get_queried_object()->ID;
} elseif (is_date()) {
  $wpType = 'date';
  $wpId = get_query_var('m');
  if ($wpId === '') {
    $year = get_query_var('year');
    $monthnum = str_pad(get_query_var('monthnum'), 2, '0', STR_PAD_LEFT);
    $wpId = $year . $monthnum;
  }
} else {
  $wpType = 'none';
}

if (is_paged()) {
  $wpPage = get_query_var('paged');
} elseif (is_home() || is_category() || is_tag() || is_author() || is_search() || is_date()) {
  $wpPage = 1;
}

$settings = get_option('worona_settings');
if (isset($settings['worona_siteid'])) {
  $siteId = $settings["worona_siteid"];
} else {
  $siteId = 'none';
}

?>

<script type='text/javascript'>
var siteId = '<?php echo $siteId; ?>', wpType = '<?php echo $wpType; ?>', wpId = '<?php echo $wpId; ?>', wpPage = '<?php echo $wpPage; ?>', <?php if (defined('HOST_DEV')) { echo "hostDev = '" . HOST_DEV . "', "; } ?>hostProd = '<?php echo HOST_PROD; ?>';
<?php require(WP_PLUGIN_DIR . '/worona/injector/injector.min.js'); ?>
</script>

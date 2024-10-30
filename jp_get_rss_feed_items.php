<?php
/*
Plugin Name: JP's Get RSS Feed
Plugin URI: http://www.jimmyscode.com/wordpress/jps-get-rss-feeds/
Description: Get last X number of posts from selected RSS feed and display in an unordered list. Default is 5 items.
Version: 1.6.3
Author: Jimmy Pe&ntilde;a
Author URI: http://www.jimmyscode.com/
License: GPLv2 or later
*/
if (!defined('JPGRF_PLUGIN_NAME')) {
	// plugin constants
	define('JPGRF_PLUGIN_NAME', 'JP\'s Get RSS Feed');
	define('JPGRF_VERSION', '1.6.3');
	define('JPGRF_SLUG', 'jps-get-rss-feed');
	define('JPGRF_LOCAL', 'jp_get_rss_feed');
	define('JPGRF_OPTION', 'jp_get_rss_feed');
	define('JPGRF_OPTIONS_NAME', 'jp_get_rss_feed_options');
	define('JPGRF_PERMISSIONS_LEVEL', 'manage_options');
	define('JPGRF_PATH', plugin_basename(dirname(__FILE__)));
	/* default values */
	define('JPGRF_DEFAULT_ENABLED', true);
	define('JPGRF_DEFAULT_URL', '');
	define('JPGRF_DEFAULT_NUM', 5);
	define('JPGRF_DEFAULT_CHARS', 0);
	define('JPGRF_DEFAULT_NOFOLLOW', false);
	define('JPGRF_DEFAULT_CSSCLASS', 'jpgetrssfeed');
	define('JPGRF_DEFAULT_DESC', false);
	define('JPGRF_DEFAULT_SHOW', false);
	define('JPGRF_DEFAULT_NEWWINDOW', false);
	define('JPGRF_DEFAULT_POSTLINK', false);
	define('JPGRF_DEFAULT_SORTORDER', 'newestfirst');
	define('JPGRF_DEFAULT_POSTTHUMBNAIL', false);
	define('JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE', 'thumbnail');
	define('JPGRF_DEFAULT_CACHETIME', 43200);
	define('JPGRF_DEFAULT_DATE_POSITION', 'before');
	define('JPGRF_DEFAULT_DATE_SHOW', false);
	define('JPGRF_DEFAULT_DATE_FORMAT', DATE_RSS);
	/* values for dropdowns */
	define('JPGRF_AVAILABLE_SORTS', 'newestfirst,oldestfirst');
	define('JPGRF_AVAILABLE_SIZES', 'thumbnail,medium,large,full');
	define('JPGRF_AVAILABLE_DATE_OPTIONS', 'before,after');
	/* option array member names */
	define('JPGRF_DEFAULT_ENABLED_NAME', 'enabled');
	define('JPGRF_DEFAULT_URL_NAME', 'url');
	define('JPGRF_DEFAULT_NUM_NAME', 'numitems');
	define('JPGRF_DEFAULT_CHARS_NAME', 'numchars');
	define('JPGRF_DEFAULT_NOFOLLOW_NAME', 'nofollow');
	define('JPGRF_DEFAULT_CSSCLASS_NAME', 'cssclass');
	define('JPGRF_DEFAULT_DESC_NAME', 'getdesc');
	define('JPGRF_DEFAULT_SHOW_NAME', 'show');
	define('JPGRF_DEFAULT_NEWWINDOW_NAME', 'opennewwindow');
	define('JPGRF_DEFAULT_POSTLINK_NAME', 'postlinkoptional');
	define('JPGRF_DEFAULT_SORTORDER_NAME', 'sortorder');
	define('JPGRF_DEFAULT_POSTTHUMBNAIL_NAME', 'postthumbnail');
	define('JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME', 'thumbnailsize');
	define('JPGRF_DEFAULT_CACHETIME_NAME', 'cachetime');
	define('JPGRF_DEFAULT_DATE_POSITION_NAME', 'dateposition');
	define('JPGRF_DEFAULT_DATE_SHOW_NAME', 'dateshow');
	define('JPGRF_DEFAULT_DATE_FORMAT_NAME', 'dateformat');
}
	// oh no you don't
	if (!defined('ABSPATH')) {
		wp_die(__('Do not access this file directly.', jpgrf_get_local()));
	}

	// localization to allow for translations
	add_action('init', 'jp_get_rss_feed_translation_file');
	function jp_get_rss_feed_translation_file() {
		$plugin_path = jpgrf_get_path() . '/translations';
		load_plugin_textdomain(jpgrf_get_local(), '', $plugin_path);
	}
	// tell WP that we are going to use new options
	// also, register the admin CSS file and javascript for later inclusion
	add_action('admin_init', 'jp_get_rss_feed_options_init');
	function jp_get_rss_feed_options_init() {
		register_setting(JPGRF_OPTIONS_NAME, jpgrf_get_option(), 'jpgrf_validation');
		register_jpgrf_admin_style();
		register_jpgrf_admin_script();
	}
	// validation function
	function jpgrf_validation($input) {
		if (!empty($input)) {
			// validate all form fields
			$input[JPGRF_DEFAULT_ENABLED_NAME] = (bool)$input[JPGRF_DEFAULT_ENABLED_NAME];
			$input[JPGRF_DEFAULT_NOFOLLOW_NAME] = (bool)$input[JPGRF_DEFAULT_NOFOLLOW_NAME];
			$input[JPGRF_DEFAULT_DESC_NAME] = (bool)$input[JPGRF_DEFAULT_DESC_NAME];
			$input[JPGRF_DEFAULT_NEWWINDOW_NAME] = (bool)$input[JPGRF_DEFAULT_NEWWINDOW_NAME];
			$input[JPGRF_DEFAULT_POSTLINK_NAME] = (bool)$input[JPGRF_DEFAULT_POSTLINK_NAME];
			$input[JPGRF_DEFAULT_POSTTHUMBNAIL_NAME] = (bool)$input[JPGRF_DEFAULT_POSTTHUMBNAIL_NAME];
			$input[JPGRF_DEFAULT_URL_NAME] = filter_var($input[JPGRF_DEFAULT_URL_NAME], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
			$input[JPGRF_DEFAULT_NUM_NAME] = intval($input[JPGRF_DEFAULT_NUM_NAME]);
			$input[JPGRF_DEFAULT_CHARS_NAME] = intval($input[JPGRF_DEFAULT_CHARS_NAME]);
			$input[JPGRF_DEFAULT_CACHETIME_NAME] = intval($input[JPGRF_DEFAULT_CACHETIME_NAME]);
			$input[JPGRF_DEFAULT_CSSCLASS_NAME] = sanitize_html_class($input[JPGRF_DEFAULT_CSSCLASS_NAME]);
			$input[JPGRF_DEFAULT_SORTORDER_NAME] = sanitize_text_field($input[JPGRF_DEFAULT_SORTORDER_NAME]);
			$input[JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME] = sanitize_text_field($input[JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME]);
			$input[JPGRF_DEFAULT_DATE_POSITION_NAME] = sanitize_text_field($input[JPGRF_DEFAULT_DATE_POSITION_NAME]);
			$input[JPGRF_DEFAULT_DATE_SHOW_NAME] = (bool)$input[JPGRF_DEFAULT_DATE_SHOW_NAME];
			$input[JPGRF_DEFAULT_DATE_FORMAT_NAME] = sanitize_text_field($input[JPGRF_DEFAULT_DATE_FORMAT_NAME]);
		}
		return $input;
	}
	// add Settings sub-menu
	add_action('admin_menu', 'jpgrf_plugin_menu');
	function jpgrf_plugin_menu() {
		add_options_page(JPGRF_PLUGIN_NAME, JPGRF_PLUGIN_NAME, JPGRF_PERMISSIONS_LEVEL, jpgrf_get_slug(), 'jp_get_rss_feed_page');
	}
	// plugin settings page
	// http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
	function jp_get_rss_feed_page() {
		// check perms
		if (!current_user_can(JPGRF_PERMISSIONS_LEVEL)) {
			wp_die(__('You do not have sufficient permission to access this page', jpgrf_get_local()));
		}
		?>
		<div class="wrap">
			<h2 id="plugintitle"><img src="<?php echo jpgrf_getimagefilename('rss.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php echo JPGRF_PLUGIN_NAME; ?> by <a href="http://www.jimmyscode.com/">Jimmy Pe&ntilde;a</a></h2>
			<div>You are running plugin version <strong><?php echo JPGRF_VERSION; ?></strong>.</div>
	
			<?php /* http://code.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-5-tabbed-navigation-for-your-settings-page--wp-24971 */ ?>
			<?php $active_tab = (isset($_GET['tab']) ? $_GET['tab'] : 'settings'); ?>
			<h2 class="nav-tab-wrapper">
			  <a href="?page=<?php echo jpgrf_get_slug(); ?>&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', jpgrf_get_local()); ?></a>
				<a href="?page=<?php echo jpgrf_get_slug(); ?>&tab=parameters" class="nav-tab <?php echo $active_tab == 'parameters' ? 'nav-tab-active' : ''; ?>"><?php _e('Parameters', jpgrf_get_local()); ?></a>
				<a href="?page=<?php echo jpgrf_get_slug(); ?>&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>"><?php _e('Support', jpgrf_get_local()); ?></a>
			</h2>
			
			<form method="post" action="options.php">
				<?php settings_fields(JPGRF_OPTIONS_NAME); ?>
				<?php $options = jpgrf_getpluginoptions(); ?>
				<?php update_option(jpgrf_get_option(), $options); ?>
				<?php if ($active_tab == 'settings') { ?>
					<h3 id="settings"><img src="<?php echo jpgrf_getimagefilename('settings.png'); ?>" title="" alt="" height="61" width="64" align="absmiddle" />Plugin Settings</h3>
					<table class="form-table" id="theme-options-wrap">
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Is plugin enabled? Uncheck this to turn it off temporarily.', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_ENABLED_NAME; ?>]"><?php _e('Plugin enabled?', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="checkbox" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_ENABLED_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_ENABLED_NAME; ?>]" value="1" <?php checked('1', jpgrf_checkifset(JPGRF_DEFAULT_ENABLED_NAME, JPGRF_DEFAULT_ENABLED, $options)); ?> /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Is plugin enabled? Uncheck this to turn it off temporarily.', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter the default URL here. This will be used wherever you use the shortcode, if you do not specify the URL.', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo esc_url(JPGRF_DEFAULT_URL_NAME); ?>]"><?php _e('Default URL for feeds', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="url" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_URL_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_URL_NAME; ?>]" value="<?php echo jpgrf_checkifset(JPGRF_DEFAULT_URL_NAME, JPGRF_DEFAULT_URL, $options); ?>" /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Enter the default URL here. This will be used wherever you use the shortcode, if you do not specify the URL in the shortcode.', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter the default number of RSS feed items to display, if you do not pass a value to the plugin.', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_NUM_NAME; ?>]"><?php _e('Default number of items', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="number" min="1" max="9999" step="1" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_NUM_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_NUM_NAME; ?>]" value="<?php echo jpgrf_checkifset(JPGRF_DEFAULT_NUM_NAME, JPGRF_DEFAULT_NUM, $options); ?>" /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Enter the default number of RSS feed items to display, if you do not pass a value to the plugin.', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter cache time in seconds. Feed will be cached for a minimum of X seconds', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_CACHETIME_NAME; ?>]"><?php _e('Cache time (in seconds)', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="number" min="1" max="<?php echo JPGRF_DEFAULT_CACHETIME; ?>" step="1" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_CACHETIME_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_CACHETIME_NAME; ?>]" value="<?php echo jpgrf_checkifset(JPGRF_DEFAULT_CACHETIME_NAME, JPGRF_DEFAULT_CACHETIME, $options); ?>" /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Enter the default cache time for feeds, in seconds. Default is 43200 seconds (12 hours).', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Do you want to add rel=nofollow to all links?', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_NOFOLLOW_NAME; ?>]"><?php _e('Nofollow links?', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="checkbox" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_NOFOLLOW_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_NOFOLLOW_NAME; ?>]" value="1" <?php checked('1', jpgrf_checkifset(JPGRF_DEFAULT_NOFOLLOW_NAME, JPGRF_DEFAULT_NOFOLLOW, $options)); ?> /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Do you want to add rel="nofollow" to all links?', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter the default CSS class name, if you do not pass a value to the plugin.', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_CSSCLASS_NAME; ?>]"><?php _e('Default CSS class', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="text" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_CSSCLASS_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_CSSCLASS_NAME; ?>]" value="<?php echo jpgrf_checkifset(JPGRF_DEFAULT_CSSCLASS_NAME, JPGRF_DEFAULT_CSSCLASS, $options); ?>" /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Enter the default CSS class name for the div tag that wraps the output, if you do not pass a value to the plugin.', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Check this box to show the feed item description after the link.', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DESC_NAME; ?>]"><?php _e('Include feed item description?', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="checkbox" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DESC_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DESC_NAME; ?>]" value="1" <?php checked('1', jpgrf_checkifset(JPGRF_DEFAULT_DESC_NAME, JPGRF_DEFAULT_DESC, $options)); ?> /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Check this box to show the feed item description after the link.', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Show this many characters of item description.', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_CHARS_NAME; ?>]"><?php _e('Show this many characters of item description.', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="number" min="0" max="9999" step="1" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_CHARS_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_CHARS_NAME; ?>]" value="<?php echo jpgrf_checkifset(JPGRF_DEFAULT_CHARS_NAME, JPGRF_DEFAULT_CHARS, $options); ?>" /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Enter the number of characters from the feed item description you want to display. Put \'0\' for full description. <strong>Does not apply if the above checkbox is not checked.</strong>', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Check this box to force links to open in a new window (uses JavaScript).', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_NEWWINDOW_NAME; ?>]"><?php _e('Open links in new window?', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="checkbox" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_NEWWINDOW_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_NEWWINDOW_NAME; ?>]" value="1" <?php checked('1', jpgrf_checkifset(JPGRF_DEFAULT_NEWWINDOW_NAME, JPGRF_DEFAULT_NEWWINDOW, $options)); ?> /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Check this box to force links to open in a new window (uses JavaScript).', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Default sort order', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_SORTORDER_NAME; ?>]"><?php _e('Default sort order', jpgrf_get_local()); ?></label></strong></th>
							<td><select id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_SORTORDER_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_SORTORDER_NAME; ?>]">
							<?php $sortorders = explode(",", JPGRF_AVAILABLE_SORTS);
								sort($sortorders);
								foreach($sortorders as $sorder) {
									echo '<option value="' . $sorder . '"' . selected($sorder, jpgrf_checkifset(JPGRF_DEFAULT_SORTORDER_NAME, JPGRF_DEFAULT_SORTORDER, $options), false) . '>' . $sorder . '</option>';
								} ?>
							</select></td>
						</tr>
						<?php jpgrf_explanationrow(__('Select the sort order you would like to use as the default.', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Is post title link optional?', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_POSTLINK_NAME; ?>]"><?php _e('Post link optional?', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="checkbox" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_POSTLINK_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_POSTLINK_NAME; ?>]" value="1" <?php checked('1', jpgrf_checkifset(JPGRF_DEFAULT_POSTLINK_NAME, JPGRF_DEFAULT_POSTLINK, $options)); ?> /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Check this box if you do not want the post titles to be hyperlinked. <strong>They will be plain text only if you check this box.</strong>', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Include Featured Image (post thumbnail)?', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_POSTTHUMBNAIL_NAME; ?>]"><?php _e('Include Featured Image (post thumbnail)?', jpgrf_get_local()); ?></label></strong></th>
							<td><input type="checkbox" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_POSTTHUMBNAIL_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_POSTTHUMBNAIL_NAME; ?>]" value="1" <?php checked('1', jpgrf_checkifset(JPGRF_DEFAULT_POSTTHUMBNAIL_NAME, JPGRF_DEFAULT_POSTTHUMBNAIL, $options)); ?> /></td>
						</tr>
						<?php jpgrf_explanationrow(__('Check this box if you want to display the "Featured Image" from each post in a local RSS feed. <strong>NOTE:</strong> Remote feeds will show the feed logo only.', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Default post thumbnail size (local RSS feeds only)', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME; ?>]"><?php _e('Default post thumbnail size (local RSS feeds only)', jpgrf_get_local()); ?></label></strong></th>
							<td><select id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME; ?>]">
							<?php $sizes = explode(",", JPGRF_AVAILABLE_SIZES);
								// sort($sizes);
								foreach($sizes as $size) {
									echo '<option value="' . $size . '"' . selected(jpgrf_checkifset(JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME, JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE, $options), $size) . '>' . $size . '</option>';
								} ?>
							</select></td>
						</tr>
						<?php jpgrf_explanationrow(__('If you choose to display Featured Images, select the size you would like to use as the default. Works for local RSS feeds only.', jpgrf_get_local())); ?>
						<?php jpgrf_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Show post date before or after post title', jpgrf_get_local()); ?>" for="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DATE_POSITION_NAME; ?>]"><?php _e('Show post date before or after post title', jpgrf_get_local()); ?></label></strong></th>
							<td>
							<input type="checkbox" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DATE_SHOW_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DATE_SHOW_NAME; ?>]" value="1" <?php checked('1', jpgrf_checkifset(JPGRF_DEFAULT_DATE_SHOW_NAME, JPGRF_DEFAULT_DATE_SHOW, $options)); ?> />
							Yes, show the post date 
							<select style="width:120px !important" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DATE_POSITION_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DATE_POSITION_NAME; ?>]">
							<?php $positions = explode(",", JPGRF_AVAILABLE_DATE_OPTIONS);
								foreach($positions as $position) {
									echo '<option value="' . $position . '"' . selected(jpgrf_checkifset(JPGRF_DEFAULT_DATE_POSITION_NAME, JPGRF_DEFAULT_DATE_POSITION, $options), $position) . '>' . $position . '</option>';
								} ?>
							</select> the post title<br /><br />
							Date format: <input type="text" id="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DATE_FORMAT_NAME; ?>]" name="<?php echo jpgrf_get_option(); ?>[<?php echo JPGRF_DEFAULT_DATE_FORMAT_NAME; ?>]" value="<?php echo jpgrf_checkifset(JPGRF_DEFAULT_DATE_FORMAT_NAME, JPGRF_DEFAULT_DATE_FORMAT, $options); ?>" />
							<?php _e('<em>Input date format details here</em>', jpgrf_get_local()); ?></td>
						</tr>
						</table>
						<?php submit_button(); ?>
					<?php } elseif ($active_tab == 'parameters') { ?>
						<h3 id="parameters"><img src="<?php echo jpgrf_getimagefilename('parameters.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php _e('Plugin Parameters and Default Values', jpgrf_get_local()); ?></h3>
						These are the parameters for using the shortcode, or calling the plugin from your PHP code.

						<?php echo jpgrf_parameters_table(jpgrf_get_local(), jpgrf_shortcode_defaults(), jpgrf_required_parameters()); ?>			

						<h3 id="examples"><img src="<?php echo jpgrf_getimagefilename('examples.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php _e('Shortcode and PHP Examples', jpgrf_get_local()); ?></h3>
						<h4><?php _e('Shortcode Format:', jpgrf_get_local()); ?></h4>
							<?php echo jpgrf_get_example_shortcode('jp-rss-feed', jpgrf_shortcode_defaults(), jpgrf_get_local()); ?>

						<h4><?php _e('PHP Format:', jpgrf_get_local()); ?></h4>
							<?php echo jpgrf_get_example_php_code('jp-rss-feed', 'jp_get_rss_feed_items', jpgrf_shortcode_defaults()); ?>
							<?php _e('<small>Note: \'show\' is false by default; set it to <strong>true</strong> echo the output, or <strong>false</strong> to return the output to your PHP code.</small>', jpgrf_get_local()); ?>
					<?php } else { ?>
						<h3 id="support"><img src="<?php echo jpgrf_getimagefilename('support.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php _e('Support', jpgrf_get_local()); ?></h3>
						<div class="support">
							<?php echo jpgrf_getsupportinfo(jpgrf_get_slug(), jpgrf_get_local()); ?>
						</div>
					<?php } ?>
			</form>
		</div>
		<?php }
	// shortcode for posts and pages
	add_shortcode('jp-rss-feed', 'jp_get_rss_feed_items');
	add_shortcode('jp-get-rss-feed', 'jp_get_rss_feed_items');
	add_shortcode('jps-get-rss-feed', 'jp_get_rss_feed_items');
	// one function for shortcode and PHP
	function jp_get_rss_feed_items($atts) {
		// get parameters
		extract(shortcode_atts(jpgrf_shortcode_defaults(), $atts));
		// plugin is enabled/disabled from settings page only
		$options = jpgrf_getpluginoptions();
		if (!empty($options)) {
			$enabled = (bool)$options[JPGRF_DEFAULT_ENABLED_NAME];
		} else {
			$enabled = JPGRF_DEFAULT_ENABLED;
		}
		
		$output = '';
		
		// ******************************
		// derive shortcode values from constants
		// ******************************
		if ($enabled) {
			$temp_fu = constant('JPGRF_DEFAULT_URL_NAME');
			$feed_url = $$temp_fu;
			$temp_noi = constant('JPGRF_DEFAULT_NUM_NAME');
			$number_of_items = $$temp_noi;
			$temp_nc = constant('JPGRF_DEFAULT_CHARS_NAME');
			$numchars = $$temp_nc;
			$temp_nf = constant('JPGRF_DEFAULT_NOFOLLOW_NAME');
			$nofollow = $$temp_nf;
			$temp_cssc = constant('JPGRF_DEFAULT_CSSCLASS_NAME');
			$cssclass = $$temp_cssc;
			$temp_desc = constant('JPGRF_DEFAULT_DESC_NAME');
			$desc = $$temp_desc;
			$temp_onn = constant('JPGRF_DEFAULT_NEWWINDOW_NAME');
			$opennewwindow = $temp_onn;
			$temp_show = constant('JPGRF_DEFAULT_SHOW_NAME');
			$show = $$temp_show;
			$temp_sortorder = constant('JPGRF_DEFAULT_SORTORDER_NAME');
			$sortorder = $$temp_sortorder;
			$temp_plo = constant('JPGRF_DEFAULT_POSTLINK_NAME');
			$postlinkoptional = $$temp_plo;
			$temp_pt = constant('JPGRF_DEFAULT_POSTTHUMBNAIL_NAME');
			$postthumbnail = $$temp_pt;
			$temp_ptsize = constant('JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME');
			$ptsize = $$temp_ptsize;
			$temp_cache = constant('JPGRF_DEFAULT_CACHETIME_NAME');
			$cachetime = $$temp_cache;
			$temp_date_show = constant('JPGRF_DEFAULT_DATE_SHOW_NAME');
			$dateshow = $$temp_date_show;
			$temp_date_position = constant('JPGRF_DEFAULT_DATE_POSITION_NAME');
			$dateposition = $$temp_date_position;
			$temp_date_format = constant('JPGRF_DEFAULT_DATE_FORMAT_NAME');
			$dateformat = $$temp_date_format;
		}
		// ****************************************************************
		// sanitize user input
		// ****************************************************************
		if ($enabled) {
			$number_of_items = intval($number_of_items);
			if (!$number_of_items) {
				$number_of_items = JPGRF_DEFAULT_NUM;
			}
			$numchars = intval($numchars);
			if (!$numchars) {
				$numchars = JPGRF_DEFAULT_CHARS;
			}
			$cachetime = intval($cachetime);
			if (!$cachetime) {
				$cachetime = JPGRF_DEFAULT_CACHETIME;
			}	
			$cssclass = sanitize_html_class($cssclass);
			if (!$cssclass) {
				$cssclass = JPGRF_DEFAULT_CSSCLASS;
			}
			$dateformat = sanitize_text_field($dateformat);
			if (!$dateformat) {
				$dateformat = JPGRF_DEFAULT_DATE_FORMAT;
			}			
			// clean the sortorder value, make sure it is a valid value
			$sortorder = sanitize_text_field($sortorder);
			if (!$sortorder) {
				$sortorder = JPGRF_DEFAULT_SORTORDER;
			}
			$sortorders = explode(",", JPGRF_AVAILABLE_SORTS);
			if (!in_array($sortorder, $sortorders)) { // use default
				$sortorder = JPGRF_DEFAULT_SORTORDER;
			}
			
			// clean the post date order value, make sure it is a valid value
			$dateposition = sanitize_text_field($dateposition);
			if (!$dateposition) {
				$dateposition = JPGRF_DEFAULT_DATE_POSITION;
			}
			$positions = explode(",", JPGRF_AVAILABLE_DATE_OPTIONS);
			if (!in_array($dateposition, $positions)) { // use default
				$dateposition = JPGRF_DEFAULT_DATE_POSITION;
			}

			$desc = (bool)$desc;
			$opennewwindow = (bool)$opennewwindow;
			$show = (bool)$show;
			$postlinkoptional = (bool)$postlinkoptional;
			$nofollow = (bool)$nofollow;
			$postthumbnail = (bool)$postthumbnail;
			$dateshow = (bool)$dateshow;

			// clean the size value, make sure it is a valid value
			// HOWEVER, ignore PT size if post thumbnails are not requested, no point in checking
			if ($postthumbnail) {
				$ptsize = sanitize_text_field($ptsize);
				if (!$ptsize) {
					$ptsize = JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE;
				}
				$ptsizes = explode(",", JPGRF_AVAILABLE_SIZES);
				if (!in_array($ptsize, $ptsizes)) { // use default
					$ptsize = JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE;
				}
			}
		}
		// ******************************
		// check for parameters, then settings, then defaults
		// ******************************
		if ($enabled) {
			// allow alternate parameter names for feed URL
			if (!empty($atts['href'])) {
				$feed_url = $atts['href'];
			} elseif (!empty($atts['link'])) {
				$feed_url = $atts['link'];
			} elseif (!empty($atts['rss'])) {
				$feed_url = $atts['rss'];
			}
			// check for overridden parameters, if nonexistent then get from DB
			if ($feed_url == JPGRF_DEFAULT_URL) { // no url passed to function, try settings page
				$feed_url = $options[JPGRF_DEFAULT_URL_NAME];
				if ($feed_url == JPGRF_DEFAULT_URL) { // no url on settings page either
					$enabled = false;
					$output = '<!-- ' . JPGRF_PLUGIN_NAME . ': ' . __('plugin is disabled. Either you did not pass a necessary setting to the plugin, or did not configure a default. Check Settings page.', jpgrf_get_local()) . ' -->';
				}
			}
			// sanitize URL here (instead of above) because it might not be passed to the shortcode and would be blank until now
			$feed_url = sanitize_text_field($feed_url);
			$feed_url = filter_var($feed_url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);

			if (!$feed_url) {
				$enabled = false;
				$output = '<!-- ' . JPGRF_PLUGIN_NAME . ': ' . __('invalid URL was passed. Please check the URL.', jpgrf_get_local()) . ' -->';
			}

			if ($enabled) { // save some cycles if the above code disabled the plugin
				$number_of_items = jpgrf_setupvar($number_of_items, JPGRF_DEFAULT_NUM, JPGRF_DEFAULT_NUM_NAME, $options);
				$numchars = jpgrf_setupvar($numchars, JPGRF_DEFAULT_CHARS, JPGRF_DEFAULT_CHARS_NAME, $options);
				$cachetime = jpgrf_setupvar($cachetime, JPGRF_DEFAULT_CACHETIME, JPGRF_DEFAULT_CACHETIME_NAME, $options);
				$nofollow = jpgrf_setupvar($nofollow, JPGRF_DEFAULT_NOFOLLOW, JPGRF_DEFAULT_NOFOLLOW_NAME, $options);
				$cssclass = jpgrf_setupvar($cssclass, JPGRF_DEFAULT_CSSCLASS, JPGRF_DEFAULT_CSSCLASS_NAME, $options);
				$sortorder = jpgrf_setupvar($sortorder, JPGRF_DEFAULT_SORTORDER, JPGRF_DEFAULT_SORTORDER_NAME, $options);
				$desc = jpgrf_setupvar($desc, JPGRF_DEFAULT_DESC, JPGRF_DEFAULT_DESC_NAME, $options);
				$postlinkoptional = jpgrf_setupvar($postlinkoptional, JPGRF_DEFAULT_POSTLINK, JPGRF_DEFAULT_POSTLINK_NAME, $options);
				$postthumbnail = jpgrf_setupvar($postthumbnail, JPGRF_DEFAULT_POSTTHUMBNAIL, JPGRF_DEFAULT_POSTTHUMBNAIL_NAME, $options);
				$opennewwindow = jpgrf_setupvar($opennewwindow, JPGRF_DEFAULT_NEWWINDOW, JPGRF_DEFAULT_NEWWINDOW_NAME, $options);
				$dateshow = jpgrf_setupvar($dateshow, JPGRF_DEFAULT_DATE_SHOW, JPGRF_DEFAULT_DATE_SHOW_NAME, $options);
				$dateposition  = jpgrf_setupvar($dateposition, JPGRF_DEFAULT_DATE_POSITION, JPGRF_DEFAULT_DATE_POSITION_NAME, $options);
				$dateformat = jpgrf_setupvar($dateformat, JPGRF_DEFAULT_DATE_FORMAT, JPGRF_DEFAULT_DATE_FORMAT_NAME, $options);
				
				// ignore PT size if post thumbnails are not requested, save some cycles
				if ($postthumbnail) {
					$ptsize = jpgrf_setupvar($ptsize, JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE, JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME, $options);
				}
			}
		} // end enabled check

		// ******************************
		// do some actual work
		// ******************************
		if ($enabled) {
			include_once(ABSPATH . WPINC . '/feed.php');
			$rss = jpgrf_fetch_feed($feed_url, $cachetime); // $rss = fetch_feed($feed_url);
			if (!is_wp_error($rss)) { // no error occurred
				$maxitems = $rss->get_item_quantity($number_of_items);
				// Build an array of all the items, starting with element 0 (first element).
				$rss_items = $rss->get_items(0, $maxitems);
				// http://wordpress.org/support/topic/option-to-reverse-display-order?replies=8
				if ($sortorder == 'oldestfirst') {
					$rss_items = array_reverse($rss_items);
				}
				$output = '<div' . ((bool)$cssclass ? ' class="' . $cssclass . '"' : '') . '><ul>';
				if ($maxitems == 0) { // no items
					$output .= '<li>' . __('No feed items at this time. Please check back later.', jpgrf_get_local()) . '</li>';
				} else { // there were items
					// Loop through each feed item and display each item as a hyperlink
					foreach ($rss_items as $item) {

						if ($postthumbnail) {
							// check if the domain name in the feed URL is the same as the local domain name
							$feedurl = parse_url($feed_url);
							$localurl = parse_url(network_site_url());
							if ($feedurl["host"] == $localurl["host"]) { // local feed
								// http://codex.wordpress.org/Function_Reference/get_the_post_thumbnail
								$thispostthumbnail = get_the_post_thumbnail(url_to_postid($item->get_permalink()), $ptsize);
							} else { // for remote feeds, get feed logo
								$thispostthumbnail = '<img title="' . $rss->get_image_title() . '" src="' . $rss->get_image_url() . '" width="' . $rss->get_image_width() . '" height="' . $rss->get_image_height() . '" />';
							}
						}

						$thisitemtitle = strip_tags($item->get_title());
						$output .= '<li>';
				
						if ($postthumbnail) {
							$output .= $thispostthumbnail;
						}
						if ($dateshow) { // include date before?
							if ($dateposition == 'before') {
								$output .= date_i18n(get_option('date_format'), $item->get_date($dateformat)) . " - ";
							}
						}
						
						if (!$postlinkoptional) {
							$output .= '<a ';
							if ($opennewwindow) {
								$output .= 'onclick="window.open(this.href); return false;" onkeypress="window.open(this.href); return false;" ';
							}
							if ($nofollow) {
								$output .= 'rel="nofollow" ';
							}
							$output .= 'href="' . esc_url($item->get_permalink()) . '" ';
							$output .= 'title="' . $thisitemtitle . '">' . $thisitemtitle;
							$output .= '</a>';
							if ($dateshow) { // include date after?
								if ($dateposition == 'after') {
									$output .= ' - ' . date_i18n(get_option('date_format'), $item->get_date($dateformat));
								}
							}
						} else {
						// no post link wanted, just put item title
						$output .= $thisitemtitle;
						}
						if ($desc) { // show description, but how much?
							// $output .= ' - ';
							if ($numchars > 0) {
								// http://simplepie.org/wiki/reference/simplepie/sanitize
								$output .= substr($item->sanitize($item->get_description(), 32), 0, $numchars-1); // SIMPLEPIE_CONSTRUCT_MAYBE_HTML = 32
							} else { // # of chars is 0 or less, show full description
								$output .= $item->sanitize($item->get_description(), 32); // SIMPLEPIE_CONSTRUCT_MAYBE_HTML = 32
							}
						}
						$output .= '</li>';
					} // end foreach
				} // end items check
				$output .= '</ul></div>';
				$output .= '<!-- ' . $sortorder . ' -->';
			} // end error check
		} else { // plugin disabled
			$output = '<!-- ' . JPGRF_PLUGIN_NAME . ': ' . __('plugin is disabled. Either you did not pass a necessary setting to the plugin, or did not configure a default. Check Settings page.', jpgrf_get_local()) . ' -->';
		} // end enabled check
		if ($show) {
			echo $output;
		} else {
			return $output;
		}
	} // end shortcode function
	// show admin messages to plugin user
	add_action('admin_notices', 'jpgrf_showAdminMessages');
	function jpgrf_showAdminMessages() {
		// http://wptheming.com/2011/08/admin-notices-in-wordpress/
		global $pagenow;
		if (current_user_can(JPGRF_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') {
				if (isset($_GET['page'])) {
					if ($_GET['page'] == jpgrf_get_slug()) { // we are on this plugin's settings page
						$options = jpgrf_getpluginoptions();
						if (!empty($options)) {
							$enabled = (bool)$options[JPGRF_DEFAULT_ENABLED_NAME];
							$feed_url = $options[JPGRF_DEFAULT_URL_NAME];
							if (!$enabled) {
								echo '<div id="message" class="error">' . JPGRF_PLUGIN_NAME . ' ' . __('is currently disabled.', jpgrf_get_local()) . '</div>';
							}
							if (($feed_url === JPGRF_DEFAULT_URL) || ($feed_url === false)) {
								echo '<div id="message" class="updated">' . __('WARNING: No default URL specified. You will need to pass a URL to the plugin each time you use the shortcode or PHP function.', jpgrf_get_local()) . '</div>';
							}
						}
					}
				}
			} // end page check
		} // end privilege check
	} // end admin msgs function
	// add admin CSS if we are on the plugin options page
	add_action('admin_head', 'insert_jpgrf_admin_css');
	function insert_jpgrf_admin_css() {
		global $pagenow;
		if (current_user_can(JPGRF_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') {
				if (isset($_GET['page'])) {
					if ($_GET['page'] == jpgrf_get_slug()) { // we are on this plugin's settings page
						jpgrf_admin_styles();
					}
				}
			}
		}
	}
	// add helpful links to plugin page next to plugin name
	// http://bavotasan.com/2009/a-settings-link-for-your-wordpress-plugins/
	// http://wpengineer.com/1295/meta-links-for-wordpress-plugins/
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'jpgrf_plugin_settings_link');
	add_filter('plugin_row_meta', 'jpgrf_meta_links', 10, 2);
	
	function jpgrf_plugin_settings_link($links) {
		return jpgrf_settingslink($links, jpgrf_get_slug(), jpgrf_get_local());
	}
	function jpgrf_meta_links($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$links = array_merge($links,
			array(
				sprintf(__('<a href="http://wordpress.org/support/plugin/%s">Support</a>', jpgrf_get_local()), jpgrf_get_slug()),
				sprintf(__('<a href="http://wordpress.org/extend/plugins/%s/">Documentation</a>', jpgrf_get_local()), jpgrf_get_slug()),
				sprintf(__('<a href="http://wordpress.org/plugins/%s/faq/">FAQ</a>', jpgrf_get_local()), jpgrf_get_slug())
			));
		}
		return $links;	
	}
	// enqueue/register the admin CSS file
	function jpgrf_admin_styles() {
		wp_enqueue_style('jpgrf_admin_style');
	}
	function register_jpgrf_admin_style() {
		wp_register_style('jpgrf_admin_style', 
			plugins_url(jpgrf_get_path() . '/css/admin.css'), 
			array(), 
			JPGRF_VERSION . "_" . date('njYHis', filemtime(dirname(__FILE__) . '/css/admin.css')), 
			'all');
	}
	// enqueue/register the admin JS file
	add_action('admin_enqueue_scripts', 'jpgrf_ed_buttons');
	function jpgrf_ed_buttons($hook) {
		if (($hook == 'post-new.php') || ($hook == 'post.php')) {
			wp_enqueue_script('jpgrf_add_editor_button');
		}
	}
	function register_jpgrf_admin_script() {
		wp_register_script('jpgrf_add_editor_button',
			plugins_url(jpgrf_get_path() . '/js/editor_button.js'), 
			array('quicktags'), 
			JPGRF_VERSION . "_" . date('njYHis', filemtime(dirname(__FILE__) . '/js/editor_button.js')), 
			true);
	}
	// when plugin is activated, create options array and populate with defaults
	register_activation_hook(__FILE__, 'jpgrf_activate');
	function jpgrf_activate() {
		$options = jpgrf_getpluginoptions();
		update_option(jpgrf_get_option(), $options);
		
		// delete option when plugin is uninstalled
		register_uninstall_hook(__FILE__, 'uninstall_jpgrf_plugin');
	}
	function uninstall_jpgrf_plugin() {
		delete_option(jpgrf_get_option());
	}
	// generic function that returns plugin options from DB
	// if option does not exist, returns plugin defaults
	function jpgrf_getpluginoptions() {
		return get_option(jpgrf_get_option(), array(
			JPGRF_DEFAULT_ENABLED_NAME => JPGRF_DEFAULT_ENABLED, 
			JPGRF_DEFAULT_URL_NAME => JPGRF_DEFAULT_URL, 
			JPGRF_DEFAULT_NUM_NAME => JPGRF_DEFAULT_NUM, 
			JPGRF_DEFAULT_NOFOLLOW_NAME => JPGRF_DEFAULT_NOFOLLOW, 
			JPGRF_DEFAULT_CSSCLASS_NAME => JPGRF_DEFAULT_CSSCLASS, 
			JPGRF_DEFAULT_DESC_NAME => JPGRF_DEFAULT_DESC, 
			JPGRF_DEFAULT_NEWWINDOW_NAME => JPGRF_DEFAULT_NEWWINDOW, 
			JPGRF_DEFAULT_SORTORDER_NAME => JPGRF_DEFAULT_SORTORDER, 
			JPGRF_DEFAULT_CHARS_NAME => JPGRF_DEFAULT_CHARS, 
			JPGRF_DEFAULT_POSTLINK_NAME => JPGRF_DEFAULT_POSTLINK,
			JPGRF_DEFAULT_POSTTHUMBNAIL_NAME => JPGRF_DEFAULT_POSTTHUMBNAIL,
			JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME => JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE,
			JPGRF_DEFAULT_CACHETIME_NAME => JPGRF_DEFAULT_CACHETIME,
			JPGRF_DEFAULT_DATE_SHOW_NAME => JPGRF_DEFAULT_DATE_SHOW, 
			JPGRF_DEFAULT_DATE_POSITION_NAME => JPGRF_DEFAULT_DATE_POSITION,
			JPGRF_DEFAULT_DATE_FORMAT_NAME => JPGRF_DEFAULT_DATE_FORMAT			
			));
	}
	// function to return shortcode defaults
	function jpgrf_shortcode_defaults() {
		return array(
				JPGRF_DEFAULT_URL_NAME => JPGRF_DEFAULT_URL, 
				JPGRF_DEFAULT_NUM_NAME => JPGRF_DEFAULT_NUM, 
				JPGRF_DEFAULT_CHARS_NAME => JPGRF_DEFAULT_CHARS, 
				JPGRF_DEFAULT_NOFOLLOW_NAME => JPGRF_DEFAULT_NOFOLLOW, 
				JPGRF_DEFAULT_CSSCLASS_NAME => JPGRF_DEFAULT_CSSCLASS, 
				JPGRF_DEFAULT_DESC_NAME => JPGRF_DEFAULT_DESC, 
				JPGRF_DEFAULT_NEWWINDOW_NAME => JPGRF_DEFAULT_NEWWINDOW, 
				JPGRF_DEFAULT_POSTLINK_NAME => JPGRF_DEFAULT_POSTLINK, 
				JPGRF_DEFAULT_SORTORDER_NAME => JPGRF_DEFAULT_SORTORDER, 
				JPGRF_DEFAULT_SHOW_NAME => JPGRF_DEFAULT_SHOW,
				JPGRF_DEFAULT_POSTTHUMBNAIL_NAME => JPGRF_DEFAULT_POSTTHUMBNAIL,
				JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE_NAME => JPGRF_DEFAULT_POSTTHUMBNAIL_SIZE,
				JPGRF_DEFAULT_CACHETIME_NAME => JPGRF_DEFAULT_CACHETIME,
				JPGRF_DEFAULT_DATE_SHOW_NAME => JPGRF_DEFAULT_DATE_SHOW, 
				JPGRF_DEFAULT_DATE_POSITION_NAME => JPGRF_DEFAULT_DATE_POSITION,
				JPGRF_DEFAULT_DATE_FORMAT_NAME => JPGRF_DEFAULT_DATE_FORMAT
		);
	}
	// function to return parameter status (required or not)
	// this MUST mirror the shortcode defaults function exactly
	function jpgrf_required_parameters() {
		return array(
			true,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			false,
			false
		);
	}
	// return feed
	function jpgrf_fetch_feed($feed_url, $cachetime) {
		// http://wordpress.org/support/topic/fetch_feed-cache-time
		add_filter('wp_feed_cache_transient_lifetime', create_function('', "return $cachetime;"));
		// http://scratch99.com/wordpress/development/fetch_feed-url-problem/
		$feed = fetch_feed(wp_specialchars_decode($feed_url));
		remove_all_filters('wp_feed_cache_transient_lifetime');
		return $feed;
	}

	// encapsulate these and call them throughout the plugin instead of hardcoding the constants everywhere
	function jpgrf_get_slug() { return JPGRF_SLUG; }
	function jpgrf_get_local() { return JPGRF_LOCAL; }
	function jpgrf_get_option() { return JPGRF_OPTION; }
	function jpgrf_get_path() { return JPGRF_PATH; }
	
	function jpgrf_settingslink($linklist, $slugname = '', $localname = '') {
		$settings_link = sprintf( __('<a href="options-general.php?page=%s">Settings</a>', $localname), $slugname);
		array_unshift($linklist, $settings_link);
		return $linklist;
	}
	function jpgrf_setupvar($var, $defaultvalue, $defaultvarname, $optionsarr) {
		if ($var == $defaultvalue) {
			$var = $optionsarr[$defaultvarname];
			if (!$var) {
				$var = $defaultvalue;
			}
		}
		return $var;
	}
	function jpgrf_getsupportinfo($slugname = '', $localname = '') {
		$output = __('Do you need help with this plugin? Check out the following resources:', $localname);
		$output .= '<ol>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/extend/plugins/%s/">Documentation</a>', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/plugins/%s/faq/">FAQ</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/support/plugin/%s">Support Forum</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://www.jimmyscode.com/wordpress/%s">Plugin Homepage / Demo</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/extend/plugins/%s/developers/">Development</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/plugins/%s/changelog/">Changelog</a><br />', $localname), $slugname) . '</li>';
		$output .= '</ol>';
		
		$output .= sprintf( __('If you like this plugin, please <a href="http://wordpress.org/support/view/plugin-reviews/%s/">rate it on WordPress.org</a>', $localname), $slugname);
		$output .= sprintf( __(' and click the <a href="http://wordpress.org/plugins/%s/#compatibility">Works</a> button. ', $localname), $slugname);
		$output .= '<br /><br /><br />';
		$output .= __('Your donations encourage further development and support. ', $localname);
		$output .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate with PayPal" title="Support this plugin" width="92" height="26" /></a>';
		$output .= '<br /><br />';
		return $output;	
	}
	
	function jpgrf_parameters_table($localname = '', $sc_defaults, $reqparms) {
	  $output = '<table class="widefat">';
		$output .= '<thead><tr>';
		$output .= '<th title="' . __('The name of the parameter', $localname) . '"><strong>' . __('Parameter Name', $localname) . '</strong></th>';
		$output .= '<th title="' . __('Is this parameter required?', $localname) . '"><strong>' . __('Is Required?', $localname) . '</strong></th>';
		$output .= '<th title="' . __('What data type this parameter accepts', $localname) . '"><strong>' . __('Data Type', $localname) . '</strong></th>';
		$output .= '<th title="' . __('What, if any, is the default if no value is specified', $localname) . '"><strong>' . __('Default Value', $localname) . '</strong></th>';
		$output .= '</tr></thead>';
		$output .= '<tbody>';
		
		$plugin_defaults_keys = array_keys($sc_defaults);
		$plugin_defaults_values = array_values($sc_defaults);
		$required = $reqparms;
		for($i = 0; $i < count($plugin_defaults_keys); $i++) {
			$output .= '<tr>';
			$output .= '<td><strong>' . $plugin_defaults_keys[$i] . '</strong></td>';
			$output .= '<td>';
			
			if ($required[$i] === true) {
				$output .= '<strong>';
				$output .= __('Yes', $localname);
				$output .= '</strong>';
			} else {
				$output .= __('No', $localname);
			}
			
			$output .= '</td>';
			$output .= '<td>' . gettype($plugin_defaults_values[$i]) . '</td>';
			$output .= '<td>';
			
			if ($plugin_defaults_values[$i] === true) {
				$output .= '<strong>';
				$output .= __('true', $localname);
				$output .= '</strong>';
			} elseif ($plugin_defaults_values[$i] === false) {
				$output .= __('false', $localname);
			} elseif ($plugin_defaults_values[$i] === '') {
				$output .= '<em>';
				$output .= __('this value is blank by default', $localname);
				$output .= '</em>';
			} elseif (is_numeric($plugin_defaults_values[$i])) {
				$output .= $plugin_defaults_values[$i];
			} else { 
				$output .= '"' . $plugin_defaults_values[$i] . '"';
			} 
			$output .= '</td>';
			$output .= '</tr>';
		}
		$output .= '</tbody>';
		$output .= '</table>';
		
		return $output;
	}
	function jpgrf_get_example_shortcode($shortcodename = '', $sc_defaults, $localname = '') {
		$output = '<pre style="background:#FFF">[' . $shortcodename . ' ';
		
		$plugin_defaults_keys = array_keys($sc_defaults);
		$plugin_defaults_values = array_values($sc_defaults);
		
		for($i = 0; $i < count($plugin_defaults_keys); $i++) {
			if ($plugin_defaults_keys[$i] !== 'show') {
				if (gettype($plugin_defaults_values[$i]) === 'string') {
					$output .= '<strong>' . $plugin_defaults_keys[$i] . '</strong>=\'' . $plugin_defaults_values[$i] . '\'';
				} elseif (gettype($plugin_defaults_values[$i]) === 'boolean') {
					$output .= '<strong>' . $plugin_defaults_keys[$i] . '</strong>=' . ($plugin_defaults_values[$i] == false ? 'false' : 'true');
				} else {
					$output .= '<strong>' . $plugin_defaults_keys[$i] . '</strong>=' . $plugin_defaults_values[$i];
				}
				if ($i < count($plugin_defaults_keys) - 2) {
					$output .= ' ';
				}
			}
		}
		$output .= ']</pre>';
		
		return $output;
	}
	
	function jpgrf_get_example_php_code($shortcodename = '', $internalfunctionname = '', $sc_defaults) {
		$plugin_defaults_keys = array_keys($sc_defaults);
		$plugin_defaults_values = array_values($sc_defaults);
		
		$output = '<pre style="background:#FFF">';
		$output .= 'if (shortcode_exists(\'' . $shortcodename . '\')) {<br />';
		$output .= '  $atts = array(<br />';
		for($i = 0; $i < count($plugin_defaults_keys); $i++) {
			$output .= '    \'' . $plugin_defaults_keys[$i] . '\' => ';
			if (gettype($plugin_defaults_values[$i]) === 'string') {
				$output .= '\'' . $plugin_defaults_values[$i] . '\'';
			} elseif (gettype($plugin_defaults_values[$i]) === 'boolean') {
				$output .= ($plugin_defaults_values[$i] == false ? 'false' : 'true');
			} else {
				$output .= $plugin_defaults_values[$i];
			}
			if ($i < count($plugin_defaults_keys) - 1) {
				$output .= ', <br />';
			}
		}
		$output .= '<br />  );<br />';
		$output .= '   echo ' . $internalfunctionname . '($atts);';
		$output .= '<br />}';
		$output .= '</pre>';
		return $output;	
	}
	function jpgrf_checkifset($optionname, $optiondefault, $optionsarr) {
		return (isset($optionsarr[$optionname]) ? $optionsarr[$optionname] : $optiondefault);
	}
	function jpgrf_getlinebreak() {
	  echo '<tr valign="top"><td colspan="2"></td></tr>';
	}
	function jpgrf_explanationrow($msg = '') {
		echo '<tr valign="top"><td></td><td><em>' . $msg . '</em></td></tr>';
	}
	function jpgrf_getimagefilename($fname = '') {
		return plugins_url(jpgrf_get_path() . '/images/' . $fname);
	}
?>
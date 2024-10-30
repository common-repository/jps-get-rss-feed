=== JP's Get RSS Feed ===
Tags: rss, xml, feed, fetch, display, list
Requires at least: 3.5
Tested up to: 3.9
Contributors: jp2112
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get last X number of posts from a selected RSS feed. Default is last 5 items. Includes shortcode for listing feed items on posts or pages.

== Description ==

<strong>Notice: Version 1.3.1 introduced admin menus for plugin settings. There is also a new way to pass parameters to the shortcode and PHP function. You should check all shortcodes and plugin function calls before and after upgrading, to confirm compatibility with this version.</strong>

<h3>If you need help with this plugin</h3>

If this plugin breaks your site or just flat out does not work, please go to <a href="http://wordpress.org/plugins/jps-get-rss-feed/#compatibility">Compatibility</a> and click "Broken" after verifying your WordPress version and the version of the plugin you are using.

Then, create a thread in the <a href="http://wordpress.org/support/plugin/jps-get-rss-feed">Support</a> forum with a description of the issue. Make sure you are using the latest version of WordPress and the plugin before reporting issues, to be sure that the issue is with the current version and not with an older version where the issue may have already been fixed.

<strong>Please do not use the <a href="http://wordpress.org/support/view/plugin-reviews/jps-get-rss-feed">Reviews</a> section to report issues or request new features.</strong>

= Features =

- Show RSS feed items on any post or page, in your sidebar or footer, or anywhere else using conditional tags in your PHP code
- Open links in a new window (optional)
- Include item description next to each article link
- Post title link is optional. Show feed links as plain text or hyperlink to article
- Show Featured Image (AKA "post thumbnails") from local RSS feeds, or display feed logo for remote feeds
- Customize feed cache lifetime (default minimum 43200 seconds / 12 hours)

This plugin uses WordPress' ability to return feeds, to get the last X number of items from any RSS feed. Display the last few items from any RSS feed of your choice. For example, your Twitter feed, or another blog or forum that outputs a RSS feed. Any RSS feed can be grabbed. Call it in your footer to list your last few tweets, or your sidebar to showcase content from another one of your blogs.

Uses `fetch_feed`, which was introduced in WordPress 2.8. Works and tested in WordPress 3.5 and above. By default, feeds are cached for 12 hours. You can choose a shorter (or longer) cache time.

Feed items are wrapped in a div tag, with class "jpgetrssfeed" so you can style the output in your CSS file. The items list is surrounded by `<ul></ul>` tags, with each feed item listed in a `<li></li>` tag. However, you can specify a new CSS class to style output differently for different feeds.

You can output the feed item description along with each feed item link.

A button is added to the post editor toolbar so you can insert the shortcode in your posts or pages.

With help from: 
http://codex.wordpress.org/Function_Reference/fetch_feed

= Shortcode =

To display a feed on any post or page, use this shortcode:

[jp-rss-feed]

Make sure you go to the plugin settings page after installing to set options.

<strong>If you use and enjoy this plugin, please rate it and click the "Works" button below so others know that it works with the latest version of WordPress.</strong>

== Installation ==

1. Upload plugin file through the WordPress interface.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings &raquo; JP's Get RSS Feed, configure plugin.
4. Insert shortcode on posts or pages, or use PHP function in functions.php or a plugin.

== Frequently Asked Questions ==

= How do I use the plugin? =

You can use the plugin in two ways:

1. In your PHP code (functions.php, or a plugin), you would call the plugin function like this:

`if (function_exists('jp_get_rss_feed_items')) {
  jp_get_rss_feed_items(array(
    'url' => "http://somefeed.com/rss", 
    'numitems' => 5,
    'nofollow' => true,
    'cssclass' => 'myclass',
    'getdesc' => false,
    'opennewwindow' => false,
    'show' => true    
  ));
}`

This will:
<ul>
<li>fetch the feed located at http://somefeed.com/rss</li>
<li>list the last 5 items</li>
<li>add rel="nofollow" to each link</li>
<li>add the "myclass" CSS class to the div tag that wraps the output (instead of default "jpgetrssfeed")</li>
<li>hide the item description</li>
<li>open links in the same window</li>
<li>echo the content to the screen</li>
<li>Hyperlink the post title</li>
</ul>

This will override any settings you configured on the plugin's Settings page. Always wrap plugin function calls in a `function_exists` check so that your site doesn't go down if the plugin is inactive.

2. As a shortcode, you call the plugin like this:

`[jp-rss-feed numitems="3"]`

This will take the settings from the Settings page (or the defaults if a setting is missing) and apply them to the shortcode, except there will only be three items even if you specified 5 on the Settings page. In this way you can override settings by passing them to the shortcode at runtime.

= What are the plugin defaults? =

The plugin arguments and default values may change over time. To get the latest list of arguments and defaults, look at the settings page after installing the plugin.

= I want to use the plugin in a widget. How? =

Add this line of code to your functions.php:

`add_filter('widget_text', 'do_shortcode');`

Or, install a plugin to do it for you: http://blogs.wcnickerson.ca/wordpress/plugins/widgetshortcodes/

Now, add the built-in text widget that comes with WordPress, and insert the JP's Get RSS Feed shortcode into the text widget. See above for how to use the shortcode.

See http://digwp.com/2010/03/shortcodes-in-widgets/ for a detailed example.

Keep in mind, if you want to show your blog feed in a sidebar widget, WordPress already has a built-in "Recent Posts" widget for that.

= I don't want the post editor toolbar button. How do I remove it? =

Add this to your functions.php:

`remove_action('admin_enqueue_scripts', 'jpgrf_ed_buttons');`

= I inserted the shortcode but don't see anything on the page. =

Clear your browser cache and also clear your cache plugin (if any). If you still don't see anything, check your webpage source for the following:

`<!-- JP's Get RSS Feed: plugin is disabled. Check Settings page. -->`

This means you didn't pass a necessary setting to the plugin, so it disabled itself. You need to pass at least the URL, either by entering it on the settings page or passing it to the plugin in the shortcode or PHP function. You should also check that the "enabled" checkbox on the plugin settings page is checked. If that box is not checked, the plugin will do nothing even if you pass it a URL.

= I requested 10 feed items but I only see 5. How do I increase the limit? =

If the RSS feed only includes 10 items at a time, you will only ever be able to request 10 items at most. (Hint: Load the feed in your browser to confirm) Even if you request 100 items, you will only get at most 10. You should contact whomever controls the feed and ask them to increase the number of items in their feed.

For WordPress blogs, go to Settings &raquo; Reading and change the value of "Syndication feeds show the most recent ___ items" to change the number of feed items available.

= Genesis Theme Framework users =

If you are using the Genesis framework from Studiopress, you might use the plugin like this:

`add_action('genesis_after_post_content', 'showrss');
function showrss() {
  if (is_page('faq')) { // we are on the FAQ page...
    if (function_exists('jp_get_rss_feed_items')) { // and the function exists...
      echo '<h3>Latest Articles From my Favorite RSS Feed</h3>';
      jp_get_rss_feed_items(array('url' => "http://feeds.feedburner.com/MyFavoriteRSSFeed", 'show' => true));
      // or: echo jp_get_rss_feed_items(array('url' => "http://feeds.feedburner.com/MyFavoriteRSSFeed"));
    }
  }
}`

This code would go in your functions.php file, or (ideally) in a plugin. Check the <a href="http://my.studiopress.com/docs/hook-reference/">Hook Reference</a> to determine where you want to place the output. The above example (remember, for Genesis framework only) would show the last five articles from a given RSS feed at the bottom of the 'FAQ' page.

= How can I list from multiple feeds? =

Use an array and a foreach loop:

`if (function_exists('jp_get_rss_feed_items')) {
  // create array
  $feedslist = array(
  "My feed URL number one",
  "My feed URL number two",
  "My feed URL number three"
  );
  // loop through array and call plugin
  foreach ($feedslist as $item) {
   jp_get_rss_feed_items(array('url' => $item, 'show' => true));
  }
}`

This will list the last five items from each feed in its own unordered list.

But suppose you want a different CSS class for each one. Use a for loop instead.

`if (function_exists('jp_get_rss_feed_items')) {
  // create array
  $feedslist = array(
  "My feed URL number one",
  "My feed URL number two",
  "My feed URL number three"
  );
  // loop through array and call plugin
  for ($i = 0, $size = count($feedslist); $i < $size; $i++) {
   jp_get_rss_feed_items(array('url' => $feedslist[$i], 'cssclass' => 'jpgetrssfeed_' . $i , 'show' => true));
  }
}`

So your CSS classes would be

- jpgetrssfeed_1
- jpgetrssfeed_2
- jpgetrssfeed_3

= How can I list items from a random feed? =

Use `array_rand`:

`if (function_exists('jp_get_rss_feed_items')) {
  // create array
  $feedslist = array(
  "My feed URL number one",
  "My feed URL number two",
  "My feed URL number three"
  );
  // get random index from array
  $item = array_rand($feedslist, 1);
  // pass randomly selected array member to plugin
  jp_get_rss_feed_items(array('url' => $feedslist[$item], 'show' => true));
}`

This selects one URL randomly and passes it to the plugin. 

= How can I style the output? =

Feed items are wrapped in a div tag, with class "jpgetrssfeed" (or whatever you change it to) so you can style the output in your CSS file. The items list is surrounded by `<ul></ul>` tags, with each feed item listed in a `<li></li>` tag.

So you could add something like this in your style.css:

`.jpgetrssfeed {border:1px solid gray;margin:10px 0}
.jpgetrssfeed ul li {list-style-type:circle}`

You can also specify your own class, or use a different class name for each shortcode to style it differently. Ex:

In my style.css file I add the following

.rssorange {border:1px solid #FF9900;margin:10px 0}
.rssblue {border:1px solid blue;margin:10px 0}
.rssred {border:1px solid red;margin:10px 0}

I specify each class in my shortcodes as follows:

[jp-rss-feed url="http://somefeed.com/rss" cssclass="rssorange"]
[jp-rss-feed url="http://some_other_feed.com/rss" cssclass="rssblue"]
[jp-rss-feed url="http://some_new_feed.com/rss" cssclass="rssred"]

Each feed will be surrounded by a different color border.

= How do I add a header or title above the feed items list? =

See the examples above. Before you call the shortcode or the PHP function, echo your header like this:

`echo '<h3>Latest Articles From my Favorite RSS Feed</h3>';`

Then call the PHP function or shortcode.

= I don't want the admin CSS. How do I remove it? =

Add this to your functions.php:

`remove_action('admin_head', 'insert_jpgrf_admin_css');`

= I don't see the plugin toolbar button(s). =

This plugin adds one or more toolbar buttons to the HTML editor. You will not see them on the Visual editor.

The label on the toolbar button is "RSS Feed".

= I don't want to see such a long description. =

Enter a lower character limit on the plugin's options page.

= The plugin is breaking the RSS feed or my site. =

Check the number of characters in the feed item description textbox. Try increasing it.

Remember that the character limit cuts off the item description. If the item description contains HTML and you cut it off in the middle, you are leaving unclosed HTML tags which can affect the rest of the page.

= I want to display items in reverse order. = 

See the plugin settings page. There is a dropdown box for you to indicate the sort order. The default is newest first, change the dropdown box to 'oldesfirst' and the items will be sorted in reverse date order.

= I am using the shortcode but the parameters aren't working. =

On the plugin settings page, go to the "Parameters" tab. There is a list of possible parameters there along with the default values. Make sure you are spelling the parameters correctly.

The Parameters tab also contains sample shortcode and PHP code.

== Screenshots ==

1. Plugin settings page
2. Example output using default settings from plugin settings page

== Changelog ==

= 1.6.3 =
- updated .pot file and readme

= 1.6.2 =
- fixed validation issue

= 1.6.1 =
- minor code optimizations

= 1.6.0 =
- code fix
- admin CSS and page updates

= 1.5.9 =
- updated support tab

= 1.5.8 =
- minor code fix

= 1.5.7 =
- if plugin is temporarily disabled (from plugin settings page) then skip some code to save some cycles
- minor code fix

= 1.5.6 =
- code optimizations
- use 'url', 'href', 'link' or 'rss' as the URL parameter name
- plugin settings page is now tabbed

= 1.5.5 =
- fix for wp_kses

= 1.5.4 =
- some minor code optimizations
- verified compatibility with 3.9

= 1.5.3 =
- OK, I am going to stop playing with the plugin now. Version check rolled back (again)

= 1.5.2 =
- plugin now requires WP 3.5 and PHP 5.2 and above

= 1.5.1 =
- minor code optimization
- cache fix per Otto in wordpress.org forum

= 1.5.0 =
- prepare strings for internationalization
- plugin now requires WP 3.5 and PHP 5.3 and above, gracefully deactivate otherwise

= 1.4.9 =
- minor bug with parameter table on plugin settings page
- show Featured Image with custom size (for local feeds) or feed logo for remote feeds
- customize feed cache lifetime
- minor plugin settings page update

= 1.4.8 =
- added submit button to top of plugin settings form

= 1.4.7 =
- option to display items in reverse order
- option to de-link post titles
- refactored admin CSS
- added helpful links on plugin settings page and plugins page
- made CSS and JS files automatically bust cache
- removed screen_icon() (deprecated)
- updated compatibility to WP 3.8.1

= 1.4.6 =
added item description character limit to plugin options page

= 1.4.5 =
fixed uninstall routine, actually deletes options now

= 1.4.4 =
- updated the plugin settings page list of parameters to indicate whether they are required or not
- updated FAQ section of readme.txt

= 1.4.3 =
some security hardening added

= 1.4.2 =
- target="_blank" is deprecated, replaced with javascript fallback
- changed esc_url to sanitize_text_field for URL cleansing

= 1.4.1 =
- encapsulated javascript into its own file

= 1.4.0 =
- minor code refactoring
- changed esc_html to strip_tags

= 1.3.9 =
- minor code refactoring
- added shortcode defaults display on settings page

= 1.3.8 =
- added donate link on admin page
- admin page CSS added
- admin page tweaks
- addressed issue from http://wordpress.org/support/topic/get-cannot-modify-header-information-error-message (I think)
- various sanitization and HTML5 options
- minor code refactoring

= 1.3.7 =
- minor code refactoring
- added option to open links in new window

= 1.3.6 =
- another minor admin page update

= 1.3.5 =
- minor admin page update

= 1.3.4 =
- updated admin messages code
- updated readme
- added feed item description option

= 1.3.3 =
* added quicktag to post editor toolbar button
* code refactoring

= 1.3.2 =
* correct handling of defaults
* fixed readme.txt errors

= 1.3.1 =
* added admin menu, moved settings there
* localized the plugin to prep for translation files (anyone want to contribute?)
* code refactoring

= 1.3.0 =
added shortcode, CSS styling, nofollow option, some code cleanup

= 1.2 =
January 2nd, 2012. Validated compatibility with WP 3.3. Added Genesis hook example to FAQ.

= 1.1 =
* Version 1.1 completed December 21st, 2010. This update fixes a bug when activating and using plugin.

= 1.0 =
* Version 1.0 completed December 28th, 2009.

== Upgrade Notice ==

= 1.6.3 =
- updated .pot file and readme

= 1.6.2 =
- fixed validation issue

= 1.6.1 =
- minor code optimizations

= 1.6.0 =
- code fix; admin CSS and page updates

= 1.5.9 =
- updated support tab

= 1.5.8 =
- minor code fix

= 1.5.7 =
- if plugin is temporarily disabled (from plugin settings page) then skip some code to save some cycles; minor code fix

= 1.5.6 =
- code optimizations; use 'url', 'href', 'link' or 'rss' as the URL parameter name; plugin settings page is now tabbed

= 1.5.5 =
- fix for wp_kses

= 1.5.4 =
- some minor code optimizations, verified compatibility with 3.9

= 1.5.3 =
- OK, I am going to stop playing with the plugin now. Version check rolled back (again)

= 1.5.2 =
- plugin now requires WP 3.5 and PHP 5.2 and above

= 1.5.1 =
- minor code optimization, cache fix per Otto in wordpress.org forum

= 1.5.0 =
- prepare strings for internationalization, plugin now requires WP 3.5 and PHP 5.3 and above, gracefully deactivate otherwise

= 1.4.9 =
- minor bug with parameter table on plugin settings page, show Featured Image with custom size (for local feeds) or feed logo for remote feeds, customize feed cache lifetime, minor plugin settings page update

= 1.4.8 =
- added submit button to top of plugin settings form

= 1.4.7 =
option to de-link post titles, 
option to display items in reverse order, 
refactored admin CSS, 
added helpful links on plugin settings page and plugins page, 
made CSS and JS files automatically bust cache, 
removed screen_icon() (deprecated), 
updated compatibility to WP 3.8.1

= 1.4.6 =
added item description character limit to plugin options page

= 1.4.5 =
fixed uninstall routine, actually deletes options now

= 1.4.4 =
- updated the plugin settings page list of parameters to indicate whether they are required or not
- updated FAQ section of readme.txt

= 1.4.3 =
some security hardening added

= 1.4.2 =
- target="_blank" is deprecated, replaced with javascript fallback
- changed esc_url to sanitize_text_field for URL cleansing

= 1.4.1 =
- encapsulated javascript into its own file

= 1.4.0 =
- minor code refactoring
- changed esc_html to strip_tags

= 1.3.9 =
- minor code refactoring
- added shortcode defaults display on settings page

= 1.3.8 =
- added donate link on admin page
- admin page CSS added
- admin page tweaks
- addressed issue from http://wordpress.org/support/topic/get-cannot-modify-header-information-error-message (I think)
- various sanitization and HTML5 options
- minor code refactoring

= 1.3.7 =
- minor code refactoring
- added option to open links in new window

= 1.3.6 =
- another minor admin page update

= 1.3.5 =
- minor admin page update

= 1.3.4 =
- updated admin messages code
- updated readme
- added feed item description option

= 1.3.3 =
* added quicktag to post editor toolbar button
* code refactoring

= 1.3.2 =
* correct handling of defaults
* fixed readme.txt errors

= 1.3.1 =
* added admin menu, moved settings there
* localized the plugin to prep for translation files (anyone want to contribute?)
* code refactoring

= 1.3.0 =
This version adds a shortcode, optional CSS styling, optional nofollow
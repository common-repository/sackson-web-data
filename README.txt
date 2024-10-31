=== SacksonWeb Data ===
Contributors: ehops32
Donate link: http://data.sacksonweb.com/author
Tags: settings, monitor, security, efficiency, seo
Requires at least: 3.0.1
Tested up to: 6.0
Stable tag: 1.3.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A tool to monitor security issues, performance issues, and Wordpress settings that should be reviewed for potential changes.

== Description ==

Free version - This plugin will locally collect data from the website you install it on. You can then access the settings page to see a collection of settings
that we recommend you review and consider changing. 

The PRO version of this plugin leverages a third party service found at https://mm.ericandtammy.com/receiver which acts as the home collection
place for all your websites collected data. As you install SacksonWeb Data on all your websites and opt-in to activite the Pro version, then the data is not 
only gathered locally, but also remotely. You will be able to log in at our main service site and view all your websites key data elements from a single location.  
The terms of use are the same as for the SacksonWeb Data plugin. Visit your Wordpress Menu > Settings > SacksonWeb Pro - Settings.

Data will not be remotley collected unless you update the plugins default setting to allow remote data collection. Opt-In and enable this in the SacksonWeb plugin settings to start using
this PRO feature to aggregate data from all your websites in one place. Visit your Wordpress Menu > Settings > SacksonWeb Pro - Settings.

== Installation ==

1. Upload `sacksonweb-data.zip` through the WordPress Plugin install process
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In Settings > SacksonWeb Data - wait 1 hour and then refresh the page to see your recommended settings changes
4. In Settings > SacksonWeb Data - opt-in or opt-out of remote data collection
5. Request access to the remote Pro version via email to eric@sacksonweb.com. To manage and monitor WordPress settings and suggestions from a single list for all your websites. 


== Frequently Asked Questions ==

= Can I use this plugin without opting into the remote data collection =

Yes. Remote data collection makes the most sense if you are a webmaster of multiple websites.

= Where can I find the recommended settings that are found? =

A new menu item will appear in your WordPress admin side and you can see the recommendations there.
If you use the Pro version, you will access multiple website suggestions at https://data.sackonweb.com

== Screenshots ==

1. This is the settings and opt-in page of this plugin.

== Changelog ==

= 1.3.1 =
* Bug fix for email log errors

= 1.3.0 =
* Gives an option to disable various email types like for theme/plugin auto updates
* Gather email log data so we can alert you to any email errors your site is having.

= 1.2.9 =
* Disables emails about auto plugin updates
* Disables admin email verification
* Hides the WordPress version from viewing

= 1.2.8 =
* A bug fix regarding FS_METHOD

= 1.2.7 =
* Capture sec settings data for sites that it exists on to enhance security further

= 1.2.6 =
* A bug fix regarding FS_METHOD
* Additional data added to site users 

= 1.2.5 =
* A new datapoint allowing the monitoring of minimum plugin versions.

= 1.2.4 =
* Bug fixes

= 1.2.3 = 
* Adding support to check the WP SMTP mail plugin to make sure Google is authorized
* Confirmed user logins and user lists are functioning

= 1.2.2 = 
* Adding FSDIRECT if not defined
* Fixing a bug related to accessing a value of type bool

= 1.2.1 =
* Adjusted for PHP 8 - defined field check was throwing an error 
* Added a new, check for any plugin being installed, using the name.

= 1.2.0 =
* Added a check to be sure plugins_loaded as the hook


= 1.1.9 =
* Bugfix - was causing an issue on home page loading

= 1.1.8 =
* Add a settings link on the plugin page to allow quick access to the settings, especially after a new install.
* Corrected a PHP warning on an empty array.
* Added option to get last login users

= 1.1.7 =
* Added an extra check to make sure the simple history table exists as its expected to be named.

= 1.1.6 =
* Checks if the simple history plugin is installed. If so, looks at the last few accounts that have logged into the website.
* Changing the remote server to now be data.sacksonweb.com.
* Check to see values in .user.ini if it exists.

= 1.1.5.1 =
* Missed a check in release 1.1.5, correcting the PHP warning

= 1.1.5 =
* Corrected a code error about accessing FS_DIRECT when it doesn't exist
* Added a setting to control how often your website checks for configuration issues and (if enabled, calls back to main server)

= 1.1.4 =
* Corrected a code warning about accessing an array
* Added 4 more data points, will check to see if wp-content/debug.log, .htaccess, and .user.ini exist, and 
* then also, what is the value for FS_DIRECT path

= 1.1.1 =
* Changes to the readme file.

= 1.1.0 =
* A new plugin structure.

== Upgrade Notice ==

= 1.1.0 =
This version should be more efficient and load faster on your website.
<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://data.sacksonweb.com/author
 * @since             1.3.1
 * @package           Sacksonweb_Data
 *
 * @wordpress-plugin
 * Plugin Name:       SacksonWeb Data
 * Plugin URI:        http://data.sacksonweb.com
 * Description:       A tool to monitor security issues, performance issues, and Wordpress settings that should be changed.
 * Version:           1.3.1
 * Author:            Eric Thornton
 * Author URI:        http://data.sacksonweb.com/author
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sacksonweb-data
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * 
 */
define( 'SACKSONWEB_DATA_VERSION', '1.3.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sacksonweb-data-activator.php
 */
function activate_sacksonweb_data() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sacksonweb-data-activator.php';
	Sacksonweb_Data_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sacksonweb-data-deactivator.php
 */
function deactivate_sacksonweb_data() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sacksonweb-data-deactivator.php';
	Sacksonweb_Data_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sacksonweb_data' );
register_deactivation_hook( __FILE__, 'deactivate_sacksonweb_data' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sacksonweb-data.php';


/**
 * Add a settings link on the plugin page to allow quick access to the settings, especially after a new install.
 */
function my_plugin_settings_link($links) { 
	$settings_link = '<a href="options-general.php?page=sackson-web-premium-settings.php">Settings</a>'; 
	array_unshift($links, $settings_link); 
	return $links;  
}
$plugin = plugin_basename(__FILE__); 
add_filter('plugin_action_links_' . $plugin, 'my_plugin_settings_link' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.1.0
 */
function run_sacksonweb_data() {

	$plugin = new Sacksonweb_Data();
	$plugin->run();

}
run_sacksonweb_data();
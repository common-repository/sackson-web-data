<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://data.sacksonweb.com/author
 * @since      1.0.0
 *
 * @package    Sacksonweb_Data
 * @subpackage Sacksonweb_Data/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sacksonweb_Data
 * @subpackage Sacksonweb_Data/includes
 * @author     Eric Thornton <eric@sacksonweb.com>
 */
class Sacksonweb_Data_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sacksonweb-data',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

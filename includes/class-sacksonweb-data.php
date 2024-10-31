<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://data.sacksonweb.com/author
 * @since      1.0.0
 *
 * @package    Sacksonweb_Data
 * @subpackage Sacksonweb_Data/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sacksonweb_Data
 * @subpackage Sacksonweb_Data/includes
 * @author     Eric Thornton <eric@sacksonweb.com>
 */
class Sacksonweb_Data {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sacksonweb_Data_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'SACKSONWEB_DATA_VERSION' ) ) {
			$this->version = SACKSONWEB_DATA_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sacksonweb-data';


		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$Sacksondata_Helper = new Sacksonweb_Data_Helper(); 
		$Sacksondata_Helper->run();

		// Disable WordPress Administration email verification prompt 
		$this->disableAdminVerificationPrompts();
		
		// Disable email notification for automatic plugin updates
		add_filter( 'auto_plugin_theme_update_email', 'myplugin_auto_plugin_theme_update_email', 10, 4 );

		// Hiding WordPress version:
		function wp_version_remove_version() {
			return '';
			}
		add_filter('the_generator', 'wp_version_remove_version');


	}
	

	/**
	 * method - myplugin_auto_plugin_theme_update_email
	 * Description - too many emails about successful plugin udpates? If the SW setting for the site is set, then this will disable successful plugin update emails.
	 */
	function myplugin_auto_plugin_theme_update_email( $email, $type, $successful_updates, $failed_updates ) {
		
		$sacksonweb_premium_settings_options = get_option( 'sacksonweb_premium_settings_option_name' );
		if (isset($sacksonweb_premium_settings_options['email_suppress_list']) && ! empty($sacksonweb_premium_settings_options['email_suppress_list'])) {
			$email_suppress_list = $sacksonweb_premium_settings_options['email_suppress_list'];
		}

		// First let's check our plugin settings to see if this website even wants to to make any changes
		if ( in_array( ['theme_plugin_update_failed'], $email_suppress_list) ) 
		{
			if ( 'fail' === $type ) {
				// Change the email subject when updates failed
				$email['subject'] = __( 'ATTN: IT Department – SOME AUTO-UPDATES WENT WRONG!', 'my-plugin' );
				
				// Change the email recipient.
				$email['to'] = 'eric@homesnaps.com';
			}
			else {
				$email['to'] = 'auto_delete_email_rule@homesnaps.com';
			}
		
			return $email;
		}

	



	}





	/**
	 * 
	 */
	public function disableAdminVerificationPrompts ()
	{
		add_filter( 'admin_email_check_interval', '__return_false' );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sacksonweb_Data_Loader. Orchestrates the hooks of the plugin.
	 * - Sacksonweb_Data_i18n. Defines internationalization functionality.
	 * - Sacksonweb_Data_Admin. Defines all hooks for the admin area.
	 * - Sacksonweb_Data_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sacksonweb-data-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sacksonweb-data-i18n.php';

		
		/**
		 * The class responsible for defining all actions that occur related to data collection
		 * 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sacksonweb-data-helper.php';


				
		/**
		 * The class creates a settings page so the logged in WP user can opt-in to data collection.
		 * 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sacksonweb-data-settings.php';



		/**
		 * The class is a random collection of helpful things that might apply to strings or other universal type of things.
		 * 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sacksonweb-data-misc.php';
		

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sacksonweb-data-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sacksonweb-data-public.php';

		$this->loader = new Sacksonweb_Data_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sacksonweb_Data_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sacksonweb_Data_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Sacksonweb_Data_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Sacksonweb_Data_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sacksonweb_Data_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	

	  

}

<?php

/**
 * 
 *
 * @link       http://data.sacksonweb.com/author
 * @since      1.1.4
 *
 * @package    Sacksonweb_Data
 * @subpackage Sacksonweb_Data/includes
 */

/**
 * 
 *
 * This class defines a settings page and a few options the WordPress logged in user can edit.
 *
 * @since      1.1.4
 * @package    Sacksonweb_Data
 * @subpackage Sacksonweb_Data/includes
 * @author     Eric Thornton <eric@sacksonweb.com>
 */
class Sacksonweb_Data_Settings {

    private $sacksonweb_premium_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'sacksonweb_premium_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'sacksonweb_premium_settings_page_init' ) );
	}

	public function sacksonweb_premium_settings_add_plugin_page() {
		add_options_page(
			'Sackson Web Pro - Settings', // page_title
			'Sackson Web Pro - Settings', // menu_title
			'manage_options', // capability
			'sackson-web-premium-settings', // menu_slug
			array( $this, 'sacksonweb_premium_settings_create_admin_page' ) // function
		);
	}

	public function sacksonweb_premium_settings_create_admin_page() {
		// Sample sacksonweb_premium_settings_option_name  -  a:3:{s:7:"email_0";s:0:"";s:15:"refresh_every_0";s:1:"1";s:7:"allow_1";s:3:"Yes";}
		$this->sacksonweb_premium_settings_options = get_option( 'sacksonweb_premium_settings_option_name' ); ?>

		<div class="wrap">
			<h2>Sackson Web Premium - Settings</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'sacksonweb_premium_settings_option_group' );
					do_settings_sections( 'sackson-web-premium-settings-admin' );
					submit_button();
				?>
			</form>
		</div>

        <div>
            <h2>You Site Settings Review</h2>
            <p>Here are some settings to consider reviewing (if any): </p>
            <?php 
                $sacksonweb_collected_data_from_database = get_option('sacksonweb_collected_data-'.Sacksonweb_Data_Misc::get_unique_url() );
                $site_data = ($sacksonweb_collected_data_from_database);
                
                $messages = array(); 

                if ( isset($site_data['WP_DEBUG']) && 'yes' == $site_data['WP_DEBUG'] )
                {
                    $messages[] = "I noticed that WP DEBUG is active.";
                }
                if ( isset($site_data['WP_DEBUG_LOG']) && 'yes' == $site_data['WP_DEBUG_LOG'] )
                {
                    $messages[] = "I noticed that WP DEBUG LOG is on.";
                }
                if ( isset($site_data['blog_public']) && '0' == $site_data['blog_public'] )
                {
                    $messages[] = "I noticed the setting in WordPress to make the site (blog) publically available was not set.";
                }

                if ( 0 == count($messages) )
				{
					echo 'The good news is, we did not find any problems with your website.<br />
					Keep in mind for the free version, we are only tracking 3 potential site issues. <br />
					For more information about signing up for the premium version, contact <a href="mailto:eric@sacksonweb.com">eric@sacksonweb.com</a>.';
				}
				else 
				{
					foreach ( $messages as $message )
					{
						echo '<p>' . esc_html($message) . '</p>';
					}
				}                  
            ?>

        </div>
	<?php }

	public function sacksonweb_premium_settings_page_init() {
		register_setting(
			'sacksonweb_premium_settings_option_group', // option_group
			'sacksonweb_premium_settings_option_name', // option_name
			array( $this, 'sacksonweb_premium_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'sacksonweb_premium_settings_setting_section', // id
			'Settings', // title
			array( $this, 'sacksonweb_premium_settings_section_info' ), // callback
			'sackson-web-premium-settings-admin' // page
		);

		add_settings_field(
			'refresh_every_0', // id
			'Refresh Every X Hours', // title
			array( $this, 'refresh_every_0_callback' ), // callback
			'sackson-web-premium-settings-admin', // page
			'sacksonweb_premium_settings_setting_section' // section
		);

		add_settings_field(
			'suppress_successful_email_updates', // id
			'Suppress Emails', // title
			array( $this, 'suppress_emails_list_field' ), // callback
			'sackson-web-premium-settings-admin', // page
			'sacksonweb_premium_settings_setting_section' // section
		);
		// Add Field for selecting countries for which you wanna ban ads
		//add_settings_field( 'aicp_country_list', __( 'Select the countries', 'aicp' ), 'country_list_field', 'aicp_settings', 'aicp_section' ); // id, title, display cb, page, section

		// Register Settings
		// register_setting( 'aicp_settings', 'aicp_settings_options', array( $this, 'validate_options' ) );
	


		add_settings_field(
			'email_0', // id
			'Email', // title
			array( $this, 'email_0_callback' ), // callback
			'sackson-web-premium-settings-admin', // page
			'sacksonweb_premium_settings_setting_section' // section
		);

		add_settings_field(
			'allow_1', // id
			'Allow Remote Data Collection (Opt - In)', // title
			array( $this, 'allow_1_callback' ), // callback
			'sackson-web-premium-settings-admin', // page
			'sacksonweb_premium_settings_setting_section' // section
		);



		
	}

	public function sacksonweb_premium_settings_sanitize($input) {
		$sanitary_values = array();

		if ( isset( $input['email_0'] ) ) {
			$sanitary_values['email_0'] = sanitize_text_field( $input['email_0'] );
		}

		if ( isset( $input['refresh_every_0'] ) ) {
			$sanitary_values['refresh_every_0'] = sanitize_text_field( $input['refresh_every_0'] );
		}

		if ( isset( $input['allow_1'] ) ) {
			$sanitary_values['allow_1'] = $input['allow_1'];
		}

		if ( isset( $input['email_suppress_list'] ) ) {
			$sanitary_values['email_suppress_list'] = $input['email_suppress_list'];
		}

		return $sanitary_values;
	}

	public function sacksonweb_premium_settings_section_info() {
		
	}

	public function email_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="sacksonweb_premium_settings_option_name[email_0]" id="email_0" value="%s">',
			isset( $this->sacksonweb_premium_settings_options['email_0'] ) ? esc_attr( $this->sacksonweb_premium_settings_options['email_0']) : ''
		);
	}
	
	public function refresh_every_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="sacksonweb_premium_settings_option_name[refresh_every_0]" id="refresh_every_0" value="%s">',
			isset( $this->sacksonweb_premium_settings_options['refresh_every_0'] ) ? esc_attr( $this->sacksonweb_premium_settings_options['refresh_every_0']) : ''
		);
	}

	public function allow_1_callback() {
		?> <fieldset><?php $checked = ( isset( $this->sacksonweb_premium_settings_options['allow_1'] ) && $this->sacksonweb_premium_settings_options['allow_1'] === 'Yes' ) ? 'checked' : '' ; ?>
		<label for="allow_1-0"><input type="radio" name="sacksonweb_premium_settings_option_name[allow_1]" id="allow_1-0" value="Yes" <?php echo esc_attr($checked); ?>> Yes</label><br>
		<?php $checked = ( isset( $this->sacksonweb_premium_settings_options['allow_1'] ) && $this->sacksonweb_premium_settings_options['allow_1'] === 'No' ) ? 'checked' : '' ; ?>
		<label for="allow_1-1"><input type="radio" name="sacksonweb_premium_settings_option_name[allow_1]" id="allow_1-1" value="No" <?php echo esc_attr($checked); ?>> No</label></fieldset> <?php
	}

	// now comes the section for checkbox
	public function suppress_emails_list_field() {

		$value = array();	

		if (isset($this->sacksonweb_premium_settings_options['email_suppress_list']) && ! empty($this->sacksonweb_premium_settings_options['email_suppress_list'])) {
			$value = $this->sacksonweb_premium_settings_options['email_suppress_list'];
		}

		$checked = in_array('theme_plugin_update_success', $value) ? 'checked' : '' ;
		echo '<input type="checkbox" name="sacksonweb_premium_settings_option_name[email_suppress_list][]" value="theme_plugin_update_success"' . $checked . '/> Suppress Successful Theme and Plugin Update Emails <br />';

		$checked = in_array('theme_plugin_update_failed', $value) ? 'checked' : '' ;
		echo '<input type="checkbox" name="sacksonweb_premium_settings_option_name[email_suppress_list][]" value="theme_plugin_update_failed"' . $checked . '/> Suppress Failed Theme and Plugin Update Emails <span style="color:red;">(not recommended)</span><br />';

		// $checked = in_array('core_update_success', $value) ? 'checked' : '' ;
		// echo '<input type="checkbox" name="sacksonweb_premium_settings_option_name[email_suppress_list][]" value="core_update_success"' . $checked . '/> Suppress Successful Core Update Email <br />';
		// $checked = in_array('core_update_failed', $value) ? 'checked' : '' ;
		// echo '<input type="checkbox" name="sacksonweb_premium_settings_option_name[email_suppress_list][]" value="core_update_failed"' . $checked . '/> Suppress Failed Core Update Emails <span style="color:red;">(not recommended)</span><br />';

	}



}
if ( is_admin() )
	$sacksonweb_premium_settings = new Sacksonweb_Data_Settings();

/* 
 * Retrieve this value with:
 * $sacksonweb_premium_settings_options = get_option( 'sacksonweb_premium_settings_option_name' ); // Array of All Options
 * $email_0 = $sacksonweb_premium_settings_options['email_0']; // Email
 * $refresh_every_0 = $sacksonweb_premium_settings_options['refresh_every_0']; // Refresh every X hours, where X is the number in this field.
 * $allow_1 = $sacksonweb_premium_settings_options['allow_1']; // Allow
 */

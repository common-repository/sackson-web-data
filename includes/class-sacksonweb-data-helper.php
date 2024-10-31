<?php

/**
 * Define the data functionality
 *
 * Loads and defines the class and methods for data checking and collecting
 *
 * @link       http://data.sacksonweb.com/author
 * @since      1.1.5.1
 *
 * @package    Sacksonweb_Data
 * @subpackage Sacksonweb_Data/includes
 */

class Sacksonweb_Data_Helper {

    /**
	 *  
	 *
	 * @since    1.1.5.1
	 * @access   protected
	 * @var      float    $how_many_hours_is_data_stale    The number of hours that we consider the data to be stale.
	 */
	public $hours_data_is_considered_stale = 1;

    /**
     * When is this running so it can be used to compare to
     * @var timestamp unix timestamp
     */
    public $timestamp;

    /**
     * This will represent when the last time we logged data, will also be saved to the database
     * @var timestamp unix timestamp
     */
    public $sacksonweb_last_log;

    /**
     * This holds the data collected
     * @var array An array, potentially multi-dimensional array of all the site data we want to monitor.
     */
    public $collected_data = array();

    /**
     *  
     */
    public $sacksonweb_remote_url = 'https://data.sacksonweb.com/receiver';

    /**
     * 
     */
    public $sacksonweb_connectiontimeout = 50;
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function run($force_collect = false) {

        $sw_option = get_option('sacksonweb_premium_settings_option_name');
        
        /* If the settings provide an interval other than 1 hour, then set the stale variable instead. */
        if ( null !== $sw_option && isset($sw_option['refresh_every_0']) && null !== $sw_option['refresh_every_0'] && .00000000000000000000001 < $sw_option['refresh_every_0'] )
        {
            // Default: $hours_data_is_considered_stale = 1;
            $refresh_every_0 = $sw_option['refresh_every_0']; // Refresh every X hours, where X is the number in this field.
            $this->hours_data_is_considered_stale = $refresh_every_0;
        }
        
        if ( $force_collect || $this->is_time_to_collect_data() ) {
                        
            /* If the user has specified the settings to allow it, then also send_data to remote server. */
            // $sw_option = get_option('sacksonweb_premium_settings_option_name');
            if ( null !== $sw_option && isset($sw_option['allow_1']) && null !== $sw_option['allow_1'] && 'Yes' == $sw_option['allow_1'] )
            {
                $this->collect_data(); // Will populate $this->collected_data
                $this->store_data();
                $this->send_data();
            }
            else 
            {

            }
        }
        else
        {
            
        }
	}

    /**
     * Decide if it's time to collect the data
     */
    public function is_time_to_collect_data (){

        $this->sacksonweb_last_log = get_option('sacksonweb_last_log-' . Sacksonweb_Data_Misc::get_unique_url()); //1657160652

        $this->timestamp = time(); 

        // If the option doesn't exist in the database options table, then set it to now.  
        if ( !$this->sacksonweb_last_log ) {
            $this->sacksonweb_last_log =  $this->timestamp;
            add_option('sacksonweb_last_log-'.Sacksonweb_Data_Misc::get_unique_url(), $this->sacksonweb_last_log);
            return true;
        }

        /* Get the number of seconds between the last time stamp and now */ 
        $seconds_since = abs($this->sacksonweb_last_log - $this->timestamp);
        $minutes_since = $seconds_since / 60;
        $hours_since = $minutes_since / 60;

        if ( $hours_since > $this->hours_data_is_considered_stale ) { 
            // If the last log timestamp is stale, over an hour old (or what its defined to be), then make the remote update call.
            $this->sacksonweb_last_log =  $this->timestamp;
            update_option('sacksonweb_last_log-'.Sacksonweb_Data_Misc::get_unique_url(), $this->sacksonweb_last_log);
           
            return true;
        }
       
        return false;
    } 
    
    /**
     * 
     */
    public function collect_data () 
    {
        // Core variables like version and site url
        $this->collected_data['hs_ver'] = SACKSONWEB_DATA_VERSION;
        $this->collected_data['site_url'] = get_site_url();    // get_site_url, i.e. https://mm.ericandtammy.com
        $this->collected_data['web_key'] = Sacksonweb_Data_Misc::get_unique_url();

        // Server info
        $this->collected_data['HTTP_REFERER'] = isset($_SERVER['HTTP_REFERER']) ? '' . Sacksonweb_Data_Misc::sacksonweb_sanitize_validate_escape($_SERVER['HTTP_REFERER'], 'sanitize') : '';
        $this->collected_data['HTTP_HOST'] = isset($_SERVER['HTTP_HOST']) ? '' . Sacksonweb_Data_Misc::sacksonweb_sanitize_validate_escape($_SERVER['HTTP_HOST'], 'sanitize') : '';
        $this->collected_data['current_user_id'] = @get_current_user_id();

        // Environment variables
        $this->collected_data['WP_DEBUG'] = WP_DEBUG ? 'y' : 'n';
        $this->collected_data['WP_DEBUG_LOG'] = WP_DEBUG_LOG ? 'y' : 'n';
        $this->collected_data['WP_DEBUG_DISPLAY'] = WP_DEBUG_DISPLAY ? 'y' : 'n';
        //if ( !defined(FS_METHOD) )
        {
            //define("FS_METHOD", null);
        }
        $this->collected_data['FS_METHOD'] = '';  // defined(FS_METHOD) && (null !== FS_METHOD) ? FS_METHOD : '';
        

        $this->getBlogInfoArray();    // get_bloginfo, i.e. bunch of stuff.
        $this->getOptions();
        $this->getDatabasePluginData();
        $this->getSiteCoreData();
        $this->getFileSystemItems ();
        $this->getWPSMTPLogData();
        $this->getSimpleHistoryLastLoginData();
        $this->getUserData();
        $this->getPluginUpdateData();
    } 
    

    /**
     * 
     */
    public function store_data () {
        if ( get_option('sacksonweb_collected_data-'.Sacksonweb_Data_Misc::get_unique_url() ) )
        {
            update_option('sacksonweb_collected_data-'.Sacksonweb_Data_Misc::get_unique_url(), $this->collected_data);
        }
        else
        {
            add_option('sacksonweb_collected_data-'.Sacksonweb_Data_Misc::get_unique_url(), $this->collected_data);
        }
    }  
    
    /**
     * 
     */
    public function send_data (){
        
        $site_data = array();

        foreach ($this->collected_data as $key => $item) {
            if (is_array($item)) {                
                $site_data[$key] = json_encode($item);
            }
            else
                $site_data[$key] = $item;
        }
    
        // $i = count($site_data);
        // $site_data = array_slice($site_data, - ($i));
        $site_data_serialized = serialize($site_data);
        $site_data_serialized_encoded = base64_encode($site_data_serialized);
        
        $this->setup_post($site_data_serialized_encoded);
    } 

    /**
     *  
     */
    function setup_post($site_data_serialized_encoded)
    {
        $post_fields = array(
            'key' => 'rrr',
            'calling_site' => Sacksonweb_Data_Misc::get_unique_url(),
            'ssd' => urlencode($site_data_serialized_encoded),
        );
        $wpremotereturn_value = $this->sendData_viaPOST ($post_fields);
    }

    /**
     * 
     */
    function sendData_viaPOST ($post_fields)
    {
        $sackwonweb_sedData_args = array(
            'body'        => $post_fields,
            'timeout'     => $this->sacksonweb_connectiontimeout,
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'cookies'     => array(),
        );
         
        return wp_remote_post( $this->sacksonweb_remote_url, $sackwonweb_sedData_args );
    }


    /**
     * There should be a possibility to return the array of all available info. Although this might not be a good practice in terms of performance this should help someone who needs to fetch all info about a website. Note that I’ve omitted some fields that might not be that important:
     */
    private function getBlogInfoArray()
    {
        $fields = array(
            'name', 'description', 'blogname', 'wpurl', 'url', 'pingback_url', 'admin_email',
            'charset', 'version', 'html_type', 'language',
        );

        foreach ($fields as $field) {
            $this->collected_data[$field] = get_bloginfo($field);
        }
    }

    /**
     * 
     */
    function getFileSystemItems ()
    {
        $this->collected_data['debug_log_exists'] = $this->doesDebugLogExist ();
        $this->collected_data['htaccess_file_exists'] = $this->doesHtaccessFileExist ();
        $this->collected_data['user_ini_file_exists'] = $this->doesUserIniExist ();
        if ( 'y' == $this->collected_data['user_ini_file_exists'] )
        {
            $this->collected_data['user_ini_file_contents'] = $this->getUserIniContents ();
        }
    }
 
    /**
     * 
     */
    function getUserIniContents ()
    {
        $user_ini_file_location = ABSPATH  . '/.user.ini';
        return file_get_contents($user_ini_file_location);
    }

    /**
     * 
     */
    function doesUserIniExist ()
    {
        $user_ini_file_location = ABSPATH  . '/.user.ini';
        return file_exists($user_ini_file_location) ? 'y' : 'n';
    }

    /**
     * 
     */
    function doesDebugLogExist ()
    {
        $debug_file_location = WP_CONTENT_DIR . '/debug.log';
        return file_exists($debug_file_location) ? 'y' : 'n';
    }

     /**
     * 
     */
    function doesHtaccessFileExist ()
    {
        $debug_file_location = ABSPATH  . '/.htaccess';
        return file_exists($debug_file_location) ? 'y' : 'n';
    }

         /**
     * 
     */
    function doesUserIniFileExist ()
    {
        $debug_file_location = ABSPATH  . '/.user.ini';
        return file_exists($debug_file_location) ? 'y' : 'n';
    }



    /**
     * 
     */
    function getOptions()
    {
        $fields = array(
            'admin_email', 'template', 'users_can_register', 'blog_public', 'start_of_week', 'page_on_front',
            'permalink_structure', 'active_plugins', 'stylesheet', 'upload_path', 'upload_url_path', 'uploads_use_yearmonth_folders', 'uninstall_plugins',
            'comment_registration', 'comment_moderation', 'comment_previously_approved', 
            'auto_plugin_theme_update_emails', 'cron', 'auto_update_plugins', 'worker_migration_version', 'recently_activated', 'timezone_string',
            'itsec-storage', 'wp_mail_smtp'
        );

        // if ($this->isWPSMTPPluginActive())
        // {
        //     $fields[] = 'wp_mail_smtp';
        // }

        foreach ($fields as $field) {
            if ( 'itsec-storage' == $field ) {
                $itsec_storage = @get_option($field);
                $this->collected_data['itsec_storage_recaptcha'] = isset($itsec_storage) && is_array($itsec_storage) &&  isset($itsec_storage['recaptcha']) ? $itsec_storage['recaptcha'] : '';
                $this->collected_data['itsec_storage'] = isset($itsec_storage) && is_array($itsec_storage) ? $itsec_storage : '';             
            }
            else {
                $option_data = get_option($field);
                if ( $option_data )
                    $this->collected_data[$field] = $option_data;
            }
        }
    }

    /**
     * 
     */
    function getSiteCoreData()
    {
        $this->collected_data['phpversion'] = phpversion();
    }



    /**
     * 
     */
    function getDatabasePluginData()
    {
        global $wpdb;
        $query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='post' AND post_status='publish'";
        $this->collected_data['post_count'] = $wpdb->get_var($query);

        $files = scandir(WP_PLUGIN_DIR);
        $plugins = '';
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..', 'index.php'))) {
                $plugins .= $file . "|";
            }
        }
        $plugins = rtrim($plugins, '|');
        $this->collected_data['plugins'] = $plugins;
    }

    function debug_to_console($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);
    
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }

    

    
    /**
     * I would like to get information about plugins, specifically version, so trying this approach
     */
    public function getPluginUpdateData()
    {
        if ( is_admin() )
        {
            // Check if get_plugins() function exists. This is required on the front end of the
            // site, since it is in a file that is normally only loaded in the admin.
            if ( ! function_exists( 'get_plugins' ) ) 
            {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $all_plugins = get_plugins();

            $this->collected_data['all_plugins'] = json_encode($all_plugins);
        }
    }

    function getUserData()
    {
        // $DBRecord = array();
        // $args = [
        //     'fields'    => 'all_with_meta',
        //     'orderby' => 'last_name',
        //     'order'   => 'ASC'
        // ];        
        // $users = get_users( $args );
        // return;
        // $i=0;
        // foreach ( $users as $user )
        // {
        //    $DBRecord[$i]['WPId']           = $user->ID;
        //     $DBRecord[$i]['FirstName']      = $user->first_name;
        //     $DBRecord[$i]['LastName']       = $user->last_name;
        //     $DBRecord[$i]['RegisteredDate'] = $user->user_registered;
        //     $DBRecord[$i]['Email']          = $user->user_email;
        //     $DBRecord[$i]['DisplayName']    = $user->display_name;
        //     $DBRecord[$i]['UserNiceName']    = $user->user_nice_name;

        //   $i++;
        // }         
        // $this->collected_data['user_list'] =  json_encode($DBRecord);



        global $wpdb;
        $DBRecord = array();
        $user_table = $wpdb->prefix . 'users';
        $users = $wpdb->get_results("SELECT * FROM $user_table");
        $i=0;
        foreach ($users as $user)
        {
            $DBRecord[$i]['WPId']           = $user->ID;
            // $DBRecord[$i]['FirstName']      = $user->first_name;
            // $DBRecord[$i]['LastName']       = $user->last_name;
            $DBRecord[$i]['RegisteredDate'] = $user->user_registered;
            $DBRecord[$i]['Email']          = $user->user_email;
            $DBRecord[$i]['DisplayName']    = $user->display_name;
            $DBRecord[$i]['UserNiceName']    = $user->user_nicename;
            $i++;
        }
        $this->collected_data['user_list'] =  json_encode($DBRecord);

    }

    /**
     * 
     */
    function doAllTheseDatabaseTablesExist($table_names = array())
    {
        global $wpdb;
        $find_count = 0;
        $number_of_tables_to_find = count($table_names);
        //foreach ($wpdb->tables('all') as $table_name => $table_name_with_prefix)
        foreach ($this->getShowTables() as $table_name => $table_name_with_prefix)
        {
            //echo '<script>alert("table_name: ' . $table_name . ' - w_prefix: ' . $table_name_with_prefix . ' table_names:' . implode(',',$table_names) . '")</script>';        

            if ( in_array($table_name, $table_names))
            {
                $find_count = $find_count + 1;
            }
            if ( in_array($table_name_with_prefix, $table_names))
            {
                $find_count = $find_count + 1;
            }            
        }
        
        return $find_count == $number_of_tables_to_find ? true : false;
    }


    /**
     * The native WP function tables(all) wasn't getting plugin tables, so I'm using this
     */
    function getShowTables()
    {
        global $wpdb;
        $tables_from_show = array();
        $mytables=$wpdb->get_results("SHOW TABLES");
        foreach ($mytables as $mytable)
        {
            foreach ($mytable as $t) 
            {       
                $tables_from_show[] = $t;
            }
        }

        return $tables_from_show;
    }

    /**
     * 
     */
    function isSimpleHistoryPluginActive( )
    {
        foreach ( $this->collected_data['active_plugins'] as $plugin )
        {
            if ( str_contains($plugin,'simple-history'))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * is wp-mail-smtp listed in the site active_plugins
     */
    function isWPSMTPPluginActive( )
    {
        if ( isset($this->collected_data['active_plugins']) )
        {
            foreach ( $this->collected_data['active_plugins'] as $plugin )
            {
                if ( str_contains($plugin,'wp-mail-smtp'))
                {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Like above, but if you knew the name from the active plugins field then you can check for any plugin being active.
     */
    function isThisPluginActive( $plugin_short_name )
    {
        foreach ( $this->collected_data['active_plugins'] as $plugin )
        {
            if ( str_contains($plugin, $plugin_short_name))
            {
                return true;
            }
        }

        return false;
    }

    
    /**
     * Get user log in data - wp_mail_smtp
     */
    function getWPSMTPLogData( )
    {
        // If WP SMTP isn't active don't return any data.
        if ( isset($this->collected_data['active_plugins']) && !$this->isWPSMTPPluginActive()  )
        {
            return;
        }

        global $wpdb;

        $smtp_table = $wpdb->prefix . 'wpmailsmtp_debug_events';
        
        $q = 'SELECT * FROM `' . $smtp_table . '` WHERE `created_at` >= ( NOW() - INTERVAL 3 DAY) ORDER BY `created_at` DESC LIMIT 5;';
        $rows = $wpdb->get_results($q);
        foreach ($rows as $row)
        {
            $last_smtp_logs[] = array($row->content, $row->initiator);
        }
        
        $this->collected_data['wpsmtp_log_data'] = json_encode($last_smtp_logs);
    }


    /**
     * Get user log in data from the simple history table called something_simple_history
     */
    function getSimpleHistoryLastLoginData( )
    {
        // If simple history isn't active don't return any data.
        if ( isset($this->collected_data['active_plugins']) && !$this->isSimpleHistoryPluginActive()  )
        {
            return;
        }

        global $wpdb;

        $simple_history_table = $wpdb->prefix . 'simple_history';
        $simple_history_context_table = $wpdb->prefix . 'simple_history_contexts';

        if ( $this->doAllTheseDatabaseTablesExist(array($simple_history_context_table, $simple_history_table)) )
        {        
            //$this->debug_to_console('they do exist'); 
            // Add hook for admin <head></head>

            //echo '<script>alert("Yes")</script>';
            /*
                FROM `wp_vmck51qx8n_simple_history` sh 
                JOIN wp_vmck51qx8n_simple_history_contexts shc 
            */
            $query = "SELECT sh.date, shc.value 
                        FROM `{$wpdb->prefix}simple_history` sh 
                        JOIN {$wpdb->prefix}simple_history_contexts shc 
                        ON sh.id = shc.history_id 
                        WHERE shc.history_id >= ((SELECT MAX(shc.history_id) FROM {$wpdb->prefix}simple_history) - 10) AND shc.key = 'user_login'
            ";

           // wp_mail('sw@homesnaps.com', 'query sample', "Q:" + $query);

            $rows = $wpdb->get_results($query);
            $last_logins = array();
            foreach ($rows as $row)
            {
                $last_logins[] = array($row->date, $row->value);
            }
            
            $this->collected_data['last_logins'] = json_encode($last_logins);
        }
    }


}
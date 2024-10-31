<?php

/**
 * 
 *
 * @link       http://data.sacksonweb.com/author
 * @since      1.1.0
 *
 * @package    Sacksonweb_Data
 * @subpackage Sacksonweb_Data/includes
 */

/**
 * 
 *
 * This class defines miscellaneous methods that can be helpful in any random locations
 *
 * @since      1.0.0
 * @package    Sacksonweb_Data
 * @subpackage Sacksonweb_Data/includes
 * @author     Eric Thornton <eric@sacksonweb.com>
 */
class Sacksonweb_Data_Misc {

	/**
	 * Get a unique ur from the option siteurl, used as a webkey in many places.
	 *
	 * A unique url, removing all the other parts of the url for a unique string to key the site with. The base input is the siteurl option
	 *
	 * @since    1.0.0
	 */
	public static function get_unique_url() {
        // I use a field name I call webkey to key off, its just the url with no http, no https, and no www.
        // Originally I put it in the AirTable formula as a field, but instead, let's do it in the php. AirTable = REPLACE(REPLACE(siteurl,1,IF(FIND("https",siteurl),8,7),""),1,IF(FIND("www.",siteurl),4,0),"")
        $webkey = trim(get_option('siteurl'));
        $webkey = str_replace('https://', '', $webkey);
        $webkey = str_replace('http://', '', $webkey);
        $webkey = str_replace('www.', '', $webkey);
        $webkey = str_replace('/', '', $webkey);
        return $webkey;
	}

	static function multi_implode($array, $glue) {
		$ret = '';
	
		foreach ($array as $item) {
			if (is_array($item)) {
				$ret .= self::multi_implode($item, $glue) . $glue;
			} else {
				$ret .= $item . $glue;
			}
		}
	
		$ret = substr($ret, 0, 0-strlen($glue));
	
		return $ret;
	}

	static function sacksonweb_debug_to_console($data)
	{

	}

	static function sacksonweb_sanitize_validate_escape ($sacksonweb_item, $sanatize_validate_escape = 'sanitize') {    
		switch ( $sanatize_validate_escape )
		{
			case 'sanitize':
				return sanitize_text_field($sacksonweb_item);
				break;
			case 'validate':
				return ($sacksonweb_item);
				break;
			case 'escape_html':
				return esc_html($sacksonweb_item);
				break;
			case 'escape':
				return esc_attr($sacksonweb_item);
				break;
			default:
				return sanitize_text_field($sacksonweb_item);
				break;
		}   
	}



}
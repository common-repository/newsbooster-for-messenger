<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://chatbooster.pl
 * @since      1.0.0
 *
 * @package    Chatbooster
 * @subpackage Chatbooster/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Chatbooster
 * @subpackage Chatbooster/includes
 * @author     newsBooster <tomasz.zewlakow@chatbooster.pl>
 */
class Chatbooster_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'newsBooster',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

    /**
     * Set the domain equal to that of the specified domain.
     *
     * @since    1.0.0
     * @param    string    $domain    The domain that represents the locale of this plugin.
     */
    public function set_domain( $domain ) {
        $this->domain = $domain;
    }

}

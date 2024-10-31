<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://wordpress.chatbooster.pl
 * @since             1.0.0
 * @package           newsBooster
 *
 * @wordpress-plugin
 * Plugin Name:       newsBooster for Messenger
 * Plugin URI:        https://wordpress.chatbooster.pl
 * Description:       Let your readers subscribe your news and receive it via Messenger.
 * Version:           1.3.1
 * Author:            newsBooster
 * Author URI:        https://wordpress.chatbooster.pl
 * Text Domain:       chatbooster
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-newsBooster-activator.php
 */
function activate_newsBooster() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-newsBooster-activator.php';
	Chatbooster_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-newsBooster-deactivator.php
 */
function deactivate_newsBooster() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-newsBooster-deactivator.php';
	Chatbooster_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_newsBooster' );
register_deactivation_hook( __FILE__, 'deactivate_newsBooster' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-newsBooster.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_newsBooster() {

	$plugin = new Chatbooster();
	$plugin->run();

}
run_newsBooster();

<?php
/*
Plugin Name:    Organik Google Reviews
Description:    Display Google Reviews on your website
Version:        1.0.0
Author:         Organik Web
Author URI:     https://www.organikweb.com.au/
License:        GNU General Public License v2 or later
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Current plugin version
 */
define( 'ORGNK_SERVICES_VERSION', '1.0.0' );

/**
 * Register activation hook
 * This action is documented in inc/class-activator.php
 */
function orgnk_google_reviews_activate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-activator.php';
	Organik_Google_Reviews_Activator::activate();
}
register_activation_hook( __FILE__, 'orgnk_google_reviews_activate_plugin' );

/**
 * Register deactivation hook
 * This action is documented in inc/class-activator.php
 */
function orgnk_google_reviews_deactivate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-activator.php';
	Organik_Google_Reviews_Activator::deactivate();
}
register_deactivation_hook( __FILE__, 'orgnk_google_reviews_deactivate_plugin' );

/*
 * Load dependencies
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/class-google-reviews.php';

/**
 * Load helper functions
 */
require_once plugin_dir_path( __FILE__ ) . 'lib/helpers.php';

/**
 * Run the main instance of this plugin
 */
Organik_Services::instance();

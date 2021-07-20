<?php
/**
 * Define constant variables
 */
define( 'ORGNK_GREVIEWS_API_KEY', esc_html( get_option( 'options_orgnk_greviews_api_key' ) ) );
define( 'ORGNK_GREVIEWS_PLACE_ID', esc_html( get_option( 'options_orgnk_greviews_place_id' ) ) );

/**
 * Main Organik_Google_Reviews class
 */
class Organik_Google_Reviews {

	/**
     * The single instance of Organik_Google_Reviews
     */
	private static $instance = null;

	/**
     * Main class instance
     * Ensures only one instance of this class is loaded or can be loaded
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
        }
        return self::$instance;
	}

	/**
     * Constructor function
     */
	public function __construct() {

		// Hook into the 'init' action to add the admin menu item
		add_action( 'init', array( $this, 'orgnk_greviews_settings_page' ) );

		// Register ACF Fields
		new Organik_Google_Reviews_Populate_ACF();
	}

	/**
	 * orgnk_greviews_settings_page()
	 * Add an ACF settings page for this plugin
	 */
	public function orgnk_greviews_settings_page() {

		if ( function_exists( 'acf_add_options_page' ) ) {

			acf_add_options_page( array(
				'page_title'		=> 'Google Reviews Settings',
				'menu_title'		=> 'Google Reviews',
				'menu_slug'			=> 'google-reviews-settings',
				'capability'		=> 'edit_posts',
				'position'			=> 40,
				'icon_url'			=> 'dashicons-google',
				'redirect'			=> false
			) );
		}
	}
}

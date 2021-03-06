<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       mark-gabinete
 * @since      1.0.0
 *
 * @package    Scientific_Citation
 * @subpackage Scientific_Citation/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Scientific_Citation
 * @subpackage Scientific_Citation/admin
 * @author     Mark Gabinete <markgabinete02@gmail.com>
 */
class Scientific_Citation_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/scientific-citation-admin.css', array(), $this->version, 'all' );
		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/scientific-citation-admin.js', array( 'jquery' ), $this->version, false );

	}

	

	/**
	 * scicit_register_admin_menu
	 *
	 * @return void
	 */
	public function scicit_register_admin_menu(){
		add_menu_page( 'Scientific Citation', 'Citations', 'manage_options', 'scientific-citation/settings.php', array($this, 'scientific_admin_page'), "dashicons-admin-links", 250 );
	}


	/**
	 * scientific_admin_page
	 *
	 * @since	1.0.0
	 * @return scientific citation admin page
	 */
	public function scientific_admin_page() {
		
		require_once 'partials/scientific-citation-admin-display.php';
	}

	
	/**
	 * register_scicit_settings
	 * 
	 * Registers this plugins settings to wordpress
	 * 
	 * @since	1.0.0
	 * @return void
	 */
	public function register_scicit_settings() {
		// Registers all general api settings
		$args = array(
            'type' => 'string', 
            'default' => 'apa',
            );
		register_setting( "scicitationSettings", "citation_style", $args );
	}

}

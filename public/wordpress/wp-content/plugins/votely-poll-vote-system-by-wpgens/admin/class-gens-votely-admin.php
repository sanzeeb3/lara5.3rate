<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Gens_Votely
 * @subpackage Gens_Votely/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Gens_Votely
 * @subpackage Gens_Votely/admin
 * @author     Your Name <email@example.com>
 */
class Gens_Votely_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $gens_votely    The ID of this plugin.
	 */
	private $gens_votely;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	public $plugin_settings_tabs = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $gens_votely       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $gens_votely, $version ) {

		$this->gens_votely = $gens_votely;
		$this->version = $version;

		$this->plugin_settings_tabs['general'] = 'General';
	}

	/**
	 * Register the Settings page.
	 *
	 * @since    1.0.0
	 */
	public function gens_votely_admin_menu() {

		 add_options_page( __('WPGens Votely', $this->gens_votely), __('WPGens Votely', $this->gens_votely), 'manage_options', $this->gens_votely, array($this, 'display_plugin_admin_page'));

	}

	/**
	 * Settings - Validates saved options
	 *
	 * @since 		1.0.0
	 * @param 		array 		$input 			array of submitted plugin options
	 * @return 		array 						array of validated plugin options
	 */
	public function settings_sanitize( $input ) {

		// Initialize the new array that will hold the sanitize values
		$new_input = array();

		if(isset($input)) {
			// Loop through the input and sanitize each of the values
			foreach ( $input as $key => $val ) {

				if($key == 'security-layer') { // dont sanitize array
					$new_input[ $key ] = $val;
				} else {
					$new_input[ $key ] = sanitize_text_field( $val );
				}
				
			}

		}

		return $new_input;

	} // sanitize()

	
	/**
	 * Renders Settings Tabs
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	function gens_votely_render_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->gens_votely . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}

	/**
	 * Plugin Settings Link on plugin page
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	function add_settings_link( $links ) {

		$mylinks = array(
			'<a href="' . admin_url( 'options-general.php?page=gens-votely' ) . '">Settings</a>',
		);
		return array_merge( $links, $mylinks );
	}


	/**
	 * Callback function for the admin settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page(){	

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/gens-votely-admin-display.php';
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

		if ( 'settings_page_gens-votely' != $hook ) {
        return;
    	}
	    wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_script( $this->gens_votely, plugin_dir_url( __FILE__ ) . 'js/gens-votely-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {

		if ( 'settings_page_gens-votely' != $hook && 'post.php' != $hook && 'post-new.php' != $hook ) {
        return;
    	}

        wp_enqueue_style( 'wp-color-picker' ); 
		wp_enqueue_style( $this->gens_votely, plugin_dir_url( __FILE__ ) . 'css/gens-votely-admin.css', array(), $this->version, 'all' );

	}


}

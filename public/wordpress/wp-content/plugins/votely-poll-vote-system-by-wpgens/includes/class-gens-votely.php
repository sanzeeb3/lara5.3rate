<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Gens_Votely
 * @subpackage Gens_Votely/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Gens_Votely
 * @subpackage Gens_Votely/includes
 * @author     Your Name <email@example.com>
 */
class Gens_Votely {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Gens_Votely_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $gens_votely    The string used to uniquely identify this plugin.
	 */
	protected $gens_votely;

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
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->gens_votely = 'gens-votely';
		$this->version = '0.9.5';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_post_metas();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Gens_Votely_Loader. Orchestrates the hooks of the plugin.
	 * - Gens_Votely_i18n. Defines internationalization functionality.
	 * - Gens_Votely_Admin. Defines all hooks for the dashboard.
	 * - Gens_Votely_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gens-votely-loader.php';
		
		/**
		 * The class responsible for adding Votely Post Metas.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/meta-box-class/meta_box.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gens-votely-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gens-votely-admin.php';

		/**
		 * The class responsible for defining all Settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/class-gens-votely-settings.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-gens-votely-public.php';

		$this->loader = new Gens_Votely_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Gens_Votely_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Gens_Votely_i18n();
		$plugin_i18n->set_domain( $this->get_gens_votely() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Gens_Votely_Admin( $this->get_gens_votely(), $this->get_version() );
		$settings_init_general = new Gens_Votely_Settings( $this->get_gens_votely() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'gens_votely_admin_menu' );
		$this->loader->add_action( 'admin_init', $settings_init_general, 'settings_api_init' );
		$this->loader->add_filter( 'plugin_action_links_wpgens-votely/gens-votely.php', $plugin_admin, 'add_settings_link' );
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

	}

	private function define_post_metas() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/meta-box-class/post_metas.php';
		$post_meta = new Votely_Add_Meta_Box( 'post_meta', 'VOTELY BY WPGENS - ENGAGE USERS', $fields, array('post'), true, 'normal' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Gens_Votely_Public( $this->get_gens_votely(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'insert_poll',45 );

		// Ajax Update Votes
		$this->loader->add_action('wp_ajax_nopriv_update_votes', $plugin_public, 'update_votes');
		$this->loader->add_action('wp_ajax_update_votes', $plugin_public, 'update_votes');

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
	public function get_gens_votely() {
		return $this->gens_votely;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Gens_Votely_Loader    Orchestrates the hooks of the plugin.
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

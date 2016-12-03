<?php

/**
 *
 * @link              http://wpgens.com
 * @since             1.0.0
 * @package           Gens_Votely
 *
 * @wordpress-plugin
 * Plugin Name:       Votely by WPGens
 * Plugin URI:        http://example.com/gens-votely-uri/
 * Description:       Simple and powerful vote/poll plugin that can be added at the end of each post.
 * Version:           0.9.5
 * Author:            Goran Jakovljevic | WPGens
 * Author URI:        http://wpgens.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gens-votely
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gens-votely-activator.php
 */
function activate_gens_votely() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gens-votely-activator.php';
	Gens_Votely_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gens-votely-deactivator.php
 */
function deactivate_gens_votely() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gens-votely-deactivator.php';
	Gens_Votely_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gens_votely' );
register_deactivation_hook( __FILE__, 'deactivate_gens_votely' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gens-votely.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gens_votely() {

	$plugin = new Gens_Votely();
	$plugin->run();

}
run_gens_votely();

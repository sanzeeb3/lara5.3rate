<?php
/* 
 * Plugin Name: Wpdevart Forms
 * Plugin URI: http://wpdevart.com/wordpress-contact-form-plugin/
 * Description: Contact Form plugin is an nice and handy tool for creating different types of contact forms on your WordPress websites. Use our WordPress Contact Form builder plugin for building intutive and functional contact us pages. 
 * Version: 1.0.2
 * Author: wpdevart
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/


//plugin version
if ( ! defined( 'ABSPATH' ) ) exit;

define('wpda_form_PLUGIN_VERSION', "1.0.0"); 

//	define secific directories paths to include .php files
//	returns full path of current file if used in any file
define('wpda_form_CUR_FILE', __FILE__ ); 

//	define our plugin's directory i.e. wp-content/plugins/wpdevart-forms
define('wpda_form_PLUGIN_DIR', untrailingslashit( dirname( wpda_form_CUR_FILE ) ) );

//	define absolute paths to specific directories to include stylesheets and scripts
define('wpda_form_PLUGIN_URI', plugin_dir_url(wpda_form_CUR_FILE) ); 

//	returns settings.ini file
define('wpda_form_FILE_INI', wpda_form_PLUGIN_DIR . '/settings.ini');
//	delimeter
define('wpda_form_DELMITER', ',');
// new line
define('wpda_form_NEW_LINE', "\r\n");

//	wordrpress built-in file to call for accessing wp_get_current_user() etc
require_once ABSPATH .'wp-includes/pluggable.php';

//	it contains all important functions
require_once wpda_form_PLUGIN_DIR . '/functions.php';

//	it contains tables structure creations and database interations etc
require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/db-tables.php';

//	handles ajax calls for deleting forms, fields, sub-fields etc
require_once wpda_form_PLUGIN_DIR . '/ajax/wpdevart-ajax-handling.php';

//	this file will be used to show created form at front-end which shows form structure, styling/formatting etc
require_once wpda_form_PLUGIN_DIR . '/frontend/html-template.php';

//	this file will be used to show specific form submissions at frontend
require_once wpda_form_PLUGIN_DIR . '/frontend/form-submissions-template.php';

//	after form submit at front-end, this file handles behind the scene
require_once wpda_form_PLUGIN_DIR . '/frontend/email-submits.php';

//	core.php file contains plugin class and all relevant other included files
require_once wpda_form_PLUGIN_DIR.'/classes/core.php';

//	core.php file contains plugin class and all relevant other included files
require_once wpda_form_PLUGIN_DIR.'/classes/forms-widget.php';

//	tinybox.php file adds forms list to wordpress editor
require_once wpda_form_PLUGIN_DIR.'/classes/tinybox.php';

//	create class object to run constructor of class
$wpdevart_forms_obj = new wpdevartForms();
?>
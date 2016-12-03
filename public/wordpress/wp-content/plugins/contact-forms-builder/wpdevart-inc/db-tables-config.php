<?php 
/**
 * we're going to use $wpda_form_table as global array for our db interactions
 * it is declared as global because local variable will not be 
 * accessible as activation hook to run properly
 *
 * @package wpdevart Forms
 * @since	1.0
 */
 if ( ! defined( 'ABSPATH' ) ) exit;
// wordpress database's global object
global $wpdb;

//	plugin's database table's info
global $wpda_form_table; 

//	Represents forms individually
$wpda_form_table['wpdevart_forms'] = $wpdb->prefix.'wpda_form_forms';

//	Represents form fields
$wpda_form_table['fields'] = $wpdb->prefix.'wpda_form_fields';

//	Represents form fields's subfields
$wpda_form_table['subfields'] = $wpdb->prefix.'wpda_form_subfields';

//	Submission time of the form
$wpda_form_table['submit_time'] = $wpdb->prefix.'wpda_form_submit_time';

//	contains form submitted values
$wpda_form_table['submissions'] = $wpdb->prefix.'wpda_form_submissions';

?>
<?php 

/**
 * Create tables if not already exist on plugin activation
 * run all required hooks whenever needed
 *
 * @package wpdevart Forms
 * @since	1.0
 */
 if ( ! defined( 'ABSPATH' ) ) exit;

 
global $wpdb;

//	contains database tables info required for creating database tables
require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/db-tables-config.php';

//	as plugin is activated, following function checks for plugins database tables
register_activation_hook( wpda_form_CUR_FILE, 'wpda_form_db_tables' );

//	creates tables for plugins's database if not exists on plugin activation
function wpda_form_db_tables()
{
	$wp_upload_dir = wp_upload_dir();
	$wpdevart_forms_uploads_dir  = wpda_form_uploads_dir();	
	//	create folder of for wpdevart-forms-attachments, index.html file
	if (!file_exists($wp_upload_dir['basedir'].$wpdevart_forms_uploads_dir)) {
		mkdir($wp_upload_dir['basedir'].$wpdevart_forms_uploads_dir);
	}
	 // defined in functions file
	$index_file = "index.html"; // or .php   
	$fh = fopen($wp_upload_dir['basedir'].'/'.$wpdevart_forms_uploads_dir.'/'.$index_file, 'w'); // or die("error");  
	$stringData = "//Silence is golden";   
	fwrite($fh, $stringData);
	
	
	global $wpdb; 
	global $wpda_form_table; 
	
	if($wpdb->get_var("show tables like '". $wpda_form_table['wpdevart_forms']."'") !=  $wpda_form_table['wpdevart_forms']) {
		$sql = "CREATE TABLE IF NOT EXISTS ". $wpda_form_table['wpdevart_forms']." 
			  (
			  id int(9) NOT NULL AUTO_INCREMENT ,
			  name varchar(255) CHARACTER SET utf8 collate utf8_general_ci,
			  params mediumtext CHARACTER SET utf8 collate utf8_general_ci,
			  datetime varchar(255) NOT NULL,
			  PRIMARY KEY (id)
			  )
			  ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	  
	}  
	if($wpdb->get_var("show tables like '". $wpda_form_table['fields']."'") != $wpda_form_table['fields']) {
		$sql = "CREATE TABLE IF NOT EXISTS ".$wpda_form_table['fields']."
			  (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  label varchar(255)  CHARACTER SET utf8 collate utf8_general_ci,
			  fieldtype varchar(255) CHARACTER SET utf8 collate utf8_general_ci,
			  placeholder varchar(255) CHARACTER SET utf8 collate utf8_general_ci,
			  is_required int(1) NOT NULL,
			  fk_form_id int(9) DEFAULT NULL,
			  position int(9) NOT NULL,
			  PRIMARY KEY (id),
			  CONSTRAINT ".$wpda_form_table['fields']."_ibfk_1 FOREIGN KEY (fk_form_id) REFERENCES 
			  ".$wpda_form_table['wpdevart_forms']." (id) ON DELETE CASCADE ON UPDATE CASCADE
			  )
			   ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($sql);
	}
	
	if($wpdb->get_var("show tables like '".$wpda_form_table['subfields']."'") != $wpda_form_table['subfields']) {
		$sql = "CREATE TABLE IF NOT EXISTS ".$wpda_form_table['subfields']." 
			 (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  fk_form_id int(9) NOT NULL ,
			  fk_field_id int(9) NOT NULL,
			  label varchar(255) CHARACTER SET utf8 collate utf8_general_ci,
			  selected_value varchar(255) NOT NULL,
			   PRIMARY KEY (id),
			   KEY fk_form_id (fk_form_id),
			   KEY fk_field_id (fk_field_id),
			   CONSTRAINT ".$wpda_form_table['subfields']."_ibfk_1 FOREIGN KEY (fk_form_id) REFERENCES ".$wpda_form_table['wpdevart_forms']." (id) ON DELETE CASCADE ON UPDATE CASCADE,
			   CONSTRAINT ".$wpda_form_table['subfields']."_ibfk_2 FOREIGN KEY (fk_field_id) REFERENCES ".$wpda_form_table['fields']." (id) ON DELETE CASCADE ON UPDATE CASCADE
			  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($sql);
	}
	
	if($wpdb->get_var("show tables like '".$wpda_form_table['submit_time']."'") != $wpda_form_table['submit_time']) {
		$sql = "CREATE TABLE IF NOT EXISTS ".$wpda_form_table['submit_time']." 
			 (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  fk_form_id int(9) NOT NULL ,
			  submit_time decimal(16,4) NOT NULL,
			  PRIMARY KEY (id),
			  CONSTRAINT ".$wpda_form_table['submit_time']."_ibfk_1 FOREIGN KEY (fk_form_id) REFERENCES ".$wpda_form_table['wpdevart_forms']." (id) ON DELETE CASCADE ON UPDATE CASCADE
			 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  dbDelta($sql);
	}
	
	if($wpdb->get_var("show tables like '". $wpda_form_table['submissions']."'") != $wpda_form_table['submissions']) {
		$sql = "CREATE TABLE IF NOT EXISTS ".$wpda_form_table['submissions']."
			  (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  fk_submit_time_id int(9) NOT NULL,
			  fk_field_id int(9) NOT NULL,
			  field_value longtext CHARACTER SET utf8 collate utf8_general_ci,
			  PRIMARY KEY (id),
			  CONSTRAINT ".$wpda_form_table['submissions']."_ibfk_1 FOREIGN KEY (fk_submit_time_id) REFERENCES ".$wpda_form_table['submit_time']." (id) ON DELETE CASCADE ON UPDATE CASCADE,
			  CONSTRAINT ".$wpda_form_table['submissions']."_ibfk_2 FOREIGN KEY (fk_field_id) REFERENCES ".$wpda_form_table['fields']." (id) ON DELETE CASCADE ON UPDATE CASCADE 
			  )
			  ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  dbDelta($sql);
	}
}// wpda_form_db_tables()
?>
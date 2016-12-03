<?php
/*
 *	Adds wpdevart forms dropdown list in wp_editor 
 *
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wpda_form_Tinybox {
	
    public function __construct(){
		add_action('admin_head', array('wpda_form_Tinybox', 'wpda_form_print_shortcodes_in_js'));
		add_action('admin_head', array('wpda_form_Tinybox', 'wpda_form_add_tinymce'));
    }
	public static function wpda_form_print_shortcodes_in_js() {
		global $wpdb;
		global $wpda_form_table;
		$forms_list = $wpdb->get_results("SELECT * FROM ".$wpda_form_table['wpdevart_forms']);
		if($forms_list) {
			$shortcodes = '';
			$first = true;
			foreach($forms_list as $form) {
				if(!$first) $shortcodes .= ',';
				$shortcodes .= "'[wpdevart_forms id=$form->id]'";
				$first = false;
			}
			//$shortcodes = "'[wpdevart_forms id=1]', '[wpdevart_forms slider id=2]', '[wpdevart_forms slider id=3]'";
			?>
			<script type="text/javascript">
				var wpdevartforms_shortcodes = [<?php echo $shortcodes; ?>];
			</script>
			<?php
		}
	}
	
	public static function wpda_form_add_tinymce() {
		add_filter('mce_external_plugins', array('wpda_form_Tinybox', 'wpda_form_add_tinymce_plugin'));
		add_filter('mce_buttons', array('wpda_form_Tinybox', 'wpda_form_btn_add_tinymce'));
	}
	 
	public static function wpda_form_add_tinymce_plugin($plugin_array) {
		
		//	Add tinybox scripts according to version of wordpress
		$version = get_bloginfo('version'); 
		if($version < 3.9)
			$plugin_array['wpdevartforms'] = plugins_url('assets/js/wp-editor-js/tbld.min.js', wpda_form_CUR_FILE);
		else
			$plugin_array['wpdevartforms'] = plugins_url('assets/js/wp-editor-js/tbld-3.9.min.js', wpda_form_CUR_FILE);
			
			
		if($version<3.9) {
			$plugin_array['wpdevartforms'] = plugins_url('assets/js/wp-editor-js/tbld.min.js', wpda_form_CUR_FILE);
		} elseif($version<4.3) {
			$plugin_array['wpdevartforms'] = plugins_url('assets/js/wp-editor-js/tbld-3.9.min.js', wpda_form_CUR_FILE);
		} else {
			$plugin_array['wpdevartforms'] = plugins_url('assets/js/wp-editor-js/tbld-4.3.min.js', wpda_form_CUR_FILE);
		}
			
		return $plugin_array;
	}
	 
	public static function wpda_form_btn_add_tinymce($buttons) {
		array_push($buttons, 'wpdevartforms');
		return $buttons;
	}
}
//	wpda_form_Tinybox class's functionality is been commented, to use it please uncomment the follwoing object 
//$obj = new wpda_form_Tinybox();
?>
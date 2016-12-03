<?php
/**
 * All important functions are included here which will be used
 * throughout the project
 *
 * front end and backend scripts and stylesheets are also loaded
 * also generates dynamic css for relevant forms
 *
 * @package wpdevart Forms
 * @since	1.0
 */

//	enable widget supports for wpdevart forms
if ( ! defined( 'ABSPATH' ) ) exit;

add_filter('widget_text', 'do_shortcode');

// add frontend scripts 
function wpda_form_enqueue_frontend_styles_scripts($args=NULL) {
	
	wp_enqueue_style( 'wf-font-awesome', wpda_form_PLUGIN_URI . 'assets/css/font-awesome.min.css');	
	wp_enqueue_style( 'wf-datepicker', wpda_form_PLUGIN_URI . 'assets/css/jquery.datetimepicker.min.css');
	wp_enqueue_style( 'wf-frontend-forms', wpda_form_PLUGIN_URI . 'frontend/css/wpdevart-forms.css');
	   if(!empty($args)) {
		wp_enqueue_style( 'wf-'.$args['frontend_template'].'-skin', wpda_form_PLUGIN_URI . 'frontend/skins/'.$args['frontend_template'].'.css');
	}
	
    
	$explode = explode('_',$args['frontend_template']);
	if( (isset($explode[1]) && $explode[1]=='inline') ) {
		wp_enqueue_style( 'wf-frontend-forms_inline', wpda_form_PLUGIN_URI . 'frontend/css/wpdevart-forms_inline.css');
	}
	
	if( wp_get_theme() == 'wpdevart' || wp_get_theme() == 'wpdevart Child' || wp_get_theme() == 'wpdevart Theme' || wp_get_theme() == 'wpdevart Child Theme' ) {
		//	don't add files because wpdevart Theme already has loaded jquery and bootstrap
	} else {
		wp_enqueue_script('jquery', null, '1.0', true);
	}
	
	wp_enqueue_script('wf-bootstrap-datepicker', wpda_form_PLUGIN_URI . 'assets/js/jquery.datetimepicker.min.js', null, '1.0', true );
	wp_enqueue_script('wf-formhelpers', wpda_form_PLUGIN_URI . 'frontend/js/bootstrap-formhelpers.min.js', null, '1.0', true );
	wp_enqueue_script('wf-frontend-forms', wpda_form_PLUGIN_URI . 'frontend/js/wpdevart-forms.js', null, '1.0', true );
	wp_enqueue_script('wf-jquery-form', wpda_form_PLUGIN_URI . 'frontend/js/jquery-form-js.min.js', null, '1.0', true );
	
  }

// add frontend scripts 
function wpda_form_enqueue_frontend_styles_scripts_submissions($args=NULL) {
	wp_enqueue_style( 'wf-popup-css', wpda_form_PLUGIN_URI . 'frontend/css/popup.css');	
	wp_enqueue_script('wf-popup-js', wpda_form_PLUGIN_URI . 'frontend/js/popup.js', null, '1.0', true );
	wp_localize_script( 'ajax-script', 'my_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}


//	add form shortcode anywhere to display form at front-end.
add_shortcode( 'wpdevart_forms', 'wpda_form_forms_shortcode' );
function wpda_form_forms_shortcode( $atts )  {
	ob_start();
	
	//	if a form at front-end has been submitted, check if email
	//	sent successfully or not. function is inside wpdevart-email-submits.php
	$email_success = wpda_form_email_submit($atts);
	if($email_success) {
		echo $email_success;
	} else {
		//	call this function as soon a shortcode runs on any page. 
		//	it'll show front-end form. function is inside frontend/html-template.php
		wpdevart_template($atts); 
	}
	$response = ob_get_clean();
	return $response;
}

//	add submission shortcode anywhere to display form submission at frontend
add_shortcode( 'wpdevart_forms_submissions', 'wpda_form_submissions_shortcode' );
function wpda_form_submissions_shortcode( $atts )  {
	ob_start();
	wpdevart_form_submissions_template($atts);
	return $response = ob_get_clean();
}

/*
 * 	This function checks whether a form field has subfields or not,
 * 	if fields's subfields found return founded subfields,
 * 	i.e. has Gender field have subfields of male and female
*/
function wpda_form_has_subfield($form_field, $form_id) {
	global $wpdb;
	global $wpda_form_table;
	$field_subfields = $wpdb->get_results( $wpdb->prepare("SELECT * FROM " .$wpda_form_table['subfields'] ." WHERE fk_form_id = %d AND fk_field_id = %d ",$form_id,$form_field->id));
	if(!empty($field_subfields)) {
		return $field_subfields;
	} else {
		return false;
	}
}
//	search if styling exists for a form and return if it has
function wpda_form_search_form_styling($array, $key, $value) {
	$results = array();
	if (is_array($array)) {
		if (isset($array[$key]) && $array[$key] == $value){
			$results[] = $array;
		}
		foreach ($array as $subarray) {
			$results = array_merge($results, wpda_form_search_form_styling($subarray, $key, $value));
		}
	}
	return $results;
}
 
//	check if styling exists against any form
function wpda_form_form_styling_exists($form_id, $wp_option) {
	if( array_key_exists($form_id, $wp_option) ){
		return true;
	} else {
		return false;
	}
}
 
//	prints custom styles set from styling tab of admin
function wpda_form_forms_custom_styles() {
	$wpda_form_styles = ''; // we'll store all styles in this variable and print at the end.
	
	$get_styles = get_option('wpdevart_forms_style');
	if (!empty($get_styles)) 
	{
		foreach($get_styles as $key => $style)  {
			//	if styling disabled don't add styling to frontend 
			if(isset($style['enable_disable_form_style']) && $style['enable_disable_form_style'] == 1){
				continue;
			}
			
			//	forms area / container
			$output = '';			
			if( isset($style['wpda_form_background_color']) && $style['wpda_form_background_color']!='') $output .= 'background-color:' .$style['wpda_form_background_color'] .';';
			if( isset($style['wpda_form_width']) && $style['wpda_form_width']!='') $output .= 'width:' .esc_html($style['wpda_form_width']) .';';
			if( isset($style['wpda_form_top_border_thickness']) && $style['wpda_form_top_border_thickness']!='') $output .= 'border-top:' .wpda_form_to_pixel($style['wpda_form_top_border_thickness']) .' ' .$style['wpda_form_top_border_style'] .' ' .$style['wpda_form_top_border_color'].' ;';
			if( isset($style['wpda_form_bottom_border_thickness']) && $style['wpda_form_bottom_border_thickness']!='') $output .= 'border-bottom:' .wpda_form_to_pixel($style['wpda_form_bottom_border_thickness']) .' ' .$style['wpda_form_bottom_border_style'] .' ' .$style['wpda_form_bottom_border_color'].' ;';
			if( isset($style['wpda_form_right_border_thickness']) && $style['wpda_form_right_border_thickness']!='') $output .= 'border-right:' .wpda_form_to_pixel($style['wpda_form_right_border_thickness']) .' ' .$style['wpda_form_right_border_style'] .' ' .$style['wpda_form_right_border_color'].' ;';
			if( isset($style['wpda_form_left_border_thickness']) && $style['wpda_form_left_border_thickness']!='') $output .= 'border-left:' .wpda_form_to_pixel($style['wpda_form_left_border_thickness']) .' ' .$style['wpda_form_left_border_style'] .' ' .$style['wpda_form_left_border_color'].' ;';
			if( isset($style['wpda_form_top_margin']) && $style['wpda_form_top_margin']!='') $output .= 'margin-top:' .wpda_form_to_mix($style['wpda_form_top_margin']).';';
			if( isset($style['wpda_form_bottom_margin']) && $style['wpda_form_bottom_margin']!='') $output .= 'margin-bottom:' .wpda_form_to_mix($style['wpda_form_bottom_margin']).';';
			if( isset($style['wpda_form_left_margin']) && $style['wpda_form_left_margin']!='') $output .= 'margin-left:' .wpda_form_to_mix($style['wpda_form_left_margin']).';';
			if( isset($style['wpda_form_right_margin']) && $style['wpda_form_right_margin']!='') $output .= 'margin-right:' .wpda_form_to_mix($style['wpda_form_right_margin']).';';
			if( isset($style['wpda_form_top_padding']) && $style['wpda_form_top_padding']!='') $output .= 'padding-top:' .wpda_form_to_pixel($style['wpda_form_top_padding']).';';
			if( isset($style['wpda_form_bottom_padding']) && $style['wpda_form_bottom_padding']!='') $output .= 'padding-bottom:' .wpda_form_to_pixel($style['wpda_form_bottom_padding']).';';
			if( isset($style['wpda_form_left_padding']) && $style['wpda_form_left_padding']!='') $output .= 'padding-left:' .wpda_form_to_pixel($style['wpda_form_left_padding']).';';
			if( isset($style['wpda_form_right_padding']) && $style['wpda_form_right_padding']!='')	 $output .= 'padding-right:' .wpda_form_to_pixel($style['wpda_form_right_padding']).';';
			if($output) {
				$wpda_form_styles .= '#wpdevart-forms-'.$key.'{' .$output .'}';
			}
			
			//	form fields
			$output = '';
			if( isset($style['wpda_form_field_height']) && $style['wpda_form_field_height']!='') $output .= 'height:' .$style['wpda_form_field_height'] .';'; 
			if( isset($style['wpda_form_field_background_color']) && $style['wpda_form_field_background_color']!='') $output .= 'background-color:' .$style['wpda_form_field_background_color'] .' !important;'; 
			if( isset($style['wpda_form_field_top_border_thickness']) && $style['wpda_form_field_top_border_thickness']!='') $output .= 'border-top:' .wpda_form_to_pixel($style['wpda_form_field_top_border_thickness']).' '.$style['wpda_form_field_top_border_style'].' '.$style['wpda_form_field_top_border_color'].' !important;';
			if( isset($style['wpda_form_field_bottom_border_thickness']) && $style['wpda_form_field_bottom_border_thickness']!='') $output .= 'border-bottom:' .wpda_form_to_pixel($style['wpda_form_field_bottom_border_thickness']).' '.$style['wpda_form_field_bottom_border_style'].' '.	$style['wpda_form_field_bottom_border_color'].'!important ;';
			if( isset($style['wpda_form_field_right_border_thickness']) && $style['wpda_form_field_right_border_thickness']!='') 	$output .= 'border-right:' .wpda_form_to_pixel($style['wpda_form_field_right_border_thickness']).' '.$style['wpda_form_field_right_border_style'].' '.$style['wpda_form_field_right_border_color'].' !important;';
			if( isset($style['wpda_form_field_left_border_thickness']) && $style['wpda_form_field_left_border_thickness']!='') $output .= 'border-left:' .wpda_form_to_pixel($style['wpda_form_field_left_border_thickness']).' '.$style['wpda_form_field_left_border_style'].' '.$style['wpda_form_field_left_border_color'].' !important;';
			
			if( isset($style['wpda_form_field_left_margin']) && $style['wpda_form_field_left_margin']!='') $output .= 'margin-left:' .wpda_form_to_mix($style['wpda_form_field_left_margin']).';';
			if( isset($style['wpda_form_field_right_margin']) && $style['wpda_form_field_right_margin']!='') $output .= 'margin-right:' .wpda_form_to_mix($style['wpda_form_field_right_margin']).';';
			// margins, top bottom missing
			if( isset($style['wpda_form_field_top_padding']) && $style['wpda_form_field_top_padding']!='') $output .= 'padding-top:' .wpda_form_to_pixel($style['wpda_form_field_top_padding']).';';
			if( isset($style['wpda_form_field_bottom_padding']) && $style['wpda_form_field_bottom_padding']!='') $output .= 'padding-bottom:' .wpda_form_to_pixel($style['wpda_form_field_bottom_padding']).';';
			if( isset($style['wpda_form_field_left_padding']) && $style['wpda_form_field_left_padding']!='') $output .= 'padding-left:' .wpda_form_to_pixel($style['wpda_form_field_left_padding']).';';
			if( isset($style['wpda_form_field_right_padding']) && $style['wpda_form_field_right_padding']!='') $output .= 'padding-right:' .wpda_form_to_pixel($style['wpda_form_field_right_padding']).';';
			if( isset($style['wpda_form_field_font_size']) && $style['wpda_form_field_font_size']!='') $output .= 'font-size:' .wpda_form_to_pixel($style['wpda_form_field_font_size']).';';
			if( isset($style['wpda_form_field_font_family']) && $style['wpda_form_field_font_family']!='') $output .= 'font-family:' . $style['wpda_form_field_font_family'].';';
			if( isset($style['wpda_form_field_font_weight']) && $style['wpda_form_field_font_weight']!='') $output .= wpda_form_get_font_styles_css($style['wpda_form_field_font_weight']);
			if( isset($style['wpda_form_field_font_color']) && $style['wpda_form_field_font_color']!='') $output .= 'color:' .$style['wpda_form_field_font_color'].'!important;';
			if( isset($style['wpda_form_field_line_height']) && $style['wpda_form_field_line_height']!='') $output .= 'line-height:' .wpda_form_to_pixel($style['wpda_form_field_line_height']).';';
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' input[type="text"],#wpdevart-forms-'.$key.' input[type="number"],#wpdevart-forms-'.$key.' input[type="email"],#wpdevart-forms-'.$key.' input[type="password"],#wpdevart-forms-'.$key.' input[type="url"],#wpdevart-forms-'.$key.' .wpdevart-select,#wpdevart-forms-'.$key.' .btn.selectpicker,#wpdevart-forms-'.$key.' textarea ' .'{' .$output .'}';
			}
			
            $output = '';
            if( isset($style['wpda_form_subfields_display_type']) && $style['wpda_form_subfields_display_type']!='') $output .= 'display:block;';
            if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' .wpdevart-sub-fields-inner .checkboxradios,#wpdevart-forms-'.$key.' .wpdevart-sub-fields-inner .checkboxradios' .'{' .$output .'}';
			}
            
			$output = '';
			if( isset($style['wpda_form_field_background_color']) && $style['wpda_form_field_background_color']!='') $output .= 'background-color:'.wpda_form_hex2rgb($style["wpda_form_field_background_color"],"0.8").' !important;';
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' .wpdevart-select'.' { padding:0px;width:100%;}';
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' .wpdevart-select select' .'{'. $output .'}';
				
			}
			
			$output = '';
			if( isset($style['wpda_form_field_top_margin']) && $style['wpda_form_field_top_margin']!='') $output .= 'margin-top:' .wpda_form_to_mix($style['wpda_form_field_top_margin']).';';
			if( isset($style['wpda_form_field_bottom_margin']) &&  $style['wpda_form_field_bottom_margin']!='') $output .= 'margin-bottom:' .wpda_form_to_mix($style['wpda_form_field_bottom_margin']).';';
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' .wpdevart-input-field ' .'{' .$output .'}';
			}
			
			
			$output = '';
			if( isset($style['wpda_form_field_font_color']) &&  $style['wpda_form_field_font_color']!='') $output .= 'color:' .$style['wpda_form_field_font_color'].'!important;';
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' .wpdevart-select select' .'{' .$output .'}';
			}
			
			$output = '';
			if( isset($style['wpda_form_field_height']) && $style['wpda_form_field_height']!='') $output .= 'height:' .$style['wpda_form_field_height'] .';'; 
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' .wpdevart-select select'.'{' .$output .'}';
			}
			
			$output = '';
			if(isset($style['wpda_form_submit_btn_left_margin']) && $style['wpda_form_submit_btn_left_margin'] !='') $output .= 'margin-left:' .wpda_form_to_mix($style['wpda_form_submit_btn_left_margin'])	.';';
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' button[type=submit]'.'{' .$output .'}';
			}
			
			$output = '';
			if(isset( $style['wpda_form_submit_btn_font_size']) && $style['wpda_form_submit_btn_font_size'] !='') $output .= 'font-size:' .wpda_form_to_pixel($style['wpda_form_submit_btn_font_size']).';';
			if(isset( $style['wpda_form_submit_btn_font_family']) && $style['wpda_form_submit_btn_font_family'] !='') $output .= 'font-family:' . $style['wpda_form_submit_btn_font_family'].';';
			if(isset( $style['wpda_form_submit_btn_font_weight']) && $style['wpda_form_submit_btn_font_weight'] !='') $output .= wpda_form_get_font_styles_css($style['wpda_form_submit_btn_font_weight']);
			if(isset( $style['wpda_form_submit_btn_font_color']) && $style['wpda_form_submit_btn_font_color'] !='') $output .= 'color:' .$style['wpda_form_submit_btn_font_color'].'!important;';
			if(isset( $style['wpda_form_submit_btn_line_height']) && $style['wpda_form_submit_btn_line_height'] !='') $output .= 'line-height:' .wpda_form_to_pixel($style['wpda_form_submit_btn_line_height']).';';
			if(isset( $style['wpda_form_submit_btn_border_thickness']) && $style['wpda_form_submit_btn_border_thickness'] !='')  $output .= 'border:' .wpda_form_to_pixel($style['wpda_form_submit_btn_border_thickness']).' '.$style['wpda_form_submit_btn_border_style'].' '.$style['wpda_form_submit_btn_border_color'].' !important;';
			if(isset( $style['wpda_form_submit_btn_top_padding']) && $style['wpda_form_submit_btn_top_padding'] !='') $output .= 'padding-top:' .wpda_form_to_pixel($style['wpda_form_submit_btn_top_padding'])	.'!important;';
			if(isset( $style['wpda_form_submit_btn_bottom_padding']) && $style['wpda_form_submit_btn_bottom_padding'] !='') $output .= 'padding-bottom:' .wpda_form_to_pixel($style['wpda_form_submit_btn_bottom_padding']) .'!important;';
			if(isset( $style['wpda_form_submit_btn_left_padding']) && $style['wpda_form_submit_btn_left_padding'] !='') $output .= 'padding-left:' .wpda_form_to_pixel($style['wpda_form_submit_btn_left_padding']) .'!important;';	
			if(isset( $style['wpda_form_submit_btn_right_padding'] ) && $style['wpda_form_submit_btn_right_padding'] !='') $output .= 'padding-right:' .wpda_form_to_pixel($style['wpda_form_submit_btn_right_padding']) .'!important;';
			if(isset( $style['wpda_form_submit_btn_top_margin']) && $style['wpda_form_submit_btn_top_margin'] !='') $output .= 'margin-top:' .wpda_form_to_mix($style['wpda_form_submit_btn_top_margin'])	.';';
			if(isset( $style['wpda_form_submit_btn_bottom_margin']) && $style['wpda_form_submit_btn_bottom_margin'] !='') $output .= 'margin-bottom:' .wpda_form_to_mix($style['wpda_form_submit_btn_bottom_margin']) .';';
			if(isset( $style['wpda_form_submit_btn_right_margin']) && $style['wpda_form_submit_btn_right_margin'] !='') $output .= 'margin-right:' .wpda_form_to_mix($style['wpda_form_submit_btn_right_margin'])	.';';
			if(isset( $style['wpda_form_submit_btn_border_radius']) && $style['wpda_form_submit_btn_border_radius'] !='') $output .= 'border-radius:' .wpda_form_to_pixel($style['wpda_form_submit_btn_border_radius'])	.';';
			if(isset( $style['wpda_form_submit_btn_background_color']) && $style['wpda_form_submit_btn_background_color'] !='') $output .= 'background-color:' .$style['wpda_form_submit_btn_background_color']	.' !important;';
			
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' button[type="submit"], #wpdevart-forms-'.$key.' input[type="reset"]'.'{' .$output .'}';
			}
			
			$output = '';
			if(isset($style['wpda_form_submit_btn_background_hover_color']) && $style['wpda_form_submit_btn_background_hover_color'] !='') $output .= 'background-color:' .$style['wpda_form_submit_btn_background_hover_color']	.' !important;';
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' button[type="submit"]:hover,#wpdevart-forms-'.$key.' button[type="submit"]:focus, #wpdevart-forms-'.$key.' input[type="reset"]:focus, #wpdevart-forms-'.$key.' input[type="reset"]:hover'.'{' .$output .'}';
			}
			
			$output = '';
			if( isset($style['wpda_form_field_focus_background_color']) && $style['wpda_form_field_focus_background_color']!='') $output .= 'background-color:' .$style['wpda_form_field_focus_background_color'].'!important;';
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.' input:focus,#wpdevart-forms-'.$key.' .wpdevart-select select:focus,#wpdevart-forms-'.$key.' textarea:focus ' .'{' .$output .'}';
			}
			
			$output = '';
			if( isset($style['wpda_form_field_width']) && $style['wpda_form_field_width']!='')$output .= 'width:' .$style['wpda_form_field_width'] .' ;';
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-' .$key .' .input-field-inner,#wpdevart-forms-' .$key .' textarea,#wpdevart-forms-' .$key .' .wpdevart-sub-fields-inner ' .'{' .$output .'}';
			}

			$output = '';
			if( isset($style['wpda_form_label_width']) && $style['wpda_form_label_width']!='') $output .= 'width:' .$style['wpda_form_label_width'] .'!important;';
			if( isset($style['wpda_form_field_label_font_size']) && $style['wpda_form_field_label_font_size']!='') $output .= 'font-size:' .wpda_form_to_pixel($style['wpda_form_field_label_font_size']).'!important;';
			if( isset($style['wpda_form_field_label_font_family']) && $style['wpda_form_field_label_font_family']!='') $output .= 'font-family:' . $style['wpda_form_field_label_font_family'].'!important;';
			if( isset($style['wpda_form_field_label_font_weight']) && $style['wpda_form_field_label_font_weight']!='') $output .=   wpda_form_get_font_styles_css($style['wpda_form_field_label_font_weight']);
			if( isset($style['wpda_form_field_label_font_color']) && $style['wpda_form_field_label_font_color']!='') $output .= 'color:' .$style['wpda_form_field_label_font_color'].'!important;';	
			if( isset($style['wpda_form_field_label_line_height']) && $style['wpda_form_field_label_line_height']!='') $output .= 'line-height:' .wpda_form_to_pixel($style['wpda_form_field_label_line_height']).'!important;';
			if($output){
				$wpda_form_styles .= '#wpdevart-forms-'.$key.'  label{'.$output.'}';
				$wpda_form_styles .= '#wpdevart-forms-'.$key.'  span label{width:auto;}';
				
				$wpda_form_styles .= '#wpdevart-forms-'.$key.'  input[type="submit"],#wpdevart-forms-'.$key.'  input[type="reset"],#wpdevart-forms-'.$key.'  input[type="button"],#wpdevart-forms-'.$key.'  button{width:auto;}';
			  	$wpda_form_styles .= '#wpdevart-forms-'.$key.'  .wpdevart-select select{width:100%;}';
			}
			
			$output = '';
			if( isset($style['wpda_form_success_message_bg_color']) && $style['wpda_form_success_message_bg_color']!='' ) $output .= 'background-color:' . $style['wpda_form_success_message_bg_color'] .' !important;';
			if( $output ) {
				$wpda_form_styles .= '.success_message' .$key .'{' .$output .'}';
			}
				
			$output = '';
			if( isset($style['wpda_form_failure_message_bg_color']) && $style['wpda_form_failure_message_bg_color']!='' ) $output .= 'background-color: ' .$style['wpda_form_failure_message_bg_color'] .' !important;';
			if( $output ) {
				$wpda_form_styles .= '.failure_message' .$key .'{' .$output .'}';
			}
			
			$output = '';
			if( isset($style['wpda_form_message_top_margin']) 	&& $style['wpda_form_message_top_margin'] !='' ) $output .= 'margin-top:' .wpda_form_to_mix($style['wpda_form_message_top_margin'])	.';';
			if( isset($style['wpda_form_message_bottom_margin']) && $style['wpda_form_message_bottom_margin'] !='' ) $output .= 'margin-bottom:' .wpda_form_to_mix($style['wpda_form_message_bottom_margin']) .';';
			if( isset($style['wpda_form_message_left_margin']) && $style['wpda_form_message_left_margin'] !='' ) $output .= 'margin-left:' .wpda_form_to_mix($style['wpda_form_message_left_margin'])	.';';
			if( isset($style['wpda_form_message_right_margin']) && $style['wpda_form_message_right_margin'] !='' ) $output .= 'margin-right:' .wpda_form_to_mix($style['wpda_form_message_right_margin'])	.';';
			if( isset($style['wpda_form_message_top_padding']) && $style['wpda_form_message_top_padding'] !='' ) $output .= 'padding-top:' .wpda_form_to_pixel($style['wpda_form_message_top_padding'])	.'!important;';
			if( isset($style['wpda_form_message_bottom_padding']) && $style['wpda_form_message_bottom_padding'] !='' ) $output .= 'padding-bottom:' .wpda_form_to_pixel($style['wpda_form_message_bottom_padding']) .'!important;';
			if( isset($style['wpda_form_message_left_padding']) && $style['wpda_form_message_left_padding'] !='' ) $output .= 'padding-left:' .wpda_form_to_pixel($style['wpda_form_message_left_padding']) .'!important;';	
			if( isset($style['wpda_form_message_right_padding']) && $style['wpda_form_message_right_padding'] !='' ) $output .= 'padding-right:' .wpda_form_to_pixel($style['wpda_form_message_right_padding']) .'!important;';
			if( isset($style['wpda_form_message_font_size']) && $style['wpda_form_message_font_size'] !='' ) $output .= 'font-size:' .wpda_form_to_pixel($style['wpda_form_message_font_size']) .';';
			if( isset($style['wpda_form_message_font_family']) && $style['wpda_form_message_font_family'] !='' ) $output .= 'font-family:'	.$style['wpda_form_message_font_family'] .' !important;';
			if( isset($style['wpda_form_message_font_weight']) && $style['wpda_form_message_font_weight'] !='' ) $output .= wpda_form_get_font_styles_css($style['wpda_form_message_font_weight']);
			if( isset($style['wpda_form_message_font_color']) && $style['wpda_form_message_font_color'] !='' ) $output .= 'color:' .$style['wpda_form_message_font_color']	 .' !important; ';						
			if( isset($style['wpda_form_message_line_height']) && $style['wpda_form_message_line_height'] !='' ) $output .= 'line-height:' .wpda_form_to_pixel($style['wpda_form_message_line_height'])	.'; ';
			if($output){
				$wpda_form_styles .= '.reply_msg' .$key .'{' .$output .'}';
			}
			
			$output = '';
			if( isset($style['wpda_form_message_font_family']) && $style['wpda_form_message_font_family'] !='' ) 	$output .= 'font-family:' .$style['wpda_form_message_font_family'] .' !important;';
			if($output){
				$wpda_form_styles .= '.reply_msg'.$key.'  h3 '.'{'.$output.'}';
			}
			
            // for separator and divider border
            $output = '';
            if( isset($style['wpda_form_separator_border_thickness']) && $style['wpda_form_separator_border_thickness']!='') $output .= 'border-bottom:' .wpda_form_to_pixel($style['wpda_form_separator_border_thickness']).' '.$style['wpda_form_separator_border_style'].' '.$style['wpda_form_separator_border_color'].' !important;';
            if($output){
                $wpda_form_styles .= '#wpdevart-forms-'.$key.' h3.separator-title:before, #wpdevart-forms-' .$key .' h3.separator-title:after, #wpdevart-forms-'.$key .' .separator-before-after '.'{' .$output .'margin:0 !important;}';
            }
            
            $output = '';
            if( isset($style['wpda_form_separator_border_thickness']) && $style['wpda_form_separator_border_thickness']!='') $output .= 'border-bottom:' .wpda_form_to_pixel($style['wpda_form_separator_border_thickness']).' '.$style['wpda_form_separator_border_style'].' '.$style['wpda_form_separator_border_color'].' !important;';
            
            if($output){
                $wpda_form_styles .= '#wpdevart-forms-'.$key.' h3.separator-title:before, #wpdevart-forms-' .$key .' h3.separator-title:after '.'{' .$output .' margin:0 10px !important;}';
            }


            $output = '';
            if( isset($style['wpda_form_separator_top_margin']) && $style['wpda_form_separator_top_margin']!='') $output .= 'margin-top:' .wpda_form_to_mix($style['wpda_form_separator_top_margin']).' !important;';
            if( isset($style['wpda_form_separator_right_margin']) && $style['wpda_form_separator_right_margin']!='') $output .= 'margin-right:' .wpda_form_to_mix($style['wpda_form_separator_right_margin']).'!important;';
            if( isset($style['wpda_form_separator_bottom_margin']) && $style['wpda_form_separator_bottom_margin']!='') $output .= 'margin-bottom:' .wpda_form_to_mix($style['wpda_form_separator_bottom_margin']).'!important;';
            if( isset($style['wpda_form_separator_left_margin']) && $style['wpda_form_separator_left_margin']!='') $output .= 'margin-left:' .wpda_form_to_mix($style['wpda_form_separator_left_margin']).'!important;';
            if( isset($style['wpda_form_separator_text_align']) && $style['wpda_form_separator_text_align']!='') $output .= 'text-align:' .$style['wpda_form_separator_text_align'].';';
            if($output){
                $wpda_form_styles .= '#wpdevart-forms-'.$key.' div.separator-with-title '.'{' .$output .'}';
            }


            // separator text align

            $output = '';
            /*padding based on text-align*/
            if( isset($style['wpda_form_separator_text_align']) && $style['wpda_form_separator_text_align'] == 'left') $output .= 'padding:0 !important;padding-right:5px !important;';
            if( isset($style['wpda_form_separator_text_align']) && $style['wpda_form_separator_text_align'] == 'center') $output .= 'padding:0 !important;padding-left:5px !important;padding-right:5px !important;';
            if( isset($style['wpda_form_separator_text_align']) && $style['wpda_form_separator_text_align'] == 'right') $output .= 'padding:0 !important;padding-left:5px !important;';
            if($output){
                $wpda_form_styles .= '#wpdevart-forms-'.$key.' h3.separator-title '.'{' .$output .'}';
            }
            // for font of separator

            $output = '';

            if( isset($style['wpda_form_separator_font_family']) && $style['wpda_form_separator_font_family']!='') $output .= 'font-family:' . $style['wpda_form_separator_font_family'].';';
            if( isset($style['wpda_form_separator_font_size']) && $style['wpda_form_separator_font_size']!='') $output .= 'font-size:' .wpda_form_to_pixel($style['wpda_form_separator_font_size']).';';
            if( isset($style['wpda_form_separator_font_weight']) && $style['wpda_form_separator_font_weight']!='') $output .= wpda_form_get_font_styles_css($style['wpda_form_separator_font_weight']);
            if( isset($style['wpda_form_separator_font_color']) && $style['wpda_form_separator_font_color']!='') $output .= 'color:' .$style['wpda_form_separator_font_color'].'!important;';
            if( isset($style['wpda_form_separator_line_height']) && $style['wpda_form_separator_line_height']!='') $output .= 'line-height:' .wpda_form_to_pixel($style['wpda_form_separator_line_height']).';';

            if($output){
                $wpda_form_styles .= '#wpdevart-forms-'.$key.' h3.separator-title '.'{' .$output .'}';
            }

            // for heading/titles


            $output = '';

            if( isset($style['wpda_form_custom_heading_border_thickness']) && $style['wpda_form_custom_heading_border_thickness']!='') $output .= 'border-bottom:' .wpda_form_to_pixel($style['wpda_form_custom_heading_border_thickness']).' '.$style['wpda_form_custom_heading_border_style'].' '.$style['wpda_form_custom_heading_border_color'].' !important;';

            if( isset($style['wpda_form_custom_heading_top_margin']) && $style['wpda_form_custom_heading_top_margin']!='') $output .= 'margin-top:' .wpda_form_to_mix($style['wpda_form_custom_heading_top_margin']).';';
            if( isset($style['wpda_form_custom_heading_right_margin']) && $style['wpda_form_custom_heading_right_margin']!='') $output .= 'margin-right:' .wpda_form_to_mix($style['wpda_form_custom_heading_right_margin']).';';
            if( isset($style['wpda_form_custom_heading_bottom_margin']) && $style['wpda_form_custom_heading_bottom_margin']!='') $output .= 'margin-bottom:' .wpda_form_to_mix($style['wpda_form_custom_heading_bottom_margin']).';';
            if( isset($style['wpda_form_custom_heading_left_margin']) && $style['wpda_form_custom_heading_left_margin']!='') $output .= 'margin-left:' .wpda_form_to_mix($style['wpda_form_custom_heading_left_margin']).';';


            if( isset($style['wpda_form_custom_heading_font_family']) && $style['wpda_form_custom_heading_font_family']!='') $output .= 'font-family:' . $style['wpda_form_custom_heading_font_family'].';';
            if( isset($style['wpda_form_custom_heading_font_size']) && $style['wpda_form_custom_heading_font_size']!='') $output .= 'font-size:' .wpda_form_to_pixel($style['wpda_form_custom_heading_font_size']).';';
            if( isset($style['wpda_form_custom_heading_font_weight']) && $style['wpda_form_custom_heading_font_weight']!='') $output .= wpda_form_get_font_styles_css($style['wpda_form_custom_heading_font_weight']);
            if( isset($style['wpda_form_custom_heading_font_color']) && $style['wpda_form_custom_heading_font_color']!='') $output .= 'color:' .$style['wpda_form_custom_heading_font_color'].'!important;';
            if( isset($style['wpda_form_custom_heading_line_height']) && $style['wpda_form_custom_heading_line_height']!='') $output .= 'line-height:' .wpda_form_to_pixel($style['wpda_form_custom_heading_line_height']).';';
            if( isset($style['wpda_form_custom_heading_text_align']) && $style['wpda_form_custom_heading_text_align']!='') $output .= 'text-align:' .$style['wpda_form_custom_heading_text_align'].';';

            if($output){
                $wpda_form_styles .= '#wpdevart-forms-'.$key.' .wf-custom-heading '.'{' .$output .'}';
            }
        }
    }

	if($wpda_form_styles) echo "\n" .'<style>' .$wpda_form_styles .'</style>' ."\n";
}
 add_action('wp_head', 'wpda_form_forms_custom_styles'); 

function wpda_form_to_pixel( $value ) {
	//	allow only integers and minus ( - ), then append px
	$clean_value = preg_replace('/[^\d-]+/', '', $value);
	$wpda_form_to_pixel = $clean_value .'px';
	return $wpda_form_to_pixel;
}
function wpda_form_to_percent( $value ) {
	//	allow only integers and minus ( - ), then append %
	$clean_value = preg_replace('/[^\d-]+/', '', $value);
	$wpda_form_to_percent = $clean_value .'%';
	return $wpda_form_to_percent;
}
function wpda_form_to_mix( $value ) {
	//	allow only integers, minus ( - ), px, em, % 
	$clean_value = preg_replace('/[^\d- \"px" \"em" \%]+/', '', $value);
	if( strrpos($clean_value, 'px')==false && strrpos($clean_value, 'em')==false && strrpos($clean_value, '%')==false ) {
		$wpda_form_to_mix = $clean_value .'px';
	} else {
		$wpda_form_to_mix = $clean_value;
	}
	
	return $wpda_form_to_mix;
}
 
function wpda_form_esc_strings( $value ) {
	$value_check = preg_replace('/\D/', '', $value);
	return $value_check;
}
 
function wpda_form_border_styles() {
	$wpda_form_border_styles = array(
		'solid'  => 'Solid',
		'dotted' => 'Dotted',
		'dashed' => 'Dashed',
		'double' => 'Two Borders',
		'groove' => '3D Grooved',
		'ridge'  => '3D Ridged',
		'inset'  => '3D Inset',
		'outset' => '3D Outset',
	);
	return $wpda_form_border_styles;
}
 
function wpda_form_size_range() {
	$wpdevart_sizes = array();
	for($i=8; $i<=100; $i++) {
		$wpdevart_sizes[] = $i;
	}
	return $wpdevart_sizes;
}
 
function wpda_form_size_range_line_height() {
	$wpdevart_sizes = array();
	for($i=8; $i <= 120; $i++) {
		$wpdevart_sizes[] = $i;
	}
	return $wpdevart_sizes;
}

function wpda_form_text_align() {
	$wpda_form_text_align = array(
		'left' => 'Left', 
		'right' => 'Right',
		'center' => 'Center',
		);
	return $wpda_form_text_align;
}

function wpda_form_font_styles() {
	$wpda_form_font_styles = array(
		'bold' => 'Bold', 
		'normal' => 'Normal', 
		'italic' => 'Italic',  
		'underline' => 'Underline',
		'bold italic' => 'Bold Italic',
		'bold underline' => 'Bold Underline',
		'italic underline' => 'Italic Underline',
		'bold italic underline' => 'Bold Italic Underline'
		);
	return $wpda_form_font_styles;
}
 
//	used in making csv export file of form submissions
function wpda_form_convert_utf8_encoding_csv($value)
{
	if(!empty($value)) {
			$temp = preg_replace('/"/', '""',$value);
			return "\"" . mb_convert_encoding($temp,'UTF-8','UTF-8')."\"" ;
	}
}
 
//	check similiar form name and append integer to it  e.g form, form, form-1, form-2 
function wpda_form_append_integer_similiar_names ($title, $id=NULL) {
	global $wpdb;	   
	global $wpda_form_table; 
	
	$i = 1;
	$j = 1;
	if(empty($title)) {
		$title = "Untitled";
		return $title;
	} else  {	
		if($id) {
			$similiarFormName = $wpdb->get_results($wpdb->prepare("SELECT name FROM ".$wpda_form_table['wpdevart_forms']." WHERE name =%s AND id !=%d ",$title,$id));
		} else {
			$similiarFormName = $wpdb->get_results($wpdb->prepare("SELECT name FROM ".$wpda_form_table['wpdevart_forms']." WHERE name =%s ",$title));	
		}
		
		if(count($similiarFormName) > 0) {
			$title = $title .'-'.$j;
			while($i) {
				$similiarFormName = $wpdb->get_results($wpdb->prepare("SELECT name FROM ".$wpda_form_table['wpdevart_forms']." WHERE name =%s ",$title));
				if(count($similiarFormName) > 0) {
					$title = $title.'-'.$j;
				} else {
					break;
				}
			}
			return $title;
			
		} else {
			return $title;
		}
	}
	unset($j);
}
 
// converts object to array e.g $obj->attr = $arr[attr]
function wpda_form_object_to_array($d)  {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	} else {
		// Return array
		return $d;
	}
}
 
function wpda_form_datetime($arg) {
	if(!empty($arg)) {
		return date("M j, Y ".' '."-  g:i a",$arg); // F
	}
}

//	print_r results
function wpda_form_pre_print_r($val) {
	return $a = "<br /><pre>". print_r($val, true)."<br />";
}

 
function wpda_form_mixed_font_faces() {
	$mixed_font_faces = array_merge(
		wpda_form_get_os_font_faces(),
		wpda_form_get_google_font_faces()
	);
	//	asort( $mixed_font_faces );
	
	return $mixed_font_faces;
}
 
function wpda_form_get_os_font_faces() {
	// OS Font Defaults
	$os_faces = array(
					//'font_face' => ' Select Font Face / Family',
					'Arial, sans-serif' => 'Arial',
					'"Avant Garde", sans-serif' => 'Avant Garde',
					'Cambria, Georgia, serif' => 'Cambria',
					'Copse, sans-serif' => 'Copse',
					'Garamond, "Hoefler Text", Times New Roman, Times, serif' => 'Garamond',
					'Georgia, serif' => 'Georgia',
					'"Helvetica Neue", Helvetica, sans-serif' => 'Helvetica Neue',
					'Tahoma, Geneva, sans-serif' => 'Tahoma'
					);
	return $os_faces;
}
 
function wpda_form_get_google_font_faces() {
	// Google Font Defaults
	$google_faces = array(
						  "ABeeZee" => "ABeeZee",
			"Abel" => "Abel",
			"Abril Fatface" => "Abril Fatface",
			"Aclonica" => "Aclonica",
			"Acme" => "Acme",
			"Actor" => "Actor",
			"Adamina" => "Adamina",
			"Advent Pro" => "Advent Pro",
			"Aguafina Script" => "Aguafina Script",
			"Akronim" => "Akronim",
			"Aladin" => "Aladin",
			"Aldrich" => "Aldrich",
			"Alef" => "Alef",
			"Alegreya" => "Alegreya",
			"Alegreya SC" => "Alegreya SC",
			"Alegreya Sans" => "Alegreya Sans",
			"Alegreya Sans SC" => "Alegreya Sans SC",
			"Alex Brush" => "Alex Brush",
			"Alfa Slab One" => "Alfa Slab One",
			"Alice" => "Alice",
			"Alike" => "Alike",
			"Alike Angular" => "Alike Angular",
			"Allan" => "Allan",
			"Allerta" => "Allerta",
			"Allerta Stencil" => "Allerta Stencil",
			"Allura" => "Allura",
			"Almendra" => "Almendra",
			"Almendra Display" => "Almendra Display",
			"Almendra SC" => "Almendra SC",
			"Amarante" => "Amarante",
			"Amaranth" => "Amaranth",
			"Amatic SC" => "Amatic SC",
			"Amethysta" => "Amethysta",
			"Anaheim" => "Anaheim",
			"Andada" => "Andada",
			"Andika" => "Andika",
			"Angkor" => "Angkor",
			"Annie Use Your Telescope" => "Annie Use Your Telescope",
			"Anonymous Pro" => "Anonymous Pro",
			"Antic" => "Antic",
			"Antic Didone" => "Antic Didone",
			"Antic Slab" => "Antic Slab",
			"Anton" => "Anton",
			"Arapey" => "Arapey",
			"Arbutus" => "Arbutus",
			"Arbutus Slab" => "Arbutus Slab",
			"Architects Daughter" => "Architects Daughter",
			"Archivo Black" => "Archivo Black",
			"Archivo Narrow" => "Archivo Narrow",
			"Arimo" => "Arimo",
			"Arizonia" => "Arizonia",
			"Armata" => "Armata",
			"Artifika" => "Artifika",
			"Arvo" => "Arvo",
			"Asap" => "Asap",
			"Asset" => "Asset",
			"Astloch" => "Astloch",
			"Asul" => "Asul",
			"Atomic Age" => "Atomic Age",
			"Aubrey" => "Aubrey",
			"Audiowide" => "Audiowide",
			"Autour One" => "Autour One",
			"Average" => "Average",
			"Average Sans" => "Average Sans",
			"Averia Gruesa Libre" => "Averia Gruesa Libre",
			"Averia Libre" => "Averia Libre",
			"Averia Sans Libre" => "Averia Sans Libre",
			"Averia Serif Libre" => "Averia Serif Libre",
			"Bad Script" => "Bad Script",
			"Balthazar" => "Balthazar",
			"Bangers" => "Bangers",
			"Basic" => "Basic",
			"Battambang" => "Battambang",
			"Baumans" => "Baumans",
			"Bayon" => "Bayon",
			"Belgrano" => "Belgrano",
			"Belleza" => "Belleza",
			"BenchNine" => "BenchNine",
			"Bentham" => "Bentham",
			"Berkshire Swash" => "Berkshire Swash",
			"Bevan" => "Bevan",
			"Bigelow Rules" => "Bigelow Rules",
			"Bigshot One" => "Bigshot One",
			"Bilbo" => "Bilbo",
			"Bilbo Swash Caps" => "Bilbo Swash Caps",
			"Bitter" => "Bitter",
			"Black Ops One" => "Black Ops One",
			"Bokor" => "Bokor",
			"Bonbon" => "Bonbon",
			"Boogaloo" => "Boogaloo",
			"Bowlby One" => "Bowlby One",
			"Bowlby One SC" => "Bowlby One SC",
			"Brawler" => "Brawler",
			"Bree Serif" => "Bree Serif",
			"Bubblegum Sans" => "Bubblegum Sans",
			"Bubbler One" => "Bubbler One",
			"Buda" => "Buda",
			"Buenard" => "Buenard",
			"Butcherman" => "Butcherman",
			"Butterfly Kids" => "Butterfly Kids",
			"Cabin" => "Cabin",
			"Cabin Condensed" => "Cabin Condensed",
			"Cabin Sketch" => "Cabin Sketch",
			"Caesar Dressing" => "Caesar Dressing",
			"Cagliostro" => "Cagliostro",
			"Calligraffitti" => "Calligraffitti",
			"Cambay" => "Cambay",
			"Cambo" => "Cambo",
			"Candal" => "Candal",
			"Cantarell" => "Cantarell",
			"Cantata One" => "Cantata One",
			"Cantora One" => "Cantora One",
			"Capriola" => "Capriola",
			"Cardo" => "Cardo",
			"Carme" => "Carme",
			"Carrois Gothic" => "Carrois Gothic",
			"Carrois Gothic SC" => "Carrois Gothic SC",
			"Carter One" => "Carter One",
			"Caudex" => "Caudex",
			"Cedarville Cursive" => "Cedarville Cursive",
			"Ceviche One" => "Ceviche One",
			"Changa One" => "Changa One",
			"Chango" => "Chango",
			"Chau Philomene One" => "Chau Philomene One",
			"Chela One" => "Chela One",
			"Chelsea Market" => "Chelsea Market",
			"Chenla" => "Chenla",
			"Cherry Cream Soda" => "Cherry Cream Soda",
			"Cherry Swash" => "Cherry Swash",
			"Chewy" => "Chewy",
			"Chicle" => "Chicle",
			"Chivo" => "Chivo",
			"Cinzel" => "Cinzel",
			"Cinzel Decorative" => "Cinzel Decorative",
			"Clicker Script" => "Clicker Script",
			"Coda" => "Coda",
			"Coda Caption" => "Coda Caption",
			"Codystar" => "Codystar",
			"Combo" => "Combo",
			"Comfortaa" => "Comfortaa",
			"Coming Soon" => "Coming Soon",
			"Concert One" => "Concert One",
			"Condiment" => "Condiment",
			"Content" => "Content",
			"Contrail One" => "Contrail One",
			"Convergence" => "Convergence",
			"Cookie" => "Cookie",
			"Copse" => "Copse",
			"Corben" => "Corben",
			"Courgette" => "Courgette",
			"Cousine" => "Cousine",
			"Coustard" => "Coustard",
			"Covered By Your Grace" => "Covered By Your Grace",
			"Crafty Girls" => "Crafty Girls",
			"Creepster" => "Creepster",
			"Crete Round" => "Crete Round",
			"Crimson Text" => "Crimson Text",
			"Croissant One" => "Croissant One",
			"Crushed" => "Crushed",
			"Cuprum" => "Cuprum",
			"Cutive" => "Cutive",
			"Cutive Mono" => "Cutive Mono",
			"Damion" => "Damion",
			"Dancing Script" => "Dancing Script",
			"Dangrek" => "Dangrek",
			"Dawning of a New Day" => "Dawning of a New Day",
			"Days One" => "Days One",
			"Dekko" => "Dekko",
			"Delius" => "Delius",
			"Delius Swash Caps" => "Delius Swash Caps",
			"Delius Unicase" => "Delius Unicase",
			"Della Respira" => "Della Respira",
			"Denk One" => "Denk One",
			"Devonshire" => "Devonshire",
			"Dhurjati" => "Dhurjati",
			"Didact Gothic" => "Didact Gothic",
			"Diplomata" => "Diplomata",
			"Diplomata SC" => "Diplomata SC",
			"Domine" => "Domine",
			"Donegal One" => "Donegal One",
			"Doppio One" => "Doppio One",
			"Dorsa" => "Dorsa",
			"Dosis" => "Dosis",
			"Dr Sugiyama" => "Dr Sugiyama",
			"Droid Sans" => "Droid Sans",
			"Droid Sans Mono" => "Droid Sans Mono",
			"Droid Serif" => "Droid Serif",
			"Duru Sans" => "Duru Sans",
			"Dynalight" => "Dynalight",
			"EB Garamond" => "EB Garamond",
			"Eagle Lake" => "Eagle Lake",
			"Eater" => "Eater",
			"Economica" => "Economica",
			"Ek Mukta" => "Ek Mukta",
			"Electrolize" => "Electrolize",
			"Elsie" => "Elsie",
			"Elsie Swash Caps" => "Elsie Swash Caps",
			"Emblema One" => "Emblema One",
			"Emilys Candy" => "Emilys Candy",
			"Engagement" => "Engagement",
			"Englebert" => "Englebert",
			"Enriqueta" => "Enriqueta",
			"Erica One" => "Erica One",
			"Esteban" => "Esteban",
			"Euphoria Script" => "Euphoria Script",
			"Ewert" => "Ewert",
			"Exo" => "Exo",
			"Exo 2" => "Exo 2",
			"Expletus Sans" => "Expletus Sans",
			"Fanwood Text" => "Fanwood Text",
			"Fascinate" => "Fascinate",
			"Fascinate Inline" => "Fascinate Inline",
			"Faster One" => "Faster One",
			"Fasthand" => "Fasthand",
			"Fauna One" => "Fauna One",
			"Federant" => "Federant",
			"Federo" => "Federo",
			"Felipa" => "Felipa",
			"Fenix" => "Fenix",
			"Finger Paint" => "Finger Paint",
			"Fira Mono" => "Fira Mono",
			"Fira Sans" => "Fira Sans",
			"Fjalla One" => "Fjalla One",
			"Fjord One" => "Fjord One",
			"Flamenco" => "Flamenco",
			"Flavors" => "Flavors",
			"Fondamento" => "Fondamento",
			"Fontdiner Swanky" => "Fontdiner Swanky",
			"Forum" => "Forum",
			"Francois One" => "Francois One",
			"Freckle Face" => "Freckle Face",
			"Fredericka the Great" => "Fredericka the Great",
			"Fredoka One" => "Fredoka One",
			"Freehand" => "Freehand",
			"Fresca" => "Fresca",
			"Frijole" => "Frijole",
			"Fruktur" => "Fruktur",
			"Fugaz One" => "Fugaz One",
			"GFS Didot" => "GFS Didot",
			"GFS Neohellenic" => "GFS Neohellenic",
			"Gabriela" => "Gabriela",
			"Gafata" => "Gafata",
			"Galdeano" => "Galdeano",
			"Galindo" => "Galindo",
			"Gentium Basic" => "Gentium Basic",
			"Gentium Book Basic" => "Gentium Book Basic",
			"Geo" => "Geo",
			"Geostar" => "Geostar",
			"Geostar Fill" => "Geostar Fill",
			"Germania One" => "Germania One",
			"Gidugu" => "Gidugu",
			"Gilda Display" => "Gilda Display",
			"Give You Glory" => "Give You Glory",
			"Glass Antiqua" => "Glass Antiqua",
			"Glegoo" => "Glegoo",
			"Gloria Hallelujah" => "Gloria Hallelujah",
			"Goblin One" => "Goblin One",
			"Gochi Hand" => "Gochi Hand",
			"Gorditas" => "Gorditas",
			"Goudy Bookletter 1911" => "Goudy Bookletter 1911",
			"Graduate" => "Graduate",
			"Grand Hotel" => "Grand Hotel",
			"Gravitas One" => "Gravitas One",
			"Great Vibes" => "Great Vibes",
			"Griffy" => "Griffy",
			"Gruppo" => "Gruppo",
			"Gudea" => "Gudea",
			"Gurajada" => "Gurajada",
			"Habibi" => "Habibi",
			"Halant" => "Halant",
			"Hammersmith One" => "Hammersmith One",
			"Hanalei" => "Hanalei",
			"Hanalei Fill" => "Hanalei Fill",
			"Handlee" => "Handlee",
			"Hanuman" => "Hanuman",
			"Happy Monkey" => "Happy Monkey",
			"Headland One" => "Headland One",
			"Henny Penny" => "Henny Penny",
			"Herr Von Muellerhoff" => "Herr Von Muellerhoff",
			"Hind" => "Hind",
			"Holtwood One SC" => "Holtwood One SC",
			"Homemade Apple" => "Homemade Apple",
			"Homenaje" => "Homenaje",
			"IM Fell DW Pica" => "IM Fell DW Pica",
			"IM Fell DW Pica SC" => "IM Fell DW Pica SC",
			"IM Fell Double Pica" => "IM Fell Double Pica",
			"IM Fell Double Pica SC" => "IM Fell Double Pica SC",
			"IM Fell English" => "IM Fell English",
			"IM Fell English SC" => "IM Fell English SC",
			"IM Fell French Canon" => "IM Fell French Canon",
			"IM Fell French Canon SC" => "IM Fell French Canon SC",
			"IM Fell Great Primer" => "IM Fell Great Primer",
			"IM Fell Great Primer SC" => "IM Fell Great Primer SC",
			"Iceberg" => "Iceberg",
			"Iceland" => "Iceland",
			"Imprima" => "Imprima",
			"Inconsolata" => "Inconsolata",
			"Inder" => "Inder",
			"Indie Flower" => "Indie Flower",
			"Inika" => "Inika",
			"Irish Grover" => "Irish Grover",
			"Istok Web" => "Istok Web",
			"Italiana" => "Italiana",
			"Italianno" => "Italianno",
			"Jacques Francois" => "Jacques Francois",
			"Jacques Francois Shadow" => "Jacques Francois Shadow",
			"Jim Nightshade" => "Jim Nightshade",
			"Jockey One" => "Jockey One",
			"Jolly Lodger" => "Jolly Lodger",
			"Josefin Sans" => "Josefin Sans",
			"Josefin Slab" => "Josefin Slab",
			"Joti One" => "Joti One",
			"Judson" => "Judson",
			"Julee" => "Julee",
			"Julius Sans One" => "Julius Sans One",
			"Junge" => "Junge",
			"Jura" => "Jura",
			"Just Another Hand" => "Just Another Hand",
			"Just Me Again Down Here" => "Just Me Again Down Here",
			"Kalam" => "Kalam",
			"Kameron" => "Kameron",
			"Kantumruy" => "Kantumruy",
			"Karla" => "Karla",
			"Karma" => "Karma",
			"Kaushan Script" => "Kaushan Script",
			"Kavoon" => "Kavoon",
			"Kdam Thmor" => "Kdam Thmor",
			"Keania One" => "Keania One",
			"Kelly Slab" => "Kelly Slab",
			"Kenia" => "Kenia",
			"Khand" => "Khand",
			"Khmer" => "Khmer",
			"Khula" => "Khula",
			"Kite One" => "Kite One",
			"Knewave" => "Knewave",
			"Kotta One" => "Kotta One",
			"Koulen" => "Koulen",
			"Kranky" => "Kranky",
			"Kreon" => "Kreon",
			"Kristi" => "Kristi",
			"Krona One" => "Krona One",
			"La Belle Aurore" => "La Belle Aurore",
			"Laila" => "Laila",
			"Lakki Reddy" => "Lakki Reddy",
			"Lancelot" => "Lancelot",
			"Lato" => "Lato",
			"League Script" => "League Script",
			"Leckerli One" => "Leckerli One",
			"Ledger" => "Ledger",
			"Lekton" => "Lekton",
			"Lemon" => "Lemon",
			"Libre Baskerville" => "Libre Baskerville",
			"Life Savers" => "Life Savers",
			"Lilita One" => "Lilita One",
			"Lily Script One" => "Lily Script One",
			"Limelight" => "Limelight",
			"Linden Hill" => "Linden Hill",
			"Lobster" => "Lobster",
			"Lobster Two" => "Lobster Two",
			"Londrina Outline" => "Londrina Outline",
			"Londrina Shadow" => "Londrina Shadow",
			"Londrina Sketch" => "Londrina Sketch",
			"Londrina Solid" => "Londrina Solid",
			"Lora" => "Lora",
			"Love Ya Like A Sister" => "Love Ya Like A Sister",
			"Loved by the King" => "Loved by the King",
			"Lovers Quarrel" => "Lovers Quarrel",
			"Luckiest Guy" => "Luckiest Guy",
			"Lusitana" => "Lusitana",
			"Lustria" => "Lustria",
			"Macondo" => "Macondo",
			"Macondo Swash Caps" => "Macondo Swash Caps",
			"Magra" => "Magra",
			"Maiden Orange" => "Maiden Orange",
			"Mako" => "Mako",
			"Mallanna" => "Mallanna",
			"Mandali" => "Mandali",
			"Marcellus" => "Marcellus",
			"Marcellus SC" => "Marcellus SC",
			"Marck Script" => "Marck Script",
			"Margarine" => "Margarine",
			"Marko One" => "Marko One",
			"Marmelad" => "Marmelad",
			"Marvel" => "Marvel",
			"Mate" => "Mate",
			"Mate SC" => "Mate SC",
			"Maven Pro" => "Maven Pro",
			"McLaren" => "McLaren",
			"Meddon" => "Meddon",
			"MedievalSharp" => "MedievalSharp",
			"Medula One" => "Medula One",
			"Megrim" => "Megrim",
			"Meie Script" => "Meie Script",
			"Merienda" => "Merienda",
			"Merienda One" => "Merienda One",
			"Merriweather" => "Merriweather",
			"Merriweather Sans" => "Merriweather Sans",
			"Metal" => "Metal",
			"Metal Mania" => "Metal Mania",
			"Metamorphous" => "Metamorphous",
			"Metrophobic" => "Metrophobic",
			"Michroma" => "Michroma",
			"Milonga" => "Milonga",
			"Miltonian" => "Miltonian",
			"Miltonian Tattoo" => "Miltonian Tattoo",
			"Miniver" => "Miniver",
			"Miss Fajardose" => "Miss Fajardose",
			"Modern Antiqua" => "Modern Antiqua",
			"Molengo" => "Molengo",
			"Molle" => "Molle",
			"Monda" => "Monda",
			"Monofett" => "Monofett",
			"Monoton" => "Monoton",
			"Monsieur La Doulaise" => "Monsieur La Doulaise",
			"Montaga" => "Montaga",
			"Montez" => "Montez",
			"Montserrat" => "Montserrat",
			"Montserrat Alternates" => "Montserrat Alternates",
			"Montserrat Subrayada" => "Montserrat Subrayada",
			"Moul" => "Moul",
			"Moulpali" => "Moulpali",
			"Mountains of Christmas" => "Mountains of Christmas",
			"Mouse Memoirs" => "Mouse Memoirs",
			"Mr Bedfort" => "Mr Bedfort",
			"Mr Dafoe" => "Mr Dafoe",
			"Mr De Haviland" => "Mr De Haviland",
			"Mrs Saint Delafield" => "Mrs Saint Delafield",
			"Mrs Sheppards" => "Mrs Sheppards",
			"Muli" => "Muli",
			"Mystery Quest" => "Mystery Quest",
			"NTR" => "NTR",
			"Neucha" => "Neucha",
			"Neuton" => "Neuton",
			"New Rocker" => "New Rocker",
			"News Cycle" => "News Cycle",
			"Niconne" => "Niconne",
			"Nixie One" => "Nixie One",
			"Nobile" => "Nobile",
			"Nokora" => "Nokora",
			"Norican" => "Norican",
			"Nosifer" => "Nosifer",
			"Nothing You Could Do" => "Nothing You Could Do",
			"Noticia Text" => "Noticia Text",
			"Noto Sans" => "Noto Sans",
			"Noto Serif" => "Noto Serif",
			"Nova Cut" => "Nova Cut",
			"Nova Flat" => "Nova Flat",
			"Nova Mono" => "Nova Mono",
			"Nova Oval" => "Nova Oval",
			"Nova Round" => "Nova Round",
			"Nova Script" => "Nova Script",
			"Nova Slim" => "Nova Slim",
			"Nova Square" => "Nova Square",
			"Numans" => "Numans",
			"Nunito" => "Nunito",
			"Odor Mean Chey" => "Odor Mean Chey",
			"Offside" => "Offside",
			"Old Standard TT" => "Old Standard TT",
			"Oldenburg" => "Oldenburg",
			"Oleo Script" => "Oleo Script",
			"Oleo Script Swash Caps" => "Oleo Script Swash Caps",
			"Open Sans" => "Open Sans",
			"Open Sans Condensed" => "Open Sans Condensed",
			"Oranienbaum" => "Oranienbaum",
			"Orbitron" => "Orbitron",
			"Oregano" => "Oregano",
			"Orienta" => "Orienta",
			"Original Surfer" => "Original Surfer",
			"Oswald" => "Oswald",
			"Over the Rainbow" => "Over the Rainbow",
			"Overlock" => "Overlock",
			"Overlock SC" => "Overlock SC",
			"Ovo" => "Ovo",
			"Oxygen" => "Oxygen",
			"Oxygen Mono" => "Oxygen Mono",
			"PT Mono" => "PT Mono",
			"PT Sans" => "PT Sans",
			"PT Sans Caption" => "PT Sans Caption",
			"PT Sans Narrow" => "PT Sans Narrow",
			"PT Serif" => "PT Serif",
			"PT Serif Caption" => "PT Serif Caption",
			"Pacifico" => "Pacifico",
			"Paprika" => "Paprika",
			"Parisienne" => "Parisienne",
			"Passero One" => "Passero One",
			"Passion One" => "Passion One",
			"Pathway Gothic One" => "Pathway Gothic One",
			"Patrick Hand" => "Patrick Hand",
			"Patrick Hand SC" => "Patrick Hand SC",
			"Patua One" => "Patua One",
			"Paytone One" => "Paytone One",
			"Peddana" => "Peddana",
			"Peralta" => "Peralta",
			"Permanent Marker" => "Permanent Marker",
			"Petit Formal Script" => "Petit Formal Script",
			"Petrona" => "Petrona",
			"Philosopher" => "Philosopher",
			"Piedra" => "Piedra",
			"Pinyon Script" => "Pinyon Script",
			"Pirata One" => "Pirata One",
			"Plaster" => "Plaster",
			"Play" => "Play",
			"Playball" => "Playball",
			"Playfair Display" => "Playfair Display",
			"Playfair Display SC" => "Playfair Display SC",
			"Podkova" => "Podkova",
			"Poiret One" => "Poiret One",
			"Poller One" => "Poller One",
			"Poly" => "Poly",
			"Pompiere" => "Pompiere",
			"Pontano Sans" => "Pontano Sans",
			"Port Lligat Sans" => "Port Lligat Sans",
			"Port Lligat Slab" => "Port Lligat Slab",
			"Prata" => "Prata",
			"Preahvihear" => "Preahvihear",
			"Press Start 2P" => "Press Start 2P",
			"Princess Sofia" => "Princess Sofia",
			"Prociono" => "Prociono",
			"Prosto One" => "Prosto One",
			"Puritan" => "Puritan",
			"Purple Purse" => "Purple Purse",
			"Quando" => "Quando",
			"Quantico" => "Quantico",
			"Quattrocento" => "Quattrocento",
			"Quattrocento Sans" => "Quattrocento Sans",
			"Questrial" => "Questrial",
			"Quicksand" => "Quicksand",
			"Quintessential" => "Quintessential",
			"Qwigley" => "Qwigley",
			"Racing Sans One" => "Racing Sans One",
			"Radley" => "Radley",
			"Rajdhani" => "Rajdhani",
			"Raleway" => "Raleway",
			"Raleway Dots" => "Raleway Dots",
			"Ramabhadra" => "Ramabhadra",
			"Ramaraja" => "Ramaraja",
			"Rambla" => "Rambla",
			"Rammetto One" => "Rammetto One",
			"Ranchers" => "Ranchers",
			"Rancho" => "Rancho",
			"Ranga" => "Ranga",
			"Rationale" => "Rationale",
			"Ravi Prakash" => "Ravi Prakash",
			"Redressed" => "Redressed",
			"Reenie Beanie" => "Reenie Beanie",
			"Revalia" => "Revalia",
			"Ribeye" => "Ribeye",
			"Ribeye Marrow" => "Ribeye Marrow",
			"Righteous" => "Righteous",
			"Risque" => "Risque",
			"Roboto" => "Roboto",
			"Roboto Condensed" => "Roboto Condensed",
			"Roboto Slab" => "Roboto Slab",
			"Rochester" => "Rochester",
			"Rock Salt" => "Rock Salt",
			"Rokkitt" => "Rokkitt",
			"Romanesco" => "Romanesco",
			"Ropa Sans" => "Ropa Sans",
			"Rosario" => "Rosario",
			"Rosarivo" => "Rosarivo",
			"Rouge Script" => "Rouge Script",
			"Rozha One" => "Rozha One",
			"Rubik Mono One" => "Rubik Mono One",
			"Rubik One" => "Rubik One",
			"Ruda" => "Ruda",
			"Rufina" => "Rufina",
			"Ruge Boogie" => "Ruge Boogie",
			"Ruluko" => "Ruluko",
			"Rum Raisin" => "Rum Raisin",
			"Ruslan Display" => "Ruslan Display",
			"Russo One" => "Russo One",
			"Ruthie" => "Ruthie",
			"Rye" => "Rye",
			"Sacramento" => "Sacramento",
			"Sail" => "Sail",
			"Salsa" => "Salsa",
			"Sanchez" => "Sanchez",
			"Sancreek" => "Sancreek",
			"Sansita One" => "Sansita One",
			"Sarina" => "Sarina",
			"Sarpanch" => "Sarpanch",
			"Satisfy" => "Satisfy",
			"Scada" => "Scada",
			"Schoolbell" => "Schoolbell",
			"Seaweed Script" => "Seaweed Script",
			"Sevillana" => "Sevillana",
			"Seymour One" => "Seymour One",
			"Shadows Into Light" => "Shadows Into Light",
			"Shadows Into Light Two" => "Shadows Into Light Two",
			"Shanti" => "Shanti",
			"Share" => "Share",
			"Share Tech" => "Share Tech",
			"Share Tech Mono" => "Share Tech Mono",
			"Shojumaru" => "Shojumaru",
			"Short Stack" => "Short Stack",
			"Siemreap" => "Siemreap",
			"Sigmar One" => "Sigmar One",
			"Signika" => "Signika",
			"Signika Negative" => "Signika Negative",
			"Simonetta" => "Simonetta",
			"Sintony" => "Sintony",
			"Sirin Stencil" => "Sirin Stencil",
			"Six Caps" => "Six Caps",
			"Skranji" => "Skranji",
			"Slabo 13px" => "Slabo 13px",
			"Slabo 27px" => "Slabo 27px",
			"Slackey" => "Slackey",
			"Smokum" => "Smokum",
			"Smythe" => "Smythe",
			"Sniglet" => "Sniglet",
			"Snippet" => "Snippet",
			"Snowburst One" => "Snowburst One",
			"Sofadi One" => "Sofadi One",
			"Sofia" => "Sofia",
			"Sonsie One" => "Sonsie One",
			"Sorts Mill Goudy" => "Sorts Mill Goudy",
			"Source Code Pro" => "Source Code Pro",
			"Source Sans Pro" => "Source Sans Pro",
			"Source Serif Pro" => "Source Serif Pro",
			"Special Elite" => "Special Elite",
			"Spicy Rice" => "Spicy Rice",
			"Spinnaker" => "Spinnaker",
			"Spirax" => "Spirax",
			"Squada One" => "Squada One",
			"Sree Krushnadevaraya" => "Sree Krushnadevaraya",
			"Stalemate" => "Stalemate",
			"Stalinist One" => "Stalinist One",
			"Stardos Stencil" => "Stardos Stencil",
			"Stint Ultra Condensed" => "Stint Ultra Condensed",
			"Stint Ultra Expanded" => "Stint Ultra Expanded",
			"Stoke" => "Stoke",
			"Strait" => "Strait",
			"Sue Ellen Francisco" => "Sue Ellen Francisco",
			"Sunshiney" => "Sunshiney",
			"Supermercado One" => "Supermercado One",
			"Suranna" => "Suranna",
			"Suravaram" => "Suravaram",
			"Suwannaphum" => "Suwannaphum",
			"Swanky and Moo Moo" => "Swanky and Moo Moo",
			"Syncopate" => "Syncopate",
			"Tangerine" => "Tangerine",
			"Taprom" => "Taprom",
			"Tauri" => "Tauri",
			"Teko" => "Teko",
			"Telex" => "Telex",
			"Tenali Ramakrishna" => "Tenali Ramakrishna",
			"Tenor Sans" => "Tenor Sans",
			"Text Me One" => "Text Me One",
			"The Girl Next Door" => "The Girl Next Door",
			"Tienne" => "Tienne",
			"Timmana" => "Timmana",
			"Tinos" => "Tinos",
			"Titan One" => "Titan One",
			"Titillium Web" => "Titillium Web",
			"Trade Winds" => "Trade Winds",
			"Trocchi" => "Trocchi",
			"Trochut" => "Trochut",
			"Trykker" => "Trykker",
			"Tulpen One" => "Tulpen One",
			"Ubuntu" => "Ubuntu",
			"Ubuntu Condensed" => "Ubuntu Condensed",
			"Ubuntu Mono" => "Ubuntu Mono",
			"Ultra" => "Ultra",
			"Uncial Antiqua" => "Uncial Antiqua",
			"Underdog" => "Underdog",
			"Unica One" => "Unica One",
			"UnifrakturCook" => "UnifrakturCook",
			"UnifrakturMaguntia" => "UnifrakturMaguntia",
			"Unkempt" => "Unkempt",
			"Unlock" => "Unlock",
			"Unna" => "Unna",
			"VT323" => "VT323",
			"Vampiro One" => "Vampiro One",
			"Varela" => "Varela",
			"Varela Round" => "Varela Round",
			"Vast Shadow" => "Vast Shadow",
			"Vesper Libre" => "Vesper Libre",
			"Vibur" => "Vibur",
			"Vidaloka" => "Vidaloka",
			"Viga" => "Viga",
			"Voces" => "Voces",
			"Volkhov" => "Volkhov",
			"Vollkorn" => "Vollkorn",
			"Voltaire" => "Voltaire",
			"Waiting for the Sunrise" => "Waiting for the Sunrise",
			"Wallpoet" => "Wallpoet",
			"Walter Turncoat" => "Walter Turncoat",
			"Warnes" => "Warnes",
			"Wellfleet" => "Wellfleet",
			"Wendy One" => "Wendy One",
			"Wire One" => "Wire One",
			"Yanone Kaffeesatz" => "Yanone Kaffeesatz",
			"Yellowtail" => "Yellowtail",
			"Yeseva One" => "Yeseva One",
			"Yesteryear" => "Yesteryear",
			"Zeyada" => "Zeyada",
						  );
	return $google_faces;
}

//	add google font to frontend used in form styling
function wpda_form_typography_google_fonts() {
		
	if(get_option('wpdevart_forms_style')) {
		$all_form_styles = get_option('wpdevart_forms_style');
		foreach($all_form_styles as $form_style) {
			if(!empty($form_style['wpda_form_custom_heading_font_family'])) {
				$selected_fonts[] = $form_style['wpda_form_custom_heading_font_family'];
			}
			if(!empty($form_style['wpda_form_field_font_family'])) {
				$selected_fonts[] = $form_style['wpda_form_field_font_family'];
			}
			if(!empty($form_style['wpda_form_field_label_font_family'])) {
				$selected_fonts[] = $form_style['wpda_form_field_label_font_family'];
			}
			if(!empty($form_style['wpda_form_separator_font_family'])) {
				$selected_fonts[] = $form_style['wpda_form_separator_font_family'];
			}
			if(!empty($form_style['wpda_form_message_font_family'])) {
				$selected_fonts[] = $form_style['wpda_form_message_font_family'];
			}
			if(!empty($form_style['wpda_form_submit_btn_font_family'])) {
				$selected_fonts[] = $form_style['wpda_form_submit_btn_font_family'];
			}
			
		}
		if(!empty($selected_fonts)) {
			//	Remove any duplicates in the list
			$selected_fonts = array_unique($selected_fonts);
			
			$all_google_fonts = array_keys(wpda_form_get_google_font_faces());
			//	Check each of the unique fonts against the defined Google fonts
			//	If it is a Google font, go ahead and call the function to enqueue it
			foreach ( $selected_fonts as $font ) {
				if ( in_array( $font, $all_google_fonts ) ) {
					wpda_form_enqueue_google_font($font);
				}
			}
		}
	}
}

add_action( 'wp_enqueue_scripts', 'wpda_form_typography_google_fonts' );

// Enqueues the Google $font that is passed
function wpda_form_enqueue_google_font($font) {
	$font = explode(',', $font);
	$font = $font[0];
	// Certain Google fonts need slight tweaks in order to load properly
	// Like our friend "Raleway"
	if ( $font == 'Raleway' )
		$font = 'Raleway:100';
	$font = str_replace(" ", "+", $font);
	wp_enqueue_style( "options_typography_$font", "http://fonts.googleapis.com/css?family=$font", false, null, 'all' );
}

function wpda_form_get_font_styles_css($style) {
  $output = '';
  if( $style == 'normal' || $style == 'bold' ) {
   $output .= 'font-weight: ' .$style . ';';
  }
  
  if( $style == 'italic' ) {
   $output .= 'font-style: ' .$style . ';';
  }
  
  if( $style == 'underline' ) {
   $output .= 'text-decoration: ' .$style . ';';
  }
  
  if( $style == 'bold italic') {
   $output .= 'font-style: italic;';
   $output .= 'font-weight: bold;';  }
  
  
  
  if( $style == 'bold underline' ) {
   $output .= 'font-weight: bold;';
   $output .= 'text-decoration: underline;';
  }
  
  if( $style == 'italic underline') {
   $output .= 'font-style: italic; ';
   $output .= 'text-decoration: underline;';
  }
  
  if( $style == 'bold italic underline' ) {
   $output .= 'font-weight: bold;';
   $output .= 'font-style: italic;';
   $output .= 'text-decoration: underline;';
  }
    
  return $output;
}


function  wpda_form_replace_space_with_dash($string) {
    $string = str_replace(" ", "-", $string);
    return $string;
}

// return bytes from mega, giga, or kilos
function wpda_form_return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

// converts color hex value to rgb or rgba depending upon opacity provided or not
function wpda_form_hex2rgb($hex,$opacity = NULL) {
	$hex = str_replace("#", "", $hex);
	
	if(strlen($hex) == 3) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	if($opacity) { 
		$rgb = array($r, $g, $b,$opacity);  
	} else {
		$rgb = array($r, $g, $b);
	}
	$result = implode(",", $rgb); // returns the rgb values separated by commas
	

	if($opacity){
		return "rgba(".$result.")";
	}else {
		return "rgb(".$result.")";  
	}
}
//	return current user role
function wpda_form_cur_user_role() {
	$current_user = wp_get_current_user();
	if(0 == $current_user->ID ) {
		return false;
		
	} else {
		$current_user_roles_arr = $current_user->roles;
		$current_user_role = array_shift($current_user_roles_arr); //administrator
		return $current_user_role;
	}
}
//	check if current user role is allowed to view content
function wpda_form_cur_user_role_allowed($allowed_roles_str) {
	if(!empty($allowed_roles_str)) {
		$current_user = wp_get_current_user();
		if(0 == $current_user->ID ) {
			return false;

		} else {
			$current_user_roles_arr = $current_user->roles;
			$current_user_role = array_shift($current_user_roles_arr); //administrator

			$allowed_roles_arr = explode(',', $allowed_roles_str);
			$allowed_roles_arr = array_map('trim', $allowed_roles_arr);
			if($allowed_roles_str != 'all') {
				if(!in_array($current_user_role, $allowed_roles_arr)) {
					return false;
				}
			}
			return true;
		}
	} else {
		return false;
	}
} 

//	check if current author (e.g username='john_doe') is allowed to view content
function wpda_form_cur_author_allowed($allowed_authors_str) {
	if(!empty($allowed_authors_str)) {
		$current_user = wp_get_current_user();
		if(0 == $current_user->ID ) {
			return false;
		} else {
			$current_username = $current_user->user_login ; 
			
			$allowed_authors_arr = explode(',', $allowed_authors_str);
			$allowed_authors_arr = array_map('trim', $allowed_authors_arr);
			if(!in_array($current_username, $allowed_authors_arr)) {
				return false;
			}
			return true;
		}
	} else {
		return false;
	}
}

// Directory for wpdevart forms uploads
function wpda_form_uploads_dir() { 
	return '/wpdevart-forms-attachments';
}

$wp_upload_dir = wp_upload_dir();
$wpdevart_forms_uploads_dir = wpda_form_uploads_dir();

//	used to change upload directory for wordpress plugin 
function wpda_form_upload_dir_func($wp_upload_dir) {
	$dir = $wp_upload_dir;
	//	$new_dir = '/wpdevart-forms-attachments'.'/'.date('Y').'/'.date('m');
	$new_dir = wpda_form_uploads_dir();
	return array(
		'path'   => $dir['basedir'] .$new_dir ,
		'url'    => $dir['baseurl'] .$new_dir ,
		'subdir' => $new_dir ,
	) + $dir;
}

//	delete direcotry after scaning the given directory and removes the files in it if any.
function wpda_form_rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir"){
            wpda_form_rrmdir($dir."/".$object);
         }else{ 
            @unlink($dir."/".$object);
         }
       }
     }
     reset($objects);
     @rmdir($dir);
  }
}

?>
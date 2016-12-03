<?php
/**
 * Ajax handling wherever needed throughout the project
 * i.e. deleting form, fields, sub fields etc
 *
 * @package wpdevart Forms
 * @since	1.0
 */
 if ( ! defined( 'ABSPATH' ) ) exit;
 
add_action( 'wp_ajax_get_post_information', 'ajax_get_post_information' );
add_action( 'wp_ajax_nopriv_get_post_information', 'ajax_get_post_information' );
function ajax_get_post_information() {
	/*	
		Note :
		======
		please note that we have databse engine = innodb, so if parent field is deleted/updated 
    	child fields will also affected automatically if it relates to them
	*/
	
	global $wpdb;
	global $wpda_form_table;

	if(isset($_REQUEST['delForm']) && current_user_can('manage_options') && wp_verify_nonce( $_REQUEST['wpdevart_form_list_actions_nonce_name'], 'wpdevart_form_list_actions_nonce_value')){
		$form_id = intval($_REQUEST['formId']);
		
		// remove attachemnts of submissions of current form 
		$attachment_fields_ids = $wpdb -> get_results ($wpdb->prepare("SELECT id FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND fieldtype = 'file' ",$form_id));
		if(!empty($attachment_fields_ids)) {
			foreach($attachment_fields_ids as $row):
				$ids[] = $row->id;
			endforeach;
			// nod need prepare because parametr getes prepared databese
			if(count($ids)>1){
				$arr = implode(',', $ids);
				$attachment_fields_submit_values = $wpdb -> get_results ("SELECT field_value FROM ".$wpda_form_table['submissions']." WHERE fk_field_id IN ($arr) AND  field_value IS NOT NULL ");
			} else {
				$id = $ids[0];
				$attachment_fields_submit_values = $wpdb -> get_results ("SELECT field_value FROM ".$wpda_form_table['submissions']." WHERE fk_field_id = $id AND  field_value IS NOT NULL ");
			}
			
			 foreach($attachment_fields_submit_values as $row){
				$result =  $row->field_value; 
				//	get the site url with domain
				$site_url= get_site_url();
				// 	convert abs path to relative
				
				$rel_path=  str_replace($site_url, '', $result);
				if( file_exists(ABSPATH .$rel_path)) {
					unlink(ABSPATH .$rel_path);
				}
			 }
		}
		
		$deleted = $wpdb->delete($wpda_form_table['wpdevart_forms'], array( 'id' => $form_id), array( '%d' ) );
		if(get_option('wpdevart_forms_style')) {
			$wpda_form_styles = get_option('wpdevart_forms_style');
			// search current form styling in option
			$form_style_found = wpda_form_form_styling_exists($form_id, get_option('wpdevart_forms_style')) ;
			if(!empty($form_style_found)) {
				unset($wpda_form_styles[$form_id]);
				// There must be atleast one style record in option, if not delete the option
				if( count($wpda_form_styles) >= 1 ) {
					update_option('wpdevart_forms_style', $wpda_form_styles);
				} else {
					delete_option('wpdevart_forms_style');
				}
			}
		}
		
		exit;
	}

	if(isset($_REQUEST['deleteMainField']) && current_user_can('manage_options') && wp_verify_nonce( $_REQUEST['wpdevart_form_add_edit_new_nonce_name'], 'wpdevart_form_add_edit_new_nonce_value') ) {
		$field_id = $_REQUEST['fieldId'];
		$deleted = $wpdb->delete( $wpda_form_table['fields'], array( 'id' => $field_id  ), array( '%d' ) );
		exit;
	}
    
    if(isset($_REQUEST['delGMSubFields']) && current_user_can('manage_options') && wp_verify_nonce( $_REQUEST['wpdevart_form_add_edit_new_nonce_name'], 'wpdevart_form_add_edit_new_nonce_value')) {
		$field_id = $_REQUEST['fieldId'];
		$deleted = $wpdb->delete( $wpda_form_table['subfields'], array( 'id' => $field_id  ), array( '%d' ) );
		$deleted = $wpdb->delete( $wpda_form_table['subfields'], array( 'id' => $field_id-1  ), array( '%d' ) );
		$deleted = $wpdb->delete( $wpda_form_table['subfields'], array( 'id' => $field_id-2  ), array( '%d' ) );
		exit;
	}
	
	if(isset($_REQUEST['deleteSubField']) && current_user_can('manage_options') && wp_verify_nonce( $_REQUEST['wpdevart_form_add_edit_new_nonce_name'], 'wpdevart_form_add_edit_new_nonce_value')) {
		$subfield_id = $_REQUEST['subfield_id'];
		$deleted = $wpdb->delete($wpda_form_table['subfields'], array( 'id' => $subfield_id  ), array( '%d' ) );
		exit;
	}
	
	//	Delete Submited Form Record 
	if(isset($_POST['delSubmitedFormRecord']) && current_user_can('manage_options') && wp_verify_nonce( $_REQUEST['wpdevart_form_submision_update_name'], 'wpdevart_form_submision_update')) {
		 //	represents id in $wpda_form_table['submit_time'] 
	    $submited_form_record_id = (int) $_POST['submitedFormRecordId'];
		$_POST['attachmentExists'];
	
		if( isset($_POST['attachmentExists']) && $_POST['attachmentExists'] == 1 ) {
			$form_id = $wpdb -> get_var ($wpdb->prepare("SELECT fk_form_id FROM ".$wpda_form_table['submit_time']." WHERE id = %d ",$submited_form_record_id));
			$attachment_fields = $wpdb -> get_results ($wpdb->prepare("SELECT id FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND fieldtype = 'file' ",$form_id));
			
			if(!empty($attachment_fields)) {
				$unlink_attachment_ids = array ();
				foreach($attachment_fields as $attachment_field) {
					$result = $wpdb -> get_var ($wpdb->prepare("SELECT field_value FROM ".$wpda_form_table['submissions']." WHERE fk_field_id = %d AND fk_submit_time_id = %d ",$attachment_field->id,$submited_form_record_id));
					if(!empty($result)) {
						$unlink_attachment_ids[$attachment_field->id] = $result;
						
						//	get the site url with domain
						$site_url = get_site_url();
						// 	convert abs path to relative
						
						$rel_path = str_replace($site_url, '', $result);
						
						if( file_exists(ABSPATH .$rel_path)) {
							unlink(ABSPATH .$rel_path);
						}
					}
				}// endforeach
			}
		} 
	
		$wpdb->delete($wpda_form_table['submit_time'], array( 'id' => $submited_form_record_id) );
		
		if($wpdb->rows_affected == 1) {
			echo $wpdb->rows_affected;
		} else {
			echo 'Error: ' .$wpdb->last_error;
		}
		
		exit;	
	}
	
	//	duplicates a form
	//pro feature
	// deletes all submissions of  a form 
	if(isset($_POST['delAllSubmissions']) && current_user_can('manage_options') && wp_verify_nonce( $_REQUEST['wpdevart_form_submision_update_name'], 'wpdevart_form_submision_update')) {
		$form_id = $_POST['formId'];
		$ids = array();
		
		// remove attachemnts of submissions of current form 
		$attachment_fields_ids = $wpdb->get_results ($wpdb->prepare("SELECT id FROM " .$wpda_form_table['fields'] ." WHERE fk_form_id = %d AND fieldtype = 'file' ",$form_id));
		if(!empty($attachment_fields_ids)) {
			foreach($attachment_fields_ids as $row):
				$ids[] = $row->id;
			endforeach;
			
			if(count($ids)>1) {
				$arr = implode(',', $ids);
				$attachment_fields_submit_values = $wpdb -> get_results ("SELECT field_value FROM ".$wpda_form_table['submissions']." WHERE fk_field_id IN ($arr) AND  field_value IS NOT NULL ");
			} else {
				$id = $ids[0];
				$attachment_fields_submit_values = $wpdb -> get_results ("SELECT field_value FROM ".$wpda_form_table['submissions']." WHERE fk_field_id = $id AND  field_value IS NOT NULL ");
			}
			
			foreach($attachment_fields_submit_values as $row) {
				$result =  $row->field_value; 
				if(!empty($result)){
					//	get the site url with domain
					$site_url = get_site_url();
					// 	convert abs path to relative
					
					$rel_path = str_replace($site_url, '', $result);
					if( file_exists(ABSPATH .$rel_path)) {
						unlink(ABSPATH .$rel_path);
					}
				}
			 }
		}
		
		// remove submissions form database
		$wpdb->delete($wpda_form_table['submit_time'], array( 'fk_form_id' => $form_id) );		
		echo $wpdb->rows_affected; 
		exit;
	}

	//	submits frontend form via ajax call frontend
	if(isset($_POST['process_ajax'])) {
		 print_r( wpda_form_email_submit($_POST['atts']) );
		exit;
	}
	
}//	ajax_get_post_information
?>
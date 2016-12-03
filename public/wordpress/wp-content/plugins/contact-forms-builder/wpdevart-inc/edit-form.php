<?php 
/**
 * Duplicates Form by clicking duplicate icon including all 
 * all form structure and its relevant submission
 *
 * @package wpdevart Forms
 * @since	1.0
 */
 if ( ! defined( 'ABSPATH' ) ) exit;
 if ( ! current_user_can('manage_options') ) exit;
global $wpdb;  
global $wpda_form_table; 
$update_flag = 0;
?>
<div id="wpdevart">
 <div id="wpdevart-forms">
	<form class="wpdevart-general-form" method="post" action="">
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/header.php');?>
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/sidebar.php');?>
			<main class="pull-left">
				<div id="update-status" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i>Form updated successfully </h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /update-status -->
                
                
                <div id="delete-field-modal" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2>Are you sure you want to remove this field?</h2>
						<div class="margin-top-20">
							<button type="submit" class="btn red" id="delMainFieldOk" data-attr="">Delete</button>
							<button type="button" class="btn green" data-dismiss="modal" aria-label="Close">Cancel</button>
                             <span id="loader-icon-delete-field-modal" style="display:none;" class="form-loader">
                            	<img src="<?php echo wpda_form_PLUGIN_URI?>/assets/images/loader.gif" />
                          	 </span>
					   </div>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /delete-field-modal -->
               
               <div id="delete-gm-subfield-modal" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2>Are you sure you want to remove selected sub-fields?</h2>
						<div class="margin-top-20">
							<button type="submit" class="btn red" id="delGMSubFields" data-attr="">Delete</button>
							<button type="button" class="btn green" data-dismiss="modal" aria-label="Close">Cancel</button>
                             <span id="loader-icon-delete-gm-subfield-modal" style="display:none;" class="form-loader">
                            	<img src="<?php echo wpda_form_PLUGIN_URI?>/assets/images/loader.gif" />
                          	 </span>
					   </div>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /delete-field-modal -->
                
                 <div id="deleted-field-modal" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i> Field deleted successfully</h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /delete-field-modal -->
               
               <div id="deleted-gm-subfield-modal" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i> Sub-fields deleted successfully</h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /deleted--gm-subfield-modal -->
                
                
                 <div id="delete-subfield-modal" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2>Are you sure you want to delete this subfield ?</h2>
						<div class="margin-top-20">
							<button type="submit" class="btn red" id="delSubFieldOk" data-attr="">Delete</button>
							<button type="button" class="btn green" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <span id="loader-icon-delete-subfield-modal" style="display:none;" class="form-loader">
                            	<img src="<?php echo wpda_form_PLUGIN_URI?>/assets/images/loader.gif" />
                          	 </span>
					   </div>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /delete-subfield-modal -->
                
                <div id="deleted-subfield-modal" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i> Subfield deleted successfully</h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /delete-subfield-modal -->
                
				<div id="form-created-modal" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i> Form created successfully  </h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /form-created-modal -->
                
				<div id="setting-saved" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i>Settings have been saved </h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /reset-status -->
				<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/help.php');?>
				<div class="tab-content wpdevart-tabs"> 
					<div role="tabpanel" class="tab-pane fade active in" id="add-new">
					  <a href="#" class="btn pull-right fields-info" data-toggle="modal" data-target="#fields-details"><i class="fa fa-question-circle"></i></a>
					   <h1>Edit</h1>
						<?php
						 if(isset($_GET['form_id'])) {
							  
							  $form_id = intval($_GET['form_id']);
							  if(isset($_POST['btn_save_wpdevart_form'])) {
                                  
								  $update_flag = 1;
								  // get user submitted form name
								  $form_name = sanitize_text_field($_POST['form_name']);
								  if($form_name =="") {
									  $form_name="Untitled Form ";
								  }
								  $email_receiver = sanitize_email($_POST['email_receiver']);
								  if(empty($email_receiver)) {
									   $admin_email = get_option( 'admin_email' );
									   $email_receiver = $admin_email;
								  }
								  $email_subject = sanitize_text_field($_POST['email_subject']);
								  if(empty($email_subject)) {
								  	/*$email_subject = 'Query from '.$form_name.' Form \n';*/
								  }
								  
								  $email_body_bottom_msg = sanitize_text_field($_POST['email_body_bottom_msg']);
								  if(empty($email_body_bottom_msg)) {
								  	/*$email_body_bottom_msg = 'This e-mail was sent from  "'.$form_name.'" form created on '.get_bloginfo().' ('. get_site_url().' ) \n';*/
								  }
								  
								  $success_msg = sanitize_text_field($_POST['success_msg']);
								  if(empty($success_msg)) {
									 /*$success_msg  = "Email to {$email_receiver} was sent Successfully \n";*/
								  }
								  $failure_msg = sanitize_text_field($_POST['failure_msg']);
								  if(empty($failure_msg)) {
									 /*$failure_msg  = "There was a problem sending email. Please try again later. Thanks!  \n";*/
								  }
								  								   
								 
								 							  
								  $get_submissions_on =intval( $_POST['get_submissions_on']);
								  if(!is_numeric($get_submissions_on)) {
									 $get_submissions_on  = "1";
								  }
								  
								  $frontend_template = 'default';							  
								  
								  //	Extra settings
								  $error_mgs_heading = sanitize_text_field($_POST['error_mgs_heading']);
								  $field_required_msg = sanitize_text_field($_POST['field_required_msg']);
								  $recaptcha_mismatch_error = sanitize_text_field($_POST['recaptcha_mismatch_error']);
								  $upload_btn_label = ($_POST['upload_btn_label']) ? sanitize_text_field($_POST['upload_btn_label']):"Choose File";
								  $upload_file_size_error_msg = sanitize_text_field($_POST['upload_file_size_error_msg']);
								  $upload_file_extension_error_msg = sanitize_text_field($_POST['upload_file_extension_error_msg']);
								  
								  
								  
								  $param_arr = array( 'frontend_template' => $frontend_template,
													   'email_receiver' => $email_receiver,
													   'email_subject' => $email_subject,
													   'email_body_bottom_msg' => $email_body_bottom_msg,
													   'get_submissions_on' => $get_submissions_on,
													   'success_msg' => $success_msg,
													   'failure_msg' => $failure_msg,	
													   'error_mgs_heading' => $error_mgs_heading,
													   'field_required_msg' => $field_required_msg,
													   'recaptcha_mismatch_error' => $recaptcha_mismatch_error,
													   'upload_btn_label' => $upload_btn_label,
													   'upload_file_size_error_msg' => $upload_file_size_error_msg,
													   'upload_file_extension_error_msg' => $upload_file_extension_error_msg													   
													   ); 
								  $params = json_encode($param_arr); 								  
								  $wpdb->update($wpda_form_table['wpdevart_forms'], array(
																				   'name' =>  wpda_form_append_integer_similiar_names($form_name, $form_id),
																				   'params' => $params
																			    ), 
																			array('id' => $form_id),
																			array('%s', '%s') );
																			
							
								  $main_field_arr = array();
								  $sub_field_arr = array(); 
								  $fieldtype_arr = array();
								  $label_arr = array ();
								  $radio_arr = array ();
								  $options_list_arr = array ();
								  $checkbox_arr = array ();
                                  
								  
								  //	let us fill these array so that we already knew which subfields were selected as default
								  foreach ($_POST as $name => $value) {
									  $explode = explode('_', $name);
									  
									  if($explode[0] == "label") {
										$label_arr[$explode[1]]= sanitize_text_field($name);
									  }
									  
									  //	we are filling these arrays with names of subfields that are in database 
									  if($explode[0] == "fieldtype") {
										$fieldtype_arr[$explode[1]] = sanitize_text_field($_POST[$name]);
									  }
									  
									  if($explode[0] == "sublabel") {
										  if($fieldtype_arr[$explode[1]] == "radio") {
											  if(!empty($explode[2])) {
												$radio_arr[$explode[1]][] = $name;
											  }
										  }
										  if($fieldtype_arr[$explode[1]] == "options_list") {
											  if(!empty($explode[2])) { 
											  	$options_list_arr[$explode[1]][]=$name;
											  }
										  }
										  
										  if($fieldtype_arr[$explode[1]] == "checkbox") {
											  if(!empty($explode[2])) { 
											   $checkbox_arr[$explode[1]][]=$name;
											  }
										  }
                                          
                                         
									  }
                                     
                                
                                      
								  } 
								  
								  //	go through the form and update/add their corresponding fields 
								  foreach ($_POST as $name => $value) {
									
									  //	explode name to get the field name
									  $explode = explode('_', $name);
									 
									  if($explode[0] == "label") {	   
									  	 
										   if(isset($explode[2])) {
											    $fieldtype = sanitize_text_field($_POST['fieldtype_'.$explode[1].'_'.$explode[2]]);
											   	 
												if(isset($_POST['placeholder_'.$explode[1].'_'.$explode[2]])) {
													$placeholder = sanitize_text_field($_POST['placeholder_'.$explode[1].'_'.$explode[2]]);
												}
												
												//	checkboxes, radio button's value will not be accessible if they are not selected
												if(isset($_POST['isRequired_'.$explode[1].'_'.$explode[2]])) {
													$is_required = sanitize_text_field($_POST['isRequired_'.$explode[1].'_'.$explode[2]]);
												} else {
													$is_required = 0;
												}
												
												// parent field contains id according to frontend form position 
												$main_field_arr[$explode[1]] = $explode[2];
												// update
												$record = array('label' => sanitize_text_field($_POST[$name]), 
																'fieldtype' => $fieldtype,
																'placeholder' => $placeholder,
																'is_required' => isset($is_required) ? $is_required : 0,
																'position' => $explode[1]
															);
												$where  = array('id' => $explode[2]);
												$wpdb->update($wpda_form_table['fields'], $record, $where);
										   } else {
											    $fieldtype = sanitize_text_field($_POST['fieldtype_'.$explode[1]]);
												
												if(isset($_POST['isRequired_'.$explode[1]])) {
													$is_required = sanitize_text_field($_POST['isRequired_'.$explode[1]]);
												}
												$record = array('id' => '', 
																'label' => sanitize_text_field($_POST[$name]),
																'fieldtype' => sanitize_text_field($_POST['fieldtype_'.$explode[1]]),
																'placeholder' =>sanitize_text_field( $_POST['placeholder_'.$explode[1]]),
																'is_required' => isset($is_required) ? $is_required : 0,
																'fk_form_id' => $form_id,
																'position' => $explode[1]);
												$wpdb->insert($wpda_form_table['fields'], $record);
												
												$form_field_id = $wpdb->insert_id; // the id of the newly created field in db
												
												// save the newly created parent id in parent field array to determine its childs fields
												$main_field_arr[$explode[1]] = $form_field_id;
										   }
										} // checking for label close
										
										if($explode[0] == "sublabel")  { 
											// checking fieldtype of the subfield 
											if(!empty($explode[2])) {
												if(isset($_POST['fieldtype_'.$explode[1].'_'.$main_field_arr[$explode[1]]])) {
													$fieldtype = sanitize_text_field($_POST['fieldtype_'.$explode[1].'_'.$main_field_arr[$explode[1]]]);
												}
											} else {
												if(isset($_POST['fieldtype_'.$main_field_arr[$explode[1]]])) {
													$fieldtype = sanitize_text_field($_POST['fieldtype_'.$main_field_arr[$explode[1]]]);
												}
											}
											
											if($fieldtype == 'radio') {
												if(!empty($explode[2])) {
													// chcking if current  radio button was selected as defualt 
													$index = array_search($name, $radio_arr[$explode[1]]);
													if(isset($_POST['isDefaultRadio_'.$explode[1]]) && ($_POST['isDefaultRadio_'.$explode[1]] == $index)) {
														$selected_value = 1; //one means checked
													} else {
														$selected_value = 0;   
													}
												}
											}
											
											if($fieldtype == 'options_list') {
												//	chcking if current  option  was selected as defualt 
												if( !empty($explode[2]) ) {
													$index = array_search($name, $options_list_arr[$explode[1]]);
													if(isset($_POST['isDefaultOption_'.$explode[1]]) && ($_POST['isDefaultOption_'.$explode[1]] == $index)) {
														$selected_value=1; //one means checked
													} else {
														$selected_value=0;   
													}
												}
											}
											if($fieldtype == 'checkbox') {
												//	chcking if current  checkbox  was selected as defualt
												if(!empty($explode[2])) {
													$index = array_search($name, $checkbox_arr[$explode[1]]);
													if( isset($_POST['isCheckedCheckbox_'.$explode[1]]) && (in_array($index, $_POST['isCheckedCheckbox_'.$explode[1]])) ) {
														$selected_value = 1; //one means checked
													} else {
														$selected_value = 0;   
													}
												}
											}
											// if database id of the sublabel is set, update the record else create record
											if(!empty($explode[2])) {	
												$_POST[$name] = array_shift($_POST[$name]);
												// subfields
												if( $_POST[$name]=="" ) { $_POST[$name] = "Untitled";}
												
												$sub_field_arr[$explode[1]][] = $explode[2]; // save subfield db id  
												// sublabels are dynamic [] array_shift used to get current sublabel value
												$record = array('label' => sanitize_text_field($_POST[$name]) , "selected_value" => $selected_value);
												$where  = array('id' => $explode[2]);
												$wpdb->update($wpda_form_table['subfields'], $record, $where); 
											} else {
												// Here foreach is used to taget the current $_post[$name][index]
                                              
												foreach($_POST[$name] as $key  =>  &$value) {  
													//	checking if there was already some existing subfields in database
													if($fieldtype_arr[$explode[1]] == "radio"){
														if(isset($radio_arr[$explode[1]])){
															$newly_created_index = count($radio_arr[$explode[1]]) + $key;
															if(isset($_POST['isDefaultRadio_'.$explode[1]]) && (is_numeric($_POST['isDefaultRadio_'.$explode[1]]))) {
																if(isset($_POST['isDefaultRadio_'.$explode[1]]) && ( $_POST['isDefaultRadio_'.$explode[1]] == $newly_created_index )) {
																	$selected_value = 1;
																} else {
																	$selected_value = 0;
																}
															} else {
																$selected_value = 0;
															}
														} else {
															if(isset($_POST['isDefaultRadio_'.$explode[1]]) && (is_numeric($_POST['isDefaultRadio_'.$explode[1]]))) {
																if($_POST['isDefaultRadio_'.$explode[1]] == $key) {
																	$selected_value = 1;
																} else {
																	$selected_value = 0;
																} 
															} else {
																$selected_value = 0;
															}
														}
													}
													if($fieldtype_arr[$explode[1]] == "options_list") {
														if(isset($options_list_arr[$explode[1]])) {
															
															$newly_created_index = count($options_list_arr[$explode[1]])+$key;
															if(isset($_POST['isDefaultOption_'.$explode[1]]) && is_numeric($_POST['isDefaultOption_'.$explode[1]])){
																if(isset($_POST['isDefaultOption_'.$explode[1]]) && ($_POST['isDefaultOption_'.$explode[1]] == $newly_created_index )) {
																	$selected_value = 1;
																} else {
																	$selected_value = 0;
																}
															} else {
																$selected_value = 0;
															}
														} else  {
															if(isset($_POST['isDefaultOption_'.$explode[1]]) && is_numeric($_POST['isDefaultOption_'.$explode[1]])) {
																if($_POST['isDefaultOption_'.$explode[1]] == $key){
																	$selected_value = 1;
																} else{
																	$selected_value = 0;
																}
															} else {
																$selected_value = 0;
															}
														}
													}
													if($fieldtype_arr[$explode[1]] == "checkbox") {
														if(isset($checkbox_arr[$explode[1]])) {
															$newly_created_index=$key+count($checkbox_arr[$explode[1]]);
															if( isset($_POST['isCheckedCheckbox_'.$explode[1]]) && (in_array($newly_created_index, $_POST['isCheckedCheckbox_'.$explode[1]])) ) {
																$selected_value = 1;
															} else {
																$selected_value = 0;
															} 
														} else {
															if(isset($_POST['isCheckedCheckbox_'.$explode[1]]) && (in_array($key, $_POST['isCheckedCheckbox_'.$explode[1]]))) {
																$selected_value=1;
															} else {
																$selected_value=0;
															} 
														}
													}
                                                   
                                                    
													if($value == "") {
														// do not add label default value for heading and separator fieldtype
														if($_POST['fieldtype_'.$explode[1]]!='heading' && $_POST['fieldtype_'.$explode[1]]!='separator')
															$value = "untitled";
													}
													
													
													$record = array('id' => '',
																	'fk_form_id'  => $form_id,
																	'fk_field_id' => $main_field_arr[$explode[1]],
																	'label' => $value,
																	'selected_value' => $selected_value
																);
													$wpdb->insert($wpda_form_table['subfields'], $record);
													$form_field_id = $wpdb->insert_id; 
													
												}
											}
										 
										}
                                      
                                      
                                     
                                      
										
									   //	saving advance options i.e. captcha, reset, cancel button etc
									   //if( $name == "recaptcha" || $name == "reset" ) {
									   if($name == "reset" ) {
											$record = array( 'label' => sanitize_text_field($_POST['reset_btn_label']), 
															   'fieldtype' => $name,
															   'placeholder' => "",
															   'is_required' => $_POST[$name],
															   'fk_form_id' => $form_id,
															   'position' => 9999
														);
											$wpdb->update($wpda_form_table['fields'], $record, array('fieldtype' => $name, 'fk_form_id' => $form_id)); 
									   }
									   
									   //	parent child relationhip because cancel button with have a subfield of redirection url 
									   if($name == "cancel") {
											$record = array('label' =>sanitize_text_field($_POST['cancel_btn_label']), 
															  'fieldtype'  => $name,
															  'is_required' => $_POST[$name],
															  'fk_form_id'  => $form_id,
															  'position' => 9999,
															  );
											$wpdb->update($wpda_form_table['fields'], $record, array("fieldtype" => $name, "fk_form_id" => $form_id)); 
											
											//update cancellation_url in subfield table
											if($_POST['cancel_redirect_url'] == ''){
												$_POST['cancel_redirect_url'] = '#';
											}
											$record=array('label' => "cancellation_url",
															'selected_value' => sanitize_text_field($_POST['cancel_redirect_url']));
											$wpdb->update($wpda_form_table['subfields'],$record, array('label' => "cancellation_url", "fk_form_id" => $form_id));
									   }
									   
									  //	submit button label
									  if($name == "submit_btn_label") {
										  	$_POST['submit_btn_label']= $_POST['submit_btn_label'];
											$record = array('label'  => $_POST['submit_btn_label']);
											$wpdb->update($wpda_form_table['fields'], $record, array('fieldtype'  => 'submit', "fk_form_id" => $form_id)); 
									   }
									 
								  } // foreach
							  } // if(isset($_POST['btn_save_wpdevart_form'])) 
                             
							  //	get currents forms all meta fields 
							  $form_metas = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".$wpda_form_table['wpdevart_forms']." WHERE id=%d",$form_id));
							  foreach($form_metas as $form_meta) {
								  $form_name = stripslashes_deep(esc_html( $form_meta->name ));
								  
								  $params = (array) json_decode($form_meta->params);
								  $frontend_template 		 		= $params['frontend_template'] ;
								  $email_receiver 					= stripslashes_deep(esc_html($params['email_receiver']));
								  $email_subject					= stripslashes_deep(esc_html($params['email_subject']));
								  $success_msg 			 			= stripslashes_deep(esc_html($params['success_msg']));
								  $email_body_bottom_msg 			= stripslashes_deep(esc_html($params['email_body_bottom_msg']));
								  $failure_msg				 		= stripslashes_deep(esc_html($params['failure_msg']));							
								  // Where attachments should go , 1 =Backend & Email , 2 = Only Backend ,3 = Only Email
								  $get_submissions_on	 			= $params['get_submissions_on'] ;
								  
								  if(!is_numeric($get_submissions_on)) {
									 $get_submissions_on  = "1";
								  }

								  
								  // extra settings
								  $error_mgs_heading = stripslashes_deep(esc_html($params['error_mgs_heading']));
								  $field_required_msg = stripslashes_deep(esc_html($params['field_required_msg']));
								  $recaptcha_mismatch_error = stripslashes_deep(esc_html($params['recaptcha_mismatch_error']));
								  $upload_btn_label = stripslashes_deep(esc_html($params['upload_btn_label']));
								  $upload_file_size_error_msg = stripslashes_deep(esc_html($params['upload_file_size_error_msg']));
								  $upload_file_extension_error_msg = stripslashes_deep(esc_html($params['upload_file_extension_error_msg']));
								  
								  
							  }
							  ?>
								<div id="addNewForm" class="generalForm">
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label>Form name</label>
												<input type="text" class="form-control" name="form_name"  value="<?php echo $form_name;?>" placeholder="Form name"/>
											</div><!-- form-group-->
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Shortcode</label>
												<input type="text" class="form-control"  placeholder="Shortcode" 
												value="[wpdevart_forms id=<?php echo $form_id; ?>]" onclick="this.select()" data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="Copy this shortcode and paste anywhere you want to show this form. i.e. pages, posts, widgets" readonly/>
											</div><!-- form-group-->
										</div>
										<div class="col-sm-2" >
										<button class="btn btn-setting" type="button" data-toggle="collapse" data-target="#settings1" aria-expanded="false" aria-controls="settings1">Settings <i class="fa fa-plus margin-left-5"></i></button>
										</div>
                                        
                                        <div class="col-sm-2">
										<button    class="btn btn-setting" type="button" data-toggle="collapse" data-target="#settings2" aria-expanded="false" aria-controls="settings2"> Extras <i class="fa fa-plus margin-left-5"></i></button>
										</div>
                                        
										<div class="clearfix"></div> 
									</div>
									<div class="collapse" id="settings1">
										<div class="collapse-body">
											<div class="col-sm-6">
												<div class="form-group">
													<label>Recepient's email</label>
													<input type="email" name="email_receiver" value="<?php echo $email_receiver; ?>" class="form-control"  placeholder="<?php echo get_option('admin_email');?>" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="On submitting this form ,its data will be sent to the email id provided in the recepient's email field."/>
												</div><!-- form-group-->
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>Subject</label>
													<input type="text" name="email_subject" value="<?php echo $email_subject; ?>" class="form-control"  placeholder='Query from "<?php echo $form_name;?>" Form' data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="Here you can provide the subject of the form."/>
												</div><!-- form-group-->
											</div>
                                            
                                            <div class="col-sm-12">
												<div class="form-group">
													<label>Append custom body message for email at bottom</label>
													<input type="text" name="email_body_bottom_msg" value="<?php echo $email_body_bottom_msg; ?>" class="form-control"  placeholder="What message you want to append in email message body" data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="What message you want to append in email message body"/>
												</div><!-- form-group-->
											</div>
											
											<div class="col-sm-6">
												<div class="form-group">
													<label>Success message</label>
													<input type="text" name="success_msg"  value="<?php echo $success_msg; ?>" class="form-control"  placeholder="Form has been submitted successfully. We'll respond your request shortly. Thanks!" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="This message will be shown at front end when form is being submitted successfully."/>
												</div><!-- form-group-->
                                                
												
												<div class="form-group">
                                                	<label>Form submissions should be received on</label>
                                                    <div class="wpdevart-select">
														<select name="get_submissions_on" class="form-control">
														  <option  value="1" <?php selected($get_submissions_on, '1') ?>>Backend & Email</option>
                                                          <option  value="2" <?php selected($get_submissions_on, '2') ?>>Only Backend </option>
                                                          <option  value="3" <?php selected($get_submissions_on, '3') ?>>Only Email</option>
                                                    	</select>
                                                	</div>
                                                </div>
                                                <div class="form-group text-left">
                                                    <input type="checkbox"   name="isRequiredEmailSubmitUrlRedirect"  id="isRequiredEmailSubmitUrlRedirect" class="wpdevart_pro cboc-content" value="1">
                                                    <label for="isRequiredEmailSubmitUrlRedirect"><span></span> After submit, redirect URL (PRO)</label>
                                                </div>
                                                
												<div class="toggle radio-url email_submit_redirect_url" >
													<div class="form-group">
														<label>Redirect URL </label>
														<input type="text" name="email_submit_redirect_url" value="#" class="form-control"  placeholder="http://example-website.com/specific-page/" data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="If you want to redirect user to some other url after form submission then you can opt for yes and it will toggle a filed for url address." />
													</div><!-- form-group-->
												</div>
                                                
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>Failure message</label>
													<input type="text" name="failure_msg" value="<?php echo $failure_msg; ?>" class="form-control"  placeholder="There was a problem sending email. Please try again later. Thanks!" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="In case of failure this message will be shown at frontend."/>
												</div><!-- form-group-->
												<div class="form-group">
                                                	
													<label>Select front-end template skin <span class="wpdevart_pro_span">(PRO)</span></label>
                                                    	<div class="wpdevart-select">
                                                        	<select name="frontend_template" class="form-control wpdevart_pro">
                                                               <option  value="default"> Default</option>
                                                           <option  value="default_inline"> Default Inline</option>
                                                   		  <option  value="default_rounded"> Default - rounded fields</option>
                                                   		  <option  value="default_full_rounded"> Default - full rounded fields</option>      
                                                    	  <option  value="dark_small" >Dark - small size fields</option>
														  <option  value="dark_medium">Dark - medium size fields</option>
														  <option  value="dark_large" >Dark - large size fields</option>
														  <option  value="dark_inline_small">Dark Inline - small size fields</option>
														  <option  value="dark_inline_medium">Dark Inline - medium size fields</option>
														  <option  value="dark_inline_large">Dark Inline - large size fields</option>
                                                    	  <option  value="light_small" >Light - small size fields</option>
														  <option  value="light_medium">Light - medium size fields</option>
														  <option  value="light_large">Light - large size fields</option>
														  <option  value="light_inline_small" >Light Inline - small size fields</option>
														  <option  value="light_inline_medium">Light Inline - medium size fields</option>
														  <option  value="light_inline_large" >Light Inline - large size fields</option>
														  <option  value="green_small" >Green - small size fields</option>
                                                          <option  value="green_medium" >Green - medium size fields</option>
                                                          <option  value="green_large" >Green - large size fields</option>
                                                          <option  value="green_inline_small" >Green Inline - small size fields</option>
                                                          <option  value="green_inline_medium">Green Inline - medium size fields</option>
                                                          <option  value="green_inline_large" >Green Inline - large size fields</option>
                                                          <option  value="purple_small" >Purple - small size fields</option>
                                                          <option  value="purple_medium">Purple - medium size fields</option>
                                                          <option  value="purple_large">Purple - large size fields</option>
                                                          <option  value="purple_inline_small" >Purple Inline - small size fields</option>
                                                          <option  value="purple_inline_medium" >Purple Inline - medium size fields</option>
                                                          <option  value="purple_inline_large" >Purple Inline - large size fields</option>
                                                          <option  value="newsletter">Newsletter - inline fields</option>
                                                    	</select>
                                                    </div>
												</div>
												
                                                <div class="after_submit_hide_form"  >
                                                    <div class="form-group text-left">
                                                        <input type="checkbox" name="after_submit_hide_form" id="after_submit_hide_form" class="wpdevart_pro cboc-content" value="0">
                                                     	<label for="after_submit_hide_form"><span></span> Hide form after form submission(PRO)</label>
                                                    </div>
                                            	</div>
                                                
											</div>
                                            
                                            <div class="col-md-12">
                                            	<div class="form-group">
                                                    <input type="checkbox" name="isRequiredAutoResponder" id="isRequiredAutoResponder" class="wpdevart_pro cboc-content" value="1" >
                                                    <label for="isRequiredAutoResponder"><span></span>Enable auto responder?(PRO)</label>
                                                </div>
												
                                            </div>
                                            
											<div class="clearfix"></div>
											<div class="col-sm-12"> 
												<button class="btn btn-default pull-right margin-left-10" type="button" data-toggle="collapse" data-target="#settings1" aria-expanded="false" aria-controls="settings1">Close</button>
												<!--<a href="#" class="btn green pull-right" data-toggle="modal" data-target="#setting-saved">Save</a>-->
												<input type="submit"  class="btn green pull-right" value="save" name="btn_save_wpdevart_form" />
												
											</div>
											<div class="clearfix margin-bottom-20"></div>
										</div>
									</div>
                                    
                                    <div class="clearfix"></div>
                                    
                                    
                                    <div class="collapse" id="settings2">
                                        <div class="collapse-body">
                                        
                                        	<div class="alert alert-warning">
                                                Customise error/success messages in your own language. Helpful for multi-language forms..
                                            </div>
                                            
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Error messages heading</label>
                                                    <input type="text" name="error_mgs_heading" value='<?php echo $error_mgs_heading;?>' class="form-control"  placeholder='Please fix the following error(s)' data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="Please fix the following error(s)"/>
                                                </div><!-- form-group-->
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Field required message</label>
                                                    <input type="text" name="field_required_msg" value='<?php echo $field_required_msg;?>' class="form-control"  placeholder='Field is required for label:' data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="Field is required for label:"/>
                                                </div><!-- form-group-->
                                            </div>
                                            <div class="clearfix"></div>
                                             
                                             <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>reCaptch mis-match error</label>
                                                    <input type="text" name="recaptcha_mismatch_error" value="<?php echo $recaptcha_mismatch_error;?>" class="form-control"  placeholder="Upload" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="What message to show if reCapcha value was mis-matched"/>
                                                </div><!-- form-group-->
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Upload button label.<span class="wpdevart_pro_span">(PRO)</span></label>
                                                    <input type="text" name="upload_btn_label" value="Upload" class="wpdevart_pro form-control"  placeholder="Upload" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="What should be the label for upload file button"/>
                                                </div><!-- form-group-->
                                            </div>
                                            <div class="clearfix"></div>
                                            
                                             
                                            
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>upload file size error message <span class="wpdevart_pro_span">(PRO)</span></label>
                                                    <input type="text" name="upload_file_size_error_msg" value='File size is greater than allowed upload limit' class="wpdevart_pro form-control"  placeholder='File size is greater than allowed upload limit' data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="File size is greater than allowed upload limit"/>
                                                </div><!-- form-group-->
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>upload file extension error <span class="wpdevart_pro_span">(PRO)</span></label>
                                                    <input type="text" name="upload_file_extension_error_msg" value='File extension not allowed.' class="wpdevart_pro form-control"  placeholder="File extension not allowed" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="File extension not allowed"/>
                                                </div><!-- form-group-->
                                            </div>
                                           
                                            <div class="clearfix"></div>
                                            
                                            <div class="col-sm-12"> 
                                                <button class="btn btn-default pull-right margin-left-10" type="button" data-toggle="collapse" data-target="#settings1" aria-expanded="false" aria-controls="settings1">Close</button>
                                                <!--<a href="#" class="btn green pull-right" data-toggle="modal" data-target="#setting-saved">Save</a>-->
                                                <input type="submit"  class="btn green pull-right" value="save" name="btn_save_wpdevart_form" />
        
                                            </div>
                                            <div class="clearfix margin-bottom-20"></div>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                    
									<div class="clearfix margin-bottom-30"></div> 
									
									<div class="addForms">
										<div class="col-sm-1 order hidden-xs"><span >#</span></div>
										<div class="col-sm-3 col-md-3  hidden-xs padding-left-0"><label>Label</label></div>
                                        <div class="col-md-3 col-sm-3  hidden-xs padding-left-0 padding-right-0"><label>Placeholder</label></div>
										<div class="col-md-2 col-sm-2  hidden-xs padding-left-0"><label>Input Type</label></div>
										<div class="col-md-2 col-sm-2  hidden-xs padding-left-0"><label>Required</label></div>
										<div class="col-md-1 col-sm-1  hidden-xs btn-actions"><label>Actions</label></div>
										<div class="clearfix border-bottom-white"></div>
										<ul class="sortable list-unstyled">
											<?php 
											//Getting all the fields for current form from db except advanced fields (cpatch,reset,cancel) and submit button having position 9999
											$form_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND position!=9999 ORDER BY position",$form_id));
											$i=1;
											if(count($form_fields)>0) {
												 foreach ($form_fields as $form_field) {
												  $form_field->label = stripslashes_deep(esc_html($form_field->label));
												  $form_field->placeholder = stripslashes_deep(esc_html($form_field->placeholder));
												  
												  $box_class= ($i%2 == 0) ? "bg-even":" bg-odd";
												  ?>
													<li id="li_<?php echo $i;?>_<?php echo $form_field->id; ?>" class="ui-state-default ">
													<div class="addFormBox  <?php echo $box_class;?>"  id="addForm-<?php echo $i; ?>">
														<div class="col-sm-1 order"><span><?php echo $i; ?></span><input type="hidden" name="position_<?php echo $i;?>_<?php echo $form_field->id; ?>"/></div>
														<div class="col-sm-3 col-md-3 padding-left-0">
															<div class="form-group">
																<input type="text" class="form-control"  placeholder="Label" name="label_<?php echo $i;?>_<?php echo $form_field->id; ?>" value="<?php  echo $form_field->label; ?>"/>
															</div><!-- form-group-->
														</div>
                                                        <div class="col-md-3 col-sm-3 padding-right-0 padding-left-0 ">
                                                        	<input type="text" class="form-control"  name="placeholder_<?php echo $i;?>_<?php echo $form_field->id; ?>" data-attr = "Placeholder" placeholder="Placeholder" value="<?php echo $form_field->placeholder; ?>"/>
                                                        </div>
														<div class="col-md-3 col-sm-3 padding-0">
															<div class="form-group wpdevart-select">
															<select name="fieldtype_<?php echo $i;?>_<?php echo $form_field->id; ?>" class="form-control bs-select">
															 <!-- <option>Select Input Type</option> -->
															  <option value="text"     		<?php if($form_field->fieldtype == "text" )			echo "selected";	?> >	Text Field		</option>
															  <option value="email"    		<?php if($form_field->fieldtype == "email")			echo "selected";	?> >	Email			</option>
															  <option value="url"      		<?php if($form_field->fieldtype == "url")			echo "selected"; 	?> >	URL				</option>
															  <option value="number"   		<?php if($form_field->fieldtype == "number")		echo "selected";	?> >	Number			</option>
                                                              <option value="tel"     		<?php if($form_field->fieldtype == "tel")			echo "selected";	?> >	Telephone Number</option>
															  <option value="date"    	 	<?php if($form_field->fieldtype == "date")   		echo "selected";	?> >	Date/Calendar	</option>
															  <option value="file" disabled >	Upload File(PRO)		</option>
															  <option value="password" 		<?php if($form_field->fieldtype == "password") 		echo "selected";	?> >	Password		</option>
															  <option value="textarea" 		<?php if($form_field->fieldtype == "textarea")		echo "selected";	?> >	Text Area		</option>
															  <option value="radio"    		<?php if($form_field->fieldtype == "radio")			echo "selected";	?> >	Radio Buttons 	</option>
															  <option value="checkbox" 		<?php if($form_field->fieldtype == "checkbox")		echo "selected";	?> >	Check Boxes		</option>
															  <option value="options_list"  <?php if($form_field->fieldtype == "options_list") 	echo "selected";	?> >	Dropdown List	</option>
                                                              <option value="heading" 		<?php if($form_field->fieldtype == "heading") 		echo "selected"; 	?> >	Heading			</option>
                                                              <option value="separator" 	<?php if($form_field->fieldtype == "separator")		echo "selected"; 	?> >	Separator/Divider</option>
															  <option value="recaptcha" 	<?php if($form_field->fieldtype == "recaptcha")		echo "selected"; 	?> >	reCaptcha		 </option>
															  <option value="googlemap" 	disabled>	Google Map(PRO)		 </option>
                                                            </select>    
														  </div><!-- form-group-->   
														</div>
														
														<div class="col-md-1 col-sm-1 padding-right-0">
														   <div class="form-group">
															<input type="checkbox"  name="isRequired_<?php echo $i;?>_<?php echo $form_field->id;?>" <?php if($form_field->is_required == 1) echo "checked"; ?> value="1" id="addForm-check-<?php echo $i;?>" value="1"  class='is-required'/><label for="addForm-check-<?php echo $i;?>"><span></span> Yes</label>
                                                            	<span class="pull-right btn-actions padding-left-0"> 
																	<a class="btn green deleteMainField" name="delMainField_<?php  echo $form_field->id;?>" data-toggle="modal" data-target="#delete-field-modal" href="javascript:void(0)"><i class="fa fa-trash"></i></a>
																</span>
														   </div><!-- form-group-->
														</div>
														
														<div class="draggable-handle"><i class="fa fa-arrows-v"></i></div>
														<div class="clearfix"></div>
														
														<div class="newFields">
														<?php 
														 $form_subfields=wpda_form_has_subfield($form_field, $form_id);
														 $j=1;
                                                         $temp_counter = 0;
                                                         $subli=0;
														 if($form_subfields) { 
															foreach($form_subfields as $form_subfield) {
																$form_subfield->label = stripslashes_deep(esc_html($form_subfield->label));
																if($form_field->fieldtype == "radio" ) {?>
																	<div  class='formField'>
																	   <div class='col-sm-1'></div>
																	   <div class='col-sm-1 col-xs-1 order'><span><?php echo $i;?>-<?php echo $j; ?></span></div>
																	   <div class='col-xs-5'>
																		 <div class='form-group'>
																			<input type='text' id="formField-<?php echo $i;?>-<?php echo $j; ?>" class='form-control' name="sublabel_<?php echo $i;?>_<?php echo $form_subfield->id; ?>[]" value="<?php echo $form_subfield->label;?>" placeholder='Radio Button Label'/>
																		 </div><!-- form-group-->
																	   </div>
																	   <div class='col-sm-3 padding-left-0'>
																	   <div class='form-group'>
																		   <input type='radio'  name="isDefaultRadio_<?php echo $i; ?>"  id="newForm-radio-<?php echo $i; ?>-<?php echo $j; ?>" value=<?php echo $j-1; ?> <?php  if($form_subfield->selected_value == 1) echo "checked";?>/>
																		   <label for='newForm-radio-<?php echo $i;?>-<?php echo $j; ?>'><span></span>Default checked</label>
																		   </div></div>
																		   <div class='col-md-2 col-sm-2 col-xs-12 btn-actions'>
																		   <a class='pull-left btn red  delGMSubField' name="delSubField_<?php echo $form_subfield->id;?>" data-toggle="modal" data-target="#delete-subfield-modal"><i class='fa fa-trash'></i></a>
																		   <a class='pull-left btn blue addNewRadio'><i class='fa fa-plus'></i></a>
																		   </div>
																		   <div class='clearfix'>
																	  </div>
																	</div>
																<?php
																}
																if($form_field->fieldtype == "checkbox" ) {?>
																  <div  class='formField'>
																  <div class='col-sm-1'></div>
																  <div class='col-sm-1 col-xs-1 order'><span><?php echo $i;?>-<?php echo $j; ?></span></div>
																  <div class='col-xs-5'>
																	<div class='form-group'>
																		<input type='text' id="formField-<?php echo $i;?>-<?php echo $j; ?>" class='form-control' name="sublabel_<?php echo $i;?>_<?php echo $form_subfield->id; ?>[]" placeholder='Checkbox Label' value="<?php echo $form_subfield->label;?>"/>
																	</div><!-- form-group-->
																  </div>
																  <div class='col-sm-3 padding-left-0'>
																	<div class='form-group'>
																		<input type='checkbox'  name="isCheckedCheckbox_<?php echo $i; ?>[]" id='newForm-check-<?php echo $i; ?>-<?php echo $j; ?>' value='<?php echo $j-1; ?>' <?php  if($form_subfield->selected_value == 1) echo "checked";?> />
																		<label for='newForm-check-<?php echo $i;?>-<?php echo $j; ?>'><span></span>Default checked</label>
																	</div>
																  </div>
																 <div class='col-md-2 col-sm-2 col-xs-12 btn-actions'>
																	<a class='pull-left btn blue deleteSubField' name="delSubField_<?php echo $form_subfield->id;?>" data-toggle="modal" data-target="#delete-subfield-modal"><i class='fa fa-trash'></i></a>
																	<a class='pull-left btn blue addNewCheck'><i class='fa fa-plus'></i></a>
																 </div><div class='clearfix'>
																 </div>
																 </div>
																<?php
																}
																if($form_field->fieldtype == "options_list" ) {?>
																	 <div  class='formField'>
																		<div class='col-sm-1'></div>
																		<div class='col-sm-1 col-xs-1 order'><span><?php echo $i;?>-<?php echo $j; ?></span>
																		</div>
																		<div class='col-xs-5'>
																			<div class='form-group'>
																				<input type='text' id='formField-<?php echo $i;?>-<?php echo $j; ?>' class='form-control' name='sublabel_<?php echo $i;?>_<?php echo $form_subfield->id; ?>[]' value="<?php echo $form_subfield->label;?>" placeholder='Option Label'/>
																			</div><!-- form-group-->
																		</div><div class='col-sm-3 padding-left-0'>
																		<div class='form-group'>
																		
																			<input type='radio'  name='isDefaultOption_<?php echo $i; ?>' id='newForm-option-<?php echo $i; ?>-<?php echo $j;?>' value='<?php echo $j-1;?>' <?php  if($form_subfield->selected_value == 1) echo "checked";?> />
																			<label for='newForm-option-<?php echo $i;?>-<?php echo $j;?>'><span></span>Default selected</label>
																			</div>
																		</div>
																		<div class='col-md-2 col-sm-2 col-xs-12 btn-actions'>
																			<a class='pull-left btn red deleteSubField' name="delSubField_<?php echo $form_subfield->id;?>" data-toggle="modal" data-target="#delete-subfield-modal"><i class='fa fa-trash'></i></a>
																			<a class='pull-left btn blue addNewOption'><i class='fa fa-plus'></i></a>
																		</div>
																	   <div class='clearfix'>
																	   </div>
																	 </div>
																
																 <?php
																}	 
                                                               
                                                               
                                                                
																$j++;
                                                                
															}
														 }
														?>
														</div><!--class="newFields" -->
														
													</div><!-- addFormBox -->
													<div class="clearfix"></div>
												  </li>
												 <div class="clearfix"></div>
												 <?php $i++; 
												 } //foreach ?>
											  <?php 
											} ?>
										</ul>
									</div>
									<?php
										 
									 //getting form submit button and advanced options
									 
									 $form_fields_submit_btn_label = $wpdb->get_var( $wpdb->prepare("SELECT label FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d  AND fieldtype='submit'",$form_id)   );
									 
									 $form_fields_submit_btn_label = stripslashes_deep(esc_html( $form_fields_submit_btn_label ));
									 									 
									 $reset_btn_required = $wpdb->get_var($wpdb->prepare("SELECT is_required FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND fieldtype='reset'",$form_id)  );
									 $reset_btn_required = $reset_btn_required;
									 
									 $reset_btn_label = $wpdb->get_var($wpdb->prepare("SELECT label FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d  AND fieldtype='reset'",$form_id)   );
									 $reset_btn_label = stripslashes_deep(esc_html( $reset_btn_label ));
									
									 
									 
									 $cancel_btn_required = $wpdb->get_var($wpdb->prepare("SELECT is_required FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d  AND fieldtype='cancel'",$form_id)   );
									 $cancel_btn_required = $cancel_btn_required;
									 
									 $cancel_btn_label = $wpdb->get_var($wpdb->prepare("SELECT label FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND fieldtype='cancel'",$form_id)   );
									 $cancel_btn_label = stripslashes_deep(esc_html( $cancel_btn_label ));
								
									
									
									 $cancel_redirect_url = $wpdb->get_var($wpdb->prepare("SELECT selected_value FROM ".$wpda_form_table['subfields']." WHERE fk_form_id = %d  AND label='cancellation_url' ",$form_id));
									 $cancel_redirect_url = stripslashes_deep(esc_html( $cancel_redirect_url ));
									  
								   ?>
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label >Enter Submit Button VALUE</label>
												<input type="text" class="form-control"  name="submit_btn_label" value="<?php echo $form_fields_submit_btn_label; ?>" placeholder="Enter Submit Button Label"/>
											</div><!-- form-group-->
											<div class="margin-bottom-15"></div>
											<input type="submit" name="btn_save_wpdevart_form" class="btn btn-lg green" value="SAVE FORM"/>
										</div>
										<div class="col-sm-8">
                                        	<button class="pull-right btn btn-sm btn-default addNewFormRow margin-bottom-10 margin-top-10"><i class="fa fa-plus"></i> Add New Field</button>
                                            <div class="clearfix"></div>
											<button class="btn btn-setting btn-setting-1 pull-right" type="button" data-toggle="collapse" data-target="#settings3" aria-expanded="false" aria-controls="settings3">Advance Options <i class="fa fa-plus margin-left-5"></i></button>
											<div class="clearfix"></div>
											<div class="collapse" id="settings3">
												<div class="collapse-body">
													<div class="col-sm-12">
														<div class="form-group">
															<label class="col-sm-8 padding-left-0">Do you want to add reset button to the form?</label>
															<div class="col-sm-4 padding-right-0 padding-left-0">
																<input type="radio"  name="reset" <?php if($reset_btn_required == 1) echo "checked"; ?> class="radio_1 reset-btn" id="reset-btn-1" value="1"/><label for="reset-btn-1"><span></span> Yes</label>                             
																<input type="radio"  name="reset" <?php if($reset_btn_required == 0) echo "checked"; ?> class="radio_2 reset-btn" id="reset-btn-2" value="0" /><label for="reset-btn-2"><span></span> No</label>           
															</div>        
														</div><!-- form-group-->
													</div>
                                                    
                                                    <div class="col-sm-12">
                                                        <div class="toggle styling-none reset-btn row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label>Enter Reset Button Label</label>
                                                                    <input type="text" name="reset_btn_label"  class="form-control"  value="<?php echo $reset_btn_label; ?>" <?php if($reset_btn_required == 1) {echo 'required';} ?>  placeholder="Reset" />
                                                                </div><!-- form-group-->
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div><!-- toggle-->
                                                	</div>
                                                    <div class="clearfix"></div>
                                                    
													<div class="col-sm-12">
														<div class="form-group">
															<label class="col-sm-8 padding-left-0">Do you want to show cancel/close button?</label>
															<div class="col-sm-4 padding-right-0 padding-left-0">
                                                                <input type="radio"  name="cancel" <?php if($cancel_btn_required == 1) echo "checked"; ?>  class="radio_1 cancel-btn"  name="forms-radio-cancel" id="forms-radio-cancel-yes" value="1"/><label for="forms-radio-cancel-yes"><span></span> Yes</label>
                                                                <input type="radio"  name="cancel"  <?php if($cancel_btn_required == 0) echo "checked"; ?> class="radio_2 cancel-btn" name="forms-radio-cancel" id="forms-radio-cancel-no" value="0" /><label for="forms-radio-cancel-no" ><span></span> No</label>
															</div>
														</div><!-- form-group-->
													</div>
													<div class="clearfix"></div>
													 <div class="col-sm-12">
															<div class="toggle styling-none cancel-btn row">
																<div class="col-sm-12">
																	<div class="form-group">
																		<label>Enter Cancel Button Label</label>
																		<input type="text"  name="cancel_btn_label" value="<?php echo $cancel_btn_label; ?>" <?php if($cancel_btn_required == 1) {echo "required";} ?>  class="form-control"  placeholder="Cancle"/>
																	</div><!-- form-group-->
																</div>
																<div class="col-sm-12">
																	<div class="form-group">
																		<label>After cancellation, redirect URL to specific location</label>
																		<input  type="text" name="cancel_redirect_url" value="<?php if(!empty($cancel_redirect_url)){echo $cancel_redirect_url;} else {echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];} ?>" <?php if($cancel_btn_required == 1) {echo "required";} ?> class="form-control"  placeholder="http://www.example.com"/>
																	</div><!-- form-group-->
																</div>
																<div class="clearfix"></div>
															</div><!-- toggle-->
														</div>
													 <div class="clearfix"></div>
												</div>
											</div>
										</div>                                       
									</div>
								</div></div><!-- / add-new --></div><!-- .tab-content -->
						 <?php } else {echo "please specify form id ";} ?>
			</main><!-- / main .pull-left -->
			<div class="clearfix"></div>
		</div><!-- / .options-area -->
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/footer.php');
		wp_nonce_field( 'wpdevart_form_add_edit_new_nonce_value','wpdevart_form_add_edit_new_nonce_name' );
		?>
	</form><!-- #wpdevart-general-form -->
  </div><!-- / #wpdevart-forms -->
</div><!-- / wpdevart -->
<?php 
// showing messages [if form was created or was updated]
if(get_transient('wpda_form_form_created_flag')){?>
<script>
jQuery(document).on("ready", function(e) {
//jQuery(document).ready(function(e) {
  jQuery('#form-created-modal').modal('show');
  setTimeout(function(){ 
	  jQuery("#form-created-modal").modal('hide');
  }, 3000);
});
</script> 
<?php 
	if(get_transient('wpda_form_form_created_flag'))delete_transient('wpda_form_form_created_flag');
} 
?>
<?php if($update_flag == 1){ // javascript functions ?>
<script>
jQuery(document).on("ready", function(e) {
//jQuery(document).ready(function(e) {
  jQuery('#update-status').modal('show');
  	setTimeout(function(){ 
		jQuery("#update-status").modal('hide');
	}, 3000);
});
</script> 
<?php  } ?>

<script>
jQuery("[data-target='#settings1']").click(function(e){
	jQuery("#settings2").attr('aria-expanded','false').removeClass('in');
	jQuery('button[data-target="#settings2"]').addClass('collapsed').attr('aria-expanded','false');
});
jQuery("[data-target='#settings2']").click(function(e){
	jQuery("#settings1").attr('aria-expanded','false').removeClass('in');;
	jQuery('button[data-target="#settings1"]').addClass('collapsed').attr('aria-expanded','false');
});
</script>
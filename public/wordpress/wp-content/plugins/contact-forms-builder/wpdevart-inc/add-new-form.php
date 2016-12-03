  <?php
/**
 * Add New Form
 * IMPORTANT:	on Add New form page, we have a settings tab which is collapsed by default
				but in php, its storing form fields although its not visible on page
 *
 * @package wpdevart Forms
 * @since	1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! current_user_can('manage_options') ) exit;

if(isset($_POST['btn_save_wpdevart_form'])  && wp_verify_nonce( $_POST['wpdevart_form_add_edit_new_nonce_name'], 'wpdevart_form_add_edit_new_nonce_value')) {
	global $wpdb; 
	global $wpda_form_table; 
	
	$form_name = sanitize_text_field($_POST['form_name']);
	if($form_name == "") $form_name = "Untitled Form";
	
	$email_receiver = sanitize_email($_POST['email_receiver']);
	if(empty($email_receiver)) $email_receiver = get_option( 'admin_email' );
	
	$email_subject = sanitize_text_field($_POST['email_subject']);
	$email_subject = str_replace("form name", $form_name, $email_subject);
	if(empty($email_subject)) $email_subject = 'Query from '.$form_name.' Form \n';
	
	$email_body_bottom_msg = sanitize_text_field($_POST['email_body_bottom_msg']);
	$email_body_bottom_msg = str_replace("form name", $form_name, $email_body_bottom_msg);
	if(empty($email_body_bottom_msg)) $email_body_bottom_msg = 'This e-mail was sent from  ""'.$form_name.'"" form created on '.get_bloginfo().' ('. get_site_url().' ) \n';
	
	$success_msg = sanitize_text_field($_POST['success_msg']);
	if(empty($success_msg)) $success_msg  = "Form has been submitted successfully. We'll respond your request shortly. Thanks. \n";
	
	$failure_msg = sanitize_text_field($_POST['failure_msg']);
	if(empty($failure_msg)) $failure_msg  = "There was a problem sending email. Please try again later. Thanks! \n";

	//
 
	
	// checkbox may not have checked
	$after_submit_hide_form =  "1"; 
	
	// Where attachments should go , 1 = Backend & Email , 2 = Only Backend ,3 = Only Email
	$get_submissions_on = intval($_POST['get_submissions_on']);
	
	$frontend_template = 'default';	
	
	$cancel_btn_label = sanitize_text_field($_POST['cancel_btn_label']);
	if(empty($cancel_btn_label)) $cancel_btn_label  = "Cancel";
	
	$reset_btn_label = sanitize_text_field($_POST['reset_btn_label']);
	if(empty($reset_btn_label)) $reset_btn_label  = "Reset";
	
	$isRequiredAutoResponder =  "0";
	
	$submit_btn_label = sanitize_text_field($_POST['submit_btn_label']);
	if(empty($submit_btn_label)) $submit_btn_label  = "Submit";
	
	
	//	Extra settings
	$error_mgs_heading = $_POST['error_mgs_heading'];
	$field_required_msg = $_POST['field_required_msg'];
	$recaptcha_mismatch_error = $_POST['recaptcha_mismatch_error'];
	$upload_btn_label = "Choose File";
	$upload_file_size_error_msg ='';
	$upload_file_extension_error_msg = '';
	
	
	// form paramters 
	$param_arr = array('frontend_template' => $frontend_template,
						'email_receiver' => $email_receiver,  
						'email_subject' => $email_subject,
						'email_body_bottom_msg' =>$email_body_bottom_msg,						
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
	// encode form paramsters in json formate
	$params = json_encode($param_arr);
						
	
	//	create form in the database and get the created form id back
	// form object icnludes forms  meta data e.g form name, datatime etc
	$wpdb->insert( $wpda_form_table['wpdevart_forms'], array(  'id'  =>  '',
													  'name' => wpda_form_append_integer_similiar_names($form_name), 
													  'params'=> $params,
													  'datetime' => wpda_form_datetime(time()) 
												));
	//	get the created form id from last query
	$form_id = $wpdb->insert_id;
	
	if(!empty($form_id)) {
		//	go through all form data 
		foreach ($_POST as $name => $value) {
			//explode name to get the field name
			$explode = explode('_', $name);
			
			//	if current element/name in loop is label, then proceed
			if( $explode[0] == "label" ) {
				/*if(empty($value)){
					// do not add label default value for heading and separator fieldtype
					if($_POST['fieldtype_'.$explode[1]]!='heading' && $_POST['fieldtype_'.$explode[1]]!='separator')
						$value = "untitled";
				}
				*/
				
				if( ($_POST['fieldtype_'.$explode[1]] == 'radio') || 
                    ($_POST['fieldtype_'.$explode[1]] == 'checkbox') || 
                    ($_POST['fieldtype_'.$explode[1]] == 'options_list') ) {
					//	Insert record of main field in form's database e.g. Gender
					$record = array(
									'id'			=> '',
									'label'			=> $value, 
									'fieldtype'		=> $_POST['fieldtype_'.$explode[1]],
									'placeholder'	=> $_POST['placeholder_'.$explode[1]],
									'is_required' 	=> isset($_POST['isRequired_'.$explode[1]]) ? $_POST['isRequired_'.$explode[1]] : 0,
									'fk_form_id'  	=> $form_id,
									'position' 	  	=> $explode[1]
								);
						
					$wpdb->insert($wpda_form_table['fields'], $record);
					$form_field_id = $wpdb->insert_id; // newly created form field id
					
					//	now insert sub-fields in database i.e. male, female
					$subfield_label = 'sublabel_'.$explode[1]; // any other field
                    
					if(isset( $_POST[$subfield_label]) ) {
						$subfield_id;
						for( $i = 0; $i < count($_POST[$subfield_label]); $i++) { 
							$selected_value = 0;
							if( $_POST['fieldtype_'.$explode[1]] == 'radio' ) {
								if( isset($_POST['isDefaultRadio_'.$explode[1]]) && ($_POST['isDefaultRadio_'.$explode[1]] == $i) ) {
									$selected_value = 1; // one means checked
								} else {
									$selected_value = 0;   
								}
							}
							if($_POST['fieldtype_'.$explode[1]] == 'options_list') {
								
								if( isset($_POST['isDefaultOption_'.$explode[1]]) && ($_POST['isDefaultOption_'.$explode[1]] == $i) ) {
									$selected_value = 1; // one means checked
								} else {
									$selected_value = 0;   
								}
							}
							if($_POST['fieldtype_'.$explode[1]] == 'checkbox') {
								if( isset($_POST['isCheckedCheckbox_'.$explode[1]]) && ( in_array($i, $_POST['isCheckedCheckbox_'.$explode[1]]) ) ) {
									$selected_value = 1; // one means checked
								} else {
									$selected_value = 0;   
								}
							}
							
							if($_POST[$subfield_label][$i] == "") $_POST[$subfield_label][$i] = "untitled";
							
							$record = array('id' => '',
												'fk_form_id' => $form_id,
												'fk_field_id' => $form_field_id,
								  				'label' => $_POST[$subfield_label][$i],
								  				'selected_value' => $selected_value
										);
							$wpdb->insert($wpda_form_table['subfields'], $record);
							$subfield_id = $wpdb->insert_id;
						}
					}
                    

				} else {
					//	if fieldtype is main field i.e. text, textarea, email, password etc
					$record = array('id' => '',
										'label'  => sanitize_text_field($value), 
										'fieldtype'  => sanitize_text_field($_POST['fieldtype_'.$explode[1]]),
										'placeholder' =>sanitize_text_field( $_POST['placeholder_'.$explode[1]]),
										'is_required' => isset($_POST['isRequired_'.$explode[1]]) ? sanitize_text_field($_POST['isRequired_'.$explode[1]]): 0,
										'fk_form_id'  => intval($form_id), 'position' => sanitize_text_field($explode[1])
									);
					$wpdb->insert($wpda_form_table['fields'], $record);
					$form_field_id=$wpdb->insert_id; // get newly created form field id
				} // if(radio, checbox, options_list)
				
			} // $explode[0]=="label"
			
			//	saving advanced options i.e. captha, reset, cancel button etc
			if($name=="reset" ) {
				$record = array('id' => '',
								'label'  =>  $reset_btn_label, 
								'fieldtype'  => $name,
								'is_required' => sanitize_text_field($_POST[$name]),
								'fk_form_id'  => $form_id,
								'position' => 9999
							);
				$wpdb->insert($wpda_form_table['fields'], $record); 
			}
			
			// parent child relationhip because cancel button with have a subfield of redirection url 
			if($name=="cancel") {
				$record = array('id' => '', 'label'  => $cancel_btn_label, 
								'fieldtype'  => $name,
								'is_required' => $_POST[$name],
								'fk_form_id'  => $form_id,
								'position' => 9999,
								);
				$wpdb->insert($wpda_form_table['fields'],$record); 
				$form_field_id = $wpdb->insert_id; // newly created form field id
				
				//	insert cancellation_url in subfield table
				if($_POST['cancel_redirect_url'] == '') {
					$_POST['cancel_redirect_url'] = '#';
				}
				$record=array('id' => '',
								'fk_form_id' => $form_id,
								'fk_field_id' => $form_field_id,
							  	'label' => "cancellation_url",
							  	'selected_value' => $_POST['cancel_redirect_url']
							);
				$wpdb->insert($wpda_form_table['subfields'], $record);
				$subfield_id=$wpdb->insert_id;
			}
			
			//	btn_save_wpdevart_form is also form field, so we will also store it into form fields tables
			if($name=="btn_save_wpdevart_form") {
				$record = array('id' => '',
								'label'  => $submit_btn_label, 
								'fieldtype'  => 'submit',
								'is_required' => 1,
								'fk_form_id'  => $form_id,
								'position' => 9999
							);
				$wpdb->insert($wpda_form_table['fields'], $record); 
			}
		} //	foreach ($_POST as $name  =>  $value) 
		
		// as form  made in database redirect to wpda_form_edit_form()
		// use transient
		set_transient( 'wpda_form_form_created_flag','true', 10 );
		
		$location = admin_url("admin.php?page={$this->slug['edit']}&form_id={$form_id}");
		if($location) {
			echo "<script>location.href='$location';</script>";
			exit;
		}			
	} else {
		echo "Error creating the Form please try again later Thanks!";
		echo $wpdb->last_error;
		exit;
	} //!empty($form_id)
}
?>
<title>Add new form </title>

     <div id="wpdevart">
     <div id="wpdevart-forms">
        <form class="wpdevart-general-form" method="post" action="#">
           <?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/header.php');?>
			<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/sidebar.php');?>
                <main class="pull-left">
                    <div id="update-status" class="modal fade">
                      <div class="modal-dialog">
                      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <div class="modal-content">
                            <h2><i class="fa fa-check"></i>Options Saved </h2>
                        </div><!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                    </div><!-- /update-status -->
                    <div id="reset-status" class="modal fade">
                      <div class="modal-dialog">
                      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <div class="modal-content">
                            <h2><i class="fa fa-check"></i>Settings have been reset </h2>
                        </div><!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                    </div><!-- /reset-status -->
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
                            <h1>Add New Form</h1>
                            <p class="successMessage"><i class="icon-check"></i>Form has been added successfully.</p>
                                <div id="addNewForm" class="generalForm">
                                	<div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Form name</label>
                                                <input type="text" class="form-control" name="form_name" placeholder="Form name"/>
                                            </div><!-- form-group-->
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                              
                                              <label>Shortcode</label>
                                              <input type="text" class="form-control"  placeholder="Shortcode will be displayed here" value="" onclick="this.select()" data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="Copy this shortcode and paste anywhere you want to show this form. i.e. pages, posts, widgets" disabled="disabled"/>
                                             
                                             </div><!-- form-group-->
                                        </div>
                                        <div class="col-sm-2" >
                                        <button class="btn btn-setting" type="button"  data-toggle="collapse" data-target="#settings1" aria-expanded="false" aria-controls="settings1">Settings <i class="fa fa-plus margin-left-5"></i></button>
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
													<input type="email" name="email_receiver" value="<?php echo get_option('admin_email');?>" class="form-control"  placeholder="<?php echo get_option('admin_email');?>" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="On submitting this form ,its data will be sent to the email id provided in the recepient's email field."/>
												</div><!-- form-group-->
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>Subject</label>
													<input type="text" name="email_subject" value='Query from "form name" Form' class="form-control"  placeholder='Query from "form name" Form' data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="Here you can provide the subject of the form."/>
												</div><!-- form-group-->
											</div>
                                            <div class="col-sm-12">
												<div class="form-group">
													<label>Append custom body message for email at bottom</label>
													<input type="text" name="email_body_bottom_msg" value='This e-mail was sent from "form name" Form created on  "<?php echo get_bloginfo();?>" (<?php echo get_site_url(); ?>)' class="form-control"  placeholder="What message you want to append in email message body" data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="What message you want to append in email message body"/>
												</div><!-- form-group-->
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label>Success message</label>
													<input type="text" name="success_msg"  value="Form has been submitted successfully. We'll respond your request shortly. Thanks!" class="form-control"  placeholder="Form has been submitted successfully. We'll respond your request shortly. Thanks!" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="This message will be shown at front end when form is being submitted successfully."/>
												</div><!-- form-group-->
												
												<div class="form-group">
                                                    <label>Form submissions should be received on</label>
                                                    <div class="wpdevart-select">
														<select name="get_submissions_on" class="form-control">
														  <option  value="1">Backend & Email</option>
                                                          <option  value="2">Only Backend </option>
                                                          <option  value="3">Only Email</option>
                                                    	</select>
                                                	</div>
                                                </div>
												<div class="form-group text-left"> 
                                                     <input type="checkbox" name="isRequiredEmailSubmitUrlRedirect" id="isRequiredEmailSubmitUrlRedirect" class="wpdevart_pro cboc-content" value="1">
                                                    <label for="isRequiredEmailSubmitUrlRedirect"><span></span> After submit, redirect URL (PRO)  </label>
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
													<input type="text" name="failure_msg" value="There was a problem sending email. Please try again later. Thanks!" class="form-control"  placeholder="There was a problem sending email. Please try again later. Thanks!" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="In case of failure this message will be shown at front end."/>
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
												
												<div class="after_submit_hide_form toggle show-hide-message radio-url " style='display:block;' >
                                                    <div class="form-group text-left">
                                                     <input type="checkbox" name="after_submit_hide_form" id="after_submit_hide_form" class="wpdevart_pro cboc-content" value="0">
                                                     <label for="after_submit_hide_form"><span></span> Hide form after form submission(PRO)</label>
                                                    </div>
                                            	</div>
											</div>
                                            
                                            <div class="col-md-12">
                                            	<div class="form-group">
                                                    <input type="checkbox" name="isRequiredAutoResponder" id="isRequiredAutoResponder" class="wpdevart_pro cboc-content" value="1">
                                                    <label for="isRequiredAutoResponder"><span></span> Enable auto responder?(PRO)</label>
                                                </div>
												<div class="autoResponderToggle" style="display:none;">
													<div class="form-group">
														<label>Auto responder email subject</label>
														<input type="text" name="autoResponderSubject" placeholder='Auto responder email subject "<?php  echo get_bloginfo ();?>"' class="form-control"   data-trigger="focus" data-toggle="tooltip"  data-placement="bottom"  />
														<label>Auto responder message body</label>
                                                        <textarea name="autoResponderMessage" rows="3" placeholder='This is acknowledgement of your form submission on "<?php echo get_bloginfo(); ?>" (<?php echo get_site_url(); ?>) <?php echo "&#013;&#013;";?>Thanks' class="form-control autoResponderMessage"></textarea>
                                                    </div><!-- form-group-->
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
                                                    <input type="text" name="error_mgs_heading" value='Please fix the following error(s)' class="form-control"  placeholder='Please fix the following error(s)' data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="Please fix the following error(s)"/>
                                                </div><!-- form-group-->
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Field required message</label>
                                                    <input type="text" name="field_required_msg" value='Field is required for label:' class="form-control"  placeholder='Field is required for label:' data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="Field is required for label:"/>
                                                </div><!-- form-group-->
                                            </div>
                                            <div class="clearfix"></div>
                                             
                                             <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>reCaptch mis-match error</label>
                                                    <input type="text" name="recaptcha_mismatch_error" value="reCaptcha value mis-matched" class="form-control"  placeholder="Upload" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="What message to show if reCapcha value was mis-matched"/>
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
                                                    <label>upload file size error message</label>
                                                    <input type="text" name="upload_file_size_error_msg" value='File size is greater than allowed upload limit' class="form-control"  placeholder='File size is greater than allowed upload limit' data-trigger="focus" data-toggle="tooltip"  data-placement="bottom" data-title="File size is greater than allowed upload limit"/>
                                                </div><!-- form-group-->
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>upload file extension error</label>
                                                    <input type="text" name="upload_file_extension_error_msg" value="File extension not allowed." class="form-control"  placeholder="File extension not allowed" data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="File extension not allowed"/>
                                                </div><!-- form-group-->
                                            </div>
                                           
                                            <div class="clearfix"></div>
                                            
                                            
                                            
                                            
                                              
                                            <div class="col-sm-12"> 
                                                <button class="btn btn-default pull-right margin-left-10" type="button"  data-toggle="collapse" data-target="#settings1" aria-expanded="false" aria-controls="settings1">Close</button>
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
                                        <ul class="sortable list-unstyled ui-sortable">
                                        <?php for($i=1;$i<=2;$i++){ $box_class= ($i%2==0) ? "bg-even":" bg-odd"; ?>
                                        <li id="li_<?php echo $i;?>" class="ui-state-default">
                                        <div class="addFormBox <?php echo $box_class;?>" id="addForm-<?php echo $i;?>">
                                            <div class="col-sm-1 order"><span><?php echo $i;?></span><input type="hidden" name="position_<?php echo $i;?>"/></div>
                                            <div class="col-sm-3 col-md-3 padding-left-0 ">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="label_<?php echo $i;?>" placeholder="Label"/>
                                                </div><!-- form-group-->
                                            </div>
                                            <div class="col-md-3 col-sm-3 padding-right-0 padding-left-0">
                                            	<input type="text" class="form-control"  name="placeholder_<?php echo $i;?>" data-attr = "Placeholder" placeholder="Placeholder"/>
                                            </div>
                                            <div class="col-md-3 col-sm-3 padding-0">
                                                <div class="form-group">
                                                	 <div class="wpdevart-select">
                                                    	<select name="fieldtype_<?php echo $i;?>" class="form-control bs-select" >
                                                          <!--<option>Select Input Type</option>-->
                                                          <option value="text">Text Field</option>
                                                          <option value="email">Email</option>
                                                          <option value="url">URL</option>
                                                          <option value="number">Number</option>
                                                          <option value="tel">Telephone Number</option>
                                                          <option value="date">Date/Calendar</option>
                                                          <option disabled value="file">Upload File(PRO)</option>
                                                          <option value="password">Password</option>
                                                          <option value="textarea">Text Area</option>
                                                          <option value="radio">Radio Buttons</option>
                                                          <option value="checkbox">Check Boxes</option>
                                                          <option value="options_list">Dropdown List</option>
                                                          <option value="heading">Heading</option>
                                                          <option value="separator">Separator/Divider</option>
                                                          <option value="recaptcha">reCaptcha</option>
                                                          <option disabled value="googlemap">Google Map(PRO)</option>
                                                        </select>  
                                                  	</div>
                                                 </div><!-- form-group-->
                                            </div>  
                                            <div class="col-md-1 col-sm-1 padding-right-0">
                                                <div class="form-group">
                                                    <input type="checkbox"  name="isRequired_<?php echo $i;?>" id="addForm-check-<?php echo $i;?>" value="1"  class='is-required'/><label for="addForm-check-<?php echo $i;?>"><span></span> Yes</label>  
                                                    <span class="pull-right btn-actions padding-left-0">
                                               			<button class="btn green removeFormRow"><i class="fa fa-trash"></i></button>
                                            		</span>                                              
                                            	</div><!-- form-group-->
                                            </div>
                                            <div class="draggable-handle"><i class="fa fa-arrows-v"></i></div>
                                            <div class="clearfix"></div>
                                            <div class="newFields"></div>
                                        </div><!-- addFormBox -->
                                        </li>
                                        <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="row">
                                    	<div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label >Enter Submit Button Value</label>
                                              	<input type="text" class="form-control"  name="submit_btn_label" placeholder="Submit"/>
                                            </div><!-- form-group-->
                                            <input type="submit" name="btn_save_wpdevart_form" class="btn btn-lg green " value="CREATE FORM"/>
                                        </div>
                                        <div class="col-sm-8">
                                       		 <button class="pull-right btn btn-default btn-sm addNewFormRow margin-bottom-10"><i class="fa fa-plus"></i> Add New Field</button>
                                             <div class="clearfix"></div>
                                        	<button class="btn btn-setting btn-setting-1 pull-right" type="button" data-toggle="collapse" data-target="#settings3" aria-expanded="false" aria-controls="settings3">Advance Options <i class="fa fa-plus margin-left-5"></i></button>
                                            <div class="clearfix"></div>
                                            <div class="collapse" id="settings3">
                                                <div class="collapse-body">
                                                	  <div class="col-sm-12">
                                                    	<div class="form-group">
                                                            <label class="col-sm-8 padding-left-0">Do you want to add reset button to the form?</label>
                                                            <div class="col-sm-4 padding-right-0 padding-left-0">
                                                                <input type="radio"  name="reset" class="radio_1 reset-btn" id="reset-btn-1" value="1"/><label for="reset-btn-1"><span></span> Yes</label>                             
                                                                <input type="radio"  name="reset" class="radio_2 reset-btn" id="reset-btn-2" value="0" checked/><label for="reset-btn-2"><span></span> No</label>           
                                                        	</div>        
                                                        </div><!-- form-group-->
                                                    </div>
                                                    
                                                    <div class="col-sm-12">
                                                        <div class="toggle styling-none reset-btn row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label>Enter Reset Button Label</label>
                                                                    <input name="reset_btn_label" type="text" class="form-control"  value="Reset" placeholder="Reset" />
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
                                                                <input type="radio" class="radio_1 cancel-btn"  name="cancel" id="forms-radio-cancel-yes" value="1"/><label for="forms-radio-cancel-yes"><span></span> Yes</label>
                                                                <input type="radio"  class="radio_2 cancel-btn" name="cancel" id="forms-radio-cancel-no" value= "0" checked/><label for="forms-radio-cancel-no" ><span></span> No</label>
                                                        	</div>
                                                        </div><!-- form-group-->
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="col-sm-12">
                                                        <div class="toggle styling-none cancel-btn row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label>Enter Cancel Button Label</label>
                                                                    <input name="cancel_btn_label" type="text" class="form-control"  value="Cancel" placeholder="Cancle" />
                                                                </div><!-- form-group-->
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label>After cancellation, redirect URL to specific location</label>
                                                                    <input type="text"  name="cancel_redirect_url" class="form-control"  value="#" placeholder="http://www.example.com"/>
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
                              </div>
                        </div><!-- / add-new -->
                    </div><!-- .tab-content -->
                </main><!-- / main .pull-left -->
            <div class="clearfix"></div>
            </div><!-- / .options-area -->
           <?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/footer.php'); 
		  	 	wp_nonce_field( 'wpdevart_form_add_edit_new_nonce_value','wpdevart_form_add_edit_new_nonce_name' );
		   ?>
           
        </form><!-- #wpdevart-general-form -->
    </div><!-- / #wpdevart-forms -->
</div><!-- / wpdevart -->
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
<?php
/**
 * wpdevart_template() function is used to create/show the frontEnd form 
 * based on shortcode and attributes (id) we provide e.g short code 
 * [wpdevart_forms id=1] will create form whose id is 1;
 * This function is called in function wpda_form_forms_shortcode() 
 *
 * @package wpdevart Forms
 * @since	1.0
 */
 if ( ! defined( 'ABSPATH' ) ) exit;
 
function wpdevart_template($atts) {
	global $wpdb;
	
	//	databse plugin tables info	
	global $wpda_form_table;
	
	$current_user = wp_get_current_user();
	
	if(isset($atts['id']) && $atts['id']!= '') {
		$form_id = $atts['id'];	//	id provided in short code e.g [wpdevart_forms id=1]

		$confirm_form_id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM ". $wpda_form_table['wpdevart_forms'] ." WHERE id=%d",$form_id));
		if($form_id == $confirm_form_id) 
		{
			$error_msg = "";
			global $arr;
			// allowed tags for form's textarea

			$allowed_html_tags = array('span' => array('class' => array()),
								'br' => array(),
								'strong' => array('class' => array()),
								'b' => array('class' => array()),
								'i' => array('class' => array()),
								'h1' => array('class' => array()),
								'h2' => array('class' => array()),
								'h3' => array('class' => array()),
								'h4' => array('class' => array()),
								'h5' => array('class' => array()),
								'h6' => array('class' => array()),
							);

			//	by default donot show form name on frontend 
			$show_form_name = "no";

			$hide_field_labels = 0;
			if(isset($atts['name'])) {
				$show_form_name = strtolower($atts['name']);
			}
			//	form status, public or private
			$status = 'public';
			if(isset($atts['status'])) {
				$status = strtolower($atts['status']);
			}
			
			if(!isset($atts['status']) && isset($atts['role'])) {
				echo "Please specify status either private or public e.g, [wpdevart_forms id=1 status='private']";
			} else {
			
				if( ($status == 'private' && (!isset($atts['role']) || empty($atts['role']))) ) {
					echo "Please specify user role e.g [wpdevart_forms id=1 status='private' role='admin,editor'] ";
				} else {
					
					if($status == "private") {
						
						if( 0 == $current_user->ID ) {
							//	$error_msg_arr[] = "You must be logged in to view that form. Thanks";
						} else {
							if(!isset($atts['role']) && !isset($atts['author']) ) {
								$error_msg = "Specify user role or author e.g [wpdevart_forms id=1 status='private' role='administrator']";
							} else {
								
								if(isset($atts['role']) && !isset($atts['author']) ) {
									if(!wpda_form_cur_user_role_allowed($atts['role'])) {
										$error_msg =  "You do not have permission to view this form. Thanks. <br>";
									}
								} 
								
								if(isset($atts['author']) && !isset($atts['role']) ) {
									if( ! wpda_form_cur_author_allowed($atts['author']) ) {
										$error_msg = "You do not have permission to view this form. Thanks.<br>";
									}
								}
								// both are set
								if(isset($atts['author']) && isset($atts['role']) ) {
									
									if( wpda_form_cur_user_role_allowed($atts['role']) || wpda_form_cur_author_allowed($atts['author']) ) {
										//ok
									} else {
										$error_msg =  "You do not have permission to view this form. Thanks.<br>";
									}
								}
							}
						}
					 }
					
					if(empty($error_msg)) {
					
						$form_params = (array) json_decode($wpdb->get_var($wpdb->prepare( "SELECT params FROM ". $wpda_form_table['wpdevart_forms'] ." WHERE id=%d",$form_id)));
	
						if(isset($form_params['frontend_template'])) {
							$frontend_template = $form_params['frontend_template'];
						}
						
						
	
						// include styles and scripts for form
						$arr = array("frontend_template" => $frontend_template);
						wpda_form_enqueue_frontend_styles_scripts($arr);
						unset($arr);
	
						// check whether labels to show on frontend or not 
						$all_styles = get_option('wpdevart_forms_style');
						if( $all_styles ) {
							$form_style_found = wpda_form_form_styling_exists ( $form_id, $all_styles ); //return 1,0
							if($form_style_found) {
								if(isset($all_styles[$form_id]['wpda_form_label_show_hide']) && ($all_styles[$form_id]['wpda_form_label_show_hide'] == "1" )){
									 $hide_field_labels = 1;
								}
							}
						}
	
						//	get the form name  label from the database based on id we get from the short code
						$form_name = $wpdb->get_var( $wpdb->prepare("SELECT name FROM ".$wpda_form_table['wpdevart_forms']." WHERE id=%d",$form_id)); // e.g contact form
						$form_name = stripslashes_deep(wp_kses($form_name, $allowed_html_tags)); 
						?>
	
	
						<form  name= "wpdevart_frontend_form_<?php echo $form_id;?>" id='wpdevart-forms-<?php echo $form_id;?>' class="wpdevart-forms <?php echo $frontend_template;?>-skin forms-general-styling" method='post'  action='' enctype='multipart/form-data'>
	
						<?php
						if($show_form_name == "yes") {
							echo "<h1>$form_name</h1>";
						}
	
						$form_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpda_form_table['fields']." WHERE fk_form_id =%d AND position < 9999 ORDER BY position ASC",$form_id));
	
                        
                        $initData = '';
						// loop through the form fields
						foreach ($form_fields as $key => $form_field) {
	
							//	we will now check if the current field [e.g skills] have subfields[e.g programming, designing] or not 
							$field_subfields = wpda_form_has_subfield($form_field, $form_id); // wpda_form_has_subfield() defined below this document
                            
							// subfields found
							if( !empty($field_subfields) ) {	
								if($form_field->fieldtype == "radio" || $form_field->fieldtype == "checkbox" || $form_field->fieldtype == "options_list") {
									// count subfields related to that parent field 
									$field_subfields_count = count($field_subfields);
									?>
									<div class="wpdevart-sub-fields">
	
									<?php if($hide_field_labels == 0) {?> 
											<label><?php echo stripslashes_deep(wp_kses($form_field->label, $allowed_html_tags)); ?></label>
									<?php } ?>
	
									<div class="wpdevart-sub-fields-inner">
									<?php 
									// loop through the subfields to show them on page
	
									$field_counter = 1;
									foreach($field_subfields as $key => $field_subfield) { 	
										$field_subfield_label_sanitized = stripslashes_deep(wp_kses($field_subfield->label, $allowed_html_tags));
										
										if($form_field->fieldtype == "radio" || $form_field->fieldtype == "checkbox" ) { 
											$is_checked = "";
											if(isset($_POST['sublabel_'.$form_field->id]) && (!empty($_POST['sublabel_'.$form_field->id]))) {
												if( in_array($key, $_POST['sublabel_'.$form_field->id])) {	
													$is_checked = "checked";
												}
											} else {	
												if( $field_subfield->selected_value == 1 ) $is_checked = "checked";
											}
											?>
											<span class="checkboxradios">
												<input type="<?php echo $form_field->fieldtype; ?>"   name="sublabel_<?php echo $form_field->id; ?>[]" id="sublabel_<?php echo $form_field->id; ?>_<?php echo $field_counter;?>" value="<?php echo esc_html($field_subfield->label); ?>" <?php echo $is_checked; ?> />
                                                <label for="sublabel_<?php echo $form_field->id; ?>_<?php echo $field_counter;?>"><span></span> <?php echo $field_subfield_label_sanitized; ?></label>
											</span>
											<?php
										}
										$field_counter++; 
									}// foreach close
	
                                    
                                    
                                    

                                   
                                    
                                    
									if($form_field->fieldtype == "options_list" ) {
	
										echo "<div class='wpdevart-select'><select name='label_".$form_field->id."' class='form-control'>";
										foreach($field_subfields as $key => $field_subfield) {
											$field_subfield->label = stripslashes_deep(esc_html( $field_subfield->label )); 
											?>
											<!-- defualt options value will be equal to the $key in the loop -->
											<option <?php echo $field_subfield->label; ?> <?php  if(isset($_POST['label_'.$form_field->id])){ if($_POST['label_'.$form_field->id] == $key) echo "selected";   } else { if($field_subfield->selected_value == 1) echo "selected";}?> >
											<?php  echo $field_subfield->label; ?>
											</option><?php
										}
										echo "</select></div>";
									}
									?>
									</div></div>
									<?php
								}
							} else {
								if($form_field->fieldtype == "textarea") {
									?>
									<div class="wpdevart-textarea">
										<?php if($hide_field_labels == 0) {?> 
												<label><?php echo stripslashes_deep(wp_kses($form_field->label, $allowed_html_tags)); ?> </label> 
										<?php  } ?> 
										<textarea name="label_<?php echo $form_field->id; ?>" placeholder="<?php echo stripslashes_deep(esc_attr($form_field->placeholder, $allowed_html_tags));?>"><?php if(isset($_POST['label_'.$form_field->id])){echo stripslashes_deep(wp_kses($_POST['label_'.$form_field->id], $allowed_html_tags));}?></textarea> 
									</div>
									<?php
								} else {
									if($form_field->fieldtype == "heading" || $form_field->fieldtype == "separator") {
										if($form_field->fieldtype == "heading") {
											echo "<h3 class='wf-custom-heading'>".stripslashes_deep(wp_kses($form_field->label, $allowed_html_tags))."</h3>";
										}
										if($form_field->fieldtype == "separator") {
											if($form_field->label == '') {
												?>
												<div class="separator-with-title" >
													<h3 class="separator-before-after"> </h3>
												</div>
												<?php
											} else {
												?>
												<div class="separator-with-title">
													<h3 class="separator-title"><?php echo stripslashes_deep(wp_kses($form_field->label, $allowed_html_tags));  ?></h3>
												</div>
												<?php
											}
										}
									?>
									<?php
									} else {
										if($form_field->fieldtype == "recaptcha") {
											   $num1 = rand(1,10);
											   $num2 = rand(2,10);
											   $recaptcha_value = $num1 + $num2; // orignal reCaptcha value
											  ?>
						
											  <div class="wpdevart-input-field field-captcha" style="margin-bottom:20px;">
												<label><?php echo $num1; ?>  + <?php echo $num2; ?> = ? </label>
												<div class="input-text input-field-inner">
													<input type="text"  name="submitedRecaptchaValue[]" placeholder="please enter your answer " <?php if($form_field->is_required == 1) echo "required"; ?> />
													<input type="hidden" name="recaptchaSumValue[]" value="<?php echo $recaptcha_value;?>" />
                                                    <input type="hidden" name="isCaptchaRequired[]" value="<?php echo $form_field->is_required;?>" />
												</div>
											  </div>
											  <?php
										} else {
											//	other than text areas,heading and separator and captcha below inputs will be printed 
										?>
                                            <!--bfh-number -->
                                            <div class="wpdevart-input-field <?php if($form_field->fieldtype == "date" ){ ?> input-date<?php }elseif( $form_field->fieldtype == "password"  ){?> input-password <?php } ?>">
                                                <?php if($hide_field_labels == 0) {?> 
                                                        <label><?php echo stripslashes_deep(wp_kses($form_field->label, $allowed_html_tags)); ?></label>
                                                <?php } ?>
        
                                                <div class="input-<?php echo $form_field->fieldtype;?> input-field-inner">
                                                    
                                                    <input  type="<?php if($form_field->fieldtype == "date" || $form_field->fieldtype == "tel"){ echo "text"; }else{ echo $form_field->fieldtype;} ?>"  
                                                            id ="input-field-<?php echo $form_field->id;?>"
                                                            name="label_<?php echo $form_field->id;?>"   <?php if($form_field->is_required == 1) echo "required";  ?>
                                                            value="<?php if(isset($_POST['label_'.$form_field->id])){echo $_POST['label_'.$form_field->id];}?>" class="form-control <?php if($form_field->fieldtype == 'tel'){ echo 'phonenumber'; } ?> <?php if($form_field->fieldtype == 'date' ){?> date-picker<?php } ?>" 
                                                            placeholder="<?php echo stripslashes_deep(esc_attr($form_field->placeholder));?>" 
                                                            <?php if($form_field->fieldtype == "number"){echo 'maxlength = "10"'; } ?> 
                                                           />
                                                </div>
                                            </div>
										<?php
										}
									}
								}        
							}
						 }// foreach ($form_fields as $form_field)
					  ?>
	
   
                   
					   <?php
	
						$btn_form_submit_label  = $wpdb->get_var($wpdb->prepare("SELECT label FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND fieldtype = 'submit'",$form_id));
						$btn_form_submit_label = stripslashes_deep(wp_kses($btn_form_submit_label,$allowed_html_tags));
						if(empty($btn_form_submit_label)) {
							$btn_form_submit_label = "submit"; 
						}
	
					   // checking if reset button is required or not 
					   $btn_reset_required  = $wpdb->get_var($wpdb->prepare("SELECT is_required FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND fieldtype = 'reset'",$form_id));
					   
					   $btn_reset_label = $wpdb->get_var($wpdb->prepare("SELECT label FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND fieldtype = 'reset'",$form_id));
	
					   $btn_reset_label = stripslashes_deep(wp_kses($btn_reset_label, $allowed_html_tags));
					   if(empty($btn_reset_label)) {
						$btn_reset_label = " Reset ";   
					   }
					   
					   
					   //checking if user has enabled cacel button or not
					   $btn_cancel_required = $wpdb->get_var($wpdb->prepare("SELECT is_required FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND fieldtype = 'cancel'",$form_id));
					   //checking the label of the cancel-close button if any
	
					   $btn_cancel_label = $wpdb->get_var($wpdb->prepare("SELECT label FROM ".$wpda_form_table['fields']." WHERE fk_form_id = %d AND fieldtype = 'cancel'",$form_id));
	
					   $btn_cancel_label = stripslashes_deep(wp_kses($btn_cancel_label,$allowed_html_tags));
					   if(empty($btn_cancel_label)) {
						$btn_cancel_label = " Cancel ";   
					   }
	
					   // getting the  url to navigate as cancel-close form button is pressed if any
					   $cancel_redirect_url = $wpdb->get_var($wpdb->prepare("SELECT selected_value FROM ".$wpda_form_table['subfields']." WHERE fk_form_id = %d AND label = 'cancellation_url' ",$form_id));
	
					   ?>
						<input type="hidden" name="form_id" value="<?php echo $form_id;?>" />  
						<!--<input type="submit" value='<?php echo $btn_form_submit_label; ?>' id="btn_send_form_email_<?php echo $form_id;?>" name="btn_send_form_email" />-->
						<button type="submit" name="btn_send_form_email" id="btn_send_form_email_<?php echo $form_id;?>" ><?php echo $btn_form_submit_label; ?></button>
						<?php
					   if($btn_reset_required == 1) {
						    //	initially type was input, but to support font-awesome used button type
					   		?>
                            <button type="reset" name="reset" class='ws-reset-form' data-attr='<?php echo $form_id; ?>'><?php echo $btn_reset_label;?></button> 
                            <?php
					   }
					   if($btn_cancel_required == 1) {?>
	
						  <button type="button" name="btn_cancel_form"   onclick='closeForm("<?php echo  $cancel_redirect_url;?>")'><?php echo $btn_cancel_label;?></button> 
						  <script> 
						  function closeForm(cancelUrl) {
							if(cancelUrl) {
								if(cancelUrl == "#"){return false;}
								if(cancelUrl.toLowerCase().search("http") == -1)
									location.href = "http://"+cancelUrl; 
								else
									location.href = cancelUrl;
							}
						 } 
						 </script>
						  <?php
					   }
						?>
	
					  <span id="loader-icon-<?php echo $form_id;?>" style="display:none;" class="form-loader">
						<img src="<?php echo wpda_form_PLUGIN_URI?>/assets/images/loader.gif" />
					  </span>
	
				   </form>
	
						<div id="frontend_form_messages_<?php echo $form_id;?>" style="margin-top:10px; margin-bottom:10px;"></div>
	
				   <script>
	
					//document
					jQuery( document ).ready(function() {
						//	donot submit form on enter , allow enter button only for tex
						jQuery(document).on("keypress", ":input:not(textarea)", function(event) {
							return event.keyCode != 13;
						});
	
						// do not use previous password saved in broswer for passowrd field
						jQuery("input[type='password']").attr("autocomplete", "new-password");
	
						//handle form submission
						//jQuery("form[name='wpdevart_frontend_form_<?php echo $form_id;?>']").submit(function(e){
						jQuery(document).on("submit", "form[name='wpdevart_frontend_form_<?php echo $form_id;?>']", function(e) {
							//shortcode attributes e.g [wpdevart_forms id=1 name=yes]
							var atts = <?php echo json_encode($atts ); ?>;
							var options = { 
								//target:'#frontend_form_messages_<?php echo $form_id;?>',   // target element(s) to be updated with server response 
								// other available options: 
								 url:"<?php echo admin_url('admin-ajax.php'); ?>",        // override for form's 'action' attribute 
								//url:"<?php echo wpda_form_PLUGIN_URI; ?>check-ajax.php",
								data:{'btn_send_form_email':'1','process_ajax':'1','atts':JSON.stringify(atts),'action':'get_post_information'},
								//type:      type        // 'get' or 'post', override for form's 'method' attribute 
								//dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
								//clearForm: true ,       // clear all form fields after successful submit 
								//resetForm: true  ,      // reset the form after successful submit 
	
								// $.ajax options can be used here too, for example: 
								//timeout:   3000 
	
								beforeSubmit: function() {
									jQuery("#loader-icon-<?php echo $form_id;?>").show();
								},
								success:showResponse, // post-submit callback 
							 }; 
	
							// inside event callbacks 'this' is the DOM element so we first 
							// wrap it in a jQuery object and then invoke ajaxSubmit 
							jQuery(this).ajaxSubmit(options); 
	
							// !!! Important !!! 
							// always return false to prevent standard browser submit and page navigation 
							return false; 
						}); 
	
	
						function showResponse(responseText, statusText, xhr, $form) {
							str = responseText;
							//replace first occurence 
							str = str.replace("successmsg_", " "); 
							// append response
	
							jQuery("#frontend_form_messages_<?php echo $form_id;?>").text(" ");
							jQuery("#frontend_form_messages_<?php echo $form_id;?>").append(str);
							setTimeout(function(){ 
								//jQuery("#frontend_form_messages_<?php echo $form_id;?> .success_message<?php echo $form_id;?>").fadeout(); 
								jQuery("#frontend_form_messages_<?php echo $form_id;?> .success_message").fadeOut();
							}, 5000);
	
							//	responseText  , updates the div in target specified in options
							jQuery("#loader-icon-<?php echo $form_id;?>").hide();
	
							var matched = responseText.search('successmsg_');
							if(matched != -1) {
	
								var formId = "<?php echo $form_id; ?>";
								
	
								setTimeout(function(){
										 jQuery('#wpdevart-forms-<?php echo $form_id;?>')[0].reset(); 
										 jQuery("#wpdevart-forms-"+formId).each(function() {
											
											
										});
								 },100);
							 }
	
						return false;
	
						}
						function showRequest() {
							return false;
						}
	
						jQuery(document).on("click","[type=reset].ws-reset-form",function(e){
							formId= jQuery(this).attr('data-attr');
							//reset
							jQuery("#wpdevart-forms-"+formId).each(function(){
								
									
									jQuery("#wpdevart-forms-"+formId)[0].reset();
								});
	
							return false;
	
						});
					});
	
					</script>
				<?php
					} 
					else {
						echo $error_msg."<br />";
					}
				}
			}
		
		} else {
				echo "Shortcode [wpdevart_forms id=".$form_id."] does not belong to any form. Form has been either deleted or shortcode inserted incorrectly.";
		}
		
		} else {
			echo "Please specify correct shortcode e.g, [wpdevart_forms id=1]";	
		}
	
} // wpdevart_template() ends here

?>

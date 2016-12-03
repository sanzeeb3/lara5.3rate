<?php
/**
 * wpdevart_form_submissions_template() function is used to show the form submissions 
 * based on shortcode and attributes (id) we provide e.g short code 
 * [wpdevart_forms_submissions id=1] will show form submissions whose id is 1;
 * This function is called in function wpda_form_submissions_shortcode() 
 *
 * @package wpdevart Forms
 * @since	1.0
 */
 if ( ! defined( 'ABSPATH' ) ) exit;
 
function wpdevart_form_submissions_template($atts) {
	
	global $wpdb;
	
	//	databse plugin tables info	
	global $wpda_form_table;
	$error_msg_arr ="";
	 
	$current_user = wp_get_current_user();
	
	if(empty($atts)) {
		$error_msg_arr[] = "ERROR: shortcode inserted incorrectly, please follow correct proceadure e.g [wpdevart_forms_submissions id=1]";
	}
	
	if(isset($atts['status']) ) {
		$atts['status'] = strtolower($atts['status']);
	} else {
		$atts['status'] = "public";
	}
	
	if(isset($atts['role'])) {
		$allowed_roles_str = strtolower($atts['role']);
	} else {
		$allowed_roles_str = '';
	}
	
	$status = $atts['status'];
	
	if($status == "private") {
		
		if( 0 == $current_user->ID ) {
			//	$error_msg_arr[] = "You must be logged in to view that form. Thanks";
		} else {
			if(!isset($atts['role']) && !isset($atts['author']) ) {
				$error_msg_arr[] = "Specify user role or author e.g [wpdevart_forms_submissions id=1 status='private' role='administrator']";
			} else {
				
				if(isset($atts['role']) && !isset($atts['author']) ) {
					if(!wpda_form_cur_user_role_allowed($atts['role'])) {
						$error_msg_arr[] =  "You do not have permission to view this form submissions. Thanks.";
					}
				} 
				
				if(isset($atts['author']) && !isset($atts['role']) ) {
					if( ! wpda_form_cur_author_allowed($atts['author']) ) {
						$error_msg_arr[] =  "You do not have permission to view this form submissions. Thanks.";
					}
				}
				// both are set
				if(isset($atts['author']) && isset($atts['role']) ) {
					
					if( wpda_form_cur_user_role_allowed($atts['role']) || wpda_form_cur_author_allowed($atts['author']) ) {
						//ok
					} else {
						$error_msg_arr[] =  "You do not have permission to view this form submissions. Thanks.";
					}
				}
				
			}
		}
	 }
	
	
	if(empty($error_msg_arr)) {
		//	update_submission_form_entry_record
		if(isset($_POST['update_submission_form_entry_record'])) {
			// remove last button
			array_pop($_POST);
			$temp_arr = array('submit_time_fk_id');
			$submit_time_fk_id = $_POST['submit_time_fk_id'];

			$submission_updated_flag = 0;
			foreach($_POST  as $key => $value) {
				if(in_array($key, $temp_arr)) {
					continue;
				}
				$output = explode("_",$key);
				$count = count($output);
				$last_part = $output[$count-1];


				if(!is_numeric($last_part)) {
					continue;
				}

				$value = $_POST[$key];
				$value_db_id = $last_part;
				$wpdb->update($wpda_form_table['submissions'], 
							  array( 'field_value' => $value),
							  array( 'id' => $value_db_id ));
				$submission_updated_flag = 1;
				$msg = "Record updated successfully";
				unset($last_part);
				unset($value);
				unset($value_db_id);
			} 
		}
		
		if(isset($atts['id']) && $atts['id']!= '') {
			wpda_form_enqueue_frontend_styles_scripts_submissions();
			wpda_form_enqueue_frontend_styles_scripts();
			$form_id = $atts['id'];	//	id provided in short code e.g [wpdevart_forms id=1]

			$confirm_form_id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM ". $wpda_form_table['wpdevart_forms'] ." WHERE id=%d",$form_id));
			if($form_id == $confirm_form_id) {
				echo "<div class='wpdevart-forms-submission-template'>";
				$form_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpda_form_table['fields']." WHERE fk_form_id=%d AND fieldtype NOT IN('heading','separator','recaptcha') AND position != 9999 ORDER BY position ASC ",$form_id));
				if($form_fields) {
					$total_records = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".$wpda_form_table['submit_time']." WHERE fk_form_id=%d ",$form_id));
					$per_page = 2;
					$cur_page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
					$offset = ($cur_page - 1) * $per_page;

					if( $total_records > 0 ) {
					?>
						<table id="show_submissions_table" class="col-md-12 table-bordered table-striped table-condensed cf">
							<thead class="cf">
								<tr>
									<?php if(isset($atts['editable']) && strtolower($atts['editable'])=='yes' ) { ?>
									<th style="min-width:80px !important;"> </th>
									<?php } ?>
									<th> # </th>
									<?php
									$empty_field_num = 1;
									foreach($form_fields as $form_field) {
										?>
									<th>
										<?php 
										if($form_field->label) {
											
											echo wp_strip_all_tags(html_entity_decode(stripslashes_deep( $form_field->label) ));
										} else {
											echo "Empty Label ".$empty_field_num;
											$empty_field_num++;
										}
										?>
									</th>
										<?php
									}
									unset($empty_field_num);?>

									<th>Date - Time (GMT)</th>
								</tr>
							</thead>

							<tbody>
							<?php
							// find record of submited forms
							$submited_forms_record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpda_form_table['submit_time']." WHERE fk_form_id=%d  ORDER BY id DESC LIMIT %d OFFSET %d",$form_id,$per_page,$offset));
							$form_num = ( $cur_page * $per_page ) - ( $per_page - 1 ); 
							foreach($submited_forms_record as $submited_form_record) { 
								$submited_form_values = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpda_form_table['submissions']." WHERE fk_submit_time_id=%d",$submited_form_record->id));
								foreach($submited_form_values as $submited_form_val){	
									$ids[$submited_form_val->fk_field_id] = stripslashes_deep(esc_html( $submited_form_val->field_value ));
									//	used to update submission
									$temp_submit_time_id[$submited_form_val->fk_field_id] = $submited_form_val->id;

								}//	endforeach

								?>
								<tr id="<?php echo $submited_form_record->id;?>">
									<?php if(isset($atts['editable']) && strtolower($atts['editable'])=='yes' ){ ?>
									<td data-title="Delete" >
										<a class="btn red deleteForm delFormRecord"  attachment-exists="<?php if(isset($attacment_flag)) {echo $attacment_flag;}?>"  data-id="<?php echo $submited_form_record->id; ?>" data-toggle="modal" data-target="#delete-record">
											<i class="fa fa-trash"></i>
										</a>

										<a class="btn green editForm "   data-id="<?php echo $submited_form_record->id; ?>" data-toggle="modal" data-target="#form_submission_record_modal_<?php echo $form_num;?>">
											<i class="fa fa-edit"></i>
										</a>

									</td>
									<?php } ?>

									<td><?php echo $form_num;?></td>

									<?php
									$attacment_flag = 0 ; 
									$temp_ids = $ids;
									for($i = 0; $i< count($form_fields); $i++){ ?>
									<td>
										<?php 
										if($form_fields[$i]->fieldtype == "file") {
											if(isset($ids[$form_fields[$i]->id]) && !empty($ids[$form_fields[$i]->id])){ ?>
												<a href="<?php echo $ids[$form_fields[$i]->id];?>" target="_blank">attachment</a><?php 
												$attacment_flag = 1;
										}
										} else {
											if(isset($ids[$form_fields[$i]->id])) {
												echo stripslashes_deep($ids[$form_fields[$i]->id]);
											}
										}

										unset($ids[$form_fields[$i]->id]);
												?>
									</td><?php
									}?>

									<td><?php echo wpda_form_datetime($submited_form_record->submit_time); ?></td>
								</tr>



								<div id="form_submission_record_modal_<?php echo $form_num;?>" class="modal fade content-large wpdevart_edit_submission_form_popup">
									<div class="modal-dialog"> 
										<form name="form_submission_record_form" method="post" action="">
											<div class="modal-content">
												<h2 class="noBottomPadding">Edit submission #<?php echo $form_num; ?></h2>
												<input type="hidden" name="submit_time_fk_id" value="<?php echo $submited_form_record->id; ?>" />
												<div class="form-group ">
                                                	
                                                    <?php $empty_field_num = 1; ?>
                                                         
													  <?php $j = 0; ?>
                                                      <?php foreach($temp_submit_time_id as $key => $temp_id) { ?>
                                                            <label >
                                                                 <?php 
                                                                    if($form_fields[$j]->label !='') {
                                                                        echo wp_strip_all_tags(html_entity_decode(stripslashes_deep( $form_fields[$j]->label ) ));
                                                                    } else {
                                                                        echo "Empty Label ".$empty_field_num;
                                                                        $empty_field_num++;
                                                                    }
                                                                ?>
                                                            </label>
                                                            <?php if($form_fields[$j]->fieldtype == "date") { ?>
                                                            <input type="text" class="date-picker" name="submission_temp_id_<?php echo $temp_submit_time_id[$form_fields[$j]->id];?>" value="<?php echo stripslashes_deep(esc_html($temp_ids[$form_fields[$j]->id])); ?>" />
                                                            <?php } elseif($form_fields[$j]->fieldtype == "textarea") { ?>
                                                            <textarea rows="5"  style="width:100%; color:#555;" name="submission_temp_id_<?php echo $temp_submit_time_id[$form_fields[$j]->id];?>"><?php echo stripslashes_deep(esc_html($temp_ids[$form_fields[$j]->id] ));?></textarea>
                                                            <?php } else { ?>
                                                             <input type="text" name="submission_temp_id_<?php echo $temp_submit_time_id[$form_fields[$j]->id];?>" value="<?php echo stripslashes_deep(esc_html($temp_ids[$form_fields[$j]->id])); ?>" />
                                                            <?php } ?>
                                                       <?php $j++;} //endforeach;?>
                                                       <?php unset($j);unset($empty_field_num); ?>
                                                    
												</div>

												<div class="margin-top-20">

													<button type="submit" class="btn green" name="update_submission_form_entry_record">Update</button>  
													<button type="button" class="btn red " data-dismiss="modal" aria-label="Close">Cancel</button>
												</div>
											</div><!-- /.modal-content -->
										</form>

									</div><!-- /.modal-dialog -->
								</div><!-- /export-records -->
								<?php
								$form_num ++;
								unset($ids[$submited_form_val->fk_field_id]);
								unset($temp_submit_time_id);
								unset($submited_form_record);
								unset($submited_form_values);

							}//endforeach;
							// unset($form_num);
							?>
							</tbody>
						</table>
						<?php if(isset($atts['editable']) && strtolower($atts['editable'])=='yes' ) { ?>
						<div id="record-deleted" class="modal fade">
							<div class="modal-dialog">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
								<div class="modal-content">
									<h2><i class="fa fa-check"></i>Record has been deleted successfully.</h2>
								</div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /record-deleted -->

						<div id="submissions_updated_modal" class="modal fade">
							<div class="modal-dialog">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
								<div class="modal-content">
									<h2><i class="fa fa-check"></i>Submission updated successfully </h2>
								</div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /submissions_updated_modal -->

						<div id="delete-record" class="modal fade">
							<div class="modal-dialog">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
								<div class="modal-content">
									<h2>Are you sure to delete this record?</h2>
									<div class="margin-top-20">
										<button type="submit" class="btn red"  id="del-record-ok"  attachment-exists="" data-id="">Delete</button>
										<button type="button" class="btn green" data-dismiss="modal" aria-label="Close">Cancel</button>
										<span id="loader-icon-delete-submission" style="display:none;" class="form-loader">
											<img src="<?php echo wpda_form_PLUGIN_URI?>/assets/images/loader.gif" />
										</span>
									</div>
								</div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /delete-record -->
					    <?php } ?>
						<script>
							// Assign ids for deleting  record of submited form TO delete b button
							jQuery(document).on("click",'.delFormRecord',function(e){
								e.preventDefault();
								jQuery("#del-record-ok").attr("attachment-exists",jQuery(this).attr("attachment-exists")) ;
								jQuery("#del-record-ok").attr("data-id",jQuery(this).attr("data-id"));
							});

							jQuery(document).on("click",'#del-record-ok',function(e){
								e.preventDefault();
								jQuery('#loader-icon-delete-submission').show();
								currentElement = jQuery(this);
								submitedFormRecordId = jQuery(this).attr("data-id");
								attachmentExists = jQuery(this).attr("attachment-exists");


								/*
								NOTE :
								====== 
								action": 'get_post_information' will call AJAX_get_post_information() function 
								defined in ajax/wpdevart_ajax_handling.php file
								*/ 
								jQuery.ajax({
									// IMPORTATN : url:ajaxurl is wordpress built-in functionality for using ajax
									url:"<?php echo admin_url('admin-ajax.php'); ?>", //do not change this
									data:{"delSubmitedFormRecord":"1","submitedFormRecordId":submitedFormRecordId,"attachmentExists":attachmentExists,"action": 'get_post_information'},
									method:"POST",
									success: function(data){
										jQuery('#loader-icon-delete-submission').hide();
										if(data == 1){
											currentUrl= window.location.href;
											// hide row in table
											jQuery("tr#"+submitedFormRecordId).remove();
											// hide modal box 
											jQuery('#delete-record').modal('hide');
											// show success message
											setTimeout(function(){ jQuery('#record-deleted').modal('show'); }, 500);
											// hide message
											setTimeout(function(){ jQuery('#record-deleted').modal('hide'); },3000);

											var  rowCount= jQuery('#show_submissions_table tr').length;
											if(rowCount > 1 ){
												e.preventDefault();
											} else { 
												// get all url parts splited by & , to get the same structure like $_GET
												var parts = window.location.search.substr(1).split("&");
												var $_GET = {};
												for (var i = 0; i < parts.length; i++) {
													var temp = parts[i].split("=");
													$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
												}

												//	$_GET here is js var
												if($_GET['cpage']) {
													if($_GET['cpage'] > 1 ) {
														var findStr = "cpage="+$_GET['cpage'];
														var newPageNumber = parseInt($_GET['cpage'])-1;
														var replaceWithStr = "cpage="+newPageNumber;
														var newUrl = currentUrl.replace(findStr,replaceWithStr);
														jQuery('#record-deleted').modal('show');

														setTimeout(function(){ 
															location.href = newUrl;

														}, 1000);
													} else {
														jQuery('#record-deleted').modal('show');
														setTimeout(function(){ 
															location.href = currentUrl;
														}, 1000);
													}
												} else {
													jQuery('#record-deleted').modal('show');
													setTimeout(function(){ 
														location.href = currentUrl;
													}, 1000);

												} // if($_GET['cpage']) {
											}
										} else {
											alert("There was some Error deleting the Record ");
										} // if(data == 1){
									} //success: function(data){
								}); // ajax
								e.preventDefault();
							}); // jQuery(document).on("click",'#del-record-ok',function(e){

						</script>

						<?php
						if(isset($submission_updated_flag)&& $submission_updated_flag == "1") {?>
							<script>
								jQuery(document).ready(function(e) {
									jQuery('#submissions_updated_modal').modal('show');
									setTimeout(function(){ jQuery('#submissions_updated_modal').modal('hide'); }, 2000);
								});
							</script>
						<?php }?>

				<?php

					} else {
						echo "<p>There are no submissions for this form yet.</p>";
					}

					//	pagination
					if($total_records > 0) {
						//echo (( $cur_page * $per_page ) - ( $per_page - 1 )).'-'.($form_num-1).' of '.$total_records;
						$pages=  paginate_links( array(
							'base' => add_query_arg( 'cpage', '%#%'),
							'format' => '',
							'prev_text' => __('&laquo;'),
							'next_text' => __('&raquo;'),
							'total' => ceil($total_records / $per_page),
							'current' => $cur_page,
							'type'  => 'array',
						));
						
						if($pages) {	  
							echo '<div class="wf-frontend-pagination pagination-container text-center margin-top-20 "><ul class="pagination">';
							foreach ( $pages as $page ) {
								echo "<li>$page</li>";
							}
							echo "</ul></div>";	  
						}
					}

				} else {
					echo "This form doesn't have any fields/columns.";
				}
				echo "</div>";
			} else {
				echo "Shortcode [wpdevart_forms id=".$form_id."] does not belong to any form. Form has been either deleted or shortcode inserted incorrectly.";
			}

		} else {
			echo "Please specify correct shortcode e.g, [wpdevart_forms id=1]";	
		}
	
	} else {
		if(!empty($error_msg_arr)) {
			echo "<h3 class='wpdevart-fst-error'>ERROR(s)</h3>";
			foreach($error_msg_arr as $key=>$val) {
				$num = $key+1;
				echo $num.'. '.$val;
				echo "<br>";
			}
		}
		
	}
	
} // wpdevart_form_submissions_template() ends here
?>
<?php
/**
 * Enlists all form submissions of relevant form
 *
 * @package wpdevart Forms
 * @since	1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;	 
global $wpda_form_table; 
//	

if( isset($_GET['form_id']) && !empty($_GET['form_id']) && current_user_can('manage_options') && wp_verify_nonce($_GET['wpdevart_form_submision_update_name'], 'wpdevart_form_submision_update')) {
	$form_id = intval($_GET['form_id']);
	$form_exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".$wpda_form_table['wpdevart_forms']." WHERE id =%d ",$form_id));
	if($form_exists != $form_id) {
		echo "The form you are trying to access does not exist.";
		exit;
	}
} else {
	echo "Please specify the correct form id in url.";
	exit;
}

if(isset($_POST['del_submited_form_record']) && curent_user_can('manage_options') && wp_verify_nonce( $_POST['wpdevart_form_submision_update_name'], 'wpdevart_form_submision_update')) {
	//	represents id in $wpda_form_table['submit_time'] 
    $submited_form_record_id = intval($_POST['submited_form_record_id']);

	if( isset($_POST['attachment_exits']) && $_POST['attachment_exits'] == 1 ) {
		$form_id = $wpdb -> get_var ($wpdb->prepare("SELECT fk_form_id FROM ".$wpda_form_table['submit_time']." WHERE id =%d ",$submited_form_record_id));
		$attachment_fields = $wpdb -> get_results ($wpdb->prepare("SELECT id FROM ".$wpda_form_table['fields']." WHERE fk_form_id =%d AND fieldtype='file' ",$form_id));
		
		if($attachment_fields) {
			$unlink_attachment_ids = array ();
			foreach($attachment_fields as $attachment_field) {
				$result= $wpdb -> get_var ($wpdb->prepare("SELECT field_value FROM ".$wpda_form_table['submissions']." WHERE fk_field_id =%d AND fk_submit_time_id=%d ",$attachment_field->id,$submited_form_record_id));
				$unlink_attachment_ids[$attachment_field->id] = $result;
				
				$site_url= get_site_url();
				$rel_path=  str_replace($site_url,'',$result);
			
				if( file_exists(ABSPATH .$rel_path)) {
					unlink(ABSPATH .$rel_path);
				}
			}//endforeach
		}
	} 
	
	$wpdb->delete( $wpda_form_table['submit_time'], array( 'id' => $submited_form_record_id ) );
	if($wpdb->rows_affected == 1) {
		$msg = "Record deleted successfully";
	} else {
		print_r($wpdb->last_error);
	}
}
//	update_submission_form_entry_record
if(isset($_POST['update_submission_form_entry_record']) && current_user_can('manage_options') && wp_verify_nonce( $_POST['wpdevart_form_submision_update_name'], 'wpdevart_form_submision_update')) {
	// remove last button
	array_pop($_POST);
	$temp_arr = array('submit_time_fk_id');
	$submit_time_fk_id = intval($_POST['submit_time_fk_id']);
	
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
		unset($last_part);
		unset($value);
		unset($value_db_id);
	} 
}

?>
<div id="wpdevart">
<div id="wpdevart-forms">
	<div class="wpdevart-general-form" method="post" action="">
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/header.php');?>
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/sidebar.php');?>
			<main class="pull-left">
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
                
                <div id="del-all-records" class="modal fade">
                      <div class="modal-dialog">
                      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <div class="modal-content">
                            <h2>Are you sure to  delete all submissions of this form?</h2>
                            <div class="margin-top-20">
                                <button type="submit" class="btn red"  id="del-all-records-ok" data-attr = "<?php echo $form_id ;?>">Delete</button>
                                <button type="button" class="btn green" data-dismiss="modal" aria-label="Close">Cancel</button>
                                <span id="loader-icon-delete-all-submissions" style="display:none;" class="form-loader">
                            		<img src="<?php echo wpda_form_PLUGIN_URI?>/assets/images/loader.gif" />
                          		</span>
                           </div>
                        </div><!-- /.modal-content -->
                      </div><!-- /.modal-dialog -->
                </div><!-- /delete-record -->
                
				<div id="update-status" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i>Settings have been saved successfully.</h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /update-status -->
				<div id="reset-status" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i>Settings have been reset.</h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /reset-status -->
				<div id="setting-saved" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i>Settings have been saved.</h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /reset-status -->
				<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/help.php');?>
                
				<div class="tab-content wpdevart-tabs"> 
					<div role="tabpanel" class="tab-pane fade active in" id="form-styling">
                        <!--<p class="successMessage"><i class="icon-check"></i>Settings have been saved successfully.</p>-->
						<?php 
							
						$form_fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpda_form_table['fields']." WHERE fk_form_id=%d AND fieldtype NOT IN('heading','separator','recaptcha') AND position != 9999 ORDER BY position ASC ",$form_id));
						//print_r($form_fields);
						
						if($form_fields) {	
							// pagination if required
							$total_records = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".$wpda_form_table['submit_time']." WHERE fk_form_id=%d ",$form_id));
							$per_page = 20;
							$cur_page = isset( $_GET['cpage'] ) ? abs( intval($_GET['cpage']) ) : 1;
							$offset = ($cur_page - 1) * $per_page;
						
							if( $total_records > 0 ) {
							?>
							<a href="#" class="btn pull-right fields-info" data-toggle="modal" data-target="#fields-details-submission"><i class="fa fa-question-circle"></i></a>
							
							<h1><?php echo wp_strip_all_tags(stripslashes_deep( $wpdb->get_var($wpdb->prepare("SELECT name FROM ".$wpda_form_table['wpdevart_forms']." WHERE id = %d",$form_id)) ));?> (Total submissions: <?php echo $total_records;?>)</h1>
							<?php
								$serial = $wpdb->get_results($wpdb->prepare("SELECT id FROM " .$wpda_form_table['submit_time'] ." WHERE fk_form_id=%d  LIMIT %d OFFSET %d",$form_id,$per_page,$offset));
							?>
                            <b> 
								<?php 
								 $temp = (( $cur_page * $per_page ) - ( $per_page - 1 ));
								 echo "Record : ".$temp .' - '.($temp+(count($serial)-1)).' of '.$total_records;
								?>
                            </b>
							<a class="btn red margin-left-10 pull-right margin-bottom-20" name="btn_del_all_records" data-toggle="modal" data-target="#del-all-records">DELETE ALL SUBMISSIONS</a>
							<a class="wpdevart_pro btn green margin-left-10 pull-right margin-bottom-20 btn_export_records" name="btn_export_records" data-toggle="modal" data-target="#export-records">Export Records CSV<span class="wpdevart_pro_span">(PRO)</span></a>

                            <div class="clearfix"></div>
							<!-- /export-records -->
              				<div class="submission-table">
								<div class="table-condensed-outer">
								   <table id="show_submissions_table" class="col-md-12 table-bordered table-striped table-condensed cf">
									   <thead class="cf">
											<tr>
												<th style="min-width:80px !important;"> </th>
												<th> # </th><?php
												$empty_field_num = 1;
                                                foreach($form_fields as $form_field):?>
													<th>
                                                    	<?php 
														if($form_field->label == '') {
															echo "Empty Label ".$empty_field_num;
															$empty_field_num++;
															
														} else {
															echo wp_strip_all_tags(html_entity_decode(stripslashes_deep( $form_field->label ) ));
														}
														?>
                                                    </th>
														<?php
                                                endforeach;
												unset($empty_field_num);?>
												<th>Date - Time (GMT)</th>
												
											</tr>
										  </thead>
										<tbody><?php
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
                                                
                                                 <td data-title="Delete" >
													<a class="btn red deleteForm delFormRecord"  attachment-exists="<?php if(isset($attacment_flag)) {echo $attacment_flag;}?>"  data-id="<?php echo $submited_form_record->id; ?>" data-toggle="modal" data-target="#delete-record">
														<i class="fa fa-trash"></i>
													</a>
                                                    
                                                   <a class="btn green editForm "   data-id="<?php echo $submited_form_record->id; ?>" data-toggle="modal" data-target="#form_submission_record_modal_<?php echo $form_num;?>">
														<i class="fa fa-edit"></i>
													</a>
                                                   
												 </td>
                                                 
												<td><?php echo $form_num;?></td>
																								
												<?php
                                                $attacment_flag = 0 ; 
												$temp_ids = $ids;
												for($i = 0; $i< count($form_fields); $i++){ ?>
                                                    <td>
                                                    <?php
                                                     //if(filter_var($ids[$form_fields[$i]->id], FILTER_VALIDATE_URL)){
													if($form_fields[$i]->fieldtype == "file"){
														if(isset($ids[$form_fields[$i]->id]) && !empty($ids[$form_fields[$i]->id])){ ?>
															<a href="<?php echo $ids[$form_fields[$i]->id];?>" target="_blank">attachment</a><?php 
															$attacment_flag = 1;
														}
													} else {
														if(isset($ids[$form_fields[$i]->id])) {
															echo (stripslashes_deep( $ids[$form_fields[$i]->id]));
														}
													}
													 
													// unset($ids[$form_fields[$i]->id]);
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
																 <input type="text" name="submission_temp_id_<?php echo (isset($temp_submit_time_id[$form_fields[$j]->id]))?$temp_submit_time_id[$form_fields[$j]->id]:'';?>" value="<?php echo (isset($temp_submit_time_id[$form_fields[$j]->id]))?stripslashes_deep(esc_html($temp_ids[$form_fields[$j]->id])):''; ?>" />
																<?php } ?>
                                                           
														   <?php $j++;} //endforeach;?>
                                                           <?php unset($j);unset($empty_field_num); ?>
														  </div>
														  
														  <div class="margin-top-20">
															  <?php wp_nonce_field('wpdevart_form_submision_update','wpdevart_form_submision_update_name') ?>
															  <input type="submit" class="btn green" name="update_submission_form_entry_record"  value="Update" />  
															  <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancel</button>
														  </div>
													  </div><!-- /.modal-content -->
													</form>
													
												 </div><!-- /.modal-dialog -->
											</div><!-- /  -->
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
									 </div><?php
									}
									else {
										echo "<p>There are no submissions for this form yet.</p>";
									}
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
											echo '<div class="pagination-container text-center margin-top-20 "><ul class="pagination">';
												 foreach ( $pages as $page ) {
												  echo "<li>$page</li>";
												  }
											echo "</ul></div>";	  
										}
									}
							} else {
								echo "This form doesn't have any fields/columns.";
								exit;
							}
						?>
					</div><!-- tab-pane -->
				</div><!-- .tab-content -->
			</main><!-- / main .pull-left -->
		<div class="clearfix"></div>
		</div><!-- / .options-area -->
        <?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/footer.php');?>
</div><!-- / #wpdevart-forms -->
</div><!-- / wpdevart -->
<script>

jQuery('#chooseFormForCheckingSubmits').change(function () {
 var optionSelected = jQuery(this).find("option:selected");
 var form_id  = optionSelected.val();
 location.href="<?php echo admin_url("admin.php?page=".$this->slug['submissions']."&form_id="); ?>"+form_id;
});

</script>

<script>
 jQuery(document).ready(function(e) {
	 // 
	 jQuery(document).on("click",'#export_ok',function(e){
		 if(jQuery("input[name=export_options]:checked").val() == "some") {
			 var one	= jQuery('input[name="column[]"]:checked').length;
			 var two	= jQuery('input[name="serial"]:checked').length;
			 var three  = jQuery('input[name="datetime"]:checked').length;
			 
			if( (one) < 1 )  {
				e.preventDefault();
				alert("Please select atleast 1 column(exluding serial, datetime) to export");
			} 
		}
	  });
	 if(jQuery(".export-all").is(":checked")) {
		 jQuery(".export-check-opt").each(function(index, element) {
            jQuery(this).attr("readonly","readonly" );
			jQuery(this).addClass("read-only");
			jQuery(this).removeAttr("checked");
        });
	 } else {
		 jQuery(this).removeAttr("readonly" );
	 }
	 
	jQuery(document).on("click",':checkbox[readonly=readonly]',function() {
            return false;
    });
	

  
         
	
});
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

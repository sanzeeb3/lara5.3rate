<?php
/**
 * Form List gets all generated form from database
 *
 * @package wpdevart Forms
 * @since	1.0
 */
 if ( ! defined( 'ABSPATH' ) ) exit;
  if ( ! current_user_can('manage_options') ) exit;
 
global $wpdb; 
global $wpda_form_table;

?>
<title>Form List</title>
<div id="wpdevart">
   <div id="wpdevart-forms">
	<div class="wpdevart-general-form" method="post" action="">
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/header.php');?>
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/sidebar.php');?>
			<main class="pull-left">
				<div id="update-status" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i>Options Saved</h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /update-status -->
                
              
                
				<div id="delete-form" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2>Are you sure you want to delete this form ?</h2>
						<div class="margin-top-20">
							<button type="submit" class="btn red" id="delFormOk" data-attr="">Delete</button>
							<button type="button" class="btn green" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <span id="loader-icon-delete-form" style="display:none;" class="form-loader">
                            	<img src="<?php echo wpda_form_PLUGIN_URI?>/assets/images/loader.gif" />
                          	</span>
							
					   </div>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /delete-form -->
                
                <div id="form-deleted-modal" class="modal fade hideAfterDisplay">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i>Form deleted successfully </h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /delete-form -->
                
                <div id="wait-msg-modal" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content" >
                    	<div>
                        	<img src="<?php echo  wpda_form_PLUGIN_URI; ?>/assets/images/wait.gif" width="100" height="130" />
                        </div>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /loading-modal -->
                
				<div id="reset-status" class="modal fade">
				  <div class="modal-dialog">
                  	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<div class="modal-content">
						<h2><i class="fa fa-check"></i>Settings have been reset </h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /reset-status -->
				<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/help.php');?>
				<?php 
				  //	showing forms with pagination
				  $total = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpda_form_table['wpdevart_forms']." ");
				  $per_page = 20;
				  $cur_page = isset( $_GET['cpage'] ) ? abs( intval($_GET['cpage']) ) : 1;
				  $offset= ($cur_page - 1) * $per_page;
				 ?>
				<div class="tab-content wpdevart-tabs">
					<div role="tabpanel" class="tab-pane fade active in" id="forms">
						<a href="#" class="btn pull-right fields-info" data-toggle="modal" data-target="#fields-details-list"><i class="fa fa-question-circle"></i></a>
						<h1>wpdevart Forms</h1>
                        
                        <div class="notify-response" >
							<?php if(isset($msg)) echo $msg;?>
                        </div>
                        
                        <button class="btn green btn-default  pull-right wpdevart_pro" data-toggle="modal" data-target="#import-form-modal">import wpdevart Form <span class="wpdevart_pro_span">(PRO)</span></button>
                        <div class="clearfix"></div>
						<?php if($total>0) {?>
						<div class="generalForm margin-top-20 addForms form-lists">
							<div class="col-sm-1 order hidden-xs"><span >#</span></div>
                            <div class="col-sm-2 col-md-2  hidden-xs padding-left-0"><label>Form Name</label></div>
                            <div class="col-sm-2 col-md-2  hidden-xs padding-left-0"><label>Submissions</label></div>
                          	<div class="col-md-4 col-sm-4  hidden-xs"><label>Shortcode</label></div>
                            <div class="col-md-3 col-sm-3  hidden-xs"><label>Actions</label></div>
                         	<div class="clearfix"></div>
							<?php
							  $wpdevart_forms = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpda_form_table['wpdevart_forms']."  LIMIT %d OFFSET %d ",$per_page,$offset));
							  $form_num = ( $cur_page * $per_page ) - ( $per_page - 1 ); 
							 
							  foreach( $wpdevart_forms as $wpdevart_form ) { 
								$box_class = ( $form_num % 2 == 0 ) ? "bg-even" : "bg-odd";
							   ?>
								<div class="addFormBox <?php echo $box_class;?>" id="addForm-<?php echo $wpdevart_form->id; ?>">
									
                                    <div class="col-sm-1   order">
                                    	<span><?php echo $form_num;?></span>
                                    </div>
									
                                    <div class="col-sm-2 col-md-2 ">
										<h4><?php echo wp_strip_all_tags(html_entity_decode(stripslashes_deep( $wpdevart_form->name) ));?> </h4>
									</div>
                                    
                                     <div class="col-md-2 col-sm-2">
                                    	<?php $submits_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".$wpda_form_table['submit_time']." WHERE fk_form_id = %d",$wpdevart_form->id ));?>
										<h4 class="text-center"><a href="<?php echo wp_nonce_url(admin_url("admin.php?page={$this->slug['submissions']}&form_id={$wpdevart_form->id}"),'wpdevart_form_submision_update','wpdevart_form_submision_update_name'); ?>">(<?php echo $submits_count;?>)</a></h4>
                                    </div>
                                    
									<div class="col-md-4 col-sm-4 ">
										<input class="form-credentials form-control" onclick="this.select()" value="<?php echo "[wpdevart_forms id=".$wpdevart_form->id."]"; ?>" readonly data-trigger="focus" data-toggle="tooltip" data-placement="bottom" data-title="Copy this shortcode and paste anywhere you want to show this form. i.e. pages, posts, widgets" >
									</div>
                                    
									<div class="col-md-3 col-sm-3"> 
										<a  href="<?php echo admin_url("admin.php?page={$this->slug['edit']}&form_id={$wpdevart_form->id}"); ?>" class="pull-left btn green editForm" data-toggle="tooltip" data-placement="bottom" title="Edit this form"><i class="fa fa-edit"></i></a>
										<a  data-attr="<?php echo $wpdevart_form->id;?>" class="pull-left btn red deleteForm  assingIdToConfirmDelBtn margin-left-5" data-toggle="modal" data-target="#delete-form"><i class="fa fa-trash" data-toggle="tooltip" data-placement="bottom" title="Delete this form" ></i></a>
										
                                         
                                        <a  name="duplicate_form"   class="wpdevart_pro pull-left btn purple margin-left-5" data-toggle="tooltip" data-placement="bottom" title="Duplicate this form(PRO)">
                                        	<i class="fa fa-copy" data-toggle="modal" data-target="#duplicate-form-modal"></i>
                                        </a>
										
                                        <a  name="export_form"   class="wpdevart_pro pull-left btn orange margin-left-5" data-toggle="tooltip" data-placement="bottom" title="Export this form(PRO)">
                                       		<i class="fa fa-mail-forward" data-toggle="modal" data-target="#export-form"></i>
                                        </a>
                                        
                                    </div>
									
                                    <div class="clearfix"></div>
                                   <?php  wp_nonce_field( 'wpdevart_form_list_actions_nonce_value','wpdevart_form_list_actions_nonce_name' ); ?>
								</div><!-- addFormBox -->
                                
							   <?php $form_num++;};unset($form_num); ?>
							  <?php 
							  } else {
								?>
								<br />
								<div >
									<p>There are no forms yet. Please click below to create a new form.</p>
									<a  href='<?php echo admin_url("admin.php?page={$this->slug['add_new']}"); ?>'>create a new form</a>
								</div>
                                <br />
								<?php
							  }  //	total >0
							  ?>
						</div><!-- generalForm -->
						<?php
						
						$pages=  paginate_links( array(
													  'base' => add_query_arg( 'cpage', '%#%'),
													  'format' => '',
													  'prev_text' => __('&laquo;'),
													  'next_text' => __('&raquo;'),
													  'total' => ceil($total / $per_page),
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
					   ?>
					</div>
				</div><!-- .tab-content -->
			</main><!-- / main .pull-left -->
		<div class="clearfix"></div>
		</div><!-- / .options-area -->
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/footer.php');?>
	</div><!-- #wpdevart-general-form -->
</div><!-- / #wpdevart-forms -->
</div><!-- / wpdevart -->
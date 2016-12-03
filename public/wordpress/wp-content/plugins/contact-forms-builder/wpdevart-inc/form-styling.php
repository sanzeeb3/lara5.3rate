<?php
/*
  This file includes custom styling for forms
  There are multiple ways in which css  styles  are applied to our form , that are :
    1 . general theme styling for all forms (included in theme css file)
	2 . form skins , red,green etc
    3 . general styling for all forms (created by our plugin) [static  form_id = -1 ]
		--NOTE--  please note that form with id = -1 do not exists in database . its just a static id that
		      	  is used only in styling purposes 
				  general form styling is been DEPRECATED
    4 . Every form created by wpdevart Forms plugin can have its own styling 
 
  NOTE: please note that custom styling for wpdevart Forms is stored in wordpress 
  		table " wp_options " with our defined option of "wpdevart_forms_style"
*/
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! current_user_can('manage_options') ) exit;
?>
<div id="wpdevart">
<div id="wpdevart-forms">
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/header.php');?>
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/sidebar.php');?>
			<main class="pull-left">
				<div id="update-status" class="modal fade">
				  <div class="modal-dialog">
					<div class="modal-content">
						<h2> <i class="fa fa-check"></i>Settings saved successfully</h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /update-status -->
				<div id="reset-status" class="modal fade">
				  <div class="modal-dialog">
					<div class="modal-content">
						<h2><i class="fa fa-check"></i> Settings have been reset </h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /reset-status -->
				<div id="setting-saved" class="modal fade">
				  <div class="modal-dialog">
					<div class="modal-content">
						<h2><i class="fa fa-check"></i>Settings have been saved </h2>
					</div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /reset-status -->
				<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/help.php');?>          
                <div class="tab-content wpdevart-tabs"> 
					<img class="wpdevart_pro" src="<?php echo wpda_form_PLUGIN_URI ?>/images/styling_1.jpg">
                    <img class="wpdevart_pro" src="<?php echo wpda_form_PLUGIN_URI ?>/images/styling_2.jpg">
                    <img class="wpdevart_pro" src="<?php echo wpda_form_PLUGIN_URI ?>/images/styling_3.jpg">
                    <img class="wpdevart_pro" src="<?php echo wpda_form_PLUGIN_URI ?>/images/styling_4.jpg">
                    <img class="wpdevart_pro" src="<?php echo wpda_form_PLUGIN_URI ?>/images/styling_5.jpg">
				</div><!-- .tab-content -->
			</main><!-- / main .pull-left -->
		<div class="clearfix"></div>
		</div><!-- / .options-area -->
		<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/footer.php');?>
</div><!-- / #wpdevart-forms -->
</div><!-- / wpdevart -->
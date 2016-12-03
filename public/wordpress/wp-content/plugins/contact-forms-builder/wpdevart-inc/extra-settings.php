  <?php
/**
 * Extra options
 * IMPORTANT:	on Add New form page, we have a settings tab which is collapsed by default
				but in php, its storing form fields although its not visible on page
 *
 * @package wpdevart Forms
 * @since	1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;
 if ( ! current_user_can('manage_options') ) exit;
?>
<title>Extra Options</title>
    <div id="wpdevart">
        <div id="wpdevart-forms">
        <form class="wpdevart-general-form" method="post" action="#">
			<?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/header.php');?>
            <?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/sidebar.php');?>
            <main class="pull-left">
                <div class="tab-content wpdevart-tabs">                
                </div><!-- .tab-content -->
            </main><!-- / main .pull-left -->
            <div class="clearfix"></div>
            </div><!-- / .options-area -->
        </form><!-- #wpdevart-general-form -->
        <?php require_once( wpda_form_PLUGIN_DIR .'/wpdevart-layout/footer.php');?>
    </div><!-- / #wpdevart-forms -->
</div><!-- / wpdevart -->
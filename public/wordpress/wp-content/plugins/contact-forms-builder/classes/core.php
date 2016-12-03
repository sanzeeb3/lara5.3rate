<?php
/**
 * This is Plugin Core Class
 * Contains definitions about plugin pages i.e. add new form, edit form, show forms and settings
 *
 * @package wpdevart Forms
 * @since	1.0
 */
 if ( ! defined( 'ABSPATH' ) ) exit;
 
class wpdevartForms {
	
	// plugin admin pages's url slug	
	private $slug; 
	
	public function __construct() { 
		
		global $wpdb;
		$settings = array();
		
/*		if(file_exists(wpda_form_FILE_INI)) {
			$settings = parse_ini_file(wpda_form_FILE_INI);
		}
*/		
		if( empty($settings) || ( !isset($settings['base_slug']) || empty($settings['base_slug']) ) ) {
			$settings['base_slug'] = "wpdevart-forms";
		}
		
		// slugs & menu
		$this->slug['add_new']	= $settings['base_slug'] . '-add-new';
		$this->slug['edit']	= $settings['base_slug'] . '-edit';
		$this->slug['list']	= $settings['base_slug'] . '-list';
		$this->slug['styling']	= $settings['base_slug'] . '-styling';
		$this->slug['settings']	= $settings['base_slug'] . '-settings';
		$this->slug['submissions']	= $settings['base_slug'] . '-submissions';
		$this->slug['extra_settings']	= $settings['base_slug'] . '-extra-settings';
		
		//set roles for users who can access this plugin
		$allowed_roles = array('editor', 'administrator');
		
		//	ENQUEUE SCIPTS AND STYLES FOR wpdevart WIDGETS
		add_action( 'admin_enqueue_scripts', array($this, 'wpda_form_enqueue_admin_styles') );
		add_action( 'admin_enqueue_scripts', array($this, 'wpda_form_enqueue_admin_scripts') );
		add_action('admin_init', array($this, 'wpda_form_export_form') );
		add_action('admin_init', array($this, 'wpda_form_export_form_submissions') );
		
		//add post page button
		add_filter( 'mce_external_plugins', array( $this ,'mce_external_plugins' ) );
		add_filter( 'mce_buttons', array($this, 'mce_buttons' ) );
		
		// ajax hook for mce button
		add_action("wp_ajax_wpdevart_forms_mce_ajax",array($this,"wpdevart_forms_mce_ajax"));

		//	wp_get_current_user() required pluggable.php defined in wp-includes/pluggable.php
		$user_role = wp_get_current_user();
		
		if( array_intersect($allowed_roles, $user_role->roles )){
			
			
			// plugin version
			if(get_option("wpdevart_forms_plugin_version")) {
				update_option("wpdevart_forms_plugin_version", wpda_form_PLUGIN_VERSION);
			} else {
				add_option("wpdevart_forms_plugin_version", wpda_form_PLUGIN_VERSION);
			}
			
			if( wp_get_theme() == 'wpdevart' || wp_get_theme() == 'wpdevart Child' || wp_get_theme() == 'wpdevart Theme' || wp_get_theme() == 'wpdevart Child Theme' ) {
				//	if wpdevart Theme is active, then we do not add sidebar menu because
				//	wpdevart Theme automatically adds sidebar menu items under wpdevart tab
			} else {
				// add plugin  pages to wordpress admin menu
				add_action('admin_menu', array($this, 'wpda_form_add_menu'));
			}
		}	
	}
	// admin post page tinmce buttons
	public function mce_external_plugins( $plugin_array ) {
		$plugin_array['wpdevart_forms'] = wpda_form_PLUGIN_URI. 'assets/js/mce-button.js';
		return $plugin_array;
	}

	public function mce_buttons( $buttons ) {
		array_push( $buttons, 'wpdevart_forms');
		return $buttons;
	}
	public function wpdevart_forms_mce_ajax(){
		if(!current_user_can('edit_posts')){
			exit;
		}
		?>
		<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <title>Wpdevart Form</title>           
            <base target="_self">
            <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
			<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
            <script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
			<?php wp_print_scripts('jquery') ?>
        </head>
        <body id="link"   class="forceColors">
        <?php
            global $wpdb;
			global $wpda_form_table;      
			$wpdevart_forms = $wpdb->get_results("SELECT * FROM ".$wpda_form_table['wpdevart_forms']);
 
            ?>
            <table width="100%" class="paramlist admintable" cellspacing="1" style="margin-bottom: 120px;">
                <tbody>
                    <tr>
                        <td style="width: 100px;" class="paramlist_key">
                            <span class="editlinktip">
                                <label style="font-size:14px" id="paramsstandcatid-lbl" for="Category" class="hasTip">Select Form: </label>
                            </span>
                        </td>
                        <td class="paramlist_value" >                        
							<?php
                             if(count($wpdevart_forms) > 0){?>   
                                    <select id="wpdevart_forms_id" style="width: 200px; font-size: 14;">
                                    <?php foreach ($wpdevart_forms as $key => $form ){ ?>
                                            <option value="<?php echo $form->id ?>"<?php if(isset($_GET['id'])) selected(intval($_GET['id']), $form->id ); ?> > 
                                                <?php echo $form->name; ?>
                                            </option>
                                    <?php } ?>
                                    </select>
                            <?php } else {
                                echo '<label>No any form creaetd yet. Please create a form from wpdevart Forms, then a list of forms will be displayed here.</label>';
                            } ?>                       
                        </td>
                    </tr>
                </tbody>
            </table>
       		<div class="mceActionPanel">
                <div style="float: left">
                    <input type="button" id="cancel" name="cancel" value="Cancel" onClick="tinyMCEPopup.close();"/>
                </div>
    
                <div style="float: right">
                    <input type="submit" id="insert" name="insert" value="Insert" onClick="insert_poll();"/>
                    <input type="hidden" name="iden" value="1"/>
                </div>
            </div>
        
    
        	<script type="text/javascript">
				function insert_poll() {					  
					if(jQuery('#wpdevart_forms_id').val()!='0'){
						var tagtext;
						tagtext = '[wpdevart_forms id="' + jQuery('#wpdevart_forms_id').val()+'" ]';
						window.parent.tinyMCE.execCommand('mceInsertContent', false, tagtext);
						tinyMCEPopup.close();
					}
					else{
						tinyMCEPopup.close();
					}
				}    
        	</script>
        	</body>
        </html>
        <?php
        die();

	}
 	// Adds menus to left-hand sidebar
	public function wpda_form_add_menu() {
		
		add_menu_page('wpdevart Forms', 'Wpdevart Forms', 'manage_options', $this->slug['list'], array($this, 'wpda_form_list_forms'), wpda_form_PLUGIN_URI .'assets/images/admin-icon.png', 66);
		add_submenu_page(null, 'Add New Form', 'Add New', 'manage_options', $this->slug['add_new'], array($this, 'wpda_form_add_new_form'));
		add_submenu_page(null, 'wpdevart Forms - Edit', 'Edit', 'manage_options', $this->slug['edit'], array($this, 'wpda_form_edit_form'));	
		add_submenu_page(null, 'wpdevart Forms - Styling', 'Styling', 'manage_options', $this->slug['styling'], array($this, 'wpda_form_form_styling'));
		add_submenu_page(null, 'wpdevart Forms - Submissions ', 'Submissions', 'manage_options', $this->slug['submissions'], array($this, 'wpda_form_form_submissions'));
		add_submenu_page(null, 'wpdevart Forms Settings ', 'Settings','manage_options', $this->slug['settings'], array($this, 'wpda_form_forms_settings'));
		add_submenu_page(null, 'wpdevart Forms Extra settings ', 'Extra Settings', 'manage_options', $this->slug['extra_settings'], array($this, 'wpda_form_forms_extra_settings'));
		add_submenu_page($this->slug['list'], 'Featured Plugins', 'Featured Plugins', 'manage_options', 'featured_plugins', array($this, 'featured_plugins'));
		add_submenu_page($this->slug['list'], 'Uninstall', 'Uninstall', 'manage_options', 'wpda_form_uninstall', array($this, 'uninstall_controller'));
		
	}
	
	// Call back function add new (form page)
	public function wpda_form_add_new_form() {
		require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/add-new-form.php';
	}
	
	// Call back function edit form (page)
	public function wpda_form_edit_form() {
		require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/edit-form.php';
	}
	
	// Call back function for En-listing forms (page)
	public function wpda_form_list_forms() {
		wpda_form_db_tables();
		require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/list-forms.php';
	}
	
	// Call back function for forms styling (page)
	public function wpda_form_form_styling() {
		require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/form-styling.php';
	}
	
	// Call back function for forms submissions(page), How many times a forms submitted and containing what data
	public function wpda_form_form_submissions(){
		require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/form-submissions.php';
	}
	
	// Call back function for forms setting (page), for later use
	public function wpda_form_forms_settings(){		
		//	later use
		//	echo "Page not found";
		exit;
	}
	
	// Call back function for forms extra settings (page), 
	public function wpda_form_forms_extra_settings() {
		require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/extra-settings.php';
	}
	
	//	Function for importing form from export file
	public function wpda_form_import_form() {
		//pro feature
	}
		
		
	// -------------------------------------------
	//				FORM EXPORT
	//  ------------------------------------------

	public function wpda_form_export_form() {
		if(isset($_POST['btn_export_form'])) {
			require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/export-form.php';
		}
	}
	
	//	Exporting forms submissions [ in csv formate ]
	public function wpda_form_export_form_submissions() {
		if(isset($_POST['export_form_record'])) { 
			require_once wpda_form_PLUGIN_DIR . '/wpdevart-inc/export-form-submissions.php';
		}
	}
	
	//	Enqueue backend form pages css
	public function wpda_form_enqueue_admin_styles() { 
		$page = "";
		if(isset($_GET['page'])) {
			
			$page = $_GET['page'];
			
			//	this will load styles to only those pags that are in slug
			if ( ! in_array($page, $this->slug) )
				return;
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wf-bootstrap', wpda_form_PLUGIN_URI . 'assets/css/bootstrap.min.css');
			wp_enqueue_style( 'wf-font-awesome', wpda_form_PLUGIN_URI . 'assets/css/font-awesome.min.css');
			wp_enqueue_style( 'wf-tabs', wpda_form_PLUGIN_URI . 'assets/css/tabs.min.css');
			wp_enqueue_style( 'wf-sortable', wpda_form_PLUGIN_URI . 'assets/css/sortable.min.css');
			wp_enqueue_style( 'wf-message-effects', wpda_form_PLUGIN_URI . 'assets/css/message-effects.min.css');
			//wp_enqueue_style( 'wf-wpdevart', wpda_form_PLUGIN_URI . 'assets/wpdevart.min.css');
			wp_enqueue_style( 'wf-wpdevart', wpda_form_PLUGIN_URI . 'assets/wpdevart.css');
		}
	}
	
	//	Enqueue backend form pages scripts
	public function wpda_form_enqueue_admin_scripts() 
	{
		$page = "";
		if(isset($_GET['page'])) {
			$page = $_GET['page'];
			
			//	this will load scripts to only those pags that are in slug
			if ( ! in_array($page, $this->slug) )
				return;
			
			wp_enqueue_script('wf-forms-custom', wpda_form_PLUGIN_URI . 'assets/js/wpdevart-forms-custom.min.js', array('jquery', 'wp-color-picker'), '1.0', true );
			wp_enqueue_script('jquery', array(), '1.0', false );
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('wf-bootstrap', wpda_form_PLUGIN_URI . 'assets/js/bootstrap.min.js', array(), '1.0',true);
			wp_enqueue_script('wf-message-effects', wpda_form_PLUGIN_URI . '/assets/js/message-effects.js', array(), '1.0', true );
			wp_enqueue_script('wf-wpdevart', wpda_form_PLUGIN_URI . 'assets/wpdevart.js', array('jquery'), '1.0', true);
			wp_enqueue_script('wf-ajax_custom_script', wpda_form_PLUGIN_URI . 'ajax/wpdevart-ajax-calls.js', array(), 1.0, false);
			wp_enqueue_script('wf-jquery-form', wpda_form_PLUGIN_URI . '/frontend/js/jquery-form-js.min.js', array(), '1.0', false );
		}
	}
	
	public function uninstall_controller(){
		if(isset( $_POST['wpdevartForms_uninstall_bad'] )   && wp_verify_nonce( $_POST['wpdevartForms_uninstall_bad'], 'wpdevartForms_uninstall')){
			$this->remove_databese_and_deactivete();
			return;
		}
		$this->display_uninstall_main();
	}
	private function remove_databese_and_deactivete(){
		global $wpdb;
		global $wpda_form_table;
		
		/**
		*	remove all attachments of the form
		*/
		// remove attachemnts of submissions of current form 
			$attachment_fields_ids = $wpdb -> get_results ("SELECT id FROM " .$wpda_form_table['fields'] ." WHERE  fieldtype='file' ");
			if(!empty($attachment_fields_ids))
			{
				foreach($attachment_fields_ids as $row):
					$ids[] = $row->id;
				endforeach;
				
				if(count($ids)>1){
					$arr = implode(',',$ids);
					$attachment_fields_submit_values = $wpdb -> get_results ("SELECT field_value FROM " .$wpda_form_table['submissions'] ." WHERE fk_field_id IN ($arr) AND  field_value IS NOT NULL ");
				} else {
					$id = $ids[0];
					$attachment_fields_submit_values = $wpdb -> get_results ("SELECT field_value FROM " .$wpda_form_table['submissions'] ." WHERE fk_field_id = $id AND  field_value IS NOT NULL ");
				}
				
				 foreach($attachment_fields_submit_values as $row)
				 {
					$result =  $row->field_value; 
					//	get the site url with domain
					$site_url= get_site_url();
					// 	convert abs path to relative
					
					$rel_path=  str_replace($site_url,'',$result);
					if( file_exists(ABSPATH .$rel_path))
					{
						unlink(ABSPATH .$rel_path);
					}
				 }
			}
		
		//	remove styling of forms 
		if(get_option('wpdevart_forms_style')){
			delete_option('wpdevart_forms_style');
		}
		
		//drop tables
		
		$wpdb->query( "DROP TABLE IF EXISTS " .$wpda_form_table['submissions'] );
		$wpdb->query( "DROP TABLE IF EXISTS " .$wpda_form_table['submit_time'] );
		$wpdb->query( "DROP TABLE IF EXISTS " .$wpda_form_table['subfields'] );
		$wpdb->query( "DROP TABLE IF EXISTS " .$wpda_form_table['fields']);
		$wpdb->query( "DROP TABLE IF EXISTS " .$wpda_form_table['wpdevart_forms'] );
		
		if(get_option("wpdevart_forms_plugin_version")) {
			delete_option("wpdevart_forms_plugin_version");
		}
		
		// remove attachment folder
		$wp_upload_dir = wp_upload_dir();
		$wpdevart_forms_uploads_dir  = wpda_form_uploads_dir();
		
		//	Delete folder of for wpdevart-forms-attachments
		wpda_form_rrmdir($wp_upload_dir['basedir'].'/'.$wpdevart_forms_uploads_dir);		
		?>
		<div id="message" class="updated fade">
		  <p>The following Database Tables successfully deleted:</p>
		  <p><?php echo $wpdb->prefix; ?>wpda_form_fields,</p>
		  <p><?php echo $wpdb->prefix; ?>wpda_form_forms,</p>
		  <p><?php echo $wpdb->prefix; ?>wpda_form_subfields,</p>
           <p><?php echo $wpdb->prefix; ?>wpda_form_submissions,</p>
		  <p><?php echo $wpdb->prefix; ?>wpda_form_submit_time,</p>
		</div>
		<div class="wrap">
		  <h2>Uninstall Form</h2>
		  <p><strong><a href="<?php echo wp_nonce_url('plugins.php?action=deactivate&amp;plugin=contact-forms-builder/wpdevart-form.php', 'deactivate-plugin_contact-forms-builder/wpdevart-form.php'); ?>">Click Here</a> To Finish the Form Uninstallation</strong></p>
		  <input id="task" name="task" type="hidden" value="" />
		</div>
	  <?php	
	}
	private function display_uninstall_main(){
		global $wpdb;
		?>
        <form method="post" action="admin.php?page=wpda_form_uninstall" style="width:99%;">
			 <?php wp_nonce_field('wpdevartForms_uninstall','wpdevartForms_uninstall_bad'); ?>
              <div class="wrap">
                <span class="uninstall_icon"></span>
                <h2>Uninstall Forms</h2>
                <p>
                  Deactivating Forms plugin does not remove any data that may have been created. To completely remove this plugin, you can uninstall it here.
                </p>
                <p style="color: red;">
                  <strong>WARNING:</strong>
                  Once uninstalled, this can't be undone. You should use a Database Backup plugin of WordPress to back up all the data first.
                </p>
                <p style="color: red">
                  <strong>The following Database Tables will be deleted:</strong>
                </p>
                <table class="widefat">
                  <thead>
                    <tr>
                      <th>Database Tables</th>
                    </tr>
                  </thead>
                  <tr>
                    <td valign="top">
                      <ol>
                          <li><?php echo $wpdb->prefix; ?>wpda_form_fields</li>
                          <li><?php echo $wpdb->prefix; ?>wpda_form_forms</li>
                          <li><?php echo $wpdb->prefix; ?>wpda_form_subfields</li>
                          <li><?php echo $wpdb->prefix; ?>wpda_form_submissions</li>
                          <li><?php echo $wpdb->prefix; ?>wpda_form_submit_time</li>
                      </ol>
                    </td>
                  </tr>
                </table>
                <p style="text-align: center;">
                  Do you really want to uninstall Form ?
                </p>
                <p style="text-align: center;">
                  <input type="checkbox" id="check_yes" value="yes" />&nbsp;<label for="check_yes">Yes</label>
                </p>
                <p style="text-align: center;">
                  <input type="submit" value="UNINSTALL" class="button-primary" onclick="if (check_yes.checked) { 
                                                                                            if (confirm('You are About to Uninstall Form.\nThis Action Is Not Reversible.')) {
                                                                                               
                                                                                            } else {
                                                                                                return false;
                                                                                            }
                                                                                          }
                                                                                          else {
                                                                                            return false;
                                                                                          }" />
                </p>
              </div>
            </form>
          <?php
    
		
		
	}
	public function featured_plugins(){
		$plugins_array=array(
			'coming_soon'=>array(
						'image_url'		=>	wpda_form_PLUGIN_URI.'images/featured_plugins/coming_soon.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-coming-soon-plugin/',
						'title'			=>	'Coming soon and Maintenance mode',
						'description'	=>	'Coming soon and Maintenance mode plugin is an awesome tool to show your visitors that you are working on your website to make it better.'
						),
			'Booking Calendar'=>array(
						'image_url'		=>	wpda_form_PLUGIN_URI.'images/featured_plugins/Booking_calendar_featured.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-booking-calendar-plugin/',
						'title'			=>	'Booking Calendar',
						'description'	=>	'WordPress Booking Calendar plugin is an awesome tool to create a booking system for your website. Create booking calendars in a few minutes.'
						),	
			'youtube'=>array(
						'image_url'		=>	wpda_form_PLUGIN_URI.'images/featured_plugins/youtube.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-youtube-embed-plugin',
						'title'			=>	'WordPress YouTube Embed',
						'description'	=>	'YouTube Embed plugin is an convenient tool for adding videos to your website. Use YouTube Embed plugin to add YouTube videos in posts/pages, widgets.'
						),
            'facebook-comments'=>array(
						'image_url'		=>	wpda_form_PLUGIN_URI.'images/featured_plugins/facebook-comments-icon.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-facebook-comments-plugin/',
						'title'			=>	'WordPress Facebook comments',
						'description'	=>	'Our Facebook comments plugin will help you to display Facebook Comments on your website. You can use Facebook Comments on your pages/posts.'
						),						
			'countdown'=>array(
						'image_url'		=>	wpda_form_PLUGIN_URI.'images/featured_plugins/countdown.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-countdown-plugin/',
						'title'			=>	'WordPress Countdown plugin',
						'description'	=>	'WordPress Countdown plugin is an nice tool for creating countdown timers for your website posts/pages and widgets.'
						),
			'lightbox'=>array(
						'image_url'		=>	wpda_form_PLUGIN_URI.'images/featured_plugins/lightbox.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-lightbox-plugin',
						'title'			=>	'WordPress Lightbox plugin',
						'description'	=>	'WordPress Lightbox Popup is an high customizable and responsive plugin for displaying images and videos in popup.'
						),
			'facebook'=>array(
						'image_url'		=>	wpda_form_PLUGIN_URI.'images/featured_plugins/facebook.jpg',
						'site_url'		=>	'http://wpdevart.com/wordpress-facebook-like-box-plugin',
						'title'			=>	'Facebook Like Box',
						'description'	=>	'Our Facebook like box plugin will help you to display Facebook like box on your wesite, just add Facebook Like box widget to sidebar or insert it into posts/pages and use it.'
						),
			'poll'=>array(
						'image_url'		=>	wpda_form_PLUGIN_URI.'images/featured_plugins/poll.png',
						'site_url'		=>	'http://wpdevart.com/wordpress-polls-plugin',
						'title'			=>	'WordPress Polls plugin',
						'description'	=>	'WordPress Polls plugin is an handy tool for creating polls and survey forms for your visitors. You can use our polls on widgets, posts and pages.'
						),														
			
		);
		?>
        <style>
         .featured_plugin_main{
			 background-color: #ffffff;
			 border: 1px solid #dedede;
			 box-sizing: border-box;
			 float:left;
			 margin-right:20px;
			 margin-bottom:20px;
			 
			 width:450px;
		 }
		.featured_plugin_image{
			padding: 15px;
			display: inline-block;
			float:left;
		}
		.featured_plugin_image a{
		  display: inline-block;
		}
		.featured_plugin_information{			
			float: left;
			width: auto;
			max-width: 282px;

		}
		.featured_plugin_title{
			color: #0073aa;
			font-size: 18px;
			display: inline-block;
		}
		.featured_plugin_title a{
			text-decoration:none;
					
		}
		.featured_plugin_title h4{
			margin:0px;
			margin-top: 20px;
			margin-bottom:8px;			  
		}
		.featured_plugin_description{
			display: inline-block;
		}
        
        </style>
        <script>
		
        jQuery(window).resize(wpdevart_countdown_feature_resize);
		jQuery(document).ready(function(e) {
            wpdevart_countdown_feature_resize();
        });
		
		function wpdevart_countdown_feature_resize(){
			var wpdevart_countdown_width=jQuery('.featured_plugin_main').eq(0).parent().width();
			var count_of_elements=Math.max(parseInt(wpdevart_countdown_width/450),1);
			var width_of_plugin=((wpdevart_countdown_width-count_of_elements*24-2)/count_of_elements);
			jQuery('.featured_plugin_main').width(width_of_plugin);
			jQuery('.featured_plugin_information').css('max-width',(width_of_plugin-160)+'px');
		}
       	</script>
        	<h2>Featured Plugins</h2>
            <br>
            <br>
            <?php foreach($plugins_array as $key=>$plugin) { ?>
            <div class="featured_plugin_main">
            	<span class="featured_plugin_image"><a target="_blank" href="<?php echo $plugin['site_url'] ?>"><img src="<?php echo $plugin['image_url'] ?>"></a></span>
                <span class="featured_plugin_information">
                	<span class="featured_plugin_title"><h4><a target="_blank" href="<?php echo $plugin['site_url'] ?>"><?php echo $plugin['title'] ?></a></h4></span>
                    <span class="featured_plugin_description"><?php echo $plugin['description'] ?></span>
                </span>
                <div style="clear:both"></div>                
            </div>
            <?php } 
	}
} 
?>
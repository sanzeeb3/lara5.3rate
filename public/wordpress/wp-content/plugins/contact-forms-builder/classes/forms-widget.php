<?php
/**
 * wpdevart Forms Widget
 * Adds wpdevart forms dropdown list widgets  
 * @package wpdevart Forms
 * @since 1.0
 */ 

if ( ! defined( 'ABSPATH' ) ) exit;

class wpdevartFormsWidget extends WP_Widget {
	
	function __construct() {
		
		$widget_options = array(
		'classname' =>	'wpdevart_forms',
		'name'	=>	'wpdevart Forms',
		'description' => 'Display any form created by wpdevart Forms'
		);
		
		parent::__construct('wpdevart-forms', 'wpdevart Forms Widget', $widget_options);
	}


	//	set widget's default arguments that how it should look on admin's widgets when moved to a widget area
	function widget( $args, $instance ) {
		extract ( $args, EXTR_SKIP );
		$title = ( $instance['title'] ) ? $instance['title'] : 'Title of wpdevart Forms';
		$forms_list = ( $instance['forms_list'] ) ? $instance['forms_list'] : 'Select a form created by wpdevart Forms';
		echo $before_widget;
		//	OUTPUT BELOW AT FRONTEND
		echo '<h2 class="widget-title">' .$instance["title"] .'</h2>';
		echo do_shortcode('[wpdevart_forms id=' .$instance["forms_list"] .']'); 
		echo $after_widget; 
	}
	
	// function form here is wordpress built-in function
	function form( $instance ) {
		$defaults = array(
			'title'	=> '',
			'forms_list'	=> ''
		);

	$instance = wp_parse_args((array) $instance, $defaults); 
			
	?>
	<div id="wpdevart-forms-widget" class="wpdevart-widget">
		<p>
        	<label for="<?php echo $this->get_field_id('title'); ?>">Form Title  </label>
			<input name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>"  type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" class="title" placeholder="optional form title" />
      	</p> 
      
		<?php
		global $wpdb;
		global $wpda_form_table;
		
		//	get all the form [meta data ] to print them on page 
		$wpdevart_forms = $wpdb->get_results("SELECT * FROM ".$wpda_form_table['wpdevart_forms']);
	
		if(count($wpdevart_forms) > 0){
		?>      
            <label for="<?php echo $this->get_field_id('forms_list'); ?>">Select Form  </label> 
            <div class="wpdevart-select"> 
                <select name="<?php echo $this->get_field_name('forms_list'); ?>" id="<?php echo $this->get_field_id('forms_list'); ?>">
                <?php
                    foreach ($wpdevart_forms as $key => $form ): ?>
                        <option value="<?php echo $form->id ?>"<?php selected( $instance['forms_list'], $form->id ); ?> > 
                            <?php echo $form->name; ?>
                        </option>
                <?php
                    endforeach; ?>
                </select>
           </div> 
           <br>
<?php	} else {
			echo '<label>No any form creaetd yet. Please create a form from wpdevart Forms, then a list of forms will be displayed here.</label>';
		}
?>     
	</div><!-- / .wpdevart-widget -->

<?php
	} // function form( $instance ) {
	
} // class wpdevartText extends WP_Widget {

/**
 * Register the widget
 */
function wpda_form_widget_init() {
	register_widget("wpdevartFormsWidget");
}
add_action('widgets_init','wpda_form_widget_init');

 global $pagenow;
 if( $pagenow == 'widgets.php') {
	echo '
	<style>
		#wpdevart-forms-widget {line-height:25px;}
		#wpdevart-forms-widget .title,
		#wpdevart-forms-widget .wpdevart-select {
			height:34px;
			background-color:#fff;
			border:1px solid #ddd;
			width:100%;
		}
		#wpdevart-forms-widget .wpdevart-select {
			overflow: hidden;
			background: url("' .wpda_form_PLUGIN_URI .'assets/images/drop-arrow2.png") right no-repeat;
		}
		#wpdevart-forms-widget .wpdevart-select select {
			background:transparent;
			height:34px;
			width:110%;
			padding-left:8px;
			font-size: 14px;
			color:#333;
			line-height: 1;
			border: 0;
			border-radius: 0;
			-webkit-appearance: none;
			box-shadow:none !important;
		}
	</style>';
 }

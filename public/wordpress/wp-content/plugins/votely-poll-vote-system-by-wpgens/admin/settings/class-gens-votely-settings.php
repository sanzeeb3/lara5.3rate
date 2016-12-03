<?php

/**
 * Admin Part of Plugin, dashboard and options.
 *
 * @package    gens_votely
 * @subpackage gens_votely/admin
 */
class Gens_Votely_Settings extends Gens_Votely_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0 
	 * @access   private
	 * @var      string    $gens_votely    The ID of this plugin.
	 */
	private $gens_votely;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $gens_votely       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $gens_votely ) {

		$this->gens_votely = $gens_votely;
	}

	/**
	 * Creates our settings sections with fields etc. 
	 *
	 * @since    1.0.0
	 */
	public function settings_api_init(){

		// register_setting( $option_group, $option_name, $settings_sanitize_callback );
		register_setting(
			$this->gens_votely . '_options',
			$this->gens_votely . '_options',
			array( $this, 'settings_sanitize' )
		);

		// add_settings_section( $id, $title, $callback, $menu_slug );
		add_settings_section(
			$this->gens_votely . '-display-options', // section
			apply_filters( $this->gens_votely . '-display-section-title', __( '', $this->gens_votely ) ),
			array( $this, 'display_options_section' ),
			$this->gens_votely
		);

		add_settings_field(
			'security-layer',
			apply_filters( $this->gens_votely . '-security-layer-label', __( 'Limit votes by', $this->gens_votely ) ),
			array( $this, 'security_layer' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options' // section to add to
		);

		add_settings_field(
			'share-icons',
			apply_filters( $this->gens_votely . '-share-icons', __( 'Turn off sharing', $this->gens_votely ) ),
			array( $this, 'share_icons' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options'
		);
		add_settings_field(
			'first-answer-color',
			apply_filters( $this->gens_votely . '-first-color-label', __( 'First Choice Color', $this->gens_votely ) ),
			array( $this, 'first_answer_color' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options'
		);
		add_settings_field(
			'second-answer-color',
			apply_filters( $this->gens_votely . '-second-color-label', __( 'Second Choice Color', $this->gens_votely ) ),
			array( $this, 'second_answer_color' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options'
		);
		add_settings_field(
			'square-circle',
			apply_filters( $this->gens_votely . '-square-circle-label', __( 'Line or Circle look?', $this->gens_votely ) ),
			array( $this, 'square_circle' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options'
		);
		add_settings_field(
			'vote-text',
			apply_filters( $this->gens_votely . '-vote-text-label', __( 'Vote Message', $this->gens_votely ) ),
			array( $this, 'vote_text' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options'
		);
		add_settings_field(
			'already-vote-text',
			apply_filters( $this->gens_votely . '-already-vote-text-label', __( 'Already Voted Message', $this->gens_votely ) ),
			array( $this, 'already_vote_text' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options'
		);
		add_settings_field(
			'vote-closed-text',
			apply_filters( $this->gens_votely . '-vote-closed-text-label', __( 'Voting is Closed Message', $this->gens_votely ) ),
			array( $this, 'vote_closed_text' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options'
		);
		add_settings_field(
			'thanks-text',
			apply_filters( $this->gens_votely . '-thanks-text-label', __( 'Thank You Message', $this->gens_votely ) ),
			array( $this, 'thanks_text' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options'
		);
		/*
		add_settings_field(
			'only-users',
			apply_filters( $this->gens_votely . '-only-users-label', __( 'Only registered users can vote', $this->gens_votely ) ),
			array( $this, 'only_registered_users' ),
			$this->gens_votely,
			$this->gens_votely . '-display-options' 
		);
		*/
	}

	/**
	 * Creates a settings section
	 *
	 * @since 		1.0.0
	 * @param 		array 		$params 		Array of parameters for the section
	 * @return 		mixed 						The settings section
	 */
	public function display_options_section( $params ) {

		echo '<p>' . $params['title'] . '</p>';

	} // display_options_section()


	/**
	 * Registered Users
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function only_registered_users() {

		$options 	= get_option( $this->gens_votely . '_options' );
		$option 	= 0;

		if ( ! empty( $options['disable-bar'] ) ) {

			$option = $options['disable-bar'];

		}

		?><input type="checkbox" id="<?php echo $this->gens_votely; ?>_options[disable-bar]" name="<?php echo $this->gens_votely; ?>_options[disable-bar]" value="1" <?php checked( $option, 1 , true ); ?> />
		<p class="description">If you want to enable votes to only registered users, check this box.</p> <?php
	}

	/**
	 * Time to read text field
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function share_icons() {

		$options  	= get_option( $this->gens_votely . '_options' );
		$option 	= '';

		if ( ! empty( $options['share-icons'] ) ) {
			$option = $options['share-icons'];
		}

		?>
		<input type="checkbox" id="<?php echo $this->gens_votely; ?>_options[share-icons]" name="<?php echo $this->gens_votely; ?>_options[share-icons]" value="1" <?php checked( $option, 1 , true ); ?>>
		<p class="description">Check this box to remove share icons.</p> 
		<?php
	}
	/**
	 * Time to read text field
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function first_answer_color() {

		$options  	= get_option( $this->gens_votely . '_options' );
		$option 	= '';

		if ( ! empty( $options['first-color'] ) ) {
			$option = $options['first-color'];
		}

		?>
		<input class="cpicker" type="text" id="<?php echo $this->gens_votely; ?>_options[first-color]" name="<?php echo $this->gens_votely; ?>_options[first-color]" value="<?php echo esc_attr( $option ); ?>">
		<p class="description">Fancy colorpicker, everyone loves colorpickers.</p> 
		<?php
	}

	/**
	 * Time to read text field
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function second_answer_color() {

		$options  	= get_option( $this->gens_votely . '_options' );
		$option 	= '';

		if ( ! empty( $options['second-color'] ) ) {
			$option = $options['second-color'];
		}

		?>
		<input class="cpicker" type="text" id="<?php echo $this->gens_votely; ?>_options[second-color]" name="<?php echo $this->gens_votely; ?>_options[second-color]" value="<?php echo esc_attr( $option ); ?>">
		<p class="description">And another one</p> 
		<?php
	}
	

	/**
	 * Vote text field
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function square_circle() {

		$options  	= get_option( $this->gens_votely . '_options' );
		$option 	= '';

		if ( ! empty( $options['square-circle'] ) ) {
			$option = $options['square-circle'];
		}

		?>
		<select id="<?php echo $this->gens_votely; ?>_options[square-circle]" name="<?php echo $this->gens_votely; ?>_options[square-circle]" >
			<option value="circle" <?php selected( $option, "circle" ); ?> >Circle</option>
			<option value="square" <?php selected( $option, "square" ); ?> >Line (Fat line)</option>
		</select>
		<p class="description">Choose between circle or line design. WARNING: Circle design will break if answer has many letters, it fits only small answers.</p>
		<?php

	}

	/**
	 * Vote text field
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function vote_text() {

		$options  	= get_option( $this->gens_votely . '_options' );
		$option 	= 'Vote!';

		if ( ! empty( $options['vote-text'] ) ) {
			$option = $options['vote-text'];
		}

		?>
		<input type="text" id="<?php echo $this->gens_votely; ?>_options[vote-text]" name="<?php echo $this->gens_votely; ?>_options[vote-text]" value="<?php echo esc_attr( $option ); ?>">
		<?php
	}

	/**
	 * Time to read text field
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function already_vote_text() {

		$options  	= get_option( $this->gens_votely . '_options' );
		$option 	= 'You have already voted.';

		if ( ! empty( $options['already-vote-text'] ) ) {
			$option = $options['already-vote-text'];
		}

		?>
		<input type="text" id="<?php echo $this->gens_votely; ?>_options[already-vote-text]" name="<?php echo $this->gens_votely; ?>_options[already-vote-text]" value="<?php echo esc_attr( $option ); ?>">
		<?php
	}

	/**
	 * Time to read text field
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function vote_closed_text() {

		$options  	= get_option( $this->gens_votely . '_options' );
		$option 	= 'Vote is closed.';

		if ( ! empty( $options['vote-closed-text'] ) ) {
			$option = $options['vote-closed-text'];
		}

		?>
		<input type="text" id="<?php echo $this->gens_votely; ?>_options[vote-closed-text]" name="<?php echo $this->gens_votely; ?>_options[vote-closed-text]" value="<?php echo esc_attr( $option ); ?>">
		<?php
	}

	/**
	 * Time to read text field
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function thanks_text() {

		$options  	= get_option( $this->gens_votely . '_options' );
		$option 	= 'Thank you for the vote!';

		if ( ! empty( $options['thanks-text'] ) ) {
			$option = $options['thanks-text'];
		}

		?>
		<input type="text" id="<?php echo $this->gens_votely; ?>_options[thanks-text]" name="<?php echo $this->gens_votely; ?>_options[thanks-text]" value="<?php echo esc_attr( $option ); ?>">
		<?php
	}

	/**
	 * Enable Bar Field
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	public function security_layer() {

		$options 	= get_option( $this->gens_votely . '_options' );
		$option 	= array();

		if ( ! empty( $options['security-layer'] ) ) {
			$option = $options['security-layer'];
		}

	?>
			<p>
				<input type="checkbox" id="<?php echo $this->gens_votely; ?>_options[security-layer]" name="<?php echo $this->gens_votely; ?>_options[security-layer][]" value="cache" <?php checked( in_array( "cache", $option ) ); ?> />
	   			Cookie		
	   		</p>
	   		<p>
				<input type="checkbox" id="<?php echo $this->gens_votely; ?>_options[security-layer]" name="<?php echo $this->gens_votely; ?>_options[security-layer][]" value="ip" <?php checked( in_array( "ip", $option ) ); ?> />
	   			IP Address		
	   		</p>
			<p class="description">You probably want both of these checked.But be aware, we cant help against proxies.</p>
	<?php 
	}


}

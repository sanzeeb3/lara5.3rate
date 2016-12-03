<?php
/*
Plugin Name: Front End Registration and Login
Plugin URI: https://pippinsplugins.com/creating-custom-front-end-registration-and-login-forms
Description: Provides simple front end registration and login forms
Version: 1.0
Author: Pippin Williamson
Author URI: https://pippinsplugins.com
*/


// user registration login form
function pippin_registration_form() {
 
	// only show the registration form to non-logged-in members
	if(!is_user_logged_in()) {
 
		global $pippin_load_css;
 
		// set this to true so the CSS is loaded
		$pippin_load_css = true;
 
		// check to make sure user registration is enabled
		$registration_enabled = get_option('users_can_register');
 
		// only show the registration form if allowed
		if($registration_enabled) {
			$output = pippin_registration_form_fields();
		} else {
			$output = __('User registration is not enabled');
		}
		return $output;
	}
}
add_shortcode('register_form', 'pippin_registration_form');

// user login form
function pippin_login_form() {
 
	if(!is_user_logged_in()) {
 
		global $pippin_load_css;
 
		// set this to true so the CSS is loaded
		$pippin_load_css = true;
 
		$output = pippin_login_form_fields();
	} else {
		// could show some logged in user info here
		 $output = 'logged in!';
	}
	return $output;
}
add_shortcode('login_form', 'pippin_login_form');

// registration form fields
function pippin_registration_form_fields() {
 
	ob_start(); ?>	
		<h3 class="pippin_header"><?php _e('Register New Account'); ?></h3>
 
		<?php 
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<form id="pippin_registration_form" class="pippin_form" action="" method="POST">
			<fieldset>
				<p>
					<label for="pippin_user_Login"><?php _e('Username'); ?></label>
					<input name="pippin_user_login" id="pippin_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="pippin_user_email"><?php _e('Email'); ?></label>
					<input name="pippin_user_email" id="pippin_user_email" class="required" type="email"/>
				</p>
				<p>
					<label for="pippin_user_first"><?php _e('First Name'); ?></label>
					<input name="pippin_user_first" id="pippin_user_first" type="text"/>
				</p>
				<p>
					<label for="pippin_user_last"><?php _e('Last Name'); ?></label>
					<input name="pippin_user_last" id="pippin_user_last" type="text"/>
				</p>
				<p>
					<label for="password"><?php _e('Password'); ?></label>
					<input name="pippin_user_pass" id="password" class="required" type="password"/>
				</p>
				<p>
					<label for="password_again"><?php _e('Password Again'); ?></label>
					<input name="pippin_user_pass_confirm" id="password_again" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="pippin_register_nonce" value="<?php echo wp_create_nonce('pippin-register-nonce'); ?>"/>
					<input type="submit" value="<?php _e('Register Your Account'); ?>"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}

// login form fields
function pippin_login_form_fields() {
 
	ob_start(); ?>
		<h3 class="pippin_header"><?php _e('Login'); ?></h3>
 
		<?php
		// show any error messages after form submission
		pippin_show_error_messages(); ?>
 
		<form id="pippin_login_form"  class="pippin_form"action="" method="post">
			<fieldset>
				<p>
					<label for="pippin_user_Login">Username</label>
					<input name="pippin_user_login" id="pippin_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="pippin_user_pass">Password</label>
					<input name="pippin_user_pass" id="pippin_user_pass" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="pippin_login_nonce" value="<?php echo wp_create_nonce('pippin-login-nonce'); ?>"/>
					<input id="pippin_login_submit" type="submit" value="Login"/>
				</p>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}

function pippin_add_new_member() {
  	if (isset( $_POST["pippin_user_login"] ) && wp_verify_nonce($_POST['pippin_register_nonce'], 'pippin-register-nonce')) {
		$user_login		= $_POST["pippin_user_login"];	
		$user_email		= $_POST["pippin_user_email"];
		$user_first 	= $_POST["pippin_user_first"];
		$user_last	 	= $_POST["pippin_user_last"];
		$user_pass		= $_POST["pippin_user_pass"];
		$pass_confirm 	= $_POST["pippin_user_pass_confirm"];
 
		// this is required for username checks
		require_once(ABSPATH . WPINC . '/registration.php');
 
		if(username_exists($user_login)) {
			// Username already registered
			pippin_errors()->add('username_unavailable', __('Username already taken'));
		}
		if(!validate_username($user_login)) {
			// invalid username
			pippin_errors()->add('username_invalid', __('Invalid username'));
		}
		if($user_login == '') {
			// empty username
			pippin_errors()->add('username_empty', __('Please enter a username'));
		}
		if(!is_email($user_email)) {
			//invalid email
			pippin_errors()->add('email_invalid', __('Invalid email'));
		}
		if(email_exists($user_email)) {
			//Email address already registered
			pippin_errors()->add('email_used', __('Email already registered'));
		}
		if($user_pass == '') {
			// passwords do not match
			pippin_errors()->add('password_empty', __('Please enter a password'));
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			pippin_errors()->add('password_mismatch', __('Passwords do not match'));
		}
 
		$errors = pippin_errors()->get_error_messages();
 
		// only create the user in if there are no errors
		if(empty($errors)) {
 
			$new_user_id = wp_insert_user(array(
					'user_login'		=> $user_login,
					'user_pass'	 		=> $user_pass,
					'user_email'		=> $user_email,
					'first_name'		=> $user_first,
					'last_name'			=> $user_last,
					'user_registered'	=> date('Y-m-d H:i:s'),
					'role'				=> 'subscriber'
				)
			);
			if($new_user_id) {
				// send an email to the admin alerting them of the registration
				wp_new_user_notification($new_user_id);
 
				// log the new user in
				wp_setcookie($user_login, $user_pass, true);
				wp_set_current_user($new_user_id, $user_login);	
				do_action('wp_login', $user_login);
 
				// send the newly created user to the home page after logging them in
				wp_redirect(home_url()); exit;
			}
 
		}
 
	}
}
add_action('init', 'pippin_add_new_member');

// used for tracking error messages
function pippin_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function pippin_show_error_messages() {
	if($codes = pippin_errors()->get_error_codes()) {
		echo '<div class="pippin_errors">';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = pippin_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}

// register our form css
function pippin_register_css() {
	wp_register_style('pippin-form-css', plugin_dir_url( __FILE__ ) . '/css/forms.css');
}
add_action('init', 'pippin_register_css');

// load our form css
function pippin_print_css() {
	global $pippin_load_css;
 
	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $pippin_load_css )
		return; // this means that neither short code is present, so we get out of here
 
	wp_print_styles('pippin-form-css');
}
add_action('wp_footer', 'pippin_print_css');
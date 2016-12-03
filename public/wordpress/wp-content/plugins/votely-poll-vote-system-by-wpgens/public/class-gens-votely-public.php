<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Gens_Votely
 * @subpackage Gens_Votely/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Gens_Votely
 * @subpackage Gens_Votely/public
 * @author     Your Name <email@example.com>
 */
class Gens_Votely_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $gens_votely    The ID of this plugin.
	 */
	private $gens_votely;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $gens_votely       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $gens_votely, $version ) {

		$this->gens_votely = $gens_votely;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $post;
		$votely_active = get_post_meta($post->ID,"_gens_votely_active",true);
		if($votely_active == "yes") {
				wp_enqueue_style( $this->gens_votely, plugin_dir_url( __FILE__ ) . 'css/gens-votely-public.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the scripts for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post;
		$votely_active = get_post_meta($post->ID,"_gens_votely_active",true);
		if($votely_active == "yes") {
			wp_enqueue_script( $this->gens_votely, plugin_dir_url( __FILE__ ) . 'js/gens-votely-public.js', array( 'jquery' ), $this->version, false );
			wp_localize_script($this->gens_votely, 'admin_urls', 
				array( 
				'admin_ajax' => admin_url( 'admin-ajax.php'),
				'post_id' => get_queried_object_id(),
				'postNonce' => wp_create_nonce( 'myajax-post-nonce' )
				)
			);
		}


	}

	/**
	 * Add Votely below post content
	 *
	 * @since    2.0.0
	 */
	public function insert_poll($content) {
		if(!is_single())
		  return $content;
		
		$votely_active = get_post_meta(get_the_ID(), '_gens_votely_active', true);
		$custom_content = "";
		if($votely_active == "yes") {
			ob_start();
			$this->gens_votely_html();
			$custom_content .= ob_get_contents();
			ob_end_clean();
		}
		$content = $content . $custom_content;
		return $content;
	}

	/**
	 * Call for Votely HTML part
	 *
	 * @since    2.0.0
	 */
	public function gens_votely_html() {
		global $post;
		$votely_options = get_option("gens-votely_options");
		$votely_active = get_post_meta($post->ID,"_gens_votely_active",true);
		$can_vote = get_post_meta($post->ID,"_gens_votely_vote_active",true);
		$question = get_post_meta($post->ID,"_gens_votely_question",true);
		$first_answer = get_post_meta($post->ID,"_gens_votely_first_answer",true);
		$second_answer = get_post_meta($post->ID,"_gens_votely_second_answer",true);
		$first_value = get_post_meta($post->ID,"_gens_votely_first_vote_val",true);
		if(!$first_value) {$first_value = 0;}
		$second_value = get_post_meta($post->ID,"_gens_votely_second_vote_val",true);
		if(!$second_value) {$second_value = 0;}
		$thanks_msg = (isset($votely_options["thanks-text"]) && $votely_options["thanks-text"] != false) ? $votely_options['thanks-text'] : "Thank you for the vote!";
		$vote_msg = (isset($votely_options["vote-text"]) && $votely_options["vote-text"] != false) ? $votely_options['vote-text'] : "Vote!";
		$vote_closed_msg = (isset($votely_options["vote-closed-text"]) && $votely_options["vote-closed-text"] != false) ? $votely_options['vote-closed-text'] : "Voting is closed.";
		$square = (isset($votely_options["square-circle"]) && $votely_options["square-circle"] == "square") ? "gens_square" : "";
		// TODO: Dividing with zero.
		$all_votes = (int)$first_value + (int)$second_value;
		$first_percentage = ($first_value == 0) ? 100 : 100 - ($first_value / $all_votes * 100);
		$second_percentage = ($second_value == 0) ? 100 : 100 - ($second_value / $all_votes * 100);

		// Check for IP and cookies
		$security = (isset($votely_options["security-layer"]) && $votely_options["security-layer"] != false) ? $votely_options['security-layer'] : array();
		$voted = "";
		// IP
		if(in_array("ip",$security)) {
			$userIP = $this->getUserIP();
			$votedIPs = get_post_meta($post->ID,"_gens_votely_voted_ips",false);
			if(in_array($userIP,$votedIPs)) {
				$voted = " voted";
			}
		}
		// Check Cookie
		if(in_array("cache",$security)) {
			if(isset($_COOKIE['gens_votely_'.$post->ID])) {
				$voted = " voted";
			}
		}

		if($can_vote == "no") {
			$voted = " voted";
			$thanks_msg = $vote_closed_msg;
		}

		require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/gens-votely-public-display.php';

	}


	/**
	 * Get User IP
	 *
	 * @since    1.2.0
	 */
	public function getUserIP()
	{
	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];

	    if(filter_var($client, FILTER_VALIDATE_IP))
	    {
	        $ip = $client;
	    }
	    elseif(filter_var($forward, FILTER_VALIDATE_IP))
	    {
	        $ip = $forward;
	    }
	    else
	    {
	        $ip = $remote;
	    }

	    return $ip;
	}


	/**
	 * Ajax update votes
	 *
	 * @since    1.2.0
	 */
	public function update_votes() {

		// Check nounce - security thingy
		$nonce = $_POST['postNonce'];
		if ( ! wp_verify_nonce( $nonce, 'myajax-post-nonce' ) ) {
			die ( 'Busted!');
		}
		$update = false;
		$votely_options = get_option("gens-votely_options");
		$alrdy_vote_msg = (isset($votely_options["already-vote-text"]) && $votely_options["already-vote-text"] != false) ? $votely_options['already-vote-text'] : "You have already voted.";
		$security = (isset($votely_options["security-layer"]) && $votely_options["security-layer"] != false) ? $votely_options['security-layer'] : array();
		$vote_closed_msg = (isset($votely_options["vote-closed-text"]) && $votely_options["vote-closed-text"] != false) ? $votely_options['vote-closed-text'] : "Voting is closed.";

		$post_id = $_POST['post_id'];
		$choice = $_POST['choice'];

		// Closed voting ?
		if(get_post_meta($post_id,"_gens_votely_vote_active",true) == "no") {
			$return = array(
					'update' => $update,
					'msg'	 => $vote_closed_msg
				);
			return wp_send_json($return);
		}

		// Check IP
		if(in_array("ip",$security)) {
			$userIP = $this->getUserIP();
			$votedIPs = get_post_meta($post_id,"_gens_votely_voted_ips",false);
			if(in_array($userIP,$votedIPs)) {
				$return = array(
					'update' => $update,
					'msg'	 => $alrdy_vote_msg
				);
				return wp_send_json($return);
			} else {
				$update = update_post_meta($post_id,"_gens_votely_voted_ips",$userIP);
			}
		}
		// Check Cookie
		if(in_array("cache",$security)) {
			if(isset($_COOKIE['gens_votely_'.$post_id])) {
				$return = array(
					'update' => $update,
					'msg'	 => $alrdy_vote_msg
				);
				return wp_send_json($return);
			}
		}

		if($choice == "first") {
			$first_choice = get_post_meta($post_id,"_gens_votely_first_vote_val",true);
			$update = update_post_meta($post_id,"_gens_votely_first_vote_val",$first_choice + 1);
		} else {
			$second_choice = get_post_meta($post_id,"_gens_votely_second_vote_val",true);
			$update = update_post_meta($post_id,"_gens_votely_second_vote_val",$second_choice + 1);
		}
		
		$return = array(
			'choice' => $choice,
			'update' => $update
		);

		return wp_send_json($return);
	}

}
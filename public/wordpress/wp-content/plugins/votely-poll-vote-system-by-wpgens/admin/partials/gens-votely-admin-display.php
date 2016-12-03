<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    gens_votely
 * @subpackage gens_votely/admin/partials
 */
?>
<?php
//flush rewrite rules when we load this page!
flush_rewrite_rules();
?>
<div class="wrap">
	<?php
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
		$this->gens_votely_render_tabs(); 
	?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-2" class="postbox-container">
				<?php 
				// If no tab or general
				switch ($tab) {
					default: ?>
						<div id="top-sortables" class="meta-box-sortables ui-sortable">
							<div id="itsec_self_protect" class="postbox ">
								<h3 class="hndle"><span>Votely by WPGens</span></h3>
								<div class="inside">
									<p>Thanks for installing Votely plugin!</p>
									<p>This plugin is lightweight and will help you boost user engagement by adding simple and eye cachy vote/poll option at the end of each post where Votely has been setup.
									Its coded with best practice, it is super fast and you can expect regular updates with new options added, so check back here often. Also, try out our other plugins by visiting <a href="http://www.wpgens.com" target="_blank">WPGens.com</a></p>
							
								</div>
							</div>
						</div>
						<form method="post" action="options.php">				
							<div id="normal-sortables" class="meta-box-sortables ui-sortable">
								<div id="itsec_get_started" class="postbox ">
									<h3 class="hndle"><span>General Settings</span></h3>
									<div class="inside">
										<?php 
											settings_fields( 'gens-votely_options' );

											do_settings_sections( 'gens-votely' );

											submit_button( 'Save Settings' );
										?>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</form>
					<?php	
						break;
				} ?>

			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div id="priority_side-sortables" class="meta-box-sortables ui-sortable">
					<div id="itsec_security_updates" class="postbox ">
						<h3 class="hndle"><span>Subscribe to super cool plugins</span></h3>
						<div class="inside">
							<div id="mc_embed_signup">
								<p>Check out our other plugins by visiting <a href="http://www.wpgens.com" target="_blank">WPGens.com</a></p>
								<p>Our plugins are coded with best practices in mind, they will not slow down your site or spam database.
									Guaranteed to work and always up to date. 
									<br/>Subscribe to get notificed once new plugin is out. No spam, just one mail per one new plugin.</p>
									<p>We are just starting, so each subscriber means a lot to us...no spam...pinky promise.</p>
								<!-- Begin MailChimp Signup Form -->
								<div id="mc_embed_signup">
								<form action="//itsgoran.us10.list-manage.com/subscribe/post?u=2fd0b1cc409e74a29dbf5d7a9&amp;id=d2653040af" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
								    <div id="mc_embed_signup_scroll">
									
								<div class="mc-field-group">
									<label for="mce-EMAIL">Email Address </label>
									<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
								</div>
									<div id="mce-responses" class="clear">
										<div class="response" id="mce-error-response" style="display:none"></div>
										<div class="response" id="mce-success-response" style="display:none"></div>
									</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
								    <div style="position: absolute; left: -5000px;"><input type="text" name="b_2fd0b1cc409e74a29dbf5d7a9_d2653040af" tabindex="-1" value=""></div>
								    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
								    </div>
								</form>
								</div>
								<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
								<!--End mc_embed_signup-->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
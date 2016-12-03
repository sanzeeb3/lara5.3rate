<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
$header_title = "";
	if( isset($_GET['page']) ) {
		$page = sanitize_text_field($_GET['page']);
		$chunks = explode( '-', $page);
		if( $chunks[0] == 'wpdevart' ) {
			if($chunks[1] == "options") $header_title = "Theme";
			if($chunks[1] == "demo") $header_title = "Theme";
			if($chunks[1] == "system") $header_title = "Theme";
			if($chunks[1] == "forms") $header_title = "Forms";
			if($chunks[1] == "sliders") $header_title = "Sliders";				
		}
	}
?>
<header class="header">
    <div class="main-bar">
        <div class="col-sm-5">
            <div class="logo"><a href="http://wpdevart.com/" target="_blank"><img src=<?php echo wpda_form_PLUGIN_URI . "assets/images/logo.png"; ?> alt="wpdevart Forms"><span class="logo-title">
				<?php echo $header_title;?>
			</span></a></div>
        </div>
        <div class="col-sm-7">
            <div class="theme-info">
                <div class="theme-version"><span>V <?php echo wpda_form_PLUGIN_VERSION; ?></span><a target="_blank" href="http://wpdevart.com/wordpress-contact-form-plugin" style="color: #B4272C; font-weight: bold; font-size: 18px;text-decoration: none;">(Upgrade to Pro Version)</a></div>
                <ul  class="list-inline">
                    <li><a href="http://wpdevart.com/wordpress-contact-form-plugin" target="_blank">Documentation</a></li>
                    <li><a href="http://wpdevart.com/wordpress-contact-form-plugin" target="_blank">Support</a></li>
                    <li><a href="http://wpdevart.com/wordpress-contact-form-plugin" target="_blank">More</a></li>
                </ul>
            </div><!-- .theme-info -->
        </div>
        <div class="clearfix"></div>
    </div>
	<?php
		if( wp_get_theme() == 'wpdevart' || wp_get_theme() == 'wpdevart Child' || wp_get_theme() == 'wpdevart Theme' || wp_get_theme() == 'wpdevart Child Theme' ) {
				//	if wpdevart Theme is active, then we do not add sidebar menu because
				//	wpdevart Theme automatically adds sidebar menu items under wpdevart tab
	?>
    <div class="lower-bar">
        <ul class="options-links-tabs">
            <li <?php echo $active_class = ($chunks[1] == "options") ? "class='active'" : ""; ?>><a href="<?php echo admin_url() .'admin.php?page=wpdevart-options' ?>">THEME Options</a></li>
            <?php if (class_exists('wpdevartForms')) { ?>
            <li <?php echo $active_class = ($chunks[1] == "forms") ? "class='active'" : ""; ?>><a href="<?php echo admin_url() .'admin.php?page=wpdevart-forms-list' ?>">wpdevart Forms</a></li>
			<?php } if (class_exists('wpdevartSliders')) { ?>
            <li <?php echo $active_class = ($chunks[1] == "sliders") ? "class='active'" : ""; ?>><a href="<?php echo admin_url() .'admin.php?page=wpdevart-sliders-list' ?>">wpdevart Sliders</a></li>
			<?php } ?>
            <li <?php echo $active_class = ($chunks[1] == "demo") ? "class='active'" : ""; ?>><a href="<?php echo admin_url() .'admin.php?page=wpdevart-demo-importer' ?>">DEMO Importer</a></li>
            <li <?php echo $active_class = ($chunks[1] == "system") ? "class='active'" : ""; ?>><a href="<?php echo admin_url() .'admin.php?page=wpdevart-system-status' ?>#">SYSTEM Status</a></li>
        </ul>
        <div class="clearfix"></div>
    </div>
	<?php } ?>
</header><!-- / .wpdevart-header -->
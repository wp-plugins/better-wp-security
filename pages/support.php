<?php
	global $BWPS, $wpdb;
	$opts = $BWPS->getOptions();
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('System Status', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
	
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Support', 'better-wp-security'); ?></h3>
				<div class="inside">
					<p><?php _e('Please visit the', 'better-wp-security'); ?> <a href="http://bit51.com/software/better-wp-security/">Better WP Security</a> <?php _e('homepage for support and change-log', 'better-wp-security'); ?></p>
					<h4><?php _e('Support Articles', 'better-wp-security'); ?></h4>
					<ul>
						<li><a href="http://bit51.com/2011/09/what-is-changed-by-better-wp-security/" target="_blank"><?php _e('What is Changed By Better WP Security', 'better-wp-security'); ?></a></li>
						<li><a href="http://bit51.com/2011/09/fixing-better-wp-security-lockouts/" target="_blank"><?php _e('Fixing Better WP Security Lockouts', 'better-wp-security'); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
		
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
	</div>
	
</div>
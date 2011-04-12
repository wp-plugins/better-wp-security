<?php
	global $BWPS;
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('System Status', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
	
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Better WP Security System Status', 'better-wp-security'); ?></h3>
				<div class="inside">
					<?php $BWPS->status_getStatus(); ?>
				</div>
			</div>
		</div>
		
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		<div class="clear"></div>
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Support', 'better-wp-security'); ?></h3>
				<div class="inside">
					<p><?php _e('Please visit the', 'better-wp-security'); ?> <a href="http://www.chriswiegman.com/projects/better-wp-security/">Better WP Security</a> <?php _e('homepage for support and change-log', 'better-wp-security'); ?></p>
				</div>
			</div>
		</div>
		
	</div>
	
</div>
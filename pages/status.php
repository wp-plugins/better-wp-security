<?php
	require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/status.php');
	
	$BWPS_status = new BWPS_status();
?>

<div class="wrap" >

	<h2>Better WP Security - System Status</h2>
	
	<div id="poststuff" class="ui-sortable">
	
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Better WP Security System Status</h3>
				<div class="inside">
					<?php $BWPS_status->getStatus(); ?>
				</div>
			</div>
		</div>
		
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		<div class="clear"></div>
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Support</h3>
				<div class="inside">
					<p>Please visit the <a href="http://www.chriswiegman.com/projects/wordpress/better-wp-security/">Better WP Security</a> homepage for support and change-log</p>
				</div>
			</div>
		</div>
		
	</div>
	
</div>
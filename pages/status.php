<?php
	global $BWPS;
?>

<div class="wrap" >

	<h2>Better WP Security - System Status</h2>
	
	<div id="poststuff" class="ui-sortable">
	
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Better WP Security System Status</h3>
				<div class="inside">
					<?php $BWPS->status_getStatus(); ?>
				</div>
			</div>
		</div>
		
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		<div class="clear"></div>
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Support</h3>
				<div class="inside">
					<p>Please visit the <a href="http://www.chriswiegman.com/projects/better-wp-security/">Better WP Security</a> homepage for support and change-log</p>
				</div>
			</div>
		</div>
		
	</div>
	
</div>
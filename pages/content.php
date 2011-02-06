<?php
	global $BWPS;
	
	if (isset($_POST['BWPS_content_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_content_save')) {
			die('Security error!');
		}
		
		$BWPS->renameContent($_POST['newdir']);
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p><em>content</em> directory successfully changed.</p></div>';
		}
	}
			
	if (!isset($_POST['BWPS_content_save'])) {
?>

	<div class="wrap" >

		<h2>Better WP Security - Content Directory</h2>
	
		<div id="poststuff" class="ui-sortable">	
			<div class="postbox-container" style="width:70%">	
				<div class="postbox opened">
					<h3>Change Content Directory</h3>	
					<div class="inside">
						<p>Select a new name for the content directory.</p>
						<form method="post">
							<?php wp_nonce_field('BWPS_content_save','wp_nonce') ?>
							<label for="newdir">Directory Name: </label> <input id="newdir" name="newdir" type="text" value="<?php echo $BWPS->getDir(); ?>">
							<p class="submit"><input type="submit" name="BWPS_content_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
						</form>
					</div>
				</div>
			</div>
			
			<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		</div>
	</div>
<?php } else { ?>
	<div class="wrap" >

		<h2>Better WP Security - Content Directory</h2>
	
		<div id="poststuff" class="ui-sortable">	
			<div class="postbox-container" style="width:100%">	
				<div class="postbox opened">
					<h3>Change Content Directory</h3>	
					<div class="inside">
						<p>The directory has been changed please reload this page if you wish to change it again.</p>
					</div>
				</div>
			</div>
		
		</div>
	</div>
<?php } ?>
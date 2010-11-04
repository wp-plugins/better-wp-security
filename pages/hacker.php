<?php
	global $wpdb, $BWPS_hacker;
	
	$opts = $BWPS_hacker->getOptions();
	
	if (isset($_POST['BWPS_hacker_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_hacker_save')) {
			die('Security error!');
		}	
		
		$opts = $BWPS_hacker->saveOptions("hacker_Version", BWPS_HACKER_VERSION);
		
		$opts = $BWPS_hacker->saveOptions("hacker_enable", $_POST['BWPS_hacker_enable']);
		$opts = $BWPS_hacker->saveOptions("hacker_emailnotify", $_POST['BWPS_hacker_emailnotify']);
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>Settings Saved</p></div>';
		}		
	}
?>

<div class="wrap" >

	<h2>Better WP Security - Detect Hackers Options</h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Detect Hackers Options</h3>	
				<div class="inside">
					<p>Use the options below to enable hacker detection.</p>
					<form method="post">
						<?php wp_nonce_field('BWPS_hacker_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_hacker_enable">Enable Hacking Detection</label>
									</th>
									<td>
										<label><input name="BWPS_hacker_enable" id="BWPS_hacker_enable" value="1" <?php if ($opts['hacker_enable'] == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
										<label><input name="BWPS_hacker_enable" value="0" <?php if ($opts['hacker_enable'] == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="BWPS_hacker_emailnotify">Enable Email Notifications.</label>
									</th>
									<td>
										<label><input name="BWPS_hacker_emailnotify" id="BWPS_hacker_emailnotify" value="1" <?php if ($opts['hacker_emailnotify'] == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
										<label><input name="BWPS_hacker_emailnotify" value="0" <?php if ($opts['hacker_emailnotify'] == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label><br />
										<p>
											Enabling this feature will trigger an email to be sent to the website administrator whenever a system is locked out due to too many suspicious attempts.
										</p>
									</td>
								</tr>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_hacker_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		
		<?php if ($opts['hacker_enable'] == 1) { ?>
			<div class="clear"></div>
		
			<div class="postbox-container" style="width:70%">
				<div class="postbox opened">
					<h3>Active Lockouts</h3>	
					<div class="inside">
						<p>Select a host or computer and click remove to release the lockout and allow them to log into the system.</p>
					</div>
				</div>
			</div>
		<?php } ?>
		
	</div>
</div>
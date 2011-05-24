<?php
	global $wpdb, $BWPS;
	
	$opts = $BWPS->getOptions();
	
	if (isset($_POST['BWPS_d404_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_d404_save')) {
			die('Security error!');
		}
		
		$opts = $BWPS->saveOptions("idetect_d404enable", $_POST['BWPS_idetect_d404enable']);
		$opts = $BWPS->saveOptions("idetect_emailnotify", $_POST['BWPS_idetect_emailnotify']);
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>' . __('Settings Saved', 'better-wp-security') . '</p></div>';
		}		
	}
	
	if (isset($_POST['BWPS_releasesave'])) {
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_releasesave')) { //verify nonce field
			die('Security error!');
		}
		
		
		while (list($key, $value) = each($_POST)) {
			if (strstr($key,"lo")) {
				$wpdb->query("DELETE FROM " . BWPS_TABLE_LOCKOUTS . " WHERE lockout_ID = " . $value . " AND mode = 1;");
			}
		}
	}
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('Intrusion Detection', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Detect 404s', 'better-wp-security'); ?></h3>	
				<div class="inside">
					<p><?php _e('Use the options below to enable 404 detection.', 'better-wp-security'); ?></p>
					<form method="post">
						<?php wp_nonce_field('BWPS_d404_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_idetect_d404enable"><?php _e('Enable 404 Detection', 'better-wp-security'); ?></label>
									</th>
									<td>
										<label><input name="BWPS_idetect_d404enable" id="BWPS_idetect_d404enable" value="1" <?php if ($opts['idetect_d404enable'] == 1) echo 'checked="checked"'; ?> type="radio" /> <?php _e('On', 'better-wp-security'); ?></label>
										<label><input name="BWPS_idetect_d404enable" value="0" <?php if ($opts['idetect_d404enable'] == 0) echo 'checked="checked"'; ?> type="radio" /> <?php _e('Off', 'better-wp-security'); ?></label>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_idetect_emailnotify"><?php _e('Enable Email Notifications.', 'better-wp-security'); ?></label>
									</th>
									<td>
										<label><input name="BWPS_idetect_emailnotify" id="BWPS_idetect_emailnotify" value="1" <?php if ($opts['idetect_emailnotify'] == 1) echo 'checked="checked"'; ?> type="radio" /> <?php _e('On', 'better-wp-security'); ?></label>
										<label><input name="BWPS_idetect_emailnotify" value="0" <?php if ($opts['idetect_emailnotify'] == 0) echo 'checked="checked"'; ?> type="radio" /> <?php _e('Off', 'better-wp-security'); ?></label><br />
										<p>
											<?php _e('Enabling this feature will trigger an email to be sent to the website administrator whenever a host or user is locked out of the system.', 'better-wp-security'); ?>
										</p>
									</td>
								</tr>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_d404_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		
		<?php if ($opts['idetect_d404enable'] == 1) { ?>
			<div class="clear"></div>
		
			<div class="postbox-container" style="width:70%">
				<div class="postbox opened">
					<h3><?php _e('Active Lockouts', 'better-wp-security'); ?></h3>	
					<div class="inside">
						<p><?php _e('Select a host and click remove to release the lockout and allow them to log into the system.', 'better-wp-security'); ?></p>
						<form method="post">
						<?php wp_nonce_field('BWPS_releasesave','wp_nonce') ?>

						<?php 
							$lockedList = $BWPS->d404_listLocked();
		
							if (sizeof($lockedList) > 0) {
								foreach ($lockedList as $item) {
									echo "<span style=\"border-bottom: 1px solid #ccc; padding: 2px; margin: 2px 10px 2px 10px; display: block;\"><input type=\"checkbox\" name=\"" . "lo" . $item['lockout_ID'] . "\" id=\"" . "lo" . $item['lockout_ID'] . "\" value=\"" . $item['lockout_ID'] . "\" /> <label for=\"" . "lo" . $item['lockout_ID'] . "\">" . $item['computer_id'] . " <span style=\"color: #ccc; font-style:italic;\">" . __('Expires in', 'better-wp-security') . ": " . $BWPS->dispRem(($item['lockout_date'] + 1800)) . "</span></label>\n";
													}
								echo "<p class=\"submit\"><input type=\"submit\" name=\"BWPS_releasesave\" value=\"" . __('Release Selected Lockouts', 'better-wp-security') . "\"></p>\n";
							} else {
								echo "<p style=\"text-align: center;\">" . __('There are no hosts currently locked out.', 'better-wp-security') . "</p>\n";
							}
						?>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
		
	</div>
</div>
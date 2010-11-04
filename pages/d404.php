<?php
	global $wpdb, $BWPS_d404;
	
	$opts = $BWPS_d404->getOptions();
	
	if (isset($_POST['BWPS_d404_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_d404_save')) {
			die('Security error!');
		}	
		
		$opts = $BWPS_d404->saveOptions("d404_Version", BWPS_D404_VERSION);
		
		$opts = $BWPS_d404->saveOptions("d404_enable", $_POST['BWPS_d404_enable']);
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>Settings Saved</p></div>';
		}		
	}
	
	if (isset($_POST['BWPS_releasesave'])) {
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_releasesave')) { //verify nonce field
			die('Security error!');
		}
		
		
		while (list($key, $value) = each($_POST)) {
			if (strstr($key,"lo")) {
				$wpdb->query("DELETE FROM " . $opts['d404_table_lockouts'] . " WHERE lockout_ID = " . $value . ";");
			}
		}
	}
?>

<div class="wrap" >

	<h2>Better WP Security - Detect 404s Options</h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Detect 404s Options</h3>	
				<div class="inside">
					<p>Use the options below to enable 404 detection.</p>
					<form method="post">
						<?php wp_nonce_field('BWPS_d404_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_d404_enable">Enable 404 Detection</label>
									</th>
									<td>
										<label><input name="BWPS_d404_enable" id="BWPS_d404_enable" value="1" <?php if ($opts['d404_enable'] == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
										<label><input name="BWPS_d404_enable" value="0" <?php if ($opts['d404_enable'] == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
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
		
		
		<?php if ($opts['d404_enable'] == 1) { ?>
			<div class="clear"></div>
		
			<div class="postbox-container" style="width:70%">
				<div class="postbox opened">
					<h3>Active Lockouts</h3>	
					<div class="inside">
						<p>Select a host and click remove to release the lockout and allow them to log into the system.</p>
						<form method="post">
						<?php wp_nonce_field('BWPS_releasesave','wp_nonce') ?>

						<?php 
							$lockedList = $BWPS_d404->listLocked();
		
							if (sizeof($lockedList) > 0) {
								foreach ($lockedList as $item) {
									echo "<span style=\"border-bottom: 1px solid #ccc; padding: 2px; margin: 2px 10px 2px 10px; display: block;\"><input type=\"checkbox\" name=\"" . "lo" . $item['lockout_ID'] . "\" id=\"" . "lo" . $item['lockout_ID'] . "\" value=\"" . $item['lockout_ID'] . "\" /> <label for=\"" . "lo" . $item['lockout_ID'] . "\">" . $item['computer_id'] . " <span style=\"color: #ccc; font-style:italic;\">Expires in: " . $BWPS_d404->dispRem(($item['lockout_date'] + 1800)) . "</span></label>\n";
													}
								echo "<p class=\"submit\"><input type=\"submit\" name=\"BWPS_releasesave\" value=\"Release Selected Lockouts\"></p>\n";
							} else {
								echo "<p style=\"text-align: center;\">There are no hosts currently locked out.</p>\n";
							}
						?>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
		
	</div>
</div>
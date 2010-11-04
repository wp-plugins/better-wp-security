<?php
	global $wpdb, $BWPS_limitlogin;
	
	$opts = $BWPS_limitlogin->getOptions();
	
	if (isset($_POST['BWPS_limitlogin_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_limitlogin_save')) {
			die('Security error!');
		}	
		
		$opts = $BWPS_limitlogin->saveOptions("limitlogin_Version", BWPS_LIMITLOGIN_VERSION);
		
		$mahinput = (string)absint(intval($_POST['BWPS_limitlogin_maxattemptshost']));
		$mauinput = (string)absint(intval($_POST['BWPS_limitlogin_maxattemptsuser']));
		$ciinput = (string)absint(intval($_POST['BWPS_limitlogin_checkinterval']));
		$bainput = (string)absint(intval($_POST['BWPS_limitlogin_banperiod']));
		
		if (strcmp($_POST['BWPS_limitlogin_maxattemptshost'], $mahinput) || strcmp($_POST['BWPS_limitlogin_maxattemptsuser'], $mauinput) || strcmp($_POST['BWPS_limitlogin_checkinterval'], $ciinput) || strcmp($_POST['BWPS_limitlogin_banperiod'], $bainput)) {
			
			$errorHandler = new WP_Error();
			
			if ($_POST['BWPS_limitlogin_maxattemptshost'] != $mahinput) {
				$errorHandler->add("3", __("<strong>Max Login Attempts Per Host</strong> MUST be a positive integer."));
			}
		
			if ($_POST['BWPS_limitlogin_maxattemptsuser'] != $mauinput) {
				$errorHandler->add("3", __("<strong>Max Login Attempts Per User</strong> MUST be a positive integer."));
			}
		
			if ($_POST['BWPS_limitlogin_checkinterval'] != $ciinput) {
				$errorHandler->add("3", __("<strong>Login Time Period (minutes)</strong> MUST be a positive integer."));
			}
		
			if ($_POST['BWPS_limitlogin_banperiod'] != $bainput) {
				$errorHandler->add("3", __("<strong>Lockout Time Period (minutes)</strong> MUST be a positive integer."));
			}
			
		} else {
			$opts = $BWPS_limitlogin->saveOptions("limitlogin_enable", $_POST['BWPS_limitlogin_enable']);
			$opts = $BWPS_limitlogin->saveOptions("limitlogin_maxattemptshost", $mahinput);
			$opts = $BWPS_limitlogin->saveOptions("limitlogin_maxattemptsuser", $mauinput);
			$opts = $BWPS_limitlogin->saveOptions("limitlogin_checkinterval", $ciinput);
			$opts = $BWPS_limitlogin->saveOptions("limitlogin_banperiod", $bainput);
			$opts = $BWPS_limitlogin->saveOptions("limitlogin_denyaccess", $_POST['BWPS_limitlogin_denyaccess']);
			$opts = $BWPS_limitlogin->saveOptions("limitlogin_emailnotify", $_POST['BWPS_limitlogin_emailnotify']);
		}
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>Settings Saved</p></div>';
		}
		
		$ledisplay = $_POST['BWPS_limitlogin_enable'];
		$mahdisplay = $_POST['BWPS_limitlogin_maxattemptshost'];
		$maudisplay = $_POST['BWPS_limitlogin_maxattemptsuser'];
		$cidsplay = $_POST['BWPS_limitlogin_checkinterval'];
		$bpdisplay = $_POST['BWPS_limitlogin_banperiod'];
		$dadisplay = $_POST['BWPS_limitlogin_denyaccess'];
		$endisplay = $_POST['BWPS_limitlogin_emailnotify'];
		
	} else {
		
		$ledisplay = $opts['limitlogin_enable'];
		$mahdisplay = $opts['limitlogin_maxattemptshost'];
		$maudisplay = $opts['limitlogin_maxattemptsuser'];
		$cidsplay = $opts['limitlogin_checkinterval'];
		$bpdisplay = $opts['limitlogin_banperiod'];
		$dadisplay = $opts['limitlogin_denyaccess'];
		$endisplay = $opts['limitlogin_emailnotify'];
		
	}
	
	if (isset($_POST['BWPS_releasesave'])) {
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_releasesave')) { //verify nonce field
			die('Security error!');
		}
		
		
		while (list($key, $value) = each($_POST)) {
			if (strstr($key,"lo")) {
				$wpdb->query("DELETE FROM " . $opts['limitlogin_table_lockouts'] . " WHERE lockout_ID = " . $value . ";");
			}
		}
	}
	
?>

<div class="wrap" >

	<h2>Better WP Security - Limit Logins Options</h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Limit Logins Options</h3>	
				<div class="inside">
					<p>Set options below to limit the number of bad login attempts. Once this limit is reached, the host or computer attempting to login will be banned from the site for the specified "lockout length" period.</p>
					<form method="post">
						<?php wp_nonce_field('BWPS_limitlogin_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_limitlogin_enable">Enable Limit Bad Login Attempts</label>
									</th>
									<td>
										<label><input name="BWPS_limitlogin_enable" id="BWPS_limitlogin_enable" value="1" <?php if ($ledisplay == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
										<label><input name="BWPS_limitlogin_enable" value="0" <?php if ($ledisplay == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="BWPS_limitlogin_maxattemptshost">Max Login Attempts Per Host</label>
									</th>
									<td>
										<input name="BWPS_limitlogin_maxattemptshost" id="BWPS_limitlogin_maxattemptshost" value="<?php echo $mahdisplay; ?>" type="text">
										<p>
											The number of login attempts a user has before their host or computer is locked out of the system.
										</p>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_limitlogin_maxattemptsuser">Max Login Attempts Per User</label>
									</th>
									<td>
										<input name="BWPS_limitlogin_maxattemptsuser" id="BWPS_limitlogin_maxattemptsuser" value="<?php echo $maudisplay; ?>" type="text">
										<p>
											The number of login attempts a user has before their username is locked out of the system.
										</p>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_limitlogin_checkinterval">Login Time Period (minutes)</label>
									</th>
									<td>
										<input name="BWPS_limitlogin_checkinterval" id="BWPS_limitlogin_checkinterval" value="<?php echo $cidsplay; ?>" type="text"><br />
										<p>
											The number of minutes in which bad logins should be remembered.
										</p>
									</td>
								</tr>
		
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_limitlogin_banperiod">Lockout Time Period (minutes)</label>
									</th>
									<td>
										<input name="BWPS_limitlogin_banperiod" id="BWPS_limitlogin_banperiod" value="<?php echo $bpdisplay; ?>" type="text"><br />
										<p>
											The length of time a host or computer will be banned from this site after hitting the limit of bad logins.
										</p>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_limitlogin_denyaccess">Deny All Site Access To Locked Out Hosts.</label>
									</th>
									<td>
										<label><input name="BWPS_limitlogin_denyaccess" id="BWPS_limitlogin_denyaccess" value="1" <?php if ($dadisplay == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
										<label><input name="BWPS_limitlogin_denyaccess" value="0" <?php if ($dadisplay == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label><br />
										<p>
											If the host is locked out it will be completely banned from the site and unable to access either content or the backend for the duration of the logout.
										</p>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_limitlogin_emailnotify">Enable Email Notifications.</label>
									</th>
									<td>
										<label><input name="BWPS_limitlogin_emailnotify" id="BWPS_limitlogin_emailnotify" value="1" <?php if ($endisplay == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
										<label><input name="BWPS_limitlogin_emailnotify" value="0" <?php if ($endisplay == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label><br />
										<p>
											Enabling this feature will trigger an email to be sent to the website administrator whenever a host or user is locked out of the system.
										</p>
									</td>
								</tr>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_limitlogin_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		
		<?php if ($opts['limitlogin_enable'] == 1) { ?>
			<div class="clear"></div>
		
			<div class="postbox-container" style="width:70%">
				<div class="postbox opened">
					<h3>Active Lockouts</h3>	
					<div class="inside">
						<p>Select a host or computer and click remove to release the lockout and allow them to log into the system.</p>
						<table width="100%" border="1">
							<tbody>
								<thead>
									<tr valign="top">
										<th>Locked Out Hosts</th>
										<th>Locked Out Users</th>	
									</tr>
								</thead>
								<tr valign="top">
									<form method="post">
										<?php wp_nonce_field('BWPS_releasesave','wp_nonce') ?>
										<td width="50%">
											<?php 
												$lockedList = $BWPS_limitlogin->listLocked();
												
												if (sizeof($lockedList) > 0) {
													foreach ($lockedList as $item) {
														echo "<span style=\"border-bottom: 1px solid #ccc; padding: 2px; margin: 2px 10px 2px 10px; display: block;\"><input type=\"checkbox\" name=\"" . "lo" . $item['lockout_ID'] . "\" id=\"" . "lo" . $item['lockout_ID'] . "\" value=\"" . $item['lockout_ID'] . "\" /> <label for=\"" . "lo" . $item['lockout_ID'] . "\">" . $item['loLabel'] . " <span style=\"color: #ccc; font-style:italic;\">Expires in: " . $BWPS_limitlogin->dispRem(($item['lockout_date'] + ($opts['limitlogin_banperiod'] * 60))) . "</span></label>\n";
													}
													echo "<p class=\"submit\"><input type=\"submit\" name=\"BWPS_releasesave\" value=\"Release Selected Lockouts\"></p>\n";
												} else {
													echo "<p style=\"text-align: center;\">There are no hosts currently locked out.</p>\n";
												}
											?>
										</td>
										<td width="50%">
											<?php 
												$lockedList = $BWPS_limitlogin->listLocked("users");
												
												if (sizeof($lockedList) > 0) {
													foreach ($lockedList as $item) {
														echo "<span style=\"border-bottom: 1px solid #ccc; padding: 2px; margin: 2px 10px 2px 10px; display: block;\"><input type=\"checkbox\" name=\"" . "lo" . $item['lockout_ID'] . "\" id=\"" . "lo" . $item['lockout_ID'] . "\" value=\"" . $item['lockout_ID'] . "\" /> <label for=\"" . "lo" . $item['lockout_ID'] . "\">" . $item['loLabel'] . " <span style=\"color: #ccc; font-style:italic;\">Expires in: " . $BWPS_limitlogin->dispRem(($item['lockout_date'] + ($opts['limitlogin_banperiod'] * 60))) . "</span></label>\n";
													}
													echo "<p class=\"submit\"><input type=\"submit\" name=\"BWPS_releasesave\" value=\"Release Selected Lockouts\"></p>\n";
												} else {
													echo "<p style=\"text-align: center;\">There are no users currently locked out.</p>\n";
												}
											?>
										</td>
									</form>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>
		
	</div>
</div>
<?php
	global $wpdb, $BWPS;
	
	$opts = $BWPS->getOptions();
	
	if (isset($_POST['BWPS_d404_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_d404_save')) {
			die('Security error!');
		}
		
		$whiteList = explode("\n", $_POST['BWPS_idetect_whitelist']);
		$whiteitems = array();
	
		if(!empty($whiteList)) {
			foreach($whiteList as $item) {
				if (strlen($item) > 0) {
					if (strstr($item,' - ')) {
						$range = explode('-', $item);
						$start = trim($range[0]);
						$end = trim($range[1]);
						if (ip2long($end) == false) {
							if (!$errorHandler) {
								$errorHandler = new WP_Error();
							}
							$errorHandler->add("1", __($item . " contains an invalid ip (" . $end . ").", 'better-wp-security'));
						}
						if (ip2long($start) == false ) {
							if (!$errorHandler) {
								$errorHandler = new WP_Error();
							}
							$errorHandler->add("1", __($item . " contains an invalid ip (" . $start . ").", 'better-wp-security'));
						}	else {
							$whiteitems[] = trim($item);
						}	
						
					} else {
						$ipParts = explode('.',$item);
						$isIP = 0;
						foreach ($ipParts as $part) {
							if (is_numeric(trim($part)) || trim($part) == '*') {
								$isIP++;
							}
						}
						if($isIP == 4) {
							if (ip2long(trim(str_replace('*', '0', $item))) == false) {
								if (!$errorHandler) {
									$errorHandler = new WP_Error();
								}
								$errorHandler->add("1", __($item . " is not a valid ip.", 'better-wp-security'));
							} else {
								$whiteitems[] = trim($item);
							}
						} else {
							$parts = explode(".",$item);
							$goodHost = true;
    						foreach($parts as $part) {
								if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', trim($part)) || preg_match('/-$/', trim($part)) ) {
           							$goodHost = false;
        						} 
        					}
        					if ($goodHost == true) {
        						$whiteitems[] = trim($item);
        					} else {
        						if (!$errorHandler) {
										$errorHandler = new WP_Error();
									}
									$errorHandler->add("1", __($item . " is note a valid hostname.", 'better-wp-security'));
        					}
						}
					}
				}
			}
		}
				
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			$opts = $BWPS->saveOptions("idetect_d404enable", $_POST['BWPS_idetect_d404enable']);
			$opts = $BWPS->saveOptions("idetect_emailnotify", $_POST['BWPS_idetect_emailnotify']);
			$opts = $BWPS->saveOptions("idetect_checkint", ($_POST['BWPS_idetect_checkint'] * 60));
			$opts = $BWPS->saveOptions("idetect_locount", $_POST['BWPS_idetect_locount']);
			$opts = $BWPS->saveOptions("idetect_lolength", ($_POST['BWPS_idetect_lolength'] * 60));
			$opts = $BWPS->saveOptions("idetect_error_message", $_POST['BWPS_idetect_error_message']);
			$opts = $BWPS->saveOptions("idetect_whitelist",implode("\n",$whiteitems));	
	
			echo '<div id="message" class="updated"><p>' . __('Settings Saved', 'better-wp-security') . '</p></div>';
		}		
		
		$idetect_whitelist = $_POST['BWPS_idetect_whitelist'];
		
	} else {
		$idetect_whitelist = $opts['idetect_whitelist'];
	}
	
	if (isset($_POST['BWPS_releasesave'])) {
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_releasesave')) { //verify nonce field
			die('Security error!');
		}
		
		
		while (list($key, $value) = each($_POST)) {
			if (strstr($key,"lo")) {
				$reHost = $wpdb->get_var("SELECT computer_id FROM " . BWPS_TABLE_LOCKOUTS  . " WHERE lockout_ID = " . $value . " AND mode = 1;");
				$wpdb->query("DELETE FROM " . BWPS_TABLE_LOCKOUTS . " WHERE lockout_ID = " . $value . " AND mode = 1;");
				$wpdb->query("DELETE FROM " . BWPS_TABLE_D404 . " WHERE computer_id = '" . $reHost . "'  AND attempt_date > " . (time() - $opts['idetect_checkint']) . ";");
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
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_idetect_checkint"><?php _e('How many minutes should Better WP Security remember a bad page?', 'better-wp-security'); ?></label>
									</th>
									<td>
										<input name="BWPS_idetect_checkint" id="BWPS_idetect_checkint" value="<?php echo ($opts['idetect_checkint'] / 60); ?>" type="text">
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_idetect_locount"><?php _e('How many 404 errors (bad pages) should trigger a lockout?', 'better-wp-security'); ?></label>
									</th>
									<td>
										<input name="BWPS_idetect_locount" id="BWPS_idetect_locount" value="<?php echo $opts['idetect_locount']; ?>" type="text">
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_idetect_lolength"><?php _e('For how many minutes should an offending host be locked out?', 'better-wp-security'); ?></label>
									</th>
									<td>
										<input name="BWPS_idetect_lolength" id="BWPS_idetect_lolength" value="<?php echo ($opts['idetect_lolength'] / 60); ?>" type="text">
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_idetect_error_message"><?php _e('Default Error Message', 'better-wp-security'); ?></label>
									</th>
									<td>
										<input name="BWPS_idetect_error_message" id="error_message" value="<?php echo $opts['idetect_error_message']; ?>" type="text"><br />
										<em><?php _e('The message that will display when someone has been locked out.'); ?></em>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_idetect_whitelist"><?php _e('White List', 'better-wp-security'); ?></label>
									</th>
									<td>
										<textarea rows="10" cols="50" name="BWPS_idetect_whitelist" id="BWPS_idetect_whitelist"><?php echo $idetect_whitelist; ?></textarea><br />
										<ul><em>
											<li><?php _e('You may whitelist users by individual IP address, IP address range, or hostname.', 'better-wp-security'); ?></li>
											<li><?php _e('Individual IP addesses must be in IPV4 standard format (i.e. ###.###.###.###). Wildcards (*) are allowed to specify a range of ip addresses.', 'better-wp-security'); ?></li>
											<li><?php _e('IP Address ranges may also be specified using the format ###.###.###.### - ###.###.###.###. Wildcards cannot be used in addresses specified like this.', 'better-wp-security'); ?></li>
											<li><?php _e('Hostnames may be specified individually do NOT include slashes (/), http://, or any other extra information.', 'better-wp-security'); ?></li>
											<li><a href="http://ip-lookup.net/domain-lookup.php" target="_blank"><?php _e('Lookup IP Address.', 'better-wp-security'); ?></a></li>
											<li><?php _e('Enter only 1 IP address per line.', 'better-wp-security'); ?></li>
											<li><?php _e('404 errors will still be logged for users on the whitelist. Only the lockout will be prevented', 'better-wp-security'); ?></li>
										</em></ul>
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
									echo "<span style=\"border-bottom: 1px solid #ccc; padding: 2px; margin: 2px 10px 2px 10px; display: block; float: left\"><input type=\"checkbox\" name=\"" . "lo" . $item['lockout_ID'] . "\" id=\"" . "lo" . $item['lockout_ID'] . "\" value=\"" . $item['lockout_ID'] . "\" /> <label for=\"" . "lo" . $item['lockout_ID'] . "\"><a href=\"http://whois.domaintools.com/" . $item['computer_id'] . "\" target=\"_blank\">" . $item['computer_id'] . "</a> <span style=\"color: #ccc; font-style:italic;\">" . __('Expires in', 'better-wp-security') . ": " . $BWPS->dispRem(($item['lockout_date'] + $opts['idetect_lolength'])) . "</span></label></span>\n";
								}
								echo "<div style=\"clear: both;\"></div>\n";
								echo "<p class=\"submit\"><input type=\"submit\" name=\"BWPS_releasesave\" value=\"" . __('Release Selected Lockouts', 'better-wp-security') . "\"></p>\n";
							} else {
								echo "<p style=\"text-align: center;\">" . __('There are no hosts currently locked out.', 'better-wp-security') . "</p>\n";
							}
						?>
						</form>
					</div>
				</div>
			</div>
	
			<div class="clear"></div>
		
			<div class="postbox-container" style="width:100%">
				<div class="postbox opened">
					<h3><?php _e('Current 404 Errors', 'better-wp-security'); ?></h3>	
					<div class="inside">
						<p><?php _e('Here is a list of the 404 errors this plugin has encountered. You may want to try to fix them as if this plugin is seeing them, so are the search engines.', 'better-wp-security'); ?></p>
						<?php 
							$d404list = $BWPS->d404_list404s();
		
							if (sizeof($d404list) > 0) {
								echo "<table width=\"100%\">\n" . 
									"<tr>\n" .
									"<th>Page</th>\n" .
									"<th>Referrer</th>\n" .
									"<th>Host</th>\n" .
									"</tr>\n";
								foreach ($d404list as $item) {
									echo "<tr>\n" . 
										"<td>" . $item['qstring'] . "</td>\n" . 
										"<td>" . $item['referrer'] . "</td>\n" .
										"<td>" . $item['computer_id'] . "</td>\n" .
										"</tr>\n";
								}
								echo "</table>\n" . 
									"<div style=\"clear:both;\"></div>\n";
							} else {
								echo "<p style=\"text-align: center;\">" . __('There are no 404 errors in the database.', 'better-wp-security') . "</p>\n";
							}
						?>
					</div>
				</div>
			</div>
		<?php } ?>

		
	</div>
</div>
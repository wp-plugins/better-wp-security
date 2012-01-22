<?php
	global $BWPS;
	
	$opts = $BWPS->getOptions();
	
	$userIP = $BWPS->computer_id;
	
	if (isset($_POST['BWPS_banvisits_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_banvisits_save')) {
			die('Security error!');
		}
		
		$visitorList = explode("\n", $_POST['BWPS_banvisits_banlist']);
		$banitems = array();
	
		if(!empty($visitorList)) {
			foreach($visitorList as $item) {
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
						}		
						if($BWPS->banvisits_ipinrange($userIP, $start, $end)) {
							if (!$errorHandler) {
								$errorHandler = new WP_Error();
							}
							$errorHandler->add("2", __("You current ip is in the range " . $range . " and cannot be banned.", 'better-wp-security'));
						} else {
							$banitems[] = trim($item);
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
							} elseif($ip == $userIP) {
								if (!$errorHandler) {
									$errorHandler = new WP_Error();
								}
								$errorHandler->add("1", __($item . " is your current address and cannot be banned.", 'better-wp-security'));
							} else {
								$banitems[] = trim($item);
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
        						$banitems[] = trim($item);
        					} else {
        						if (!$errorHandler) {
										$errorHandler = new WP_Error();
									}
									$errorHandler->add("1", __($item . " is your current address and cannot be banned.", 'better-wp-security'));
        					}
						}
					}
				}
			}
		}	
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
			$opts = $BWPS->saveOptions("banvisits_enable","0");
		} else {
			echo '<div id="message" class="updated"><p>' . __('Settings Saved', 'better-wp-security') . '</p></div>';
			$opts = $BWPS->saveOptions("banvisits_banlist",implode("\n",$banitems));		
			if (strlen(implode("\n",$banitems)) > 0) {
				$opts = $BWPS->saveOptions("banvisits_enable",$_POST['BWPS_banvisits_enable']);
			} else {
				$opts = $BWPS->saveOptions("banvisits_enable",'0');
			}
		}
		
		$banvisits_banlist = $_POST['BWPS_banvisits_banlist'];
		
	} else {
		
		$banvisits_banlist = $opts['banvisits_banlist'];
		
	}
		
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('Ban Users Options', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Ban User Options', 'better-wp-security'); ?></h3>	
				<div class="inside">
					<p><?php _e('List below the addresses of users you would like to ban from your site.', 'better-wp-security'); ?></p>
					<form method="post">
						<?php wp_nonce_field('BWPS_banvisits_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_banvisits_enable"><?php _e('Enable Ban Users', 'better-wp-security'); ?></label>
									</th>
									<td>
										<label><input name="BWPS_banvisits_enable" id="BWPS_banvisits_enable" value="1" <?php if ($opts['banvisits_enable'] == 1) echo 'checked="checked"'; ?> type="radio" /> <?php _e('On', 'better-wp-security'); ?></label>
										<label><input name="BWPS_banvisits_enable" value="0" <?php if ($opts['banvisits_enable'] == 0) echo 'checked="checked"'; ?> type="radio" /> <?php _e('Off', 'better-wp-security'); ?></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_banvisits_banlist"><?php _e('User List', 'better-wp-security'); ?></label>
									</th>
									<td>
										<textarea rows="10" cols="50" name="BWPS_banvisits_banlist" id="BWPS_banvisits_banlist"><?php echo $banvisits_banlist; ?></textarea><br />
										<ul><em>
											<li><?php _e('You may ban users by individual IP address, IP address range, or hostname.', 'better-wp-security'); ?></li>
											<li><?php _e('Individual IP addesses must be in IPV4 standard format (i.e. ###.###.###.###). Wildcards (*) are allowed to specify a range of ip addresses.', 'better-wp-security'); ?></li>
											<li><?php _e('IP Address ranges may also be specified using the format ###.###.###.### - ###.###.###.###. Wildcards cannot be used in addresses specified like this.', 'better-wp-security'); ?></li>
											<li><?php _e('Hostnames may be specified individually do NOT include slashes (/), http://, or any other extra information.', 'better-wp-security'); ?></li>
											<li><a href="http://ip-lookup.net/domain-lookup.php" target="_blank"><?php _e('Lookup IP Address.', 'better-wp-security'); ?></a></li>
											<li><?php _e('Enter only 1 IP address per line.', 'better-wp-security'); ?></li>
											<li><?php _e('You may NOT ban your own IP address', 'better-wp-security'); ?></li>
										</em></ul>
									</td>
								</tr>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_banvisits_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>		
	</div>
</div>
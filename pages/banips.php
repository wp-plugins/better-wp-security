<?php
	global $BWPS;
	
	$opts = $BWPS->getOptions();
	
	if (isset($_POST['BWPS_banips_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_banips_save')) {
			die('Security error!');
		}
		
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
		if (!$BWPS->can_write($htaccess)) {

			$opts = $BWPS->saveOptions("banips_enable","0");
			
			if (!$errorHandler) {
				$errorHandler = new WP_Error();
			}
			
			$errorHandler->add("2", __("Unable to update htaccess rules"));
			
		} else {
				
			if (strlen($_POST['BWPS_banips_iplist']) > 0) {
		
				$ipInput = esc_html__($_POST['BWPS_banips_iplist']);
		
				$ipArray = explode("\n", $ipInput);	
				
				$goodAddress = true;
				
				//get current ip address
				$myIp = getenv("REMOTE_ADDR");
				
				for ($i = 0; $i < sizeof($ipArray) && $goodAddress == true; $i++) {
					$ipArray[$i] = trim($ipArray[$i]);
					if (strlen($ipArray[$i]) > 0 && (!$BWPS->banips_checkIps($ipArray[$i]) || $ipArray[$i] == $myIp)) {
						$goodAddress = false; //we have a bad ip
					}
				}
				
				if ($goodAddress == true) {
					$opts = $BWPS->saveOptions("banips_enable",$_POST['BWPS_banips_enable']);
					$opts = $BWPS->saveOptions("banips_iplist",implode(' ',$ipArray));
				} else {
					if (!$errorHandler) {
						$errorHandler = new WP_Error();
					}
			
					$errorHandler->add("2", __("You have entered an invalid IP address,", 'better-wp-security'));
				}
			
			} else {
				$opts = $BWPS->saveOptions("banips_enable","0");
				$opts = $BWPS->saveOptions("banips_iplist","");
			}
		
			if ($_POST['BWPS_banips_enable'] == 1 && $opts['banips_iplist'] == "") {
				$opts = $BWPS->saveOptions("banips_enable","0");
			} 
			
			if (!$errorHandler) {
				$BWPS->createhtaccess();		
			}
		} 
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			$BWPS->saveVersions('BANIPS', BWPS_VERSION_BANIPS);
			echo '<div id="message" class="updated"><p>' . __('Settings Saved', 'better-wp-security') . '</p></div>';
		}
		
		$banips_iplist = $_POST['BWPS_banips_iplist'];
		
	} else {
	
		$banips_iplist = $opts['banips_iplist'];
		
	}
		
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('Ban IPs Options', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Ban IPs Options', 'better-wp-security'); ?></h3>	
				<div class="inside">
					<p><?php _e('List below the IP addresses you would like to ban from your site. These will be banned in .htaccess.', 'better-wp-security'); ?></p>
					<form method="post">
						<?php wp_nonce_field('BWPS_banips_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_banips_enable"><?php _e('Enable Ban IPs', 'better-wp-security'); ?></label>
									</th>
									<td>
										<label><input name="BWPS_banips_enable" id="BWPS_banips_enable" value="1" <?php if ($opts['banips_enable'] == 1) echo 'checked="checked"'; ?> type="radio" /> <?php _e('On', 'better-wp-security'); ?></label>
										<label><input name="BWPS_banips_enable" value="0" <?php if ($opts['banips_enable'] == 0) echo 'checked="checked"'; ?> type="radio" /> <?php _e('Off', 'better-wp-security'); ?></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_banips_iplist"><?php _e('IP List', 'better-wp-security'); ?></label>
									</th>
									<td>
										<textarea rows="10" cols="50" name="BWPS_banips_iplist" id="BWPS_banips_iplist"><?php echo $banips_iplist; ?></textarea><br />
										<p><em>
											<?php _e('IP addesses must be in IPV4 standard format (i.e. ###.###.###.###).', 'better-wp-security'); ?><br />
											<a href="http://ip-lookup.net/domain-lookup.php" target="_blank"><?php _e('Lookup IP Address.', 'better-wp-security'); ?></a><br />
											<?php _e('Enter only 1 IP address per line.', 'better-wp-security'); ?><br />
											<?php _e('You may NOT ban your own IP address', 'better-wp-security'); ?>
										</em></p>
									</td>
								</tr>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_banips_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		<?php if ($opts['banips_enable'] == 1) { ?>
			<div class="clear"></div>
		
			<div class="postbox-container" style="width:70%">
				<div class="postbox opened">
					<h3><?php _e('Current .htaccess', 'better-wp-security'); ?></h3>	
					<div class="inside">
						<p><?php _e('Here are the current contents of your .htaccess file.', 'better-wp-security'); ?></p>
						<?php $BWPS->htaccess_showContents(); ?>
					</div>
				</div>
			</div>
		<?php } ?>
		
	</div>
</div>
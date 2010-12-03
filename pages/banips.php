<?php
	global $BWPS;
	
	$opts = $BWPS->getOptions();
	
	if (isset($_POST['BWPS_banips_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_banips_save')) {
			die('Security error!');
		}
		
		$opts = $BWPS->saveOptions("banips_enable",$_POST['BWPS_banips_enable']);
				
		if (strlen($_POST['BWPS_banips_iplist']) > 0) {
		
			$ipInput = esc_html__($_POST['BWPS_banips_iplist']);
		
			$ipArray = explode("\n", $ipInput);	
			
			if (!$BWPS->banips_createRules($ipArray)) {
				if (!$errorHandler) {
					$errorHandler = new WP_Error();
				}
				$errorHandler->add("1", __("You entered a bad IP address"));
			}  else {
				$opts = $BWPS->saveOptions("banips_iplist",$ipInput);
			}
		} else {
			$opts = $BWPS->saveOptions("banips_enable","0");
			$opts = $BWPS->saveOptions("banips_iplist","");
		}
		
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
		if (!$BWPS->can_write($htaccess)) {

			$opts = $BWPS->saveOptions("banips_enable","0");
			
			if (!$errorHandler) {
				$errorHandler = new WP_Error();
			}
			
			$errorHandler->add("2", __("Unable to update htaccess rules"));
			
		} else {
		
			if ($_POST['BWPS_banips_enable'] == 1 && $opts['banips_iplist'] != "") {
				$BWPS->remove_section($htaccess, 'Better WP Security Ban IPs');
				insert_with_markers($htaccess,'Better WP Security Ban IPs', explode( "\n", $BWPS->banips_getList()));

			} else {
			
				$opts = $BWPS->saveOptions("banips_enable","0");
				$BWPS->remove_section($htaccess, 'Better WP Security Ban IPs');
				
			}		
			
		} 
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			$BWPS->saveVersions('BANIPS', BWPS_VERSION_BANIPS);
			echo '<div id="message" class="updated"><p>Settings Saved</p></div>';
		}
		
		$banips_iplist = $_POST['BWPS_banips_iplist'];
		
	} else {
	
		$banips_iplist = $opts['banips_iplist'];
		
	}
		
?>

<div class="wrap" >

	<h2>Better WP Security - Ban IPs Options</h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Ban IPs Options</h3>	
				<div class="inside">
					<p>List below the IP addresses you would like to ban from your site. These will be banned in .htaccess.</p>
					<form method="post">
						<?php wp_nonce_field('BWPS_banips_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_banips_enable">Enable Ban IPs</label>
									</th>
									<td>
										<label><input name="BWPS_banips_enable" id="BWPS_banips_enable" value="1" <?php if ($opts['banips_enable'] == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
										<label><input name="BWPS_banips_enable" value="0" <?php if ($opts['banips_enable'] == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_banips_iplist">IP List</label>
									</th>
									<td>
										<textarea rows="10" cols="50" name="BWPS_banips_iplist" id="BWPS_banips_iplist"><?php echo $banips_iplist; ?></textarea><br />
										<p><em>
											IP addesses must be in IPV4 standard format (i.e. ###.###.###.###).<br />
											<a href="http://ip-lookup.net/domain-lookup.php" target="_blank">Lookup IP Address.</a><br />
											Enter only 1 IP address per line.<br />
											You may NOT ban your own IP address
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
		
			<?php
				$bgColor = $BWPS->banips_confirmRules();
			?>
			<div class="postbox-container" style="width:70%">
				<div class="postbox opened" style="background-color: <?php echo $bgColor; ?>;">
					<h3>Hide Backend Rewrite Rules</h3>	
					<div class="inside">
						<?php
							if ($bgColor == "#ffebeb") {
								echo "<h4 style=\"text-align: center;\">Your htaccess rules have a problem. Please save this form to fix them</h4>";
							}
						?>
						<pre><?php echo $BWPS->banips_getList(); ?></pre>
					</div>
				</div>
			</div>
		<?php } ?>
		
	</div>
</div>
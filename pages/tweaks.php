<?php
	global $opts, $BWPS_tweaks;
	
	$opts = $BWPS_tweaks->getOptions();
	
	if (isset($_POST['BWPS_tweaks_save'])) { // Save options
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_tweaks_save')) { //verify nonce field
			die('Security error!');
		}	
		
		$opts = $BWPS_tweaks->saveOptions("tweaks_Version", BWPS_tweaks_VERSION);
		
		$opts = $BWPS_tweaks->saveOptions("tweaks_removeGenerator",$_POST['BWPS_removeGenerator']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_removeLoginMessages",$_POST['BWPS_removeLoginMessages']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_randomVersion",$_POST['BWPS_randomVersion']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_themeUpdates",$_POST['BWPS_themeUpdates']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_pluginUpdates",$_POST['BWPS_pluginUpdates']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_coreUpdates",$_POST['BWPS_coreUpdates']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_removewlm",$_POST['BWPS_removewlm']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_removersd",$_POST['BWPS_removersd']);
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>Settings Saved</p></div>';
		}
		
	}
	
?>

<div class="wrap" >

	<h2>Better WP Security - Security Tweaks</h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>tweaks Options</h3>	
				<div class="inside">
					<p></p>
					<form method="post">
						<?php wp_nonce_field('BWPS_tweaks_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<h4>Header Tweaks</h4>
								<p>
									<input type="checkbox" name="BWPS_removeGenerator" id="BWPS_removeGenerator" value="1" <?php if ($opts['tweaks_removeGenerator'] == 1) echo "checked"; ?> /> <label for="BWPS_removeGenerator"><strong>Remove Wordpress Generator Meta Tag</strong></label><br />
									Removes the <em>&lt;meta name="generator" content="WordPress [version]" /&gt;</em> meta tag from your sites header. This process hides version information from a potential attacker making it more difficult to determine vulnerabilities.
								</p>
								<p>
									<input type="checkbox" name="BWPS_removewlm" id="BWPS_removewlm" value="1" <?php if ($opts['tweaks_removewlm'] == 1) echo "checked"; ?> /> <label for="BWPS_removewlm"><strong>Remove <em>wlwmanifest</em> header</strong></label><br />
									Removes the Windows Live Writer header. This is not needed if you do not use Windows Live Writer.
								</p>
								<p>
									<input type="checkbox" name="BWPS_removersd" id="BWPS_removersd" value="1" <?php if ($opts['tweaks_removersd'] == 1) echo "checked"; ?> /> <label for="BWPS_removersd"><strong>Remove <em>EditURI</em> header</strong></label><br />
									Removes the RSD (Really Simple Discovery) header. If you don't integrate your blog with external XML-RPC services such as Flickr then the "RSD" function is pretty much useless to you.
								</p>
								<h4>Dashboard Tweaks</h4>
								<p>
									<input type="checkbox" name="BWPS_themeUpdates" id="BWPS_themeUpdates" value="1" <?php if ($opts['tweaks_themeUpdates'] == 1) echo "checked"; ?> /> <label for="BWPS_themeUpdates"><strong>Hide Theme Update Notifications</strong></label><br />
									Hides theme update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.
								</p>
								<p>
									<input type="checkbox" name="BWPS_pluginUpdates" id="BWPS_pluginUpdates" value="1" <?php if ($opts['tweaks_pluginUpdates'] == 1) echo "checked"; ?> /> <label for="BWPS_pluginUpdates"><strong>Hide Plugin Update Notifications</strong></label><br />
									Hides plugin update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.
								</p>
								<p>
									<input type="checkbox" name="BWPS_coreUpdates" id="BWPS_coreUpdates" value="1" <?php if ($opts['tweaks_coreUpdates'] == 1) echo "checked"; ?> /> <label for="BWPS_coreUpdates"><strong>Hide Core Update Notifications</strong></label><br />
									Hides core update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.
								</p>
								<h4>Other Tweaks</h4>
								<p>
									<input type="checkbox" name="BWPS_removeLoginMessages" id="BWPS_removeLoginMessages" value="1" <?php if ($opts['tweaks_removeLoginMessages'] == 1) echo "checked"; ?> /> <label for="BWPS_removeLoginMessages"><strong>Remove Wordpress Login Error Messages</strong></label><br />
									Prevents error messages from being displayed to a user upon a failed login attempt.
								</p>
								<p>
									<input type="checkbox" name="BWPS_randomVersion" id="BWPS_randomVersion" value="1" <?php if ($opts['tweaks_randomVersion'] == 1) echo "checked"; ?> /> <label for="BWPS_randomVersion"><strong>Display random version number to all non-administrative users</strong></label><br />
									Displays a random version number to non-administrator users in all places where version number must be used.
								</p>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_tweaks_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
	</div>
</div>
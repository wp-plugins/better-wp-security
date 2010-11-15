<?php
	global $BWPS_tweaks;
	
	$opts = $BWPS_tweaks->getOptions();
	
	if (isset($_POST['BWPS_tweaks_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_tweaks_save')) {
			die('Security error!');
		}	
		
		$opts = $BWPS_tweaks->saveOptions("tweaks_Version", BWPS_TWEAKS_VERSION);
		
		$opts = $BWPS_tweaks->saveOptions("tweaks_removeGenerator",$_POST['BWPS_removeGenerator']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_removeLoginMessages",$_POST['BWPS_removeLoginMessages']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_randomVersion",$_POST['BWPS_randomVersion']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_themeUpdates",$_POST['BWPS_themeUpdates']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_pluginUpdates",$_POST['BWPS_pluginUpdates']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_coreUpdates",$_POST['BWPS_coreUpdates']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_removewlm",$_POST['BWPS_removewlm']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_removersd",$_POST['BWPS_removersd']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_strongpass",$_POST['BWPS_strongpass']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_strongpassrole",$_POST['BWPS_strongpassrole']);
		$opts = $BWPS_tweaks->saveOptions("tweaks_longurls",$_POST['BWPS_longurls']);

		if ($_POST['BWPS_enforceSSL'] == 1) {
			$conf_f = trailingslashit(ABSPATH).'/wp-config.php';
			$scanText = "/* That's all, stop editing! Happy blogging. */";
			$newText = "define('FORCE_SSL_LOGIN', true);\r\ndefine('FORCE_SSL_ADMIN', true);\r\n\r\n/* That's all, stop editing! Happy blogging. */";
			chmod($conf_f, 0755);
			$handle = @fopen($conf_f, "r+");
			if ($handle) {
				while (!feof($handle)) {
					$lines[] = fgets($handle, 4096);
				}
				fclose($handle);
				$handle = @fopen($conf_f, "w+");
				foreach ($lines as $line) {
					if (strstr($line, $scanText)) {
						$line = str_replace($scanText, $newText, $line);
					}
					fwrite($handle, $line);
				}
				fclose($handle);
			}
			$sslon = "1";
		} else {
			$conf_f = trailingslashit(ABSPATH).'/wp-config.php';
			$scanText = "define('FORCE_SSL_LOGIN', true);\r\ndefine('FORCE_SSL_ADMIN', true);\r\n";
			$newText = "";
			chmod($conf_f, 0755);
			$handle = @fopen($conf_f, "r+");
			if ($handle) {
				while (!feof($handle)) {
					$lines[] = fgets($handle, 4096);
				}
				fclose($handle);
				$handle = @fopen($conf_f, "w+");
				foreach ($lines as $line) {
					if (strstr($line, $scanText)) {
						$line = str_replace($scanText, $newText, $line);
					}
					fwrite($handle, $line);
				}
				fclose($handle);
			}
			$sslon = "0";
		} 

		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>Settings Saved</p></div>';
		}
		
	} else {
		if (isset($_SERVER['HTTPS'])) {
			$sslon = "1";
		} else {
			$sslon = "0";
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
								<style type="text/css">
									h4 {
										font-size: 130%;
										display: block;
										padding: 2px;
										border-bottom: 1px solid #ccc;
									}
								</style>
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
								<h4>Strong Password Tweaks</h4>
								<p>
									<input type="checkbox" name="BWPS_strongpass" id="BWPS_strongpass" value="1" <?php if ($opts['tweaks_strongpass'] == 1) echo "checked"; ?> /> <label for="BWPS_strongpass"><strong>Enable strong password enforcement</strong></label><br />
									Enforce strong passwords for all users with at least the role specified below.
								</p>
								<p>
									<select name="BWPS_strongpassrole" id="BWPS_strongpassrole"><option value="administrator" <?php if ($opts['tweaks_strongpassrole'] == "administrator") echo "selected"; ?>>Administrator</option><option value="editor" <?php if ($opts['tweaks_strongpassrole'] == "editor") echo "selected"; ?>>Editor</option><option value="author" <?php if ($opts['tweaks_strongpassrole'] == "author") echo "selected"; ?>>Author</option><option value="contributor" <?php if ($opts['tweaks_strongpassrole'] == "contributor") echo "selected"; ?>>Contributor</option><option value="subscriber" <?php if ($opts['tweaks_strongpassrole'] == "subscriber") echo "selected"; ?>>Subscriber</option></select> <label for="BWPS_strongpassrole"><strong>Strong Password Role</strong></label><br />
									Minimum role at which a user must choose a strong password. For more information on Wordpress roles and capabilities please see <a hre="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">http://codex.wordpress.org/Roles_and_Capabilities</a>.
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
								<p>
									<input type="checkbox" name="BWPS_longurls" id="BWPS_longurls" value="1" <?php if ($opts['tweaks_longurls'] == 1) echo "checked"; ?> /> <label for="BWPS_longurls"><strong>Prevent long URL strings.</strong></label><br />
									Limits the number of characters that can be sent in the URL. Hackers often take advantage of long URLs to try to inject information into your database.
								</p>
								<h4>SSL Tweaks</h4>
								<p>
									<h4 style="color: red; text-align: center; border-bottom: none;">WARNING: You're server MUST support SSL to use this feature. Using this feature without SSL support will cause the backend of your site to become unavailable.</h4><br />
									<input type="checkbox" name="BWPS_enforceSSL" id="BWPS_enforceSSL" value="1" <?php if ($sslon == "1" || $BWPS_tweaks->checkSSL()) echo "checked"; ?> /> <label for="BWPS_enforceSSL"><strong>Enforce SSL</strong></label><br />
									Prevents error messages from being displayed to a user upon a failed login attempt.
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
<?php
	global $BWPS;
	
	$opts = $BWPS->getOptions();
	
	if (isset($_POST['BWPS_tweaks_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_tweaks_save')) {
			die('Security error!');
		}	
		
		$opts = $BWPS->saveOptions("tweaks_removeGenerator",$_POST['BWPS_removeGenerator']);
		$opts = $BWPS->saveOptions("tweaks_removeLoginMessages",$_POST['BWPS_removeLoginMessages']);
		$opts = $BWPS->saveOptions("tweaks_randomVersion",$_POST['BWPS_randomVersion']);
		$opts = $BWPS->saveOptions("tweaks_themeUpdates",$_POST['BWPS_themeUpdates']);
		$opts = $BWPS->saveOptions("tweaks_pluginUpdates",$_POST['BWPS_pluginUpdates']);
		$opts = $BWPS->saveOptions("tweaks_coreUpdates",$_POST['BWPS_coreUpdates']);
		$opts = $BWPS->saveOptions("tweaks_removewlm",$_POST['BWPS_removewlm']);
		$opts = $BWPS->saveOptions("tweaks_removersd",$_POST['BWPS_removersd']);
		$opts = $BWPS->saveOptions("tweaks_strongpass",$_POST['BWPS_strongpass']);
		$opts = $BWPS->saveOptions("tweaks_strongpassrole",$_POST['BWPS_strongpassrole']);
		$opts = $BWPS->saveOptions("tweaks_longurls",$_POST['BWPS_longurls']);
		
		
		$wpText = "/* That's all, stop editing! Happy blogging. */";
		$sslText1 = "define('FORCE_SSL_LOGIN', true);";
		$sslText2 = "define('FORCE_SSL_ADMIN', true);";
		$editText = "define('DISALLOW_FILE_EDIT', true);";
		$configFile = $BWPS->getConfig();
			
		//Read wp-config.php into an array
		$handle = @fopen($configFile, "r+");
		if ($handle) {
			while (!feof($handle)) {
				$lines[] = fgets($handle, 4096);
			}
			fclose($handle);
		}
			
		$handle = fopen($configFile, "w+");
		if (isset($_POST['BWPS_enforceSSL']) || isset($_POST['BWPS_nofileedit'])) {
			$sslon = '0';
			$nofileedit = '0';
						
			//rewrite wp-config.php	
			foreach ($lines as $line) {
				if (strstr($line, $sslText1) || strstr($line, $sslText2) || strstr($line, $editText)) {
					$line = str_replace($line, '', $line);
				}
				if (strstr($line, $wpText)) {
					$line = $wpText;
					if (isset($_POST['BWPS_enforceSSL'])) {
						$line = $sslText1 . "\r\n" . $sslText2 . "\r\n" . $line;
						$sslon = '1';
					}
					if (isset($_POST['BWPS_nofileedit'])) {
						$line = $editText . "\r\n" . $line;
					$nofileedit = '1';
					}					
				}
				fwrite($handle, $line);
			}
		
		} else {
			foreach ($lines as $line) {
				if (!stristr($line, $sslText1) && !stristr($line, $sslText2) && !stristr($line, $editText)) {
					fwrite($handle, $line);
				}
			}
		}
		
		fclose($handle);

		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>' . __('Settings Saved', 'better-wp-security') . '</p></div>';
		}
		
	} else {
		if (isset($_SERVER['HTTPS'])) {
			$sslon = "1";
		} else {
			$sslon = "0";
		}
		$nofileedit = "0";
		$conf_f = trailingslashit(ABSPATH).'/wp-config.php';
		$scanText = "define('DISALLOW_FILE_EDIT', true);";
		$handle = @fopen($conf_f, "r");
		if ($handle) {
			while (!feof($handle)) {
				$lines[] = fgets($handle, 4096);
			}
			fclose($handle);
			foreach ($lines as $line) {
				if (strstr($line, $scanText)) {
					$nofileedit = "1";
				}
			}
		}
	}
	
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('Security Tweaks', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
	
		<?php if ($BWPS->can_write(ABSPATH . WPINC . '/wp-config.php')) { ?>
			<div class="postbox-container" style="width:70%">	
				<div class="postbox opened">
					<h3><?php _e('tweaks Options', 'better-wp-security'); ?></h3>	
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
									<h4><?php _e('Header Tweaks', 'better-wp-security'); ?></h4>
									<p>
										<input type="checkbox" name="BWPS_removeGenerator" id="BWPS_removeGenerator" value="1" <?php if ($opts['tweaks_removeGenerator'] == 1) echo "checked"; ?> /> <label for="BWPS_removeGenerator"><strong><?php _e('Remove Wordpress Generator Meta Tag', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Removes the', 'better-wp-security'); ?> <em>&lt;meta name="generator" content="WordPress [version]" /&gt;</em> <?php _e('meta tag from your sites header. This process hides version information from a potential attacker making it more difficult to determine vulnerabilities.', 'better-wp-security'); ?>
									</p>
									<p>
										<input type="checkbox" name="BWPS_removewlm" id="BWPS_removewlm" value="1" <?php if ($opts['tweaks_removewlm'] == 1) echo "checked"; ?> /> <label for="BWPS_removewlm"><strong><?php _e('Remove', 'better-wp-security'); ?> <em>wlwmanifest</em> <?php _e('header', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Removes the Windows Live Writer header. This is not needed if you do not use Windows Live Writer.', 'better-wp-security', 'better-wp-security'); ?>
									</p>
									<p>
										<input type="checkbox" name="BWPS_removersd" id="BWPS_removersd" value="1" <?php if ($opts['tweaks_removersd'] == 1) echo "checked"; ?> /> <label for="BWPS_removersd"><strong><?php _e('Remove', 'better-wp-security'); ?> <em>EditURI</em> <?php _e('header', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Removes the RSD (Really Simple Discovery) header. If you don\'t integrate your blog with external XML-RPC services such as Flickr then the "RSD" function is pretty much useless to you.', 'better-wp-security'); ?>
									</p>
									<h4><?php _e('Dashboard Tweaks', 'better-wp-security'); ?></h4>
									<p>
										<input type="checkbox" name="BWPS_themeUpdates" id="BWPS_themeUpdates" value="1" <?php if ($opts['tweaks_themeUpdates'] == 1) echo "checked"; ?> /> <label for="BWPS_themeUpdates"><strong><?php _e('Hide Theme Update Notifications', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Hides theme update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.', 'better-wp-security'); ?>
									</p>
									<p>
										<input type="checkbox" name="BWPS_pluginUpdates" id="BWPS_pluginUpdates" value="1" <?php if ($opts['tweaks_pluginUpdates'] == 1) echo "checked"; ?> /> <label for="BWPS_pluginUpdates"><strong><?php _e('Hide Plugin Update Notifications', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Hides plugin update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.', 'better-wp-security'); ?>
									</p>
									<p>
										<input type="checkbox" name="BWPS_coreUpdates" id="BWPS_coreUpdates" value="1" <?php if ($opts['tweaks_coreUpdates'] == 1) echo "checked"; ?> /> <label for="BWPS_coreUpdates"><strong><?php _e('Hide Core Update Notifications', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Hides core update notifications from users who cannot update themes. Please note that this only makes a difference in multi-site installations.', 'better-wp-security'); ?>
									</p>
									<h4><?php _e('Strong Password Tweaks', 'better-wp-security'); ?></h4>
									<p>
										<input type="checkbox" name="BWPS_strongpass" id="BWPS_strongpass" value="1" <?php if ($opts['tweaks_strongpass'] == 1) echo "checked"; ?> /> <label for="BWPS_strongpass"><strong><?php _e('Enable strong password enforcement', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Enforce strong passwords for all users with at least the role specified below.', 'better-wp-security'); ?>
									</p>
									<p>
										<select name="BWPS_strongpassrole" id="BWPS_strongpassrole"><option value="administrator" <?php if ($opts['tweaks_strongpassrole'] == "administrator") echo "selected"; ?>>Administrator</option><option value="editor" <?php if ($opts['tweaks_strongpassrole'] == "editor") echo "selected"; ?>>Editor</option><option value="author" <?php if ($opts['tweaks_strongpassrole'] == "author") echo "selected"; ?>>Author</option><option value="contributor" <?php if ($opts['tweaks_strongpassrole'] == "contributor") echo "selected"; ?>>Contributor</option><option value="subscriber" <?php if ($opts['tweaks_strongpassrole'] == "subscriber") echo "selected"; ?>>Subscriber</option></select> <label for="BWPS_strongpassrole"><strong><?php _e('Strong Password Role', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Minimum role at which a user must choose a strong password. For more information on Wordpress roles and capabilities please see', 'better-wp-security'); ?> <a hre="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">http://codex.wordpress.org/Roles_and_Capabilities</a>.
									</p>
									<h4><?php _e('Other Tweaks', 'better-wp-security'); ?></h4>
									<p>
										<input type="checkbox" name="BWPS_removeLoginMessages" id="BWPS_removeLoginMessages" value="1" <?php if ($opts['tweaks_removeLoginMessages'] == 1) echo "checked"; ?> /> <label for="BWPS_removeLoginMessages"><strong><?php _e('Remove Wordpress Login Error Messages', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Prevents error messages from being displayed to a user upon a failed login attempt.', 'better-wp-security'); ?>
									</p>
									<p>
										<input type="checkbox" name="BWPS_randomVersion" id="BWPS_randomVersion" value="1" <?php if ($opts['tweaks_randomVersion'] == 1) echo "checked"; ?> /> <label for="BWPS_randomVersion"><strong><?php _e('Display random version number to all non-administrative users', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Displays a random version number to non-administrator users in all places where version number must be used and removes the version completely from where it can.', 'better-wp-security'); ?>
									</p>
									<p>
										<input type="checkbox" name="BWPS_longurls" id="BWPS_longurls" value="1" <?php if ($opts['tweaks_longurls'] == 1) echo "checked"; ?> /> <label for="BWPS_longurls"><strong><?php _e('Prevent long URL strings.', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Limits the number of characters that can be sent in the URL. Hackers often take advantage of long URLs to try to inject information into your database.', 'better-wp-security'); ?>
									</p>
									<p>
										<input type="checkbox" name="BWPS_nofileedit" id="BWPS_nofileedit" value="1" <?php if ($nofileedit == 1) echo "checked"; ?> /> <label for="BWPS_nofileedit"><strong><?php _e('Turn off file editor in Wordpress Back-end.', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Disables the file editor for plugins and themes requiring users to have access to the file system to modify files.', 'better-wp-security'); ?>
									</p>
									<h4><?php _e('SSL Tweaks'); ?></h4>
									<p>
										<h4 style="color: red; text-align: center; border-bottom: none;"><?php _e('WARNING: You\'re server MUST support SSL to use this feature. Using this feature without SSL support will cause the backend of your site to become unavailable.', 'better-wp-security'); ?></h4><br />
										<input type="checkbox" name="BWPS_enforceSSL" id="BWPS_enforceSSL" value="1" <?php if ($sslon == "1" || $BWPS->tweaks_checkSSL()) echo "checked"; ?> /> <label for="BWPS_enforceSSL"><strong><?php _e('Enforce SSL', 'better-wp-security'); ?></strong></label><br />
										<?php _e('Prevents error messages from being displayed to a user upon a failed login attempt.', 'better-wp-security'); ?>
									</p>
								</tbody>
							</table>	
							<p class="submit"><input type="submit" name="BWPS_tweaks_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
						</form>
					</div>
				</div>
			</div>
			
		<?php } else { ?>
			<div class="postbox-container" style="width:70%">	
				<div class="postbox opened" style="background-color: #ffebeb;">
					<h3><?php _e('Security Tweaks', 'better-wp-security'); ?></h3>	
					<div class="inside">
						<h4><?php _e('Warning: Better WP Security cannot write to your <em>wp-config.php</em> file. You must fix this error before continuing.','better-wp-security'); ?></h4>
					</div>
				</div>
			</div>
		<?php } ?>		
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
	</div>
</div>
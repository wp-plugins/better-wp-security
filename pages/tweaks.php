<?php
	global $BWPS_tweaks;
	
	$opts = $BWPS_tweaks->getOptions();
	
	if (isset($_POST['BWPS_tweaks_save'])) { // Save options
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_tweaks_save')) { //verify nonce field
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
		
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
		if (!$BWPS_tweaks->can_write($htaccess)) { 
			
			$errorHandler = new WP_Error();
			
			$errorHandler->add("2", __("Unable to update htaccess rules"));
			
		} else {
		
			$opts = $BWPS_tweaks->saveOptions("tweaks_protectht",$_POST['BWPS_protectht']);
			$opts = $BWPS_tweaks->saveOptions("tweaks_protectwpc",$_POST['BWPS_protectwpc']);
			$opts = $BWPS_tweaks->saveOptions("tweaks_dirbrowse",$_POST['BWPS_dirbrowse']);
			$opts = $BWPS_tweaks->saveOptions("tweaks_hotlink",$_POST['BWPS_hotlink']);
			$opts = $BWPS_tweaks->saveOptions("tweaks_request",$_POST['BWPS_request']);
			$opts = $BWPS_tweaks->saveOptions("tweaks_qstring",$_POST['BWPS_qstring']);
		
			if ($_POST['BWPS_protectht'] == 1) { 
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Protect htaccess');
				$rules = "<files .htaccess>\n" .
					"order allow,deny\n" . 
					"deny from all\n" .
					"</files>\n";
				insert_with_markers($htaccess,'Better WP Security Protect htaccess', explode( "\n", $rules));		
			} else {
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Protect htaccess');
			}
			
			if ($_POST['BWPS_protectwpc'] == 1) { 
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Protect wp-config');
				$rules = "<files wp-config.php>\n" .
					"order allow,deny\n" . 
					"deny from all\n" .
					"</files>\n";
				insert_with_markers($htaccess,'Better WP Security Protect wp-config', explode( "\n", $rules));		
			} else {
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Protect wp-config');
			}
			
			if ($_POST['BWPS_request'] == 1) { 
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Filter Request Methods');
				$rules = "<IfModule mod_rewrite.c>\n" . 
					"RewriteEngine On\n" . 
					"RewriteBase /\n" . 
					"RewriteCond %{REQUEST_METHOD} ^(HEAD|TRACE|DELETE|TRACK) [NC]\n" . 
					"RewriteRule ^(.*)$ - [F,L]\n" . 
					"</IfModule>\n";
				insert_with_markers($htaccess,'Better WP Security Filter Request Methods', explode( "\n", $rules));		
			} else {
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Filter Request Methods');
			}
			
			if ($_POST['BWPS_qstring'] == 1) { 
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Filter Query String Exploits');
				$rules = "<IfModule mod_rewrite.c>\n" . 
					"RewriteEngine On\n" . 
					"RewriteBase /\n" . 
					"RewriteCond %{QUERY_STRING} \.\.\/ [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} boot\.ini [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} tag\= [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} ftp\:  [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} http\:  [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} https\:  [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|%3D) [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} ^.*(\[|\]|\(|\)|<|>|Õ|\"|;|\?|\*|=$).* [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} ^.*(&#x22;|&#x27;|&#x3C;|&#x3E;|&#x5C;|&#x7B;|&#x7C;).* [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} ^.*(%24&x).* [NC,OR]\n" .  
					"RewriteCond %{QUERY_STRING} ^.*(%0|%A|%B|%C|%D|%E|%F|127\.0).* [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} ^.*(globals|encode|localhost|loopback).* [NC,OR]\n" . 
					"RewriteCond %{QUERY_STRING} ^.*(request|select|insert|union|declare|drop).* [NC]\n" . 
					"RewriteRule ^(.*)$ - [F,L]\n" . 
					"</IfModule>\n";
				insert_with_markers($htaccess,'Better WP Security Filter Query String Exploits', explode( "\n", $rules));		
			} else {
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Filter Query String Exploits');
			}
			
			if ($_POST['BWPS_dirbrowse'] == 1) { 
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Prevent Directory Browsing');
				$rules = "Options All -Indexes\n";
				insert_with_markers($htaccess,'Better WP Security Prevent Directory Browsing', explode( "\n", $rules));		
			} else {
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Prevent Directory Browsing');
			}
			
			if ($_POST['BWPS_hotlink'] == 1) { 
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Prevent Hotlinking');
				
				$reDomain = $BWPS_tweaks->uDomain(get_option('siteurl'));
				
				$rules = "<IfModule mod_rewrite.c>\n" . 
					"RewriteEngine On\n" . 
					"RewriteBase /\n" . 
					"RewriteCond %{HTTP_REFERER} !^$\n" .
					"RewriteCond %{HTTP_REFERER} !^" . $reDomain . "/.*$ [NC]\n" .
					"RewriteRule .(jpg|jpeg|png|gif|pdf|doc)$ - [F]\n" . 
					"</IfModule>\n";
				
				$wprules = implode("\n", extract_from_markers($htaccess, 'WordPress' ));
				
				$BWPS_tweaks->remove_section($htaccess, 'WordPress');
				insert_with_markers($htaccess,'Better WP Security Prevent Hotlinking', explode( "\n", $rules));	
				insert_with_markers($htaccess,'WordPress', explode( "\n", $wprules));
					
			} else {
				$BWPS_tweaks->remove_section($htaccess, 'Better WP Security Prevent Hotlinking');
			}
			
		}

		
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
								<h4>.htaccess Tweaks</h4>
								<p>
									<input type="checkbox" name="BWPS_protectht" id="BWPS_protectht" value="1" <?php if ($opts['tweaks_protectht'] == 1) echo "checked"; ?> /> <label for="BWPS_protectht"><strong>Protect .htaccess</strong></label><br />
									Add extra protection to the .htaccess file.
								</p>
								<p>
									<input type="checkbox" name="BWPS_protectwpc" id="BWPS_protectwpc" value="1" <?php if ($opts['tweaks_protectwpc'] == 1) echo "checked"; ?> /> <label for="BWPS_protectwpc"><strong>Protect wp-config.php</strong></label><br />
									Prevents access to the wp-config.php file
								</p>
								<p>
									<input type="checkbox" name="BWPS_dirbrowse" id="BWPS_dirbrowse" value="1" <?php if ($opts['tweaks_dirbrowse'] == 1) echo "checked"; ?> /> <label for="BWPS_dirbrowse"><strong>Disable directory browsing</strong></label><br />
									Prevents users from seeing a list of files in a directory when no index file is present
								</p>
								<p>
									<input type="checkbox" name="BWPS_hotlink" id="BWPS_hotlink" value="1" <?php if ($opts['tweaks_hotlink'] == 1) echo "checked"; ?> /> <label for="BWPS_hotlink"><strong>Prevent Hotlinking</strong></label><br />
									Prevents visitors from being able to directly link to images, documents, and other files which could hurt your bandwidth.
								</p>
								<p>
									<input type="checkbox" name="BWPS_request" id="BWPS_request" value="1" <?php if ($opts['tweaks_request'] == 1) echo "checked"; ?> /> <label for="BWPS_request"><strong>Filter Request Methods</strong></label><br />
									Filter out hits with the head, trace, delete, or track request methods.
								</p>
								<p>
									<input type="checkbox" name="BWPS_qstring" id="BWPS_qstring" value="1" <?php if ($opts['tweaks_qstring'] == 1) echo "checked"; ?> /> <label for="BWPS_qstring"><strong>Filter suspicious query strings</strong></label><br />
									Filter out suspicious query strings in the URL.
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
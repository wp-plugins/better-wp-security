<?php
	global $BWPS_htaccess;
	
	$opts = $BWPS_htaccess->getOptions();
	
	if (isset($_POST['BWPS_htaccess_save'])) { // Save options
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_htaccess_save')) { //verify nonce field
			die('Security error!');
		}	
		
		$opts = $BWPS_htaccess->saveOptions("htaccess_Version", BWPS_HTACCESS_VERSION);
				
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
		if (!$BWPS_htaccess->can_write($htaccess)) { 
			
			$errorHandler = new WP_Error();
			
			$errorHandler->add("2", __("Unable to update htaccess rules"));
			
		} else {
		
			$opts = $BWPS_htaccess->saveOptions("htaccess_protectht",$_POST['BWPS_protectht']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_protectwpc",$_POST['BWPS_protectwpc']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_dirbrowse",$_POST['BWPS_dirbrowse']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_hotlink",$_POST['BWPS_hotlink']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_request",$_POST['BWPS_request']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_qstring",$_POST['BWPS_qstring']);
		
			if ($_POST['BWPS_protectht'] == 1) { 
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Protect htaccess');
				$rules = "<files .htaccess>\n" .
					"order allow,deny\n" . 
					"deny from all\n" .
					"</files>\n";
				insert_with_markers($htaccess,'Better WP Security Protect htaccess', explode( "\n", $rules));		
			} else {
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Protect htaccess');
			}
			
			if ($_POST['BWPS_protectwpc'] == 1) { 
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Protect wp-config');
				$rules = "<files wp-config.php>\n" .
					"order allow,deny\n" . 
					"deny from all\n" .
					"</files>\n";
				insert_with_markers($htaccess,'Better WP Security Protect wp-config', explode( "\n", $rules));		
			} else {
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Protect wp-config');
			}
			
			if ($_POST['BWPS_request'] == 1) { 
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Filter Request Methods');
				$rules = "<IfModule mod_rewrite.c>\n" . 
					"RewriteEngine On\n" . 
					"RewriteBase /\n" . 
					"RewriteCond %{REQUEST_METHOD} ^(HEAD|TRACE|DELETE|TRACK) [NC]\n" . 
					"RewriteRule ^(.*)$ - [F,L]\n" . 
					"</IfModule>\n";
				insert_with_markers($htaccess,'Better WP Security Filter Request Methods', explode( "\n", $rules));		
			} else {
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Filter Request Methods');
			}
			
			if ($_POST['BWPS_qstring'] == 1) { 
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Filter Query String Exploits');
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
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Filter Query String Exploits');
			}
			
			if ($_POST['BWPS_dirbrowse'] == 1) { 
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Prevent Directory Browsing');
				$rules = "Options All -Indexes\n";
				insert_with_markers($htaccess,'Better WP Security Prevent Directory Browsing', explode( "\n", $rules));		
			} else {
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Prevent Directory Browsing');
			}
			
			if ($_POST['BWPS_hotlink'] == 1) { 
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Prevent Hotlinking');
				
				$reDomain = $BWPS_htaccess->uDomain(get_option('siteurl'));
				
				$rules = "<IfModule mod_rewrite.c>\n" . 
					"RewriteEngine On\n" . 
					"RewriteBase /\n" . 
					"RewriteCond %{HTTP_REFERER} !^$\n" .
					"RewriteCond %{HTTP_REFERER} !^" . $reDomain . "/.*$ [NC]\n" .
					"RewriteRule .(jpg|jpeg|png|gif|pdf|doc)$ - [F]\n" . 
					"</IfModule>\n";
				
				$wprules = implode("\n", extract_from_markers($htaccess, 'WordPress' ));
				
				$BWPS_htaccess->remove_section($htaccess, 'WordPress');
				insert_with_markers($htaccess,'Better WP Security Prevent Hotlinking', explode( "\n", $rules));	
				insert_with_markers($htaccess,'WordPress', explode( "\n", $wprules));
					
			} else {
				$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Prevent Hotlinking');
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

	<h2>Better WP Security - .htaccess Options</h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>htaccess Options</h3>	
				<div class="inside">
					<p></p>
					<form method="post">
						<?php wp_nonce_field('BWPS_htaccess_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<p>
									<input type="checkbox" name="BWPS_protectht" id="BWPS_protectht" value="1" <?php if ($opts['htaccess_protectht'] == 1) echo "checked"; ?> /> <label for="BWPS_protectht"><strong>Protect .htaccess</strong></label><br />
									Add extra protection to the .htaccess file.
								</p>
								<p>
									<input type="checkbox" name="BWPS_protectwpc" id="BWPS_protectwpc" value="1" <?php if ($opts['htaccess_protectwpc'] == 1) echo "checked"; ?> /> <label for="BWPS_protectwpc"><strong>Protect wp-config.php</strong></label><br />
									Prevents access to the wp-config.php file
								</p>
								<p>
									<input type="checkbox" name="BWPS_dirbrowse" id="BWPS_dirbrowse" value="1" <?php if ($opts['htaccess_dirbrowse'] == 1) echo "checked"; ?> /> <label for="BWPS_dirbrowse"><strong>Disable directory browsing</strong></label><br />
									Prevents users from seeing a list of files in a directory when no index file is present
								</p>
								<p>
									<input type="checkbox" name="BWPS_hotlink" id="BWPS_hotlink" value="1" <?php if ($opts['htaccess_hotlink'] == 1) echo "checked"; ?> /> <label for="BWPS_hotlink"><strong>Prevent Hotlinking</strong></label><br />
									Prevents visitors from being able to directly link to images, documents, and other files which could hurt your bandwidth.
								</p>
								<p>
									<input type="checkbox" name="BWPS_request" id="BWPS_request" value="1" <?php if ($opts['htaccess_request'] == 1) echo "checked"; ?> /> <label for="BWPS_request"><strong>Filter Request Methods</strong></label><br />
									Filter out hits with the head, trace, delete, or track request methods.
								</p>
								<p>
									<input type="checkbox" name="BWPS_qstring" id="BWPS_qstring" value="1" <?php if ($opts['htaccess_qstring'] == 1) echo "checked"; ?> /> <label for="BWPS_qstring"><strong>Filter suspicious query strings</strong></label><br />
									Filter out suspicious query strings in the URL.
								</p>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_htaccess_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		<div class="clear"></div>
		
		<div class="postbox-container" style="width:70%">
			<div class="postbox opened">
				<h3>Current .htaccess</h3>	
				<div class="inside">
					<p>Here are the current contents of your .htaccess file.</p>
					<?php $BWPS_htaccess->showContents(); ?>
				</div>
			</div>
		</div>
		
	</div>
</div>
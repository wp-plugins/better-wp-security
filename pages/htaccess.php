<?php
	require_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/functions/htaccess.php');
	
	$BWPS_htaccess = new BWPS_htaccess();
	
	$opts = $BWPS_htaccess->getOptions();
	
	if (isset($_POST['BWPS_htaccess_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_htaccess_save')) {
			die('Security error!');
		}	
		
		$opts = $BWPS_htaccess->saveOptions("htaccess_Version", BWPS_HTACCESS_VERSION);
				
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
		if (!$BWPS_htaccess->can_write($htaccess)) { 
			
			if (!$errorHandler) {
				$errorHandler = new WP_Error();
			}
			
			$errorHandler->add("2", __("Unable to update htaccess rules"));
			
		} else {
		
			$opts = $BWPS_htaccess->saveOptions("htaccess_protectht",$_POST['BWPS_protectht']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_protectwpc",$_POST['BWPS_protectwpc']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_dirbrowse",$_POST['BWPS_dirbrowse']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_hotlink",$_POST['BWPS_hotlink']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_request",$_POST['BWPS_request']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_qstring",$_POST['BWPS_qstring']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_protectreadme",$_POST['BWPS_protectreadme']);
			$opts = $BWPS_htaccess->saveOptions("htaccess_protectinstall",$_POST['BWPS_protectinstall']);
			
			$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Protect wp-config');
			$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Prevent Directory Browsing');
			$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Prevent Hotlinking');
			$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Filter Request Methods');
			$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Filter Query String Exploits');
			
			$BWPS_htaccess->remove_section($htaccess, 'Better WP Security Protect htaccess');
			
			$wprules = implode("\n", extract_from_markers($htaccess, 'WordPress' ));
				
			$BWPS_htaccess->remove_section($htaccess, 'WordPress');
			
			insert_with_markers($htaccess,'Better WP Security Protect htaccess', explode( "\n", $BWPS_htaccess->genRules()));
			insert_with_markers($htaccess,'WordPress', explode( "\n", $wprules));	
			
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
									<input type="checkbox" name="BWPS_protectreadme" id="BWPS_protectreadme" value="1" <?php if ($opts['htaccess_protectreadme'] == 1) echo "checked"; ?> /> <label for="BWPS_protectreadme"><strong>Protect Readme.html</strong></label><br />
									Remove access to <a href="/readme.html" target="_blank">readme.html</a> which can give away your Wordpress version.
								</p>
								<p>
									<input type="checkbox" name="BWPS_protectinstall" id="BWPS_protectinstall" value="1" <?php if ($opts['htaccess_protectinstall'] == 1) echo "checked"; ?> /> <label for="BWPS_protectinstall"><strong>Protect Wordpress installer script</strong></label><br />
									Remove access to <a href="wp-admin/install.php" target="_blank">wp-admin/install.php</a>.
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
<?php
	global $BWPS;
	
	$opts = $BWPS->getOptions();
	
	if (isset($_POST['BWPS_htaccess_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_htaccess_save')) {
			die('Security error!');
		}	
				
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
		if (!$BWPS->can_write($htaccess)) { 
			
			if (!$errorHandler) {
				$errorHandler = new WP_Error();
			}
			
			$errorHandler->add("2", __("Unable to update htaccess rules", 'better-wp-security'));
			
		} else {
		
			$opts = $BWPS->saveOptions("htaccess_protectht",$_POST['BWPS_protectht']);
			$opts = $BWPS->saveOptions("htaccess_protectwpc",$_POST['BWPS_protectwpc']);
			$opts = $BWPS->saveOptions("htaccess_dirbrowse",$_POST['BWPS_dirbrowse']);
			$opts = $BWPS->saveOptions("htaccess_hotlink",$_POST['BWPS_hotlink']);
			$opts = $BWPS->saveOptions("htaccess_request",$_POST['BWPS_request']);
			$opts = $BWPS->saveOptions("htaccess_qstring",$_POST['BWPS_qstring']);
			$opts = $BWPS->saveOptions("htaccess_protectreadme",$_POST['BWPS_protectreadme']);
			$opts = $BWPS->saveOptions("htaccess_protectinstall",$_POST['BWPS_protectinstall']);
			
			$BWPS->remove_section($htaccess, 'Better WP Security Protect wp-config');
			$BWPS->remove_section($htaccess, 'Better WP Security Prevent Directory Browsing');
			$BWPS->remove_section($htaccess, 'Better WP Security Prevent Hotlinking');
			$BWPS->remove_section($htaccess, 'Better WP Security Filter Request Methods');
			$BWPS->remove_section($htaccess, 'Better WP Security Filter Query String Exploits');
			
			$BWPS->createhtaccess();	
			
		}
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			$BWPS->saveVersions('HTACCESS', BWPS_VERSION_HTACCESS);
			echo '<div id="message" class="updated"><p>' . __('Settings Saved', 'better-wp-security') . '</p></div>';
		}
	}
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('.htaccess Options', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('htaccess Options', 'better-wp-security'); ?></h3>	
				<div class="inside">
					<p></p>
					<form method="post">
						<?php wp_nonce_field('BWPS_htaccess_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<p>
									<input type="checkbox" name="BWPS_protectht" id="BWPS_protectht" value="1" <?php if ($opts['htaccess_protectht'] == 1) echo "checked"; ?> /> <label for="BWPS_protectht"><strong><?php _e('Protect .htaccess', 'better-wp-security'); ?></strong></label><br />
									<?php _e('Add extra protection to the .htaccess file.', 'better-wp-security'); ?>
								</p>
								<p>
									<input type="checkbox" name="BWPS_protectreadme" id="BWPS_protectreadme" value="1" <?php if ($opts['htaccess_protectreadme'] == 1) echo "checked"; ?> /> <label for="BWPS_protectreadme"><strong><?php _e('Protect Readme.html', 'better-wp-security'); ?></strong></label><br />
									<?php _e('Remove access to', 'better-wp-security'); ?> <a href="/readme.html" target="_blank">readme.html</a> <?php _e('which can give away your Wordpress version.'); ?>
								</p>
								<p>
									<input type="checkbox" name="BWPS_protectinstall" id="BWPS_protectinstall" value="1" <?php if ($opts['htaccess_protectinstall'] == 1) echo "checked"; ?> /> <label for="BWPS_protectinstall"><strong><?php _e('Protect Wordpress installer script', 'better-wp-security'); ?></strong></label><br />
									<?php _e('Remove access to', 'better-wp-security'); ?> <a href="wp-admin/install.php" target="_blank">wp-admin/install.php</a>.
								</p>
								<p>
									<input type="checkbox" name="BWPS_protectwpc" id="BWPS_protectwpc" value="1" <?php if ($opts['htaccess_protectwpc'] == 1) echo "checked"; ?> /> <label for="BWPS_protectwpc"><strong><?php _e('Protect wp-config.php', 'better-wp-security'); ?></strong></label><br />
									<?php _e('Prevents access to the wp-config.php file', 'better-wp-security'); ?>
								</p>
								<p>
									<input type="checkbox" name="BWPS_dirbrowse" id="BWPS_dirbrowse" value="1" <?php if ($opts['htaccess_dirbrowse'] == 1) echo "checked"; ?> /> <label for="BWPS_dirbrowse"><strong><?php _e('Disable directory browsing', 'better-wp-security'); ?></strong></label><br />
									<?php _e('Prevents users from seeing a list of files in a directory when no index file is present'); ?>
								</p>
								<p>
									<input type="checkbox" name="BWPS_request" id="BWPS_request" value="1" <?php if ($opts['htaccess_request'] == 1) echo "checked"; ?> /> <label for="BWPS_request"><strong><?php _e('Filter Request Methods', 'better-wp-security'); ?></strong></label><br />
									<?php _e('Filter out hits with the head, trace, delete, or track request methods.', 'better-wp-security'); ?>
								</p>
								<p>
									<input type="checkbox" name="BWPS_qstring" id="BWPS_qstring" value="1" <?php if ($opts['htaccess_qstring'] == 1) echo "checked"; ?> /> <label for="BWPS_qstring"><strong><?php _e('Filter suspicious query strings', 'better-wp-security'); ?></strong></label><br />
									<?php _e('Filter out suspicious query strings in the URL.', 'better-wp-security'); ?>
								</p>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_htaccess_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
	</div>
</div>
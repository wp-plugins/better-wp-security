<?php
	global $wpdb;
	
	if (isset($_POST['BWPS_content_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_content_save')) {
			die('Security error!');
		}
		
		$olddir = getDir();
		$newdir = $wpdb->escape($_POST['newdir']);
		
		rename(trailingslashit(ABSPATH) . $olddir, trailingslashit(ABSPATH) . $newdir);
		
		$conf_f = trailingslashit(ABSPATH).'/wp-config.php';
		$scanText = "/* That's all, stop editing! Happy blogging. */";
		$newText = "define('WP_CONTENT_DIR', '" . trailingslashit(ABSPATH) . $newdir . "');\r\ndefine('WP_CONTENT_URL', '" . trailingslashit(get_option('siteurl')) . $newdir . "');\r\n\r\n/* That's all, stop editing! Happy blogging. */";
		chmod($conf_f, 0755);
		$handle = @fopen($conf_f, "r+");
		if ($handle) {
			while (!feof($handle)) {
				$lines[] = fgets($handle, 4096);
			}
			fclose($handle);
			$handle = @fopen($conf_f, "w+");
			foreach ($lines as $line) {
				if (strstr($line,"WP_CONTENT_DIR") || strstr($line,"WP_CONTENT_URL") ) {
					$line = str_replace($line, "", $line);
				}
				if (strstr($line, $scanText)) {
					$line = str_replace($scanText, $newText, $line);
				}
				fwrite($handle, $line);
			} 
			fclose($handle);
		}
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p><em>content</em> directory successfully changed.</p></div>';
		}
	}
	
	function getDir() {
		if (defined('WP_CONTENT_DIR') && defined('WP_CONTENT_URL')) {
		$dir = WP_CONTENT_DIR;
		$ls =  strripos($dir,'/') + 1;
		$dir = substr($dir, $ls, strlen($dir));
		} else {
			$dir = 'wp-content';
		}
		return $dir;
	}
		
	if (!isset($_POST['BWPS_content_save'])) {
?>

	<div class="wrap" >

		<h2>Better WP Security - Content Directory</h2>
	
		<div id="poststuff" class="ui-sortable">	
			<div class="postbox-container" style="width:70%">	
				<div class="postbox opened">
					<h3>Change Content Directory</h3>	
					<div class="inside">
						<p>Select a new name for the content directory.</p>
						<form method="post">
							<?php wp_nonce_field('BWPS_content_save','wp_nonce') ?>
							<label for="newdir">Directory Name: </label> <input id="newdir" name="newdir" type="text" value="<?php echo getDir(); ?>">
							<p class="submit"><input type="submit" name="BWPS_content_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
						</form>
					</div>
				</div>
			</div>
			
			<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		</div>
	</div>
<?php } else { ?>
	<div class="wrap" >

		<h2>Better WP Security - Content Directory</h2>
	
		<div id="poststuff" class="ui-sortable">	
			<div class="postbox-container" style="width:100%">	
				<div class="postbox opened">
					<h3>Change Content Directory</h3>	
					<div class="inside">
						<p>The directory has been changed please reload this page if you wish to change it again.</p>
					</div>
				</div>
			</div>
		
		</div>
	</div>
<?php } ?>
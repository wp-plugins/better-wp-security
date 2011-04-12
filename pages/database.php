<?php
	global $wpdb;
	
	if (isset($_POST['BWPS_database_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_database_save')) {
			die('Security error!');
		}
		
		 $checkPreExists = $wpdb->prefix;
		
		while ($checkPreExists) {
			$prelength = rand(3,5);
			$newPrefix = substr(md5(rand()), rand(0, (32 - $prelength)), $prelength) . '_';
			$checkPreExists = $wpdb->get_results('SHOW TABLES LIKE "' . $newPrefix . '%";', ARRAY_N);
		}
		
		$tables = $wpdb->get_results('SHOW TABLES LIKE "' . $wpdb->prefix . '%"', ARRAY_N);
					
		if ($tables) {
			$tablesCopied = array();
						
			foreach ($tables as $table) {
				$table = substr($table[0], strlen($wpdb->prefix), strlen($table[0]));
							
				$sql = 'CREATE TABLE `' . $newPrefix . $table . '`LIKE `' . $wpdb->prefix . $table . '`;';
					
				$createTable = $wpdb->query($sql);
			
				if ($createTable === false) {
					if (!$errorHandler) {
						$errorHandler = new WP_Error();
					}
			
					$errorHandler->add("2", __("Could not create table.", 'better-wp-security'));
				} else {
					
					$sql = 'INSERT INTO `' . $newPrefix . $table . '` SELECT * FROM `' . $wpdb->prefix . $table . '`;';
								
					$popTable = $wpdb->query($sql);
								
					if ($popTable === false) {
						if (!$errorHandler) {
							$errorHandler = new WP_Error();
						}
			
						$errorHandler->add("2", __("Could not copy table.", 'better-wp-security'));
					} else {
							$tablesCopied[] = $table;
					}
				}
			}
				
			if (count($tablesCopied) == count($tables)) {
				$sql = 'UPDATE `' . $newPrefix . 'options` SET `option_name` = "' . $newPrefix . 'user_roles" WHERE `option_name` = "' . $wpdb->prefix . 'user_roles" LIMIT 1;';
							
				$upOpts = $wpdb->query($sql);
							
				if ($upOpts === FALSE) {
					if (!$errorHandler) {
						$errorHandler = new WP_Error();
					}
			
					$errorHandler->add("2", __("Could not update prefix refences in options table.", 'better-wp-security'));
				} else {
					$fields = array(
						'user_level',
						'capabilities',
						'autosave_draft_ids'
					);

					foreach ($fields as $field) {
						$sql = 'UPDATE `' . $newPrefix . 'usermeta` SET `meta_key` = "' . $newPrefix . 'capabilities" WHERE `meta_key` = "' . $wpdb->prefix . 'capabilities" LIMIT 1;';

						$upMeta = $wpdb->query($sql);

						if ($upMetar === FALSE) {
							if (!$errorHandler) {
								$errorHandler = new WP_Error();
							}
			
							$errorHandler->add("2", __("Could not update prefix refences in usermeta table.", 'better-wp-security'));
						}
					}
				}
			}
		}
		
		$tables = $wpdb->get_results('SHOW TABLES LIKE "' . $wpdb->prefix . '%"', ARRAY_N);
					
		if ($tables) {
			$tablesDropped = array();
						
			foreach ($tables as $table) {
				$table = $table[0];
							
				$dropTable = $wpdb->query('DROP TABLE `' . $table . '`;');
							
				if ($dropTabler === FALSE) {
					if (!$errorHandler) {
						$errorHandler = new WP_Error();
					}
					$errorHandler->add("2", __("Could not drop table.", 'better-wp-security'));
				} else {
					$tablesDropped[] = $table;
				}
			}
		}
		
		$conf_f = trailingslashit(ABSPATH).'/wp-config.php';

		chmod($conf_f, 0755);
		$handle = @fopen($conf_f, "r+");
		if ($handle) {
			while (!feof($handle)) {
				$lines[] = fgets($handle, 4096);
			}
			fclose($handle);
			$handle = @fopen($conf_f, "w+");
			foreach ($lines as $line) {
				if (strpos($line, $wpdb->prefix)) {
					$line = str_replace($wpdb->prefix, $newPrefix, $line);
				}
				fwrite($handle, $line);
			}
			fclose($handle);
		}
        	
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>' . __('Database Prefix Changed.', 'better-wp-security') . '</p></div>';
		}
	}
	
	function checkTablePre(){
		global $wpdb;
		
		if ($wpdb->prefix == 'wp_') {
			return true;
		}else{
			echo false;
		}
	}
		
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('Database Prefix', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<?php 
			if ((checkTablePre() && !isset($_POST['BWPS_database_save'])) || (!checkTablePre() && isset($_POST['BWPS_database_save']) && isset($errorHandler))) {
				$bgcolor = "#ffebeb";
			} else {
				$bgcolor = "#fff";
			}
		?>
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened" style="background-color: <?php echo $bgcolor; ?>;">
				<h3><?php _e('Rename Admin User', 'better-wp-security'); ?></h3>	
				<div class="inside">
				<p><?php _e('Use the form below to change the table prefix for your Wordpress Database.', 'better-wp-security'); ?></p>
				<p style="text-align: center; font-size: 130%; font-weight: bold; color: blue;"><?php _e('WARNING: BACKUP YOUR DATABASE BEFORE USING THIS TOOL!', 'better-wp-security'); ?></p>
					<?php if ((checkTablePre() && !isset($_POST['BWPS_database_save'])) || (!checkTablePre() && isset($_POST['BWPS_database_save']) && isset($errorHandler))) { ?>
						<p><strong><?php _e('Your database is using the default table prefix', 'better-wp-security'); ?> <em>wp_</em>. <?php _e('You should change this.', 'better-wp-security'); ?></strong></p>
					<?php } else { ?>
						<?php 
							if (isset($_POST['BWPS_database_save']) && !isset($errorHandler)) {
								$pre = $newPrefix;
							} else {
								$pre = $wpdb->prefix;
							}
						?>
						<p><?php _e('Your current database table prefix is', 'better-wp-security'); ?> <strong><em><?php echo $pre; ?></em></strong></p>
					<?php } ?>
					<form method="post">
						<?php wp_nonce_field('BWPS_database_save','wp_nonce') ?>
						<p><?php _e('Press the button below to generate a random database prefix value and update all of your tables accordingly.', 'better-wp-security'); ?></p>
						<p class="submit"><input type="submit" name="BWPS_database_save" value="<?php _e('Change Database Table Prefix', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
	</div>
</div>
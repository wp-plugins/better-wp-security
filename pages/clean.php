<?php
	global $wpdb, $BWPS;
	
	if (isset($_POST['BWPS_clean_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_clean_save')) {
			die('Security error!');
		}
		
		if ($_POST['BWPS_remove404'] == 1) {
			$wpdb->query("DELETE FROM " . BWPS_TABLE_D404 . " WHERE attempt_date < '" . (time() - 300) . "';");
		}
		
		if ($_POST['BWPS_removeLogin'] == 1) {
			$wpdb->query("DELETE FROM " . BWPS_TABLE_LL . " WHERE attempt_date < '" . (time() - 1800) . "';");
		}
		
		if ($_POST['BWPS_removeLockouts'] == 1) {
			$opts = $BWPS->getOptions();
			$wpdb->query("DELETE FROM " . BWPS_TABLE_LOCKOUTS . " WHERE lockout_date < '" . (time() - ($opts['ll_checkinterval'] * 60)) . "';");
			unset($opts);
		}
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>Old Data Has Been Cleared From The Database.</p></div>';
		}
	}
	
	/**
	 * Count how many 404s are in the database
	 * @return integer
	 * @param NULL
	 */
	function d404_count() {
		global $wpdb;		
		$count = $wpdb->get_var("SELECT COUNT(attempt_ID) FROM " . BWPS_TABLE_D404 . " WHERE attempt_date < '" . (time() - 300). "';");
		echo $count;
	}
	
	/**
	 * Count how many 404s are in the database
	 * @return integer
	 * @param NULL
	 */
	function old_lockouts() {
		global $wpdb;		
		$count = $wpdb->get_var("SELECT COUNT(lockout_id) FROM " . BWPS_TABLE_LOCKOUTS . " WHERE lockout_date < " . (time() - 1800) . ";");
		echo $count;
	}
	
	/**
	 * Count how many 404s are in the database
	 * @return integer
	 * @param NULL
	 */
	function logins_count() {
		global $wpdb, $BWPS;
		$opts = $BWPS->getOptions();
		$count = $wpdb->get_var("SELECT COUNT(attempt_ID) FROM " . BWPS_TABLE_LL . " WHERE attempt_date < '" . (time() - ($opts['ll_checkinterval'] * 60)) . "';");
		unset($opts);
		echo $count;
	}
		
?>

<div class="wrap" >

	<h2>Better WP Security - Clean Database</h2>
	
	<div id="poststuff" class="ui-sortable">
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Old Data</h3>
				<div class="inside">
					<p>Below is old security data still in your Wordpress database. Data is considered old when the lockout has expired or the attempt will no longer be used to generate a lockout.</p>
					<p>This data is not automatically deleted so that it may be used for analysis. You may delete this data with the form below. To see the actual data you will need to access your database directly.</p>
					<form method="post">
						<?php wp_nonce_field('BWPS_clean_save','wp_nonce') ?>
						<p>Check the box next to the data you would like to clear and then press the "Remove Old Data" button.</p>
						<ul>
							<li> <input type="checkbox" name="BWPS_remove404" id="BWPS_remove404" value="1" /> <label for="BWPS_remove404">Your database contains <strong><?php d404_count(); ?> 404 (page not found) errors.</label></strong></li>
							<li> <input type="checkbox" name="BWPS_removeLogin" id="BWPS_removeLogin" value="1" /> <label for="BWPS_removeLogin">Your database contains <strong><?php logins_count(); ?> bad login attempts.</label></strong></li>
							<li> <input type="checkbox" name="BWPS_removeLockouts" id="BWPS_removeLockouts" value="1" /> <label for="BWPS_removeLockouts">Your database contains <strong><?php old_lockouts(); ?> old lockouts.</label></strong></li>
						</ul>
					
						<p class="submit"><input type="submit" name="BWPS_clean_save" value="<?php _e('Remove Old Data', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
	</div>
</div>
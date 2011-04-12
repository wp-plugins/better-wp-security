<?php
	global $wpdb;
	
	if (isset($_POST['BWPS_admin_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_admin_save')) {
			die('Security error!');
		}
		
		$newuser = $wpdb->escape($_POST['newuser']);
		
		if(validate_username($newuser)) {
			if (checkAdminUser($newuser)) {
				$errorHandler = new WP_Error();
			
				$errorHandler->add("2", $newuser . __(" already exists. Please try again", 'better-wp-security'));
			} else {
				$wpdb->query("UPDATE " . $wpdb->users . " SET user_login = '" . $newuser . "' WHERE user_login='admin'");
			}
		} else {
			if (!$errorHandler) {
				$errorHandler = new WP_Error();
			}
			
			$errorHandler->add("2", $newuser . __(" is not a valid username. Please try again", 'better-wp-security'));
		}
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p><em>admin</em> ' . __('username changed', 'better-wp-security') . '.</p></div>';
		}
	}
	
	function checkAdminUser($theuser) {
		global $wpdb;

		$adminUser = $wpdb->get_var("SELECT user_login FROM " . $wpdb->users . " WHERE user_login='" . $theuser . "'");
		
		if ($adminUser == $theuser) {
			return true;
		} else {
			return false;
		}
	}
		
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('Admin User', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">

		<?php 
			if (checkAdminUser("admin")) {
				$bgcolor = "#ffebeb";
			} else {
				$bgcolor = "#fff";
			}
		?>		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened" style="background-color: <?php echo $bgcolor; ?>;">
				<h3><?php _e('Rename Admin User', 'better-wp-security'); ?></h3>	
				<div class="inside">
					<?php if (checkAdminUser("admin")) { ?>
						<p><?php _e('Select a new name to use instead of <em>admin</em>', 'better-wp-security'); ?>.</p>
						<form method="post">
							<?php wp_nonce_field('BWPS_admin_save','wp_nonce') ?>
							<label for="newuser"><?php _e('Username:'); ?> </label> <input id="newuser" name="newuser" type="text">
							<p class="submit"><input type="submit" name="BWPS_admin_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
						</form>
					<?php } else { ?>
						<p>
							<?php _e('Congratulations, the <em>admin</em> user has already been removed. You do not need to take further action on this page.', 'better-wp-security'); ?>
						</p>
					<?php } ?>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
	</div>
</div>
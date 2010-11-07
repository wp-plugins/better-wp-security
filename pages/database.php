<?php
	global $wpdb;
	
	if (isset($_POST['BWPS_database_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_database_save')) {
			die('Security error!');
		}
		
		$prefix = $_POST['prefix'];
		
		if(validate_username($newuser)) {
			
		} else {
			$errorHandler = new WP_Error();
			
			$errorHandler->add("2", __($newuser . " is not a valid username. Please try again"));
		}
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>Database Prefix Changed.</p></div>';
		}
	}
	
	function checkTablePre(){
		global $table_prefix;

		if ($table_prefix == 'wp_') {
			return true;
		}else{
			echo false;
		}
	}
		
?>

<div class="wrap" >

	<h2>Better WP Security - Database Prefix</h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<?php 
			if (checkTablePre()) {
				$bgcolor = "#ffebeb";
			} else {
				$bgcolor = "#fff";
			}
		?>
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened" style="background-color: <?php echo $bgcolor; ?>;">
				<h3>Rename Admin User</h3>	
				<div class="inside">
				<p>Use the form below to change the table prefix for your Wordpress Database.</p>
				<p STYLE="text-align: center; font-size: 150%; font-weight: bold;">WARNING: BACKUP YOUR DATABASE BEFORE USING THIS TOOL!</p>
					<?php if (checkTablePre()) { ?>
						<p><strong>Your database is using the default table prefix <em>wp_</em>. You should change this.</strong></p>
					<?php } ?>
					<form method="post">
						<?php wp_nonce_field('BWPS_database_save','wp_nonce') ?>
						<label for="prefix">Table Prefix: </label> <input id="prefix" name="prefix" type="text">
						<p class="submit"><input type="submit" name="BWPS_database_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
	</div>
</div>
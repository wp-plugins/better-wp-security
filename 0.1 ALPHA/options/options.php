<?php
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/hideadmin.php');
	include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/functions/banips.php');
	
	if (isset($_POST['save'])) {
		if (!wp_verify_nonce($_POST['wp_nonce'], 'save')) {
			die('Security error!');
		}	
		
		update_option("BWPS_removeGenerator", $_POST['BWPS_removeGenerator']);
		update_option("BWPS_removeLoginMessages", $_POST['BWPS_removeLoginMessages']);
		
		update_option("BWPS_hideadmin_enable", $_POST['BWPS_hideadmin_enable']);
		update_option("BWPS_hideadmin_login_slug", $_POST['BWPS_hideadmin_login_slug']);
		update_option("BWPS_hideadmin_login_redirect", $_POST['BWPS_hideadmin_login_redirect']);
		update_option("BWPS_hideadmin_logout_slug", $_POST['BWPS_hideadmin_logout_slug']);
		update_option("BWPS_hideadmin_admin_slug", $_POST['BWPS_hideadmin_admin_slug']);
		update_option("BWPS_hideadmin_login_custom", $_POST['BWPS_hideadmin_login_custom']);
		update_option("BWPS_hideadmin_register_slug", $_POST['BWPS_hideadmin_register_slug']);
		
		$ipSize = strlen($_POST['BWPS_banips_iplist']);
		if ($ipSize > 0) {
			$ipArray = explode("\n",  $_POST['BWPS_banips_iplist']);
		
			if (!$htString = CreateBanList($ipArray)) {
				echo "You entered a bad IP address";
			} else {
				update_option("BWPS_banips_iplist", $_POST['BWPS_banips_iplist']);
			}
		} else {
			delete_option("BWPS_banips_iplist");
		}
		
		update_option("BWPS_banips_enable", $_POST['BWPS_banips_enable']);
		
		$htaccess = trailingslashit(ABSPATH).'.htaccess';

		if ($_POST['BWPS_hideadmin_enable'] == 1) {
		
			if (!is_writeable_ACLSafe($htaccess)) {
				echo "Unable to update htaccess rules";
			} else {
				$wprules = implode("\n", extract_from_markers($htaccess, 'WordPress' ));
				wpsc_remove_marker($htaccess, 'WordPress');
				wpsc_remove_marker($htaccess, 'Better WP Security Hide Admin');
				insert_with_markers($htaccess,'Better WP Security Hide Admin', explode( "\n", CreateRewriteRules()));
				insert_with_markers($htaccess,'WordPress', explode( "\n", $wprules));
			}		
			
		} else {
			
			if (!is_writeable_ACLSafe($htaccess)) {
				echo "Unable to update htaccess rules";
			} else {
				wpsc_remove_marker($htaccess, 'Better WP Security Hide Admin');
			}	
			
		}	
		
		if ($_POST['BWPS_banips_enable'] == 1 && get_option("BWPS_banips_iplist")) {
		
			if (strlen($htaccess) > 0) {
				wpsc_remove_marker($htaccess, 'Better WP Security Ban IPs');
				insert_with_markers($htaccess,'Better WP Security Ban IPs', explode( "\n", $htaccess));
			}

		} else {
			
			if (!is_writeable_ACLSafe($htaccess)) {
				echo "Unable to update htaccess rules";
			} else {
				wpsc_remove_marker($htaccess, 'Better WP Security Ban IPs');
			}	
		}
			
		echo '<div id="message" class="updated"><p>Settings Saved</p></div>';

	}
	
	if (get_option('BWPS_hideadmin_enable')== 1) {
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		$ruleCheck = implode("\n", extract_from_markers($htaccess, 'Better WP Security Hide Admin' ));
		if (strlen($ruleCheck) < 1) {
			echo '<div id="message" class="error"><p>Your htaccess settings appear to be missing. Please save your settings below to reset them.</p></div>';
		}
	}	
?>

<div class="wrap" >

	<h2>Better WP Security Options</h2>
	
	<div id="poststuff" class="ui-sortable">
		<div class="postbox-container" style="width:60%">	
			<div class="postbox opened">
				<h3>Instructions and Support</h3>
				<div class="inside">
					<h4>Instuctions</h4>
					<p>
						&bull;&nbsp;Select the features below that you would like to implement<br />
						&bull;&nbsp;Press Save
					</p>
					<p>
						<em><strong>Note: </strong> You may need to update your bookmarks after setting options below. Make sure you have direct access to your .htaccess file should anything go wrong.</em>
					</p>
					<h4>Support</h4>
					<p>Please visit the <a href="http://www.chriswiegman.com/projects/wordpress/better-wp-security/">Better WP Security</a> homepage for support and change-log</p>
				</div>
			</div>
		</div>
		<div class="postbox-container" style="width:39%">
			<div class="postbox opened">
				<h3>Please Donate</h3>
				<div class="inside">
				<p>If you find this plugin useful please consider a small donation.</p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post"> 
					<input name="cmd" type="hidden" value="_donations" /> 
					<input name="business" type="hidden" value="ZLMVYQBK7WRRS" /> 
					<input name="lc" type="hidden" value="US" /> 
					<input name="item_name" type="hidden" value="Wordpress Better WP Security Plugin" /> 
					<input name="currency_code" type="hidden" value="USD" /> 
					<input name="bn" type="hidden" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted" /> 
					<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" type="image" /> <img src="https://www.paypal.com/en_US/i/scr/pixel.gif" border="0" alt="" width="1" height="1" /><br /> 
				</form> 
			</div>
		</div>
	</div>
	<div class="clear"></div>
		<form method="post">
			<?php wp_nonce_field('save','wp_nonce') ?>
			<div class="postbox-container" style="width:100%">
				<div class="postbox opened">
					<h3>General Options</h3>
					<div class="inside">
						<?php include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/general.php'); ?>
						<p class="submit"><input type="submit" name="save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="postbox-container" style="width:100%">
				<div class="postbox opened">
					<h3>Hide Backend Options</h3>	
					<div class="inside">
						<?php include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/hideadmin.php'); ?>
						<p class="submit"><input type="submit" name="save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="postbox-container" style="width:100%">
				<div class="postbox opened">
					<h3>Ban Troublesome IP Addresses</h3>	
					<div class="inside">
						<?php include(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/banips.php'); ?>
						<p class="submit"><input type="submit" name="save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
	global $BWPS;
	
	$opts = $BWPS->getOptions();
	
	if (isset($_POST['BWPS_hidebe_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_hidebe_save')) {
			die('Security error!');
		}	
		
		$htaccess = trailingslashit(ABSPATH).'.htaccess';
		
		if (!$BWPS->can_write($htaccess)) {
			
			$opts = $BWPS->saveOptions("hidebe_enable","0");
			
			if (!$errorHandler) {
				$errorHandler = new WP_Error();
			}
			
			$errorHandler->add("2", __("Unable to update htaccess rules", 'better-wp-security'));
			
		} else {
		
			$login_slug = sanitize_title(esc_html__($_POST['BWPS_hidebe_login_slug']));
			$admin_slug = sanitize_title(esc_html__($_POST['BWPS_hidebe_admin_slug']));
			$register_slug = sanitize_title(esc_html__($_POST['BWPS_hidebe_register_slug']));
		
			$opts = $BWPS->saveOptions("hidebe_enable",$_POST['BWPS_hidebe_enable']);
			$opts = $BWPS->saveOptions("hidebe_login_slug", $login_slug);
			$opts = $BWPS->saveOptions("hidebe_admin_slug", $admin_slug);
			$opts = $BWPS->saveOptions("hidebe_register_slug", $register_slug);
		
			$BWPS->createhtaccess();	
			
		}
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			$BWPS->saveVersions('HIDEBE', BWPS_VERSION_HIDEBE);
			echo '<div id="message" class="updated"><p>' . __('Settings Saved', 'better-wp-security') . '</p></div>';
		}
		
	}
?>

<div class="wrap" >

	<h2>Better WP Security - <?php _e('Hide Backend Options', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Hide Backend Options', 'better-wp-security'); ?></h3>	
				<div class="inside">
					<p><?php _e('The options below allow you to "hide" the backed of Wordpress replacing known URLs of important areas with ones of your choosing. This is useful if slowing bots and keeping away other nosy users especially on closed or private systems.', 'better-wp-security'); ?></p>
					<form method="post">
						<?php wp_nonce_field('BWPS_hidebe_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_hidebe_enable"><?php _e('Enable Hide Backend', 'better-wp-security'); ?></label>
									</th>
									<td>
										<label><input name="BWPS_hidebe_enable" id="BWPS_hidebe_enable" value="1" <?php if ($opts['hidebe_enable'] == 1) echo 'checked="checked"'; ?> type="radio" /> <?php _e('On', 'better-wp-security'); ?></label>
										<label><input name="BWPS_hidebe_enable" value="0" <?php if ($opts['hidebe_enable'] == 0) echo 'checked="checked"'; ?> type="radio" /> <?php _e('Off', 'better-wp-security'); ?></label>
									</td>
								</tr>
     
								<tr valign="top">
									<th scope="row">
										<label for="login_slug"><?php _e('Login Slug', 'better-wp-security'); ?></label>
									</th>
									<td>
										<input name="BWPS_hidebe_login_slug" id="login_slug" value="<?php echo $opts['hidebe_login_slug']; ?>" type="text"><br />
										<em><span style="color: #666666;"><strong><?php _e('Login URL:', 'better-wp-security'); ?></strong> <?php echo trailingslashit( get_option('siteurl') ); ?></span><span style="color: #4AA02C"><?php echo $opts['hidebe_login_slug']; ?>	</span></em>
									</td>
								</tr>
		                            	
								<tr valign="top">
									<th scope="row">
										<label for="register_slug"><?php _e('Register Slug', 'better-wp-security'); ?></label>
									</th>
									<td>
										<input type="text" name="BWPS_hidebe_register_slug" id="register_slug" value="<?php echo $opts['hidebe_register_slug']; ?>" /><br />
										<em><span style="color: #666666;"><strong><?php _e('Register URL:', 'better-wp-security'); ?></strong> <?php echo trailingslashit( get_option('siteurl') ); ?></span><span style="color: #4AA02C"><?php echo $opts['hidebe_register_slug']; ?></span></em>
									</td>
								</tr>
	
								<tr valign="top">
									<th scope="row">
										<label for="admin_slug"><?php _e('Admin Slug', 'better-wp-security'); ?></label>
									</th>
									<td>
										<input name="BWPS_hidebe_admin_slug" id="admin_slug" value="<?php echo $opts['hidebe_admin_slug']; ?>" type="text"><br />
										<em><span style="color: #666666;"><strong><?php _e('Admin URL:', 'better-wp-security'); ?></strong> <?php echo trailingslashit( get_option('siteurl') ); ?></span><span style="color: #4AA02C"><?php echo $opts['hidebe_admin_slug']; ?></span></em>
									</td>
								</tr>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_hidebe_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		<?php if ($opts['hidebe_enable'] == 1) { ?>
			<div class="clear"></div>
		
			<div class="postbox-container" style="width:70%">
				<div class="postbox opened">
					<h3><?php _e('Current .htaccess', 'better-wp-security'); ?></h3>	
					<div class="inside">
						<p><?php _e('Here are the current contents of your .htaccess file.', 'better-wp-security'); ?></p>
						<?php $BWPS->htaccess_showContents(); ?>
					</div>
				</div>
			</div>
		<?php } ?>
		
	</div>
</div>
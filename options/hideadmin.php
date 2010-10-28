<p>The options below allow you to "hide" the backed of Wordpress replacing known URLs of important areas with ones of your choosing. This is useful if slowing bots and keeping away other nosy users especially on closed or private systems.</p>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_hideadmin_enable">Enable Hide Backend</label>
			</th>
			<td>
				<label><input name="BWPS_hideadmin_enable" id="BWPS_hideadmin_enable" value="1" <?php if ($opts['hideadmin_enable'] == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
				<label><input name="BWPS_hideadmin_enable" value="0" <?php if ($opts['hideadmin_enable'] == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
			</td>
		</tr>
     
		<tr valign="top">
			<th scope="row">
				<label for="login_slug">Login Slug</label>
			</th>
			<td>
				<input name="BWPS_hideadmin_login_slug" id="login_slug" value="<?php echo $opts['hideadmin_login_slug']; ?>" type="text"><br />
				<em><span style="color: #666666;"><strong>Login URL:</strong> <?php echo trailingslashit( get_option('siteurl') ); ?></span><span style="color: #4AA02C"><?php echo $opts['hideadmin_login_slug']; ?>	</span></em>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row">
				<label for="login_redirect">Login Redirect</label>
			</th>
			<td>
				<select name="BWPS_hideadmin_login_redirect" id="login_redirect">
					<option value="<?php echo get_option('siteurl'); ?>/wp-admin/" <?php if ($opts['hideadmin_login_redirect'] == get_option('siteurl').'/wp-admin/') echo 'selected="selected"'; ?>">WordPress Admin</option>
					<option value="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo get_option('siteurl');?>" <?php if ($opts['hideadmin_login_redirect'] == get_option('siteurl').'/wp-login.php?redirect_to='.get_option('siteurl')) echo 'selected="selected"'; ?>">WordPress Address</option>
					<option value="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo get_option('home');?>" <?php if ($opts['hideadmin_login_redirect'] == get_option('siteurl').'/wp-login.php?redirect_to='.get_option('home')) echo 'selected="selected"'; ?>">Blog Address</option>
					<option value="Custom" <?php if ($opts['hideadmin_login_redirect'] == "Custom") echo 'selected="selected"'; ?>">Custom URL (Enter Below)</option>
				</select><br />
				<input type="text" name="login_custom" size="40" value="<?php echo $opts['hideadmin_login_custom']; ?>" /><br />
				<em><span style="color: #666666;"><strong>Redirect URL:</strong> </span><span style="color: #4AA02C"><?php echo $opts['hideadmin_login_redirect']; ?></span></em>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="logout_slug">Logout Slug</label>
			</th>
			<td>
				<input type="text" name="BWPS_hideadmin_logout_slug" id="logout_slug" value="<?php echo $opts['hideadmin_logout_slug']; ?>" /><br />
				<em><span style="color: #666666;"><strong>Logout URL:</strong> <?php echo trailingslashit( get_option('siteurl') ); ?></span><span style="color: #4AA02C"><?php echo $opts['hideadmin_logout_slug']; ?></span></em>
			</td>
		</tr>
		                            	
		<?php if (get_option('users_can_register')) { ?>
			<tr valign="top">
				<th scope="row">
					<label for="register_slug">Register Slug</label>
				</th>
				<td>
					<input type="text" name="BWPS_hideadmin_register_slug" id="register_slug" value="<?php echo $opts['hideadmin_register_slug']; ?>" /><br />
					<em><span style="color: #666666;"><strong>Register URL:</strong> <?php echo trailingslashit( get_option('siteurl') ); ?></span><span style="color: #4AA02C"><?php echo $opts['hideadmin_register_slug']; ?></span></em>
				</td>
			</tr>
		<?php } else { ?>
			<input type="hidden" name="BWPS_hideadmin_register_slug" id="register_slug" value="<?php echo $opts['hideadmin_register_slug']; ?>" />
		<?php } ?>

		<tr valign="top">
			<th scope="row">
				<label for="admin_slug">Admin Slug</label>
			</th>
			<td>
				<input name="BWPS_hideadmin_admin_slug" id="admin_slug" value="<?php echo $opts['hideadmin_admin_slug']; ?>" type="text"><br />
				<em><span style="color: #666666;"><strong>Admin URL:</strong> <?php echo trailingslashit( get_option('siteurl') ); ?></span><span style="color: #4AA02C"><?php echo $opts['hideadmin_admin_slug']; ?></span></em>
			</td>
		</tr>
	</tbody>
</table>	
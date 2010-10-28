<p>Set options below to limit the number of bad login attempts. Once this limit is reached, the host or computer attempting to login will be banned from the site for the specified "lockout length" period.</p>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limitlogin_enable">Enable Limit Bad Login Attempts</label>
			</th>
			<td>
				<label><input name="BWPS_limitlogin_enable" id="BWPS_limitlogin_enable" value="1" <?php if ($opts['limitlogin_enable'] == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
				<label><input name="BWPS_limitlogin_enable" value="0" <?php if ($opts['limitlogin_enable'] == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
			</td>
		</tr>
     
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limitlogin_maxattemptshost">Max Login Attempts Per Host</label>
			</th>
			<td>
				<input name="BWPS_limitlogin_maxattemptshost" id="BWPS_limitlogin_maxattemptshost" value="<?php echo $opts['limitlogin_maxattemptshost']; ?>" type="text">
				<p>
					The number of login attempts a user has before their host or computer is locked out of the system.
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limitlogin_maxattemptsuser">Max Login Attempts Per User</label>
			</th>
			<td>
				<input name="BWPS_limitlogin_maxattemptsuser" id="BWPS_limitlogin_maxattemptsuser" value="<?php echo $opts['limitlogin_maxattemptsuser']; ?>" type="text">
				<p>
					The number of login attempts a user has before their username is locked out of the system.
				</p>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limitlogin_checkinterval">Login Time Period (minutes)</label>
			</th>
			<td>
				<input name="BWPS_limitlogin_checkinterval" id="BWPS_limitlogin_checkinterval" value="<?php echo $opts['limitlogin_checkinterval']; ?>" type="text"><br />
				<p>
					The number of minutes in which bad logins should be remembered.
				</p>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limitlogin_banperiod">Login Time Period (minutes)</label>
			</th>
			<td>
				<input name="BWPS_limitlogin_banperiod" id="BWPS_limitlogin_banperiod" value="<?php echo $opts['limitlogin_banperiod']; ?>" type="text"><br />
				<p>
					The length of time a host or computer will be banned from this site after hitting the limit of bad logins.
				</p>
			</td>
		</tr>
	</tbody>
</table>

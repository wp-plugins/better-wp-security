<p>Set options below to limit the number of bad login attempts. Once this limit is reached, the host or computer attempting to login will be banned from the site for the specified "lockout length" period.</p>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limilogin_enable">Enable Limit Bad Login Attemps</label>
			</th>
			<td>
				<label><input name="BWPS_limilogin_enable" id="BWPS_limilogin_enable" value="1" <?php if (get_option('BWPS_limilogin_enable') == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
				<label><input name="BWPS_limilogin_enable" value="0" <?php if (get_option('BWPS_limilogin_enable') == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
			</td>
		</tr>
     
		<?php
			if (!get_option('BWPS_limilogin_maxattemptshost')) {
				$$maxattemptshost = "5";
			} else {
				$maxattemptshost = get_option('BWPS_limilogin_maxattemptshost');
			}
		?>
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limilogin_maxattemptshost">Max Login Attempts Per Host</label>
			</th>
			<td>
				<input name="BWPS_limilogin_maxattemptshost" id="BWPS_limilogin_maxattemptshost" value="<?php echo $maxattemptshost; ?>" type="text">
				<p>
					The number of login attempts a user has before their host or computer is locked out of the system.
				</p>
			</td>
		</tr>
		
		<?php
			if (!get_option('BWPS_limitlogin_maxattemptsuser')) {
				$maxattemptsuser = "10";
			} else {
				$maxattemptsuser = get_option('BWPS_limitlogin_maxattemptsuser');
			}
		?>
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limitlogin_maxattemptsuser">Max Login Attempts Per User</label>
			</th>
			<td>
				<input name="BWPS_limitlogin_maxattemptsuser" id="BWPS_limitlogin_maxattemptsuser" value="<?php echo $maxattemptsuser; ?>" type="text">
				<p>
					The number of login attempts a user has before their username is locked out of the system.
				</p>
			</td>
		</tr>
		
		<?php
			if (!get_option('BWPS_limilogin_checkinterval')) {
				$checkinterval = "5";
			} else {
				$checkinterval = get_option('BWPS_limilogin_checkinterval');
			}
		?>
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limilogin_checkinterval">Login Time Period (minutes)</label>
			</th>
			<td>
				<input name="BWPS_limilogin_checkinterval" id="BWPS_limilogin_checkinterval" value="<?php echo $checkinterval; ?>" type="text"><br />
				<p>
					The number of minutes in which bad logins should be remembered.
				</p>
			</td>
		</tr>
		
		<?php
			if (!get_option('BWPS_limilogin_banperiod')) {
				$banperiod = "60";
			} else {
				$banperiod = get_option('BWPS_limilogin_banperiod');
			}
		?>
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_limilogin_banperiod">Login Time Period (minutes)</label>
			</th>
			<td>
				<input name="BWPS_limilogin_banperiod" id="BWPS_limilogin_banperiod" value="<?php echo $banperiod; ?>" type="text"><br />
				<p>
					The length of time a host or computer will be banned from this site after hitting the limit of bad logins.
				</p>
			</td>
		</tr>
	</tbody>
</table>

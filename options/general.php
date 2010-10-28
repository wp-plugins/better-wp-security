<p>
	<input type="checkbox" name="BWPS_removeGenerator" id="BWPS_removeGenerator" value="1" <?php if ($opts['general_removeGenerator'] == 1) echo "checked"; ?> /> <label for="BWPS_removeGenerator"><strong>Remove Wordpress Generator Meta Tag</strong></label><br />
	Removes the <em>&lt;meta name="generator" content="WordPress [version]" /&gt;</em> meta tag from your sites header. This process hides version information from a potential attacker making it more difficult to determine vulnerabilities.
</p>
<p>
	<input type="checkbox" name="BWPS_removeLoginMessages" id="BWPS_removeLoginMessages" value="1" <?php if ($opts['general_removeLoginMessages'] == 1) echo "checked"; ?> /> <label for="BWPS_removeLoginMessages"><strong>Remove Wordpress Login Error Messages</strong></label><br />
	Prevents error messages from being displayed to a user upon a failed login attempt.
</p>
<p>
	<input type="checkbox" name="BWPS_randomVersion" id="BWPS_randomVersion" value="1" <?php if ($opts['general_randomVersion'] == 1) echo "checked"; ?> /> <label for="BWPS_randomVersion"><strong>Display random version number to all non-administrative users</strong></label><br />
	Displays a random version number to non-administrator users in all places where version number must be used.
</p>
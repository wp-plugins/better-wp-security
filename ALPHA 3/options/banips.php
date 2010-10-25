<p>List below the IP addresses you would like to ban from your site. These will be banned in .htaccess.</p>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_banips_enable">Enable Ban IPs</label>
			</th>
			<td>
				<label><input name="BWPS_banips_enable" id="BWPS_banips_enable" value="1" <?php if (get_option('BWPS_banips_enable') == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
				<label><input name="BWPS_banips_enable" value="0" <?php if (get_option('BWPS_banips_enable') == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="BWPS_banips_iplist">Enable Ban IPs</label>
			</th>
			<td>
				<?php if(!get_option("BWPS_banips_iplist")) {
					$ipList = $_POST['BWPS_banips_iplist'];
				} else {
					$ipList = get_option("BWPS_banips_iplist");
				}
				
				?>
				<textarea name="BWPS_banips_iplist" id="BWPS_banips_iplist"><?php echo $ipList; ?></textarea><br />
				<p><em>IP addesses must be in IPV4 standard format (i.e. ###.###.###.###).<br />
				<a href="http://ip-lookup.net/domain-lookup.php" target="_blank">Lookup IP Address.</a><br />
				Enter only 1 IP address per line.<br />
				You may NOT ban your own IP address</em></p>
			</td>
		</tr>
	</tbody>
</table>
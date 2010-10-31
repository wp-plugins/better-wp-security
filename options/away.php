<?php
	global $wpdb, $opts, $BWPS, $versions;
	
	if (isset($_POST['BWPS_away_save'])) { // Save options
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_away_save')) { //verify nonce field
			die('Security error!');
		}
		
		$BWPS->saveOptions("away_Version", $versions['away_Version']);
		
		if (checkdate($_POST['BWPS_away_startmonth'], $_POST['BWPS_away_startday'], $_POST['BWPS_away_startyear']) && checkdate($_POST['BWPS_away_endmonth'], $_POST['BWPS_away_endday'], $_POST['BWPS_away_endyear'])) {
		
			$startDate = $_POST['BWPS_away_startmonth'] . "/" . $_POST['BWPS_away_startday'] . "/" . $_POST['BWPS_away_startyear'];
			$endDate = $_POST['BWPS_away_endmonth'] . "/" . $_POST['BWPS_away_endday'] . "/" . $_POST['BWPS_away_endyear'];
		
			$startTime = $_POST['BWPS_away_starthour'] . ":" . $_POST['BWPS_away_startmin'] . " " . $_POST['BWPS_away_startsel'];
			$endTime = $_POST['BWPS_away_endhour'] . ":" . $_POST['BWPS_away_endmin'] . " " . $_POST['BWPS_away_endsel'];
		
			$BWPS->saveOptions("away_enable", $_POST['BWPS_away_enable']);
			$BWPS->saveOptions("away_mode", $_POST['BWPS_away_mode']);
			$BWPS->saveOptions("away_start", strtotime($startDate . " " . $startTime));
			$BWPS->saveOptions("away_end", strtotime($endDate . " " . $endTime));
		
			$opts = $BWPS->getOptions();
			
		} else {
			$errorHandler = new WP_Error();
			
			if (!checkdate($_POST['BWPS_away_startmonth'], $_POST['BWPS_away_startday'], $_POST['BWPS_away_startyear'])) {
				$errorHandler->add("1", __("<strong>You MUST enter a valid date for start time."));
			}
			
			if (!checkdate($_POST['BWPS_away_endmonth'], $_POST['BWPS_away_endday'], $_POST['BWPS_away_endyear'])) {
				$errorHandler->add("2", __("<strong>You MUST enter a valid date for end time."));
			}
		
		}
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			echo '<div id="message" class="updated"><p>Settings Saved</p></div>';
		}
		
		$aedisplay = $_POST['BWPS_away_enable'];
		$modisplay = $_POST['BWPS_away_mode'];
		$smdisplay = $_POST['BWPS_away_startmonth'];
		$sddisplay = $_POST['BWPS_away_startday'];
		$sydisplay = $_POST['BWPS_away_startyear'];
		$shdisplay = $_POST['BWPS_away_starthour'];
		$sidisplay = $_POST['BWPS_away_startmin'];
		$ssdisplay = $_POST['BWPS_away_startsel'];
		$emdisplay = $_POST['BWPS_away_endmonth'];
		$eddisplay = $_POST['BWPS_away_endday'];
		$eydisplay = $_POST['BWPS_away_endyear'];
		$ehdisplay = $_POST['BWPS_away_endhour'];
		$eidisplay = $_POST['BWPS_away_endmin'];
		$esdisplay = $_POST['BWPS_away_endsel'];
		
	} else {
	
		$aedisplay = $opts['away_enable'];
		$modisplay = $opts['away_mode'];
		$sTime = $opts['away_start'];
		$eTime = $opts['away_end'];
		$shdisplay = date('g',$sTime);
		$sidisplay = date('i',$sTime);
		$ssdisplay = date('a',$sTime);
		$ehdisplay = date('g',$eTime);
		$eidisplay = date('i',$eTime);
		$esdisplay = date('a',$eTime);
		
		if ($opts['away_enable'] == 1) {	
			$smdisplay = date('n',$sTime);
			$sddisplay = date('j',$sTime);
			$sydisplay = date('Y',$sTime);
			
			$emdisplay = date('n',$eTime);
			$eddisplay = date('j',$eTime);
			$eydisplay = date('Y',$eTime);
			
		} else {
			$sDate = strtotime(get_date_from_gmt(date('Y-m-d H:i:s',time() + 86400)));
			if ($eTime < (strtotime(get_date_from_gmt(date('Y-m-d H:i:s',time() - 86400))))) {
				$eDate = strtotime(get_date_from_gmt(date('Y-m-d H:i:s',time() + (86400 * 2))));	
			} else {
				$eDate = $eTime;
			}
			$smdisplay = date('n',$sDate);
			$sddisplay = date('j',$sDate);
			$sydisplay = date('Y',$sDate);
			
			$emdisplay = date('n',$eDate);
			$eddisplay = date('j',$eDate);
			$eydisplay = date('Y',$eDate);
		}
	}
	
?>

<div class="wrap" >

	<h2>Better WP Security - Away Options</h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Away Mode Options</h3>	
				<div class="inside">
					<p>As many of us update our sites on a general schedule it is not always necessary to permit site access all of the time. The options below will disable the backend of the site for the specified period.</p>
					<form method="post">
						<?php wp_nonce_field('BWPS_away_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_away_enable">Enable Away Mode</label>
									</th>
									<td>
										<label><input name="BWPS_away_enable" id="BWPS_away_enable" value="1" <?php if ($aedisplay == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
										<label><input name="BWPS_away_enable" value="0" <?php if ($aedisplay == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_away_enable">Type of Restriction</label>
									</th>
									<td>
										<label><input name="BWPS_away_mode" id="BWPS_away_mode" value="1" <?php if ($modisplay == 1) echo 'checked="checked"'; ?> type="radio" /> Daily</label>
										<label><input name="BWPS_away_mode" value="0" <?php if ($modisplay == 0) echo 'checked="checked"'; ?> type="radio" /> One Time</label>
										<p>
										Selecting <em>"One Time"</em> will lock out the backend of your site from the start date and time to the end date and time. Selecting <em>"Daily"</em> will ignore the start and and dates and will disable your site backend from the start time to the end time.
										</p>
									</td>
								</tr>
							
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_away_start">Start Date and Time</label>
									</th>
									<td>
										<select name="BWPS_away_startmonth" id="BWPS_away_start">
											<?php
												for ($i = 1; $i <= 12; $i++) {
													if ($smdisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $i . "'" . $selected . ">" . date("F", strtotime($i . "/1/" . date("Y",time()))) . "</option>";
												}
											?>
										</select> 
										<select name="BWPS_away_startday">
											<?php
												for ($i = 1; $i <= 31; $i++) {
													if ($sddisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $i . "'" . $selected . ">" . date("jS", strtotime("1/" . $i . "/" . date("Y",time()))) . "</option>";
												}
											?>
										</select>, 
										<select name="BWPS_away_startyear">
											<?php
												for ($i = date("Y",time()); $i < (date("Y",time()) + 2); $i++) {
													if ($sydisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $i . "'" . $selected . ">" . $i . "</option>";
												}
											?>
										</select> at 
										<select name="BWPS_away_starthour">
											<?php
												for ($i = 1; $i <= 12; $i++) {
													if ($shdisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $i . "'" . $selected . ">" . $i . "</option>";
												}
											?>
										</select> : 
										<select name="BWPS_away_startmin">
											<?php
												for ($i = 0; $i < 60; $i++) {
													if ($sidisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													if ($i < 10) {
														$val = "0" . $i;
													} else {
														$val = $i;
													}
													echo "<option value='" . $val . "'" . $selected . ">" . $val . "</option>";
												}
											?>
										</select> 
										<select name="BWPS_away_startsel">											
											<option value="am"<?php if ($ssdisplay == "am") echo " selected"; ?>>am</option>
											<option value="pm"<?php if ($ssdisplay == "pm") echo " selected"; ?>>pm</option>
										</select>
										<p>
										Select the date and time at which access to the backend of this site will be disabled. Note that if <em>"Daily"</em> mode is selected the date will be ignored and access will be banned each day at the specified time.
										</p>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="BWPS_away_end">End Date and Time</label>
									</th>
									<td>
										<select name="BWPS_away_endmonth" id="BWPS_away_end">
											<?php
												for ($i = 1; $i <= 12; $i++) {
													if ($emdisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $i . "'" . $selected . ">" . date("F", strtotime($i . "/1/" . date("Y",time()))) . "</option>";
												}
											?>
										</select> 
										<select name="BWPS_away_endday">
											<?php
												for ($i = 1; $i <= 31; $i++) {
													if ($eddisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $i . "'" . $selected . ">" . date("jS", strtotime("1/" . $i . "/" . date("Y",time()))) . "</option>";
												}
											?>
										</select>, 
										<select name="BWPS_away_endyear">
											<?php
												for ($i = date("Y",time()); $i < (date("Y",time()) + 2); $i++) {
													if ($eydisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $i . "'" . $selected . ">" . $i . "</option>";
												}
											?>
										</select> at 
										<select name="BWPS_away_endhour">
											<?php
												for ($i = 1; $i <= 12; $i++) {
													if ($ehdisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $i . "'" . $selected . ">" . $i . "</option>";
												}
											?>
										</select> : 
										<select name="BWPS_away_endmin">
											<?php
												for ($i = 0; $i < 60; $i++) {
													if ($eidisplay == $i) {
														$selected = " selected";
													} else {
														$selected = "";
													}
													if ($i < 10) {
														$val = "0" . $i;
													} else {
														$val = $i;
													}
													echo "<option value='" . $val . "'" . $selected . ">" . $val . "</option>";
												}
											?>
										</select> 
										<select name="BWPS_away_endsel">											
											<option value="am"<?php if ($esdisplay == "am") echo " selected"; ?>>am</option>
											<option value="pm"<?php if ($esdisplay == "pm") echo " selected"; ?>>pm</option>
										</select>
										<p>
										Select the date and time at which access to the backend of this site will be restored. Note that if <em>"Daily"</em> mode is selected the date will be ignored and access will be restored each day at the specified time.
										</p>
									</td>
								</tr>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="BWPS_away_save" value="<?php _e('save', 'better-wp-security'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
			
		<?php include_once(trailingslashit(ABSPATH) . 'wp-content/plugins/better-wp-security/options/donate.php'); ?>
		
		<?php if ($opts['away_enable'] == 1) { ?>
			<div class="clear"></div>
			<div class="postbox-container" style="width:70%">
				<div class="postbox opened" style="background-color: lightYellow;">
					<h3>Access time rules.</h3>	
					<div class="inside">
						<?php
							if ($opts['away_mode'] == 1) {
								$freq = " <strong><em>every day</em></strong>";
								$stime = "<strong><em>" . date('g:i a', $opts['away_start']) . "</em></strong>";
								$etime = "<strong><em>" . date('g:i a', $opts['away_end']) . "</em></strong>";
							} else {
								$freq = "";
								$stime = "<strong><em>" . date('l, F jS, Y \a\\t g:i a', $opts['away_start']) . "</em></strong>";
								$etime = "<strong><em>" . date('l, F jS, Y \a\\t g:i a', $opts['away_end']) . "</em></strong>";
							}
						?>
						<p style="font-size: 150%; text-align: center;">The backend (administrative section) of this site will be unavailable<?php echo $freq; ?> from <?php echo $stime; ?> until <?php echo $etime; ?>.</p>
						<p>Please note that according to your <a href="options-general.php">Wordpress timezone settings</a> your local time is <strong><em><?php echo date('l, F jS, Y \a\\t g:i a', $BWPS->getLocalTime()); ?></em></strong>. If this is incorrect please correct it on the <a href="options-general.php">Wordpress general settings page</a> by setting the appropriate time zone. Failure to do so may result in unintended lockouts.</p>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
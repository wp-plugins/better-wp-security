<?php
	global $BWPS;
	
	$opts = $BWPS->getOptions();
	
	if (isset($_POST['BWPS_away_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_away_save')) {
			die('Security error!');
		}
		
		if (checkdate($_POST['BWPS_away_startmonth'], $_POST['BWPS_away_startday'], $_POST['BWPS_away_startyear']) && checkdate($_POST['BWPS_away_endmonth'], $_POST['BWPS_away_endday'], $_POST['BWPS_away_endyear'])) {
		
			$startDate = $_POST['BWPS_away_startmonth'] . "/" . $_POST['BWPS_away_startday'] . "/" . $_POST['BWPS_away_startyear'];
			$endDate = $_POST['BWPS_away_endmonth'] . "/" . $_POST['BWPS_away_endday'] . "/" . $_POST['BWPS_away_endyear'];
		
			$startTime = $_POST['BWPS_away_starthour'] . ":" . $_POST['BWPS_away_startmin'] . " " . $_POST['BWPS_away_startsel'];
			$endTime = $_POST['BWPS_away_endhour'] . ":" . $_POST['BWPS_away_endmin'] . " " . $_POST['BWPS_away_endsel'];
		
			$opts = $BWPS->saveOptions("away_enable", $_POST['BWPS_away_enable']);
			$opts = $BWPS->saveOptions("away_mode", $_POST['BWPS_away_mode']);
			$opts = $BWPS->saveOptions("away_start", strtotime($startDate . " " . $startTime));
			$opts = $BWPS->saveOptions("away_end", strtotime($endDate . " " . $endTime));
			
		} else {
			if (!$errorHandler) {
				$errorHandler = new WP_Error();
			}
			
			if (!checkdate($_POST['BWPS_away_startmonth'], $_POST['BWPS_away_startday'], $_POST['BWPS_away_startyear'])) {
				$errorHandler->add("1", __("<strong>You MUST enter a valid date for start time.", 'better-wp-security'));
			}
			
			if (!checkdate($_POST['BWPS_away_endmonth'], $_POST['BWPS_away_endday'], $_POST['BWPS_away_endyear'])) {
				$errorHandler->add("2", __("<strong>You MUST enter a valid date for end time.", 'better-wp-security'));
			}
		
		}
		
		if (isset($errorHandler)) {
			echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
		} else {
			$BWPS->saveVersions('AWAY', BWPS_VERSION_AWAY);
			echo '<div id="message" class="updated"><p>' . __('Settings Saved', 'better-wp-security') . '</p></div>';
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

	<h2>Better WP Security - <?php _e('Away Options', 'better-wp-security'); ?></h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3><?php _e('Away Mode Options', 'better-wp-security'); ?></h3>	
				<div class="inside">
					<p><?php _e('As many of us update our sites on a general schedule it is not always necessary to permit site access all of the time. The options below will disable the backend of the site for the specified period.', 'better-wp-security'); ?></p>
					<form method="post">
						<?php wp_nonce_field('BWPS_away_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_away_enable"><?php _e('Enable Away Mode', 'better-wp-security'); ?></label>
									</th>
									<td>
										<label><input name="BWPS_away_enable" id="BWPS_away_enable" value="1" <?php if ($aedisplay == 1) echo 'checked="checked"'; ?> type="radio" /> <?php _e('On', 'better-wp-security'); ?></label>
										<label><input name="BWPS_away_enable" value="0" <?php if ($aedisplay == 0) echo 'checked="checked"'; ?> type="radio" /> <?php _e('Off', 'better-wp-security'); ?></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_away_enable"><?php _e('Type of Restriction', 'better-wp-security'); ?></label>
									</th>
									<td>
										<label><input name="BWPS_away_mode" id="BWPS_away_mode" value="1" <?php if ($modisplay == 1) echo 'checked="checked"'; ?> type="radio" /> <?php _e('Daily', 'better-wp-security'); ?></label>
										<label><input name="BWPS_away_mode" value="0" <?php if ($modisplay == 0) echo 'checked="checked"'; ?> type="radio" /> <?php _e('One Time', 'better-wp-security'); ?></label>
										<p>
										<?php _e('Selecting <em>"One Time"</em> will lock out the backend of your site from the start date and time to the end date and time. Selecting <em>"Daily"</em> will ignore the start and and dates and will disable your site backend from the start time to the end time.', 'better-wp-security'); ?>
										</p>
									</td>
								</tr>
							
								<tr valign="top">
									<th scope="row">
										<label for="BWPS_away_start"><?php _e('Start Date and Time', 'better-wp-security'); ?></label>
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
										<?php _e('Select the date and time at which access to the backend of this site will be disabled. Note that if <em>"Daily"</em> mode is selected the date will be ignored and access will be banned each day at the specified time.', 'better-wp-security'); ?>
										</p>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="BWPS_away_end"><?php _e('End Date and Time', 'better-wp-security'); ?></label>
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
										<?php _e('Select the date and time at which access to the backend of this site will be restored. Note that if <em>"Daily"</em> mode is selected the date will be ignored and access will be restored each day at the specified time.', 'better-wp-security'); ?>
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
			
		<?php include_once(trailingslashit(WP_PLUGIN_DIR) . 'better-wp-security/pages/donate.php'); ?>
		
		<?php if ($opts['away_enable'] == 1) { ?>
			<div class="clear"></div>
			<div class="postbox-container" style="width:70%">
				<div class="postbox opened" style="background-color: lightYellow;">
					<h3><?php _e('Access time rules.'); ?></h3>	
					<div class="inside">
						<?php
							if ($opts['away_mode'] == 1) {
								$freq = " <strong><em>" . __('every day') . "</em></strong>";
								$stime = "<strong><em>" . date('g:i a', $opts['away_start']) . "</em></strong>";
								$etime = "<strong><em>" . date('g:i a', $opts['away_end']) . "</em></strong>";
							} else {
								$freq = "";
								$stime = "<strong><em>" . date('l, F jS, Y \a\\t g:i a', $opts['away_start']) . "</em></strong>";
								$etime = "<strong><em>" . date('l, F jS, Y \a\\t g:i a', $opts['away_end']) . "</em></strong>";
							}
						?>
						<p style="font-size: 150%; text-align: center;"><?php _e('The backend (administrative section) of this site will be unavailable', 'better-wp-security'); ?><?php echo $freq; ?> <?php _e('from', 'better-wp-security'); ?> <?php echo $stime; ?> <?php _e('until'); ?> <?php echo $etime; ?>.</p>
						<p><?php _e('Please note that according to your', 'better-wp-security'); ?> <a href="options-general.php"><?php _e('Wordpress timezone settings', 'better-wp-security'); ?></a> <?php _e('your local time is', 'better-wp-security'); ?> <strong><em><?php echo date('l, F jS, Y \a\\t g:i a', $BWPS->getLocalTime()); ?></em></strong>. <?php _e('If this is incorrect please correct it on the', 'better-wp-security'); ?> <a href="options-general.php"><?php _e('Wordpress general settings page', 'better-wp-security'); ?></a> <?php _e('by setting the appropriate time zone. Failure to do so may result in unintended lockouts.', 'better-wp-security'); ?></p>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<?php

if ( ! class_exists( 'bwps_admin_content' ) ) {

	class bwps_admin_content extends bwps_admin_common {
	
		function register_settings_page() {
		
			add_menu_page(
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Dashboard', $this->hook ),
				__( 'Security', $this->hook ),
				$this->accesslvl,
				$this->hook,
				array( &$this, 'admin_dashboard' ),
				BWPS_PU . 'images/padlock.png'
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Change Admin User', $this->hook ),
				__( 'Admin User', $this->hook ),
				$this->accesslvl,
				$this->hook . '-adminuser',
				array( &$this, 'admin_adminuser' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Away Mode', $this->hook ),
				__( 'Away Mode', $this->hook ),
				$this->accesslvl,
				$this->hook . '-awaymode',
				array( &$this, 'admin_awaymode' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Ban Hosts', $this->hook ),
				__( 'Ban Hosts', $this->hook ),
				$this->accesslvl,
				$this->hook . '-banhosts',
				array( &$this, 'admin_banhosts' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Change Content Directory', $this->hook ),
				__( 'Content Directory', $this->hook ),
				$this->accesslvl,
				$this->hook . '-contentdirectory',
				array( &$this, 'admin_contentdirectory' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Backup WordPress Database', $this->hook ),
				__( 'Database Backup', $this->hook ),
				$this->accesslvl,
				$this->hook . '-databasebackup',
				array( &$this, 'admin_databasebackup' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Change Database Prefix', $this->hook ),
				__( 'Database Prefix', $this->hook ),
				$this->accesslvl,
				$this->hook . '-databaseprefix',
				array( &$this, 'admin_databaseprefix' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Hide Backend', $this->hook ),
				__( 'Hide Backend', $this->hook ),
				$this->accesslvl,
				$this->hook . '-hidebackend',
				array( &$this, 'admin_hidebackend' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Intrusion Detection', $this->hook ),
				__( 'Intrusion Detection', $this->hook ),
				$this->accesslvl,
				$this->hook . '-intrusiondetection',
				array( &$this, 'admin_intrusiondetection' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'Limit Login Attempts', $this->hook ),
				__( 'Login Limits', $this->hook ),
				$this->accesslvl,
				$this->hook . '-loginlimits',
				array( &$this, 'admin_loginlimits' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'WordPress System Tweaks', $this->hook ),
				__( 'System Tweaks', $this->hook ),
				$this->accesslvl,
				$this->hook . '-systemtweaks',
				array( &$this, 'admin_systemtweaks' )
			);
			
			add_submenu_page(
				$this->hook, 
				__( $this->pluginname, $this->hook ) . ' - ' . __( 'View Logs', $this->hook ),
				__( 'View Logs', $this->hook ),
				$this->accesslvl,
				$this->hook . '-logs',
				array( &$this, 'admin_logs' )
			);
			
			//Make the dashboard the first submenu item and the item to appear when clicking the parent.
			global $submenu;
			if ( isset( $submenu[$this->hook] ) ) {
			
				$submenu[$this->hook][0][0] = __( 'Dashboard', $this->hook );
				
			}
			
		}	
		
		function admin_dashboard() {
			
		}
		
		function admin_adminuser() {
			$this->admin_page( $this->pluginname  . ' - ' .  __( 'Change Admin User', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'adminuser_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Change The Admin User', $this->hook ), 'adminuser_content_2' ) //adminuser options
				)
			);
		}
		
		function admin_awaymode() {
			$this->admin_page( $this->pluginname  . ' - ' .  __( 'Administor Away Mode', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'awaymode_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Away Mode Options', $this->hook ), 'awaymode_content_2' ), //awaymode options
					array( __( 'Away Mode Rules', $this->hook ), 'awaymode_content_3' )
				)
			);
		}
		
		function admin_banhosts() {
			$this->admin_page( $this->pluginname  . ' - ' .  __( 'Ban Hosts', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'banhosts_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Banned Hosts Configuration', $this->hook ), 'banhosts_content_2' ) //banhosts options
				)
			);
		}
		
		function admin_contentdirectory() {
			$this->admin_page( $this->pluginname  . ' - ' .  __( 'Change wp-content Directory', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'contentdirectory_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Change The wp-content Directory', $this->hook ), 'contentdirectory_content_2' ) //contentdirectory options
				)
			);
		}
		
		function admin_databasebackup() {
			$this->admin_page( $this->pluginname  . ' - ' .  __( 'Backup WordPress Database', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'databasebackup_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Backup Your WordPress Database', $this->hook ), 'databasebackup_content_2' ), //backup switch
					array( __( 'Schedule Automated Backups', $this->hook ), 'databasebackup_content_3' ), //scheduled backup options
					array( __( 'Download Backups', $this->hook ), 'databasebackup_content_4' ) //where to find downloads
				)
			);
		}
		
		function admin_databaseprefix() {
			$this->admin_page( $this->pluginname  . ' - ' .  __( 'Change Database Prefix', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'databaseprefix_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Change The Database Prefix', $this->hook ), 'databaseprefix_content_2' ) //databaseprefix options
				)
			);
		}
		
		function admin_hidebackend() {
		
		}
		
		function admin_intrusiondetection() {
			$this->admin_page( $this->pluginname  . ' - ' .  __( 'Intrusion Detection', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'intrusiondetection_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Intrusion Detection', $this->hook ), 'intrusiondetection_content_2' ) //intrusiondetection options
				)
			);
		}
		
		function admin_loginlimits() {
			$this->admin_page( $this->pluginname  . ' - ' .  __( 'Limit Login Attempts', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'loginlimits_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Limit Login Attempts', $this->hook ), 'loginlimits_content_2' ) //loginlimit options
				)
			);
		}
		
		function admin_systemtweaks() {
		
		}
		
		function admin_logs() {
			$this->admin_page( $this->pluginname  . ' - ' .  __( 'Better WP Security Logs', $this->hook ),
				array(
					array( __( 'Before You Begin', $this->hook ), 'logs_content_1' ), //information to prevent the user from getting in trouble
					array( __( 'Clean Database', $this->hook ), 'logs_content_2' ), //Clean Database
					array( __( 'Current Lockouts', $this->hook ), 'logs_content_3' ), //Current Lockouts log
					array( __( '404 Errors', $this->hook ), 'logs_content_4' ) //404 Errors
				)
			);
		}
	
		function adminuser_content_1() {
			?>
			<p><?php _e( 'By default WordPress initially creates a username with the username of "admin." This is insecure as this user has full rights to your WordPress system and a potential hacker already knows that it is there. All an attacker would need to do at that point is guess the password. Changing this username will force a potential attacker to have to guess both your username and your password which makes some attacks significantly more difficult.', $this->hook ); ?></p>
			<p><?php _e( 'Note that this function will only work if you chose a username other than "admin" when installing WordPress.', $this->hook ); ?></p>
			<?php
		}
		
		function adminuser_content_2() {
			if ( $this->user_exists( 'admin' ) ) { //only show form if user 'admin' exists
				?>
				<form method="post" action="">
					<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
					<input type="hidden" name="bwps_page" value="adminuser_1" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for "newuser"><?php _e( 'Enter Username', $this->hook ); ?></label>
							</th>
							<td>
								<?php //username field ?>
								<input id="newuser" name="newuser" type="text" />
								<p><?php _e( 'Enter a new username to replace "admin." Please note that if you are logged in as admin you will have to log in again.', $this->hook ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
				</form>
				<?php
			} else { //if their is no admin user display a note 
				?>
					<p><?php _e( 'Congratulations! You do not have a user named "admin" in your WordPress installation. No further action is available on this page.', $this->hook ); ?></p>
				<?
			}
		}
		
		function awaymode_content_1() {
			?>
			<p><?php _e( 'As many of us update our sites on a general schedule it is not always necessary to permit site access all of the time. The options below will disable the backend of the site for the specified period. This could also be useful to disable site access based on a schedule for classroom or other reasons.', $this->hook ); ?></p>
			<p><?php _e( 'Please note that according to your', $this->hook ); ?> <a href="options-general.php"><?php _e( 'Wordpress timezone settings', $this->hook ); ?></a> <?php _e( 'your local time is', $this->hook ); ?> <strong><em><?php echo date( 'l, F jS, Y \a\\t g:i a', strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s',time() ) ) ) ); ?></em></strong>. <?php _e( 'If this is incorrect please correct it on the', $this->hook ); ?> <a href="options-general.php"><?php _e( 'Wordpress general settings page', $this->hook ); ?></a> <?php _e( 'by setting the appropriate time zone. Failure to do so may result in unintended lockouts.', $this->hook ); ?></p>
			<?php
		}
		
		function awaymode_content_2() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="awaymode_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
			<?php 
				$cDate = strtotime( date( 'n/j/y 12:00 \a\m', time() ) );
				$sTime = $options['am_starttime'];
				$eTime = $options['am_endtime'];
				$sDate = $options['am_startdate'];
				$eDate = $options['am_enddate'];
				$shdisplay = date( 'g', $sTime );
				$sidisplay = date( 'i', $sTime );
				$ssdisplay = date( 'a', $sTime );
				$ehdisplay = date( 'g', $eTime );
				$eidisplay = date( 'i', $eTime );
				$esdisplay = date( 'a', $eTime );
				
				if ( $options['am_enabled'] == 1 && $eDate > $cDate ) {	
					$smdisplay = date( 'n', $sDate );
					$sddisplay = date( 'j', $sDate );
					$sydisplay = date( 'Y', $sDate );
					
					$emdisplay = date( 'n', $eDate );
					$eddisplay = date( 'j', $eDate );
					$eydisplay = date( 'Y', $eDate );
					
				} else {
					$sDate = strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', time() + 86400) ) );
					$eDate = strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', time() + ( 86400 * 2 ) ) ) );
					$smdisplay = date( 'n', $sDate );
					$sddisplay = date( 'j', $sDate );
					$sydisplay = date( 'Y', $sDate );
					
					$emdisplay = date( 'n', $eDate );
					$eddisplay = date( 'j', $eDate );
					$eydisplay = date( 'Y', $eDate );
				}
			?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "am_enabled"><?php _e( 'Enable Away Mode', $this->hook ); ?></label>
						</th>
						<td>
							<input id="am_enabled" name="am_enabled" type="checkbox" value="1" <?php checked( '1', $options['am_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable away mode.', $this->hook ); ?></p>
						</td>
					</tr>	
					<tr valign="top">
						<th scope="row">
							<label for="am_type"><?php _e( 'Type of Restriction', $this->hook ); ?></label>
						</th>
						<td>
							<label><input name="am_type" id="am_type" value="1" <?php checked( '1', $options['am_type'] ); ?> type="radio" /> <?php _e( 'Daily', $this->hook ); ?></label>
							<label><input name="am_type" value="0" <?php checked( '0', $options['am_type'] ); ?> type="radio" /> <?php _e( 'One Time', $this->hook ); ?></label>
							<p><?php _e( 'Selecting <em>"One Time"</em> will lock out the backend of your site from the start date and time to the end date and time. Selecting <em>"Daily"</em> will ignore the start and and dates and will disable your site backend from the start time to the end time.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="am_startdate"><?php _e( 'Start Date', $this->hook ); ?></label>
						</th>
						<td>
							<select name="am_startmonth" id="am_startdate">
								<?php
									for ( $i = 1; $i <= 12; $i++ ) {
										if ( $smdisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . date( 'F', strtotime( $i . '/1/' . date( 'Y', time() ) ) ) . '</option>';
									}
								?>
							</select> 
							<select name="am_startday">
								<?php
									for ( $i = 1; $i <= 31; $i++ ) {
										if ( $sddisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . date( 'jS', strtotime( '1/' . $i . '/' . date( 'Y', time() ) ) ) . '</option>';
									}
								?>
							</select>, 
							<select name="am_startyear">
								<?php
									for ( $i = date( 'Y', time() ); $i < ( date( 'Y', time() ) + 2 ); $i++ ) {
										if ( $sydisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
									}
								?>
							</select>
							<p><?php _e( 'Select the date at which access to the backend of this site will be disabled. Note that if <em>"Daily"</em> mode is selected this field will be ignored and access will be banned every day at the specified time.', $this->hook ); ?>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="am_enddate"><?php _e( 'End Date', $this->hook ); ?></label>
						</th>
						<td>
							<select name="am_endmonth" id="am_enddate">
								<?php
									for ( $i = 1; $i <= 12; $i++ ) {
										if ( $emdisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . date( 'F', strtotime( $i . '/1/' . date( 'Y', time() ) ) ) . '</option>';
									}
								?>
							</select> 
							<select name="am_endday">
								<?php
									for ( $i = 1; $i <= 31; $i++ ) {
										if ( $eddisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . date( 'jS', strtotime( '1/' . $i . '/' . date( 'Y', time() ) ) ) . '</option>';
									}
								?>
							</select>, 
							<select name="am_endyear">
								<?php
									for ( $i = date( 'Y', time() ); $i < ( date( 'Y', time() ) + 2 ); $i++ ) {
										if ( $eydisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
									}
								?>
							</select>
							<p><?php _e( 'Select the date at which access to the backend of this site will be re-enabled. Note that if <em>"Daily"</em> mode is selected this field will be ignored and access will be banned every day at the specified time.', $this->hook ); ?>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="am_starttime"><?php _e( 'Start Time', $this->hook ); ?></label>
						</th>
						<td>
							<select name="am_starthour"  id="am_starttime">
								<?php
									for ( $i = 1; $i <= 12; $i++ ) {
										if ( $shdisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
									}
								?>
							</select> : 
							<select name="am_startmin">
								<?php
									for ( $i = 0; $i < 60; $i++ ) {
										if ( $sidisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										if ( $i < 10 ) {
											$val = "0" . $i;
										} else {
											$val = $i;
										}
										echo '<option value="' . $val . '"' . $selected . '>' . $val . '</option>';
									}
								?>
							</select> 
							<select name="am_starthalf">											
								<option value="am"<?php if ( $ssdisplay == 'am' ) echo ' selected'; ?>>am</option>
								<option value="pm"<?php if ( $ssdisplay == 'pm' ) echo ' selected'; ?>>pm</option>
							</select>
							<p><?php _e( 'Select the time at which access to the backend of this site will be disabled. Note that if <em>"Daily"</em> mode is selected access will be banned every day at the specified time.', $this->hook ); ?>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="am_endtime"><?php _e( 'End Time', $this->hook ); ?></label>
						</th>
						<td>
							<select name="am_endhour"  id="am_endtime">
								<?php
									for ( $i = 1; $i <= 12; $i++ ) {
										if ( $ehdisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										echo '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
									}
								?>
							</select> : 
							<select name="am_endmin">
								<?php
									for ( $i = 0; $i < 60; $i++ ) {
										if ( $eidisplay == $i ) {
											$selected = ' selected';
										} else {
											$selected = '';
										}
										if ( $i < 10 ) {
											$val = "0" . $i;
										} else {
											$val = $i;
										}
										echo '<option value="' . $val . '"' . $selected . '>' . $val . '</option>';
									}
								?>
							</select> 
							<select name="am_endhalf">											
								<option value="am"<?php if ( $esdisplay == 'am' ) echo ' selected'; ?>>am</option>
								<option value="pm"<?php if ( $esdisplay == 'pm' ) echo ' selected'; ?>>pm</option>
							</select>
							<p><?php _e( 'Select the time at which access to the backend of this site will be re-enabled. Note that if <em>"Daily"</em> mode is selected access will be banned every day at the specified time.', $this->hook ); ?>
							</p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		function awaymode_content_3() {

			$options = get_option( $this->primarysettings ); //use settings fields 
			
			if ( $options['am_type'] == 1 ) {
				$freq = ' <strong><em>' . __( 'every day' ) . '</em></strong>';
				$stime = '<strong><em>' . date( 'g:i a', $options['am_starttime'] ) . '</em></strong>';
				$etime = '<strong><em>' . date( 'g:i a', $options['am_endtime'] ) . '</em></strong>';
			} else {
				$freq = '';
				$stime = '<strong><em>' . date( 'l, F jS, Y', $options['am_startdate'] ) . __( ' at ', $this->hook ) . date( 'g:i a', $options['am_starttime'] ) . '</em></strong>';
				$etime = '<strong><em>' . date( 'l, F jS, Y', $options['am_enddate'] ) . __( ' at ', $this->hook ) . date( 'g:i a', $options['am_endtime'] ) . '</em></strong>';
			}
			if ( $options['am_enabled'] == 1 ) {
				?>
				<p style="font-size: 150%; text-align: center;"><?php _e( 'The backend (administrative section) of this site will be unavailable', $this->hook ); ?><?php echo $freq; ?> <?php _e( 'from', $this->hook ); ?> <?php echo $stime; ?> <?php _e( 'until', $this->hook ); ?> <?php echo $etime; ?>.</p>
				<?php } else { ?>
					<p><?php _e( 'Away mode is currently diabled', $this->hook ); ?></p>
				<?php
			}	
		}
		
		function banhosts_content_1() {
			?>
			<p><?php _e( 'This feature allows you to ban hosts from your site completely using individual or groups of IP addresses without having to manage any configuration of your server. Any IP found in the list below will not be allowed any access to your site.', $this->hook ); ?></p>
			<p><?php _e( 'Please note this feature works using the WordPress database and PHP. That said, it is not nearly as effecient as banning hosts via your server configuration. I recommend keeping the list here short or using it only for temporary bans to avoid performance issues.', $this->hook ); ?></p>
			<?php
		}
		
		function banhosts_content_2() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="banhosts_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "bh_enabled"><?php _e( 'Enable Banned Hosts', $this->hook ); ?></label>
						</th>
						<td>
							<input id="bh_enabled" name="bh_enabled" type="checkbox" value="1" <?php checked( '1', $options['bh_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable the banned hosts feature.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "bh_banlist"><?php _e( 'Ban List', $this->hook ); ?></label>
						</th>
						<td>
							<textarea id="bh_banlist" rows="10" cols="50" name="bh_banlist"><?php echo isset( $_POST['bh_banlist'] ) ? $_POST['bh_banlist'] : $options['bh_banlist']; ?></textarea>
							<p><?php _e( 'Use the guidelines below to enter hosts that will not be allowed access to your site. Note you cannot ban yourself.', $this->hook ); ?></p>
							<ul><em>
								<li><?php _e( 'You may ban users by individual IP address or IP address range.', $this->hook ); ?></li>
								<li><?php _e( 'Individual IP addesses must be in IPV4 standard format (i.e. ###.###.###.###). Wildcards (*) are allowed to specify a range of ip addresses.', $this->hook ); ?></li>
								<li><?php _e( 'IP Address ranges may also be specified using the format ###.###.###.### - ###.###.###.###. Wildcards cannot be used in addresses specified like this.', $this->hook ); ?></li>
								<li><a href="http://ip-lookup.net/domain-lookup.php" target="_blank"><?php _e( 'Lookup IP Address.', $this->hook ); ?></a></li>
								<li><?php _e( 'Enter only 1 IP address or 1 IP address range per line.', $this->hook ); ?></li>
							</em></ul>
						</td>
					</tr>
					
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		function contentdirectory_content_1() {
			?>
			<p><?php _e( 'By default WordPress puts all your content including images, plugins, themes, uploads, and more in a directory called "wp-content". This makes it easy to scan for vulnerable files on your WordPress installation as an attacker already knows where the vulnerable files will be at. As there are many plugins and themes with security vulnerabilities moving this folder can make it harder for an attacker to find problems with your site as scans of your site\'s file system will not produce any results.', $this->hook ); ?></p>
			<p><?php _e( 'Please note that changing the name of your wp-content directory on a site that already has images and other content referencing it will break your site. For that reason I highly recommend you do not try this on anything but a fresh WordPress install. In addition, this tool will not allow further changes to your wp-content folder once it has already been renamed in order to avoid accidently breaking a site later on. This includes uninstalling this plugin which will not revert the changes made by this page.', $this->hook ); ?></p>
			<p><?php _e( 'Finally, changing the name of the wp-content directory may in fact break plugins and themes that have "hard-coded" it into their design rather than call it dynamically.', $this->hook ); ?></p>
			<p style="text-align: center; font-size: 130%; font-weight: bold; color: blue;"><?php _e( 'WARNING: BACKUP YOUR WORDPRESS INSTALLATION BEFORE USING THIS TOOL!', $this->hook ); ?></p>
			<?php
		}
		
		function contentdirectory_content_2() {
			if ( ! isset( $_POST['bwps_page'] ) && strpos( WP_CONTENT_DIR, 'wp-content' ) ) { //only show form if user the content directory hasn't already been changed
				?>
				<form method="post" action="">
					<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
					<input type="hidden" name="bwps_page" value="contentdirectory_1" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for "dirname"><?php _e( 'Directory Name', $this->hook ); ?></label>
							</th>
							<td>
								<?php //username field ?>
								<input id="dirname" name="dirname" type="text" value="wp-content" />
								<p><?php _e( 'Enter a new directory name to replace "wp-content." You may need to log in again after performing this operation.', $this->hook ); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
				</form>
				<?php
			} else { //if their is no admin user display a note 
				if ( isset( $_POST['bwps_page'] ) ) {
					$dirname = $_POST['dirname'];
				} else {
					$dirname = substr( WP_CONTENT_DIR, strrpos( WP_CONTENT_DIR, '/' ) + 1 );
				}
				?>
					<p><?php _e( 'Congratulations! You have already renamed your "wp-content" directory.', $this->hook ); ?></p>
					<p><?php _e( 'Your current content directory is: ', $this->hook ); ?><strong><?php echo $dirname ?></strong></p>
					<p><?php _e( 'No further actions are available on this page.', $this->hook ); ?></p>
				<?
			}
		}
		
		function databasebackup_content_1() {
			?>
			<p><?php _e( 'While this plugin goes a long way to helping secure your website nothing can give you a 100% guarantee that your site won\'t be the victim of an attack. When something goes wrong one of the easiest ways of getting your site back is to restore the database from a backup and replace the files with fresh ones. Use the button below to create a full backup of your database for this purpose. You can also schedule automated backups and download or delete previous backups.', $this->hook ); ?></p>
			<?php		
		}
		
		function databasebackup_content_2() {
			?>
			<form method="post" action="">
				<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
				<input type="hidden" name="bwps_page" value="databasebackup_1" />
				<p><?php _e( 'Press the button below to create a backup of your WordPress database. If you have "Send Backups By Email" selected in automated backups you will receive an email containing the backup file.', $this->hook ); ?></p>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Create Database Backup', $this->hook ) ?>" /></p>			
			</form>
			<?php
		}	
		
		function databasebackup_content_3() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="databasebackup_2" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "backup_enabled"><?php _e( 'Enable Scheduled Backups', $this->hook ); ?></label>
						</th>
						<td>
							<input id="backup_enabled" name="backup_enabled" type="checkbox" value="1" <?php checked( '1', $options['backup_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable scheduled backups which will be emailed to the address below.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "backup_int"><?php _e( 'Backup Interval', $this->hook ); ?></label>
						</th>
						<td>
							<select id="backup_int" name="backup_int">
								<option value="hourly" <?php selected( $options['backup_int'], 'hourly' ); ?>>Hourly</option>
								<option value="twicedaily" <?php selected( $options['backup_int'], 'twicedaily' ); ?>>Twice Daily</option>
								<option value="daily" <?php selected( $options['backup_int'], 'daily' ); ?>>Daily</option>
							</select>
							<p><?php _e( 'Select the frequency of automated backups.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "backup_email"><?php _e( 'Send Backups by Email', $this->hook ); ?></label>
						</th>
						<td>
							<input id="backup_email" name="backup_email" type="checkbox" value="1" <?php checked( '1', $options['backup_email'] ); ?> />
							<p><?php _e( 'Email backups to the current site admin.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "backups_to_retain"><?php _e( 'Backups to Keep', $this->hook ); ?></label>
						</th>
						<td>
							<input id="backups_to_retain" name="backups_to_retain" type="text" value="<?php echo $options['backups_to_retain']; ?>" />
							<p><?php _e( 'Number of backup files to retain. Enter 0 to keep all files.', $this->hook ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		function databasebackup_content_4() {
			$options = get_option( $this->primarysettings );
			if ( $options['backup_email'] == 1 ) {
				?>
				<p><?php echo __( 'Database backups are NOT saved to the server and instead will be emailed to', $this->hook ) . ' <strong>' . get_option( 'admin_email' ) . '</strong>. ' . __( 'To change this unset "Send Backups by Email" in the "Scheduled Automated Backups" section above.', $this->hook ); ?></p>
				<?php
			} else {
				?>
				<p><?php _e( 'Please note that for security backups are not available for direct download. You will need to go to ', $this->hook ); ?></p>
				<p><strong><em><?php echo BWPS_PP . 'lib/phpmysqlautobackup/backups'; ?></em></strong></p>
				<p><?php _e( ' via FTP or SSH to download the files. This is because there is too much sensative information in the backup files and you do not want anyone just stumbling upon them.', $this->hook ); ?></p>
				<?php
			}
		}
			
		function databaseprefix_content_1() {
			?>
			<p><?php _e( 'By default WordPress assigns the prefix "wp_" to all the tables in the database where your content, users, and objects live. For potential attackers this means it is easier to write scripts that can target WordPress databases as all the important table names for 95% or so of sites are already known. Changing this makes it more difficult for tools that are trying to take advantage of vulnerabilites in other places to affect the database of your site.', $this->hook ); ?></p>
			<p><?php _e( 'Please note that the use of this tool requires quite a bit of system memory which my be more than some hosts can handle. If you back your database up you can\'t do any permanent damage but without a proper backup you risk breaking your site and having to perform a rather difficult fix.', $this->hook ); ?></p>
			<p style="text-align: center; font-size: 130%; font-weight: bold; color: blue;"><?php _e( 'WARNING: <a href="?page=better_wp_security-databasebackup">BACKUP YOUR DATABASE</a> BEFORE USING THIS TOOL!', $this->hook ); ?></p>
			<?php
		}
		
		function databaseprefix_content_2() {
			global $wpdb;
			?>
			<?php if ( $wpdb->base_prefix == 'wp_' ) { ?>
				<p><strong><?php _e( 'Your database is using the default table prefix', $this->hook ); ?> <em>wp_</em>. <?php _e( 'You should change this.', $this->hook ); ?></strong></p>
			<?php } else { ?>
				<p><?php _e( 'Your current database table prefix is', $this->hook ); ?> <strong><em><?php echo $wpdb->base_prefix; ?></em></strong></p>
			<?php } ?>
			<form method="post" action="">
				<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
				<input type="hidden" name="bwps_page" value="databaseprefix_1" />
				<p><?php _e( 'Press the button below to generate a random database prefix value and update all of your tables accordingly.', $this->hook ); ?></p>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Change Database Table Prefix', $this->hook ) ?>" /></p>			
			</form>
			<?php
		}
		
		function intrusiondetection_content_1() {
			?>
			<p><?php _e( 'Currently intrusion detection looks only at a user who is hitting a large number of non-existent pages, that is they are getting a large number of 404 errors. It assumes that a user who hits a lot of 404 errors in a short period of time is scanning for something (presumably a vulnerability) and locks them out accordingly (you can set the thresholds for this below). This also gives the added benefit of helping you find hidden problems causing 404 errors on unseen parts of your site as all errors will be logged in the "View Logs" page. You can set threshholds for this feature below.', $this->hook ); ?></p>
			<?php
		}
		
		function intrusiondetection_content_2() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="intrusiondetection_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "id_enabled"><?php _e( 'Enable Instrusion Detection', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_enabled" name="id_enabled" type="checkbox" value="1" <?php checked( '1', $options['id_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable instrustion detection.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_emailnotify"><?php _e( 'Email Notifications', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_emailnotify" name="id_emailnotify" type="checkbox" value="1" <?php checked( '1', $options['id_emailnotify'] ); ?> />
							<p><?php _e( 'Enabling this feature will trigger an email to be sent to the website administrator whenever a host is locked out of the system.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_checkinterval"><?php _e( 'Check Period', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_checkinterval" name="id_checkinterval" type="text" value="<?php echo $options['id_checkinterval']; ?>" />
							<p><?php _e( 'The number of minutes in which 404 errors should be remembered. Setting this too long can cause legitimate users to be banned.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_threshold"><?php _e( 'Error Threshold', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_threshold" name="id_threshold" type="text" value="<?php echo $options['id_threshold']; ?>" />
							<p><?php _e( 'The numbers of errors (within the check period timeframe) that will trigger a lockout.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_banperiod"><?php _e( 'Lockout Period', $this->hook ); ?></label>
						</th>
						<td>
							<input id="id_banperiod" name="id_banperiod" type="text" value="<?php echo $options['id_banperiod']; ?>" />
							<p><?php _e( 'The number of minutes a host will be banned from the site after triggering a lockout.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "id_whitelist"><?php _e( 'White List', $this->hook ); ?></label>
						</th>
						<td>
							<textarea id="id_whitelist" rows="10" cols="50" name="id_whitelist"><?php echo isset( $_POST['id_whitelist'] ) ? $_POST['id_whitelist'] : $options['id_whitelist']; ?></textarea>
							<p><?php _e( 'Use the guidelines below to enter hosts that will never be locked out due to too many 404 errors. This could be useful for Google, etc.', $this->hook ); ?></p>
							<ul><em>
								<li><?php _e( 'You may whitelist users by individual IP address or IP address range.', $this->hook ); ?></li>
								<li><?php _e( 'Individual IP addesses must be in IPV4 standard format (i.e. ###.###.###.###). Wildcards (*) are allowed to specify a range of ip addresses.', $this->hook ); ?></li>
								<li><?php _e( 'IP Address ranges may also be specified using the format ###.###.###.### - ###.###.###.###. Wildcards cannot be used in addresses specified like this.', $this->hook ); ?></li>
								<li><a href="http://ip-lookup.net/domain-lookup.php" target="_blank"><?php _e( 'Lookup IP Address.', $this->hook ); ?></a></li>
								<li><?php _e( 'Enter only 1 IP address or 1 IP address range per line.', $this->hook ); ?></li>
								<li><?php _e( '404 errors will still be logged for users on the whitelist. Only the lockout will be prevented', $this->hook ); ?></li>
							</em></ul>
						</td>
					</tr>
					
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		function loginlimits_content_1() {
			?>
			<p><?php _e( 'If one had unlimited time and wanted to try an unlimited number of password combimations to get into your site they eventually would, right? This method of attach, known as a brute force attack, is something that WordPress is acutely susceptible by default as the system doesn\t care how many attempts a user makes to login. It will always let you try agin. Enabling login limits will ban the host user from attempting to login again after the specified bad login threshhold has been reached.', $this->hook ); ?></p>
			<?php	
		}
		
		function loginlimits_content_2() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="loginlimits_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "ll_enabled"><?php _e( 'Enable Login Limits', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_enabled" name="ll_enabled" type="checkbox" value="1" <?php checked( '1', $options['ll_enabled'] ); ?> />
							<p><?php _e( 'Check this box to enable login limits on this site.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_maxattemptshost"><?php _e( 'Max Login Attempts Per Host', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_maxattemptshost" name="ll_maxattemptshost" type="text" value="<?php echo $options['ll_maxattemptshost']; ?>" />
							<p><?php _e( 'The number of login attempts a user has before their host or computer is locked out of the system.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_maxattemptsuser"><?php _e( 'Max Login Attempts Per User', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_maxattemptsuser" name="ll_maxattemptsuser" type="text" value="<?php echo $options['ll_maxattemptsuser']; ?>" />
							<p><?php _e( 'The number of login attempts a user has before their username is locked out of the system. Note that this is different from hosts in case an attacker is using multiple computers. In addition, if they are using your login name you could be locked out yourself.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_checkinterval"><?php _e( 'Login Time Period (minutes)', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_checkinterval" name="ll_checkinterval" type="text" value="<?php echo $options['ll_checkinterval']; ?>" />
							<p><?php _e( 'The number of minutes in which bad logins should be remembered.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_banperiod"><?php _e( 'Lockout Time Period (minutes)', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_banperiod" name="ll_banperiod" type="text" value="<?php echo $options['ll_banperiod']; ?>" />
							<p><?php _e( 'The length of time a host or computer will be banned from this site after hitting the limit of bad logins.', $this->hook ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "ll_emailnotify"><?php _e( 'Email Notifications', $this->hook ); ?></label>
						</th>
						<td>
							<input id="ll_emailnotify" name="ll_emailnotify" type="checkbox" value="1" <?php checked( '1', $options['ll_emailnotify'] ); ?> />
							<p><?php _e( 'Enabling this feature will trigger an email to be sent to the website administrator whenever a host or user is locked out of the system.', $this->hook ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		function logs_content_1() {
			?>
			<p><?php _e( 'This page contains the logs generated by Better WP Security, current lockouts (which can be cleared here) and a way to cleanup the logs to save space on the server and reduce CPU load. Please note, you must manually clear these logs, they will not do so automatically. I highly recommend you do so regularly to improve performance which can otherwise be slowed if the system has to search through large log-files on a regular basis.', $this->hook ); ?></p>
			<?php
		}
		
		function logs_content_2() {
			global $wpdb;
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="log_1" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
			<?php 
				$countlogin = $wpdb->get_var( "SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "bwps_log` WHERE `timestamp` < " . ( time() - ( $options['ll_checkinterval'] * 60 ) ) . " AND `type` = 1;" );
				$count404 = $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "bwps_log` WHERE `timestamp` < " . (time() - ( $options['id_checkinterval'] * 60 ) ) . " AND `type` = 2;" );
				$countlockout = $wpdb->get_var( "SELECT COUNT(*) FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `exptime` < " . time() . " OR `active` = 0;" );
			 ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Old Data', $this->hook ); ?>
						</th>
						<td>
							<p><?php _e( 'Below is old security data still in your Wordpress database. Data is considered old when the lockout has expired, or been manually cancelled, or when the log entry will no longer be used to generate a lockout.', $this->hook ); ?></p>
							<p><?php _e( 'This data is not automatically deleted so that it may be used for analysis. You may delete this data with the form below. To see the actual data you will need to access your database directly.', $this->hook ); ?></p>
							<p><?php _e( 'Check the box next to the data you would like to clear and then press the "Remove Old Data" button.', $this->hook ); ?></p>
							<ul>
								<li style="list-style: none;"> <input type="checkbox" name="badlogins" id="badlogins" value="1" /> <label for="badlogins"><?php _e( 'Your database contains', $this->hook ); ?> <strong><?php echo $countlogin; ?> <?php _e( 'bad login entries.', $this->hook ); ?></strong></label></li>
								<li style="list-style: none;"> <input type="checkbox" name="404s" id="404s" value="1" /> <label for="404s"><?php _e( 'Your database contains', $this->hook ); ?> <strong><?php echo $count404; ?> <?php _e( '404 errors.', $this->hook ); ?></strong><br />
								<em><?php _e( 'This will clear the 404 log below.', $this->hook ); ?></em></label></li>
								<li style="list-style: none;"> <input type="checkbox" name="lockouts" id="lockouts" value="1" /> <label for="lockouts"><?php _e( 'Your database contains', $this->hook ); ?> <strong><?php echo $countlockout; ?> <?php _e( 'old lockouts.', $this->hook ); ?></strong></label></li>
							</ul>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Remove Data', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		function logs_content_3() {
			global $wpdb;
			?>
			<form method="post" action="">
			<?php wp_nonce_field( 'BWPS_admin_save','wp_nonce' ) ?>
			<input type="hidden" name="bwps_page" value="log_2" />
			<?php $options = get_option( $this->primarysettings ); //use settings fields ?>
			<?php 
				$hostLocks = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `active` = 1 AND `exptime` > " . time() . " AND `host` != 0;", ARRAY_A );
				$userLocks = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_lockouts` WHERE `active` = 1 AND `exptime` > " . time() . " AND `user` != 0;", ARRAY_A );
			 ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Locked out hosts', $this->hook ); ?>
						</th>
						<td>
							<?php if ( sizeof( $hostLocks ) > 0 ) { ?>
							<ul>
								<?php foreach ( $hostLocks as $host) { ?>
									<li style="list-style: none;"><input type="checkbox" name="lo_<?php echo $host['id']; ?>" id="lo_<?php echo $host['id']; ?>" value="<?php echo $host['id']; ?>" /> <label for="lo_<?php echo $host['id']; ?>"><strong><?php echo $host['host']; ?></strong> - Expires <em><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $host['exptime'] ) ); ?></em></label></li>
								<?php } ?>
							</ul>
							<?php } else { ?>
								<p><?php _e( 'Currently no hosts are locked out of this website.', $this->hook ); ?></p>
							<?php } ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Locked out users', $this->hook ); ?>
						</th>
						<td>
							<?php if (sizeof( $userLocks ) > 0 ) { ?>
							<ul>
								<?php foreach ( $userLocks as $user ) { ?>
									<?php $userdata = get_userdata( $user['user'] ); ?>
									<li style="list-style: none;"><input type="checkbox" name="lo_<?php echo $user['id']; ?>" id="lo_<?php echo $user['id']; ?>" value="<?php echo $user['id']; ?>" /> <label for="lo_<?php echo $user['id']; ?>"><strong><?php echo $userdata->user_login; ?></strong> - Expires <em><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $user['exptime'] ) ); ?></em></label></li>
								<?php } ?>
							</ul>
							<?php } else { ?>
								<p><?php _e( 'Currently no users are locked out of this website.', $this->hook ); ?></p>
							<?php } ?>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Release Lockout', $this->hook ) ?>" /></p>
			</form>
			<?php
		}
		
		function logs_content_4() {
			global $wpdb;
			
			$errors = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "bwps_log` WHERE `type` = 2;", ARRAY_A );
			$grouped = array();
			foreach ( $errors as $error ) {
				if ( isset( $grouped[$error['url']] ) ) {
					$grouped[$error['url']]['count'] = $grouped[$error['url']]['count'] + 1;
					$grouped[$error['url']]['last'] = $grouped[$error['url']]['last'] > $error['timestamp'] ? $grouped[$error['url']]['last'] : $error['timestamp'];
				} else {
					$grouped[$error['url']]['count'] = 1;
					$grouped[$error['url']]['last'] = $error['timestamp'];
				} 
			}
			if ( sizeof( $grouped ) > 0 ) {
			?>
			<p><?php _e( 'The following is a list of 404 errors found on your site with the relative url listed first, the number of times the error was encountered in parenthases, and the last time the error was encounterd given last.', $this->hook ); ?></p>
			<?php
				foreach ( $grouped as $url => $data ) {
					?>
					<li><?php echo $url; ?> (<?php echo $data['count']; ?>) <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $data['last'] ) ); ?></li>
					<?php
				}
			?>
			
			<?php
			} else {
			?>
				<p><?php _e( 'There are currently no 404 errors in the log', $this->hook ); ?></p>
			<?php 
			}
		}
	
	}

}

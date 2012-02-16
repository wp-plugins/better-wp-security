<?php

if (!class_exists('bwps_admin')) {

	class bwps_admin extends bit51_bwps {
		
		/**
		 * Initialize admin function
		 */
		function __construct() {
							
		}
	
		/**
		 * Register page settings
		 */
		function register_settings_page() {
			add_menu_page(
				__($this->pluginname, $this->hook) . ' - ' . __('Dashboard', 'better-wp-security'),
				__('Security', 'better-wp-security'),
				$this->accesslvl,
				$this->hook,
				array(&$this, 'admin_dashboard'),
				BWPS_PU . 'images/padlock.png'
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('Change Admin User', $this->hook),
				__('Admin User', $this->hook),
				$this->accesslvl,
				$this->hook . '-adminuser',
				array(&$this, 'admin_adminuser')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('Away Mode', $this->hook),
				__('Away Mode', $this->hook),
				$this->accesslvl,
				$this->hook . '-awaymode',
				array(&$this, 'admin_awaymode')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('Ban Users', $this->hook),
				__('Ban Users', $this->hook),
				$this->accesslvl,
				$this->hook . '-banusers',
				array(&$this, 'admin_banusers')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('Change Content Directory', $this->hook),
				__('Content Directory', $this->hook),
				$this->accesslvl,
				$this->hook . '-contentdirectory',
				array(&$this, 'admin_contentdirectory')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('Backup WordPress Database', $this->hook),
				__('Database Backup', $this->hook),
				$this->accesslvl,
				$this->hook . '-databasebackup',
				array(&$this, 'admin_databasebackup')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('Change Database Prefix', $this->hook),
				__('Database Prefix', $this->hook),
				$this->accesslvl,
				$this->hook . '-databaseprefix',
				array(&$this, 'admin_databaseprefix')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('Hide Backend', $this->hook),
				__('Hide Backend', $this->hook),
				$this->accesslvl,
				$this->hook . '-hidebackend',
				array(&$this, 'admin_hidebackend')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('Intrusion Detection', $this->hook),
				__('Intrusion Detection', $this->hook),
				$this->accesslvl,
				$this->hook . '-intrusiondetection',
				array(&$this, 'admin_intrusiondetection')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('Limit Login Attempts', $this->hook),
				__('Limit Logins', $this->hook),
				$this->accesslvl,
				$this->hook . '-limitlogins',
				array(&$this, 'admin_limitlogins')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('WordPress System Tweaks', $this->hook),
				__('System Tweaks', $this->hook),
				$this->accesslvl,
				$this->hook . '-systemtweaks',
				array(&$this, 'admin_systemtweaks')
			);
			add_submenu_page(
				$this->hook, 
				__($this->pluginname, $this->hook) . ' - ' . __('View Logs', $this->hook),
				__('Ban Users', $this->hook),
				$this->accesslvl,
				$this->hook . '-logs',
				array(&$this, 'admin_logs')
			);
			
			//Make the dashboard the first submenu item and the item to appear when clicking the parent.
			global $submenu;
			if (isset($submenu[$this->hook])) {
				$submenu[$this->hook][0][0] = __('Dashboard', $this->hook);
			}
		}	
		
		/**
		 * Register admin page main content
		 * To add more boxes to the admin page add a 2nd inner array item with title and callback function or content
		 */
		function admin_dashboard() {
			
		}
		
		function admin_adminuser() {
			$this->admin_page($this->pluginname  . ' - ' .  __('Change Admin User', $this->hook),
				array(
					array(__('Before You Begin', $this->hook), 'adminuser_content_1'), //information to prevent the user from getting in trouble
					array(__('Change The Admin User', $this->hook), 'adminuser_content_2') //adminuser options
				)
			);
		}
		
		function admin_awaymode() {
		
		}
		
		function admin_banusers() {
		
		}
		
		function admin_contentdirectory() {
			$this->admin_page($this->pluginname  . ' - ' .  __('Change wp-content Directory', $this->hook),
				array(
					array(__('Before You Begin', $this->hook), 'contentdirectory_content_1'), //information to prevent the user from getting in trouble
					array(__('Change The wp-content Directory', $this->hook), 'contentdirectory_content_2') //adminuser options
				)
			);
		}
		
		function admin_databasebackup() {
			$this->admin_page($this->pluginname  . ' - ' .  __('Backup WordPress Database', $this->hook),
				array(
					array(__('Before You Begin', $this->hook), 'databasebackup_content_1'), //information to prevent the user from getting in trouble
					array(__('Backup Your WordPress Database', $this->hook), 'databasebackup_content_2'), //backup switch
					array(__('Schedule Automated Backups', $this->hook), 'databasebackup_content_3'),
					array(__('Download Backups', $this->hook), 'databasebackup_content_4'),
				)
			);
		}
		
		function admin_databaseprefix() {
			$this->admin_page($this->pluginname  . ' - ' .  __('Change Database Prefix', $this->hook),
				array(
					array(__('Before You Begin', $this->hook), 'databaseprefix_content_1'), //information to prevent the user from getting in trouble
					array(__('Change The Database Prefix', $this->hook), 'databaseprefix_content_2') //adminuser options
				)
			);
		}
		
		function admin_hidebackend() {
		
		}
		
		function admin_intrusiondetection() {
		
		}
		
		function admin_limitlogins() {
		
		}
		
		function admin_systemtweaks() {
		
		}
		
		function admin_logs() {
		
		}
			
		/**
		 * Introduction text for change admin user page
		 **/
		function adminuser_content_1() {
			?>
			<p><?php _e('By default WordPress initially creates a username with the username of "admin." This is insecure as this user has full rights to your WordPress system and a potential hacker already knows that it is there. All an attacker would need to do at that point is guess the password. Changing this username will force a potential attacker to have to guess both your username and your password which makes some attacks significantly more difficult.', $this->hook); ?></p>
			<p><?php _e('Note that this function will only work if you chose a username other than "admin" when installing WordPress.', $this->hook); ?></p>
			<?php
		}
		
		/**
		 * Options form for change admin user page
		 **/
		function adminuser_content_2() {
			if ($this->user_exists('admin')) { //only show form if user 'admin' exists
				?>
				<form method="post" action="">
					<?php wp_nonce_field('BWPS_admin_save','wp_nonce') ?>
					<input type="hidden" name="bwps_page" value="adminuser_1" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for "newuser"><?php _e('Enter Username', $this->hook); ?></label>
							</th>
							<td>
								<?php //username field ?>
								<input id="newuser" name="newuser" type="text" />
								<p><?php _e('Enter a new username to replace "admin." Please note that if you are logged in as admin you will have to log in again.', $this->hook); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes', $this->hook) ?>" /></p>
				</form>
				<?php
			} else { //if their is no admin user display a note 
				?>
					<p><?php _e('Congratulations! You do not have a user named "admin" in your WordPress installation. No further action is available on this page.', $this->hook); ?></p>
				<?
			}
		}
		
		
		function contentdirectory_content_1() {
			?>
			<p><?php _e('By default WordPress puts all your content including images, plugins, themes, uploads, and more in a directory called "wp-content". This makes it easy to scan for vulnerable files on your WordPress installation as an attacker already knows where the vulnerable files will be at. As there are many plugins and themes with security vulnerabilities moving this folder can make it harder for an attacker to find problems with your site as scans of your site\'s file system will not produce any results.', $this->hook); ?></p>
			<p><?php _e('Please note that changing the name of your wp-content directory on a site that already has images and other content referencing it will break your site. For that reason I highly recommend you do not try this on anything but a fresh WordPress install. In addition, this tool will not allow further changes to your wp-content folder once it has already been renamed in order to avoid accidently breaking a site later on. This includes uninstalling this plugin which will not revert the changes made by this page.', $this->hook); ?></p>
			<p><?php _e('Finally, changing the name of the wp-content directory may in fact break plugins and themes that have "hard-coded" it into their design rather than call it dynamically.', $this->hook); ?></p>
			<p style="text-align: center; font-size: 130%; font-weight: bold; color: blue;"><?php _e('WARNING: BACKUP YOUR WORDPRESS INSTALLATION BEFORE USING THIS TOOL!', $this->hook); ?></p>
			<?php
		}
		
		function contentdirectory_content_2() {
			if (!isset($_POST['bwps_page']) && strpos(WP_CONTENT_DIR, 'wp-content')) { //only show form if user the content directory hasn't already been changed
				?>
				<form method="post" action="">
					<?php wp_nonce_field('BWPS_admin_save','wp_nonce') ?>
					<input type="hidden" name="bwps_page" value="contentdirectory_1" />
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for "dirname"><?php _e('Directory Name', $this->hook); ?></label>
							</th>
							<td>
								<?php //username field ?>
								<input id="dirname" name="dirname" type="text" value="wp-content" />
								<p><?php _e('Enter a new directory name to replace "wp-content." You may need to log in again after performing this operation.', $this->hook); ?></p>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes', $this->hook) ?>" /></p>
				</form>
				<?php
			} else { //if their is no admin user display a note 
				if (isset($_POST['bwps_page'])) {
					$dirname = $_POST['dirname'];
				} else {
					$dirname = substr(WP_CONTENT_DIR, strrpos(WP_CONTENT_DIR, '/') + 1);
				}
				?>
					<p><?php _e('Congratulations! You have already renamed your "wp-content" directory.', $this->hook); ?></p>
					<p><?php _e('Your current content directory is: ', $this->hook); ?><strong><?php echo $dirname ?></strong></p>
					<p><?php _e('No further actions are available on this page.', $this->hook); ?></p>
				<?
			}
		}
		
		function databasebackup_content_1() {
			?>
			<p><?php _e('While this plugin goes a long way to helping secure your website nothing can give you a 100% guarantee that your site won\'t be the victim of an attack. When something goes wrong one of the easiest ways of getting your site back is to restore the database from a backup and replace the files with fresh ones. Use the button below to create a full backup of your database for this purpose. You can also schedule automated backups and download or delete previous backups.', $this->hook); ?></p>
			<?php		
		}
		
		function databasebackup_content_2() {
			?>
			<form method="post" action="">
				<?php wp_nonce_field('BWPS_admin_save','wp_nonce') ?>
				<input type="hidden" name="bwps_page" value="databasebackup_1" />
				<p><?php _e('Press the button below to create a backup of your WordPress database. If you have "Send Backups By Email" selected in automated backups you will receive an email containing the backup file.', $this->hook); ?></p>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Create Database Backup', $this->hook) ?>" /></p>			
			</form>
			<?php
		}	
		
		function databasebackup_content_3() {
			?>
			<form method="post" action="">
			<?php wp_nonce_field('BWPS_admin_save','wp_nonce') ?>
			<input type="hidden" name="bwps_page" value="databasebackup_2" />
			<?php $options = get_option('bit51_bwps'); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "backup_enabled"><?php _e('Enable Scheduled Backups', $this->hook); ?></label>
						</th>
						<td>
							<input id="backup_enabled" name="backup_enabled" type="checkbox" value="1" <?php checked('1', $options['backup_enabled']); ?> />
							<p><?php _e('Check this box to enable scheduled backups which will be emailed to the address below.', $this->hook); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "backup_int"><?php _e('Backup Interval', $this->hook); ?></label>
						</th>
						<td>
							<select id="backup_int" name="backup_int">
								<option value="hourly" <?php selected( $options['backup_int'], 'hourly' ); ?>>Hourly</option>
								<option value="twicedaily" <?php selected( $options['backup_int'], 'twicedaily' ); ?>>Twice Daily</option>
								<option value="daily" <?php selected( $options['backup_int'], 'daily' ); ?>>Daily</option>
							</select>
							<p><?php _e('Select the frequency of automated backups.', $this->hook); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "backup_email"><?php _e('Send Backups by Email', $this->hook); ?></label>
						</th>
						<td>
							<input id="backup_email" name="backup_email" type="checkbox" value="1" <?php checked('1', $options['backup_email']); ?> />
							<p><?php _e('Email backups to the current site admin.', $this->hook); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for "backups_to_retain"><?php _e('Backups to Keep', $this->hook); ?></label>
						</th>
						<td>
							<input id="backups_to_retain" name="backups_to_retain" type="text" value="<?php echo $options['backups_to_retain']; ?>" />
							<p><?php _e('Number of backup files to retain. Enter 0 to keep all files.', $this->hook); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
			</form>
			<?php
		}
		
		function databasebackup_content_4() {
			$options = get_option('bit51_bwps');
			if ($options['backup_email'] == 1) {
				?>
				<p><?php _e('Database backups are NOT saved to the server and instead will be emailed to the site admin\'s email address. To change this unset "Send Backups by Email" in the "Scheduled Automated Backups" section above.', $this->hook); ?></p>
				<?php
			} else {
				?>
				<p><?php _e('Please note that for security backups are not available for direct download. You will need to go to ', $this->hook); ?></p>
				<p><strong><em><?php echo BWPS_PP . 'lib/phpmysqlautobackup/backups'; ?></em></strong></p>
				<p><?php _e(' via FTP or SSH to download the files. This is because there is too much sensative information in the backup files and you do not want anyone just stumbling upon them.', $this->hook); ?></p>
				<?php
			}
		}
		
		/**
		 * Intro for change database prefix page
		 **/
				
		function databaseprefix_content_1() {
			?>
			<p><?php _e('By default WordPress assigns the prefix "wp_" to all the tables in the database where your content, users, and objects live. For potential attackers this means it is easier to write scripts that can target WordPress databases as all the important table names for 95% or so of sites are already known. Changing this makes it more difficult for tools that are trying to take advantage of vulnerabilites in other places to affect the database of your site.', $this->hook); ?></p>
			<p><?php _e('Please note that the use of this tool requires quite a bit of system memory which my be more than some hosts can handle. If you back your database up you can\'t do any permanent damage but without a proper backup you risk breaking your site and having to perform a rather difficult fix.', $this->hook); ?></p>
			<p style="text-align: center; font-size: 130%; font-weight: bold; color: blue;"><?php _e('WARNING: <a href="?page=better_wp_security-databasebackup">BACKUP YOUR DATABASE</a> BEFORE USING THIS TOOL!', $this->hook); ?></p>
			<?php
		}
		
		/**
		 * form for change database prefix page
		 **/
		 
		function databaseprefix_content_2() {
			global $wpdb;
			?>
			<?php if ($wpdb->base_prefix == 'wp_') { ?>
				<p><strong><?php _e('Your database is using the default table prefix', $this->hook); ?> <em>wp_</em>. <?php _e('You should change this.', $this->hook); ?></strong></p>
			<?php } else { ?>
				<p><?php _e('Your current database table prefix is', $this->hook); ?> <strong><em><?php echo $wpdb->base_prefix; ?></em></strong></p>
			<?php } ?>
			<form method="post" action="">
				<?php wp_nonce_field('BWPS_admin_save','wp_nonce') ?>
				<input type="hidden" name="bwps_page" value="databaseprefix_1" />
				<p><?php _e('Press the button below to generate a random database prefix value and update all of your tables accordingly.', $this->hook); ?></p>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Change Database Table Prefix', $this->hook) ?>" /></p>			
			</form>
			<?php
		}
		
		/**
		 * Send form processor to correct function
		 **/
		function form_dispatcher() {
			//verify nonce
			if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_admin_save')) {
				die('Security error!');
			}
			
			switch ($_POST['bwps_page']) {
				case 'adminuser_1':
					$this->adminuser_process_1();
					break;
				case 'contentdirectory_1':
					$this->contentdirectory_process_1();
					break;
				case 'databasebackup_1':
					$this->databasebackup_process_1();
					break;
				case 'databasebackup_2':
					$this->databasebackup_process_2();
					break;
				case 'databaseprefix_1':
					$this->databaseprefix_process_1();
					break;
			}
		}
		
		/**
		 * process changing admin username
		 **/
		function adminuser_process_1() {
			global $wpdb;
			$errorHandler = __('Successfully Changed admin Username. If you are logged in as admin you will have to log in again before continuing.', $this->hook);
			
			//sanitize the username
			$newuser = $wpdb->escape($_POST['newuser']);
			
			if (strlen($newuser) < 1) { //if the field was left blank set an error message
			
				$errorHandler = new WP_Error();
				$errorHandler->add("2", $newuser . __("You must enter a valid username. Please try again", $this->hook));
				
			} else {	
			
				if (validate_username($newuser)) { //make sure username is valid
				
					if ($this->user_exists($newuser)) { //if the user already exists set an error
					
						if (!is_wp_error($errorHandler)) {
							$errorHandler = new WP_Error();
						}
								
						$errorHandler->add("2", $newuser . __(" already exists. Please try again", $this->hook));
								
					} else {
								
						//query main user table
						$wpdb->query("UPDATE `" . $wpdb->users . "` SET user_login = '" . $newuser . "' WHERE user_login='admin'");
						
						if (is_multisite()) { //process sitemeta if we're in a multi-site situation
						
							$oldAdmins = $wpdb->get_var("SELECT meta_value FROM `" . $wpdb->sitemeta . "` WHERE meta_key='site_admins'");
							$newAdmins = str_replace('5:"admin"',strlen($newuser) . ':"' . $newuser . '"',$oldAdmins);
							$wpdb->query("UPDATE `" . $wpdb->sitemeta . "` SET meta_value = '" . $newAdmins . "' WHERE meta_key='site_admins'");
							
						}
						
					}
					
				} else {
				
					if (!is_wp_error($errorHandler)) { //set an error for invalid username
						$errorHandler = new WP_Error();
					}
				
					$errorHandler->add("2", $newuser . __(" is not a valid username. Please try again", $this->hook));
				}
			}
			
			$this-> showmessages($errorHandler); //finally show messages
			
		}
		
		/**
		 * Function to change the wp-content directory
		 **/
		function contentdirectory_process_1() {
			global $wpdb;
			$errorHandler = __('Settings Saved', $this->hook);
			
			$oldDir = WP_CONTENT_DIR;
			$newDir = trailingslashit(ABSPATH) . $wpdb->escape($_POST['dirname']);
			
			$renamed = rename($oldDir, $newDir);
			
			if (!$renamed) {
			
				if (!is_wp_error($errorHandler)) {
					$errorHandler = new WP_Error();
				}
						
				$errorHandler->add("2", $newuser . __("Unable to rename the wp-content folder. Operation cancelled.", $this->hook));
				
				die('Old Dir = ' . $oldDir . ', New Dir = ' . $newDir);
				
			}
			
			$wpconfig = $this->getConfig(); //get the path for the config file
					
			chmod($wpconfig, 0644); //make sure the config file is writable
					
			$handle = @fopen($wpconfig, 'r+'); //open for reading
					
			if ($handle && $renamed) {
			
				$scanText = "/* That's all, stop editing! Happy blogging. */";
				$altScan = "/* Stop editing */";
				$newText = "define('WP_CONTENT_DIR', '" . $newDir . "');\r\ndefine('WP_CONTENT_URL', '" . trailingslashit(get_option('siteurl')) . $wpdb->escape($_POST['dirname']) . "');\r\n\r\n/* That's all, stop editing! Happy blogging. */\r\n";
					
				//read each line into an array
				while ($lines[] = fgets($handle, 4096)){}
						
				fclose($handle); //close reader
						
				$handle = @fopen($wpconfig, 'w+'); //open writer
						
				foreach ($lines as $line) { //process each line
						
					if (strstr($line,'WP_CONTENT_DIR') || strstr($line,'WP_CONTENT_URL') ) {
					
						$line = str_replace($line, '', $line);

					}

					if (strstr($line, $scanText)) {
					
						$line = str_replace($scanText, $newText, $line);
					
					} else if (strstr($line, $altScan)) {
					
						$line = str_replace($altScan, $newText, $line);
					
					}
							
					fwrite($handle, $line); //write the line
							
				}
						
				fclose($handle); //close the config file
						
				chmod($wpconfig, 0444); //make sure the config file is no longer writable
						
			}
			
			$this-> showmessages($errorHandler); //finally show messages
		}
		
		/**
		 * Process database backup
		 **/
		function databasebackup_process_1() {
			$errorHandler = __('Database Backup Completed.', $this->hook);
			
			$this->db_backup();
			
			$this->showmessages($errorHandler);		
			
		}
		
		/**
		 * Validate input
		 */
		function databasebackup_process_2() {
			$errorHandler = __('Settings Saved', $this->hook);
			
			$options = get_option('bit51_bwps'); //load the options
			
			$options['backup_email'] = ($_POST['backup_email'] == 1 ? 1 : 0);
			$options['backup_enabled'] = ($_POST['backup_enabled'] == 1 ? 1 : 0);
			$options['backups_to_retain'] = abs(intval($_POST['backups_to_retain']));
			$options['backup_int'] = $_POST['backup_int'];
						
			update_option('bit51_bwps',$options);
			
			if ($options['backup_email'] == 1) {
			
				$backuppath = BWPS_PP . 'lib/phpmysqlautobackup/backups';
				$files = scandir($backuppath);
			
				foreach ($files as $file) {
					unlink($backuppath . '/' . $file);			
				}
			}
			
			if (wp_next_scheduled('bwps_backup')) {
				wp_clear_scheduled_hook('bwps_backup');
			}
			
			$this-> showmessages($errorHandler);
			
		}
		
		/**
		 * Process changing table names and associated data
		 **/
		function databaseprefix_process_1() {
			global $wpdb;
			$errorHandler = __('Database Prefix Changed', $this->hook);	
	
			$checkPrefix = true;//Assume the first prefix we generate is unique
			
			while ($checkPrefix) {
			
				$avail = 'abcdefghijklmnopqrstuvwxyz0123456789';
				
				$newPrefix = $avail[rand(0, 25)];
				
				$prelength = rand(4, 9);
				
				for ($i = 0; $i < $prelength; $i++) {
					$newPrefix .= $avail[rand(0, 35)];
				}
				
				$newPrefix .= '_';
						
				$checkPrefix = $wpdb->get_results('SHOW TABLES LIKE "' . $newPrefix . '%";', ARRAY_N); //if there are no tables with that prefix in the database set checkPrefix to false
					
			}
					
			$tables = $wpdb->get_results('SHOW TABLES LIKE "' . $wpdb->base_prefix . '%"', ARRAY_N); //retrieve a list of all tables in the DB
					
			//Rename each table
			foreach ($tables as $table) {
					
				$table = substr($table[0], strlen($wpdb->base_prefix), strlen($table[0])); //Get the table name without the old prefix
		
				//rename the table and generate an error if there is a problem
				if ($wpdb->query('RENAME TABLE `' . $wpdb->base_prefix . $table . '` TO `' . $newPrefix . $table . '`;') === false) {
		
					if (!is_wp_error($errorHandler)) { //set an error for invalid username
						$errorHandler = new WP_Error();
					}
		
					$errorHandler->add('2', __('Error: Could not rename table ', $this->hook) . $wpdb->base_prefix . __('. You may have to rename the table manually.', $this->hook));	
						
				}
						
			}
					
			$upOpts = true; //assume we've successfully updated all options to start
					
			if (is_multisite()) {
						
				$blogs = $wpdb->get_col("SELECT blog_id FROM `" . $wpdb->blogs . "` WHERE public = '1' AND archived = '0' AND mature = '0' AND spam = '0' ORDER BY blog_id DESC"); //get list of blog id's
					
				if (is_array($blogs)) { //make sure there are other blogs to update
						
					//update each blog's user_roles option
					foreach ($blogs as $blog) {
							
						$results = $wpdb->query('UPDATE `' . $newPrefix . $blog . '_options` SET option_name = "' . $newPrefix . $blog . '_user_roles" WHERE option_name = "' . $wpdb->base_prefix . $blog . '_user_roles" LIMIT 1;');
								
						if ($results === false) { //if there's an error upOpts should equal false
							$upOpts = false;
						}
								
					}
							
				}
						
			}
					
			$upOpts = $wpdb->query('UPDATE `' . $newPrefix . 'options` SET option_name = "' . $newPrefix . 'user_roles" WHERE option_name = "' . $wpdb->base_prefix . 'user_roles" LIMIT 1;'); //update options table and set flag to false if there's an error
										
			if ($upOpts === false) { //set an error
		
				if (!$errorHandler) {
					$errorHandler = new WP_Error();
				}
							
				$errorHandler->add('2', __('Could not update prefix refences in options tables.', $this->hook));
						
			}
										
			$rows = $wpdb->get_results('SELECT * FROM `' . $newPrefix . 'usermeta`'); //get all rows in usermeta
										
			//update all prefixes in usermeta
			foreach ($rows as $row) {
					
				if (substr($row->meta_key, 0, strlen($wpdb->base_prefix)) == $wpdb->base_prefix) {
						
					$pos = $newPrefix . substr($row->meta_key, strlen($wpdb->base_prefix), strlen($row->meta_key));
							
					$result = $wpdb->query('UPDATE `' . $newPrefix . 'usermeta` SET meta_key="' . $pos . '" WHERE meta_key= "' . $row->meta_key . '" LIMIT 1;');
							
					if ($result == false) {
								
						if (!$errorHandler) {
							$errorHandler = new WP_Error();
						}
										
						$errorHandler->add('2', __('Could not update prefix refences in usermeta table.', $this->hook));
								
					}
							
				}
						
			}
					
			$wpconfig = $this->getConfig(); //get the path for the config file
					
			chmod($wpconfig, 0644); //make sure the config file is writable
					
			$handle = @fopen($wpconfig, "r+"); //open for reading
					
			if ($handle) {
					
				//read each line into an array
				while ($lines[] = fgets($handle, 4096)){}
						
				fclose($handle); //close reader
						
				$handle = @fopen($wpconfig, "w+"); //open writer
						
				foreach ($lines as $line) { //process each line
						
					//if the prefix is in the line
					if (strpos($line, 'table_prefix')) {
							
						$line = str_replace($wpdb->base_prefix, $newPrefix, $line);
								
					}
							
					fwrite($handle, $line); //write the line
							
				}
						
				fclose($handle); //close the config file
						
				chmod($wpconfig, 0444); //make sure the config file is no longer writable
						
				$wpdb->base_prefix = $newPrefix; //update the prefix
						
			}
					
			$this-> showmessages($errorHandler); //finally show messages
			remove_action('admin_notices', 'site_admin_notice');
			remove_action('network_admin_notices', 'site_admin_notice');
					
		}		
	}
}

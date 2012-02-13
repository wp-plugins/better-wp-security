<?php

if (!class_exists('bwps_admin')) {

	class bwps_admin extends bit51_bwps {
		
		/**
		 * Initialize admin function
		 */
		function __construct() {
		
			if (is_admin() || (is_multisite() && is_network_admin())) {
			
				//add scripts and css
				add_action('admin_print_scripts', array(&$this, 'config_page_scripts'));
				add_action('admin_print_styles', array(&$this, 'config_page_styles'));
			
				if (is_multisite()) { 
					add_action('network_admin_menu', array(&$this, 'register_settings_page')); 
				} else {
					add_action('admin_menu',  array(&$this, 'register_settings_page'));
				}
			
				//add settings
				add_action('admin_init', array(&$this, 'register_settings'));
			
				//add action link
				add_filter('plugin_action_links', array(&$this, 'add_action_link'), 10, 2);
			
				//add donation reminder
				add_action('admin_init', array(&$this, 'ask'));	
			
				if (isset($_POST['bwps_page'])) {
					add_action('admin_init', array(&$this, 'form_dispatcher'));
				}
			}			
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
				$this->pluginurl . 'images/padlock.png'
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
		 * Create admin page main content
		 */
		function dashboard_content_1() {
			
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
					<input type="hidden" name="bwps_page" value="adminuser" />
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
			<p><?php _e('Please note that changing the name of your wp-content directory on a site that already has images and other content referencing it will break your site. For that reason I highly recommend you do not try this on anything but a fresh WordPress install. In addition, this tool will not allow further changes to your wp-content folder once it has already been renamed. In order to avoid accidently breaking a site later on.', $this->hook); ?></p>
			<p><?php _e('Finally, changing the name of the wp-content directory may in fact break plugins and themes that have "hard-coded" it into their design rather than call it dynamically.', $this->hook); ?></p>
			<p style="text-align: center; font-size: 130%; font-weight: bold; color: blue;"><?php _e('WARNING: BACKUP YOUR WORDPRESS INSTALLATION BEFORE USING THIS TOOL!', $this->hook); ?></p>
			<?php
		}
		
		function contentdirectory_content_2() {
			if (strpos(WP_CONTENT_DIR, 'wp-content')) { //only show form if user the content directory hasn't already been changed
				?>
				<form method="post" action="">
					<?php wp_nonce_field('BWPS_admin_save','wp_nonce') ?>
					<input type="hidden" name="bwps_page" value="contentdirectory" />
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
				?>
					<p><?php _e('Congratulations! You have already renamed your "wp-content" directory.', $this->hook); ?></p>
					<p><?php _e('Your current content directory is: ', $this->hook); ?><strong><?php echo substr(WP_CONTENT_DIR, strrpos(WP_CONTENT_DIR, '/') + 1); ?></strong></p>
					<p><?php _e('No further actions are available on this page.', $this->hook); ?></p>
				<?
			}
		}		
		
		/**
		 * Intro for change database prefix page
		 **/
				
		function databaseprefix_content_1() {
			?>
			<p><?php _e('By default WordPress assigns the prefix "wp_" to all the tables in the database where your content, users, and objects live. For potential attackers this means it is easier to write scripts that can target WordPress databases as all the important table names for 95% or so of sites are already known. Changing this makes it more difficult for tools that are trying to take advantage of vulnerabilites in other places to affect the database of your site.', $this->hook); ?></p>
			<p><?php _e('Please note that the use of this tool requires quite a bit of system memory which my be more than some hosts can handle. If you back your database up you can\'t do any permanent damage but without a proper backup you risk breaking your site and having to perform a rather difficult fix.', $this->hook); ?></p>
			<p style="text-align: center; font-size: 130%; font-weight: bold; color: blue;"><?php _e('WARNING: BACKUP YOUR DATABASE BEFORE USING THIS TOOL!', $this->hook); ?></p>
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
				<input type="hidden" name="bwps_page" value="databaseprefix" />
				<p><?php _e('Press the button below to generate a random database prefix value and update all of your tables accordingly.', $this->hook); ?></p>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Change Database Table Prefix', $this->hook) ?>" /></p>			
			</form>
			<?php
		}
		
		/**
		 * Validate input
		 */
		function bwps_val_options($input) {
			$input['enabled'] = ($input['enabled'] == 1 ? 1 : 0);
		    
		    return $input;
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
				case 'adminuser':
					$this->adminuser_process();
					break;
				case 'contentdirectory':
					$this->contentdirectory_process();
					break;
				case 'databaseprefix':
					$this->databaseprefix_process();
					break;
			}
		}
		
		/**
		 * process changing admin username
		 **/
		function adminuser_process() {
			global $wpdb;
			$errorHandler = '';
			
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
		function contentdirectory_process() {
		
		}
		
		/**
		 * Process changing table names and associated data
		 **/
		function databaseprefix_process() {
			global $wpdb;
			$errorHandler = '';			
	
			$checkPrefix = true;//Assume the first prefix we generate is unique
			
			while ($checkPrefix) {
			
				$avail = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				
				$newPrefix = $avail[rand(0, 51)];
				
				$prelength = rand(4, 9);
				
				for ($i = 0; $i < $prelength; $i++) {
					$newPrefix .= $avail[rand(0, 61)];
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
					if (strpos($line, $wpdb->base_prefix)) {
							
						$line = str_replace($wpdb->base_prefix, $newPrefix, $line);
								
					}
							
					fwrite($handle, $line); //write the line
							
				}
						
				fclose($handle); //close the config file
						
				chmod($wpconfig, 0444); //make sure the config file is no longer writable
						
				$wpdb->base_prefix = $newPrefix; //update the prefix
						
			}
					
			$this-> showmessages($errorHandler); //finally show messages
			add_action( 'admin_notices', 'site_admin_notice' );
			add_action( 'network_admin_notices', 'site_admin_notice' );
					
		}		
	}
}

new bwps_admin();

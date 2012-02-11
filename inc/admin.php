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
				
					switch ($_POST['bwps_page']) {
						case 'adminuser':
							add_action('admin_init', array(&$this, 'adminuser_process'));
							break;
					}
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
			$this->admin_page($this->pluginname  . ' - ' .  __('System Dashboard', $this->hook),
				array(
					array(__('Enable/Disable Content Filter', $this->hook), 'dashboard_content_1') //primary admin page content
				)
			);
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
		
		}
		
		function admin_databaseprefix() {
		
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
			?>
			<form method="post" action="options.php">
			<?php settings_fields('bit51_rat_options'); //use main settings group ?>
			<?php $options = get_option('bit51_rat'); //use settings fields ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for "enable"><?php _e('Enable Filter', $this->hook); ?></label>
						</th>
						<td>
							<input id="enable" name="bit51_rat[enabled]" type="checkbox" value="1" <?php checked('1', $options['enabled']); ?> />
							<p><?php _e('Uncheck to disable content filter (this will disable plugin functionality).', $this->hook); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
			</form>
			<?php
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
					<?php wp_nonce_field('BWPS_adminuser_save','wp_nonce') ?>
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
		
		/**
		 * process changing admin username
		 **/
		function adminuser_process() {
			global $wpdb;
			$errorHandler = '';
			
			//verify nonce
			if (!wp_verify_nonce($_POST['wp_nonce'], 'BWPS_adminuser_save')) {
				die('Security error!');
			}
			
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
		 * Validate input
		 */
		function bwps_val_options($input) {
			$input['enabled'] = ($input['enabled'] == 1 ? 1 : 0);
		    
		    return $input;
		}
		
		function process_form() {
		
		}
	}
}

new bwps_admin();

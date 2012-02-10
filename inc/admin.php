<?php

if (!class_exists('bwps_admin')) {

	class bwps_admin extends bit51_bwps {
		
		/**
		 * Initialize admin function
		 */
		function __construct() {
			
			//add scripts and css
			add_action('admin_print_scripts', array(&$this, 'config_page_scripts'));
			add_action('admin_print_styles', array(&$this, 'config_page_styles'));
			
			//add menu items
			add_action('admin_menu', array(&$this, 'register_settings_page'));
			
			//add settings
			add_action('admin_init', array(&$this, 'register_settings'));
			
			//add action link
			add_filter('plugin_action_links', array(&$this, 'add_action_link'), 10, 2);
			
			//add donation reminder
			add_action('admin_init', array(&$this, 'ask'));
			
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
					array(__('Enable/Disable Content Filter', $this->hook), 'dashboard_content') //primary admin page content
				)
			);
		}
		
		function admin_adminuser() {
			$this->admin_page($this->pluginname  . ' - ' .  __('Change Admin User', $this->hook),
				array(
					array(__('Enable/Disable Content Filter', $this->hook), 'dashboard_content') //primary admin page content
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
		function dashboard_content() {
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
		 * Validate input
		 */
		function bwps_val_options($input) {
			$input['enabled'] = ($input['enabled'] == 1 ? 1 : 0);
		    
		    return $input;
		}
	}
}

new bwps_admin();

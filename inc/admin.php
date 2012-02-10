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
			add_options_page($this->pluginname, $this->pluginname, $this->accesslvl, $this->hook, array(&$this,'rat_admin_init'));
		}	
		
		/**
		 * Register admin page main content
		 * To add more boxes to the admin page add a 2nd inner array item with title and callback function or content
		 */
		function rat_admin_init() {
			$this->admin_page($this->pluginname . ' Options', 
				array(
					array(__('Enable/Disable Content Filter', $this->hook), 'options_content') //primary admin page content
				)
			);
		}
		
		/**
		 * Create admin page main content
		 */
		function options_content() {
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
		function rat_val_options($input) {
			$input['enabled'] = ($input['enabled'] == 1 ? 1 : 0);
		    
		    return $input;
		}
	}
}

new bwps_admin();

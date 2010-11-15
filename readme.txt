
=== Better WP Security ===
Contributors: ChrisWiegman
Donate link: http://www.chriswiegman.com/projects/wordpress/better-wp-security/
Tags: security, login, lockdown, htaccess, hack, header, cleanup, ban, restrict, access, protect, protection, disable, images, image, hotlink, admin, username, database, prefix, wp-content, rename, directory, directories, secure
Requires at least: 3.0.1
Tested up to: 3.0.1
Stable tag: 0.2.BETA

A collection of numerous security fixes and modifications to help protect a standard Wordpress installation.

== License ==  
Released under the terms of the GNU General Public License. 

== Description ==

NOTE: This plugin is not yet ready for production and should be used only to test features and provide feedback!!!!

Better WP Security takes the best Wordpress security features and techniques and combines them in a single plugin thereby ensuring that as many security holes as possible are patched without  having to worry about conflicting features or the possibility of missing anything on your site.

= Current features =

* Remove the meta "Generator" tag
* Removes login error messages
* Change the urls for backend functions including login, admin, and more
* Limit admin access to specified IP or range of IP addresses
* Ban troublesome bots and other hosts
* Completely turn off the ability to login for a given time period (away mode)
* Prevent brute force attacks by banning hosts and users with too many invalid login attempts
* Display a random version number to non administrative users anywhere version is used (often attached to plugin resources such as scripts and style sheets)
* Remove theme, plugin, and core update notifications from users who do not have permission to update them (useful on multisite installations)
* Remove Windows Live Write header information
* Remove RSD header information
* Strengthen .htaccess settings
* Enforce strong passwords for all accounts of a configurable minimum role
* Basic Intrusion detection (based on 404 logging)
* Rename "admin" account
* Security checker
* Allow for changing the Wordpress table prefix where necessary
* Force SSL for admin pages (on supporting servers)
* Change wp-content path

== Installation ==

1. Backup your Wordpress database, config file, and .htaccess file
2. Upload the zip file to the `/wp-content/plugins/` directory
3. Unzip
4. Activate the plugin through the 'Plugins' menu in WordPress
5. Visit the Better security menu for checklist and options

== Frequently Asked Questions ==

= When will this plugin be officially released? =
* Better WP Security will be released officially when all known bugs are fixed and documentation is complete.

= Why release it at all before it is finished? =
* As this is a rather complex program I am releasing it early to get feedback from the Wordpress community. This will help insure that when it is fully released it will be the most stable and comprehensive security solution available for Wordpress.

= Will you keep developing this after the initial release? =
* Yes. The functionality of this plugin is a requirement of my job so this plugin will continue to be developed.

= Can I help? =
* Of course! I am in constant need of testers and I would be more than happy to add the right contributor. In addition, I could always use help with translations for internationalization.

= Will this break my site? =
* Until it is officially released it is quite possible (maybe even probable) that something will break. I will not release this officially until I am completely convinced that the code is stable and usable over as many systems as possible. That said, under no circumstances do I release this plugin with any warranty, implied or otherwise, and at no time will I take any responsibility for any damage that might arise from the use of this plugin. REMEMBER TO ALWAYS BACKUP BEFORE TRYING NEW SOFTWARE!

== Changelog ==

= 0.2.BETA - November 15, 2010=
* Updated hidebe to handle standard logout links
* Numerous other bugfixes

= 0.1.BETA - November 7, 2010 = 
* Finished status reporting
* Force SSL for admin pages (on supporting servers)
* Change wp-content path

= ALPHA 11 - November 6, 2010 =
* Added security checklist
* Added option to rename existing admin account
* Added option to change DB table prefix
* Various bugfixes

= ALPHA 10 - November 3, 2010 =
* Added more htaccess security options
* All htaccess options have been moved to their own page
* Added simple intrusion detection based on 404s
* Bugfixes and code optimization

= ALPHA 9 - November 2, 2010 =
* Deactivating now removes all htaccess areas and turns off associated options
* Enforce strong passwords for all users of a given minimum role
* Minor bug fixes

= ALPHA 8 - November 1, 2010 =
* Added various .htaccess options to strengthen file security
* Modified "hide backend" rewrite rules to work with multi-site
* Removed non-security hide-backend options
* Various bug fixes 
* Renamed "General" options page to "System Tweaks" to avoid confusion
* Added more options to clean up Wordpress headers
* Added options to remove plugin notifications from non-super admin users

= ALPHA 7 - October 31, 2010 =
* Continued code refactoring and bug-fixes
* Improved error handling and upgrade support
* Combined status and support options pages

= ALPHA 6 - October 30, 2010 =
* Added sanitization and validation to user input
* Added "away mode" to limit backend access by time
* Script no longer dies when logged out and instead returns to homepage.

= ALPHA 5 - October 28, 2010 =
* Complete refactor of the existing code
* Divided settings sections for better UX
* Added htaccess checks
* Redesigned options system for less database calls
* Reduced table usage from 4 to 2
* Added email notifications for login limiter
* Added complete access blocker for login limiter

= ALPHA 4 - October 26, 2010 =
* Added login limiter to limit invalid attempts
* various Bug fixes

= ALPHA 3 - October 25, 2010 =
* Corrected error display
* Added registration rules regardless of whether registrations are on or off.
* Added "Display random version to non-admins"
* Fixed rewrite rules on hide admin urls so going to the admin slug will work whether the user is logged in or not
* Added crude upgrade warning to warn of old (not so great) rewrite rules

= ALPHA 2 - October 24, 2010 =
* Optimized and commented code
* Added uninstall function
* Numerous fixes to bugs and logic

= 0.1 ALPHA - October 22, 2010 =
First alpha release including simple featureset. 

== Upgrade Notice ==

If upgrading from a version prior to ALPHA 5 you MUST deactivate the plugin manually first. Failure to do so may result in an inability to access the backend.

== Support ==

Please visit the <a href="http://www.chriswiegman.com/projects/wordpress/better-wp-security/">Homepage</a> for support
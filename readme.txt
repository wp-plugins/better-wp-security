
=== Better WP Security ===
Contributors: ChrisWiegman
Donate link: http://www.chriswiegman.com/projects/better-wp-security/
Tags: security, login, lockdown, htaccess, hack, header, cleanup, ban, restrict, access, protect, protection, disable, images, image, hotlink, admin, username, database, prefix, wp-content, rename, directory, directories, secure
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.7

Helps protect your Wordpress single or multi-site installation from attackers. Hardens standard Wordpress security by hiding vital areas of your site, protecting access to important files via htaccess, preventing brute-force login attempts, detecting attack attempts, and more.

== License ==  
Released under the terms of the GNU General Public License. 

== Description ==

Better WP Security takes the best Wordpress security features and techniques and combines them in a single plugin thereby ensuring that as many security holes as possible are patched without  having to worry about conflicting features or the possibility of missing anything on your site.

NOTE: It is quite possible (maybe even probable) that something will break due to the complexity of the changes made by this plugin. That said, under no circumstances do I release this plugin with any warranty, implied or otherwise, and at no time will I take any responsibility for any damage that might arise from the use of this plugin. REMEMBER TO ALWAYS BACKUP BEFORE TRYING NEW SOFTWARE!

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

= Are you still developing this plugin? =
* Yes. The functionality of this plugin is a requirement of my job so this plugin will continue to be developed.

= Does this work with netowork or multisite installations? =
* Yes, as of version 1.7 it should work fine with multisite installations.

= Can I help? =
* Of course! I am in constant need of testers and I would be more than happy to add the right contributor. In addition, I could always use help with translations for internationalization.

= Will this break my site? =
* It is quite possible (maybe even probable) that something will break. That said, under no circumstances do I release this plugin with any warranty, implied or otherwise, and at no time will I take any responsibility for any damage that might arise from the use of this plugin. REMEMBER TO ALWAYS BACKUP BEFORE TRYING NEW SOFTWARE!
* Note that renaming the wp-content directory will not update the path in existing content. Use this feature only on new sites or in a situation where you can easily update all existing links.

== Changelog ==

= Dev =
* Changed plugin description

= 1.7 May 21, 2011 =
* Renamed detect 404s section to intrusion detection to include upcoming features
* general spelling and grammer corrections
* Moved configuration to network dashboard for multisite installations
* Improved multisite support
* Warns if .htaccess or wp-config.php files aren't writable where needed
* Added icon to menu for easier identification
* Cleaned up and refined status information

= 1.6 - May 8, 2011 =
* Fixed WLManifest link removal from header
* Added nofollow to all meta links
* "Away Mode" page now displays current time even when feature has not been enabled
* Status page now shows system information
* htaccess contents moved to status page
* fixed fatal activation error affecting php 5.2 users

= 1.5 - May 8, 2011 =
* Meta links update correctly when changing backend links

= 1.4 - May 4, 2011 =
* Fixed another issue that prevented the "htaccess" options page from displaying on some hosts

= 1.3 - May 4, 2011 =
* Fixed an issue that prevented the "htaccess" options page from displaying on some hosts

= 1.2 - Apr 12, 2011 =
* Finished support for localization

= 1.1 - Apr 12, 2011 =
*Fixed bug that prevented cleaning old lockouts from database

= 1.0 - Apr 6, 2011 =
* More code documentation
* Added warnings to changing content directory (until I can find a good way to update all existing content)
* Added options to clean old entries out of the database
* Fixed minor typos throughout

= 0.16.BETA - Mar 8, 2011 =
* Updated Homepage

= 0.15.BETA - Mar 5, 2011 =
* Fixed error for potential conflicts with old htaccess rules

= 0.14.BETA - Mar 5, 2011 =
* Removed hotlinking protection as it has been deemed to be outside the scope of this project
* Removed protocol from hide backend htaccess rules for consistency between http and https
* Combined all httaccess rules into single Better WP Security Block
* 404 check now ignores all logged in users

= 0.13.BETA - Feb 11, 2011 =
* Fixed a bug that could erase part of the wp-config file

= 0.12.BETA - Feb 11, 2011 =
* Changing content directories should no longer break sites that were upgraded from versions prior to 3.0

= 0.11.BETA - Feb 8, 2011 =
* Update to project homepage and other minor changes

= 0.10.BETA - Feb 6, 2011 =
* Removed WP version check from status page as it was redundant
* On uninstall wp-content location will be returned to default
* Fixed setup error
* Error checking now correctly identifies database table prefix
* Rendom version # generator now removes version number from scripts and css where it can (thanks to Dave for this)

= 0.9.BETA - Jan 11, 2011 =
* Bug fixes
* Internationalization improvements

= 0.8.BETA - Dec 2, 2010 =
* Fixed more critical bugs

= 0.7.BETA - Dec 2, 2010 =
* Fixed more critical bugs

= 0.6.BETA - Dec 2, 2010 =
* Fixed 2 critical bugs

= 0.5.BETA - Dec 2, 2010 =
* Major refactoring
* Streamline database tables
* Numerous bugfixes
* Code documentation and continued internationalization prep

= 0.4.BETA - Dec 1, 2010 =
* Changed the main menu name to "Security"
* Minimum requirement raised to 3.0.2
* Begun code documentation and intl prep

= 0.3.BETA - Nov 21, 2010 =
* Numerous bugfixes
* 404 check will NOT ban logged in users
* Lockdown rules no longer apply to logged in users

= 0.2.BETA - Nov 15, 2010 =
* Updated hidebe to handle standard logout links
* Numerous other bugfixes

= 0.1.BETA - Nov 7, 2010 = 
* Finished status reporting
* Force SSL for admin pages (on supporting servers)
* Change wp-content path

= ALPHA 11 - Nov 6, 2010 =
* Added security checklist
* Added option to rename existing admin account
* Added option to change DB table prefix
* Various bugfixes

= ALPHA 10 - Nov 3, 2010 =
* Added more htaccess security options
* All htaccess options have been moved to their own page
* Added simple intrusion detection based on 404s
* Bugfixes and code optimization

= ALPHA 9 - Nov 2, 2010 =
* Deactivating now removes all htaccess areas and turns off associated options
* Enforce strong passwords for all users of a given minimum role
* Minor bug fixes

= ALPHA 8 - Nov 1, 2010 =
* Added various .htaccess options to strengthen file security
* Modified "hide backend" rewrite rules to work with multi-site
* Removed non-security hide-backend options
* Various bug fixes 
* Renamed "General" options page to "System Tweaks" to avoid confusion
* Added more options to clean up Wordpress headers
* Added options to remove plugin notifications from non-super admin users

= ALPHA 7 - Oct 31, 2010 =
* Continued code refactoring and bug-fixes
* Improved error handling and upgrade support
* Combined status and support options pages

= ALPHA 6 - Oct 30, 2010 =
* Added sanitization and validation to user input
* Added "away mode" to limit backend access by time
* Script no longer dies when logged out and instead returns to homepage.

= ALPHA 5 - Oct 28, 2010 =
* Complete refactor of the existing code
* Divided settings sections for better UX
* Added htaccess checks
* Redesigned options system for less database calls
* Reduced table usage from 4 to 2
* Added email notifications for login limiter
* Added complete access blocker for login limiter

= ALPHA 4 - Oct 26, 2010 =
* Added login limiter to limit invalid attempts
* various Bug fixes

= ALPHA 3 - Oct 25, 2010 =
* Corrected error display
* Added registration rules regardless of whether registrations are on or off.
* Added "Display random version to non-admins"
* Fixed rewrite rules on hide admin urls so going to the admin slug will work whether the user is logged in or not
* Added crude upgrade warning to warn of old (not so great) rewrite rules

= ALPHA 2 - Oct 24, 2010 =
* Optimized and commented code
* Added uninstall function
* Numerous fixes to bugs and logic

= 0.1 ALPHA - Oct 22, 2010 =
First alpha release including simple featureset. 

== Support ==

Please visit the <a href="http://www.chriswiegman.com/projects/better-wp-security/">Homepage</a> for support and more
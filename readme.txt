
=== Better WP Security ===
Contributors: ChrisWiegman
Donate link: http://www.chriswiegman.com/projects/wordpress/better-wp-security/
Tags: security, login, lockdown
Requires at least: 3.0.1
Tested up to: 3.0.1
Stable tag: ALPHA 5

A collection of numerous security fixes and modifications to help protect a standard wordpress installation.

== License ==  
Released under the terms of the GNU General Public License. 

== Description ==

NOTE: This plugin is not yet ready for production and should be used only to test features and provide feedback!!!!

Current features:
* Ban individual IP addresses from the Wordpress backend
* Remove generator tag
* Remove login error messages
* Change backend urls such as wp-login, wp-admin, and more
* Display a random version number to non administrative users anywhere version is used (often attached to plugin resources such as scripts and style sheets)
* Lockout host or user after too many invalid login attempts
	
Features coming soon:
* Too many to list right now

== Installation ==

Coming soon

== Frequently Asked Questions ==

Coming soon

== Changelog ==

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
----This realease contains no new features----

= 0.1 ALPHA - October 22, 2010 =
First alpha release including simple featureset. 

== Upgrade Notice ==

= ALPHA 5 = 
* This is a major reworking of the existing code. Please check all settings after installing

= ALPHA 4=
* Upgrade should work fine through wordpress updater. Just make a database backup first

= ALPHA 3 =
* Do to a change in the mod-rewrite rules you MUST re-save options

= ALPHA 2 =
* Now removes .htaccess rules on uninstall
* more error checking for stability

== Support ==

Please visit the <a href="http://www.chriswiegman.com/projects/wordpress/better-wp-security/">Homepage</a> for support
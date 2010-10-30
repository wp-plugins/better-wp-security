
=== Better WP Security ===
Contributors: ChrisWiegman
Donate link: http://www.chriswiegman.com/projects/wordpress/better-wp-security/
Tags: security, login, lockdown
Requires at least: 3.0.1
Tested up to: 3.0.1
Stable tag: ALPHA 3

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
	
Features coming soon:
* Too many to list right now

== Installation ==

Coming soon

== Frequently Asked Questions ==

Coming soon

== Changelog ==

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

= ALPHA 3 =
* Do to a change in the mod-rewrite rules you MUST re-save options

= ALPHA 2 =
* Now removes .htaccess rules on uninstall
* more error checking for stability

== Support ==

Please visit the <a href="http://www.chriswiegman.com/projects/wordpress/better-wp-security/">Homepage</a> for support
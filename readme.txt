
=== Better WP Security ===
Contributors: Bit51
Donate link: http://bit51.com/software/better-wp-security/
Tags: security, secure, multi-site, network, mu, login, lockdown, htaccess, hack, header, cleanup, ban, restrict, access, protect, protection, disable, images, image, hotlink, admin, username, database, prefix, wp-content, rename, directory, directories, secure, SSL
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 2.16

Helps secure Wordpress by protecting your single or multi-site installation from attackers. Hardens standard Wordpress security by hiding vital areas of your site, protecting access to important files via htaccess, preventing brute-force login attempts, detecting attack attempts, and more.

== License ==  
Released under the terms of the GNU General Public License. 

== Description ==

= #1 WORDPRESS SECURITY PLUGIN =

Better WP Security takes the best Wordpress security features and techniques and combines them in a single plugin thereby ensuring that as many security holes as possible are patched without  having to worry about conflicting features or the possibility of missing anything on your site.

= Current features =

1. Remove the meta "Generator" tag
2. Removes login error messages
3. Change the urls for backend functions including login, admin, and more
4. Limit admin access to specified IP or range of IP addresses
5. Ban troublesome bots and other hosts
6. Completely turn off the ability to login for a given time period (away mode)
7. Prevent brute force attacks by banning hosts and users with too many invalid login attempts
8. Display a random version number to non administrative users anywhere version is used (often attached to plugin resources such as scripts and style sheets)
9. Remove theme, plugin, and core update notifications from users who do not have permission to update them (useful on multisite installations)
10. Remove Windows Live Write header information
11. Remove RSD header information
12. Strengthen .htaccess settings
13. Enforce strong passwords for all accounts of a configurable minimum role
14. Detect attempts to attack your site
15. Rename "admin" account
16. Security checker
17. Change the Wordpress database table prefix
18. Force SSL for admin pages (on supporting servers)
19. Change wp-content path
20. Turn off file editing from within Wordpress admin area
20. Works on multi-site (network) and single site installations

= Translations =
* Bahasa Indonesia (Indonesian) by <a href="http://dhany.web.id/panduan-seo">Belajar SEO, Jasa SEO Indonesia</a>
* French by Claude ALTAYRAC
* German by <a href="www.smeier.biz">Stefan Meier</a>
* Italian by <a href="http://www.polslinux.it">Paolo Stivanin</a>
* Romanian by <a href="http://noblecom.com">Luke Tyler</a>

= More Information =
* <a href="http://bit51.com/2011/09/fixing-better-wp-security-lockouts/">Fixing Better WP Security Lockouts</a>
* <a href="http://bit51.com/2011/09/what-is-changed-by-better-wp-security/">What is Changed By Better WP Security</a>

= Warning =
Please read the installation instructions and FAQ before installing this plugin. It makes some significant changes to your database and other site files which, without a proper backup, can cause problems if something goes wrong. While problems are rare, most (not all) support requests I get for this plugin involve the users failure to make a proper backup before installing.

== Installation ==

1. Backup your Wordpress database, config file, and .htaccess file
2. Upload the zip file to the `/wp-content/plugins/` directory
3. Unzip
4. Activate the plugin through the 'Plugins' menu in WordPress
5. Visit the Better security menu for checklist and options

NOTE: It is quite possible (maybe even probable) that something will break due to the complexity of the changes made by this plugin. That said, under no circumstances do I release this plugin with any warranty, implied or otherwise, and at no time will I take any responsibility for any damage that might arise from the use of this plugin. REMEMBER TO ALWAYS BACKUP BEFORE TRYING NEW SOFTWARE!

== Frequently Asked Questions ==

= Will this completely stop all attacks on my site? =
* Of course not. Better WP Security is designed to help improve the security of your WordPress installation from many common attack methods. It can no way prevent every possible attack on your website. Nothing replaces diligence and good practice. This plugin just makes it a little easier for you to apply both.

= Is this only for new sites or can I use it on existing sites too? =
* Many of the changes made by this plugin are complex and can break existing sites. While it can be installed in either a new or existing site I cannot stress enough the importance of making a backup of your existing site before applying any of the options in this plugin.

= Will this work on all servers and hosts? =
* Better WP Security requires Apache and mod_rewrite to work. That said, I am considering a port for nginx but doing so is not currently something I have the time for.
* While this plugin should work on all hosts with Apache and mod_rewrite it has been known to experience problems in shared hosting environments where it runs out of resources such as available CPU or RAM. For this reason it is extremely important that you make a backup of your site before installing on any existing site as, if you run out of resources during an operation such as renaming your database table, you may need your backup to be able to restore access to your site.

= Are you still developing this plugin? =
* Yes. The functionality of this plugin is a requirement of my job so this plugin will continue to be developed.

= Does this work with netowork or multisite installations? =
* Yes, as of version 1.7 it should work fine with multisite installations.

= Can I help? =
* Of course! I am in constant need of testers and I would be more than happy to add the right contributor. In addition, I could always use help with translations for internationalization.

= Will this break my site? =
* It is quite possible (maybe even probable) that something will break. That said, under no circumstances do I release this plugin with any warranty, implied or otherwise, and at no time will I take any responsibility for any damage that might arise from the use of this plugin. REMEMBER TO ALWAYS BACKUP BEFORE TRYING NEW SOFTWARE!
* Note that renaming the wp-content directory will not update the path in existing content. Use this feature only on new sites or in a situation where you can easily update all existing links.

= I've enabled the Enforce SSL option and it broke my site. How do I get back in? =
* Open your wp-config.php file in a text editor and remove the following 2 lines:
* define('FORCE_SSL_LOGIN', true);;
* define('FORCE_SSL_ADMIN', true);;

== Changelog ==

<a href="http://bit51.com/software/better-wp-security/changelog/">Click here for the full change log</a>

== Support ==

Please visit the <a href="http://bit51.com/software/better-wp-security/">Homepage</a> for support and more
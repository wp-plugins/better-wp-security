
=== Better WP Security ===
Contributors: Bit51
Donate link: http://bit51.com/software/better-wp-security/
Tags: security, secure, multi-site, network, mu, login, lockdown, htaccess, hack, header, cleanup, ban, restrict, access, protect, protection, disable, images, image, hotlink, admin, username, database, prefix, wp-content, rename, directory, directories, secure, SSL
Requires at least: 3.3.1
Tested up to: 3.3.1
Stable tag: 3.0.13

The easiest, most effective way to secure your WordPress site from attackers. Improve the security of any WordPress site in seconds.

== License ==  
Released under the terms of the GNU General Public License. 

== Description ==

= #1 WORDPRESS SECURITY PLUGIN =

Better WP Security takes the best Wordpress security features and techniques and combines them in a single plugin thereby ensuring that as many security holes as possible are patched without  having to worry about conflicting features or the possibility of missing anything on your site.

With one-click activation for most features as well as advanced features for experienced users Better WP Security can help protect any site.

= Current features =

1. Scan your site to instantly tell where vulnerabilities are and fix them in seconds
2. Remove the meta "Generator" tag
3. Removes login error messages
4. Change the urls for backend functions including login, admin, and more
5. Create and email database backups on a schedule using wp-cron
6. Ban troublesome bots and other hosts
7. Completely turn off the ability to login for a given time period (away mode)
8. Prevent brute force attacks by banning hosts and users with too many invalid login attempts
9. Display a random version number to non administrative users anywhere version is used (often attached to plugin resources such as scripts and style sheets)
10. Remove theme, plugin, and core update notifications from users who do not have permission to update them (useful on multisite installations)
11. Remove Windows Live Write header information
12. Remove RSD header information
13. Strengthen server settings
14. Enforce strong passwords for all accounts of a configurable minimum role
15. Detect attempts to attack your site
16. Rename "admin" account
17. Security checker
18. Change the Wordpress database table prefix
19. Force SSL for admin pages (on supporting servers)
20. Change wp-content path
21. Turn off file editing from within Wordpress admin area
22. Works on multi-site (network) and single site installations
23. Works with Apache or NGINX

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

= Is "one-click" protection good enough? =
* While one-click protection will go a long way to helping reduce the risk of attack on your site, the more features you can activate the better off you are. If you have a plugin or theme that conflicts with a feature of Better WP Security then just turn off the offending feature. It is better to use as much as you can than to not use anything at all.

= Is this only for new sites or can I use it on existing sites too? =
* Many of the changes made by this plugin are complex and can break existing sites. While it can be installed in either a new or existing site I cannot stress enough the importance of making a backup of your existing site before applying any of the options in this plugin.

= Will this work on all servers and hosts? =
* Better WP Security requires Apache and mod_rewrite or NGINX to work.
* While this plugin should work on all hosts with Apache and mod_rewrite or NGINX it has been known to experience problems in shared hosting environments where it runs out of resources such as available CPU or RAM. For this reason it is extremely important that you make a backup of your site before installing on any existing site as, if you run out of resources during an operation such as renaming your database table, you may need your backup to be able to restore access to your site.

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

== Screenshots ==

1. Instantly scan your site and see where you can improve your security.
2. One-click access to most features can make securing your site easy
3. Simple informative options panels show you what you need to know about each and every option
4. If you do get stuck help is never more than a few clicks away.

== Changelog ==

= 3.0.13 =
* Security fix for XSS vulnerability. Thanks to Ole Aass (@oleaass) for finding and disclosing this vulnerability to the iThemes Security team.

= 3.0 =
* Now works with NGINX

<a href="http://bit51.com/software/better-wp-security/changelog/">Click here for the full change log</a>

== Support ==

Please visit the <a href="http://bit51.com/software/better-wp-security/">Homepage</a> for support and more
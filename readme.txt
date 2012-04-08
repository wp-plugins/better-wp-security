
=== Better WP Security ===
Contributors: Bit51
Donate link: http://bit51.com/software/better-wp-security/
Tags: security, secure, multi-site, network, mu, login, lockdown, htaccess, hack, header, cleanup, ban, restrict, access, protect, protection, disable, images, image, hotlink, admin, username, database, prefix, wp-content, rename, directory, directories, secure, SSL
Requires at least: 3.3.1
Tested up to: 3.4-beta1
Stable tag: 3.2.2

The easiest, most effective way to secure WordPress. Improve the security of any WordPress site in seconds.

== License ==  
Released under the terms of the GNU General Public License. 

== Description ==

= #1 WORDPRESS SECURITY PLUGIN =

Better WP Security takes the best Wordpress security features and techniques and combines them in a single plugin thereby ensuring that as many security holes as possible are patched without having to worry about conflicting features or the possibility of missing anything on your site.

With one-click activation for most features as well as advanced features for experienced users Better WP Security can help protect any site.

= Obscure =

As most WordPress attacks are a result of plugin vulnerabilities, weak passwords, and obsolete software Better WP Security will hide the places those vulnerabilities live keeping an attacker from learning too much about your site and keeping them away from sensitive areas like login, admin, etc.

* Remove the meta "Generator" tag
* Change the urls for WordPress dashboard including login, admin, and more
* Completely turn off the ability to login for a given time period (away mode)
* Remove theme, plugin, and core update notifications from users who do not have permission to update them
* Remove Windows Live Write header information
* Remove RSD header information
* Rename "admin" account
* Change the Wordpress database table prefix
* Change wp-content path
* Removes login error messages
* Display a random version number to non administrative users anywhere version is used

= Protect =

Just hiding parts of your site is helpful but won't stop everything. After we hide sensitive areas of the sites we'll protect it by blocking users that shouldn't be there and increasing the security of passwords and other vital information.

* Scan your site to instantly tell where vulnerabilities are and fix them in seconds
* Ban troublesome bots and other hosts
* Ban troublesome user agents
* Prevent brute force attacks by banning hosts and users with too many invalid login attempts
* Strengthen server security
* Enforce strong passwords for all accounts of a configurable minimum role
* Force SSL for admin pages (on supporting servers)
* Force SSL for any page or post (on supporting servers)
* Turn off file editing from within Wordpress admin area

= Detect =

Should all the protection fail Better WP Security will still monitor your site and report attempts to scan it (automatically blocking suspicious users) as well as any changes to the filesystem that might indicate a compromise.

* Detect bots and other attempts to search for vulnerabilities
* Monitor filesystem for unauthorized changes

= Recover =

Finally, should the worst happen Better WP Security will make regular backups of your WordPress database (should you choose to do so) allowing you to get back online quickly in the event someone should compromise your site.

* Create and email database backups on a customizable schedule

= Other Benefits =

* Make it easier for users to log into a site by giving them login and admin URLs that make more sense to someone not accustomed to WordPress
* Detect hidden 404 errors on your site that can affect your SEO such as bad links, missing images, etc.

= Compatibility =

* Works on multi-site (network) and single site installations
* Works with Apache, LiteSpeed or NGINX

= Configuration =

Configuration is easy, but there are a lot of options. The video below will walk you through everything Better WP Security can do.

[youtube http://www.youtube.com/watch?v=Jveq2H4bZJY]

= Translations =
* Bahasa Indonesia (Indonesian) by <a href="http://dhany.web.id/panduan-seo">Belajar SEO, Jasa SEO Indonesia</a>
* French by Claude ALTAYRAC
* German by <a href="www.smeier.biz">Stefan Meier</a>
* Italian by <a href="http://www.polslinux.it">Paolo Stivanin</a>
* Romanian by <a href="http://noblecom.com">Luke Tyler</a>

Note the translations are only tested up to version 3.0. If you would like to contribute a translation to the current version please <a href="http://bit51.com/contact/" title="Bit51 contact form">let me know</a>.



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
* <a href="http://bit51.com/fixing-better-wp-security-lockouts/">Fixing Better WP Security Lockouts</a>
* <a href="http://bit51.com/what-is-changed-by-better-wp-security/">What is Changed By Better WP Security</a>

= I've enabled the Enforce SSL option and it broke my site. How do I get back in? =
* Open your wp-config.php file in a text editor and remove the following 2 lines:
* define('FORCE_SSL_LOGIN', true);
* define('FORCE_SSL_ADMIN', true);

= Where can I get help if something goes wrong? =
* First of all please note that I don't monitor the forums on WordPress.org. Second, as I do not have a support staff and this plugin is one of many projects I am involved with I do not guarantee support at all. There is no warranty and if something goes wrong I make no promise of assistance. That said, I haven't lost a site yet and I usually respond to all queries on the Bit51 forums at <a href="http://forums.bit51.com">http://forums.bit51.com</a>

== Screenshots ==

1. Instantly scan your site and see where you can improve your security.
2. One-click access to most features can make securing your site easy
3. Simple informative options panels show you what you need to know about each and every option
4. If you do get stuck help is never more than a few clicks away.

== Changelog ==

<a href="http://bit51.com/software/better-wp-security/changelog/">Click here for the full change log</a>

== Support ==

* Please visit the <a href="http://bit51.com/software/better-wp-security/">Homepage</a> for general plugin information
* Please visit the <a href="http://forums.bit51.com">Bit51.com forums for support</a>

Note that I do not monitor the plugin forums at WordPress.org thus no support will be given via those forums.

== Upgrade Notice ==

Some users have reported issues when upgrading from version 2.x to version 3.x. While these issues are rare you can avoid them entirely by completely uninstalling version 2.x and then installing a fresh copy of version 3.x.

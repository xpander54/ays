=== Private! Wordpress Access Control Manager ===
Contributors: X-Blogs
Donate link: http://plugins.x-blogs.org/private
Tags: privacy, private, closed, restricted, restriction, registered only, posts, pages, categories, tags, family blog, intranet, login, access, remove feed, password, moderation, protect, brute force, Generator, tarpit, secure, security
Requires at least: 3.0.0
Tested up to: 3.0.1
Stable tag: 1.7.0

Manage easily, who is allowed to access your blog or certain parts of your blog or just improve the security of your installation

== Description ==

Private! makes it possible to build private blogs for your friends, family or your business.

**But Private! can do much more for you!**

= Common Features =
The configuration is as simple as powerful and the best of all: It's incredible fast - you will not
notice any differences from your standard installation to running a fully protected blog. It does
not modify your roles, capabilities and it does not need new tables or adds queries to your normal pages.

= Content Restrictions =
You can use Private! to protect paid content on your page. Lock down your blog and decide who can
access your front page, blog page, content pages, category archive, tag archive, search, feeds and
so on. You can allow full access to your blog and restrict the access to posts in single or multiple
categories and/or tags. You can allow access to certain posts even, if they are in protected
categories or tags. You can give your users access to restricted areas for years or seconds. It's
up to you!

= Security =
Improve the security of your blog by customizing your login failure messages, prevent brute force, XSS,
SQL, Field Truncation, Session Hijacking and more attacks, slow down attackers, force your users to
use strong passwords or just add or remove RSD/WLW/Generator Tags from your blog and/or the blogs
readme.html. And this is the only plugin, that can improve the security network wide. You can
force every blogs in you network to use your preferred settings by default.

= Multisite Enhancements =
Users can now register directly on single blogs in multisite installations. Network admins can force
users to stay related to the main blog. Both options must be approved in the private.php. Remember
the plugin must be installed in the main blog too or you have to activate the plugin as network plugin.

= Translations =
English, german and brazilian portugese translations are included. This is my first wordpress plugin.
Please tell me about bugs and errors or send me translations.


= Features =
* Easy setup assistant - Configure the plugin with a few clicks
* Easy to use ajax featured admin pages
* Restrict access to your whole blog or to certain features, like archives, search or single posts
* Allow or deny access to certain categories or tags or to single posts
* Allow access by Roles, Capabilities or Userlevels or mix them all
* Redirect unauthorized users to internal or external pages
* Define how long a user can access your pages
* Full I18n support
* Remove all entries from the database with just one (maybe two) click(s)
* Remove restricted posts from front page, blog home, search etc.
* Remove categories from your sidebar

= Security Features =
* Send or remove extra headers for WLW or RSD capable blog clients
* Customizable login error messages
* Force your users to use strong passwords
* Prevent most common XSS/SQL/Field Truncation/File Upload Injection Attacks
* Remove version from or the whole Generator Tag
* Remove the readme.html from the blog root
* Remove feeds from your blog header for non-authorized users
* Access feeds with special generated urls
* Encrypt (Triple DES) your password with javascript before sending login informations
* Brute Force Protection
* Tarpit Protection
* Define maximum login attempts or lockout time for attackers
* Restrict your admin to your current ip if you use a static adress

= Multisite Features =
* Full multisite compatible
* Define if all logged in users can access your pages or just those, who are related to your blog
* Allow users to register directly to your blog without inviting them or let them subscribe to your blog in their user profile
* Set your security settings blog network wide

= Translations =
* en_US (english)
* de_DE (deutsch) 
* pt_BT (brazilian portugese) [Thanks to Eduardo]

** This plugin requires PHP 5.1 or newer **

== Installation ==

1. Upload `private` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure who should be allowed to access your blog or parts of your blogs
4. Decide for every user, how long a user should be able to see restricted areas (optional)

== Frequently Asked Questions ==

= I cannot access my blog? =
Please read the descriptions. This plugin is very restrictive. If you just click enable, you will not 
be able to access your blog. But the good news is, the plugin will not lock you out, click on personal 
settings and give yourself an authorization timestamp in the future and you are back in business.

= Is it compatible to cache plugins? =
It should be. Tell your caching script, that it should not cache logged in users (that should be the default) 
and clear the cache after configuration. Regular pages for guests should be cached. Restricted areas should 
not be cached, as the user must be logged in.

= Why does Plugins like "Ultimate Security Check" keep telling me, my blog is still hackable with malicious URL requests? =
Because the test is somehow stupid. It's not enough to send a single "eval" or "base64" command without any 
parameters to your blog to hack it, but exactly this is simulated. You will have to send special url requests, 
that match more criterias than that. If you just deny access to urls, that have "eval", "base64" or "select union" 
in it, you will run into big problems e. g. you are a SQL Expert or Developer and want to write about these functions. 

== Screenshots ==

1. *Interface in English*

2. *Oberfläche in Deutsch*

3. *Setup assistant*

4. *Modified login*

5. *Let users add to single blogs in multisite installations*

== Changelog ==

= 1.7.0 =
* Added: Option to display an error message, instead of redirecting users to the login or to another page
* Added: Option to display 404 error page instead of a Bad Request error
* Added: Log all potential malicious url requests (A viewer will be added in one of the next versions)
* Added: Block requests longer than 255 chars
* Added: Block requests containing your db prefix
* Added: Block file uploads with dangerous file endings
* Added: Block directory traversal attacks
* Added: Force security settings on all member blogs in a multisite environment
* Added: Ability to remove readme.html from blog root
* Added: Protect your Plugins and Includes Folder by creating index.php files in these folders
* Added: Capability setting for editing auth time
* Added: Restrict the Admin to your current ip
* Changed: Split detection of xss/sql Attacks
* Changed: Standard capability for editing users changed to edit users
* Fixed: Redirect to login does not work correctly in some situations
* Fixed: Plugin link is now shown, even if there are no restrictions

= 1.6.5 =
* Fixed: Typo in login, signup condition, that might give access for anyone [Thanks to sulfsby]

= 1.6.4 =
* Fixed: PHP Warning when not hiding tags

= 1.6.3 =
* Added: Multisite registration to single blogs is not restricted to subdomain installations anymore
* Added: Tags can also be hidden from the Tag cloud widget
* Changed: Moved multisite options down on the settings page
* Fixed: Register page stays open, even if all pages are restricted

= 1.6.2 =
* Added: Brazilian Portugese language file [Big big thanks to Eduardo]
* Fixed: Error in check_auth, which prevents logged in user to see hidden categories [Sorry to Sabeth]

= 1.6.1 =
* Fixed: Translation issue in the main menu [Big thanks to Eduardo]

= 1.6.0 =
* Added: Allow users to register directly to a blog in the multisite network if webmaster allows [Thanks to Aphrodite for the suggestion]
* Added: Ability to add directly to a blog for users if webmaster allows [Thanks to Aphrodite for the suggestion]
* Added: PHP Version check for really really old installations [Thanks for lauryn]
* Fixed: auth date was taken from current user and not from shown user
* Fixed: admin warnings are restricted to admins now
* Fixed: update notices are restricted to admins now

= 1.5.0 =
* Added: Remove restricted posts from front page, search, blog home etc. for non authorized users based on restricted categories, tags or posts
* Added: Remove categories from sidebar

= 1.4.1 =
* Fixed: Plugin does not show categories, if the first category was deleted or edited [Thanks to Sabeth and cscscs]

= 1.4.0 =
* Added: Option to remove feeds from blog headers
* Added: Generation of feed keys, so that RSS Reader can access restricted feeds
* Added: Navigation below Admin
* Added: Ability to remove feed keys from database
* Added: Force users to use only strong passwords
* Changed: Merged the access rights pages
* Fixed: Some improvements on the assistant
* Fixed: Some wrong or forgotten translations
* Fixed: A lot of typos

= 1.3.1 =
* Fixed: A small bug in des.class.php as the password is not decrypted correctly because of unneeded whitespaces

= 1.3.0 =
* Added: Easy to use setup assistant
* Added: Option to delete brute force logs
* Fixed: Added english and german translation for user profile field

= 1.2.2 =
Fixed: Wrong parameter count on form_row [Thanks to hdridder]

= 1.2.1 =
* Added: New options are marked as new
* Fixed: Secure key was not depending on your installation

= 1.2.0 =
* Added: Secure login script

= 1.1.1 =
* Fixed: wrong datatype in array [Thanks to bamajr]

= 1.1 =
* Added: New interface design
* Added: Allow access to attachments
* Added: Disable all restrictions with a single click
* Added: Customize your login error messages
* Added: Prevent against attackers with tarpit technology
* Added: Brute force prevention
* Added: Define maximum login attempts and lockout time for brute force attempts
* Added: Remove or anonymize your Wordpress Generator Meta Tag
* Added: Notification of recent updates
* Changed: Seperate settings for RSD and WLW headers
* Fixed: Some language strings
* Fixed: Some unitialized variables
* Fixed: Issue on setting the time for access allowed
* Fixed: Allowing single posts also allowed access to pages
* Fixed: logged in users may had lower rights than logged out if they were not approved
* Fixed: fixed some very complicated rules with tags and categories in single posts
* Fixed: A lot of testing with all kinds of rules

= 1.0.4 =
* Another fix in multisites
* Deleted some warnings about uninitialized vars

= 1.0.3 =
* Fixed a bug in non multisite environment

= 1.0.2 =
* Fixed a bug with deleting user settings in multisite environment

= 1.0.1 =
* A lot of small bugfixes - mainly wrong paths and forgotten translations

= 1.0 =
* Initial release - it may let explode your hamster!
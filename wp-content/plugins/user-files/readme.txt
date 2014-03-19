=== user files ===
Contributors: Innovative Solutions
Tags: User Files, File Manager, User File System, file management
Donate:https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RMCSV4J9FALZ6
Requires at least: 3.0
Tested up to: 3.4.1
Stable Tag:2.3.2

User files allows you to upload files for a specific user to download. Custom Icons and categories are available to more easily search and filter files.  

== Description ==

The user files plugin allows you to add upload files for a specific user to download or view.  The file upload is limited to the post max of your server.  Files are uploaded to a user and can be accesses via an access page, dashboard widget, or both.  The user files options page will allow you to turn on page menu and dashboard widget. Other options to allow user to delete their files and add to them. Also adds a file manager page to view all files and which user they belong to. You can delete files individually or delete the users folder.  

Files can have icons and category, both are customizable.  Users and admins can search file names or parts and admins can filter files by category and/or by user. 



== Installation ==


1. Upload `user-files.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==



== Changelog ==
=1.0.1=
*Fixed display issue for dashboard widget

=1.0.2=
*Added option to allow users to delete their files
*Added option to allow users to add files to their account (suggestion sent in by Pedro Pregnolato)
*Both features can be turned on or off in the File Manager Options settings

=1.0.6=
*Fixed user uploads page, user was unable to upload unless they already had files
*Added Shortcode to place file list in template page
*Added help page with short general information

=1.0.8=
* Added admin notification option for user uploads. Idea submitted by Aleks Berland.

=2.0=
*Added Categories for files
*Added icon support
*Fixed error opening non-existant directory
*Added file search
*Added category filter
*Added files download widget
*Added files upload widget
*Fixed shortcode using output buffer
*Added one-click download function
*Added complete uninstall function
*Added I10n, .pot file available for contributors
*css added for in page display table

=2.0.3=
*Fixed bug with custom capabilities and restored manage_options, will add custom caps at a later date. Don't have time to fix it at this time.

=2.0.5=
*Fixed bug writing categories to the database.
*Fixed issue with corrupt file downloads on wordpress 3.2
*Fixed a couple other minor bugs

=2.0.6=
*Removed echo causing header sent error messages

=2.0.7=
*Fixed error that sometimes caused a 404 error when file download was pressed 

=2.0.8=
*Fixed misspelling in upload category query 

=2.1.0=
*Fixed download issues happening on some servers. Thanks to all who reported the bugs through our site and on wordpress.org forums. Thanks to dev123 and etruel.

=2.1.1=
*Added email notification option to notify users of uploaded files.
*Added a date column to show the date the file was uploaded (ftp or wordpress)

=2.1.5=
*Added a new fix for downloads thnaks to dev123

=2.2.0=
*New download function written which seems to fix any and all corrupt downloads

=2.2.1=
*file corruption during sync, we're going to forget this update happened

=2.2.2=
*Mail notification fix
*Fix for file corruption due to error with sync software

=2.3=
* Mail notification fix (for real)
* Increased limit on category name lenth
* added notes for files
* added security for file downloads

=2.3.1=
*fixed help file 

=2.3.2=
*fixed permalink uploading issue
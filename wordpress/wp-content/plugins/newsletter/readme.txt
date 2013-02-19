=== Newsletter ===
Tags: newsletter,email,subscription,mass mail,list build,email marketing,direct mailing
Requires at least: 3.0.0
Tested up to: 3.5
Stable tag: trunk
Donate link: http://www.satollo.net/donations

Add a real newsletter to your blog. In seconds. For free.

== Description ==

This plug-in lets you collect subscribers on your blog with a single or double opt-in (law compliant)
subscription process. Perfect for list building, you can create cool emails with visual editor, send and
track them.

Unlimited subscribers, unlimited emails.

Key features:

* unlimited subscribers (the database is your, why should I limit you?)
* unlimited emails
* single and double opt-in plus privacy acceptance checkbox (as per European laws)
* subscriber preferences to fine target your campaigns
* SMTP ready
* html and text version messages
* a real delivery engine to manage huge lists with configurable speed
* configurable themes
* easy to develop themes (for coders)
* every message fully translatable from administrative panels
* diagnostic panel

Visit the [Newsletter official page](http://www.satollo.net/plugins/newsletter).

Previous version is available [here](http://www.satollo.net/wp-content/uploads/newsletter-2.5.2.7.zip).

Thank you, Stefano Lissa (Satollo).

== Installation ==

1. Put the plug-in folder into [wordpress_dir]/wp-content/plugins/
2. Go into the WordPress admin interface and activate the plugin
3. Optional: go to the options page and configure the plugin

== Frequently Asked Questions ==

See the [Newsletter FAQ](http://www.satollo.net/plugins/newsletter/newsletter-faq) or the
[Newsletter Forum](http://www.satollo.net/forums).

For documentation start from [Newsletter official page](http://www.satollo.net/plugins/newsletter).

Thank you, Stefano Lissa (Satollo).

== Screen shots ==

No screen shots are available at this time.

== Changelog ==

= 3.1.0 (not released) =

* Added link to change preferences/sex from emails
* Added tag reference on email composer
* Added "negative" preference selection on email targeting
* Improved the subscription during WordPress user registration

= 3.0.7 =

* Fixed a warning in WP 3.5
* Fixed the visual editor on/off on composer panel

= 3.0.6 =

* Added file permissions check on diagnostic panel
* Fixed the default value for "sex" on email at database level
* Fixed the checking of required surname
* Fixed a warning on subscription panel
* Improved the subscription management for bounced or unsubscribed addresses
* Removed the simple theme of tinymce to reduce the number of files
* Added neutral style for subscription form

= 3.0.5 =

* Added styling for widget
* Fixed the widget html
* Fixed the reset button on subscription panels
* Fixed the language initialization on first installation
* Fixed save button on profile page (now it can be an image)
* Fixed email listing showing the planned status

= 3.0.4 =

* Fixed the alternative email template for subscription messages
* Added user statistics by referrer (field nr passed during subscription)
* Added user statistics by http referer (one r missing according to the http protocol)
* Fixed the preview for themes without textual version
* Fixed the subscription redirect for blogs without permalink
* Fixed the "sex" column on database so email configuration is correctly stored
* Fixed the wp user integration

= 3.0.3 =

* Fixed documentation on subscription panel and on subscription/page.php file
* Fixed the statistics module URL rewriting
* Fixed a "echo" on module.php datetime method
* Fixed the multi-delete on newsletter list
* Fixed eval() usage on add_menu_page and add_admin_page function
* Fixed a number of ob_end_clean() called wht not required and interfering with other output buffering
* Fixed the editor access level

= 3.0.2 =

* Documented how to customize the subscription/email.php file (see inside the file) for subscription messages
* Fixed the confirmation message lost (only for who do not already save the subscription options...)

= 3.0.1 =

* Fixed an extra character on head when including the form css
* Fixed the double privacy check on subscription widget
* Fixed the charset of subscription/page.php
* Fixed the theme preview with wp_nonce_url
* Added compatibility code for forms directly coded inside the subscription message
* Added link to composer when the javascript redirect fails on creation of a new newsletter
* Fixed the old email list and conversion

= 3.0.0 =

* Release

= 2.6.2 =

* Added the user massive management panel

= 2.5.3.3 =

* Updated to 20 lists instead of 9
* Max lists can be set on wp-config.php with define('NEWSLETTER_LIST_MAX', [number])
* Default preferences ocnfigurable on subscription panel

= 2.5.3.2 =

* fixed the profile fields generation on subscription form

= 2.5.3.1 =

* fixed javascript email check
* fixed rewrite of link that are anchors
* possible patch to increase concurrency detection while sending
* fixed warning message on email composer panel

= 2.5.3 =

* changed the confirmation and cancellation URLs to a direct call to Newsletter Pro to avoid double emails
* mail opening now tracked
* fixed the add api
* feed by mail settings added: categories and max posts
* feed by mail themes change to use the new settings
* unsubscribed users are marked as unsubscribed and not removed
* api now respect follow up and feed by mail subscription options
* fixed the profile form to add the user id and token
* subscribers' panel changed
* optimizations
* main url fixed everywhere
* small changes to the email composer
* small changes to the blank theme

= 2.5.2.3 =

* subscribers panel now show the profile data
* search can be ordered by profile data
* result limit on search can be specified
* {unlock_url} fixed (it was not pointing to the right configured url)

= 2.5.2.2 =

* fixed the concurrent email sending problem
* added WordPress media gallery integration inside email composer

= 2.5.2.1 =

* added the add_user method
* fixed the API (was not working) and added multilist on API (thankyou betting-tips-uk.com)
* fixed privacy check box on widget

= 2.5.2 =

* added compatibility with lite cache
* fixed the list checkboxes on user edit panel
* removed the 100 users limit on search panel
* category an max posts selection on email composer

= 2.5.1.5 =

* improved the url tag replacement for some particular blog installation
* fixed the unsubscription administrator notification
* replaced sex with gender in notification emails
* fixed the confirm/unconfirm button on user list
* fixed some labels
* subscription form table HTML

= 2.5.1.4 =

* added {date} tag and {date_'format'} tag, where 'format' can be any of the PHP date formats
* added {blog_description} tag
* fixed the feed reset button
* added one day back button to the feed
* updated custom forms documentation
* fixed the trigger button on emails panel
* changed both feed by mail themes (check them if you create your own theme)
* fixed the custom profile field generation (important!)
* fixed documentation about custom forms

Version 2.5.1.3
- fix the feed email test id (not important, it only generates PHP error logs)
- feed by mail send now now force the sending if in a non sending day
- changed the way feed by mail themes extract the posts: solves the sticky posts problem
- added the feed last check time reset button
- fixed the confirm and cancel buttons on user list
- fixed the welcome email when using a custom thank you page
- added images to theme 1
- added button to trigger the delivery engine
- fixed the widget mail check
- reintroduced style.css for themes
- updated theme documentation
- added CDATA on JavaScript
- fixed theme 1 which was not adding the images
- added theme 3

Version 2.5.1.2
- fixed the old profile fields saving

Version 2.5.1.1
- new fr_FR file
- fixed test of SMTP configuration which was sending to test address 2 instead of test address 1
- bounced voice remove on search filter
- added action "of" which return only the subscription form and fire a subcription of type "os"
- added action "os" that subscribe the user and show only the welcome/confirmation required message
- fixed issue with main page url configuration

Version 2.5.1
- Fixed the widget that was not using the extended fields
- Fixed the widget that was not using the lists
- Added the class "newsletter-profile" and "newsletter-profile-[number]" to the widget form
- Added the class "newsletter-profile" and "newsletter-profile-[number]" to the main subscription form
- Added the class "newsletter-profile" and "newsletter-profile-[number]" to the profile form
- Added the classes "newsletter-email", "newsletter-firstname", "newsletter-surname" to the respective fields on every form
- Removed email theme option on subscription panel (was not used)
- Fixed the welcome email on double opt in process
- Subscription notifications to admin only for confirmed subscription
- Fixed subscription process panel for double opt in (layout problems)
- Improved subscription process panel


Version 2.5.0.1
- Fix unsubscription process not working

Version 2.5.0
- Official first release

= SVN =

Actually I'm using SVN in a wrong way (deliberately). Usually development with SNV
should be done in this way:

* the trunk is where the latest (eventually not working code) is available
* the tags should contains some folders with public releases (stable or beta or alpha)
* the branches should contains some folders representing stable releases which are there to be eventually fixed

For example, when I released the version 3.0 of this plugin, I should have created
a 3.0 folder inside the branches and fixed it when bug were reported. From time to
time from that branch I should have created a tag, for example 3.0.4. 

Actually, to make this tag available it should have been reported on the readme.txt
committed on the trunk.

To make it easier, I keep in the trunk the 3.0 branch and I fix it committing the patches
and leaving the official stable tag on readme.txt set to "trunk". That helps me
in quick fixing the plugin without creating tags.

On branches I have the 3.1 branch where I'm develping new features and when ready to be
committed I'll merge them on trunk, updating the trunk.

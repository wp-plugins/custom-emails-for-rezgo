=== Custom Emails for Rezgo ===
Contributors: schwarzhund
Donate link: http://alexvp.elance.com
Tags:  tour operator software, tour booking system, activity booking software, tours, activities, events, attractions, booking, reservation, ticketing, e-commerce, business, rezgo, custom email, emails, notifications, web hook, api
Requires at least: 3.0.0
Tested up to: 4.2.2
Stable tag: 1.4

Send custom text or html email messages to customers when they make a booking through your Rezgo online booking engine.

== Description ==

> This plugin is completely free to use, but it requires a Rezgo account.  <a href="http://www.rezgo.com">Try Rezgo today</a> and experience the world's best hosted tour operator software.

**Rezgo** is an online booking engine for tour and activity operators that helps you manage inventory, accept reservations, and process credit card payments. This plugin allows you to send custom html or text email messages to your customers after they make a booking through your Rezgo account.


The plugin contains a web hook API endpoint that is triggered when a booking is made in your Rezgo account.  The plugin gives you the ability to create a custom email for every tour and option combination available in your account.

= Plugin features include =

* Pull current tours/activities from your Rezgo account.
* Set your own "From email" and "From Name".
* Create a custom notification for each tour/activity option.
* Supports both text and html emails.
* Logs bookings in a separate booking log.
* Logs plugin activities in a separate log.

= Support for this plugin =

This plugin was developed by AlexVP.  There is no support provided for this plugin.  It is available as-is with no guarantees.  If you would like the plugin customized or modified for your needs, please feel free to send a proposal or hire Alex through Elance.

[http://alexvp.elance.com](http://alexvp.elance.com)

== Installation ==

= Install the Rezgo Custom Email plugin =

1. Install the Rezgo Custom Email plugin in your WordPress admin by going
to 'Plugins / Add New' and searching for 'Rezgo' **OR** upload the
'wp-rezgo-custom-emailer' folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

= Plugin Configuration and Settings =

In order to use the Custom Email plugin, your Rezgo account must be activated.  This means that you **must** have a valid credit card on file with Rezgo before your plugin can connect to your Rezgo account.

1. Make sure the Custom Email plugin is activated in WordPress.
2. Add your Rezgo account CID and API KEY in the plugin settings and click the 'Save Changes' button.
3. Add your From name and email address and click the 'Save Changes' button.
4. To synchronize your tour list, click on the 	'Update Tour/Option List' button.
5. Copy your web hook endpoint URL into your Rezgo notifications settings.
6. Click on the 'Notifications' link in the side bar.
7. Click the 'Add Notification' button to create a new notification.
8. Choose a tour from the drop down list.
9. Complete the subject, text message, and/or the html message, click the 'Save Changes' button to save your new notification.  Note that the 'Save Changes' button will not appear until you have added at least a subject for your notification.
10. To test your notification, create a new front-end booking on your Rezgo account.

== Frequently Asked Questions ==

= Can I contact Rezgo for support for this plugin? =

No. Rezgo did not create this plugin and does not support it.

= I added a booking but no notification was sent, what should I do?  =

Check the Bookings and the Log in the plugin to see what error was received.  Check to make sure that your Webhook notification is set-up correctly in Rezgo.  Read this article for information on [how to create a web hook notification](http://j.mp/14CDNh7).

= Does this work for back-office or point of sale bookings? =

No, Rezgo does not support sending web hook notifications with back-office bookings.

= Can I send attachments with the notifications? =

No, attachments are not supported.  You can include links in the notifications, so it would be best to link to any documents that you want to include.

= I want the plugin to do something that it doesn't do now, who should I contact? =

You can contact Alex at Elance : [http://alexvp.elance.com](http://alexvp.elance.com)

Please note, there is NO FREE SUPPORT for this plugin.  Any changes or modifications will be charged.

== Screenshots ==

1. Once you activate the Rezgo WordPress plugin, you will need to enter 
in your Rezgo API credentials on the settings page located in your 
WordPress Admin.  Look for Rezgo Emailer in the sidebar.
2. Add a notification. You can customize the subject, text message, and html message.
3. Active notifications will appear on the Notifications page. 

== Changelog ==

= 1.4 =
* changes to edit notification
* CSS changes

= 1.3 =
* New admin styles
* Changed template files to PHP

= 1.2 =
* HTML / CSS cleanup on settings pages

= 1.1 =
* Updates to plugin info

= 1.0 =
* Initial release.

== Upgrade Notice ==

= You have the most recent version =
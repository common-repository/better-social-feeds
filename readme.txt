=== Social Feeds for Threads ===
Contributors: deepakkite, mrking2201
Tags: threads, instagram, social, feeds, social media
Requires at least: 6.0
Tested up to: 6.5.4
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display feeds from your Threads profile on your WordPress website.

== Description ==

Best WordPress threads plugin that allows admin to display feeds from Threads, an Instagram app on your website using **Threads official API**.

Threads launched their API on June 18, 2024 and we have implemented their API in this plugin. You would need to create an App from your FB developer account [here](https://developers.facebook.com/apps/) and select Threads API usecase.

Check out the live Demo [here](https://feedsforthreads.com/)

Use the shortcode **[fftmj_feeds]** to load threads from the user you connect in settings.

By default the plugin displays only 5 posts per shortcode.

Note: This plugin uses Threads API which is just launched and might not have all the data available yet but you can get the enough data to display a feed of posts that link to your Threads posts and profile. We will update the plugin as soon as more data is available through Threads API.

Plugin requirements:
- Must use SSL - https.
- Might not work with localhost.


== How to create a Threads App ==
- Create new app in FB [developer account](https://developers.facebook.com/apps/).
- Select "I dont want to connect a business portfolio yet."
- Select "Access the Threads API" usecase.
- Enter App name and your email address.
- Review and click Create App.

From Meta Developer Dashboard:
- Click Use cases from left menu.
- Customize the Threads API use case.
- From Permissions, Select "threads_basic".
- From Settings - Enter the Threads Redirect URL for all 3 fields. (Available in plugin's settings page).

Adding Test Users:
- From left menu, click App Roles > Roles.
- Click Add People.
- Select Threads Tester.
- Search for the account that you want to display threads feeds from.
- Click Add.
- An invitation is sent to that user.
- Login to Threads website and go to settings > Account > website permissions > Invites.
- Accept the invitation received from your App.

Get App client ID and Secret:
- Go to App Settings > Basic > Copy Threads App ID and Secret and enter in plugin's settings.
- Save the settings and click Connect Now button.
- Login as your thread account and click allow.
- The page should redirect back to the plugin's settings.

Now you are ready to use the shortcode "[fftmj_feeds]" on any page.

== Installation ==

You can install the Plugin in two ways.

= WordPress interface installation =

1. Go to plugins in the WordPress admin and click on “Add new”.
2. In the Search box enter “Feeds for Threads” and press Enter.
3. Click on “Install” to install the plugin.
4. Activate the plugin.

= Manual installation =

1. Download and upload the plugin files to the /wp-content/plugins/better-social-feeds directory from the WordPress plugin repository.
2. Activate the plugin through the "Plugins" screen in WordPress admin area.

== Frequently Asked Questions ==

= How to display feeds from threads? =
Install and configure Feeds for Threads plugin on your WordPress website and use the shortcode to add threads feeds.

= Do I need API? =
Yes, you would need to create an app from your FB developer account and add the client ID and client secret to the plugin's settings.

= What are the shortcodes =
Use Shortcode [fftmj_feeds] to load threads from the user you connect in settings.

= Looking for more features? =
Please create a support ticket with your request and we will try to add as soon as possible.

== Screenshots ==
1. Sample feeds from multiple accounts side by side
2. Sample feeds from one account

== Changelog ==
= 1.0.1 =
* 2024-06-19
* Update - Using the official Threads API

= 0.0.5 =
* 2024-04-02
* Fix - profile url was not working.

= 0.0.4 =
* 2024-03-08
* Support for entering profile URL instead of user ID.
* Keep the user ID option working.
* No need to find the user ID from the Threads profile.

= 0.0.3 =
* 2023-12-22
* Limit posts count 5 by default.

= 0.0.2 =
* 2023-11-07
* Updated readme and description.

= 0.0.1 =
* 2023-10-09
* First version of Feeds for Threads WordPress plugin.


== Upgrade Notice ==


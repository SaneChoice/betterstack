=== Better Stack Monitor Status ===
Contributors: sanechoice, sohel0415
Version: 1.0
Author: SaneChoice Limited
Author URI: https://www.sanechoice.cloud
Tags: 1.0
Requires at least: 6.0
Tested up to: 6.2
Stable tag: 1.0
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Display the status of website monitors using the Better Uptime API.

== Description ==
If you are using Better Stack for Monitoring, use this plugin to display your monitors directly on your WordPress website.

== Integrate your Better Stack Monitors into WordPress ==
Better Stack provides website and logs monitoring for websites. This simple plugin will allow you to select your monitors, add your Better Stack API key and create a shortcode to display it anywhere on your website. Create a free BetterStaack account here: https://uptime.betterstack.com/users/sign-up

== Usages ==
After installation, add shortcode `[betteruptime_monitor_status]` in any place. That's it :)

== Key Features ==
* Works with Better Stacks free account.
* Easily add Better Stack API Key from the Admin Panel.
* Add individual monitors to display on your website.
* Creates a shortcode to use on your pages.

== Installation ==
1. Download our Plugin Code from GitHub. 
2. Go to your WordPress website, upload the plugin, and then activate the plugin. If there are no errors during activation, then you are all set.
3. Go to the BetterStack Menu Item in the WordPress Admin Toolbar, located on the left.
4. Add your Pronounceable Names separated by commas, and also add your Better Stack API Key.
5. Click UPDATE once Monitors and API Key has been added.
6. To display your monitors, add the below shortcode to the page where you want to show it. Then, save and publish the page and look on the internet to see how it looks.

[betteruptime_monitor_status]

== Changelog ==

= 1.0 =
Release Date - 03 June 2023

* Initial code to show monitors
* Setup API and monitors list from admin panel

= 1.1 =
Release Date - 10 June 2023

* Tided the Admin Screen
* Fixed issue with Admin Panel menu item name
* Added code to support sending of Incident via API
* Added code to select notification methods when sending incident via API

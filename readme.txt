=== Plugin Memos ===
Contributors: michaelpfister, pictureplanet
Tags: plugin notes, memos, plugins, plugin, notes, plugin list, plugin memos, labels, markieren, mark
Requires at least: 5.2
Tested up to: 6.1
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Stick notes or memos with comments and reminders to your plugins in the plugin-list. 

== Description ==
Plugin Memos will add a button to each plugin in the plugin-list where you can add labels with comments to the plugin. Those labels act as notes or reminders. Add labels to an important plugin so it doesnt get deactivated or deleted by someone who doesnt know about its importance.
The labels are made by clicking the "Edit Labels"-button in the plugin-list and selecting one or multiple stati in the label-form. There are 4 default stati which are automatically created when the plugin is first installed and activated.
Each status has options to block certain functions like deactivating, delete or updating a plugin. If you make a label for a plugin with those options, the plugin can no longer be deactivated / updated / deleted until you remove the label.
You can make your own stati to make labels out of under Settings -> Plugin Memos Status Settings. You can give the status a name, select a color specifically for it and choose wether labels made with this status will block any of the options mentioned before.


== Installation ==

1. Install the plugin from the plugin repository and activate it. No settings needed.


== Frequently Asked Questions ==

= When should I use Plugin Memos? =
With Plugin Memos you can add labels with comments or reminders to a specific plugins. Example: You have a plugin your theme needs in order to work. You can add the label "Required for theme" together with the text "This plugin is important for the theme. Dont deactivate or delete it" to the plugin.
This way you have a reminder to the importance of this plugin for yourself and others who might have admin permission to the website.


== Screenshots ==

1. This image description corresponds to screenshot-1.png (stored in /assets). This image depicts the the labels in the plugin-list with the "Edit Labels"-button. The color of a label is determined by the selected status.
2. This image description corresponds to screenshot-2.png (stored in /assets). In this image you can see the popup-box where you can select the labels for a plugin and write a comment for each one.


== Changelog ==
= 1.0.0 =
* PHP 8 Refactoring
* Bug fix: With several clicks on the save button, labels were created according to the number of clicks.

= 0.1.2 =
* Bug fixes

= 0.1.1 =
* Bug fixes

= 0.1.0 = 
* First version

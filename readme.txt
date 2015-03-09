=== Pocket WP ===
Contributors: ciaranm
Tags: links, pocket, shortcode, widget
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Pocket WP allows you to embed your Pocket links into a WordPress page or post via a shortcode or a widget.

== Description ==

Pocket WP connects to the Pocket API and pulls in your latest saved links with tags and excerpts into a WordPress page, post or widget.

= Setup =

Due to the way Pocket's API works, you need to complete a few steps before you can use the plugin.

1. Install and activate the plugin.

2. Create an application on the [Pocket Developers website](http://getpocket.com/developer/apps/new), enter your Consumer Key into the Pocket WP settings page and click Save Changes to start the authorization process.

3. Click the Get Access Key link to complete the authorization.

= Shortcode =

The shortcode embeds a list of Pocket links into a page or post.

The basic shortcode is `[pocket_links]` and it accepts some optional arguments:

`count: [any number] // How many links to display. Default is all.`

`excerpt: yes, no // Whether or not to display the excerpt extracted by Pocket. Default is yes.`

`tag: [any one of your Pocket tags] // Choose to show links from one tag. Supports one tag only. Default is all links, tagged or untagged.`

`credit: yes, no // Choose to add author credit. Default is to not show credit links.`

`tag_list: yes, no // Whether or not to show a list of tags after each link.`

= Widget =

The Widget is available to drag and drop into any widgetized sidebars. It has some options:

`title // the title for the widget`

`count // the number of links to show. Default is 5`

`tag: [any of your Pocket tags] // Choose to show links from one tag. Currently supports one tag only. Default is all links, tagged or untagged.`

`author credit: yes, no // Choose to give author credit. Default is to not show credit links.`


== Installation ==

1. Activate the plugin through the 'Plugins' menu in WordPress.
2. Follow installation instructions on the plugin options page or at the [plugin website](http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=pocket-wp&utm_source=pwp-readme&utm_medium=wp-plugins).

== Frequently Asked Questions ==
Visit the [plugin website](http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=pocket-wp&utm_source=pwp-readme&utm_medium=wp-plugins) for more information.

== Screenshots ==

1. Pocket WordPress settings page. 
2. Screenshot of the shortcode as it displays in a page.
3. Screenshot of the shortcode in the editor.
4. Screenshot of the widget settings box.
5. Screenshot of the widget as it displays in a sidebar.

== Changelog ==

= 0.4.3 = 
* Fixing an issue with link generator not having closing div. (Thanks to Myles Gray for catching this.)

= 0.4.2 =
* Improved the way plugin handles untagged posts. It now shows "untagged" in the tag list instead of throwing an error. Tag list can be turned off by using tag_list="no"

= 0.4.1 =
* Adding error checking on pocket feed retrieval.

= 0.4 =
* Fix so shortcode doesn't always display above text.
* Fixing activation message to only show once on first install.

= 0.3 =
* First public release
* Made author credit opt-in (was opt-out)
* Code clean up
* Added settings link in plugin list
* Added activation notice

= 0.2 =
* Second beta version (not public)
* Fixed bugs and cleaned up code
* Removed option to use multiple tags as Pocket didn’t seem to support this

= 0.1 = 
* Beta version (not public)


== Upgrade Notice ==

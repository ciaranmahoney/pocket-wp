# Pocket WP

_Pocket WP allows you to embed your Pocket links into a WordPress page or post via a shortcode or a widget._

* * *

    **Contributors:** ciaranm

    **Donate link:** 

    **Tags:** links, pocket, shortcode, widget

    **Requires at least:** 3.0.1

    **Tested up to:** 3.4

    **Stable tag:** trunk

    **License:** GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)  

* * *

### Description

Pocket WP connects to the Pocket API and pulls in your latest saved links with tags and excerpts into a WordPress page, post or widget.

#### Setup

Due to the way Pocket's API works, you need to complete a few steps before you can use the plugin.

1.  Install and activate the plugin.

2.  Create an application on the [Pocket Developers website](http://getpocket.com/developer/apps/new), enter your Consumer Key into the Pocket WP settings page and click Save Changes to start the authorization process.

3.  Click the Get Access Key link to complete the authorization.

#### Shortcode

The shortcode embeds a list of Pocket links into a page or post.

The basic shortcode is `[pocket_links]` and it accepts some optional arguments:

    count: [any number] // How many links to display. Default is all.`</pre>

    <pre>`excerpt: yes, no // Whether or not to display the excerpt extracted by Pocket. Default is yes.`</pre>

    <pre>`tag: [any one of your Pocket tags] // Choose to show links from one tag. Supports one tag only. Default is all links, tagged or untagged.`</pre>

    <pre>`credit: yes, no // Choose to add author credit. Default is to not show credit links.`</pre>

    tag_list: yes, no // Whether or not to show a list of tags after each link.`

    #### Widget

    The Widget is available to drag and drop into any widgetized sidebars. It has some options:

    <pre>`title // the title for the widget`</pre>

    <pre>`count // the number of links to show. Default is 5`</pre>

    <pre>`tag: [any of your Pocket tags] // Choose to show links from one tag. Currently supports one tag only. Default is all links, tagged or untagged.

`author credit: yes, no // Choose to give author credit. Default is to not show credit links.

* * *

### Installation

1.  Activate the plugin through the 'Plugins' menu in WordPress.
2.  Follow installation instructions on the plugin options page or at the [plugin website](http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=pocket-wp&amp;utm_source=pwp-readme&amp;utm_medium=wp-plugins). 

* * *

### Frequently Asked Questions

Visit the [plugin website](http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=pocket-wp&amp;utm_source=pwp-readme&amp;utm_medium=wp-plugins) for more information.

* * *

### Screenshots

Visit the [plugin website](http://ciaranmahoney.me/code/pocket-wp/?utm_campaign=pocket-wp&amp;utm_source=pwp-readme&amp;utm_medium=wp-plugins) for more information.

* * *

### Changelog

#### 0.3

*   First public release
*   Made author credit opt-in (was opt-out)
*   Code clean up
*   Added setting links in plugin list
*   Added activation notice

#### 0.2

*   Second beta version (not public)
*   Fixed bugs and cleaned up code
*   Removed option to use multiple tags as Pocket didn’t seem to support this

#### 0.1

*   Beta version (not public)   

* * *

### Upgrade Notice
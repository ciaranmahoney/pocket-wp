Pocket WP
===

Pocket WP allows you to embed your Pocket links into a WordPress page via a shortcode and/or a widget.

I would love feedback and feature requests! Let me know on [Twitter](https://twitter.com/ciaransm)

## Setup
Due to the way Pocket's API works, you need to complete a few steps before you can use the plugin.

1. Install and activate the plugin.

2. Create an application on the [Pocket Developers website](http://getpocket.com/developer/apps/new), enter your Consumer Key into the Pocket WP settings page and click Save Changes to start the authorization process.

3. Click the Get Access Key link to complete the authorization.

## Shortcode

The shortcode embeds a list of Pocket links into a page or post.

The basic shortcode is `[pocket_links]` and it accepts three optional arguments:

`count: [any number] // How many links to display. Default is all.`
`excerpt: yes, no //Whether or not to display the excerpt extracted by Pocket. Default is yes.` 
`tags: [any of your Pocket tags] // Choose to show links from one tag. Currently supports one tag only. Default is any links, tagged or untagged.`

## Widget

The Widget is available to drag and drop into any widgetized sidebars. It has three options:

` title // the title for the widget`
` count // the number of links to show. Default is 5`
`tags: [any of your Pocket tags] // Choose to show links from one tag. Currently supports one tag only. Default is any links, tagged or untagged.`

## Screenshots

**Screenshot of the Shortcode Output**
![alt text](https://raw.githubusercontent.com/ciaranmahoney/Pocket-WP/master/screenshots/shortcode-display.png)

**Screenshot of the Shortcode Editing Screen**
![alt text](https://raw.githubusercontent.com/ciaranmahoney/Pocket-WP/master/screenshots/shortcode-page.png)

**Screenshot of the Widget Display**
![alt text](https://raw.githubusercontent.com/ciaranmahoney/Pocket-WP/master/screenshots/widget-display.png)

**Screenshot of the Widget Panel**
![alt text](https://raw.githubusercontent.com/ciaranmahoney/Pocket-WP/master/screenshots/widget-label.png)

**Screenshot of the Widget Options**
![alt text](https://raw.githubusercontent.com/ciaranmahoney/Pocket-WP/master/screenshots/widget-options.png)


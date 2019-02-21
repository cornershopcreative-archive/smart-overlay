=== Smart Overlay ===
Contributors: drywallbmb, rxnlabs, dannycorner, rpasillas
Tags: modal window, popup, lightbox
Requires at least: 4.3
Tested up to: 5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

*Alert: Smart Overlay will soon be retired in favor of WP Popup. [Learn more here.](https://wordpress.org/plugins/wp-pop-up/)*

== Description ==

*Alert: Smart Overlay will soon be retired in favor of WP Popup. [Learn more here.](https://wordpress.org/plugins/wp-pop-up/)*

Smart Overlay is a plugin for implementing whatever you want to call them — modals, lightboxes, overlays or popups — on your site. While it offers fine-tuned control over where and when the lightboxes display, it was developed with the goal of being simple and lightweight: Smart Overlay won’t cause your site to take a big performance hit by loading lots of complicated and extraneous CSS and JavaScript.

Smart Overlay lets you use the standard WordPress post editor to build and configure your overlays. In addition to full WYSIWYG editing of overlay content, Smart Overlay gives you powerful control over what triggers the appearance of your lightbox. Triggers can be set so popups show:

* Immediately on page load
* After a configurable number of seconds
* After the page is scrolled a configurable number of pixels
* After the page is scrolled halfway, or to the bottom
* After the user has spent a configurable number of minutes on the site
* After the user has visited a configurable number of pages over the past 90 days

In addition to those sophisticated trigger controls, you also get options on each overlay for:

* Background image: Make a richer, more visually engaging popup by using a photo or illustration that covers the entire overlay.
* Where to display: On your site’s homepage, on all pages, or all pages except the homepage
* Scheduling: Configure whether users should see the overlay just once or all the time or periodically based on a schedule.
* Mobile control: Avoid hits to your SEO by suppressing your overlays from appearing on mobile devices!
* Width control: Set the max-width of the lightbox/popup.
* Cookie identifier: Easily change how browsers know about this overlay so you don’t have to save a whole new overlay after fixing a typo if you want it appearing again.

*Alert: Smart Overlay will soon be retired in favor of WP Popup. [Learn more here.](https://wordpress.org/plugins/wp-pop-up/)*

*Note: This plugin uses cookies, so if you’re bound by EU or other regulations requiring you notify users of such, be sure to do so if you’ve got Smart Overlay enabled.*

**Interested in other plugins from Cornershop Creative? We made [these things](https://cornershopcreative.com/products).**

== Installation ==

1. Upload the `smart-overlay` directory to your plugins directory (typically wp-content/plugins)
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit Smart Overlay > Create New Overlay to begin setting up your first popup.

== Frequently Asked Questions ==

= How many overlays can I create? =

As many as you want, though only one will show on any given URL.

= What happens if more than one overlay is set to appear on a given page? =

To avoid annoying your site users with multiple popups, Smart Overlay will only display the most recent one.

= What styling and animation options are there? =

Smart Overlay was written to be lean & mean. It offers minimal styling out-of-the-box (just a small close X in the upper right corner) and no animation controls, so that it doesn’t bloat your site with unnecessary code for different themes & styles you’re not actually using.
Of course, you’re free to use the WYSIWYG and graft on your own custom CSS to change the appearance however you want!

= How can I contribute? =

The git repository should be publicly available at [on Bitbucket](https://bitbucket.org/cornershopcreative/smart-overlay). Feel free to fork, edit, make pull requests, etc.

== Changelog ==

= 0.9.3 =
* Bug fix to only enqueue cmb2 js scripts on smart_overlay post type admin screens. This will likely be the last-ever update to Smart Overlay.

= 0.9.2 =
* Add new admin notice about the plugin's retirement in favor of WP Popup

= 0.9.1 =
* Bugfix to resolve missing files.

= 0.9.0 =
* Add new setting fields type to input a dimension and units.
* Add Min Height setting.
* Update how the styles are processed for front-end display.
* Refactor how the styles are processed for front-end display.
* Refactor how the JavaScript is fed its configuration options.
* Refactor which methods are called in which hooks.
* Fix JavaScript error for overlays that have no background image.
* Fix bug that prevented multiple overlays from displaying correctly.
* Fix fatal PHP errors that happen in PHP 7.2.
* Fix small PHP notices.
* Only display trigger amount field if a proper trigger is selected.

= 0.8.1 =
* Bugfixes for overlay styling and file inclusion.

= 0.8 =
* Refactoring entire codebase to be object-oriented in preparation for future features; no other functional changes.

= 0.7 =
* Refactoring mobile check to occur on front-end rather than with wp_is_mobile() to get around caching issues.
* Updating Featherlight library from 1.2.3 to 1.7.8.

= 0.6 =
* Initial public release.
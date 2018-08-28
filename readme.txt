=== Smart Popup ===
Contributors: drywallbmb, rxnlabs, dannycorner, rpasillas
Tags: modal window, popup, lightbox
Requires at least: 4.3
Tested up to: 4.9.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Looking for a new way to entice your site visitors? Smart Popup is the lightbox/popup plugin built with performance in mind.

== Description ==

Smart Popup is a plugin for implementing whatever you want to call them — modals, lightboxes, overlays or popups — on your site. While it offers fine-tuned control over where and when the lightboxes display, it was developed with the goal of being simple and lightweight: Smart Popup won't cause your site to take a big performance hit by loading lots of complicated and extraneous CSS and JavaScript.

Smart Popup lets you use the standard WordPress post editor to build and configure your overlays. In addition to full WYSIWYG editing of overlay content, Smart Popup gives you powerful control over what triggers the appearance of your lightbox. Triggers can be set so popups show:

* Immediately on page load
* After a configurable number of seconds
* After the page is scrolled a configurable number of pixels
* After the page is scrolled halfway or to the bottom
* After the user has spent a configurable number of minutes on the site
* After the user has visited a configurable number of pages over the past 90 days

In addition to those sophisticated trigger controls, you also get options on each overlay for:

* Mask background color: Choose an appropriate color and opacity to set as the background of the mask that covers your site.
* Background image: Make a richer, more visually engaging popup by using a photo or illustration that fills the inside of the popup.
* Background color: Choose an appropriate color and opacity to set as the background of the popup.
* Width control: Set a minimum and maximum width.
* Height control: Set minimum and maximum values along with pixels or percentages.
* Padding: Control the padding within your popup.
* Border: Add a border of any color, width and radius.
* Opacity: Adjust the opacity of the popup.
* Where to display: Choose whether to display on your site's homepage, on all pages, or on all pages except the homepage.
* Scheduling: Configure whether users should see the overlay just once, all the time, or periodically based on a schedule.
* Mobile control: Avoid hits to your SEO by suppressing your overlays from appearing on mobile devices!

* Cookie identifier: Easily change how browsers know about this overlay so you don't have to save a whole new overlay after fixing a typo if you want your updated popup to appear again.

*Note: This plugin uses cookies, so if you're bound by the EU or other regulations requiring you notify users of such, be sure to do so if you've got Smart Popup enabled.*

*Note: This plugin disables Gutenberg as an editing option for all popups.*

**Interested in other plugins from Cornershop Creative? We've made [these things](https://cornershopcreative.com/products).**

== Installation ==

1. Upload the `smart-overlay` directory to your plugins directory (typically wp-content/plugins)
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit Smart Popup > Create New Popup to begin setting up your first popup.

== Frequently Asked Questions ==

= How many popups can I create? =

As many as you want, though only one will show on any given URL.

= What happens if more than one popup is set to appear on a given page? =

To avoid annoying your site users with multiple popups, Smart Popup will only display the most recent one.

= What styling and animation options are there? =

Smart Popup was written to be lean & mean. It offers minimal styling out-of-the-box (just a small close X in the upper right corner) and no animation controls, so that it doesn't bloat your site with unnecessary code for different themes & styles you're not actually using. Of course, you're free to use the WYSIWYG and graft on your own custom CSS to change the appearance however you want!

= How can I contribute? =

The git repository is publicly available at [on Bitbucket](https://bitbucket.org/cornershopcreative/smart-overlay). Feel free to fork, edit, make pull requests, etc.

== Changelog ==

= 1.0 =
* Rename plugin from Smart Overlay to Smart Popup
* Re-Arranged options into groups, styles for the popup inside, styles for the popup outside and display options.
* New option: Background Color for outer mask.
* New option: Background color for inner popup.
* New option: Max Height
* New option: Min Height
* New option: Padding
* New option: Borders
* New option: Opacity
* Disable Gutenberg editor for popups.

= 0.8.1 =
* Bugfixes for overlay styling and file inclusion.

= 0.8 =
* Refactoring entire codebase to be object-oriented in preparation for future features; no other functional changes.

= 0.7 =
* Refactoring mobile check to occur on front-end rather than with wp_is_mobile() to get around caching issues.
* Updating Featherlight library from 1.2.3 to 1.7.8.

= 0.6 =
* Initial public release.
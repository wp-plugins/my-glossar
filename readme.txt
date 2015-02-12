=== Plugin Name ===
Contributors: nessio
Tags: dictionary, encyclopedia, lexicon, lexikon, page lexicon, seo lexicon, seo lexikon, glossar
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This Plugins helps you to create a simple Glossar / Encyclopedia on your site using the plugin by Nessio

== Description ==


If you have questions, feedback or an idea for a feature request you can place it [on my Website](http://www.benjamin-neske.de/stichwortlexikon-mit-wordpress-my-glossar-plugin/).

You can check the live example at http://www.handyvertrag-tipps.de/lexikon/ or a demo at http://nessio.net/demo/ .
For the Backend you can login with the following credentials:
User: demo
Password: demo

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload 'my-glossar' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Optional: Set-up the Slug for the lexicon under Settings > Glossar. The default value is 'lexikon'
4. It is important that you SAVE the Permalink structure again. (Settings > Permalinks)
5. Create a new Page with the your own or the default Slug /lexikon/ and add the Shortcodes [gl_navigation] for the navigation and [gl_directory] for the entries to the page
6. Enjoy the Plugin :-)


== Frequently Asked Questions ==


= How can i try the plugin? =
You can check the demo of the plugin at http://nessio.net/demo/ .
For the Backend you can login with the following credentials:

User: demo
Password: demo

= Clicking on a word shows a 404 Error =
The most reasons for this error is a not saved permalink structure.
Go to Settings > Permalinks and press the save button to solve this issue.

= How can i use my own template for the detail view ? =
To use an own template you can create a new file with the name single-nessio_gl.php and upload this to the theme folder.


== Screenshots ==

1. overview - all entries
2. add a new entr
3. the frontend view

== Changelog ==

= 0.9.1 =
First version of the glosslar plugin.

= 0.9.2 =
- Bugfix for the navigation shortcode
- readme adjustments

= 0.9.3 =
- Bugfix Navigationcode

= 0.9.4 =
- add editor for the backendview
- some minor bugfixes

= 0.9.5 =
- fix the problem with the special character (germane)
- activate support for comments
- fix problem with the text formatting
- fix some output bugs

= 0.9.6 =
- fix for special character and older php versions

= 1.0.0 =
- added the possibility to use your own url-slug instead of lexikon
- minor bugfixes

= 1.0.2 =
- fixed the not closed h3-tag
- Optimization of displaying the numeric character


== Upgrade Notice ==
coming soon
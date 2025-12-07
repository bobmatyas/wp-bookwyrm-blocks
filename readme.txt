=== Blocks for BookWyrm ===
Contributors: lastsplash
Tags: blocks, books, bookwyrm
Requires at least: 6.8
Tested up to: 6.9
Requires PHP: 8.2
Stable Tag: 1.0.1
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Adds two blocks for pulling currently reading and past read books from BookWyrm

== Description ==

This plugin adds two blocks to pull data from BookWyrm and to display it on your WordPress site:

- Recently Read books
- Currently Reading books

=== External Sources ===

This plugin makes API calls to the user-configued [BookWyrm](https://joinbookwyrm.com/) instance. It sends the user-configured username to:

- Retrieve a list of recently read or currently reading books

It relies on public endpoints and only displays already public information. 

Each BookWyrm instance configures its own privacy policy based on [the BookWyrm software's privacy controls](https://docs.joinbookwyrm.com/privacy-controls.html). This is [the Privacy Policy for the BookWyrm.social instance](https://bookwyrm.social/privacy).

To display book covers, the plugin calls [the OpenLibrary.org API](https://openlibrary.org/developers/api). The only data sent to OpenLibrary is an ISBN. No user information is sent. As a project of Archive.org, OpenLibrary.org's API is governed by the Archive.org [Terms of Service and Privacy Policy](https://archive.org/about/terms).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/blocks-for-bookwyrm` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress


== Frequently Asked Questions ==

= Does this work with any instance? =

It should although it was only tested against BookWyrm.social

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0.1 - 12/07/25 =
- Fix linting issues
- Switch to PHP-based API calls

= 1.0.0
- Initial release
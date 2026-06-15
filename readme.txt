=== Custom Icons ===
Contributors: silas229
Donate link: https://github.com/sponsors/silas229
Tags: block-editor, gutenberg, icons, svg, media-library
Requires at least: 6.6
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a Custom Icons screen under Design and makes SVG icons from the Media Library available to the core/icon block.

== Description ==

Custom Icons lets site administrators manage reusable SVG icons for the WordPress Icon block.

Features include:

* A **Custom Icons** management screen under **Design**
* Selecting SVG files from the Media Library
* Uploading SVG files to the Media Library
* Default icon names based on the media item's title
* Optional custom slugs for icon names
* Registration of icons for use with the `core/icon` block
* Editor integration so custom icons can be discovered in the block editor

Icons are registered with names like:

`custom-icons/my-icon`

These icons can then be used in the WordPress Icon block.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/custom-icons` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Design > Custom Icons** to add icons.

== Frequently Asked Questions ==

= Can editors use the custom icons in the block editor? =

Yes. Users who can edit posts can use the registered custom icons in the block editor.

= Who can manage the Custom Icons screen? =

Only users with the `switch_themes` capability can view and manage Custom Icons.

= Does this plugin enable SVG uploads? =

Yes. The plugin adds SVG to the allowed Media Library upload types so SVG icons can be uploaded and selected directly.

== Changelog ==

= 0.4.0 =
* Enabled SVG uploads in the Media Library by allow-listing SVG files.
* Updated the plugin FAQ to document SVG upload support.

= 0.3.0 =
* Centralized capability definitions.
* Restricted Custom Icons management to users with `switch_themes`.
* Preserved SVG `viewBox` during rendering.
* Fixed icon registration for environments where the registry `register()` method is protected.
* Converted plugin documentation to WordPress readme.txt format.

= 0.2.0 =
* Added the initial Custom Icons plugin implementation.
* Added editor integration for the `core/icon` block.

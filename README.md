# Custom Icons

WordPress plugin that adds a **Custom Icons** admin screen under **Design** and registers SVG icons from the Media Library for the `core/icon` block.

## Features

- Custom post type **Custom Icons** under **Design**
- Select SVG files from the Media Library
- Default icon name from the media item's post title
- Optional custom slug per icon
- Registers icons in `WP_Icons_Registry`
- Adds REST/editor integration so custom icons can be discovered by the block editor

## Installation

1. Copy this plugin into your WordPress plugins directory.
2. Activate the plugin.
3. Ensure SVG uploads are allowed in your WordPress install.

## Usage

1. Go to **Design → Custom Icons**.
2. Create a new Custom Icon.
3. Choose an SVG from the Media Library.
4. Optionally adjust the title and slug.
5. Publish the Custom Icon.

The icon will be registered with a name like:

```text
custom-icons/my-icon
```

This can then be used by the `core/icon` block.

## Notes

- The editor integration uses a REST endpoint at `/wp-json/custom-icons/v1/icons`.
- Access to the endpoint is limited to users who can `edit_posts`.
- SVG sanitization is intentionally conservative but lightweight.

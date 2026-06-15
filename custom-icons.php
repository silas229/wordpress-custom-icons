<?php
/**
 * Plugin Name: Custom Icons
 * Description: Adds a "Custom Icons" admin screen under Design and registers SVG icons for the core/icon block from Media Library attachments.
 * Version: 0.5.0
 * Author: silas229
 * Text Domain: custom-icons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CUSTOM_ICONS_VERSION', '0.5.0' );
define( 'CUSTOM_ICONS_FILE', __FILE__ );
define( 'CUSTOM_ICONS_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOM_ICONS_URL', plugin_dir_url( __FILE__ ) );

define( 'CUSTOM_ICONS_POST_TYPE', 'custom_icon' );
define( 'CUSTOM_ICONS_META_ATTACHMENT_ID', '_custom_icon_attachment_id' );
define( 'CUSTOM_ICONS_META_SLUG', '_custom_icon_slug' );
define( 'CUSTOM_ICONS_NAMESPACE', 'custom-icons/v1' );
define( 'CUSTOM_ICONS_ICON_PREFIX', 'custom-icons/' );
define( 'CUSTOM_ICONS_MANAGE_CAP', 'switch_themes' );
define( 'CUSTOM_ICONS_EDITOR_CAP', 'edit_posts' );

require_once CUSTOM_ICONS_DIR . 'includes/post-type.php';
require_once CUSTOM_ICONS_DIR . 'includes/meta.php';
require_once CUSTOM_ICONS_DIR . 'includes/helpers.php';
require_once CUSTOM_ICONS_DIR . 'includes/admin.php';
require_once CUSTOM_ICONS_DIR . 'includes/registry.php';
require_once CUSTOM_ICONS_DIR . 'includes/rest.php';
require_once CUSTOM_ICONS_DIR . 'includes/upload.php';
require_once CUSTOM_ICONS_DIR . 'includes/icon-links.php';

add_action( 'init', 'custom_icons_register_post_type' );
add_action( 'init', 'custom_icons_register_meta' );
add_action( 'init', 'custom_icons_register_icons', 20 );
add_action( 'rest_api_init', 'custom_icons_register_rest_routes' );
add_action( 'admin_enqueue_scripts', 'custom_icons_admin_enqueue_assets' );
add_action( 'enqueue_block_editor_assets', 'custom_icons_enqueue_block_editor_assets' );
add_action( 'add_meta_boxes', 'custom_icons_add_meta_boxes' );
add_action( 'save_post_' . CUSTOM_ICONS_POST_TYPE, 'custom_icons_save_post', 10, 2 );

add_filter( 'upload_mimes', 'custom_icons_allow_svg_uploads' );
add_filter( 'wp_check_filetype_and_ext', 'custom_icons_fix_svg_filetype', 10, 4 );
add_filter( 'render_block_core/icon', 'custom_icons_render_linked_icon_block', 10, 2 );

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function custom_icons_register_post_type() {
	$labels = array(
		'name'                  => __( 'Custom Icons', 'custom-icons' ),
		'singular_name'         => __( 'Custom Icon', 'custom-icons' ),
		'menu_name'             => __( 'Custom Icons', 'custom-icons' ),
		'name_admin_bar'        => __( 'Custom Icon', 'custom-icons' ),
		'add_new'               => __( 'Add New', 'custom-icons' ),
		'add_new_item'          => __( 'Add New Custom Icon', 'custom-icons' ),
		'new_item'              => __( 'New Custom Icon', 'custom-icons' ),
		'edit_item'             => __( 'Edit Custom Icon', 'custom-icons' ),
		'view_item'             => __( 'View Custom Icon', 'custom-icons' ),
		'all_items'             => __( 'Custom Icons', 'custom-icons' ),
		'search_items'          => __( 'Search Custom Icons', 'custom-icons' ),
		'not_found'             => __( 'No custom icons found.', 'custom-icons' ),
		'not_found_in_trash'    => __( 'No custom icons found in Trash.', 'custom-icons' ),
		'item_published'        => __( 'Custom icon published.', 'custom-icons' ),
		'item_updated'          => __( 'Custom icon updated.', 'custom-icons' ),
	);

	register_post_type(
		CUSTOM_ICONS_POST_TYPE,
		array(
			'labels'            => $labels,
			'public'            => false,
			'show_ui'           => true,
			'show_in_menu'      => 'themes.php',
			'show_in_admin_bar' => false,
			'show_in_nav_menus' => false,
			'show_in_rest'      => false,
			'has_archive'       => false,
			'hierarchical'      => false,
			'menu_position'     => null,
			'supports'          => array( 'title' ),
			'capability_type'   => array( 'custom_icon', 'custom_icons' ),
			'capabilities'      => array(
				'edit_post'              => CUSTOM_ICONS_MANAGE_CAP,
				'read_post'              => CUSTOM_ICONS_MANAGE_CAP,
				'delete_post'            => CUSTOM_ICONS_MANAGE_CAP,
				'edit_posts'             => CUSTOM_ICONS_MANAGE_CAP,
				'edit_others_posts'      => CUSTOM_ICONS_MANAGE_CAP,
				'publish_posts'          => CUSTOM_ICONS_MANAGE_CAP,
				'read_private_posts'     => CUSTOM_ICONS_MANAGE_CAP,
				'delete_posts'           => CUSTOM_ICONS_MANAGE_CAP,
				'delete_private_posts'   => CUSTOM_ICONS_MANAGE_CAP,
				'delete_published_posts' => CUSTOM_ICONS_MANAGE_CAP,
				'delete_others_posts'    => CUSTOM_ICONS_MANAGE_CAP,
				'edit_private_posts'     => CUSTOM_ICONS_MANAGE_CAP,
				'edit_published_posts'   => CUSTOM_ICONS_MANAGE_CAP,
				'create_posts'           => CUSTOM_ICONS_MANAGE_CAP,
			),
			'map_meta_cap'      => false,
		)
	);
}

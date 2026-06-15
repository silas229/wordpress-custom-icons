<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function custom_icons_register_meta() {
	register_post_meta(
		CUSTOM_ICONS_POST_TYPE,
		CUSTOM_ICONS_META_ATTACHMENT_ID,
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'absint',
			'auth_callback'     => function() {
				return current_user_can( CUSTOM_ICONS_MANAGE_CAP );
			},
		)
	);

	register_post_meta(
		CUSTOM_ICONS_POST_TYPE,
		CUSTOM_ICONS_META_SLUG,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'sanitize_title',
			'auth_callback'     => function() {
				return current_user_can( CUSTOM_ICONS_MANAGE_CAP );
			},
		)
	);
}

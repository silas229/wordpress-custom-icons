<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function custom_icons_register_rest_routes() {
	register_rest_route(
		CUSTOM_ICONS_NAMESPACE,
		'/icons',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
			'callback'            => 'custom_icons_rest_get_icons',
		)
	);
}

function custom_icons_rest_get_icons( WP_REST_Request $request ) {
	$icons = custom_icons_get_registered_icons_data();

	return rest_ensure_response( $icons );
}

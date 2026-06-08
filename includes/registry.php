<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function custom_icons_register_icons() {
	if ( ! class_exists( 'WP_Icons_Registry' ) ) {
		return;
	}

	$icons = custom_icons_get_registered_icons_data();
	if ( empty( $icons ) ) {
		return;
	}

	$registry = WP_Icons_Registry::get_instance();

	foreach ( $icons as $icon ) {
		$registry->register(
			$icon['name'],
			array(
				'label'   => $icon['label'],
				'content' => $icon['content'],
			)
		);
	}
}

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
		if ( ! method_exists( $registry, 'is_registered' ) || ! $registry->is_registered( $icon['name'] ) ) {
			custom_icons_register_icon_via_reflection( $registry, $icon );
		}
	}
}

function custom_icons_register_icon_via_reflection( $registry, $icon ) {
	try {
		$method = new ReflectionMethod( $registry, 'register' );

		if ( PHP_VERSION_ID < 80100 ) {
			$method->setAccessible( true );
		}

		$method->invoke(
			$registry,
			$icon['name'],
			array(
				'label'   => $icon['label'],
				'content' => $icon['content'],
			)
		);
	} catch ( Throwable $e ) {
		return;
	}
}

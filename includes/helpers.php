<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function custom_icons_allowed_svg_html() {
	return array(
		'svg'      => array(
			'xmlns'               => true,
			'width'               => true,
			'height'              => true,
			'viewBox'             => true,
			'fill'                => true,
			'stroke'              => true,
			'stroke-width'        => true,
			'stroke-linecap'      => true,
			'stroke-linejoin'     => true,
			'role'                => true,
			'aria-hidden'         => true,
			'focusable'           => true,
			'class'               => true,
			'style'               => true,
			'preserveAspectRatio' => true,
		),
		'g'        => array(
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'class'           => true,
			'transform'       => true,
		),
		'path'     => array(
			'd'               => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'class'           => true,
			'transform'       => true,
		),
		'rect'     => array(
			'x'            => true,
			'y'            => true,
			'width'        => true,
			'height'       => true,
			'rx'           => true,
			'ry'           => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'class'        => true,
			'transform'    => true,
		),
		'circle'   => array(
			'cx'           => true,
			'cy'           => true,
			'r'            => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'class'        => true,
			'transform'    => true,
		),
		'ellipse'  => array(
			'cx'           => true,
			'cy'           => true,
			'rx'           => true,
			'ry'           => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'class'        => true,
			'transform'    => true,
		),
		'line'     => array(
			'x1'              => true,
			'y1'              => true,
			'x2'              => true,
			'y2'              => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'class'           => true,
			'transform'       => true,
		),
		'polyline' => array(
			'points'          => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'class'           => true,
			'transform'       => true,
		),
		'polygon'  => array(
			'points'          => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'class'           => true,
			'transform'       => true,
		),
		'title'    => array(),
		'desc'     => array(),
	);
}

function custom_icons_get_registered_icons_data() {
	$posts = get_posts(
		array(
			'post_type'      => CUSTOM_ICONS_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	if ( empty( $posts ) ) {
		return array();
	}

	$icons = array();

	foreach ( $posts as $post ) {
		$attachment_id = (int) get_post_meta( $post->ID, CUSTOM_ICONS_META_ATTACHMENT_ID, true );
		if ( ! $attachment_id ) {
			continue;
		}

		$mime_type = get_post_mime_type( $attachment_id );
		if ( 'image/svg+xml' !== $mime_type ) {
			continue;
		}

		$file_path = get_attached_file( $attachment_id );
		if ( ! $file_path || ! file_exists( $file_path ) ) {
			continue;
		}

		$svg = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! $svg ) {
			continue;
		}

		$svg = wp_kses( $svg, custom_icons_allowed_svg_html() );
		if ( ! $svg ) {
			continue;
		}

		$label = get_the_title( $post->ID );
		$slug  = (string) get_post_meta( $post->ID, CUSTOM_ICONS_META_SLUG, true );

		if ( ! $slug ) {
			$slug = sanitize_title( $label );
		}

		if ( ! $slug ) {
			$slug = 'icon-' . $post->ID;
		}

		$icons[] = array(
			'name'          => CUSTOM_ICONS_ICON_PREFIX . $slug,
			'label'         => $label ? $label : ( CUSTOM_ICONS_ICON_PREFIX . $slug ),
			'content'       => $svg,
			'slug'          => $slug,
			'post_id'       => (int) $post->ID,
			'attachment_id' => $attachment_id,
		);
	}

	return $icons;
}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function custom_icons_allow_svg_uploads( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';

	return $mimes;
}

function custom_icons_fix_svg_filetype( $data, $file, $filename, $mimes ) {
	$filetype = wp_check_filetype( $filename, $mimes );

	if ( 'svg' === $filetype['ext'] ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}

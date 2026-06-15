<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function custom_icons_render_linked_icon_block( $block_content, $block ) {
	if ( empty( $block['attrs']['url'] ) || ! is_string( $block['attrs']['url'] ) ) {
		return $block_content;
	}

	$url = esc_url( $block['attrs']['url'] );
	if ( ! $url ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );
	if ( ! $processor->next_tag( 'div' ) ) {
		return $block_content;
	}

	$target = '';
	if ( ! empty( $block['attrs']['linkTarget'] ) && is_string( $block['attrs']['linkTarget'] ) ) {
		$target = $block['attrs']['linkTarget'];
	}

	$rel = '';
	if ( ! empty( $block['attrs']['rel'] ) && is_string( $block['attrs']['rel'] ) ) {
		$rel = $block['attrs']['rel'];
	}

	if ( '_blank' === $target ) {
		$rel_parts = preg_split( '/\s+/', $rel, -1, PREG_SPLIT_NO_EMPTY );
		$rel_parts = is_array( $rel_parts ) ? $rel_parts : array();

		if ( ! in_array( 'noopener', $rel_parts, true ) ) {
			$rel_parts[] = 'noopener';
		}

		if ( ! in_array( 'noreferrer', $rel_parts, true ) ) {
			$rel_parts[] = 'noreferrer';
		}

		$rel = implode( ' ', array_unique( $rel_parts ) );
	}

	$anchor_attributes = ' href="' . esc_url( $url ) . '"';

	if ( $target ) {
		$anchor_attributes .= ' target="' . esc_attr( $target ) . '"';
	}

	if ( $rel ) {
		$anchor_attributes .= ' rel="' . esc_attr( $rel ) . '"';
	}

	return preg_replace(
		'/^(<div\b[^>]*>)(.*)(<\/div>)$/s',
		'$1<a' . $anchor_attributes . '>$2</a>$3',
		$block_content,
		1
	) ?: $block_content;
}

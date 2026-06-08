<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function custom_icons_admin_enqueue_assets( $hook_suffix ) {
	global $post_type;

	if ( CUSTOM_ICONS_POST_TYPE !== $post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'custom-icons-admin',
		CUSTOM_ICONS_URL . 'assets/admin.js',
		array( 'jquery', 'underscore' ),
		CUSTOM_ICONS_VERSION,
		true
	);

	wp_localize_script(
		'custom-icons-admin',
		'CustomIconsAdmin',
		array(
			'title'       => __( 'Select SVG Icon', 'custom-icons' ),
			'button'      => __( 'Use this SVG', 'custom-icons' ),
			'selectLabel' => __( 'Choose SVG from Media Library', 'custom-icons' ),
			'removeLabel' => __( 'Remove selected SVG', 'custom-icons' ),
			'onlySvg'     => __( 'Please select an SVG file.', 'custom-icons' ),
			'empty'       => __( 'No SVG selected yet.', 'custom-icons' ),
		)
	);
}

function custom_icons_enqueue_block_editor_assets() {
	if ( ! function_exists( 'wp_enqueue_script_module' ) ) {
		return;
	}

	wp_enqueue_script_module(
		'custom-icons-editor',
		CUSTOM_ICONS_URL . 'assets/editor.js',
		array(
			array(
				'id'     => '@wordpress/hooks',
				'import' => 'wp/hooks',
			),
		),
		CUSTOM_ICONS_VERSION
	);

	wp_localize_script(
		'wp-block-editor',
		'CustomIconsEditorData',
		array(
			'restUrl' => esc_url_raw( rest_url( CUSTOM_ICONS_NAMESPACE . '/icons' ) ),
		)
	);
}

function custom_icons_add_meta_boxes() {
	add_meta_box(
		'custom-icons-media',
		__( 'SVG Icon', 'custom-icons' ),
		'custom_icons_render_meta_box',
		CUSTOM_ICONS_POST_TYPE,
		'normal',
		'high'
	);
}

function custom_icons_render_meta_box( $post ) {
	wp_nonce_field( 'custom_icons_save', 'custom_icons_nonce' );

	$attachment_id = (int) get_post_meta( $post->ID, CUSTOM_ICONS_META_ATTACHMENT_ID, true );
	$slug          = (string) get_post_meta( $post->ID, CUSTOM_ICONS_META_SLUG, true );

	$attachment = $attachment_id ? get_post( $attachment_id ) : null;
	$file_url   = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
	$file_path  = $attachment_id ? get_attached_file( $attachment_id ) : '';
	$mime_type  = $attachment_id ? get_post_mime_type( $attachment_id ) : '';
	$title      = $attachment ? $attachment->post_title : '';

	$default_slug = $slug ? $slug : sanitize_title( $post->post_title );
	?>
	<p>
		<label for="custom-icon-title-note">
			<strong><?php esc_html_e( 'Name', 'custom-icons' ); ?></strong>
		</label>
	</p>
	<p id="custom-icon-title-note">
		<?php esc_html_e( 'The post title is used as the icon label. If empty when selecting media, it defaults to the media item title.', 'custom-icons' ); ?>
	</p>

	<hr />

	<p>
		<strong><?php esc_html_e( 'Selected SVG', 'custom-icons' ); ?></strong>
	</p>

	<input type="hidden" id="custom_icon_attachment_id" name="custom_icon_attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>" />

	<p>
		<button type="button" class="button button-secondary" id="custom-icons-select">
			<?php esc_html_e( 'Choose SVG from Media Library', 'custom-icons' ); ?>
		</button>
		<button
			type="button"
			class="button button-link-delete"
			id="custom-icons-remove"
			<?php disabled( ! $attachment_id ); ?>
		>
			<?php esc_html_e( 'Remove selected SVG', 'custom-icons' ); ?>
		</button>
	</p>

	<div id="custom-icons-preview" style="margin-top:12px;">
		<?php if ( $attachment_id ) : ?>
			<p><strong><?php esc_html_e( 'Attachment ID:', 'custom-icons' ); ?></strong> <?php echo (int) $attachment_id; ?></p>
			<p><strong><?php esc_html_e( 'Media title:', 'custom-icons' ); ?></strong> <span class="custom-icons-media-title"><?php echo esc_html( $title ); ?></span></p>
			<p><strong><?php esc_html_e( 'File URL:', 'custom-icons' ); ?></strong> <code><?php echo esc_html( $file_url ); ?></code></p>
			<p><strong><?php esc_html_e( 'MIME type:', 'custom-icons' ); ?></strong> <code><?php echo esc_html( $mime_type ); ?></code></p>
			<?php if ( $file_path && file_exists( $file_path ) ) : ?>
				<div class="custom-icons-inline-svg" style="max-width:160px;padding:12px;border:1px solid #ddd;background:#fff;">
					<?php echo wp_kses( file_get_contents( $file_path ), custom_icons_allowed_svg_html() ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents ?>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<p class="description"><?php esc_html_e( 'No SVG selected yet.', 'custom-icons' ); ?></p>
		<?php endif; ?>
	</div>

	<hr />

	<p>
		<label for="custom_icon_slug">
			<strong><?php esc_html_e( 'Slug', 'custom-icons' ); ?></strong>
		</label>
	</p>
	<p>
		<input
			type="text"
			class="regular-text"
			id="custom_icon_slug"
			name="custom_icon_slug"
			value="<?php echo esc_attr( $default_slug ); ?>"
			placeholder="<?php esc_attr_e( 'my-icon', 'custom-icons' ); ?>"
		/>
	</p>
	<p class="description">
		<?php esc_html_e( 'Optional. Used as part of the registered icon name. Defaults to a sanitized version of the title.', 'custom-icons' ); ?>
	</p>
	<?php
}

function custom_icons_save_post( $post_id, $post ) {
	if ( ! isset( $_POST['custom_icons_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['custom_icons_nonce'] ) ), 'custom_icons_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$attachment_id = isset( $_POST['custom_icon_attachment_id'] ) ? absint( $_POST['custom_icon_attachment_id'] ) : 0;
	$slug          = isset( $_POST['custom_icon_slug'] ) ? sanitize_title( wp_unslash( $_POST['custom_icon_slug'] ) ) : '';

	if ( $attachment_id ) {
		$mime_type = get_post_mime_type( $attachment_id );
		if ( 'image/svg+xml' !== $mime_type ) {
			$attachment_id = 0;
		}
	}

	if ( $attachment_id ) {
		update_post_meta( $post_id, CUSTOM_ICONS_META_ATTACHMENT_ID, $attachment_id );

		$attachment = get_post( $attachment_id );

		if ( $attachment && empty( $post->post_title ) ) {
			remove_action( 'save_post_' . CUSTOM_ICONS_POST_TYPE, 'custom_icons_save_post', 10 );
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => $attachment->post_title,
				)
			);
			add_action( 'save_post_' . CUSTOM_ICONS_POST_TYPE, 'custom_icons_save_post', 10, 2 );
		}
	} else {
		delete_post_meta( $post_id, CUSTOM_ICONS_META_ATTACHMENT_ID );
	}

	if ( $slug ) {
		update_post_meta( $post_id, CUSTOM_ICONS_META_SLUG, $slug );
	} else {
		delete_post_meta( $post_id, CUSTOM_ICONS_META_SLUG );
	}
}

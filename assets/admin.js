jQuery( function( $ ) {
	let frame;

	function setPreview( attachment ) {
		const attachmentId = attachment?.id || '';
		const title = attachment?.title || '';
		const url = attachment?.url || '';
		const mime = attachment?.mime || '';

		$( '#custom_icon_attachment_id' ).val( attachmentId );
		$( '#custom-icons-remove' ).prop( 'disabled', ! attachmentId );

		let previewHtml = '';

		if ( attachmentId ) {
			previewHtml += '<p><strong>Attachment ID:</strong> ' + attachmentId + '</p>';
			previewHtml += '<p><strong>Media title:</strong> <span class="custom-icons-media-title">' + _.escape( title ) + '</span></p>';
			previewHtml += '<p><strong>File URL:</strong> <code>' + _.escape( url ) + '</code></p>';
			previewHtml += '<p><strong>MIME type:</strong> <code>' + _.escape( mime ) + '</code></p>';
		} else {
			previewHtml = '<p class="description">' + _.escape( CustomIconsAdmin.empty ) + '</p>';
		}

		$( '#custom-icons-preview' ).html( previewHtml );

		const postTitleField = $( '#title' );
		if ( attachmentId && postTitleField.length && ! postTitleField.val().trim() ) {
			postTitleField.val( title );
		}

		const slugField = $( '#custom_icon_slug' );
		if ( attachmentId && slugField.length && ! slugField.val().trim() ) {
			slugField.val(
				title
					.toLowerCase()
					.normalize( 'NFD' )
					.replace( /[\u0300-\u036f]/g, '' )
					.replace( /[^a-z0-9]+/g, '-' )
					.replace( /(^-|-$)/g, '' )
			);
		}
	}

	$( '#custom-icons-select' ).on( 'click', function( e ) {
		e.preventDefault();

		if ( frame ) {
			frame.open();
			return;
		}

		frame = wp.media( {
			title: CustomIconsAdmin.title,
			button: {
				text: CustomIconsAdmin.button,
			},
			multiple: false,
			library: {
				type: 'image',
			},
		} );

		frame.on( 'select', function() {
			const attachment = frame.state().get( 'selection' ).first().toJSON();

			if ( attachment.mime !== 'image/svg+xml' ) {
				window.alert( CustomIconsAdmin.onlySvg );
				return;
			}

			setPreview( attachment );
		} );

		frame.open();
	} );

	$( '#custom-icons-remove' ).on( 'click', function( e ) {
		e.preventDefault();
		setPreview( null );
	} );
} );

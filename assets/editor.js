import { addFilter } from '@wordpress/hooks';

const customIcons = {
	loaded: false,
	icons: [],
};

async function loadCustomIcons() {
	if ( customIcons.loaded ) {
		return customIcons.icons;
	}

	const restUrl = window?.CustomIconsEditorData?.restUrl;
	if ( ! restUrl ) {
		customIcons.loaded = true;
		customIcons.icons = [];
		return customIcons.icons;
	}

	try {
		const response = await window.fetch( restUrl, {
			credentials: 'same-origin',
			headers: {
				'X-WP-Nonce': window.wpApiSettings?.nonce || '',
			},
		} );

		if ( ! response.ok ) {
			throw new Error( 'Failed to load custom icons.' );
		}

		const items = await response.json();
		customIcons.icons = Array.isArray( items ) ? items : [];
	} catch ( error ) {
		customIcons.icons = [];
	}

	customIcons.loaded = true;
	return customIcons.icons;
}

addFilter(
	'core.data.receiveEntityRecords',
	'custom-icons/merge-icon-entities',
	( records, entityConfig ) => {
		if ( entityConfig?.kind !== 'root' || entityConfig?.name !== 'icon' ) {
			return records;
		}

		loadCustomIcons();

		if ( ! customIcons.loaded || ! customIcons.icons.length ) {
			return records;
		}

		const list = Array.isArray( records ) ? records : records ? [ records ] : [];
		const byName = new Map();

		for ( const icon of list ) {
			if ( icon?.name ) {
				byName.set( icon.name, icon );
			}
		}

		for ( const icon of customIcons.icons ) {
			if ( icon?.name ) {
				byName.set( icon.name, icon );
			}
		}

		return Array.from( byName.values() );
	}
);

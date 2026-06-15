import { addFilter } from '@wordpress/hooks';
import { InspectorControls, LinkControl } from '@wordpress/block-editor';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { PanelBody, TextControl } from '@wordpress/components';

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
	'blocks.registerBlockType',
	'custom-icons/extend-core-icon-attributes',
	( settings, name ) => {
		if ( name !== 'core/icon' ) {
			return settings;
		}

		return {
			...settings,
			attributes: {
				...settings.attributes,
				url: {
					type: 'string',
					default: '',
				},
				linkTarget: {
					type: 'string',
					default: '',
				},
				rel: {
					type: 'string',
					default: '',
				},
			},
		};
	}
);

const withIconLinkInspectorControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		if ( props.name !== 'core/icon' ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;
		const { url = '', linkTarget = '', rel = '' } = attributes;

		return (
			<Fragment>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody title="Link" initialOpen={ true }>
						<LinkControl
							value={ {
								url,
								opensInNewTab: linkTarget === '_blank',
							} }
							onChange={ ( nextValue ) => {
								setAttributes( {
									url: nextValue?.url || '',
									linkTarget: nextValue?.opensInNewTab ? '_blank' : '',
								} );
							} }
						/>
						<TextControl
							label="Rel"
							value={ rel }
							onChange={ ( value ) => setAttributes( { rel: value } ) }
							help="Optional link relationship, e.g. nofollow"
						/>
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	};
}, 'withIconLinkInspectorControls' );

addFilter(
	'editor.BlockEdit',
	'custom-icons/add-icon-link-inspector-controls',
	withIconLinkInspectorControls
);

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

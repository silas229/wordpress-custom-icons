import { addFilter } from '@wordpress/hooks';
import {
	BlockControls,
	LinkControl,
	__experimentalLinkControl as ExperimentalLinkControl,
} from '@wordpress/block-editor';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment, useState } from '@wordpress/element';
import { ToolbarButton, Popover, TextControl } from '@wordpress/components';
import { link, linkOff } from '@wordpress/icons';

const CustomIconsLinkControl = ExperimentalLinkControl || LinkControl;

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

const withIconLinkBlockControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		if ( props.name !== 'core/icon' ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes, isSelected } = props;
		const { url = '', linkTarget = '', rel = '' } = attributes;
		const [ isLinkUIOpen, setIsLinkUIOpen ] = useState( false );

		const openLinkUI = () => setIsLinkUIOpen( true );
		const closeLinkUI = () => setIsLinkUIOpen( false );

		return (
			<Fragment>
				<BlockEdit { ...props } />
				{ isSelected && (
					<BlockControls group="block">
						<ToolbarButton
							icon={ link }
							label="Link setzen"
							onClick={ openLinkUI }
							isPressed={ isLinkUIOpen }
						/>
						{ url && (
							<ToolbarButton
								icon={ linkOff }
								label="Link entfernen"
								onClick={ () => {
									setAttributes( {
										url: '',
										linkTarget: '',
										rel: '',
									} );
									closeLinkUI();
								} }
							/>
						) }
					</BlockControls>
				) }
				{ isSelected && isLinkUIOpen && CustomIconsLinkControl && (
					<Popover position="bottom center" onClose={ closeLinkUI }>
						<div style={ { width: '320px', padding: '16px' } }>
							<CustomIconsLinkControl
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
						</div>
					</Popover>
				) }
			</Fragment>
		);
	};
}, 'withIconLinkBlockControls' );

addFilter(
	'editor.BlockEdit',
	'custom-icons/add-icon-link-block-controls',
	withIconLinkBlockControls
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

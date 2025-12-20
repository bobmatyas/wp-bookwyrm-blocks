/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { configurePluginMessage } from '../inc/shared';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */

import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } )  {
	
	const { bookwyrmUserName, bookwyrmInstance, placeholderType = 'svg' } = attributes;
	
	// Get placeholder image URLs for preview
	// Try to get from editor settings first, then fallback to global variable
	let placeholderSvg = '';
	let placeholderPng = '';
	
	if ( typeof wp !== 'undefined' && wp.data && wp.data.select && wp.data.select( 'core/block-editor' ) ) {
		try {
			const settings = wp.data.select( 'core/block-editor' ).getSettings();
			if ( settings?.blocksForBookwyrm ) {
				placeholderSvg = settings.blocksForBookwyrm.placeholderSvg || '';
				placeholderPng = settings.blocksForBookwyrm.placeholderPng || '';
			}
		} catch ( e ) {
			// Settings not available, use global fallback
		}
	}
	
	// Fallback to global variable
	if ( ! placeholderSvg && window.blocksForBookwyrm ) {
		placeholderSvg = window.blocksForBookwyrm.placeholderSvg || '';
		placeholderPng = window.blocksForBookwyrm.placeholderPng || '';
	}
	
	// Get the selected placeholder URL for the preview
	const selectedPlaceholder = placeholderType === 'svg' ? placeholderSvg : placeholderPng;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'bookwyrm-reading-block' ) }>
					<TextControl
                        label={ __(
                            'Bookwyrm Username',
                            'bookwyrm-reading-block'
                        ) }
                        value={ bookwyrmUserName }
                        onChange={ ( value ) =>
                            setAttributes( { bookwyrmUserName: value } )
                        }
                        __next40pxDefaultSize={ true }
                        __nextHasNoMarginBottom={ true }
                    />
					<TextControl
                        label={ __(
                            'Bookwyrm Instance',
                            'bookwyrm-reading-block'
                        ) }
                        value={ bookwyrmInstance }
                        onChange={ ( value ) =>
                            setAttributes( { bookwyrmInstance: value } )
                        }
                        __next40pxDefaultSize={ true }
                        __nextHasNoMarginBottom={ true }
                    />
                </PanelBody>
				<PanelBody title={ __( 'Placeholder Cover', 'bookwyrm-reading-block' ) } initialOpen={ false }>
					<SelectControl
						label={ __( 'Placeholder Type', 'bookwyrm-reading-block' ) }
						value={ placeholderType }
						options={ [
							{ label: __( 'SVG (Simple)', 'bookwyrm-reading-block' ), value: 'svg' },
							{ label: __( 'PNG (Fancy)', 'bookwyrm-reading-block' ), value: 'png' },
						] }
						onChange={ ( value ) => setAttributes( { placeholderType: value } ) }
						__nextHasNoMarginBottom={ true }
					/>
					{ placeholderType && (
						<div style={ { marginTop: '16px', textAlign: 'center' } }>
							<p style={ { marginBottom: '8px', fontSize: '12px', color: '#757575' } }>
								{ __( 'Preview:', 'bookwyrm-reading-block' ) }
							</p>
							<img 
								src={ placeholderType === 'svg' ? placeholderSvg : placeholderPng }
								alt={ __( 'Placeholder preview', 'bookwyrm-reading-block' ) }
								style={ { 
									width: '150px', 
									height: '225px', 
									border: '1px solid #ddd',
									display: 'block',
									margin: '0 auto'
								} }
							/>
						</div>
					) }
                </PanelBody>
            </InspectorControls>
			<div { ...useBlockProps() } data-user={ attributes.bookwyrmUserName } data-instance={ attributes.bookwyrmInstance } >
					<div className="configuration--message"> { (attributes.bookwyrmUserName == '' || attributes.bookwyrmInstance == '' ) ? <p className="bookwyrm-editor-notice bookwyrm-editor-notice-red">{ configurePluginMessage( 'no' ) }</p> : <p className="bookwyrm-editor-notice bookwyrm-editor-notice-green">Displaying <b>currently reading</b> books from <i>{attributes.bookwyrmUserName}</i> at <u>{attributes.bookwyrmInstance}</u><br/>{configurePluginMessage( 'yes') }</p> }
					</div>

					<div className="reading--list">
					<div className="book">
						{ selectedPlaceholder ? (
							<img 
								src={ selectedPlaceholder }
								alt={ __( 'Placeholder book cover', 'bookwyrm-reading-block' ) }
								className="bookwyrm-book-cover"
								width="150"
								height="225"
								style={ { border: '1px solid #ccc', backgroundColor: '#eee' } }
							/>
						) : (
							<div className="bookwyrm-book-cover bookwyrm-book-cover-editor" />
						) }
						<p className="bookwyrm-book-title">
							<cite>Test Book Title</cite><br /> 
							by Example Author</p>
					</div>
					<div className="book">
						{ selectedPlaceholder ? (
							<img 
								src={ selectedPlaceholder }
								alt={ __( 'Placeholder book cover', 'bookwyrm-reading-block' ) }
								className="bookwyrm-book-cover"
								width="150"
								height="225"
								style={ { border: '1px solid #ccc', backgroundColor: '#eee' } }
							/>
						) : (
							<div className="bookwyrm-book-cover bookwyrm-book-cover-editor" />
						) }
						<p className="bookwyrm-book-title">
							<cite>Test Book Title</cite><br /> 
							by Example Author</p>
					</div>	
					<div className="book">
						{ selectedPlaceholder ? (
							<img 
								src={ selectedPlaceholder }
								alt={ __( 'Placeholder book cover', 'bookwyrm-reading-block' ) }
								className="bookwyrm-book-cover"
								width="150"
								height="225"
								style={ { border: '1px solid #ccc', backgroundColor: '#eee' } }
							/>
						) : (
							<div className="bookwyrm-book-cover bookwyrm-book-cover-editor" />
						) }
						<p className="bookwyrm-book-title">
							<cite>Test Book Title</cite><br /> 
							by Example Author</p>
					</div>	
					
				</div>
			</div>

		</>
	);
}

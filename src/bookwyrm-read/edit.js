/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */

import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

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
	
	const { bookwyrmUserName, bookwyrmInstance } = attributes

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'bookwyrm-read-block' ) }>
					<TextControl
                        label={ __(
                            'Bookwyrm Username',
                            'bookwyrm-read-block'
                        ) }
                        value={ bookwyrmUserName }
                        onChange={ ( value ) =>
                            setAttributes( { bookwyrmUserName: value } )
                        }
                    />
					<TextControl
                        label={ __(
                            'Bookwyrm Instance',
                            'bookwyrm-read-block'
                        ) }
                        value={ bookwyrmInstance }
                        onChange={ ( value ) =>
                            setAttributes( { bookwyrmInstance: value } )
                        }
                    />
                </PanelBody>
            </InspectorControls>
			<div { ...useBlockProps() } data-user={ attributes.bookwyrmUserName } data-instance={ attributes.bookwyrmInstance } >

				<h2>Read</h2>
				<p className="bookwrym-editor-notice">Displaying books read by <b>{ attributes.bookwyrmUserName }</b> on <b>{ attributes.bookwyrmInstance }</b><br/>This screen previews the live view. This notice isn't shown on your site's frontend.</p>
				<div className="read--list">
					<div class="book">
						<div class="bookwyrm-book-cover bookwyrm-book-cover-editor" />
						<p class="bookwyrm-book-title">
							<cite>Test Book Title</cite><br /> 
							by Example Author</p>
					</div>
					<div class="book">
						<div class="bookwyrm-book-cover bookwyrm-book-cover-editor" />
						<p class="bookwyrm-book-title">
							<cite>Test Book Title</cite><br /> 
							by Example Author</p>
					</div>	
					<div class="book">
						<div class="bookwyrm-book-cover bookwyrm-book-cover-editor" />
						<p class="bookwyrm-book-title">
							<cite>Test Book Title</cite><br /> 
							by Example Author</p>
					</div>	
					
				</div>
			</div>

		</>
	);
}

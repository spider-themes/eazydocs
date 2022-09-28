// import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save( props ) {
	const { col, include, exclude, show_docs, show_articles, more } = props.attributes;
	const blockProps = useBlockProps.save();
	return (
		<>
			<div { ...blockProps }>
				[eazydocs col="{col}" include="{include}"]
			</div>
		</>
	);
}

// import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

// Custom functions
import {doc_ids} from "../custom-functions";

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

	//  Shorlettcode attributes
	let include_doc_ids = doc_ids(include) ? 'include="'+doc_ids(include)+'"' : '';
	let exclude_doc_ids = doc_ids(exclude) ? 'exclude="'+doc_ids(exclude)+'"' : '';
	let columns = col ? 'col="'+col+'"' : '';
	let ppp = show_docs ? 'show_docs="'+show_docs+'"' : '';
	let articles = show_articles ? 'show_articles="'+show_articles+'"' : '';
	let more_txt = more ? 'more="'+more+'"' : '';

	return (
		<>
			<div { ...blockProps }>
				[eazydocs {columns} {include_doc_ids} {exclude_doc_ids} {ppp} {articles} {more_txt}]
			</div>
		</>
	);
}

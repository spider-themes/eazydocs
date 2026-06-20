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
	const { col, include, exclude, show_docs, show_articles, more, parent_docs_order, child_docs_order, parent_docs_order_by, show_topic, topic_label, docs_layout, img_size, show_private, show_protected, show_status_badge, show_lock_icon } = props.attributes;
	const blockProps = useBlockProps.save();

	//  Shorlettcode attributes
	let include_doc_ids = doc_ids(include) ? 'include="'+doc_ids(include)+'"' : '';
	let exclude_doc_ids = doc_ids(exclude) ? 'exclude="'+doc_ids(exclude)+'"' : '';
	let columns = col ? 'col="'+col+'"' : '';
	let ppp = show_docs ? 'show_docs="'+show_docs+'"' : '';
	let articles = show_articles ? 'show_articles="'+show_articles+'"' : '';
	let more_txt = more ? 'more="'+more+'"' : '';
	let is_topic = show_topic ? 'show_topic="'+show_topic+'"' : '';
	let is_topic_label = is_topic ? 'topic_label="'+topic_label+'"' : '';
	let is_parent_docs_order = parent_docs_order ? 'parent_docs_order="'+parent_docs_order+'"' : '';
	let is_child_docs_order = child_docs_order ? 'child_docs_order="'+child_docs_order+'"' : '';
	let is_parent_docs_order_by = parent_docs_order_by ? 'parent_docs_order_by="'+parent_docs_order_by+'"' : '';
	let is_docs_layout = docs_layout ? 'docs_layout="'+docs_layout+'"' : '';
	let is_img_size = img_size ? 'img_size="'+img_size+'"' : '';
	// Restricted docs toggles — always emit an explicit yes/no so "off" is honored.
	let is_show_private = 'show_private="'+(show_private === false ? 'no' : 'yes')+'"';
	let is_show_protected = 'show_protected="'+(show_protected === false ? 'no' : 'yes')+'"';
	let is_show_status_badge = 'show_status_badge="'+(show_status_badge === false ? 'no' : 'yes')+'"';
	let is_show_lock_icon = 'show_lock_icon="'+(show_lock_icon === false ? 'no' : 'yes')+'"';

	return (
		<>
			<div { ...blockProps }>
				[eazydocs {columns} {include_doc_ids} {exclude_doc_ids} {ppp} {articles} {more_txt} {is_topic} {is_topic_label} {is_parent_docs_order} {is_child_docs_order} {is_parent_docs_order_by} {is_docs_layout} {is_img_size} {is_show_private} {is_show_protected} {is_show_status_badge} {is_show_lock_icon}]
			</div>
		</>
	);
}

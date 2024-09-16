import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { PanelBody, FormTokenField, RangeControl, TextControl, CheckboxControl, SelectControl, RadioControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

const { Fragment } = wp.element;

// editor style
import './editor.scss';

// Custom functions
import {doc_ids} from "../custom-functions";

// colors
import colors from '../colors-palette';

export default function Edit( { attributes, setAttributes } ) {
	const { col, include, exclude, show_docs, show_articles, more, list, show_topic, topic_label, child_docs_order, parent_docs_order, parent_docs_order_by, docs_layout } = attributes;
	const blockProps = useBlockProps();

	const docs = useSelect( (select) => {
		return select("core").getEntityRecords('postType', 'docs', {
			parent: 0,
			status: ['publish', 'private']
		})
	}, [])


	const docSuggestions = docs ? docs.map((doc) => doc.id + " | " + doc.title.rendered) : [];


	// console.log( docSuggestions )

	// Set attributes value
	const onChangeCol = ( newCol ) => {
		setAttributes({
			col: newCol == '' ? 3 : newCol
		})
	}
 
	const orderOptions = [
		{ label: __('Ascending', 'eazydocs'), value: 'asc' },
		{ label: __('Descending', 'eazydocs'), value: 'desc' },
	];
	
	const layoutOptions = [
		{ label: __('Masonry', 'eazydocs'), value: 'masonry' },
		{ label: __('Grid', 'eazydocs'), value: 'grid' },
	];
	
	const parentOrderOptions = [
		{ label: __('No Order', 'eazydocs'), value: 'none' },
		{ label: __('Post ID', 'eazydocs'), value: 'ID' },
		{ label: __('Post Author', 'eazydocs'), value: 'author' },
		{ label: __('Title', 'eazydocs'), value: 'title' },
		{ label: __('Date', 'eazydocs'), value: 'date' },
		{ label: __('Last Modified Date', 'eazydocs'), value: 'modified' },
		{ label: __('Random', 'eazydocs'), value: 'rand' },
		{ label: __('Comment Count', 'eazydocs'), value: 'comment_count' },
		{ label: __('Menu Order', 'eazydocs'), value: 'menu_order' },
	];
	
	// Shortcode attributes
	let include_doc_ids = doc_ids(include) ? 'include="'+doc_ids(include)+'"' : '';
	let exclude_doc_ids = doc_ids(exclude) ? 'exclude="'+doc_ids(exclude)+'"' : '';
	let columns = col ? 'col="'+col+'"' : '';
	let ppp = show_docs ? 'show_docs="'+show_docs+'"' : '';
	let articles = show_articles ? 'show_articles="'+show_articles+'"' : '';
	let more_txt = more ? 'more="'+more+'"' : '';

	jQuery('.eazydocs-pro-block-notice').on('click', function (e) {
		e.preventDefault();
		let href = jQuery(this).attr('href')
		Swal.fire({
			title: 'Opps...',
			html: 'This is a PRO feature. You need to <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium Version to use this feature',
			icon: "warning",
			buttons: [false, "Close"],
			dangerMode: true
		})
	});

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__('Filters', 'eazydocs')}
					initialOpen={true}
				>
					<RangeControl
						initialPosition={3}
						label={__('Columns', 'eazydocs')}
						max={4}
						min={1}
						shiftStep={ 1 }
						onChange={ onChangeCol }
					/>
					
					<TextControl
						help={__('Button/link to get the full docs', 'eazydocs')}
						label={__('View More Button', 'eazydocs')}
						value={more}
						onChange={(value) => setAttributes({ more: value })}
					/>
					
					<CheckboxControl
						label={__('Show Topic', 'eazydocs')}
						checked={ show_topic }
						onChange={ (value) => setAttributes({ show_topic: value }) }
					/>

					{ show_topic &&
						<TextControl
							label={__('Topics Count Text', 'eazydocs')}
							value={topic_label}
							onChange={(value) => setAttributes({ topic_label: value })}
						/>
					}
					
					<SelectControl
						label={__('Parent Docs Order By', 'eazydocs')}
						value={parent_docs_order}
						options={parentOrderOptions}
						className={eazydocs_local_object.is_ezd_pro_block == 'yes' ? '' : 'eazydocs-pro-block-notice'}
						onChange={(value) => setAttributes({ parent_docs_order: value })}
					/>


					<SelectControl
						label={__('Parent Docs Order', 'eazydocs')}
						value={parent_docs_order_by}
						options={orderOptions}
						className={eazydocs_local_object.is_ezd_pro_block == 'yes' ? '' : 'eazydocs-pro-block-notice'}
						onChange={(value) => setAttributes({ parent_docs_order_by: value })}
					/>
					
					<SelectControl
						label={__('Child Docs Order', 'eazydocs')}
						value={child_docs_order}
						options={orderOptions}
						onChange={(value) => setAttributes({ child_docs_order: value })}
					/>
					
					<NumberControl
						label={__('Number of Docs', 'eazydocs')}
						isShiftStepEnabled={ true }
						onChange={(value) => setAttributes({ show_docs: value })}
						shiftStep={ 1 }
						value={ show_docs }
						min={1}
						__nextHasNoMarginBottom
						help={__('Number of Main Docs to show', 'eazydocs')}
					/>
					
					<NumberControl
						label={__('Number of Articles', 'eazydocs')}
						isShiftStepEnabled={ true }
						onChange={(value) => setAttributes({ show_articles: value })}
						shiftStep={ 1 }
						value={ show_articles }
						min={1}
						__nextHasNoMarginBottom
						help={__('Number of Articles to show under each Docs.', 'eazydocs')}
					/>

					<RadioControl
						label={__('Docs Layout ', 'eazydocs')}
						selected={docs_layout}
						options={layoutOptions}
						className={eazydocs_local_object.is_ezd_pro_block == 'yes' ? '' : 'eazydocs-pro-block-notice'}
						onChange={(value) => setAttributes({ docs_layout: value })}						
					/>

					<FormTokenField
						__experimentalAutoSelectFirstMatch
						__experimentalExpandOnFocus
						label={__('Docs to Show', 'eazydocs')}
						suggestions={ docSuggestions}
						value={include}
						onChange={(value) => setAttributes({ include: value })}
					/>

					<FormTokenField
						__experimentalAutoSelectFirstMatch
						__experimentalExpandOnFocus
						label={__('Docs Not to Show', 'eazydocs')}
						suggestions={ docSuggestions}
						value={exclude}
						onChange={(value) => setAttributes({ exclude: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				[eazydocs {columns} {include_doc_ids} {exclude_doc_ids} {ppp} {articles} {more_txt}]
			</div>
		</>
	);
}

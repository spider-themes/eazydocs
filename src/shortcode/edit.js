import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { __experimentalNumberControl as NumberControl } from '@wordpress/components';
import {
	PanelBody,
	FormTokenField,
	RangeControl,
	TextControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

const { Fragment } = wp.element;

// editor style
import './editor.scss';

// Custom functions
import { doc_ids } from '../custom-functions';

// colors
import colors from '../colors-palette';

export default function Edit({ attributes, setAttributes }) {
	const { col, include, exclude, show_docs, show_articles, more, list } =
		attributes;
	const blockProps = useBlockProps();

	const docs = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'docs', {
			parent: 0,
			status: ['publish', 'private'],
		});
	}, []);

	const docSuggestions =
		docs &&
		docs.map((doc) => {
			return doc.id + ' | ' + doc.title.rendered;
		});

	// console.log( docSuggestions )

	// Set attributes value
	const onChangeCol = (newCol) => {
		setAttributes({
			col: newCol == '' ? 3 : newCol,
		});
	};

	// Shortcode attributes
	let include_doc_ids = doc_ids(include)
		? 'include="' + doc_ids(include) + '"'
		: '';
	let exclude_doc_ids = doc_ids(exclude)
		? 'exclude="' + doc_ids(exclude) + '"'
		: '';
	let columns = col ? 'col="' + col + '"' : '';
	let ppp = show_docs ? 'show_docs="' + show_docs + '"' : '';
	let articles = show_articles ? 'show_articles="' + show_articles + '"' : '';
	let more_txt = more ? 'more="' + more + '"' : '';

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Filters', 'eazydocs')} initialOpen={true}>
					<RangeControl
						initialPosition={3}
						label={__('Columns', 'eazydocs')}
						max={4}
						min={1}
						shiftStep={1}
						onChange={onChangeCol}
					/>

					<NumberControl
						label={__('Docs Show Count', 'eazydocs')}
						isShiftStepEnabled={true}
						onChange={(value) =>
							setAttributes({ show_docs: value })
						}
						shiftStep={1}
						value={show_docs}
						min={1}
						__nextHasNoMarginBottom
					/>
					<small>{__('How many docs to display.', 'eazydocs')}</small>
					<br />
					<br />

					<NumberControl
						label={__('Docs Article Count', 'eazydocs')}
						isShiftStepEnabled={true}
						onChange={(value) =>
							setAttributes({ show_articles: value })
						}
						shiftStep={1}
						value={show_articles}
						min={1}
						__nextHasNoMarginBottom
					/>
					<small>
						{__(
							'Articles/child-docs show under every Docs.',
							'eazydocs'
						)}
					</small>
					<br />
					<br />

					<FormTokenField
						__experimentalAutoSelectFirstMatch
						__experimentalExpandOnFocus
						label={__('Docs to Show', 'eazydocs')}
						suggestions={docSuggestions}
						value={include}
						onChange={(value) => setAttributes({ include: value })}
					/>

					<FormTokenField
						__experimentalAutoSelectFirstMatch
						__experimentalExpandOnFocus
						label={__('Docs Not to Show', 'eazydocs')}
						suggestions={docSuggestions}
						value={exclude}
						onChange={(value) => setAttributes({ exclude: value })}
					/>

					<TextControl
						help={__(
							'Button/link to get the full docs',
							'eazydocs'
						)}
						label={__('More Button Label', 'eazydocs')}
						value={more}
						onChange={(value) => setAttributes({ more: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				[eazydocs {columns} {include_doc_ids} {exclude_doc_ids} {ppp}
				{articles} {more_txt}]
			</div>
		</>
	);
}

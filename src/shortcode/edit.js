import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { PanelBody, FormTokenField, SelectControl, CheckboxControl, ComboboxControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

const { Fragment } = wp.element;

// editor style
import './editor.scss';

// colors
import colors from '../colors-palette';

export default function Edit( { attributes, setAttributes } ) {
	const { col, include, exclude, show_docs, show_articles, more } = attributes;
	const blockProps = useBlockProps();

	const docs = useSelect( (select) => {
		return select("core").getEntityRecords('postType', 'docs', {
			parent: 0,
		})
	}, [])


	const docSuggestions = docs && docs.map( ( doc ) => {
		return doc.title.rendered
	})

	console.log( docSuggestions )

	// Set attributes value
	const onChangeCol = ( newCol ) => {
		setAttributes({
			col: newCol == '' ? 3 : newCol
		})
	}

	const onChangeIncludeDocs = (values) => {
		const hasNoSuggestions = values.some(
			(value) => typeof value === 'string' && !docSuggestions[value]
		);
		if (hasNoSuggestions) return;

		const updatedDocs = values.map((token) => {
			return typeof token === 'string' ? docSuggestions[token] : token;
		});

		setAttributes({ include: updatedDocs });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={__('Filters', 'eazydocs')}
					initialOpen={true}
				>
					<NumberControl
						label={__('Column', 'eazydocs')}
						isShiftStepEnabled={ true }
						onChange={ onChangeCol }
						shiftStep={ 1 }
						value={ col }
						min={1}
						__nextHasNoMarginBottom
					/>
					<br/>

					<FormTokenField
						__experimentalAutoSelectFirstMatch
						__experimentalExpandOnFocus
						label="Docs to Show"
						suggestions={docSuggestions}
						value={include}
						onChange={onChangeIncludeDocs}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				[eazydocs col="{col}" include="{include}"]
			</div>
		</>
	);
}

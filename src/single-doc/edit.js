import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

import { __experimentalNumberControl as NumberControl } from '@wordpress/components';
import {
	PanelBody,
	SelectControl,
	RangeControl,
	QueryControls,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';

const { Fragment } = wp.element;

// editor style
import './editor.scss';

// Custom functions
import { doc_ids } from '../custom-functions';

// colors
import colors from '../colors-palette';

export default function Edit({ attributes, setAttributes }) {
	const { numberOfPosts, include, exclude, show_docs, child_values, postid } =
		attributes;
	const [error, setError] = useState(null);
	const [metaId, setId] = useState(null);
	const [mypost, setPost] = useState([]);
	const [isLoaded, setIsLoaded] = useState(false);
	const blockProps = useBlockProps();

	useEffect(() => {
		apiFetch({ path: `myplugin/v1/docs?id=2563` }).then(
			(result) => {
				setIsLoaded(true);
				setPost(result);
			},
			(error) => {
				setIsLoaded(true);
				setError(error);
			}
		);
	}, []);
	// console.log(docs);

	// const docSuggestions =
	// 	docs &&
	// 	docs.map((doc) => {
	// 		return doc.id + ' | ' + doc.title.rendered;
	// 	});

	// console.log(docSuggestions);

	// let options = [];
	// if (docs) {
	// 	options.push({ value: 0, label: 'Select a Docs' });
	// 	docs.forEach((docs) => {
	// 		options.push({ value: docs.id, label: docs.title.rendered });
	// 	});
	// } else {
	// 	options.push({ value: 0, label: 'Loading...' });
	// }

	// Set attributes value
	const onChangeCol = (newCol) => {
		setAttributes({
			col: newCol == '' ? 3 : newCol,
		});
	};

	const onNumberOfItemsChange = (value) => {
		setAttributes({ numberOfPosts: value });
	};

	// Shortcode attributes
	let include_doc_ids = doc_ids(include)
		? 'include="' + doc_ids(include) + '"'
		: '';
	let exclude_doc_ids = doc_ids(exclude)
		? 'exclude="' + doc_ids(exclude) + '"'
		: '';
	// let columns = col ? 'col="' + col + '"' : '';
	// let ppp = show_docs ? 'show_docs="' + show_docs + '"' : '';
	// let articles = show_articles ? 'show_articles="' + show_articles + '"' : '';

	// const blocks = useSelect((select) => select(blockStore).getBlocks(), []);
	// const atts = useSelect(
	// 	(select) =>
	// 		select('blockStore').getBlocksAttributes(blocks[0].clientId),
	// 	[]
	// );

	//   const atts = useEffect(
	// 	() => {
	// 	  const child_values = child_blocks.map(
	// 		( { clientId, attributes: { title } } ) => ( { clientId, title } )
	// 	  );
	// 	  setAttributes( { child_values } );
	// 	},
	// 	[ child_blocks ]
	//   );

	// const postid = apiFetch({ path: '/myplugin/v1/docs?id=2563' }).then(
	// 	(posts) => {
	// 		console.log(posts);
	// 	}
	// );

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Filters', 'eazydocs')} initialOpen={true}>
					<QueryControls
						numberOfItems={numberOfPosts}
						onNumberOfItemsChange={onNumberOfItemsChange}
						maxItems={10}
						minItems={1}
					/>

					{/* <small>
						{__(
							'Articles/child-docs show under every Docs.',
							'eazydocs'
						)}
					</small> */}
					<br />
					<br />

					{/* <SelectControl
						label={__('Docs Show', 'eazydocs')}
						options={options}
						onChange={(value) => setAttributes({ include: value })}
						value={include}
						__nextHasNoMarginBottom
					/> */}
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{/* [eazydocs {columns} {include_doc_ids} {exclude_doc_ids}] */}
				{console.log(parentAttributes + 'asdfdsf sdf')}
				{/* {docs &&
					docs.map((docs) => {
						return (
							<div key={docs.id}>
								<div class="col-lg-4 col-sm-6">
									<div class="categories_guide_item box-item wow fadeInUp single-doc-layout-one">
										<div class="doc-top d-flex align-items-start">
											<a class="doc_tag_title">
												<h4 class="title">
													{docs.title.rendered}
												</h4>
											</a>
										</div>
										<ul class="list-unstyled tag_list"></ul>
									</div>
								</div>
							</div>
						);
					})} */}
			</div>
		</>
	);
}

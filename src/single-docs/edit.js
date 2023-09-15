import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { __experimentalNumberControl as NumberControl } from '@wordpress/components';
import {
	PanelBody,
	SelectControl,
	QueryControls,
	TextControl,
} from '@wordpress/components';
import './editor.scss';
import { useSelect } from '@wordpress/data';

export default function Edit({ attributes, setAttributes }) {
	const { numberOfPosts } = attributes;
	const blockProps = useBlockProps();
	const docs = useSelect(
		(select) => {
			return select('core').getEntityRecords('postType', 'docs', {
				parent: 0,
				per_page: numberOfPosts,
				_embed: true,
				status: ['publish', 'private'],
			});
		},
		[numberOfPosts]
	);
	const onNumberOfItemsChange = (value) => {
		setAttributes({ numberOfPosts: value });
	};
	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Filters', 'eazydocs')} initialOpen={true}>
					<QueryControls
						numberOfItems={numberOfPosts}
						onNumberOfItemsChange={onNumberOfItemsChange}
						maxItems={10}
						minItems={1}
						orderBy=""
						onOrderByChange={(value) => console.log(value)}
						order=""
						onOrderChange={(value) => console.log(value)}
						categorySuggestions={catSuggestions}
						selectedCategories={categories}
						onCategoryChange={onCategoryChange}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				{/* [eazydocs {columns} {include_doc_ids} {exclude_doc_ids}] */}
				{docs &&
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
					})}
			</div>
		</>
	);
}

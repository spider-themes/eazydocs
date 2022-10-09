import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody, ColorPalette } from '@wordpress/components';
const { Fragment } = wp.element;
import { useSelect } from '@wordpress/data';

// editor style
import './editor.scss';

// colors
import colors from '../colors-palette';

/**
 * Editor appearance and fields
 * @param attributes
 * @param setAttributes
 * @returns {JSX.Element}
 * @constructor
 */
export default function Edit({ attributes, setAttributes }) {
	const siteURL = useSelect( (select) => {
		return select('core').getSite().url;
	})
	const settingsPage = siteURL + '/wp-admin/admin.php?page=eazydocs-settings#tab=doc-single/search-banner';
	console.log(settingsPage)
	return (
		<Fragment>
			<InspectorControls>
				<PanelBody>
					<small>
						The search banner settings/ background image, colors, keywords are getting from the plugin's
						<a href={settingsPage} target="_blank"> Settings page </a>.
					</small>
				</PanelBody>
			</InspectorControls>

			<div {...useBlockProps()}>
				<div className="focus_overlay"></div>
				<section className="ezd_search_banner has_bg_dark no_cs_bg">
					<div className="container">
						<div className="row doc_banner_content">
							<div className="col-md-12">
								<form action="" role="search" method="post" className="ezd_search_form">
									<div className="header_search_form_info">
										<div className="form-group">
											<div className="input-wrapper">
												<input type='search' id="ezd_searchInput" name="s" placeholder="Search here"/>
												<label htmlFor="ezd_searchInput">
													<i className="icon_search"></i>
												</label>
												<div className="spinner-border spinner" role="status">
													<span className="visually-hidden">Loading...</span>
												</div>
											</div>
										</div>
									</div>
									<div id="ezd-search-results" className="eazydocs-search-tree" data-noresult="No Results Found"></div>
								</form>
							</div>
						</div>
					</div>
				</section>
			</div>
		</Fragment>
	);
}
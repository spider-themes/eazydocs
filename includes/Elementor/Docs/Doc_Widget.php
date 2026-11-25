<?php
/**
 * Use namespace to avoid conflict
 */
namespace EazyDocs\Elementor\Docs;

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use WP_Query;
use WP_Post;


require_once __DIR__ . '/Doc_Widget_Content_Controls.php';
require_once __DIR__ . '/Doc_Widget_Style_Controls.php';

class Doc_Widget extends Widget_Base
{
	use Doc_Widget_Content_Controls;
	use Doc_Widget_Style_Controls;

	public function get_name()
	{
		return 'ezd_docs';
	}

	public function get_title()
	{
		return esc_html__('(EazyDocs) MultiDocs', 'eazydocs');
	}

	public function get_icon()
	{
		return 'eicon-document-file';
	}

	public function get_categories()
	{
		return ['eazydocs'];
	}

	public function get_style_depends()
	{
		return ['ezd-docs-widgets', 'elegant-icon'];
	}

	public function get_script_depends()
	{
		return ['ezd-script-handle', 'scrollspy'];
	}

	public function get_keywords()
	{
		return ['eazydocs', 'docs', 'documentations', 'knowledge base', 'knowledgebase', 'kb', 'eazydocs'];
	}

	/*
	 * Tab ID Format
	 */
	public function tab_id_format($doc_id, $prefix)
	{
		$settings = $this->get_settings_for_display();
		$slug_type = $settings['docs_slug_format'] ?? '';
		$widget_id = $this->get_id();

		$post_title_slug = get_post_field('post_name', $doc_id);

		if ($slug_type == 1) {
			$tab_id = $prefix . '-' . $post_title_slug;
		} else {
			$tab_id = $prefix . '-' . $widget_id . $doc_id;
		}

		echo esc_attr($tab_id);
	}

	/**
	 * Name: register_controls()
	 * Desc: Register controls for these widgets
	 * Params: no params
	 * Return: @void
	 * Author: spider-themes
	 */
	protected function register_controls()
	{
		$this->elementor_content_control();
		$this->elementor_style_control();
	}

	/**
	 * Docs Layout Options based on license for Elementor Widget
	 *
	 * @return array[]
	 */
	protected function docs_layout_option()
	{
		$base_options = [
			'1' => [
				'title' => esc_html__('Docs without tab', 'eazydocs'),
				'image' => EAZYDOCS_IMG . 'elementor-skins/no-tab-docs.jpg'
			],
		];

		$pro_options = [
			'2' => ['title' => esc_html__('Tabbed with doc lists', 'eazydocs')],
			'3' => ['title' => esc_html__('Flat tabbed docs', 'eazydocs')],
			'4' => ['title' => esc_html__('Boxed Style', 'eazydocs')],
			'5' => ['title' => esc_html__('Book Chapters / Tutorials', 'eazydocs')],
			'6' => ['title' => esc_html__('List Style', 'eazydocs')],
			'7' => ['title' => esc_html__('Topics Grid', 'eazydocs')]
		];

		foreach ($pro_options as $key => $option) {
			$icon_suffix = ezd_unlock_themes('docy', 'docly') ? '' : ' ezd-pro-docs';
			$base_options[$key] = [
				'title' => $option['title'],
				'image' => EAZYDOCS_IMG . "elementor-skins/docs-" . ($key - 1) . ".png" . $icon_suffix
			];
		}

		return $base_options;
	}

	/**
	 * Name: elementor_render()
	 * Desc: Render the widget output on the frontend.
	 * Params: no params
	 * Return: @void
	 * Author: spider-themes
	 */
	protected function render()
	{
		$settings = $this->get_settings();
		$doc_number = $settings['ppp_doc_items'] ?? -1;
		$read_more = $settings['read_more'] ?? '';
		$doc_order = $settings['order'] ?? 'asc'; // 'asc' or 'desc'
		$child_order = $settings['child_order'] ?? 'asc';
		$order_by = $settings['order_by'] ?? 'menu_order'; // e.g., 'post_title', 'menu_order', 'post_date'
		$doc_exclude = $settings['exclude'] ?? '';
		$topics_label = !empty($settings['topics_label']) ? $settings['topics_label'] : esc_html__('Topics', 'eazydocs');
		$hide_empty = ($settings['md_hide_empty_docs'] ?? '') === 'yes'; // Hide docs without child docs (all skins).
		// IDs of docs with no child docs — excluded at the query level so "Number of Docs" stays accurate.
		$empty_doc_ids = ( $hide_empty && function_exists('ezd_get_empty_doc_ids') ) ? ezd_get_empty_doc_ids( ['publish', 'private'] ) : [];

		// Restricted docs visibility toggles (shared by every skin).
		$show_private   = ezd_setting_enabled( $settings, 'md_show_private_docs' );
		$show_protected = ezd_setting_enabled( $settings, 'md_show_protected_docs' );
		$doc_statuses   = ezd_doc_listing_statuses( $show_private );

		// Map Elementor 'orderby' options to get_pages 'sort_column'
		$valid_sort_columns = [
			'ID',
			'post_title',
			'menu_order',
			'post_date',
			'post_modified',
		];

		$sort_column = in_array($order_by, $valid_sort_columns) ? $order_by : 'menu_order';

		$args = array(
			'post_type' => 'docs',
			'parent' => 0,
			'sort_column' => $sort_column,
			'sort_order' => $doc_order,
			'post_status' => $doc_statuses,
		);

		$exclude_list = [];
		if (!empty($doc_exclude)) {
			$exclude_list = is_array($doc_exclude) ? $doc_exclude : array_map('trim', explode(',', $doc_exclude));
		}
		$exclude_list = array_merge($exclude_list, $empty_doc_ids);
		if (!empty($exclude_list)) {
			$args['exclude'] = $exclude_list;
		}

		$parent_docs = get_pages($args);

		// Drop password-protected (and, as a safety net, private) parents per the toggles.
		$parent_docs = ezd_filter_doc_visibility($parent_docs, $show_private, $show_protected);


		/**
		 * Get the doc sections
		 */
		if ($parent_docs) {
			// Optimized approach: Fetch all children in one query
			$parent_ids = wp_list_pluck($parent_docs, 'ID');
			$sections_by_parent = array();

			if (!empty($parent_ids)) {
				$all_sections = get_posts(array(
					'post_parent__in' => $parent_ids,
					'post_type' => 'docs',
					'post_status' => $doc_statuses,
					'orderby' => $order_by,
					'order' => $child_order,
					'posts_per_page' => -1,
				));

				// Respect the protected/private toggles on child sections too.
				$all_sections = ezd_filter_doc_visibility($all_sections, $show_private, $show_protected);

				foreach ($all_sections as $section) {
					$sections_by_parent[$section->post_parent][$section->ID] = $section;
				}
			}

			$limit = !empty($settings['show_section_count']) ? $settings['show_section_count'] : -1;

			foreach ($parent_docs as $root) {
				$sections = isset($sections_by_parent[$root->ID]) ? $sections_by_parent[$root->ID] : array();

				// Skip docs with no child docs when "Hide Empty Docs" is enabled (skins 1-6; skin 7 self-filters).
				if ($hide_empty && empty($sections)) {
					continue;
				}

				if ($limit != -1 && count($sections) > $limit) {
					$sections = array_slice($sections, 0, $limit, true);
				}

				$docs[] = array(
					'doc' => $root,
					'sections' => $sections,
				);

			}
		}

		// Whitelist valid skin values to prevent Local File Inclusion
		$pro_skins  = array('2', '3', '4', '5', '6');
		$free_skins = array('1', '7');

		if (ezd_unlock_themes('docy', 'docly')) {
			$allowed_skins = array_merge($free_skins, $pro_skins);
			$skin = isset($settings['doc-widget-skin']) && in_array($settings['doc-widget-skin'], $allowed_skins, true) ? $settings['doc-widget-skin'] : '1';
		} else {
			$skin = isset($settings['doc-widget-skin']) && in_array($settings['doc-widget-skin'], $free_skins, true) ? $settings['doc-widget-skin'] : '1';
		}

		include(__DIR__ . "/docs-{$skin}.php");

		?>

		<script type="text/javascript">
			;
			(function ($) {
				"use strict";

				$(document).ready(function () {

					// === Tabs Slider
					var tabId = "#Arrow_slides-<?php echo esc_js($this->get_id()) ?>";
					var tabSliderContainers = $(tabId + " .tabs_sliders");

					tabSliderContainers.each(function () {
						let tabWrapWidth = $(this).outerWidth();
						let totalWidth = 0;

						let slideArrowBtn = $(tabId + " .scroller-btn");
						let slideBtnLeft = $(tabId + " .scroller-btn.left");
						let slideBtnRight = $(tabId + " .scroller-btn.right");
						let navWrap = $(tabId + " .slide_nav_tabs");
						let navWrapItem = $(tabId + " .slide_nav_tabs li");

						navWrapItem.each(function () {
							totalWidth += $(this).outerWidth();
						});

						if (totalWidth > tabWrapWidth) {
							slideArrowBtn.removeClass("inactive");
						} else {
							slideArrowBtn.addClass("inactive");
						}

						if (navWrap.scrollLeft() === 0) {
							slideBtnLeft.addClass("inactive");
						} else {
							slideBtnLeft.removeClass("inactive");
						}

						slideBtnRight.on("click", function () {
							navWrap.animate({
								scrollLeft: "+=200px"
							}, 300);
							console.log(navWrap.scrollLeft() + " px");
						});

						slideBtnLeft.on("click", function () {
							navWrap.animate({
								scrollLeft: "-=200px"
							}, 300);
						});

						scrollerHide(navWrap, slideBtnLeft, slideBtnRight);
					});

					function scrollerHide(navWrap, slideBtnLeft, slideBtnRight) {
						let scrollLeftPrev = 0;
						navWrap.scroll(function () {
							let $elem = $(this);
							let newScrollLeft = $elem.scrollLeft(),
								width = $elem.outerWidth(),
								scrollWidth = $elem.get(0).scrollWidth;
							if (scrollWidth - newScrollLeft === width) {
								slideBtnRight.addClass("inactive");
							} else {
								slideBtnRight.removeClass("inactive");
							}
							if (newScrollLeft === 0) {
								slideBtnLeft.addClass("inactive");
							} else {
								slideBtnLeft.removeClass("inactive");
							}
							scrollLeftPrev = newScrollLeft;
						});
					}

					// Storage key for persisting the last opened tab of this widget.
					var ezdTabStorageKey = 'ezd_active_tab_' + tabId.replace('#', '');

					// custom tab js
					$('.ezd-tab-menu li a').on('click', function (e) {
						e.preventDefault();

						// Remove active class from all tabs within the same menu
						$(this).closest('.ezd-tab-menu').find('li a').removeClass('active');

						// Add active class to the clicked tab
						$(this).addClass('active');

						var target = $(this).attr('data-rel');

						$('#' + target)
							.addClass('active')
							.siblings('.ezd-tab-box')
							.removeClass('active');

						// Remember the last opened tab for its widget container.
						var $widget = $(this).closest('[id^="Arrow_slides-"]');
						if ($widget.length) {
							try {
								window.localStorage.setItem('ezd_active_tab_' + $widget.attr('id'), target);
							} catch (err) {}
						}

						return false;
					});

					// Restore the last opened tab for this widget on page load.
					try {
						var ezdSavedTab = window.localStorage.getItem(ezdTabStorageKey);
						if (ezdSavedTab) {
							var $savedLink = $(tabId + ' .ezd-tab-menu li a[data-rel="' + ezdSavedTab + '"]');
							var $savedPane = $(tabId + ' #' + ezdSavedTab);

							if ($savedLink.length && $savedPane.length) {
								$(tabId + ' .ezd-tab-menu li a').removeClass('active');
								$savedLink.addClass('active');
								$savedPane.addClass('active').siblings('.ezd-tab-box').removeClass('active');
							}
						}
					} catch (err) {}

				});
			})(jQuery);
		</script>
		<?php

	}
}


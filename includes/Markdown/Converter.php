<?php
/**
 * Markdown <-> HTML conversion for EazyDocs.
 *
 * Wraps league/commonmark (Markdown -> HTML) and league/html-to-markdown
 * (HTML -> Markdown). The bundled CommonMark release requires PHP 8.1+, so the
 * whole Markdown feature degrades gracefully on older PHP via is_available().
 *
 * @package EazyDocs\Markdown
 */

namespace EazyDocs\Markdown;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\HTMLToMarkdown\HtmlConverter;

defined( 'ABSPATH' ) || exit;

class Converter {

	/**
	 * Minimum PHP version required by the bundled CommonMark (2.8.x).
	 */
	const MIN_PHP = 80100;

	/**
	 * Whether Markdown conversion can run in this environment.
	 *
	 * @return bool
	 */
	public static function is_available() {
		return PHP_VERSION_ID >= self::MIN_PHP
			&& class_exists( GithubFlavoredMarkdownConverter::class )
			&& class_exists( HtmlConverter::class );
	}

	/**
	 * Convert Markdown to HTML that is safe to store as doc content.
	 *
	 * Raw HTML embedded in the Markdown is stripped, and the result is passed
	 * through wp_kses_post() as defence in depth.
	 *
	 * @param string $markdown
	 * @return string Sanitised HTML.
	 */
	public static function markdown_to_html( $markdown ) {
		if ( ! self::is_available() ) {
			return '';
		}

		$converter = new GithubFlavoredMarkdownConverter(
			[
				'html_input'         => 'strip',
				'allow_unsafe_links' => false,
			]
		);

		$html = (string) $converter->convert( (string) $markdown );

		return wp_kses_post( $html );
	}

	/**
	 * Convert rendered doc HTML to Markdown.
	 *
	 * @param string $html Already-rendered HTML (blocks/shortcodes expanded).
	 * @return string Markdown.
	 */
	public static function html_to_markdown( $html ) {
		if ( ! self::is_available() ) {
			return '';
		}

		$converter = new HtmlConverter(
			[
				'strip_tags'   => true,
				'remove_nodes' => 'script style',
				'hard_break'   => true,
				'header_style' => 'atx',
			]
		);

		return trim( $converter->convert( (string) $html ) );
	}
}

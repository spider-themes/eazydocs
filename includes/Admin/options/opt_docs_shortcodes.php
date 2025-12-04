<?php
/**
 * Shortcode Reference
 * Quick reference for all available EazyDocs shortcodes.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


//
// Shortcode Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'shortcode_fields',
	'title'  => esc_html__( 'Shortcode Reference', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-shortcode',
	'fields' => [

		array(
			'id'         => 'eazydocs_docs_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Documentation Grid', 'eazydocs' ),
			'subtitle' => sprintf(
				// translators: %1$s: opening HTML anchor tag, %2$s: closing HTML anchor tag
				__( 'Display your documentation in a grid layout. %1$sView full documentation%2$s for all available options.', 'eazydocs' ),
				'<a href="https://tinyurl.com/24zm4oj3" target="_blank">',
				'</a>'
			),
			'desc'       => esc_html__( 'Available attributes:', 'eazydocs' )
			                . '<br><code>[eazydocs col="3" include="" exclude="" show_docs="" show_articles="" more="View More"]</code>',
			'default'    => '[eazydocs col="3" include="" exclude="" show_docs="" show_articles="" more="View More"]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		),

		array(
			'id'         => 'conditional_data_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Conditional Content', 'eazydocs' ),
			// translators: %1$s opening HTML anchor tag, %2$s closing HTML anchor tag
			'subtitle' => sprintf( esc_html__( 'Show or hide content based on dropdown selections. %1$sLearn more%2$s', 'eazydocs' ),
				'<a href="https://tinyurl.com/24d9rw72" target="_blank">', '</a>' ),
			'default'    => '[conditional_data dependency=""]Conditional Data[/conditional_data]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		),

		array(
			'id'         => 'ezdocs_login_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Login Form', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Display a customizable login form for restricted documentation access.', 'eazydocs' ),
			'desc'       => esc_html__( 'Example with all attributes:', 'eazydocs' )
			                . '<br><code>[ezd_login_form login_title="You must log in to continue."  login_subtitle="Login to ' . get_bloginfo()
			                . '" login_btn="Log In" login_forgot_btn="Forgotten account?"]</code>',
			'default'    => '[ezd_login_form login_title="" login_subtitle="" login_btn=""]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice'
		),

		array(
			'id'         => 'ezdocs_footnote_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Footnote Reference', 'eazydocs' ),
			/* translators: %1$s opening HTML anchor tag, %2$s closing HTML anchor tag */
			'subtitle' => sprintf( esc_html__( 'Add numbered footnotes with tooltips to your documentation. %1$sSetup guide%2$s', 'eazydocs' ),
				'<a href="https://tinyurl.com/2ewlorze" target="_blank">', '</a>' ),
			'desc'       => esc_html__( 'Usage example:', 'eazydocs' )
			                . '<br><code>[reference number="1"]Tooltip Content[/reference]</code>',
			'default'    => '[reference number="1"]Tooltip Content[/reference]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice active-theme-docy active-theme-docly active-theme-ama'
		),

		array(
			'id'         => 'ezdocs_embed_post_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Embed Documentation', 'eazydocs' ),
			/* translators: %1$s opening HTML anchor tag, %2$s closing HTML anchor tag */
			'subtitle' => sprintf( esc_html__( 'Embed content from one doc into another to avoid duplication. %1$sLearn more%2$s',
				'eazydocs' ), '<a href="https://tinyurl.com/bde27yn4" target="_blank">', '</a>' ),
			'desc'       => esc_html__( 'Available attributes:', 'eazydocs' )
			                . '<br><code>[embed_post id="POST_ID" limit="no" thumbnail="yes"]</code> <br>',
			'default'    => '[embed_post id="POST_ID" limit="no" thumbnail="yes"]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice'
		)
	]
) );
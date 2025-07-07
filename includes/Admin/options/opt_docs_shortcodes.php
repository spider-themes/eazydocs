<?php

//
// Shortcode Fields
//
CSF::createSection( $prefix, array(
	'id'     => 'shortcode_fields',
	'title'  => esc_html__( 'Docs Shortcodes', 'eazydocs' ),
	'icon'   => 'dashicons dashicons-shortcode',
	'fields' => [

		array(
			'id'         => 'eazydocs_docs_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Docs archive', 'eazydocs' ),
			/* translators: %1$s: opening HTML anchor tag, %2$s: closing HTML anchor tag */
			'subtitle' => sprintf(
				__( 'Use this shortcode to display the Docs. Learn more about the shortcode and the attributes %1$s here %2$s.', 'eazydocs' ),
				'<a href="https://tinyurl.com/24zm4oj3" target="_blank">',
				'</a>'
			),
			'desc'       => esc_html__( 'See the shortcode with the available attributes', 'eazydocs' )
			                . '<br><code>[eazydocs col="3" include="" exclude="" show_docs="" show_articles="" more="View More"]</code>',
			'default'    => '[eazydocs col="3" include="" exclude="" show_docs="" show_articles="" more="View More"]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		),

		array(
			'id'         => 'conditional_data_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Conditional Dropdown', 'eazydocs' ),
			// translators: %1$s opening HTML anchor tag, %2$s closing HTML anchor tag
			'subtitle' => sprintf( esc_html__( 'Know the usage of this shortcode %1$s here %2$s', 'eazydocs' ),
				'<a href="https://tinyurl.com/24d9rw72" target="_blank">', '</a>' ),
			'default'    => '[conditional_data dependency=""]Conditional Data[/conditional_data]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		),

		array(
			'id'         => 'ezdocs_login_shortcode',
			'type'       => 'text',
			'title'      => esc_html__( 'Docs Login', 'eazydocs' ),
			'subtitle'   => esc_html__( 'Use this shortcode to display login form.', 'eazydocs' ),
			'desc'       => esc_html__( 'See the shortcode example with the available attributes', 'eazydocs' )
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
			'title'      => esc_html__( 'Footnote Shortcode', 'eazydocs' ),
			/* translators: %1$s opening HTML anchor tag, %2$s closing HTML anchor tag */
			'subtitle' => sprintf( esc_html__( 'Use this shortcode to display footnotes. %1$s Learn how to create Footnotes %2$s', 'eazydocs' ),
				'<a href="https://tinyurl.com/2ewlorze" target="_blank">', '</a>' ),
			'desc'       => esc_html__( 'See the shortcode example with the available attributes', 'eazydocs' )
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
			'title'      => esc_html__( 'Embed Post Shortcode', 'eazydocs' ),
			/* translators: %1$s opening HTML anchor tag, %2$s closing HTML anchor tag */
			'subtitle' => sprintf( esc_html__( 'Use this shortcode to display a doc inside another doc. Know the usage of this shortcode %1$s here %2$s',
				'eazydocs' ), '<a href="https://tinyurl.com/bde27yn4" target="_blank">', '</a>' ),
			'desc'       => esc_html__( 'See the shortcode with the available attributes.', 'eazydocs' )
			                . '<br><code>[embed_post id="POST_ID" limit="no" thumbnail="yes"]</code> <br>',
			'default'    => '[embed_post id="POST_ID" limit="no" thumbnail="yes"]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
			'class'      => 'eazydocs-pro-notice'
		)
	]
) );
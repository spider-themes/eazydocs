<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

ezd_render_setup_step_wrapper(1, esc_html__( 'Welcome to EazyDocs', 'eazydocs' ), esc_html__( 'Discover EazyDocs by this guide that walks you through creating professional, user-friendly website documentation seamlessly. Then click next to setup initial settings.', 'eazydocs' ), true, 'setup-welcome');
?>

	<iframe width="650" height="350" src="https://www.youtube.com/embed/4H2npHIR2qg?si=ApQh7BL6CL5QM4zX" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

	<?php
	ezd_render_setup_buttons([
		[
			'text' => esc_html__( 'Documentation', 'eazydocs' ),
			'href' => 'https://helpdesk.spider-themes.net/docs/eazydocs-wordpress-plugin/',
			'target' => '_blank',
			'icon' => 'sos'
		],
		[
			'text' => esc_html__( 'Video Tutorials', 'eazydocs' ),
			'href' => 'https://www.youtube.com/playlist?list=PLeCjxMdg411XgYy-AekTE-bhvCXQguZWJ',
			'target' => '_blank',
			'class' => 'btn-red',
			'icon' => 'playlist-video'
		],
		[
			'text' => esc_html__( 'Support', 'eazydocs' ),
			'href' => 'https://wordpress.org/support/plugin/eazydocs/',
			'target' => '_blank',
			'class' => 'ezd-btn-pro',
			'icon' => 'editor-help'
		]
	]);
	?>
</div>
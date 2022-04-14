<?php
$settings_options       = '';
$update_text            = '';
if ( class_exists( 'EazyDocsPro' ) ) {
	$settings_options   = get_option( 'eazydocs_settings' ); // prefix of framework
	$update_text        = $settings_options['breadcrumb-update-text'] ?? ''; // id of field
}
?>
<section class="page_breadcrumb">
	<div class="container custom_container">
		<div class="row">
			<div class="col-lg-9 col-md-8">
				<nav aria-label="breadcrumb">
					<?php eazydocs_breadcrumbs();  ?>
				</nav>
			</div>
			<div class="col-lg-3 col-md-4">
                <time itemprop="dateModified" datetime="<?php the_modified_time(get_option('date_format')); ?>" class="date">
                    <i class="icon_clock_alt"></i>
                    <?php echo esc_html( $update_text ); ?>
	                <?php the_modified_time(get_option('date_format')); ?>
                </time>
			</div>
		</div>
	</div>
</section>
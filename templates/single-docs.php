<?php
/**
 * The template for displaying a single doc
 *
 * To customize this template, create a folder in your current theme named "eazydocs" and copy it there.
 *
 * @package eazydocs
 */

get_header();
?>
    <section class="doc_documentation_area" id="sticky_doc">
        <div class="overlay_bg"></div>
		<?php eazydocs_get_template_part( 'breadcrumbs' ); ?>
        <div class="container custom_container">
            <div class="row">
				<?php
				while ( have_posts() ) : the_post();
					eazydocs_get_template_part( 'docs-sidebar' );
					?>
                    <div class="col-lg-7 doc-middle-content">
						<?php eazydocs_get_template_part( 'single-doc-content' ); ?>
                    </div>
					<?php
					eazydocs_get_template_part( 'docs-right-sidebar' );
				endwhile;
				?>
            </div>
        </div>
    </section>

<?php
get_footer();
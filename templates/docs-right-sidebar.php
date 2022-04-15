<?php
$options       = get_option( 'eazydocs_settings' );
$article_print = $options['pr-icon-switcher'] ?? '';
?>
<div class="col-lg-2 col-md-4 doc_right_mobile_menus">
    <div class="open_icon" id="right">
        <i class="arrow_carrot-left"></i>
        <i class="arrow_carrot-right"></i>
    </div>
    <div class="doc_rightsidebar scroll">
        <div class="pageSideSection">
            <div id="font-switcher" class="d-flex justify-content-between align-items-center">
                <div id="rvfs-controllers" class="fontsize-controllers group">
                    <div class="btn-group">
                        <button id="switcher-small" class="rvfs-decrease btn" title="<?php esc_attr_e( 'Decrease font size', 'eazydocs' ); ?>">
							<?php esc_html_e( 'A-', 'eazydocs' ); ?>
                        </button>
                        <button id="switcher-default" class="rvfs-reset btn" title="<?php esc_attr_e( 'Default font size', 'eazydocs' ); ?>">
							<?php esc_html_e( 'A', 'eazydocs' ); ?>
                        </button>
                        <button id="switcher-large" class="rvfs-increase btn" title="<?php esc_attr_e( 'Increase font size', 'eazydocs' ); ?>">
							<?php esc_html_e( 'A+', 'eazydocs' ); ?>
                        </button>
                    </div>
                </div>
				<?php
				if ( $article_print == '1' ) : ?>
                    <a href="#" class="print"><i class="icon_printer"></i></a>
				<?php endif; ?>
            </div>
            <div class="table-of-content">
                <h6><i class="icon_ul"></i> <?php esc_html_e( 'CONTENTS', 'eazydocs' ); ?></h6>
                <nav class="list-unstyled doc_menu" id="eazydocs-toc"></nav>
            </div>
        </div>
    </div>
</div>
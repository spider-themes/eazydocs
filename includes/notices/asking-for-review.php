<?php
$optionReview = get_option('ezd_notify_review');
if ( time() >= (int)$optionReview && $optionReview !== '0' ) {
	$ezd_installed = get_option('eazyDocs_installed');
	// Check if timestamp exists
	if ( $ezd_installed ) {
		// Add 7days to the timestamp
		$show_notice = $ezd_installed + (7 * 24 * 60 * 60);

		// Get the current time
		$current_time = current_time('timestamp');

		// Compare current time with timestamp + 7 days
		if ($current_time >= $show_notice) {
			add_action('admin_notices', 'ezd_notify_give_review');
		}
	}
}

add_action('wp_ajax_ezd_notify_save_review', 'ezd_notify_save_review');

/**
 ** Give Notice
 **/
function ezd_notify_give_review() {
	$docs = get_pages( [
		'post_type'   => 'docs',
		'parent'      => 0,
		'post_status' => 'publish'
	] );

	$articles = get_pages( [
		'post_type'   => 'docs',
		'post_status' => 'publish'
	] );
	?>
    <div class="notice notice-success is-dismissible" id="ezd_notify_review">
		<div>
			<img src="<?php echo EAZYDOCS_IMG . '/eazydocs-logo.png' ?>" />
		</div>
		<div>
			<h3><?php esc_html_e( 'Give EazyDocs a review', 'eazydocs' ); ?></h3>
			<p style="margin-top: 10px;">
				<?php
				if ( count( $docs ) <= 0 ) {
					esc_html_e( 'Thank you for choosing EazyDocs. We hope you love it. Could you take a couple of seconds posting a nice review to share your happy experience?', 'eazydocs' );
				} else {
					$articles_count = count( $articles ) - count( $docs );
					$articles_text  = $articles_count > 0 ? " and <b>" . $articles_count . "</b> articles" : '';
					echo wp_kses(
						sprintf(
						/* translators: 1: number of docs created, 2: additional articles text */
							__( 'You have created <b>%1$d</b> docs%2$s with EazyDocs. That\'s awesome! May we ask you to give it a 5-Star rating on WordPress? It will help us spread the word and boost our motivation.', 'eazydocs' ),
							count( $docs ),
							$articles_text
						),
						array(
							'b'      => array(), // Allow <b> tag
							'strong' => array(), // If needed, allow <strong> tag
						)
					);
				}
				?>
			</p>
			<p class="ezd_notify_review_subheading" style="margin-bottom: 12px;">
				<?php esc_html_e( 'We will be forever grateful. Thank you in advance.', 'eazydocs' ); ?>
			</p>
			<div class="action_links">
				<a href="javascript:;" data="rateNow" class="button button-primary" style="margin-right: 5px">
					<?php esc_html_e( 'Ok, you deserve', 'eazydocs' ) ?>
					<span class='icon_star'></span>
					<span class='icon_star'></span>
					<span class='icon_star'></span>
					<span class='icon_star'></span>
					<span class='icon_star'></span>
				</a>
				<a href="javascript:;" class="btn" data="later" style="margin-right: 5px"> 
					<span class='icon icon_clock_alt'></span>
					<?php esc_html_e( 'Nope, maybe later', 'eazydocs' ) ?> 
				</a>
				<a href="javascript:;" class="btn red" data="alreadyDid">
					<span class='icon icon_close_alt2'></span>
					<?php esc_html_e( 'Never show again', 'eazydocs' ) ?> 
				</a>
			</div>
		</div>
    </div>

    <script>
        jQuery(document).ready(function () {
            // Show review popup
            jQuery("#ezd_notify_review a").on("click", function () {
                const thisElement = this;
                const fieldValue = jQuery(thisElement).attr("data");
                const freeLink = "https://wordpress.org/support/plugin/eazydocs/reviews/#new-post";
                let hidePopup = false;
                if ( fieldValue === "rateNow" ) {
                    window.open(freeLink, "_blank");
                } else {
                    hidePopup = true;
                }

                jQuery
                    .ajax({
                        dataType: 'json',
                        url: eazydocs_local_object.ajaxurl,
                        type: "post",
                        data: {
                            action: "ezd_notify_save_review",
                            field: fieldValue,
                            nonce: eazydocs_local_object.nonce,
                        },
                    })
                    .done(function (result) {
                        if (hidePopup == true) {
                            jQuery("#ezd_notify_review .notice-dismiss").trigger("click");
                        }
                    })
                    .fail(function (res) {
                        if (hidePopup == true) {
                            console.log(res.responseText);
                            jQuery("#ezd_notify_review .notice-dismiss").trigger("click");
                        }
                    });
            })
        })
    </script>
	<?php
}

/**
 ** Save Notice
 **/
function ezd_notify_save_review() {
	if ( isset( $_POST ) ) {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : null;
		$field = isset( $_POST['field'] ) ? sanitize_text_field( wp_unslash( $_POST['field'] ) ) : null;

		if ( ! wp_verify_nonce( $nonce, 'eazydocs-admin-nonce' ) ) {
			wp_send_json_error( array( 'status' => 'Wrong nonce validate!' ) );
			exit();
		}

		if ( $field == 'later' ) {
			update_option( 'ezd_notify_review', time() + 3 * 60 * 60 * 24 ); //After 3 days show
		} else if ( $field == 'alreadyDid' ) {
			update_option( 'ezd_notify_review', 0 );
		}
		wp_send_json_success();
	}
	wp_send_json_error( array( 'message' => 'Update fail!' ) );
}
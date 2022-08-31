<?php
if ( 'yes' == $fields['is_keywords'] && ! empty( $fields['keywords'] ) ) : ?>
	<div class="header_search_keyword eazydocs-block-keywords justify-content-center">
		<?php if ( ! empty( $fields['keywords_label'] ) ) : ?>
			<span class="header-search-form__keywords-label search_keyword_label"> <?php echo esc_html( $fields['keywords_label'] ); ?> </span>
		<?php endif; ?>
		<?php if ( ! empty( $fields['keywords'] ) ) : ?>
			<ul class="list-unstyled">
				<?php foreach ( $fields['keywords'] as $keyword ) : ?>
					<li class="wow fadeInUp" data-wow-delay="0.2s">
						<a class="has-bg" href="#"> <?php echo esc_html( $keyword['keyword'] ); ?> </a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
<?php endif; ?>

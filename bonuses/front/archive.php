<?php

$page_id       = $object->ID;
$predicts_type = 'bookmakers';
$after_text = $second_content;
$faq_info = get_post_meta( get_the_ID(), '_faq_info', true );
?>
<?php if ( isset( $faq_info ) && $faq_info['counter__0'][0] != '' ): ?>
	<?php $faq_info_count = count( $faq_info ); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [<?php
                 $fi = 1;
				foreach ( $faq_info as $value ) {
					if ( $value[0] != '' ):
				echo '{';
				echo '"@type": "Question",';
				echo '"name": ' . do_shortcode( wp_json_encode( $value[0], JSON_UNESCAPED_SLASHES ) ) . ',';
				echo '"acceptedAnswer": {';
				echo '"@type": "Answer",';
				echo '"text": ' . do_shortcode( wp_json_encode( $value[1], JSON_UNESCAPED_SLASHES ) );
				echo '}';
				echo '}';
				if ( $fi < $faq_info_count ) {
					echo ',';
				}
				$fi ++;
				endif;
			}
            ?>]
        }
    </script>
<?php endif; ?>
<div class="wrapper">
    <div class="bookmaker-items-header saving-place">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				?>
                <h1><?php echo $archive_title ?></h1>

				<?php
			}
		}
		?>
    </div>
	<?php

        $adrotate = new Options('adrotate_banner');

        if($adrotate->getOption('filter_bonuses_desktop') || $adrotate->getOption('filter_bonuses_mobile')) { ?>
            <div class="filter-predicts-image">
                <?php
                echo do_shortcode('[adrotate group="' . $adrotate->getOption('filter_bonuses_mobile') . '"]');
                echo do_shortcode('[adrotate group="' . $adrotate->getOption('filter_bonuses_desktop') . '"]');
                ?>
            </div>
        <?php }

	?>
</div>
<div class="wrapper wrapper-bookmaker">
    <div class="main main-page flex-column">
        <?php $the_content = apply_filters( 'the_content', get_the_content() ); ?>
        <?php if($the_content) : ?>
        <div class="bookmaker-items-header__text">
		    <?php echo $the_content; ?>
            <button class="bookmaker-items-header__text-more"><i class="fa-three-dots"></i></button>
        </div>
	    <?php endif; ?>
        <div class="page-items">
        <?php
		Bonuses::template('archive-list/index.php', [
			'bonuses'        => $bonuses_objects,
			'bonuses_count'  => $bonuses_count,
			'bookmakers'     => $bookmakers,
			'limit'          => 30,
            'button_text'    => $button_text,
            'is_filter'		 => $is_filter	
		]);
		?>
        </div>

        <?php
		//echo ssr_content('bonus_after_list');

		
		if ( $after_text):
			?>
            <section class="bookmaker-description">
                <div class="bookmaker-description__content">
                    <div class="h3">Поделиться с друзьями</div>
                    <div class="social-share social-share--no-margin">
						<?php include get_template_directory() . '/templates/share.php'; ?>
                    </div>
                </div>
            </section>
            <section class="bookmaker-description">
                <div class="bookmaker-description__content">
					<?php $author_id = get_the_author_meta( 'ID' ); ?>
					<?php if ( $author_id ): ?>
						<?php
						$author_block_title = get_post_meta( $page_id, 'author_block_title', true );

						if ( ! $author_block_title ) {
                            $author_block_title = 'Материал подготовлен';
						}
						?>
						<?php locate_template( 'templates/author-block.php', true, false, [
							'author_id'   => $author_id,
							'block_title' => $author_block_title,
							'show_expert' => true
						] ); ?>
					<?php endif; ?>
					<?php
					echo $after_text;
					?>
                </div>
            </section>
		<?php endif; ?>
		<?php if ( (! is_user_logged_in() ) && get_comments_number() == 0 ): ?>
            <section class="bookmaker-description no-comment__guest">
                <div class="bookmaker-description__content">
                    <div class="no-comment__guest-title">Ваш комментарий будет первым</div>
                    <div class="no-comment__guest-text"><img src="<?php echo get_template_directory_uri(); ?>/img/bonus-ico.png"/> Поделитесь
                        вашим мнением и получите уникальный бонус!
                    </div>
                    <button class="btn btn-blue no-comment__guest-btn open-auth">Войти</button>
                </div>
            </section>
		<?php else: ?>
            <section class="bookmaker-description">
                <div class="bookmaker-description__content">
                    <div class="h3">Комментарии</div>
					<?php comments_template( '', true ); ?>
                </div>
            </section>
		<?php endif; ?>
    </div>
    <aside class="sidebar">
		<?php get_sidebar(); ?>
    </aside>
</div>


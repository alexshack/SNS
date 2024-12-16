<?php
if($faq) {
    include $faq_schema_template;
}
?>
<div class="wrapper">
	<div class="bookmaker-items-header saving-place">
        <h1><?php echo $archive_title; ?></h1>
	</div>
</div>
<div class="wrapper wrapper-bookmaker">
	<div class="main main-page flex-column">
 
        <div class="page-items">
        	<div class="express_list">
				<?php 
				//echo '<pre>';
				//print_r($expresses);
				//echo '<pre>';
				foreach ( $expresses as $i => $express ){
					include 'loop-1.php';
				} 
				?>
        	</div>
        </div>
       <?php

        if ( ! empty( $post_content ) ) : ?>

            <div class="bookmaker-items-header__text">
		        <?php echo $post_content; ?>
 
            </div>

        <?php endif; ?>        
		<section class="bookmaker-description">
			<div class="bookmaker-description__content">
				<div class="h3">Поделиться с друзьями</div>
				<div class="social-share social-share--no-margin">
					<?php include $share_template; ?>
				</div>
			</div>
		</section>

		<?php if ( $second_content ): ?>
			<section class="bookmaker-description">

				<div class="bookmaker-description__content">
                    <?php

                    locate_template( 'templates/author-block.php', true, false, [
                        'author_id'   => $author_id,
                        'block_title' => $author_block_title,
                        'show_expert' => true
                    ] );

                    echo $second_content;

                    ?>
				</div>
			</section>
		<?php endif; ?>
		<?php if ( (! is_user_logged_in() ) && get_comments_number() == 0 ): ?>
            <section class="bookmaker-description no-comment__guest">
                <div class="bookmaker-description__content">
                    <div class="no-comment__guest-title">Ваш комментарий будет первым</div>
                    <div class="no-comment__guest-text"><img src="<?php echo get_template_directory_uri(); ?>/img/bonus-ico.png" alt="бонус"> Поделитесь
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


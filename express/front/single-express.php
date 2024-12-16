<?php
/**
 * @package WordPress
 */
get_header();

$express_id = get_the_ID();
$express = Express::setup($express_id);
  
?>


<?php breadcrumbs(); ?>
<div class="wrapper">
    <?php include SNS_PATH . '/templates/express/single/header.php'; ?>
</div>
<div class="wrapper wrapper-bookmaker wrapper-express">
	<?php if ( have_posts() ) {
		while ( have_posts() ) : the_post(); ?>
            <div class="main express-page">
	            <?php include SNS_PATH . '/templates/express/single/main.php'; ?>
            </div>
             <aside class="sidebar">
				<?php get_sidebar(); ?>
            </aside>
            <div class="duplicate-block duplicate-block_mobile">
				<?php if ( (! is_user_logged_in() ) && get_comments_number() == 0 ): ?>
                    <section class="bookmaker-description no-comment__guest">
                        <div class="bookmaker-description__content">
                            <div class="no-comment__guest-title">Ваш комментарий будет первым</div>
                            <div class="no-comment__guest-text">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/bonus-ico.png" alt="">
                                Поделитесь вашим мнением и получите уникальный бонус!
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
		<?php endwhile;
	} ?>
</div>
<?php get_footer(); ?>

<script>
	document.addEventListener( 'click', ( e ) => {
		if ( e.target.classList.contains( 'forecast-table__open' ) ) {
			let btn = e.target;
			btn.closest( '.forecast-table__action' ).classList.add( 'open' );
		}
	} );
</script>

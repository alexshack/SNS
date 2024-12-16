<div class="bookmaker-description">
	<?php
	the_content();
	?>
</div>
<div class="duplicate-block duplicate-block_desktop">
	<?php if ( (! is_user_logged_in() ) && get_comments_number() == 0 ): ?>
		<section class="bookmaker-description no-comment__guest">
			<div class="bookmaker-description__content">
				<div class="no-comment__guest-title">Ваш комментарий будет первым</div>
				<div class="no-comment__guest-text"><img src="<?php echo get_template_directory_uri(); ?>/img/bonus-ico.png" alt="">
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

<?php
Bonuses::template('sliders.php', ['bonus_type' => '316', 'title' => 'Бонусы на экспресс', 'type_link' => 'vse-bonusy-bukmekerov']);
?>
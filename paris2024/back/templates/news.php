<?php $i = 1; ?>
<?php foreach ( $news as $news_item ) {
	if ($i == 1) : ?>
		<div class="olympics_news-main">
			<h2 class="olympics_block-title">Главная новость</h2>
			<div class="olympics_news-main-wrapper">
				<?php print $controller->getSingleNewsContentMain( $news_item ); ?>
				<div class="olympics_news-quiz lazy lozad" data-background-image="<?php echo SNS_URL ?>/img/olympics/quiz.jpg">
					<div class="olympics_news-quiz-text">
						<div class="olympics_news-quiz-text-first">Угадай, кто возьмет медали</div>
						<div class="olympics_news-quiz-text-second">Выбери 5 фаворитов и получи приз</div>
					</div>
					<div class="olympics_news-quiz-btn" onclick="Paris2024.Quiz.show(0);return false;" >Проголосовать</div>
				</div>
			</div>
		</div>
	<?php else: ?>
		<?php if ($i == 2) : ?>
			<div class="olympics_news-items">
				<h2 class="olympics_block-title">Последние новости</h2>
		<?php endif; ?>

		<?php print $controller->getSingleNewsContent( $news_item ); ?>

		<?php if ($i == $controller->getNewsLimit() || $i == count( $news ) ) : ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php $i++;
} ?>

<?php
/**
 * Template part for event main block before match SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$content = get_the_content();

if ( empty( $content ) ) {
	$content = 'Прогноз на матч ' . $team_home->post_title . ' — ' . $team_away->post_title . ' ' . wp_date('j F Y', strtotime($event->post->post_date)) . ' в ' . $league->name . ' обоснован анализом статистических данных и трендов. В основе аналитики — оценка текущей формы коллективов и статистика личных встреч.';
}

$content = apply_filters( 'the_content', $content );

?>

<div class="sp_block">
	<div class="sp_block_title">
		<h2>Обзор матча</h2>
	</div>
	<?php echo $content; ?>
</div> 

<?php if ( $event->tv_link ) : ?>
	<div class="sp_block">
		<div class="sp_block_title">
			<h2>Трансляция</h2>
		</div>		
		<div class="sp_event_online">
			<a class="sp_event_online_link" href="<?php echo $event->tv_link; ?>" title="Трансляция" target="blank" rel="nofollow">
				<div class="sp_event_online_link-btn">
					<span>Смотреть</span>
					<svg><use xlink:href="<?php echo get_template_directory_uri() ?>/sportspress/assets/img/play.svg#play"></use></svg>
				</div>
			</a>
			<div class="sp_event_online_title">Как посмотреть матч?</div>
			<p>Легальная трансляция матча в отличном качестве скоро будет доступна по ссылке. Нужно только:</p>
			<ol>
				<li>Пройти по ссылке</li>
				<li>Зарегистрируйтесь на сайте трансляции</li>
				<li>Смотреть трансляции без рекламы</li>
			</ol>
		</div>
	</div>
<?php endif; ?>

<?php if ( !empty( $bets = $event->get_bets() ) ) : ?>
	<div class="sp_block">
		<div class="sp_block_title">
				<h2>Коэффициенты на матч</h2>
		</div>
		<?php sp_get_template( 'event-bets-all.php', [ 'bets' => $bets ], SP()->template_path() . 'event/',  );	?>

	</div>

<?php endif; ?>
<?php
/**
 * Template part for event main block before match SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$events_home_args = array(
	'title'         => false,
	'team'          => $team_home->ID,
	'season'        => $season->term_id,
	'orderby'       => 'date',
	'order'         => 'DESC',
	'number'        => 5,
	'show_league'   => true,
	'show_matchday' => true,
	'status'        => 'publish',
);

$events_away_args = array(
	'title'         => false,
	'team'          => $team_away->ID,
	'season'        => $season->term_id,
	'orderby'       => 'date',
	'order'         => 'DESC',
	'number'        => 5,
	'show_league'   => true,
	'show_matchday' => true,
	'status'        => 'publish',
);

$stat_args = array(
	'title'        => false,
	'event'        => $event,
	'season'       => $season->term_id,
	'orderby'      => 'date',
	'order'        => 'DESC',
	'number'       => 10,
	'status'       => 'publish',
	'single_event' => false
);

$table_args = [
	'post_type'      => 'sp_table',
	'posts_per_page' => -1,
	'status'         => 'publish',
	'tax_query'      => [
		'relation' => 'AND',
		[
			'taxonomy' => 'sp_season',
  			'field'    => 'term_id',
  			'terms'    => $season->term_id,
		],
		[
			'taxonomy' => 'sp_league',
  			'field'    => 'term_id',
  			'terms'    => $league->term_id,
		],				
	],
	'meta_query'   => [
		'relation' => 'AND',
		[
			'key'      => 'sp_teams',
			'value'    => $team_home->ID,
			'compare'  => 'LIKE'
		],
		[
			'key'      => 'sp_teams',
			'value'    => $team_away->ID,
			'compare'  => 'LIKE'
		],		
	]  	  
];

$tables_query = new WP_Query;

$tables = $tables_query->query($table_args);

?>

<?php if ($predict) : ?>
<div class="sp_event_section">
	<h1 class="sp_event_section_header">
		<?php echo $team_home->post_title . ' - ' . $team_away->post_title . ' ' . wp_date('j F Y, h:i', strtotime($event->post->post_date)) . ' смотреть онлайн'; ?>
	</h1>
	<div class="sp_event_section_content">
		<div class="sp_event_online">
			<a class="sp_event_online_link" href="<?php echo $predict->getBetLink($predict->bookmaker->ID); ?>">
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
</div>
<?php else : ?>
	<h1 class="sp_event_section_header">
		<?php echo $team_home->post_title . ' - ' . $team_away->post_title . ' ' . wp_date('j F Y, h:i', strtotime($event->post->post_date)) . ' смотреть онлайн'; ?>
	</h1>
<?php endif; ?>




<?php if ($tables) : ?> 
<div class="sp_event_section">
	<h2 class="sp_event_section_header">Турнирная таблица</h2>
<?php
	foreach ($tables as $table) { 
		$table_args = array(
			'id'         => $table->ID,
			'show_title' => false,
			'tab'        => $tab,
			'highlight'  => $team_home->ID,
			'highlight2' => $team_away->ID
		);	
		sp_get_template( 'league-table.php', $table_args, SP()->template_path() . 'league/'  ); 
		//sp_get_template( 'league-matrix.php', $matrix_args, SP()->template_path() . 'league/' );
	} ?>

</div>
<?php endif; ?>

<div class="sp_event_section">
	<h2 class="sp_event_section_header">Ближайшие прогнозы</h2>
	<div class="sp_event_section_content">
		<?php echo do_shortcode('[predicts limit="10" show_cats=true]'); ?>
	</div>
</div>
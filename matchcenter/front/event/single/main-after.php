<?php
/**
 * Template part for event main block after match SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */



$stat_args = array(
	'title'        => false,
	'event'        => $event,
	'single_event' => true
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

<h1 class="sp_event_section_header">
	<?php echo 'Результаты матча ' . $team_home->post_title . ' - ' . $team_away->post_title . ' ' . wp_date('j F Y, h:i', strtotime($event->post->post_date)); ?>
</h1>

<div class="sp_event_section">
	<h2 class="sp_event_section_header">Таймлайн</h2>
	<div class="sp_event_section_content">
		<?php sp_get_template( 'event-timeline.php', $timeline_args, '', SP_TIMELINES_DIR . 'templates/' );  ?>
		<?php sp_get_template( 'event-timeline-vertical.php', $timeline_args, '', SP_TIMELINES_DIR . 'templates/' ); ?>
	</div>
</div>

<div class="sp_event_section">
	<h2 class="sp_event_section_header">Статистика матча</h2>
	<div class="sp_event_section_content">
		<?php sp_get_template( 'event-statistics.php', $stat_args, SP()->template_path() . 'event/'  ); ?>

	</div>
</div>

<div class="sp_event_section">
	<h2 class="sp_event_section_header">Составы команд</h2>
	<div class="sp_event_section_content">
		<?php echo do_shortcode('[event_performance id="' . $event->ID . '" align="none"]'); ?>
	</div>
</div>

<?php if ($tables) : ?> 
<div class="sp_event_section">
	<h2 class="sp_event_section_header">Турнирная таблица</h2>
	<div class="sp_event_section_content">
		<p>В этом блоке сравниваются результаты 10 последних матчей команд. Раздел содержит в себе, статистику забитых и пропущенных, побед и поражений и тд.<p>
	</div>
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
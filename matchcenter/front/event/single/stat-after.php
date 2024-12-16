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

<div class="sp_block">
	<div class="sp_block_title">
		<h2>Статистика матча</h2>
	</div>
	<?php sp_get_template( 'event-statistics.php', $stat_args, SP()->template_path() . 'event/'  ); ?>
</div>

<?php if ( $event->fixtures && 1 == 0) : ?>
<div class="sp_block">
	<div class="sp_block_title">
		<h2>Таймлайн</h2>
	</div>
	<?php sp_get_template( 'event-timeline-vertical.php', $timeline_args, '', SP_TIMELINES_DIR . 'templates/' ); ?>
</div>
<?php endif; ?>


<?php if ($tables) : ?> 

<?php
	foreach ($tables as $table) { 
		$table_args = array(
			'id'         => $table->ID,
			'show_title' => true,
			'tab'        => $tab,
			'highlight'  => $team_home->ID,
			'highlight2' => $team_away->ID
		);	
		sp_get_template( 'league-table.php', $table_args, SP()->template_path() . 'league/'  ); 
		//sp_get_template( 'league-matrix.php', $matrix_args, SP()->template_path() . 'league/' );
	} ?>

<?php endif; ?>
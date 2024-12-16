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
	$content = 'Матч ' . $team_home->post_title . ' — ' . $team_away->post_title . ' завершен ' . wp_date('j F Y', strtotime($event->post->post_date)) . '. Выберите другую встречу для обзора.';
}

$content = apply_filters( 'the_content', $content );

$games_next_args = [
	'date'     => 'range',
	'date_from' => date('Y-m-d'),
	'date_to'   => date('Y-m-d', strtotime('+30 days')),	
	'number'    => 6,
	'orderby'   => 'post_date',
	'order'     => 'ASC',
	'status'    => 'future',
	'season'    => $season->term_id
];

?>

<div class="sp_block">
	<div class="sp_block_title">
		<h2>Матч завершен</h2>
	</div>
	<?php echo $content; ?>
	<div class="sp_inner_block">
		<?php sp_get_template( 'event-rows.php', $games_next_args, SP()->template_path() . 'event/',  ); ?>
	</div>
</div> 

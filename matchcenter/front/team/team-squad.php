<?php
/**
 * Team Blocks
 *
 * @author      Alex Torbeev
 * @package     SportsPress SNS/Templates
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$defaults = array(
	'team'   => null,
	'league' => null,
   'season' => null,
   'show_goals' => true,
);

extract( $defaults, EXTR_SKIP );

$post_args = [
	'post_type'      => 'sp_player',
	'status'         => 'publish',
	'orderby'        => 'post_title',
	'order'          => 'ASC',
];

if ( $team ) {
	$post_args['meta_query'][] = [
		[
			'key'      => 'sp_current_team',
  			'value'    => $team,
		]
	];
}

if ($season) {
	$post_args['tax_query'][] = [
		[
			'taxonomy' => 'sp_season',
  			'field'    => 'term_id',
  			'terms'    => $season,
		]
	];
}

$posts_query = new WP_Query;

$players = $posts_query->query($post_args);

if ( $players ) : ?>

	<div class="sp_players">

	      <?php foreach ( $players as $player ) : ?>
	      	<?php 	
	      	$image = get_the_post_thumbnail_url( $player->ID, 'w70h70' );
       		$positions = get_the_terms( $player->ID, 'sp_position' );
        		$position = $positions ? $positions[0]->name : '';

	      	?>
	      	<div class="sp_player_row">
					<img class="sp_player_row_img lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $image; ?>" width="38" height="38" alt="<?php echo wp_kses_post( $player->post_title ); ?>" title="<?php echo wp_kses_post( $player->post_title ); ?>">
					<div class="sp_player_row_name"><?php echo wp_kses_post( $player->post_title ); ?></div>
					<div class="sp_player_row_position"><?php echo wp_kses_post( $position ); ?></div>
	      	</div>


	      <?php endforeach; ?>
	</div>
<?php endif; ?>
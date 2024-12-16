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
   'league' => null,
   'season' => null,
   'limit'  => -1,
   'title'  => 'Команды'
);

extract( $defaults, EXTR_SKIP );

$post_args = [
	'post_type'      => 'sp_team',
	'posts_per_page' => $limit,
	'status'         => 'publish',
	'orderby'        => 'post_title',
	'order'          => 'ASC',
	'tax_query'      => [
		'relation' => 'AND',
	]  
];

if ($league) {
	$post_args['tax_query'][] = [
		[
			'taxonomy' => 'sp_league',
  			'field'    => 'term_id',
  			'terms'    => $league,
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

$teams = $posts_query->query($post_args);

if ( $teams ) : ?>

	<div class="sp_block sp_block_accord">
		<h2 class="sp_block_title sp_block_accord_title"><?php echo wp_kses_post( $title ); ?></h2>
	   <div class="sp_team_blocks sp_block_accord_content">
	      <?php foreach ( $teams as $team ) {
	         echo '<a href="' . get_the_permalink($team) . '" class="sp_team_block" title="' . $team->post_title . '">';
	         echo '<img class="lazy lozad sp_team_block_img" src="' . Thumbnail::$lazy_preview . '" data-src="' . get_the_post_thumbnail_url($team, 'w78h78') . '" width="40" height="40">';
	         echo '<div class="sp_team_block_name">' . $team->post_title . '</div>';
	         echo '</a>';
	      } ?>
	   </div>
	</div>
<?php endif; ?>
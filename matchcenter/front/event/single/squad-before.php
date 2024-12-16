<?php
/**
 * Template part for event main block after match SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

$home_args = [
	'team' => $team_home->ID,
	'season' => $season->term_id,
	'league' => $league->term_id
];

$away_args = [
	'team' => $team_away->ID,
	'season' => $season->term_id,
	'league' => $league->term_id
];

?>

<div class="sp_block">
	<div class="sp_block_title">
		<h2>Составы команд</h2>
	</div>
	<div class="sp_squads">
		<div class="sp_inner_block">
			<div class="sp_squads_team home">
	            <a href="<?php echo get_permalink($team_home) ?>">
	                <img class="lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo get_the_post_thumbnail_url($team_home, 'w70h70'); ?>" alt="<?php echo $team_home->post_title; ?>" width="150" height="150">
	            </a>            
	            <a href="<?php echo get_permalink($team_home) ?>" ><?php echo $team_home->post_title; ?></a>
	         </div>			
			<?php sp_get_template( 'team-squad.php', $home_args, SP()->template_path() . 'team/'  ); ?>
		</div>
		<div class="sp_inner_block">
			<div class="sp_squads_team away">
	            <a href="<?php echo get_permalink($team_away) ?>" >
	                <img class="lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo get_the_post_thumbnail_url($team_away, 'w70h70'); ?>" alt="<?php echo $team_away->post_title; ?>" width="150" height="150">
	            </a>            
	            <a href="<?php echo get_permalink($team_away) ?>" class="sp_squads_team_name"><?php echo $team_away->post_title; ?></a>
	         </div>				
			<?php sp_get_template( 'team-squad.php', $away_args, SP()->template_path() . 'team/'  ); ?>
		</div>		
	</div>


</div>
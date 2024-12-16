<?php
/**
 * Template part for League playoff block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$defaults = array(
	'stage'  => false,
	'league' => false,
	'season' => false,
	'sport'  => false
);

extract( $defaults, EXTR_SKIP );

if ( $stage && $league && $season && $sport ) {

	$event_args = [
        'post_type'   => 'sp_event',
        'numberposts' => -1,
        'status'      => 'any',
        'order'       => 'ASC',
        'tax_query'   => [
            'relation' => 'AND',
            [
                'taxonomy' => 'sp_league',
                'field'    => 'id',
                'terms'    => [ $league ],
                'include_children' => false                   
            ],
            [
                'taxonomy' => 'sp_season',
                'field'    => 'id',
                'terms'    => [ $season ],
                'include_children' => false                    
            ],
            [
                'taxonomy' => 'sp_stage',
                'field'    => 'id',
                'terms'    => [ $stage ],
                'include_children' => false                    
            ],                               
        ]
	];

	$events = get_posts( $event_args );

	$pairs = [];
	
	foreach ( $events as $event ) {
		$teams = (array) get_post_meta( $event->ID, 'sp_team', false );
		$pairs_args = [
	        'post_type'   => 'sp_event',
	        'numberposts' => -1,
	        'status'      => 'any',
	        'order'       => 'ASC',
	        'tax_query'   => [
	            'relation' => 'AND',
	            [
	                'taxonomy' => 'sp_league',
	                'field'    => 'id',
	                'terms'    => [ $league ],
	                'include_children' => false                   
	            ],
	            [
	                'taxonomy' => 'sp_season',
	                'field'    => 'id',
	                'terms'    => [ $season ],
	                'include_children' => false                    
	            ],
	            [
	                'taxonomy' => 'sp_stage',
	                'field'    => 'id',
	                'terms'    => [ $stage ],
	                'include_children' => false                    
	            ],                               
	        ],
	        'meta_query' => [
	        	'relation' => 'AND',
	        	[
	        		'key' => 'sp_team',
	        		'value' => $teams[0],
	        	],
	        	[
	        		'key' => 'sp_team',
	        		'value' => $teams[1],
	        	]	        	
	        ]
		];

		$pairs[] = get_posts( $pairs_args );

	}

	$pairs = array_unique($pairs, SORT_REGULAR);

/*	echo '<pre>';
	print_r($pairs);
	echo '</pre>';*/

    if ( count($events) == count( $pairs ) ) {

	    $events_args = array(
			'show_date'     => true,
			'show_time'     => true,
			'show_league'   => false,
			'show_matchday' => false,
			'hide_if_empty' => true,
			'data'      => $events 			
		);
	    echo '<div class="sp_event_rows_wrapper">';
	    sp_get_template( 'event-rows.php', $events_args, SP()->template_path() . 'event/'  );
	    echo '</div>';

	} else { ?>
		<div class="sp_playoffs">
		<?php foreach ( $pairs as $games ) : ?>
			<div class="sp_playoff_wrapper sp_block_accord">
				<?php
				if ( $sport == 'football' ) {
					$scores = [];
				} else {
					$scores = [ [0, 0] ] ;
				}

				foreach ( $games as $key => $game ) {
					$game = new SP_SNS_Event( $game->ID );
					if ( $key == 0 ) {
						$p_game = $game;
					}
					if ( $sport == 'football' ) {
						if ( $game->team_home->ID == $p_game->team_home->ID ) {
							$scores[$key][0] = $game->team_home->score;
							$scores[$key][1] = $game->team_away->score;
						} else {
							$scores[$key][0] = $game->team_away->score;
							$scores[$key][1] = $game->team_home->score;
						}
					} else {
						if ( $game->team_home->ID == $p_game->team_home->ID ) {
							if ( $game->team_home->score > $game->team_away->score ) {
								$scores[0][0] = $scores[0][0] + 1;
							} else {
								$scores[0][1] = $scores[0][1] + 1;
							}
						} else {
							if ( $game->team_home->score > $game->team_away->score ) {
								$scores[0][1] = $scores[0][1] + 1;
							} else {
								$scores[0][0] = $scores[0][0] + 1;
							}
						}
					}

				}

				$home_start = '<span>';
				$home_end = '</span>';
				$away_start = '<span>';
				$away_end = '</span>';

				if ( $sport == 'football' ) {
					$scores[2][0] = (int)$scores[0][0] + (int)$scores[1][0];
					$scores[2][1] = (int)$scores[0][1] + (int)$scores[1][1];
					if ( $scores[2][0] > $scores[2][1] ) {
						$home_start = '<strong>';
						$home_end = '</strong>';
					}
					if ( $scores[2][0] < $scores[2][1] ) {
						$away_start = '<strong>';
						$away_end = '</strong>';
					}									
					if ( $scores[2][0] == $scores[2][1] ) {
						$results = get_post_meta( $games[1]->ID, 'sp_results', 1 );
						if ( isset( $results[ $p_game->team_home->ID ]['overtime'] ) ) {
							if ( $results[ $p_game->team_home->ID ]['overtime'] > $results[ $p_game->team_away->ID ]['overtime'] ) {
								$home_start = '<strong>';
								$home_end = '</strong>';
							}
							if ( $results[ $p_game->team_home->ID ]['overtime'] < $results[ $p_game->team_away->ID ]['overtime'] ) {
								$away_start = '<strong>';
								$away_end = '</strong>';
							}							
						}
						if ( isset( $results[ $p_game->team_home->ID ]['penalties'] ) ) {
							if ( $results[ $p_game->team_home->ID ]['penalties'] > $results[ $p_game->team_away->ID ]['penalties'] ) {
								$home_start = '<strong>';
								$home_end = '</strong>';
							}
							if ( $results[ $p_game->team_home->ID ]['penalties'] < $results[ $p_game->team_away->ID ]['penalties'] ) {
								$away_start = '<strong>';
								$away_end = '</strong>';
							}							
						}						
					}
					$scores[0][0] = '<span>' . $scores[0][0] . '</span>';
					$scores[0][1] = '<span>' . $scores[0][1] . '</span>';
					$scores[1][0] = '<span>' . $scores[1][0] . '</span>';
					$scores[1][1] = '<span>' . $scores[1][1] . '</span>';
					$scores[2][0] = $home_start . $scores[2][0] . $home_end;
					$scores[2][1] = $away_start . $scores[2][1] . $away_end;

				} else {
					if ( $scores[0][0] > $scores[0][1] ) {
						
						$home_start = '<strong>';
						$home_end = '</strong>';
					}
					if ( $scores[0][1] > $scores[0][0] ) {
						
						$away_start = '<strong>';
						$away_end = '</strong>';						
					}
					$scores[0][0] = $home_start . $scores[0][0] . $home_end;
					$scores[0][1] = $away_start . $scores[0][1] . $away_end;				
				}

				?>
				<div class="sp_playoff">
					<div class="sp_playoff_teams">
						<div class="sp_playoff_team">
							<img class="lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $p_game->team_home->logo; ?>" alt="<?php $p_game->team_home->post->post_title; ?>" width="16" height="16">
							<?php echo $home_start . $p_game->team_home->post->post_title . $home_end; ?>
						</div>
						<div class="sp_playoff_team">
							<img class="lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $p_game->team_away->logo; ?>" alt="<?php $p_game->team_away->post->post_title; ?>" width="16" height="16">
							<?php echo $away_start . $p_game->team_away->post->post_title . $away_end; ?>
						</div>
					</div>
					<div class="sp_playoff_scores">
						<?php foreach ( $scores as $score ) : ?>
							<div class="sp_playoff_score">
								<?php echo $score[0]; ?>
								<?php echo $score[1]; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="sp_block_accord_title" aria-label="Смотреть все матчи" title="История игр"></div>
				</div>
				<div class="sp_event_rows_wrapper sp_block_accord_content ">
					<?php
					    $events_args = array(
							'show_date'     => true,
							'show_time'     => true,
							'show_league'   => false,
							'show_matchday' => false,
							'hide_if_empty' => true,
							'data'          => $games 			
						);

					    sp_get_template( 'event-rows.php', $events_args, SP()->template_path() . 'event/'  );
					?>

				</div>
			</div>
		<?php endforeach; ?>
		</div>

	<?php
	}

}
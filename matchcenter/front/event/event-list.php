<?php
/**
 * Event List
 *
 * @author      ThemeBoy
 * @package     SportsPress/Templates
 * @version   2.7.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$defaults = array(
	'type'                 => false,
	'title'                => false,
	'status'               => 'any',
	'format'               => 'row',
	'date'                 => 'default',
	'date_from'            => 'default',
	'date_to'              => 'default',
	'day'                  => 'default',
	'league'               => null,
	'season'               => null,
	'team'                 => null,
	'number'               => -1,
	'orderby'              => 'default',
	'order'                => 'default',
	'show_all_events_link' => false,
	'show_league'          => get_option( 'sportspress_event_blocks_show_league', 'no' ) == 'yes' ? true : false,
	'show_season'          => get_option( 'sportspress_event_blocks_show_season', 'no' ) == 'yes' ? true : false,
	'show_matchday'        => get_option( 'sportspress_event_blocks_show_matchday', 'no' ) == 'yes' ? true : false,
	'hide_if_empty'        => false,
	'bonus'                => false
);

extract( $defaults, EXTR_SKIP );



if ( $hide_if_empty && empty( $data ) ) {
	return false;
}

if ( $show_title && false === $title && $id ) :
	$caption = $calendar->caption;
	if ( $caption ) {
		$title = $caption;
	} else {
		$title = get_the_title( $id );
	}
endif;

if ($data) {

	if ($bonus) {
		$home_bonuses = get_option('home_bonuses');

		$bonuses = Bonuses::setup((new BonusesFilter())->where([
		    'ID__in' => $home_bonuses
		])->limit(1)->order('popular')->getResults());
		$bonus = $bonuses[0];
	}

	echo '<div class="sp_block">';

	if ( $title ) {
		echo '<div class="sp_block_title">';
		echo '<h2 class="sp-table-caption">' . wp_kses_post( $title ) . '</h2>';
		if ($league != null) {
			echo '<a href="' . get_term_link($league) . 'calendar/">Все результаты</a>';
		}
		echo '</div>';
	}
	?>
	<div class="sp_event_blocks">
			<?php
			$i = 0;

			if ( intval( $number ) > 0 ) {
				$limit = $number;
			}

			foreach ( $data as $event ) :
				if ( isset( $limit ) && $i >= $limit ) {
					continue;
				}

				$event_object = new SP_SNS_Event($event);
				$event_status = get_post_meta( $event->ID, 'sp_status', true );


				if ( 'day' === $calendar->orderby ) :
					$event_group = get_post_meta( $event->ID, 'sp_day', true );
					if ( ! isset( $group ) || $event_group !== $group ) :
						$group = $event_group;
						echo '<div class="sp_event_group_name">', esc_attr__( 'Match Day', 'sportspress' ), ' ', wp_kses_post( $group ), '</div>';
					endif;
				endif;
				?>
				<a class="sp_event_block" href="<?php echo $event_object->permalink ?>">
					<div class="sp_event_block_header">
						<?php
							if ( $show_league && $league == null ) :
								$leagues = get_the_terms( $event, 'sp_league' );
								if ( $leagues ) :
									$game_league   = array_shift( $leagues );
									$league_img_id = get_term_meta($game_league->term_id, '_thumbnail_id', 1 );
									$league_img    = wp_get_attachment_image_url( $league_img_id, 'sportspress-fit-mini' );
									$league_link   = get_term_link($game_league);
								?>
								<div class="sp_event_block_league">
									<img class="sp_event_block_league_img lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $league_img; ?>" width="32" height="32" title="<?php echo wp_kses_post( $game_league->name ); ?>">
									<div class="sp_event_block_league_name"><?php echo wp_kses_post( $game_league->name ); ?></div>
								</div>
							<?php endif;
							endif;
						?>
						<?php
						if ( $show_matchday && $league == null ) :
							$matchday = get_post_meta( $event->ID, 'sp_day', true ); 
							if ( $matchday != '' ) :
								?>
							<div class="sp_event_block_matchday"><?php echo wp_kses_post( $matchday ); ?></div>
							<?php endif;
						endif;
						?>	
					</div>
					<div class="sp_event_block_content">
						<div class="sp_event_block_teams">
							<div class="sp_event_block_team">
								<div class="sp_event_block_team_name">
									<img class="lozad team_logo" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $event_object->team_home->logo; ?>" width="150" height="150">
									<div class="team_name"><?php echo $event_object->team_home->post->post_title; ?></div>
								</div>
								<div class="sp_event_block_team_score">
									<?php echo $event_object->team_home->score; ?>
								</div>
							</div>
							<div class="sp_event_block_team">
								<div class="sp_event_block_team_name">
									<img class="lozad team_logo" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $event_object->team_away->logo; ?>" width="150" height="150">
									<div class="team_name"><?php echo $event_object->team_away->post->post_title; ?></div>
								</div>
								<div class="sp_event_block_team_score">
									<?php echo $event_object->team_away->score; ?>
								</div>									
							</div>							
						</div>
						<div class="sp_event_block_results">
							<?php
							if ( $show_matchday && $league != null ) :
								$matchday = get_post_meta( $event->ID, 'sp_day', true ); 
								if ( $matchday != '' ) :
									?>
								<div class="sp_event_block_matchday"><?php echo wp_kses_post( $matchday ); ?></div>
								<?php endif;
							endif;
							?>	
							<time class="sp_event_block_date" datetime="<?php echo esc_attr( $event->post_date ); ?>" >
								<?php echo wp_date('d.m.Y', strtotime($event->post_date)); ?>
							</time>
							<div class="sp_event_block_time">
								<?php echo wp_date('H:i', strtotime($event->post_date)); ?>
							</div>
						</div>
					</div>
					<?php do_action( 'sportspress_event_blocks_after', $event, $usecolumns ); ?>

				</a>
				<?php if ($bonus) { ?>
					<div class="sp_event_block_bonus">
						<?php echo $bonus->bookmaker->thumbnail->getLazyLoadImg('sp_event_block_bonus_bk', ['alt' => do_shortcode($bonus->post_title)], '105x50'); ?>
						<span class="sp_event_block_bonus_name">
							<?php echo do_shortcode($bonus->post_title); ?>
						</span>
						<a class="sp_event_block_bonus_btn" target="_blank" rel="nofollow" href="<?php echo $bonus->bookmaker->getPartnerLink() ?>" aria-label="Забрать бонус"></a>					
					</div>
				<?php } ?>				
				<?php
				$i++;
			endforeach;
			?>
	</div>
	<?php
	if ( $id && $show_all_events_link ) {
		echo '<div class="sp-calendar-link sp-view-all-link"><a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_attr__( 'View all events', 'sportspress' ) . '</a></div>';
	}
	?>
</div>
<?php } ?>

<?php
/**
 * Template part for event header block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
    $event_tabs = [
        'main'    => 'Обзор',
        'predict' => 'Прогноз',
    ];

    $show_votes = false;
    $votes = $event->getVotes();
    $show_votes = ( ( ! $event->finished ) || ( $event->finished && $votes[0] > 0 ) );


    $user_vote        = ( isset( $_COOKIE['sp_user_vote'] ) ) ? explode( ',', $_COOKIE['sp_user_vote'] ) : [];
    $user_logged_vote = '';
    $can_vote = ( ! $event->finished );

    if ( function_exists( 'is_user_activated' ) ) {
        $user_activated = is_user_activated();
    } else {
        $user_activated = true;
    }

    if ( is_user_logged_in() && $user_activated ) {
        $user_logged_vote = get_user_meta( get_current_user_id(), 'sp_user_vote', true );
        $user_logged_vote = explode( ',', $user_logged_vote );
        $can_vote = ( $can_vote && ! in_array( $event->ID, $user_logged_vote ) && ! in_array( $event->ID, $user_vote ) );
    } else {
        $can_vote = ( $can_vote && ! in_array( $event->ID, $user_vote ) );
    }

/*    echo '<pre>';
    print_r($event);
    echo '</pre>';*/

?>

<div class="sp_event_header sp_event_section">
    <div class="sp_event_header__info">
        <?php echo $league->name . '. ' . $event->getStage();?>
    </div>
    <div class="sp_event_header__results">
        <div class="sp_event_header__team team_home">
            <a href="<?php echo get_permalink($team_home) ?>" title="<?php echo $team_home->post_title; ?>" class="team_link">
                <img class="team_logo lazy lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo get_the_post_thumbnail_url($team_home, 'w90h90'); ?>" width="150" height="150">
                <div class="team_title"><?php echo $team_home->post_title; ?></div>
            </a>
        </div>
        <div class="sp_event_header__center">
            <div class="sp_event_header__time">
                <span class="event_date"><?php echo wp_date('j M Y, H:i', strtotime($event->post->post_date)); ?></span>
                <div class="sp_event_header__date">
                    <?php if ( $event->finished ) : ?>
                        <div class="sp_event_header__date-col"><?php echo $results[0]; ?></div>
                        <div class="sp_event_header__date-col">:</div>
                        <div class="sp_event_header__date-col"><?php echo $results[1]; ?></div>
                    <?php else : ?>
                        <?php $clock = SP_SNS_Theme::downcounter($event->post->post_date); ?>
                        <div class="sp_event_header__date-col">
                            <?php echo $clock['days']['digit'] ?>
                            <span><?php echo $clock['days']['word'] ?></span>
                        </div>
                        <div class="sp_event_header__date-col">:</div>
                        <div class="sp_event_header__date-col">
                            <?php echo $clock['hours']['digit'] ?>
                            <span><?php echo $clock['hours']['word'] ?></span>
                        </div>
                        <div class="sp_event_header__date-col">:</div>
                        <div class="sp_event_header__date-col">
                            <?php echo $clock['mins']['digit'] ?>
                            <span><?php echo $clock['mins']['word'] ?></span>
                        </div>                                                
                    <?php endif; ?>
                </div>
                <?php if ( $finished ) : ?>
                    <div class="sp_event_header__score">
                        <?php echo $event->getFullScore(); ?>
                    </div>
                <?php else: ?>
                    <?php
                        if ( $bets = $event->get_best_bets() ) {
                            sp_get_template( 'event-bets.php', ['bets' => $bets, 'class' => 'sp_event_top_item_bets'], SP()->template_path() . 'event/',  );
                        }
                    ?>
                <?php endif; ?>
            </div>
            <div class="sp_event_header__place">
                <?php if (isset( $venue ) ) echo $venue->name; ?>
            </div>
            <?php if ( $show_votes ) : ?>
                <div class="sp_event_header__vote" id="sp_event_header__vote">
                    <div class="vote_title">Проголосуй кто победит в основное время?</div>
                    <div class="vote_buttons <?php echo $can_vote ? 'active' : ''; ?>">
                        <button class="vote_btn vote_btn_1" onclick="SPSNS.voteEvent(<?php echo $event->ID ?>, 1);">П1</button>
                        <button class="vote_btn vote_btn_2" onclick="SPSNS.voteEvent(<?php echo $event->ID ?>, 2);">X</button>
                        <button class="vote_btn vote_btn_3" onclick="SPSNS.voteEvent(<?php echo $event->ID ?>, 3);">П2</button>
                    </div>
                    <div class="vote_results <?php echo $can_vote ? '' : 'active'; ?>">
                        <div class="vote_digits">
                            <div class="vote_digit">П1 <strong class="vote_digit_1"><?php echo $votes[1]; ?></strong></div>
                            <div class="vote_digit">X <strong class="vote_digit_2"><?php echo $votes[2]; ?></strong></div>
                            <div class="vote_digit">П2 <strong class="vote_digit_3"><?php echo $votes[3]; ?></strong></div>
                        </div>
                        <div class="vote_lines">
                            <div class="vote_line vote_line_1" style="width:<?php echo $votes[1]; ?>"></div>
                            <div class="vote_line vote_line_2" style="width:<?php echo $votes[2]; ?>"></div>
                            <div class="vote_line vote_line_3" style="width:<?php echo $votes[3]; ?>"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="sp_event_header__team team_away">
            <a href="<?php echo get_permalink($team_away) ?>" title="<?php echo $team_away->post_title; ?>" class="team_link">
                <img class="team_logo lozad" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo get_the_post_thumbnail_url($team_away, 'w90h90'); ?>" width="150" height="150">
                <div class="team_title"><?php echo $team_away->post_title; ?></div>
            </a>            
        </div>        
    </div>
    <?php if ( $event->fixtures ) : ?>
        <div class="sp_inner_block sp_mob_hidden sp_event_header_timline">
            <?php sp_get_template( 'event-timeline.php', $timeline_args, '', SP_TIMELINES_DIR . 'templates/' );  ?>
        </div>
    <?php endif; ?>    
</div>
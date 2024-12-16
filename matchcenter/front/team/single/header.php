<?php
/**
 * Template part for team header block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
    $image_url = get_the_post_thumbnail_url($team_id, 'sportspress-fit-medium');

    $team_tabs = [
        'main'       => 'Обзор',
        'table'      => 'Таблицы',
        'calendar'   => 'Календарь',
        'squad'      => 'Состав',
        'transfers'  => 'Трансферы',
        //'trophey'    => 'Достижения',
    ];

    if ( $sport->type != 'football' || ! count( $squads ) ) {
        unset( $team_tabs['squad'] );
    }

    if ( ! $transfer_link ) {
        unset( $team_tabs['transfers'] );
    }

    $all_seasons = SP_SNS_Theme::getMainSeasons();

    $header_title = $team->post->post_title . ' ' . $season->name;
    if ($tab != 'main') {
        $header_title = $team_tabs[$tab] . ' ' . $header_title;
    }

    $game_header_args = [
        'date_from' => date('Y-m-d'),
        'date_to'   => date('Y-m-d', strtotime('+30 days')),
        'range'     => 'w',
        'number'    => 1,
        'orderby'   => 'post_date',
        'order'     => 'ASC',
        'status'    => 'future',
        'title'     => false,
        'season'    => $main_season->ID,
        'team'      => $team_id,
        'bonus'     => true
    ];

?>

<div class="sp_header sp_block">
    
 
        <div class="sp_header_logo">
            <img class="lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $image_url; ?>" alt="<?php $team->post->post_title; ?>" width="40" height="40">
        </div>
        <div class="sp_header_info">
            <div class="sp_header_top">
                <h1 class="sp_header_title"><?php echo $header_title ; ?></h1>
                <div class="sp_header_tabs  sp_header_tabs_season">
                    <?php foreach ($all_seasons as $all_season) {
                        if ($all_season->ID == $season_id) {
                            echo '<div class="sp_header_tab active">' . $all_season->name . '</div>'; 
                        } else {
                            if ( $all_season->ID == $main_season->ID ) {
                                $tab_link = $team_permalink; 
                            } else {
                                $tab_link = $team_permalink . 'season-' . $all_season->term->slug  . '/'; 
                            }
                            echo '<a class="sp_header_tab" href="' . $tab_link . '">' . $all_season->name . '</a>';
                        }                        
                    } ?>
                </div>
            </div>
            <div class="sp_header_tabs sp_header_tabs_desktop">
                <?php foreach ($team_tabs as $key => $team_tab) {
                    if ($tab === $key) {
                        echo '<div class="sp_header_tab active">' . $team_tab . '</div>'; 
                    } else {
                        if ($key == 'main') {
                            if ($main_season->ID == $season_id) {
                                $tab_link = $team_permalink;
                            } else {
                                $tab_link = $team_permalink . 'season-' . $season_slug . '/';
                            }

                        } else {
                            if ($main_season->ID == $season_id) {
                                $tab_link = $team_permalink . $key  . '/';
                            } else {
                                $tab_link = $team_permalink . 'season-' . $season_slug . '/' . $key . '/';
                            }
                        }
                        echo '<a class="sp_header_tab" href="' . $tab_link . '">' . $team_tab . '</a>';
                    }
                 }; ?>
            </div>
        </div>
        <div class="sp_header_tabs sp_header_tabs_mobile">
            <?php foreach ($team_tabs as $key => $team_tab) {
                if ($tab === $key) {
                    echo '<div class="sp_header_tab active">' . $team_tab . '</div>'; 
                } else {
                    if ($key == 'main') {
                        if ($main_season->ID == $season_id) {
                            $tab_link = $team_permalink;
                        } else {
                            $tab_link = $team_permalink . 'season-' . $season_slug . '/';
                        }

                    } else {
                        if ($main_season->ID == $season_id) {
                            $tab_link = $team_permalink . $key  . '/';
                        } else {
                            $tab_link = $team_permalink . 'season-' . $season_slug . '/' . $key . '/';
                        }
                    }
                    echo '<a class="sp_header_tab" href="' . $tab_link . '">' . $team_tab . '</a>';
                }
             }; ?>
        </div>        
        <?php sp_get_template( 'event-blocks.php', $game_header_args, SP()->template_path() . 'event/',  ); ?>

</div>
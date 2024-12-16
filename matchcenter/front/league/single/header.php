<?php
/**
 * Template part for league header block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */


$league_tabs = [
    'main'       => 'Обзор',
    'table'      => 'Таблица',
    'calendar'   => 'Календарь',
    'transfers'  => 'Трансферы',
    //'statistics' => 'Статистика',
];
if ( ! $league->has_transfers ) {
    unset($league_tabs['transfers']);
}
$all_seasons = $league->getSeasons();

$header_title = $league->name . ' ' . $season->name;
if ($tab != 'main') {
    $header_title = $league_tabs[$tab] . ' ' . $header_title;
}

$game_header_args = [
    'date_from' => date('Y-m-d'),
    'date_to'   => date('Y-m-d', strtotime('+7 days')),
    'range'     => 'w',
    'number'    => 1,
    'orderby'   => 'post_date',
    'order'     => 'ASC',
    'status'    => 'future',
    'title'     => false,
    'league'    => $league->ID,
    'season'    => $season_id,
    'bonus'     => true
];

?>

<div class="sp_header sp_block">
 
    <div class="sp_header_logo">
        <img class="lozad lazy" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $league->image_url; ?>" alt="<?php $league->name; ?>" width="40" height="40">
    </div>
    <div class="sp_header_info">
        <div class="sp_header_top">
            <h1 class="sp_header_title"><?php echo $header_title ; ?></h1>
            <div class="sp_header_tabs sp_header_tabs_season">
                <?php foreach ($all_seasons as $all_season) {
                    if ($all_season->ID == $season_id) {
                        echo '<div class="sp_header_tab active">' . $all_season->name . '</div>'; 
                    } else {
                        if ( $all_season->ID == $main_season->ID ) {
                            $tab_link = $league->url;
                        } else {
                            $tab_link = $league->url . 'season-' . $all_season->term->slug  . '/'; 
                        }
                        echo '<a class="sp_header_tab" href="' . $tab_link . '">' . $all_season->name . '</a>';
                    }                        
                } ?>
            </div>
        </div>
        <div class="sp_header_tabs sp_header_tabs_desktop">
            <?php foreach ($league_tabs as $key => $league_tab) {
                if ($tab === $key) {
                    echo '<div class="sp_header_tab active">' . $league_tab . '</div>'; 
                } else {
                    if ($key == 'main') {
                        if ($league->season->ID == $season_id) {
                            $tab_link = $league->url;
                        } else {
                            $tab_link = $league->url . 'season-' . $season_slug . '/';
                        }

                    } else {
                        if ($league->season->ID == $season_id) {
                            $tab_link = $league->url . $key  . '/';
                        } else {
                            $tab_link = $league->url . 'season-' . $season_slug . '/' . $key . '/';
                        }
                    }
                    echo '<a class="sp_header_tab" href="' . $tab_link . '">' . $league_tab . '</a>';
                }
             }; ?>
        </div>
    </div>
        <div class="sp_header_tabs sp_header_tabs_mobile">
            <?php foreach ($league_tabs as $key => $league_tab) {
                if ($tab === $key) {
                    echo '<div class="sp_header_tab active">' . $league_tab . '</div>'; 
                } else {
                    if ($key == 'main') {
                        if ($league->season->ID == $season_id) {
                            $tab_link = $league->url;
                        } else {
                            $tab_link = $league->url . 'season-' . $season_slug . '/';
                        }
                    } else {
                        if ($league->season->ID == $season_id) {
                            $tab_link = $league->url . $key  . '/';
                        } else {
                            $tab_link = $league->url . 'season-' . $season_slug . '/' . $key . '/';
                        }
                    }
                    echo '<a class="sp_header_tab" href="' . $tab_link . '">' . $league_tab . '</a>';
                }
             }; ?>
        </div>    
    <?php sp_get_template( 'event-blocks.php', $game_header_args, SP()->template_path() . 'event/',  ); ?>

</div>
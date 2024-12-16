<?php
/**
 * Tournament Class
 *
 * The SNS SportsPress event class.
 *
 * @class 		SP_SNS_Event
 * @version		1.0.0
 * @package		SportsPress_SNS
 * @category	Class
 * @author 		Alex Torbeev
 */
class SP_SNS_Event extends SP_Event {

    public $team_home, $team_away, $permalink, $league_link, $season_link, $predict_id;

    public function __construct( $post ) {
        if ( $post instanceof WP_Post || $post instanceof SP_Custom_Post ) :
            $this->ID   = absint( $post->ID );
            $this->post = $post;
        else :
            $this->ID   = absint( $post );
            $this->post = get_post( $this->ID );
        endif;
        $this->set_teams();
        $this->set_links();
        $this->set_predict();
    }

    public function get_best_bets() {
        $bk_bets = get_post_meta( $this->post->ID, 'bets', 1 );

        if ( empty( $bk_bets ) ) {
            return false;
        }

        $bks = [
            'winline' => 8889,
            'fonbet'  => 8934,
            'betboom' => 9047
        ];

        $bets = [
            'П1'  => [
                'coef'  => 0,
                'url'   => '',
                'bk_id' => ''
            ],
            'X'   => [
                'coef'  => 0,
                'url'   => '',
                'bk_id' => ''                
            ],
            'П2'  => [
                'coef'  => 0,
                'url'   => '',
                'bk_id' => ''                
            ],
        ];
        foreach ( $bk_bets as $bk => $bk_bet ) {
            if ( (double)$bk_bet['П1'] > (double)$bets['П1']['coef'] ) {
                $bets['П1']['coef']  = $bk_bet['П1'];
                $bets['П1']['url']   = $bk_bet['url'];
                $bets['П1']['bk_id'] = $bks[$bk];
                $bets['П1']['bk']    = $bk;
            }
            if ( (double)$bk_bet['X'] > (double)$bets['X']['coef'] ) {
                $bets['X']['coef']  = $bk_bet['X'];
                $bets['X']['url']   = $bk_bet['url'];
                $bets['X']['bk_id'] = $bks[$bk];
                $bets['X']['bk']    = $bk;
            }
            if ( (double)$bk_bet['П2'] > (double)$bets['П2']['coef'] ) {
                $bets['П2']['coef']  = $bk_bet['П2'];
                $bets['П2']['url']   = $bk_bet['url'];
                $bets['П2']['bk_id'] = $bks[$bk];
                $bets['П2']['bk']    = $bk;
            }                         
        }

        return $bets;
    }

    public function get_bets() {
        $bk_bets = get_post_meta( $this->post->ID, 'bets', 1 );

        if ( empty( $bk_bets ) ) {
            return false;
        }

        $bks = [
            'winline' => 8889,
            'fonbet'  => 8934,
            'betboom' => 9047
        ];

        $bets = [];

        foreach ( $bk_bets as $bk => $bet ) {
            $bookmaker = Bookmaker::setup_all( $bks[ $bk ] );
            if ( $bk == 'winline' ) {
                $bet_link = $bet['url'];
            } else {
                $bet_link = $bookmaker->getPartnerLink();
            }
            $bk_image = $bookmaker->thumbnail->getLazyLoadImg('wp-post-image', ['alt' => $bookmaker->post_title], '131x40');
            $bk_title = $bookmaker->post_title;            
            $bets[] = [
                'bk_name'  => get_post_meta( $bks[ $bk ], 'bm_main_name', true ),
                'bk_link'  => get_permalink( $bks[ $bk ] ),
                'bk_image' => $bk_image,
                'bk_title' => $bk_title,
                'bet_link' => $bet_link,
                'П1'       => $bet['П1'],
                'X'        => $bet['X'],
                'П2'       => $bet['П2'],
            ];
        }

        return $bets;
    }

    public function get_score() {
        $score = '<a href="' . $this->permalink . '" title="' . $this->team_home->post->post_title . ' - ' . $this->team_away->post->post_title . '" >';
        if ( $this->finished ) {
            $results = sp_get_main_results( $this->ID );
            
            if ( isset( $results[0] ) ) {
                $score .= $results[0];
            }
            $score .= ':';
            if ( isset( $results[1] ) ) {
                $score .= $results[1];
            }
                      
        } else {
            $score .= '-';
        }
        $score .= '</a>';
        return $score;
    }

    public function getfullScore() {
        $results    = get_post_meta( $this->post->ID , 'sp_results', 1 );
        $sport_type = get_term_meta( wp_get_post_terms($this->post->ID, 'sp_league')[0]->term_id, 'sport_type', 1 );
        if ( $sport_type == 'football' ) {
            $overtime = 'ДВ ';
            $penalties = 'П ';
        }

        if ( $sport_type == 'hockey' ) {
            $overtime = 'OT ';
            $penalties = 'Б ';
        }

        $score = '(';

        $score .= $results[ $this->team_home->ID ]['firsthalf'] . ':' . $results[ $this->team_away->ID ]['firsthalf'];
        $score .= ', ' . $results[ $this->team_home->ID ]['secondhalf'] . ':' . $results[ $this->team_away->ID ]['secondhalf'];

        if ( isset( $results[ $this->team_home->ID ]['thirdhalf'] ) ) {
            $score .= ', ' . $results[ $this->team_home->ID ]['thirdhalf'] . ':' . $results[ $this->team_away->ID ]['thirdhalf'];
        }

        if ( isset( $results[ $this->team_home->ID ]['overtime'] ) ) {
            $score .= ', ' . $overtime . $results[ $this->team_home->ID ]['overtime'] . ':' . $results[ $this->team_away->ID ]['overtime'];
        }

        if ( isset( $results[ $this->team_home->ID ]['penalties'] ) ) {
            $score .= ', ' . $penalties . $results[ $this->team_home->ID ]['penalties'] . ':' . $results[ $this->team_away->ID ]['penalties'];
        }

        $score .= ')';
        return $score;

    }

    public function get_status_text() {
        $results    = get_post_meta( $this->post->ID , 'sp_results', 1 );
        if ( $this->sport_type == 'football' ) {
            $overtime = 'ДВ';
            $penalties = 'П';
        }

        if ( $this->sport_type == 'hockey' ) {
            $overtime = 'OT';
            $penalties = 'Б';
        }

        if ( $this->post->post_status == 'publish' ) {
            $prefix = '';
            if ( isset( $results[ $this->team_home->ID ]['overtime'] ) ) {
                $prefix = '<span class="result_prefix">' . $overtime . '</span>';
            }

            if ( isset( $results[ $this->team_home->ID ]['penalties'] ) ) {
                $prefix = '<span class="result_prefix">' . $penalties . '</span>';
            }            
            $result = $prefix . '<span class="result_status result_status_publish">завершен</span>';
        } else {
            $result = '<span class="result_status result_status_future">не начался</span>';
        }
        return $result;
    }


    public function set_teams() {
        $teams = (array) get_post_meta( $this->ID, 'sp_team');
        $teams = array_filter( $teams, 'sp_filter_positive' );
        if ( ! $teams ) {
           return;
        }
        $results = sp_get_main_results( $this->ID );

        $home_start = '';
        $home_end = '';
        $away_start = '';
        $away_end = '';        

        if ( isset($results[0]) && isset($results[1]) ) {
            if( $results[0] > $results[1] ) {
                $home_start = '<strong>';
                $home_end = '</strong>';
            }
            if( $results[1] > $results[0] ) {
                $away_start = '<strong>';
                $away_end = '</strong>';
            }            
        }

        $this->team_home = new SP_Team($teams[0]);
        $this->team_home->logo = get_the_post_thumbnail_url($this->team_home->post, 'post-thumbnail');
        $this->team_home->score = $results[0] ?? '–';
        $this->team_home->score_start = $home_start;
        $this->team_home->score_end = $home_end;

        $this->team_away = new SP_Team($teams[1]);
        $this->team_away->logo = get_the_post_thumbnail_url($this->team_away->post, 'post-thumbnail'); 
        $this->team_away->score = $results[1] ?? '–';
        $this->team_away->score_start = $away_start;
        $this->team_away->score_end = $away_end;        

    }

    public function set_links() {
        $main_page_slug = get_option('sportspress_sns_main_page', '');

        $seasons = get_the_terms( $this->ID, 'sp_season' );
        if ( $seasons ) {
            $season = array_shift( $seasons );
            $season_slug = $season->slug;
        }

        $leagues = get_the_terms( $this->ID, 'sp_league' );
        if ( $leagues ) {
            $league = array_shift( $leagues );
            $league_slug = $league->slug;
        }
        $sport_type = get_term_meta( $league->term_id, 'sport_type', true );

        $tv_link = get_post_meta( $this->ID, 'bk_tv', 1 );
        $date_now = strtotime('-1 day');
 
        if ( !empty($tv_link) && strtotime( $this->post->post_date ) > $date_now ) {
            $this->tv_link = $tv_link;
        } else {
            $this->tv_link = false;
        }

        $this->permalink   = '/game-' . $this->post->post_name . '/';
        $this->season_link = '/' . $main_page_slug . '/' . $season_slug . '/';
        $this->league_link = '/' . $main_page_slug . '/' . $season_slug . '/' . $league_slug . '/' ;
        $this->finished    = ( get_post_meta( $this->ID, 'sp_finished', true ) == 'yes' );
        $this->fixtures    = ( get_post_meta( $this->ID, 'sns_fixture_loaded', true ) == 'yes' );
        $this->sport_type  = $sport_type;

    }

    public function set_predict() {
        $post_args = [
            'post_type'   => 'predicts',
            'posts_per_page' => 1,
            'cache_results' => false,
            'update_post_meta_cache' => false,
            'meta_query' => [
                [
                    'key'   => 'sp_event',
                    'value' => $this->ID,
                ],
            ]
        ];

        $posts_query = new WP_Query;

        $posts = $posts_query->query($post_args);
        
        if ( $posts ) {
            $this->predict_id = $posts[0]->ID;
        } else {
            $this->predict_id = false;
        }
    }    

    public function title() {
        
        $title = 'Обзор матча ' . $this->team_home->post->post_title . ' - ' . $this->team_away->post->post_title;
 
        return apply_filters( 'sportspress_sns_event_get_page_header_title', $title );
    }



}

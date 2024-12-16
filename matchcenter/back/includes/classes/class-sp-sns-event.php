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

    public $team_home, $team_away, $permalink, $league_link, $season_link, $predict_id, $finished;

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
            if ( ! is_numeric( $bk ) ) {
                continue;
            }
            $bookmaker = new SP_SNS_Bookmaker( $bk );
            if ( (double)$bk_bet['П1'] > (double)$bets['П1']['coef'] ) {
                $bets['П1']['coef']  = $bk_bet['П1'];
                $bets['П1']['url']   = $bk_bet['url'];
                $bets['П1']['name']  = $bookmaker->name;
                $bets['П1']['image'] = $bookmaker->image;
            }
            if ( (double)$bk_bet['X'] > (double)$bets['X']['coef'] ) {
                $bets['X']['coef']  = $bk_bet['X'];
                $bets['X']['url']   = $bk_bet['url'];
                $bets['X']['name']  = $bookmaker->name;
                $bets['X']['image'] = $bookmaker->image;
            }
            if ( (double)$bk_bet['П2'] > (double)$bets['П2']['coef'] ) {
                $bets['П2']['coef']  = $bk_bet['П2'];
                $bets['П2']['url']   = $bk_bet['url'];
                $bets['П2']['name']  = $bookmaker->name;
                $bets['П2']['image'] = $bookmaker->image;
            }                         
        }

        if ( $bets['П1']['coef'] == 0 || $bets['X']['coef'] == 0 || $bets['П2']['coef'] == 0 ) {
            return false;
        }
        
        return $bets;
    }

    public function get_bets() {
        $bk_bets = get_post_meta( $this->post->ID, 'bets', 1 );

        if ( empty( $bk_bets ) ) {
            return false;
        }

        $bets = [];

        foreach ( $bk_bets as $bk => $bet ) {

            if ( ! is_numeric( $bk ) ) {
                continue;
            }

            $bookmaker = new SP_SNS_Bookmaker( $bk );
     
            $bets[] = [
                'bk_name'  => $bookmaker->name,
                'bk_link'  => $bookmaker->url,
                'bk_image' => $bookmaker->image,
                'bet_link' => $bet['url'],
                'П1'       => $bet['П1'],
                'X'        => $bet['X'],
                'П2'       => $bet['П2'],
            ];
        }

        return $bets;
    }

    public function get_score() {
        $score = '';
        $results = sp_get_main_results( $this->ID );
        if ( $this->finished ) {
            $score = '<div class="score_finished">';
            if ( isset( $results[0] ) ) {
                $score .= $results[0];
            }
            $score .= ':';
            if ( isset( $results[1] ) ) {
                $score .= $results[1];
            }
            $score .= '</div>';        
        } else {
            if ( $this->live ) {
                $score = '<div class="score_live">';
                if ( isset( $results[0] ) ) {
                    $score .= $results[0];
                }
                $score .= ':';
                if ( isset( $results[1] ) ) {
                    $score .= $results[1];
                }
                $score .= '</div>';
                $score .= '<div class="score_live_mins">';
                $score .= $this->timer . "'";
                $score .= '</div>';

            } else {
                $score .= '-';
            }
        }
        
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

        if ( $sport_type == 'basketball' ) {
            $overtime = 'OT ';
        }

        $score = '(';

        $score .= $results[ $this->team_home->ID ]['firsthalf'] . ':' . $results[ $this->team_away->ID ]['firsthalf'];
        $score .= ', ' . $results[ $this->team_home->ID ]['secondhalf'] . ':' . $results[ $this->team_away->ID ]['secondhalf'];

        if ( isset( $results[ $this->team_home->ID ]['thirdhalf'] ) ) {
            $score .= ', ' . $results[ $this->team_home->ID ]['thirdhalf'] . ':' . $results[ $this->team_away->ID ]['thirdhalf'];
        }

        if ( isset( $results[ $this->team_home->ID ]['fourthhalf'] ) ) {
            $score .= ', ' . $results[ $this->team_home->ID ]['fourthhalf'] . ':' . $results[ $this->team_away->ID ]['fourthhalf'];
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

        if ( $this->sport_type == 'basketball' ) {
            $overtime = 'OT';
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

        $home_start = '<span>';
        $home_end = '</span>';
        $away_start = '<span>';
        $away_end = '</span>';        

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

        $this->team_home = new SP_SNS_Team($teams[0]);
        //$this->team_home->logo = get_the_post_thumbnail_url($this->team_home->post, 'post-thumbnail');
        $this->team_home->score = $results[0] ?? '–';
        $this->team_home->score_start = $home_start;
        $this->team_home->score_end = $home_end;

        $this->team_away = new SP_SNS_Team($teams[1]);
        //$this->team_away->logo = get_the_post_thumbnail_url($this->team_away->post, 'post-thumbnail'); 
        $this->team_away->score = $results[1] ?? '–';
        $this->team_away->score_start = $away_start;
        $this->team_away->score_end = $away_end; 
        $this->results = $results;       

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

        $this->permalink   = get_home_url() . '/game-' . $this->post->post_name . '/';
        $this->season_link = '/' . $main_page_slug . '/' . $season_slug . '/';
        $this->league_link = '/' . $main_page_slug . '/' . $season_slug . '/' . $league_slug . '/' ;
        $this->finished    = ( get_post_meta( $this->ID, 'sp_finished', true ) == 'yes' );
        $this->live        = ( get_post_meta( $this->ID, 'sp_event_status', true ) == 'live' );
        $this->fixtures    = ( get_post_meta( $this->ID, 'sns_fixture_loaded', true ) == 'yes' );
        $this->timer       = get_post_meta( $this->ID, 'sp_minutes', true );
        $this->sport_type  = $sport_type;

    }

    public function set_predict() {
        
        $this->predict_id = false;
        $predict_type = get_option('sp_sns_predicts_post_type', false);

        if ( $predict_type ) {
            $post_args = [
                'post_type'   => $predict_type,
                'posts_per_page' => 1,
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
            }
        }

    }    

    public function title() {
        
        $title = 'Обзор матча ' . $this->team_home->post->post_title . ' - ' . $this->team_away->post->post_title;
 
        return apply_filters( 'sportspress_sns_event_get_page_header_title', $title );
    }

    public function setVote( $vote ) {
        $votes_meta = get_post_meta( $this->ID, 'sp_votes', true );
        if ( $votes_meta ) {
            $votes = $votes_meta;
        } else {
            $votes = [0, 0, 0, 0];            
        }
        $votes[ $vote ]++;
        $votes[0] = (int)$votes[1] + (int)$votes[2] +(int) $votes[3];
        update_post_meta( $this->ID, 'sp_votes', $votes );

        return $this->getVotes();

    }

    public function getVotes() {
        $votes = [0, 0, 0, 0];
        $votes_meta = get_post_meta( $this->ID, 'sp_votes', true );
        if ( $votes_meta ) {
            $votes_count = (int)$votes_meta[0];
            if ( $votes_count ) {
                $votes = [];
                $votes[0] = $votes_count;
                $votes[1] = round( $votes_meta[1] / $votes_count * 100 ) . '%';
                $votes[2] = round( $votes_meta[2] / $votes_count * 100 ) . '%';
                $votes[3] = round( $votes_meta[3] / $votes_count * 100 ) . '%';
            }
        }
        return $votes;
    }

    public function getApiId() {
        if ( $api_id = get_post_meta(  $this->ID, 'sns_apisport_id', true  ) ) {
            return $api_id;
        }
        return false;
    }

    public function getPreStatistics() {
        return SP_Loader_Predictions_Footbal::import( $this );
    }

    public function getStage() {
        $stages = wp_get_post_terms( $this->ID, 'sp_stage', array('fields' => 'ids') );
        if ( count( $stages ) ) {
            $stage = new SP_SNS_Stage( $stages[0] );
            return $stage->name;
        } else {
            return get_post_meta( $this->ID, 'sp_day', true ); 
        }
    }

}

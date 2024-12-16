<?php
/**
 * Team Class
 *
 * The SNS SportsPress team class.
 *
 * @class 		SP_SNS_Team
 * @version		1.0.0
 * @package		SportsPress_SNS
 * @category	Class
 * @author 		Alex Torbeev
 */
class SP_SNS_Team {

    public $ID, $post, $logo, $url, $sport;

    public function __construct( $post ) {
        if ( $post instanceof WP_Post || $post instanceof SP_Custom_Post ) :
            $this->ID   = absint( $post->ID );
            $this->post = $post;
        else :
            $this->ID   = absint( $post );
            $this->post = get_post( $this->ID );
        endif;
        $this->setVars();
    }

    protected function setVars() {
        $this->logo          = get_the_post_thumbnail_url($this->ID, 'post-thumbnail');
        $this->url           = get_permalink( $this->ID );
        $this->api_id        = false;
 
        $api_id = get_post_meta( $this->ID, 'sns_apisport_id', 1 );
        if ( $api_id && ! empty($api_id) ) {
            $this->api_id = $api_id;
        }

        if ( $leagues = $this->getLeagues() ) {
            foreach ( $leagues as $league );
        } 

    }

    public function getSport() {
        if ( $leagues = $this->getLeagues() ) {
            return new SP_SNS_Sport( $leagues[0]->sport_type );
        } 
        return false;       
    }

    public function hasTransfers() {
        $transfers = true;
        if ( $leagues = $this->getLeagues() ) {
            foreach ( $leagues as $league ) {
                if ( ! $league->has_transfers ) {
                    $transfers = false;
                }
            }
        } else {
            $transfers = false;
        } 
        return $transfers;       
    }    

    public function getLeagues() {
        $leagues_terms = wp_get_post_terms( $this->ID, 'sp_league' );
        if ( count( $leagues_terms ) ) {
            $leagues = [];
            foreach ( $leagues_terms as $league_term ) {
                $leagues[] = new SP_SNS_League( $league_term->term_id );;
            }
            return $leagues;          
        }
        return false;
        
    }

    public function getSeasonLeagues( $season_id ) {
        $leagues_terms = wp_get_post_terms( $this->ID, 'sp_league' );
        $leagues = [];
        if ( count( $leagues_terms ) ) {
            foreach ( $leagues_terms as $league_term ) {
                $league = new SP_SNS_League( $league_term->term_id );
                $seasons = $league->getSeasonSeasons( $season_id );
                if ( count( $seasons ) ) {
                    $leagues[] = $league;
                }
            }            
        }
        return $leagues;
    }

    public function getSeasonSeasons( $season_id ) {
        $season_seasons = [];
        $seasons_terms = wp_get_post_terms( $this->ID, 'sp_season' );
        foreach ( $seasons_terms as $season_term ) {
            $season = new SP_SNS_Season( $season_term->term_id );
            if ( $season->ID == $season_id || ( $season->parent && $season->parent->ID == $season_id ) ) {
                $season_seasons[] = $season;
            }
        }
        return $season_seasons;
    }

    public function getNews( $count = 6 ) {
        $news_args = [
            'post_type'   => 'post',
            'numberposts' => $count,
            'status'      => 'publish',
            'meta_query'   => [
                [
                    'key'   => 'sp_post_team',
                    'value' => $this->ID,
                ],
            ]
        ];

        $sport = $this->getSport();
        if ( $sport && $sport->news_term ) {
            $news_args['tax_query'][] = [
                'taxonomy' => 'category',
                'field'    => 'id',
                'terms'    => $sport->news_term->term_id
            ];
        }

        $news = get_posts( $news_args );
 
        if ( count( $news ) ) {
            return $news;
        }
 

        return false;        
    }


    public function getPredicts( $count = 6 ) {

        $predict_type = get_option('sp_sns_predicts_post_type', false);

        if ( $predict_type ) {

            $predicts_args = [
                'post_type'   => $predict_type,
                'numberposts' => $count,
                'status'      => 'publish',
                'meta_query'   => [
                    [
                        'key'   => 'sp_team',
                        'value' => $this->ID,
                    ],
                ]
            ];

            $predicts = get_posts( $predicts_args );
     
            if ( count( $predicts ) ) {
                return $predicts;
            }
        } 

        return false;        
    }

    public function getTables( $season_id = false ) {

        if ( $season_id ) {
            $seasons = $this->getSeasonSeasons( $season_id );
            $season_ids = [];
            foreach ( $seasons as $season ) {
                $season_ids[] = $season->ID;
            }
            $tables_args = [
                'post_type'      => 'sp_table',
                'numberposts'    => -1,
                'status'         => 'publish',
                'tax_query'      => [
                    'relation' => 'AND',
                    [
                        'taxonomy' => 'sp_season',
                        'field'    => 'term_id',
                        'terms'    => $season_ids,
                    ]       
                ],
                'meta_query'   => [
                    [
                        'key'      => 'sp_teams',
                        'value'    => $this->ID,
                        'compare'  => 'LIKE' 
                    ]
                ]  
            ];

            $tables = get_posts( $tables_args );
            if ( count( $tables ) ) {
                return $tables;
            }
        }

        return false;

    }    

    public function getPlayers( $season_id ) {

        if ( ! $season_id ) {
            return false;
        }

        $leagues = $this->getSeasonLeagues( $season_id );

        $players = [];

        foreach ( $leagues as $league ) {
            $seasons = $league->getSeasonSeasons( $season_id );
            foreach ( $seasons as $season ) {
                $array_name = $league->name . ' (' . $season->name . ')';
                $meta_key = 'sp_stat_' . $season->ID . '_' . $league->ID . '_' . $this->ID;
                $players_args = [
                    'post_type'      => 'sp_player',
                    'numberposts'    => -1,
                    'status'         => 'publish',
                    'meta_query'   => [
                        'stat' => [
                            'key'         => $meta_key,
                            'compare_key' => 'EXISTS',
                        ],
                        'position' => [
                            'key'         => 'sp_position',
                            'compare_key' => 'EXISTS',
                        ]
                    ],
                    'orderby' => 'position',
                    'order'   => 'ASC',
                ];

                $players_posts = get_posts( $players_args );
                if ( count( $players_posts ) ) {
                    foreach ( $players_posts as $player ) {
                        $players[ $array_name ][ $player->ID ] = get_post_meta( $player->ID, $meta_key, 1 );
                    }
                }

            } 


        }

        return $players;

    }  


}

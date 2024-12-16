<?php
/**
 * League Class
 *
 * The SNS SportsPress sport class.
 *
 * @class 		SP_SNS_League
 * @version		1.0.0
 * @package		SportsPress_SNS
 * @category	Class
 * @author 		Alex Torbeev
 */
class SP_SNS_League {

    public $ID, $term;

    public function __construct( $term_id ) {
        $this->ID   = $term_id;
        $this->term = get_term( $term_id );
        $this->setVars();
    }

    protected function setVars() {
        $this->sport_type     = get_term_meta( $this->ID, 'sport_type', 1 );
        $this->url            = get_term_link( $this->term );
        $this->name           = $this->term->name;
        $this->hide_transfers = ( get_term_meta( $this->ID, 'hide_transfers', 1 ) == 'yes' );
        $this->news_term      = false;
        $this->news_link      = false;
        $this->articles_term  = false;
        $this->articles_link  = false;
        $this->predicts_term  = false;
        $this->predicts_link  = false;
        $this->api_id         = false;
        $this->image_url      = false;
        $this->has_transfers  = false;
        $this->is_national    = get_term_meta( $this->ID, 'is_national', true ) == 'yes';

        $api_id = get_term_meta( $this->ID, 'sp_order', 1 );
        if ( $api_id && ! empty($api_id) ) {
            $this->api_id = $api_id;
        }

        $image_id = get_term_meta( $this->ID, '_thumbnail_id', 1 );
        if ( $image_id ) {
            $this->image_url = wp_get_attachment_image_url( $image_id, 'full' );
        }

        $option_season_id = get_option('sportspress_season');
        $league_season_id = get_term_meta( $this->ID, 'season_term', 1 );

        if ( $league_season_id ) {
            $this->season = new SP_SNS_Season( $league_season_id );
        } else {
            $this->season = new SP_SNS_Season( $option_season_id );
        }

        if ( $this->season->term->parent ) {
            $this->season_main = new SP_SNS_Season( $this->season->term->parent );
        } else {
            $this->season_main = $this->season;
        }

        $news_term = get_term_meta( $this->ID, 'news_term', true );
        if ( $news_term && $news_term > 0 ) {
            $this->news_term = get_term( $news_term, 'category' );
            $this->news_link = get_term_link( $this->news_term );
            $this->news = $news_term;
        }

        $articles_term = get_term_meta( $this->ID, 'articles_term', true );
        if ( $articles_term && $articles_term > 0 ) {
            $this->articles_term = get_term( $articles_term, 'category' );
            $this->articles_link = get_term_link( $this->articles_term );
        }        

        $predicts_term = get_term_meta( $this->ID, 'predicts_term', true );
        if ( $predicts_term && $predicts_term > 0 )  {

            $predicts_tax       = get_option('sp_sns_predicts_taxonomy_league');
            $has_slices         = get_option('sp_sns_predicts_slices');

            $this->predicts_term = get_term( $predicts_term, $predicts_tax );

            if ( $has_slices ) {
                $slice_meta      = get_option( 'sp_sns_predicts_slice_meta' );
                $predicts_page   = get_term_meta( $this->predicts_term->term_id, $slice_meta, true);
                $this->predicts_link = get_permalink( $predicts_page );            
            } else {
                $this->predicts_link = get_term_link( $this->predicts_term );
            }            
        }

        if ( $sport = $this->getSport() ) {
            $this->has_transfers = ( $sport->has_transfers && ! $this->is_national && ! $this->hide_transfers );
        }

    }

    public function isCron() {
        return get_term_meta( $this->ID, 'is_cron', true ) == 'yes';
    }

    public function getSport() {
        if ( $this->sport_type ) {
            return new SP_SNS_Sport( $this->sport_type );
        }
        return false;
    }

    public function getSeasons() {
        $season_ids = get_term_meta( $this->ID, 'season_terms', true);
        $seasons = [];
        if ( is_array( $season_ids ) && count( $season_ids ) ) {
            foreach ( $season_ids as $season_id ) {
                $seasons[] = new SP_SNS_Season( $season_id );
            }
        }
        return $seasons;
    }

    public function getSeasonSeasons( $season_id ) {
        $season_seasons = [];
        $seasons = $this->getSeasons();
        foreach ( $seasons as $season ) {
            if ( $season->ID == $season_id || ( $season->parent && $season->parent->ID == $season_id ) ) {
                $season_seasons[] = $season;
            }
        }
        return $season_seasons;
    }

    public function getStages( $season_id ) {
        
        $stages = [];
        
        $all_stages = get_term_meta( $this->ID, 'sp_stages', true );
        if ( $season_id && $all_stages && is_array( $all_stages ) && isset( $all_stages[ $season_id ] ) ) {
            $season_stages = $all_stages[ $season_id ];
            
            foreach ( $season_stages as $stage_id ) {
                $stages[] = new SP_SNS_Stage( $stage_id );
            }
            
        }

        return $stages;

    }

    public function getPlayoffs( $season_id ) {
        
        $stages = [];
        
        $all_stages = get_term_meta( $this->ID, 'sp_stages', true );
        if ( $season_id && $all_stages && is_array( $all_stages ) && isset( $all_stages[ $season_id ] ) ) {
            $season_stages = $all_stages[ $season_id ];
        }

        $playoff_args = [
            'taxonomy'  => 'sp_stage',
            'include'   => $season_stages,
            'orderby'   => 'id',
            'meta_query' => [
                [
                    'key'   => 'is_playoff',
                    'value' => 'yes'
                ]
            ]
        ];

        $playoffs = get_terms( $playoff_args );

        if ( count( $playoffs ) ) {

            foreach ( $playoffs as $key => $playoff ) {
                if ( $playoff->parent == 0 ) {
                    $stages[ $playoff->term_id ][] = $playoff;
                    unset( $playoffs[ $key ] );
                }
            }

            foreach ( $playoffs as $playoff ) {
                foreach ( $stages as $key => $stage ) {
                    if ( term_is_ancestor_of( $key, $playoff, 'sp_stage' ) ) {
                        $stages[$key][] = $playoff;
                    }
                }
            }

            foreach ( $stages as $index => $terms ) {
                foreach ( $terms as $key => $stage ) {
                    $stages[$index][$key] = new SP_SNS_Stage( $stage->term_id );
                }
            }

        }

        return $stages;          
    }


    public function getTeams( $season_id = false ) {

        $teams_args = [
            'post_type'   => 'sp_team',
            'numberposts' => -1,
            'orderby'     => 'post_title',
            'order'       => 'ASC',  
            'tax_query'   => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'sp_league',
                    'field'    => 'term_id',
                    'terms'    => $this->ID,
                ],
             ],
        ];
        if ( $season_id ) {
            $teams_args['tax_query'][] = [
                'taxonomy' => 'sp_season',
                'field'    => 'term_id',
                'terms'    => $season_id,
            ];
        }

        $teams = get_posts( $teams_args );
        return $teams;

    }

    public function getNews( $count = 6 ) {
        
        if ( $this->news_term ) {
            $news_args = [
                'post_type'   => 'post',
                'numberposts' => $count,
                'status'      => 'publish',
                'tax_query'   => [
                    'relation' => 'OR',
                    [
                        'taxonomy' => 'sp_league',
                        'field'    => 'id',
                        'terms'    => [ $this->ID ]                    
                    ],
                    [
                        'taxonomy' => 'category',
                        'field'    => 'id',
                        'terms'    => [ $this->news_term->term_id ]                    
                    ],                    
                ]
            ];

            $news = get_posts( $news_args );

            if ( count( $news ) ) {
                return $news;
            }
        } 

        return false;
    } 

    public function getArticles( $count = 6 ) {
        
        if ( $this->articles_term ) {
            $articles_args = [
                'post_type'   => 'post',
                'numberposts' => $count,
                'status'      => 'publish',
                'tax_query'   => [
                    'relation' => 'OR',
                    [
                        'taxonomy' => 'sp_league',
                        'field'    => 'id',
                        'terms'    => [ $this->ID ]                    
                    ],
                    [
                        'taxonomy' => 'category',
                        'field'    => 'id',
                        'terms'    => [ $this->articles_term->term_id ]                    
                    ],                    
                ]
            ];

            $articles = get_posts( $articles_args );

            if ( count( $articles ) ) {
                return $articles;
            }
        } 

        return false;
    }

    public function getPredicts( $count = 6 ) {
        
        $predict_type = get_option('sp_sns_predicts_post_type', false);

        if ( $predict_type && $this->predicts_term ) {

            $predicts_args = [
                'post_type'   => $predict_type,
                'numberposts' => $count,
                'status'      => 'publish',
            ];

  
            $tournament_field  = get_option('sp_sns_predicts_tournament_meta', false);
            if ( $tournament_field ) {
               $predicts_args['meta_query'][] = [
                    'key'   => $tournament_field,
                    'value' => $this->predicts_term->term_id                    
               ];
            } else {
                $tournament_taxonomy  = get_option('sp_sns_predicts_taxonomy_league', false);
                $predicts_args['tax_query'] = [
                    'relation' => 'OR',
                    [
                        'taxonomy' => $tournament_taxonomy,
                        'field'    => 'id',
                        'terms'    => [ $this->predicts_term->term_id ]
                    ],
                    [
                        'taxonomy' => 'sp_league',
                        'field'    => 'id',
                        'terms'    => [ $this->ID ]                    
                    ],                        
                ];
            }


            $predicts = get_posts( $predicts_args );

            if ( count( $predicts ) ) {
                return $predicts;
            }

        }
 
        return false;
    }

    public function getTables( $season_id = false ) {

        if ( $season_id ) {
            $tables_args = [
                'post_type'      => 'sp_table',
                'numberposts'    => -1,
                'status'         => 'publish',
                'order'          => 'ASC',
                'tax_query'      => [
                    'relation' => 'AND',
                    [
                        'taxonomy' => 'sp_league',
                        'field'    => 'term_id',
                        'terms'    => $this->ID,
                    ],
                    [
                        'taxonomy' => 'sp_season',
                        'field'    => 'term_id',
                        'terms'    => $season_id,
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


}

<?php
/**
 * Sport Class
 *
 * The SNS SportsPress sport class.
 *
 * @class 		SP_SNS_Sport
 * @version		1.0.0
 * @package		SportsPress_SNS
 * @category	Class
 * @author 		Alex Torbeev
 */
class SP_SNS_Sport {

    public $type;

    public function __construct( $type ) {
        $this->type = $type;
        $this->setVars();
    }

    protected function setVars() {

        $names = [
            'football'   => 'Футбол',
            'hockey'     => 'Хоккей',
            'basketball' => 'Баскетбол',
            'tennis'     => 'Теннис'
        ];

        $this->name          = $names[ $this->type ];
        $this->url           = get_permalink( get_page_by_path( get_option('sportspress_sns_main_' . $this->type . '_page', '') ) );

        $this->news_term     = false;
        $this->news_link     = false;
        $this->articles_term = false;
        $this->articles_link = false;
        $this->predicts_term = false;
        $this->predicts_link = false;
        $this->transfer_link = false;
        $this->has_transfers = $this->type == 'football';
    
        $news_term_slug     = get_option('sportspress_sns_news_' . $this->type . '_term', false);
        $articles_term_slug = get_option('sportspress_sns_articles_' . $this->type . '_term', false);
        $predicts_term_slug = get_option('sportspress_sns_predicts_' . $this->type . '_term', false);
        $transfer_slug      = get_option('sportspress_sns_transfers_' . $this->type . '_page', false);

        if ( $news_term_slug && $news_term_slug != 'default' ) {
            $this->news_term = get_term_by('slug', $news_term_slug, 'category' );
            $this->news_link = get_term_link( $this->news_term );
        }
        if ( $articles_term_slug != 'default' ) {
            $this->articles_term = get_term_by('slug', $articles_term_slug, 'category' );
            $this->articles_link = get_term_link( $this->articles_term );
        }
        
        if ( $predicts_term_slug && $predicts_term_slug != 'default' ) {

            $predicts_tax       = get_option('sp_sns_predicts_taxonomy');
            $has_slices         = get_option('sp_sns_predicts_slices');

            $this->predicts_term = get_term_by('slug', $predicts_term_slug, $predicts_tax );

            if ( $has_slices ) {
                $slice_meta      = get_option( 'sp_sns_predicts_slice_meta' );
                $predicts_page   = get_term_meta( $this->predicts_term->term_id, $slice_meta, true);
                $this->predicts_link = get_permalink( $predicts_page );            
            } else {
                $this->predicts_link = get_term_link( $this->predicts_term );
            }
        }

        if ( $transfer_slug && $transfer_slug != 'default' ) {
            $transfer_page       = get_page_by_path( $transfer_slug );
            $this->transfer_link = get_permalink( $transfer_page );
        }
        
    }
 
    public function getLeagues( $option_season_id = false ) {
        
        $leagues = [];

        $leagues_terms = get_terms( [
            'taxonomy'   => 'sp_league',
            'meta_key'   => 'sport_type',
            'meta_value' => $this->type
        ] );

        usort( $leagues_terms, 'sp_sort_terms' );

        if ( ! $option_season_id ) {
            $option_season_id = get_option('sportspress_season');
        }

        foreach ( $leagues_terms as $term ) {
            $league = new SP_SNS_League( $term->term_id );
            if ( $league->season_main->ID == $option_season_id ) {
                $leagues[] = $league;
            }
        }

        return $leagues;
    }

    public function getNews( $count = 6 ) {

        if ( $this->news_term ) {
            $news = get_posts( [
                'post_type'   => 'post',
                'numberposts' => $count,
                'status'      => 'publish',
                'category'    => $this->news_term->term_id
            ] );

            if ( count( $news ) ) {
                return $news;
            }
        }

        return false;
    }

    public function getArticles( $count = 6 ) {

        if ( $this->articles_term ) {
            $articles = get_posts( [
                'post_type'   => 'post',
                'numberposts' => $count,
                'status'      => 'publish',
                'category'    => $this->articles_term->term_id
            ] );

            if ( count( $articles ) ) {
                return $articles;
            }
        }

        return false;
    }

    public function getPredicts( $count = 6 ) {

        if ( $this->predicts_term ) {
            $predict_type = get_option('sp_sns_predicts_post_type', false);
            $sport_field  = get_option('sp_sns_predicts_sport_meta', false);
            if ( $predict_type ) {
                if ( $sport_field ) {
                    $predicts = get_posts( [
                        'post_type'   => $predict_type,
                        'numberposts' => $count,
                        'status'      => 'publish',
                        'meta_query'  => [
                            [
                                'key'   => $sport_field,
                                'value' => $this->predicts_term->term_id
                            ]
                        ]
                    ] );
                } else {
                    $sport_taxonomy  = get_option('sp_sns_predicts_taxonomy', false);
                    $predicts = get_posts( [
                        'post_type'   => $predict_type,
                        'numberposts' => $count,
                        'status'      => 'publish',
                        'tax_query'   => [
                            [
                                'taxonomy' => $sport_taxonomy,
                                'field'    => 'id',
                                'terms'    => $this->predicts_term->term_id
                            ]
                        ]
                    ] );
                }

                if ( count( $predicts ) ) {
                    return $predicts;
                }
            }               
        }

        return false;
    }

}

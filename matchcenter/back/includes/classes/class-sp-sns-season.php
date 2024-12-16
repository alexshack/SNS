<?php
/**
 * Season Class
 *
 * The SNS SportsPress sport class.
 *
 * @class 		SP_SNS_Season
 * @version		1.0.0
 * @package		SportsPress_SNS
 * @category	Class
 * @author 		Alex Torbeev
 */
class SP_SNS_Season {

    public $ID, $term;

    public function __construct( $term_id ) {
        $this->ID   = $term_id;
        $this->term = get_term( $term_id );
        $this->setVars();
    }

    protected function setVars() {
        $this->url    = get_term_link( $this->term );
        $this->name   = $this->term->name;
        $this->slug   = $this->term->slug;
        $this->api_id = false;
        $this->parent = false;

        $api_id = get_term_meta( $this->ID, 'sns_apisport_id', 1 );
        if ( $api_id && ! empty($api_id) ) {
            $this->api_id = $api_id;
        }

        if ( $this->term->parent ) {
            $this->parent = new SP_SNS_Season( $this->term->parent );
        }
    }

    public function getLeagues() {
        $leagues = [];

        $leagues_terms = get_terms( [
            'taxonomy'   => 'sp_league',
        ] );

        usort( $leagues_terms, 'sp_sort_terms' );

        foreach ( $leagues_terms as $term ) {
            $league = new SP_SNS_League( $term->term_id );
            $seasons = $league->getSeasons();
            foreach ( $seasons as $season ) {
                if ( $league->season_main->ID == $option_season_id ) {
                    $leagues[] = $league;
                }
            }
        }

        return $leagues;        
    }

}
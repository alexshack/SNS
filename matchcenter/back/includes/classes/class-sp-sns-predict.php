<?php
/**
 * Predict Class
 *
 * The SNS SportsPress predict class.
 *
 * @class 		SP_SNS_Predict
 * @version		1.0.0
 * @package		SportsPress_SNS
 * @category	Class
 * @author 		Alex Torbeev
 */
class SP_SNS_Predict {

    public $ID, $post, $image, $url, $bookmaker;

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
        $this->image = get_the_post_thumbnail_url($this->ID, 'post-thumbnail');
        $this->url   = get_permalink( $this->ID );
        $this->title = $this->post->post_title;

        $this->date = false;
        if ( $date_field = get_option( 'sp_sns_predicts_date_meta', false ) ) {
            if ( $date = get_post_meta( $this->ID, $date_field, 1) ) {
                $this->date = wp_date( 'd.m.Y в H:i', $date );
            }
        }

        $this->bookmaker = false;
        if ( $bk_id = get_option( 'sns_bk_api_winline', false ) ) {
            $this->bookmaker = new SP_SNS_Bookmaker( $bk_id );
        }

        $this->tournament = false;
        $tournament_taxonomy = get_option('sp_sns_predicts_taxonomy_league', false);
        $tournament_field    = get_option('sp_sns_predicts_tournament_meta', false);
        if ( $tournament_taxonomy && $tournament_field ) {
            if ( $tournament_id = get_post_meta( $this->ID, $tournament_field, 1) ) {
                $tournament = get_term_by( 'id', $tournament_id, $tournament_taxonomy );
                $this->tournament = $tournament->name;
            }
        }

        $this->stake = false;
        $stake = [];
        $bet_taxonomy = get_option('sp_sns_predicts_taxonomy_stakes', false);
        $bet_field    = get_option('sp_sns_predicts_stake_meta', false);
        $bet_field_2  = get_option('sp_sns_predicts_stake_meta_2', false);

        if ( $bet_taxonomy && $bet_field ) {
             if ( $bet_type_id = get_post_meta( $this->ID, $bet_field, 1) ) {
                $bet_type = get_term_by( 'id', $bet_type_id, $bet_taxonomy );
                $stake[] = $bet_type->name;
            }
        }

        if ( $bet_field_2 ) {
            $stake[] = get_post_meta( $this->ID, $bet_field_2, 1);
        }

        if ( count( $stake ) ) {
            $this->stake = implode(' ', $stake);
        }
    }

}

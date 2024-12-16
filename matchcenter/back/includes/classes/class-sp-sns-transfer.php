<?php
/**
 * Transfer Class
 *
 * The SportsPress tournament class handles individual tournament data.
 *
 * @class 		SP_SNS_Transfer
 * @version		1.0.0
 * @package		SportsPress_SNS
 * @category	Class
 * @author 		Alex Torbeev
 */
class SP_SNS_Transfer extends SP_Custom_Post {
    
    public $team_in, $team_out, $player, $type;

    public function __construct( $post ) {
        if ( $post instanceof WP_Post || $post instanceof SP_Custom_Post ) :
            $this->ID   = absint( $post->ID );
            $this->post = $post;
        else :
            $this->ID   = absint( $post );
            $this->post = get_post( $this->ID );
        endif;
        $this->set_data();
    }

    public function set_data() {

        $team_in_id = get_post_meta( $this->ID, 'sp_team_in', true);
        $this->team_in = new SP_Team($team_in_id);
        $this->team_in->logo = get_the_post_thumbnail_url($this->team_in->post, 'sportspress-fit-mini');

        $team_out_id = get_post_meta( $this->ID, 'sp_team_out', true);
        $this->team_out = new SP_Team($team_out_id);
        $this->team_out->logo = get_the_post_thumbnail_url($this->team_out->post, 'sportspress-fit-mini');

        $team_player_id = get_post_meta( $this->ID, 'sp_player', true);
        $this->player = new SP_Player($team_player_id);
        $this->player->logo = get_the_post_thumbnail_url($this->player->post, 'w70h70');
        $positions = get_the_terms($team_player_id, 'sp_position');
        $this->player->position = $positions ? $positions[0]->name : '';

        $types = get_the_terms($this->ID, 'sp_transfer_type');
        $type = array_shift($types);

        if ($type->slug == 'sale') {
           $this->type = get_post_meta( $this->ID, 'sp_summ', true);
        } else {
           $this->type = $type->name; 
        }

    }


}

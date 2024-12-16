<?php
/**
 * Season Meta Boxes
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SP_SNS_League_Meta_Boxes
 */

class SP_SNS_Season_Meta_Boxes {

    public function __construct() {
        add_action( 'sp_season_add_form_fields',  array( $this, 'add_season_fields') ); 
        add_action( 'sp_season_edit_form_fields', array( $this, 'add_season_edit_fields'), 10, 2 ); 
        add_action( 'created_sp_season',          array( $this, 'save_season_fields') );
        add_action( 'edited_sp_season',           array( $this, 'save_season_fields') );
    }

    public function add_season_fields($taxonomy) {
        echo '
            <div class="form-field">
                <label for="sns_apisport_id">API ID сезона</label>
                <input type="number" name="sns_apisport_id" id="sns_apisport_id" />
            </div>            
            ';

    }

    public function add_season_edit_fields( $term, $taxonomy ) {
     
        $sns_apisport_id = get_term_meta( $term->term_id, 'sns_apisport_id', true );
        $sns_description = get_term_meta( $term->term_id, 'sns_description', true );
    
        echo '
        <tr class="form-field">
        <th><label for="sns_apisport_id">API ID сезона</label></th>
        <td><input name="sns_apisport_id" id="sns_apisport_id" type="text" value="' . esc_attr( $sns_apisport_id ) .'" /></td>
        </tr>
        <tr class="form-field">
        <th><label for="sns_description">Примечания</label></th>
        <td><input name="sns_description" id="sns_description" type="text" value="' . esc_attr( $sns_description ) .'" /></td>
        </tr>                 
         ';
        ?>

        
    <?php       
     
    }

    public function save_season_fields( $term_id ) {
     
        $season_fields = [
            'sns_apisport_id',
            'sns_description'
        ];

       foreach ($season_fields as $season_field) {
            if( isset( $_POST[ $season_field ] ) ) {
                update_term_meta( $term_id, $season_field, sanitize_text_field( $_POST[ $season_field ] ) );
            } else {
                delete_term_meta( $term_id, $season_field );
            }
        }


    }


}
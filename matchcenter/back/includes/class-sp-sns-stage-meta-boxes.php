<?php
/**
 * Stage Meta Boxes
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SP_SNS_Stage_Meta_Boxes
 */

class SP_SNS_Stage_Meta_Boxes {

    public function __construct() {
        add_action( 'sp_stage_edit_form_fields', array( $this, 'add_stage_edit_fields'), 10, 2 ); 
        add_action( 'edited_sp_stage', array( $this, 'save_stage_fields') );
    }


    public function add_stage_edit_fields( $term, $taxonomy ) {
     
        $is_playoff = get_term_meta( $term->term_id, 'is_playoff', true );
        $rus_name   = get_term_meta( $term->term_id, 'rus_name', true );

        echo '
        <tr>        
        <th>Это стадия плей-офф</th>
        <td>
        <label><input type="checkbox" name="is_playoff" ' . checked( 'yes', $is_playoff, false ) . ' /> Да</label>
        </td>
        </tr>                
        <tr class="form-field">
        <th><label for="rus_name">Название на русском</label></th>
        <td><input name="rus_name" id="rus_name" type="text" value="' . esc_attr( $rus_name ) .'" /></td>
        </tr>         
         ';
        ?>

        
    <?php       
     
    }

    public function save_stage_fields( $term_id ) {
     
        $stage_fields = [
            'rus_name',
        ];

        foreach ($stage_fields as $stage_field) {
            if( isset( $_POST[ $stage_field ] ) ) {
                update_term_meta( $term_id, $stage_field, sanitize_text_field( $_POST[ $stage_field ] ) );
            } else {
                delete_term_meta( $term_id, $stage_field );
            }
        }


        if( isset( $_POST[ 'is_playoff' ] ) && 'on' == $_POST[ 'is_playoff' ] ) {
            update_term_meta( $term_id, 'is_playoff', 'yes' );
        } else {
            delete_term_meta( $term_id, 'is_playoff' );
        }

     
 
    }



}
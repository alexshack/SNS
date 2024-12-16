<?php
/**
 * Stage Class
 *
 * The SNS SportsPress stage class.
 *
 * @class 		SP_SNS_Stage
 * @version		1.0.0
 * @package		SportsPress_SNS
 * @category	Class
 * @author 		Alex Torbeev
 */
class SP_SNS_Stage {

    public $ID, $name;

    public function __construct( $term_id ) {
        $this->ID   = $term_id;
        $this->term = get_term( $term_id );
        $this->setVars();
    }

    protected function setVars() {
        $this->name   = $this->term->name;

        $rus_name = get_term_meta( $this->ID, 'rus_name', 1 );
        if ( $rus_name && ! empty($rus_name) ) {
            $this->name = $rus_name;
        }
    }

}
<?php
/**
 * Bookmaker Class
 *
 * The SNS SportsPress bookmaker class.
 *
 * @class 		SP_SNS_League
 * @version		1.0.0
 * @package		SportsPress_SNS
 * @category	Class
 * @author 		Alex Torbeev
 */
class SP_SNS_Bookmaker {

    public $ID, $link, $name;

    public function __construct( $bk_id ) {
        $this->ID   = $bk_id;
        $this->post = get_post( $this->ID );
        $this->setVars();
    }

    protected function setVars() {
        $bk_link = get_post_meta( $this->ID, get_option('sns_bk_link', ''), true );

        $this->link  = home_url( get_option('sns_bk_path', '') . $bk_link . '/' );
        $this->name  = get_post_meta( $this->ID, get_option('sns_bk_name', ''), true );
        $this->image = get_the_post_thumbnail_url( $this->ID, 'thumbnail' );
        $this->url   = get_permalink( $this->ID );

    }
 


}

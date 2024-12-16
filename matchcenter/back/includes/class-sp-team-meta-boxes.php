<?php
/**
 * Team Meta Boxes
 *
 * @author 		Alex Torbeev
 * @category 	Admin
 * @package 	SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SP_Transfer_Meta_Boxes
 */
class SP_Team_Meta_Boxes {

	static $bk_names = [
		'winline_id' => 'Винлайн',
		'fonbet_id' => 'Фонбет',
		'betboom_id' => 'Betboom',
	];

	/**
	 * Constructor
	 */
	public function __construct() {
		//add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'sportspress_process_sp_team_meta', array( $this, 'save' ) );
	}

	/**
	 * Add Meta boxes
	 */
	public function add_meta_boxes() {
		add_meta_box( 'sp_contenttablediv', 'Таблица текст', array( $this, 'content_table' ), 'sp_team', 'normal', 'default' );
		add_meta_box( 'sp_contentcalendardiv', 'Календарь текст', array( $this, 'content_calendar' ), 'sp_team', 'normal', 'default' );
		add_meta_box( 'sp_contenttransfersdiv', 'Трансферы текст', array( $this, 'content_transfers' ), 'sp_team', 'normal', 'default' );
		add_meta_box( 'sp_bkids', 'ID букмекеров', array( $this, 'bk_ids' ), 'sp_team', 'side', 'high' );
	}

	/**
	 * Remove default meta boxes
	 */
	public function remove_meta_boxes() {

	}


	/**
	 * Output the details metabox
	 */
	public static function content_table( $post ) {
		wp_nonce_field( 'sportspress_save_data', 'sportspress_meta_nonce' );
        $content_table             = get_post_meta( $post->ID, 'content_table', true );
        wp_editor($content_table, 'tag_content_table', array('textarea_name' => 'content_table','editor_css' => '<style> .html-active .wp-editor-area{border:0;}</style>'));
	}

	public static function content_calendar( $post ) {
		wp_nonce_field( 'sportspress_save_data', 'sportspress_meta_nonce' );
        $content_calendar          = get_post_meta( $post->ID, 'content_calendar', true );
        wp_editor($content_calendar, 'tag_content_calendar', array('textarea_name' => 'content_calendar','editor_css' => '<style> .html-active .wp-editor-area{border:0;}</style>'));
	}

	public static function content_transfers( $post ) {
		wp_nonce_field( 'sportspress_save_data', 'sportspress_meta_nonce' );
        $content_transfers         = get_post_meta( $post->ID, 'content_transfers', true );
        wp_editor($content_transfers, 'tag_content_transfers', array('textarea_name' => 'content_transfers','editor_css' => '<style> .html-active .wp-editor-area{border:0;}</style>'));
	}	

	public static function bk_ids( $post ) {
		wp_nonce_field( 'sportspress_save_data', 'sportspress_meta_nonce' );
		foreach ( self::$bk_names as $bk => $bk_name ) {
			$value = get_post_meta( $post->ID, $bk, true );
			echo '<p><strong>' . $bk_name . '</strong></p>';
			echo '<p><input type="text" class="widefat" id="' . $bk . '" name="' . $bk . '" value="' . $value . '"></p>';
		}
  
	}

	/**
	 * Save meta boxes data
	 */
	public static function save( $post_id ) {

		$team_mce_fields = [
            'content_table',
            'content_calendar',
            'content_transfers',
        ];

        foreach ( self::$bk_names as $bk => $bk_name ) {
        	$team_mce_fields[] = $bk;
        }

        foreach ($team_mce_fields as $team_field) {
            if( isset( $_POST[ $team_field ] ) ) {
                update_post_meta( $post_id, $team_field, $_POST[ $team_field ] );
            } else {
                delete_post_meta( $post_id, $team_field );
            }
        }

	}


}

new SP_Team_Meta_Boxes();
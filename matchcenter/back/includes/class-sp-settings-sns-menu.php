<?php
/**
 * SportsPress SNS Menu Settings
 *
 * @author 		Alex Torbeev
 * @category 	Admin
 * @package 	SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SP_Settings_SNS_Menu' ) ) :

/**
 * SP_Settings_League_Menu
 */
class SP_Settings_SNS_Menu extends SP_Settings_Page {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id    = 'sns-menu';
		$this->label = __( 'Настройки SNS', 'sportspress' );

		add_filter( 'sportspress_settings_tabs_array', array( $this, 'add_settings_page' ), 10 );
		add_action( 'sportspress_settings_' . $this->id, array( $this, 'output' ) );
		//add_filter( 'sportspress_table_options', array( $this, 'table_options' ) );
		
		add_action( 'sportspress_settings_save_' . $this->id, array( $this, 'save' ) );

	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

			$settings = array_merge (
				array(
					array(
						'title' => esc_attr__( 'Настройки Матч-центра', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_options',
					),
				),
				apply_filters( 'sportspress_sns_pages_main', array() ),
				array(
					array( 'type' => 'sectionend', 'id' => 'sns_options' ),
				),
				array(
					array(
						'title' => esc_attr__( 'Настройки Прогнозов', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_predicts_options',
					),
				),
				array(
                    array(
                        'title'    => esc_attr__( 'ID изображения фона сториз по умолчанию', 'sportspress' ),
                        'id'       => 'sp_sns_predicts_story',
                        'default'  => '',
                        'type'     => 'text',
                    ),
					array(
						'title'    => esc_attr__( 'Тип поста прогнозов', 'sportspress' ),
						'id'       => 'sp_sns_predicts_post_type',
						'default'  => '',
						'type'     => 'text',
					),
					array(
						'title'    => esc_attr__( 'Метаполе типа ставки', 'sportspress' ),
						'id'       => 'sp_sns_predicts_stake_meta',
						'default'  => '',
						'type'     => 'text',
					),
					array(
						'title'    => esc_attr__( 'Метаполе названия ставки', 'sportspress' ),
						'id'       => 'sp_sns_predicts_stake_meta_2',
						'default'  => '',
						'type'     => 'text',
					),
					array(
						'title'    => esc_attr__( 'Метаполе даты матча прогноза', 'sportspress' ),
						'id'       => 'sp_sns_predicts_date_meta',
						'default'  => '',
						'type'     => 'text',
					),
					array(
						'title'    => esc_attr__( 'Метаполе вида спорта', 'sportspress' ),
						'id'       => 'sp_sns_predicts_sport_meta',
						'default'  => '',
						'type'     => 'text',
					),						
					array(
						'title'    => esc_attr__( 'Метаполе турнира', 'sportspress' ),
						'id'       => 'sp_sns_predicts_tournament_meta',
						'default'  => '',
						'type'     => 'text',
					),																							
					array(
						'title'    => esc_attr__( 'Функционал прогнозов', 'sportspress' ),
						'desc'     => esc_attr__( 'Да', 'sportspress' ),
						'id'       => 'sp_sns_predicts',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => esc_attr__( 'Таксономия видов спорта', 'sportspress' ),
						'id'       => 'sp_sns_predicts_taxonomy',
						'default'  => '',
						'type'     => 'text',
					),
					array(
						'title'    => esc_attr__( 'Таксономия турниров', 'sportspress' ),
						'id'       => 'sp_sns_predicts_taxonomy_league',
						'default'  => '',
						'type'     => 'text',
					),
					array(
						'title'    => esc_attr__( 'Таксономия видов ставок', 'sportspress' ),
						'id'       => 'sp_sns_predicts_taxonomy_stakes',
						'default'  => '',
						'type'     => 'text',
					),										
					array(
						'title'    => esc_attr__( 'Используются срезы для видов спорта', 'sportspress' ),
						'desc'     => esc_attr__( 'Да', 'sportspress' ),
						'id'       => 'sp_sns_predicts_slices',
						'default'  => 'yes',
						'type'     => 'checkbox',
					),
					array(
						'title'    => esc_attr__( 'Метаполе для срезов', 'sportspress' ),
						'id'       => 'sp_sns_predicts_slice_meta',
						'default'  => '',
						'type'     => 'text',
					),															
				),			
				array(
					array( 'type' => 'sectionend', 'id' => 'sns_predicts_options' ),
				),
				array(
					array(
						'title' => esc_attr__( 'Настройки Букмекеров', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_bk_api',
					),
					array(
						'title' => __( 'Путь партнерской ссылки', 'sportspress' ),
						'id' => 'sns_bk_path',
						'default' => '',
						'type' => 'text',
						'placeholder' => '/go/'
					),					
					array(
						'title' => __( 'Метаполе партнерской ссылки', 'sportspress' ),
						'id' => 'sns_bk_link',
						'default' => '',
						'type' => 'text',
						'placeholder' => 'bm_main_link'
					),					
					array(
						'title' => __( 'Метаполе названия букмекера', 'sportspress' ),
						'id' => 'sns_bk_name',
						'default' => '',
						'type' => 'text',
						'placeholder' => 'bm_main_name'
					),										
					array(
						'title' => __( 'ID Winline', 'sportspress' ),
						'id' => 'sns_bk_api_winline',
						'default' => '',
						'type' => 'text',
					),					
					array(
						'title' => __( 'ID Fonbet', 'sportspress' ),
						'id' => 'sns_bk_api_fonbet',
						'default' => '',
						'type' => 'text',
					),
					array(
						'title' => __( 'ID Betboom', 'sportspress' ),
						'id' => 'sns_bk_api_betboom',
						'default' => '',
						'type' => 'text',
					),					
					array(
						'title' => __( 'ID Лига Ставок', 'sportspress' ),
						'id' => 'sns_bk_api_liga',
						'default' => '',
						'type' => 'text',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'sns_bk_api',
					),														
				),																
				array(
					array(
						'title' => esc_attr__( 'Настройки Футбола', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_options_football',
					),
				),
				apply_filters( 'sportspress_sns_pages_football', array() ),
				apply_filters( 'sportspress_sns_terms_football', array() ),
				array(
					array( 'type' => 'sectionend', 'id' => 'sns_options_football' ),
				),
				array(
					array(
						'title' => esc_attr__( 'Настройки Хоккея', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_options_hockey',
					),
				),
				apply_filters( 'sportspress_sns_pages_hockey', array() ),
				apply_filters( 'sportspress_sns_terms_hockey', array() ),
				array(
					array( 'type' => 'sectionend', 'id' => 'sns_options_hockey' ),
				),
				array(
					array(
						'title' => esc_attr__( 'Настройки Баскетбола', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_options_basketball',
					),
				),
				apply_filters( 'sportspress_sns_pages_basketball', array() ),
				apply_filters( 'sportspress_sns_terms_basketball', array() ),
				array(
					array( 'type' => 'sectionend', 'id' => 'sns_options_basketball' ),
				),
				array(
					array(
						'title' => esc_attr__( 'Настройки Тенниса', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_options_tennis',
					),
				),
				apply_filters( 'sportspress_sns_pages_tennis', array() ),
				apply_filters( 'sportspress_sns_terms_tennis', array() ),
				array(
					array( 'type' => 'sectionend', 'id' => 'sns_options_tennis' ),
				),										
				array(
					array(
						'title' => esc_attr__( 'Метаполя Команды (можно использовать [team], [season])', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_team_seo',
					),
					array(
						'title' => __( 'Обзор Title', 'sportspress' ),
						'id' => 'sns_team_title_main',
						'default' => '',
						'type' => 'text',
					),					
					array(
						'title' => __( 'Обзор Description', 'sportspress' ),
						'id' => 'sns_team_description_main',
						'default' => '',
						'type' => 'text',
					),
					array(
						'title' => __( 'Таблицы Title', 'sportspress' ),
						'id' => 'sns_team_title_table',
						'default' => '',
						'type' => 'text',
					),					
					array(
						'title' => __( 'Таблицы Description', 'sportspress' ),
						'id' => 'sns_team_description_table',
						'default' => '',
						'type' => 'text',
					),
					array(
						'title' => __( 'Календарь Title', 'sportspress' ),
						'id' => 'sns_team_title_calendar',
						'default' => '',
						'type' => 'text',
					),					
					array(
						'title' => __( 'Календарь Description', 'sportspress' ),
						'id' => 'sns_team_description_calendar',
						'default' => '',
						'type' => 'text',
					),
					array(
						'title' => __( 'Трансферы Title', 'sportspress' ),
						'id' => 'sns_team_title_transfers',
						'default' => '',
						'type' => 'text',
					),					
					array(
						'title' => __( 'Трансферы Description', 'sportspress' ),
						'id' => 'sns_team_description_transfers',
						'default' => '',
						'type' => 'text',
					),										
					array(
						'type' => 'sectionend',
						'id'   => 'sns_team_seo',
					),														
				),
				array(
					array(
						'title' => esc_attr__( 'Метаполя Матча (можно использовать [team1], [team2], [date], [league], [season])', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_event_seo',
					),
					array(
						'title' => __( 'Обзор Title', 'sportspress' ),
						'id' => 'sns_event_title_main',
						'default' => '',
						'type' => 'text',
					),					
					array(
						'title' => __( 'Обзор Description', 'sportspress' ),
						'id' => 'sns_event_description_main',
						'default' => '',
						'type' => 'text',
					),
					array(
						'title' => __( 'Прогноз Title', 'sportspress' ),
						'id' => 'sns_event_title_predict',
						'default' => '',
						'type' => 'text',
					),					
					array(
						'title' => __( 'Прогноз Description', 'sportspress' ),
						'id' => 'sns_event_description_predict',
						'default' => '',
						'type' => 'text',
					),
								
					array(
						'type' => 'sectionend',
						'id'   => 'sns_event_seo',
					),														
				),							
				array(
					array(
						'title' => esc_attr__( 'Настройки Sports API', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_sports_api',
					),
					array(
						'title' => __( 'Токен', 'sportspress' ),
						'id' => 'sns_sports_api_token',
						'default' => '',
						'type' => 'text',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'sns_sports_api',
					),														
				),
							

			);

			return apply_filters( 'sportspress_sns_settings', $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();
		SP_Admin_Settings::save_fields( $settings );
	}


}

endif;

return new SP_Settings_SNS_Menu();

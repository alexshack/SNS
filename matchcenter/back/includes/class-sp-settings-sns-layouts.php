<?php
/**
 * SportsPress SNS Layouts Settings
 *
 * @author      Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SP_Settings_SNS_Layouts' ) ) :

	/**
	 * SP_Settings_SNS_Layouts
	 */
	class SP_Settings_SNS_Layouts extends SP_Settings_Page {

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->id       = 'sns-layouts';
			$this->label    = esc_attr__( 'Шаблоны SNS', 'sportspress' );
			//$this->template = 'sns_layouts';

			add_filter( 'sportspress_settings_tabs_array', array( $this, 'add_settings_page' ), 10 );
			add_action( 'sportspress_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'sportspress_admin_field_sns_layouts_football_sport', array( $this, 'layouts_football_sport' ) );
			add_action( 'sportspress_admin_field_sns_layouts_football_league', array( $this, 'layouts_football_league' ) );
			add_action( 'sportspress_admin_field_sns_layouts_football_team', array( $this, 'layouts_football_team' ) );
			add_action( 'sportspress_admin_field_sns_layouts_hockey_sport', array( $this, 'layouts_hockey_sport' ) );
			add_action( 'sportspress_admin_field_sns_layouts_hockey_league', array( $this, 'layouts_hockey_league' ) );
			add_action( 'sportspress_admin_field_sns_layouts_hockey_team', array( $this, 'layouts_hockey_team' ) );	
			add_action( 'sportspress_admin_field_sns_layouts_basketball_sport', array( $this, 'layouts_basketball_sport' ) );
			add_action( 'sportspress_admin_field_sns_layouts_basketball_league', array( $this, 'layouts_basketball_league' ) );
			add_action( 'sportspress_admin_field_sns_layouts_basketball_team', array( $this, 'layouts_basketball_team' ) );
			add_action( 'sportspress_admin_field_sns_layouts_tennis_sport', array( $this, 'layouts_tennis_sport' ) );
			add_action( 'sportspress_admin_field_sns_layouts_tennis_league', array( $this, 'layouts_tennis_league' ) );
			add_action( 'sportspress_admin_field_sns_layouts_tennis_team', array( $this, 'layouts_tennis_team' ) );										
			add_action( 'sportspress_settings_save_' . $this->id, array( $this, 'save' ) );
		}

		/**
		 * Get settings array
		 *
		 * @return array
		 */
		public function get_settings() {

			$settings = array_merge(
				array(
					array(
						'title' => esc_attr__( 'Футбол', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_layouts_football',
					),
					array( 'type' => 'sns_layouts_football_sport' ),
					array( 'type' => 'sns_layouts_football_league' ),
					array( 'type' => 'sns_layouts_football_team' ),
					array( 
						'type' => 'sectionend', 
						'id' => 'sns_layouts_football' 
					),
				),
				array(
					array(
						'title' => esc_attr__( 'Хоккей', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_layouts_hockey',
					),
					array( 'type' => 'sns_layouts_hockey_sport' ),
					array( 'type' => 'sns_layouts_hockey_league' ),
					array( 'type' => 'sns_layouts_hockey_team' ),
					array( 
						'type' => 'sectionend', 
						'id' => 'sns_layouts_hockey' 
					),
				),
				array(
					array(
						'title' => esc_attr__( 'Баскетбол', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_layouts_basketball',
					),
					array( 'type' => 'sns_layouts_basketball_sport' ),
					array( 'type' => 'sns_layouts_basketball_league' ),
					array( 'type' => 'sns_layouts_basketball_team' ),
					array( 
						'type' => 'sectionend', 
						'id' => 'sns_layouts_basketball' 
					),
				),
				array(
					array(
						'title' => esc_attr__( 'Теннис', 'sportspress' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'sns_layouts_tennis',
					),
					array( 'type' => 'sns_layouts_tennis_sport' ),
					array( 'type' => 'sns_layouts_tennis_league' ),
					array( 'type' => 'sns_layouts_tennis_team' ),
					array( 
						'type' => 'sectionend', 
						'id' => 'sns_layouts_tennis' 
					),
				),									
			);

			return apply_filters( 'sportspress_sns_layouts', $settings );
		}

		/**
		 * Save settings
		 */
		public function save() {
			//parent::save();
			update_option( 'sportspress_football_sport_template_order', sp_array_value( $_POST, 'sportspress_football_sport_template_order', false, 'key' ) );
			update_option( 'sportspress_football_league_template_order', sp_array_value( $_POST, 'sportspress_football_league_template_order', false, 'key' ) );
			update_option( 'sportspress_football_team_template_order', sp_array_value( $_POST, 'sportspress_football_team_template_order', false, 'key' ) );
			update_option( 'sportspress_hockey_sport_template_order', sp_array_value( $_POST, 'sportspress_hockey_sport_template_order', false, 'key' ) );
			update_option( 'sportspress_hockey_league_template_order', sp_array_value( $_POST, 'sportspress_hockey_league_template_order', false, 'key' ) );
			update_option( 'sportspress_hockey_team_template_order', sp_array_value( $_POST, 'sportspress_hockey_team_template_order', false, 'key' ) );
			update_option( 'sportspress_basketball_sport_template_order', sp_array_value( $_POST, 'sportspress_basketball_sport_template_order', false, 'key' ) );
			update_option( 'sportspress_basketball_league_template_order', sp_array_value( $_POST, 'sportspress_basketball_league_template_order', false, 'key' ) );
			update_option( 'sportspress_basketball_team_template_order', sp_array_value( $_POST, 'sportspress_basketball_team_template_order', false, 'key' ) );			
			update_option( 'sportspress_tennis_sport_template_order', sp_array_value( $_POST, 'sportspress_tennis_sport_template_order', false, 'key' ) );
			update_option( 'sportspress_tennis_league_template_order', sp_array_value( $_POST, 'sportspress_tennis_league_template_order', false, 'key' ) );
			update_option( 'sportspress_tennis_team_template_order', sp_array_value( $_POST, 'sportspress_tennis_team_template_order', false, 'key' ) );

			if ( isset( $_POST['sportspress_template_visibility'] ) && is_array( $_POST['sportspress_template_visibility'] ) ) {
				foreach ( $_POST['sportspress_template_visibility'] as $option => $toggled ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
					if ( $toggled ) {
						update_option( $option, 'yes' );
					} else {
						update_option( $option, 'no' );
					}
				}
			}

		}

		public function layouts_football_sport() {
			$templates = $this->sns_templates( 'football', 'sport' );
			return $this->get_layout( 'football_sport', $templates, 'sport' );
		}

		public function layouts_football_league() {
			$templates = $this->sns_templates( 'football', 'league' );
			return $this->get_layout( 'football_league', $templates, 'league' );
		}

		public function layouts_football_team() {
			$templates = $this->sns_templates( 'football', 'team' );
			return $this->get_layout( 'football_team', $templates, 'team' );
		}		

		public function layouts_hockey_sport() {
			$templates = $this->sns_templates( 'hockey', 'sport' );
			return $this->get_layout( 'hockey_sport', $templates, 'sport' );
		}

		public function layouts_hockey_league() {
			$templates = $this->sns_templates( 'hockey', 'league' );
			return $this->get_layout( 'hockey_league', $templates, 'league' );
		}

		public function layouts_hockey_team() {
			$templates = $this->sns_templates( 'hockey', 'team' );
			return $this->get_layout( 'hockey_team', $templates, 'team' );
		}

		public function layouts_basketball_sport() {
			$templates = $this->sns_templates( 'basketball', 'sport' );
			return $this->get_layout( 'basketball_sport', $templates, 'sport' );
		}

		public function layouts_basketball_league() {
			$templates = $this->sns_templates( 'basketball', 'league' );
			return $this->get_layout( 'basketball_league', $templates, 'league' );
		}

		public function layouts_basketball_team() {
			$templates = $this->sns_templates( 'basketball', 'team' );
			return $this->get_layout( 'basketball_team', $templates, 'team' );
		}

		public function layouts_tennis_sport() {
			$templates = $this->sns_templates( 'tennis', 'sport' );
			return $this->get_layout( 'tennis_sport', $templates, 'sport' );
		}

		public function layouts_tennis_league() {
			$templates = $this->sns_templates( 'tennis', 'league' );
			return $this->get_layout( 'tennis_league', $templates, 'league' );
		}

		public function layouts_tennis_team() {
			$templates = $this->sns_templates( 'tennis', 'team' );
			return $this->get_layout( 'tennis_team', $templates, 'team' );
		}

		public function get_layout( $type, $templates, $page ) {
	
			switch ( $page ) {
				case 'sport':
					$title = 'Страница вида спорта';
					break;
				case 'league':
					$title = 'Страница лиги';
					break;
				case 'team':
					$title = 'Страница команды';
					break;									
				default:
					$title = '';
					break;
			}


			$layout = get_option( 'sportspress_' . $type . '_template_order' );
			if ( false === $layout ) {
				$layout = array_keys( $templates );
			}

			$templates = array_merge( array_flip( (array) $layout ), $templates );

			?>
		<tr valign="top">
			<th>
				<?php echo $title; ?>
			</th>
			<td class="sp-sortable-list-container">
				
				<ul class="sp-layout sp-sortable-list sp-connected-list ui-sortable">
					<?php
					foreach ( $templates as $template => $details ) :
						if ( ! is_array( $details ) ) {
							continue;
						}
						$option     = sp_array_value( $details, 'option', 'sportspress' . $type . '_show_' . $template );
						$visibility = get_option( $option, sp_array_value( $details, 'default', 'yes' ) );
						?>
						<li>
							<div class="sp-item-bar sp-layout-item-bar">
								<div class="sp-item-handle sp-layout-item-handle ui-sortable-handle">
									<span class="sp-item-title item-title"><?php echo esc_html( sp_array_value( $details, 'title', ucfirst( $template ) ) ); ?></span>
									<input type="hidden" name="sportspress_<?php echo esc_attr( $type ); ?>_template_order[]" value="<?php echo esc_attr( $template ); ?>">
								</div>
								
								<input type="hidden" name="sportspress_template_visibility[<?php echo esc_attr( $option ); ?>]" value="0">
								<input class="sp-toggle-switch" type="checkbox" name="sportspress_template_visibility[<?php echo esc_attr( $option ); ?>]" id="<?php echo esc_attr( $option ); ?>" value="1" <?php checked( $visibility, 'yes' ); ?>>
								<label for="sportspress_<?php echo esc_attr( $type ); ?>_show_<?php echo esc_attr( $template ); ?>"></label>
							</div>
						</li>
					<?php endforeach; ?>
				 </ul>
			</td>
		</tr>
			<?php			
		}

		public function sns_templates( $sport, $page ) {

			$templates = [];

			if ( $page == 'sport' || $page == 'team' ) {
				$templates['leagues'] = array(
						'title'   => esc_attr__( 'Список лиг', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_' . $page . '_show_leagues',
						'action'  => 'sportspress_output_' . $sport . '_' . $page . '_leagues',
						'default' => 'yes',
					);
			}

			if ( $page == 'league' ) {
				$templates['teams'] = array(
						'title'   => esc_attr__( 'Список команд', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_league_show_teams',
						'action'  => 'sportspress_output_' . $sport . '_league_teams',
						'default' => 'yes',
					);
			}

			$templates = array_merge( $templates, array (
					'events'      => array(
						'title'   => esc_attr__( 'Матчи', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_' . $page . '_show_events',
						'action'  => 'sportspress_output_' . $sport . '_' . $page . '_events',
						'default' => 'yes',
					),
					'predicts'    => array(
						'title'   => esc_attr__( 'Прогнозы', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_' . $page . '_show_predicts',
						'action'  => 'sportspress_output_' . $sport . '_' . $page . '_predicts',
						'default' => 'yes',
					),
					'news'    => array(
						'title'   => esc_attr__( 'Новости', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_' . $page . '_show_news',
						'action'  => 'sportspress_output_' . $sport . '_' . $page . '_news',
						'default' => 'yes',
					),
					'bonuses'     => array(
						'title'   => esc_attr__( 'Бонусы', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_' . $page . '_show_bonuses',
						'action'  => 'sportspress_output_' . $sport . '_' . $page . '_bonuses',
						'default' => 'yes',
					),
					'content'     => array(
						'title'   => esc_attr__( 'Контент', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_' . $page . '_show_content',
						'action'  => 'sportspress_output_' . $sport . '_' . $page . '_content',
						'default' => 'yes',
					),																													
				),
			);

			if ( $page == 'league' || $page == 'team' ) {
				$templates['tables'] = array(
						'title'   => esc_attr__( 'Турнирные таблицы', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_' . $page . '_show_tables',
						'action'  => 'sportspress_output_' . $sport . '_' . $page . '_tables',
						'default' => 'yes',
					);
			}

			if ( $page == 'league' || $page == 'sport' ) {
				$templates['articles'] = array(
						'title'   => esc_attr__( 'Статьи', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_' . $page . '_show_articles',
						'action'  => 'sportspress_output_' . $sport . '_' . $page . '_articles',
						'default' => 'yes',
					);
			}

			if ( $sport == 'football' ) {
				$templates['transfers'] = array(
						'title'   => esc_attr__( 'Трансферы', 'sportspress' ),
						'option'  => 'sportspress_' . $sport . '_' . $page . '_show_transfers',
						'action'  => 'sportspress_output_' . $sport . '_' . $page . '_transfers',
						'default' => 'yes',
					);
			}

			return $templates;

		}

	}

endif;

return new SP_Settings_SNS_Layouts();

/*				apply_filters( 'sportspress_sns_template_options',
					array(
						array( 'type' => 'event_layout' ),
					)
				),*/
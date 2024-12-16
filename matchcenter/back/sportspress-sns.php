<?php
/*
Plugin Name: SportsPress SNS
Plugin URI: 
Description: Adds SNS functions to SportsPress.
Author: Alex Torbeev
Author URI: 
Version: 1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'SportsPress_SNS' ) ) :

/**
 * Main SportsPress SNS Class
 *
 * @class SportsPress_SNS
 * @version	1.0.0
 */
class SportsPress_SNS {

	static $main_page_slug, $transfers_page_slug, $predicts_funcs;
	static $league_tabs = [
		'table',
		'calendar',
		'statistics',
		'transfers',
	];
	static $team_tabs = [
		'squad',
		'table',
		'calendar',
		'squad',
		'transfers',
	];	
	static $game_tabs = [
		'predict',
	];
	/**
	 * Constructor
	 */
	public function __construct() {
		// Define constants
		$this->define_constants();
		self::$main_page_slug      = get_option('sportspress_sns_main_page', '');
		self::$transfers_page_slug = get_option('sportspress_sns_transfers_page', '');
		self::$predicts_funcs      = get_option('sp_sns_predicts', '') == 'yes';

		// Include required files
		$this->includes();

		// Hooks
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_activation_hook( __FILE__, array( $this, 'cron_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'cron_deactivate' ) );
		new SP_SNS_Cron( [
			'id' => 'sns_cron_jobs',
			'auto_activate' => false, 
			'events' => [
				'sns_cron_func_events' => [
					'callback'      => [ SP_SNS_Cron_Functions::class, 'cronEvents' ],
					'interval_name' => '30 minutes',
				],
				'sns_cron_func_fixtures' => [
					'callback'      => [ SP_SNS_Cron_Functions::class, 'cronFixtures' ],
					'interval_name' => 'twicedaily',
				],
				'sns_cron_func_squad' => [
					'callback'      => [ SP_SNS_Cron_Functions::class, 'cronSquad' ],
					'interval_name' => 'daily',
				],				
				'sns_cron_func_coef' => [
					'callback'      => [ SP_SNS_Cron_Functions::class, 'cronCoef' ],
					'interval_name' => '20 minutes',
				],
				'sns_cron_func_league' => [
					'callback'      => [ SP_SNS_Cron_Functions::class, 'cronLeague' ],
					'interval_name' => '2 days',
				],
				'sns_cron_func_transfers' => [
					'callback'      => [ SP_SNS_Cron_Functions::class, 'cronTransfers' ],
					'interval_name' => '10 days',
				],												
			],
		] );		
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'sportspress_include_post_type_handlers', array( $this, 'include_post_type_handlers' ) );
		add_filter( 'sportspress_permalink_slugs', array( $this, 'add_permalink_slug' ) );
		add_filter( 'sportspress_post_types', array( $this, 'add_post_type' ) );
		add_filter( 'sportspress_importable_post_types', array( $this, 'add_post_type' ) );
		add_filter( 'sportspress_taxonomies', array( $this, 'add_taxonomy' ) );
		add_filter( 'sportspress_get_settings_pages', array( $this, 'add_settings_page' ) );
		add_filter( 'the_posts', array( $this, 'display_scheduled_transfers' ) );

		add_filter( 'post_type_link', array( $this, 'post_types_link' ), 10, 2 );
		add_filter( 'term_link', array( $this, 'terms_link' ), 10, 3 );

		add_filter( 'sportspress_screen_ids', array( $this, 'add_screen_ids' ) );
		add_filter( 'sportspress_league_object_types', array( $this, 'add_taxonomy_object' ) );
		add_filter( 'sportspress_season_object_types', array( $this, 'add_taxonomy_object' ) );

		add_filter( 'sportspress_menu_items', array( $this, 'add_menu_item' ), 30 );
		add_filter( 'sportspress_team_access_post_types', array( $this, 'add_post_type' ) );

		
		add_filter( 'sportspress_register_taxonomy_season', array( $this, 'change_season_register' ) );
		add_filter( 'sportspress_register_post_type_player', array( $this, 'change_player_register' ) );
		add_filter( 'sportspress_register_post_type_team', array( $this, 'change_team_register' ) );

		//add_filter( 'sportspress_get_presets', array( $this, 'change_presets' ) );

		add_filter( 'admin_print_footer_scripts', array( $this, 'action_links' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'get_header', array( $this, 'frontend_enqueue_styles_header' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_styles_footer' ) );

		if ( defined( 'SP_PRO_PLUGIN_FILE' ) ) {
			register_activation_hook( SP_PRO_PLUGIN_FILE, array( $this, 'install' ) );
			register_activation_hook( SP_PRO_PLUGIN_FILE, array( $this, 'cron_activate' ) );
			register_deactivation_hook( SP_PRO_PLUGIN_FILE, array( $this, 'cron_deactivate' ) );			
		}


		flush_rewrite_rules();
	}

	/**
	 * Define constants.
	 */
	private function define_constants() {
		if ( !defined( 'SP_SNS_VERSION' ) )
			define( 'SP_SNS_VERSION', '1.0.0' );

		if ( !defined( 'SP_SNS_URL' ) )
			define( 'SP_SNS_URL', plugin_dir_url( __FILE__ ) );

		if ( !defined( 'SP_SNS_DIR' ) )
			define( 'SP_SNS_DIR', plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		include_once 'includes/api/class-sp-sns-api.php';
		include_once 'includes/api/class-sp-sns-api-winline.php';
		include_once 'includes/api/class-sp-sns-api-betboom.php';
		include_once 'includes/api/class-sp-sns-api-fonbet.php';

		require_once 'includes/loaders/class-sp-loader-functions.php';
		require_once 'includes/loaders/class-sp-loader-event-football.php';
		require_once 'includes/loaders/class-sp-loader-event-hockey.php';
		require_once 'includes/loaders/class-sp-loader-event-basketball.php';
		require_once 'includes/loaders/class-sp-loader-event-tennis.php';
		require_once 'includes/loaders/class-sp-loader-event-titles.php';
		require_once 'includes/loaders/class-sp-loader-event-stages.php';
		require_once 'includes/loaders/class-sp-loader-fixture-football.php';
		require_once 'includes/loaders/class-sp-loader-transfer-football.php';
		require_once 'includes/loaders/class-sp-loader-player-football.php';
		require_once 'includes/loaders/class-sp-loader-predictions-football.php';		

		require_once 'includes/class-sp-sns-cron.php';
		require_once 'includes/class-sp-sns-cron-functions.php';

		require_once 'includes/class-sp-sns-ajax-rest.php';
		include_once 'includes/modules/sportspress-sns-ajax.php';
		
		include_once 'includes/classes/class-sp-sns-bookmaker.php';
		include_once 'includes/classes/class-sp-sns-sport.php';
		include_once 'includes/classes/class-sp-sns-season.php';
		include_once 'includes/classes/class-sp-sns-stage.php';
		include_once 'includes/classes/class-sp-sns-league.php';
		include_once 'includes/classes/class-sp-sns-transfer.php';
		include_once 'includes/classes/class-sp-sns-event.php';
		include_once 'includes/classes/class-sp-sns-team.php';
		include_once 'includes/classes/class-sp-sns-predict.php';

		include_once 'includes/modules/sportspress-sns-theme.php';
		include_once 'includes/modules/sportspress-sns-seo.php';
		include_once 'includes/modules/sportspress-sns-breadcrumbs.php';

		if ( is_admin() ) {
			include_once 'includes/class-sp-admin-loaders.php';
			include_once 'includes/modules/sportspress-page-selector.php';
			include_once 'includes/modules/sportspress-term-selector.php';
			if ( ! class_exists( 'WP_Term_Image' ) ) {
				require_once 'includes/class-sp-terms-images.php';
				add_action( 'admin_init', 'WP_Term_Image::init' );
			}
		}
	}

	public function cron_activate() {
		SP_SNS_Cron::get( 'sns_cron_jobs' )->activate();
	}

	public function cron_deactivate() {
		SP_SNS_Cron::get( 'sns_cron_jobs' )->deactivate();
	}

	public function add_settings_page( $settings = array() ) {
		$settings[] = include( 'includes/class-sp-settings-sns-menu.php' );
		$settings[] = include( 'includes/class-sp-settings-sns-layouts.php' );
		return $settings;
	}

	/**
	 * Init plugin when WordPress Initialises.
	 */
	public function init() {
		// Register post type
		$this->register_post_type();
		$this->register_taxonomy();


		foreach (self::$league_tabs as $slug) {
			add_rewrite_rule('tournament-(.+?)/season-(.+?)/' . $slug  .'/?$', 'index.php?sp_league=$matches[1]&season=$matches[2]&tab=' . $slug, 'top');
		}		
		foreach (self::$league_tabs as $slug) {
			add_rewrite_rule('tournament-(.+?)/' . $slug  .'/?$', 'index.php?sp_league=$matches[1]&tab=' . $slug, 'top');
		}
		add_rewrite_rule('tournament-(.+?)/season-(.+?)/?$', 'index.php?sp_league=$matches[1]&season=$matches[2]',	'top');
		add_rewrite_rule('tournament-(.+?)/?$', 'index.php?sp_league=$matches[1]',	'top');	

		foreach (self::$team_tabs as $slug) {
			add_rewrite_rule('team-(.+?)/season-(.+?)/' . $slug  .'/?$', 'index.php?sp_team=$matches[1]&season=$matches[2]&tab=' . $slug, 'top');
		}
		foreach (self::$team_tabs as $slug) {
			add_rewrite_rule('team-(.+?)/' . $slug  .'/?$', 'index.php?sp_team=$matches[1]&tab=' . $slug, 'top');
		}
		add_rewrite_rule('team-(.+?)/season-(.+?)/?$', 'index.php?sp_team=$matches[1]&season=$matches[2]',	'top');
		add_rewrite_rule('team-(.+?)/?$', 'index.php?sp_team=$matches[1]',	'top');

		foreach (self::$game_tabs as $slug) {
			add_rewrite_rule('game-(.+?)/' . $slug  .'/?$', 'index.php?sp_event=$matches[1]&tab=' . $slug, 'top');
		}		
		add_rewrite_rule('game-(.+?)/?$', 'index.php?sp_event=$matches[1]',	'top');
		add_rewrite_rule('football/(.+?)/?$', 'index.php?pagename=football&sport_date=$matches[1]',	'top');
		add_rewrite_rule('hockey/(.+?)/?$', 'index.php?pagename=hockey&sport_date=$matches[1]',	'top');	
		add_rewrite_rule('basketball/(.+?)/?$', 'index.php?pagename=basketball&sport_date=$matches[1]',	'top');	
		add_rewrite_rule('tennis/(.+?)/?$', 'index.php?pagename=tennis&sport_date=$matches[1]',	'top');		


		add_filter('query_vars', function ($vars) {
			$vars[] = 'season';
			$vars[] = 'tab';
			$vars[] = 'sport_date';
			return $vars;
		});

		flush_rewrite_rules();

	}

	public function post_types_link($post_link, $post) {
		
		if ($post->post_type === 'sp_team' ) {
			return str_replace( '/' . 'team' . '/', '/team-', $post_link );
		}

		if ($post->post_type === 'sp_event' ) {
			return str_replace( '/' . 'event' . '/', '/game-', $post_link );
		}		

		return $post_link;

	}

	public function terms_link( $url, $term, $taxonomy ) {
		
		if ($taxonomy === 'sp_league' ) {
			return str_replace( '/' . 'league' . '/', '/tournament-', $url );
		}

		return $url;

	    flush_rewrite_rules();
	}


	public function register_taxonomy() {
		$labels       = array(
			'name'              => esc_attr__( 'Типы трансфера', 'sportspress' ),
			'singular_name'     => esc_attr__( 'Тип трансфера', 'sportspress' ),
			'all_items'         => esc_attr__( 'Все', 'sportspress' ),
			'edit_item'         => esc_attr__( 'Edit', 'sportspress' ),
			'view_item'         => esc_attr__( 'View', 'sportspress' ),
			'update_item'       => esc_attr__( 'Update', 'sportspress' ),
			'add_new_item'      => esc_attr__( 'Add New', 'sportspress' ),
			'new_item_name'     => esc_attr__( 'Name', 'sportspress' ),
			'parent_item'       => esc_attr__( 'Parent', 'sportspress' ),
			'parent_item_colon' => esc_attr__( 'Parent:', 'sportspress' ),
			'search_items'      => esc_attr__( 'Search', 'sportspress' ),
			'not_found'         => esc_attr__( 'No results found.', 'sportspress' ),
		);
		$args         =  array(
			'label'                 => esc_attr__( 'Типы трансфера', 'sportspress' ),
			'labels'                => $labels,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => false,
			'show_tagcloud'         => false,
			'hierarchical'          => false,
		);
		$object_types = array( 'sp_transfer' ) ;
		register_taxonomy( 'sp_transfer_type', $object_types, $args );

		$labels       = array(
			'name'              => esc_attr__( 'Стадии', 'sportspress' ),
			'singular_name'     => esc_attr__( 'Стадия', 'sportspress' ),
			'all_items'         => esc_attr__( 'Все', 'sportspress' ),
			'edit_item'         => esc_attr__( 'Edit', 'sportspress' ),
			'view_item'         => esc_attr__( 'View', 'sportspress' ),
			'update_item'       => esc_attr__( 'Update', 'sportspress' ),
			'add_new_item'      => esc_attr__( 'Add New', 'sportspress' ),
			'new_item_name'     => esc_attr__( 'Name', 'sportspress' ),
			'parent_item'       => esc_attr__( 'Parent', 'sportspress' ),
			'parent_item_colon' => esc_attr__( 'Parent:', 'sportspress' ),
			'search_items'      => esc_attr__( 'Search', 'sportspress' ),
			'not_found'         => esc_attr__( 'No results found.', 'sportspress' ),
		);
		$args         =  array(
			'label'                 => esc_attr__( 'Стадии', 'sportspress' ),
			'labels'                => $labels,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => false,
			'show_tagcloud'         => false,
			'hierarchical'          => true,
		);
		$object_types = array( 'sp_event' ) ;
		register_taxonomy( 'sp_stage', $object_types, $args );
	}

	public function register_post_type() {
		register_post_type( 'sp_transfer',
			apply_filters( 'sportspress_register_post_type_transfer',
				array(
					'labels'                => array(
						'name'               => esc_attr__( 'Трансферы', 'sportspress' ),
						'singular_name'      => esc_attr__( 'Трансфер', 'sportspress' ),
						'add_new_item'       => esc_attr__( 'Добавить трансфер', 'sportspress' ),
						'edit_item'          => esc_attr__( 'Редактировать трансфер', 'sportspress' ),
						'new_item'           => esc_attr__( 'Новый', 'sportspress' ),
						'view_item'          => esc_attr__( 'Смотреть трансфер', 'sportspress' ),
						'search_items'       => esc_attr__( 'Искать', 'sportspress' ),
						'not_found'          => esc_attr__( 'No results found.', 'sportspress' ),
						'not_found_in_trash' => esc_attr__( 'No results found.', 'sportspress' ),
					),
					'public'                => false,
					'show_ui'               => true,
					'capability_type'       => 'sp_transfer',
					'map_meta_cap'          => true,
					'publicly_queryable'    => false,
					'exclude_from_search'   => true,
					'hierarchical'          => false,
					'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
					'has_archive'           => false,
					'show_in_nav_menus'     => true,
					'menu_icon'             => 'dashicons-leftright',
				)
			)
		);
	}

	public function display_scheduled_transfers( $posts ) {
		global $wp_query, $wpdb;
		if ( is_single() && $wp_query->post_count == 0 && isset( $wp_query->query_vars['sp_transfer'] ) ) {
			$posts = $wpdb->get_results( $wp_query->request );
		}
		return $posts;
	}


	public static function add_post_type( $post_types = array() ) {
		$post_types[] = 'sp_transfer';
		return $post_types;
	}

	public static function add_taxonomy( $taxonomies = array() ) {
		$taxonomies[] = 'sp_transfer_type';
		$taxonomies[] = 'sp_stage';
		return $taxonomies;
	}	

	public static function add_screen_ids( $screen_ids = array() ) {
		$screen_ids[] = 'edit-sp_transfer';
		$screen_ids[] = 'sp_transfer';
		$screen_ids[] = 'admin_page_sp_event_sports';
		$screen_ids[] = 'admin_page_sp_team_sports';
		$screen_ids[] = 'admin_page_sp_player_sports';
		$screen_ids[] = 'admin_page_sp_transfer_sports';
		$screen_ids[] = 'edit-sp_stage';

		return $screen_ids;
	}


	public function add_taxonomy_object( $object_types ) {
		$object_types[] = 'sp_transfer';
		$object_types[] = 'post';
		//if( post_type_exists( 'predicts' ) ){
			$object_types[] = 'predicts';
		//}
		return $object_types;
	}

	public static function change_season_register( $args ) {
		$args['public'] = false; 
		return $args;
	}

	public static function change_team_register( $args ) {
		$args['has_archive'] = false; 
		return $args;
	}

	public static function change_player_register( $args ) {
		$args['public'] = false; 
		$args['has_archive'] = false; 
		return $args;
	}	

	public function include_post_type_handlers() {
		include_once( 'includes/class-sp-transfer-meta-boxes.php' );
		include_once( 'includes/class-sp-team-meta-boxes.php' );
		include_once( 'includes/class-sp-admin-cpt-transfer.php' );
		include_once( 'includes/class-sp-admin-cpt-stage.php' );
		include_once( 'includes/class-sp-sns-post-types-meta-boxes.php' );
		include_once( 'includes/class-sp-sns-predicts-meta-boxes.php' );
		include_once( 'includes/class-sp-sns-league-meta-boxes.php' );
		include_once( 'includes/class-sp-sns-season-meta-boxes.php' );
		include_once( 'includes/class-sp-sns-stage-meta-boxes.php' );
		new SP_SNS_Post_Types_Meta_Boxes(['post']);
		new SP_SNS_Predicts_Meta_Boxes(['predicts']);
		new SP_SNS_League_Meta_Boxes();
		new SP_SNS_Season_Meta_Boxes();
		new SP_SNS_Stage_Meta_Boxes();
		if ( self::$predicts_funcs ) {
			include_once( 'includes/class-sp-sns-bets-meta-boxes.php' );
			new SP_SNS_Bets_Meta_Boxes();
		}
	}


	public function add_permalink_slug( $slugs ) {
		$slugs[] = array( 'transfer', __( 'Transfers', 'sportspress' ) );
		return $slugs;
	}


	public function install() {
		$this->add_capabilities();
		$this->register_post_type();
		$this->register_taxonomy();
		$this->add_transfer_types();
		$this->add_seasons();
		//$this->add_leagues();

		// Update version
		update_option( 'sportspress_sns_version', SP_SNS_VERSION );

		// Flush rules after install
		flush_rewrite_rules();
	}


	public function add_transfer_types() {
		$types = [
			'Возврат из аренды' => 'back',
			'Аренда'            => 'loan',
			'Свободный агент'   => 'free',
			'Продажа'           => 'sale',
			'Обмен'             => 'swap'
		];
		foreach ($types as $name => $type) {
			wp_insert_term( $name, 'sp_transfer_type', [ 'slug' => $type ] );
		}

	}

	public function add_seasons() {
		wp_insert_term('Сезон 2023/2024', 'sp_season', ['slug' => '2023']);
	}

	public function add_leagues() {
		$types = [
			'Premier League'          => '39',
			'Ligue 1'                 => '61',
			'Bundesliga'              => '78',
			'Serie A'                 => '135',
			'La Liga'                 => '140',
			'Российская Премьер-Лига' => '235'
		];
		foreach ($types as $name => $type) {
			$league = wp_insert_term($name, 'sp_league');
			if ($league) {
				update_term_meta( $league['term_id'], 'sp_order', $type );
			}
		}

	}

	public function change_presets($presets) {
		$presets['columns'][1]['name'] = 'В';
	}		

	public function add_capabilities() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ):
			if ( ! isset( $wp_roles ) ):
				$wp_roles = new WP_Roles();
			endif;
		endif;

		if ( is_object( $wp_roles ) ):
			$capability_type = 'sp_transfer';
			$capabilities = array(
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_published_{$capability_type}s",
				"assign_{$capability_type}_terms",
			);

			foreach ( $capabilities as $cap ):
				$wp_roles->add_cap( 'sp_event_manager', $cap );
				$wp_roles->add_cap( 'sp_team_manager', $cap );
			endforeach;

			$capabilities = array_merge( $capabilities, array(
				"delete_{$capability_type}",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
			));

			foreach ( $capabilities as $cap ):
				$wp_roles->add_cap( 'sp_league_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			endforeach;
		endif;
	}

	/**
	 * Add menu item
	 */
	public function add_menu_item( $items ) {
		$items[] = 'edit.php?post_type=sp_transfer';
		return $items;
	}


	/**
	 * Enqueue styles
	 */
	public function admin_enqueue_styles() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'admin_page_sp_event_sports', 'admin_page_sp_team_sports', 'admin_page_sp_player_sports', 'admin_page_sp_transfer_sports', 'admin_page_sp_fixtures_sports') ) ) {
			wp_enqueue_style( 'sportspress-sns-admin', SP_SNS_URL . 'css/admin.css', array(), SP_SNS_VERSION );
		}
	}

	/**
	 * Enqueue scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();

		wp_register_script( 'sportspress-sns-loader', SP_SNS_URL . 'js/admin.js', array(), SP_SNS_VERSION, true );

		if ( in_array( $screen->id, array( 'admin_page_sp_event_sports', 'admin_page_sp_team_sports', 'admin_page_sp_player_sports', 'admin_page_sp_transfer_sports', 'admin_page_sp_fixtures_sports') ) ) {

			wp_enqueue_script( 'sportspress-sns-loader' );
		}
	}

	public function frontend_enqueue_styles_header() {
		global $wp_query;

		wp_register_style( 'sns-sportspress', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/sportspress.css', array(), SP_SNS_VERSION);
		wp_register_style( 'sns-sportspress-site', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/sportspress-site.css', array(), SP_SNS_VERSION);
		
		wp_register_style( 'league-blocks', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/league-blocks.css', array(), SP_SNS_VERSION);
		wp_register_style( 'league-team-blocks', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/league-team-blocks.css', array(), SP_SNS_VERSION);
		wp_register_style( 'team-blocks', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/team-blocks.css', array(), SP_SNS_VERSION);
		wp_register_style( 'event-blocks', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/event-blocks.css', array(), SP_SNS_VERSION);
		wp_register_style( 'event-rows', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/event-rows.css', array(), SP_SNS_VERSION);
		wp_register_style( 'event-playoff', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/event-playoff.css', array(), SP_SNS_VERSION);
    	wp_register_style( 'transfer-rows', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/transfer-rows.css', array(), SP_SNS_VERSION);
    	wp_register_style( 'predict-blocks', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/predict-blocks.css', array(), SP_SNS_VERSION);
    	
		wp_register_style( 'league-header', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/league-header.css', array(), SP_SNS_VERSION);
		wp_register_style( 'league-table', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/league-table.css', array(), SP_SNS_VERSION);

		wp_register_style( 'team-header', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/team-header.css', array(), SP_SNS_VERSION);
		wp_register_style( 'team-squad', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/team-squad.css', array(), SP_SNS_VERSION);
    	
    	wp_register_style( 'event-header', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/event-header.css', array(), SP_SNS_VERSION);
    	wp_register_style( 'event-statistics', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/event-statistics.css', array(), SP_SNS_VERSION);
    	wp_register_style( 'event-predict', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/event-predict.css', array(), SP_SNS_VERSION);

    	wp_register_style( 'sns-filter', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/css/filter.css', array(), SP_SNS_VERSION);

    	wp_register_script( 'sp-sns-script', get_template_directory_uri() . '/' . SP()->template_path() . 'assets/js/sportspress.js', array(), SP_SNS_VERSION );
		

		$tab = 'main';
		if ( isset( $wp_query->query['tab'] ) ) {
			$tab = $wp_query->query['tab'];
		}

		if ( SP_SNS_Theme::isMC() ) {

			wp_enqueue_script( 'sp-sns-script' );

			global $user_ID, $post;
			wp_localize_script( 'sp-sns-script', 'SP_SNS', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'wp_url'   => get_bloginfo( 'wpurl' ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'user_ID'  => $user_ID,
				'post_ID'  => ! empty( $post->ID ) ? $post->ID : 0
			) );

		}

		if ( SP_SNS_Theme::isMC() ) {
			wp_enqueue_style( 'sns-sportspress');
			wp_enqueue_style( 'sns-sportspress-site');
		}

		if ( SP_SNS_Theme::isMain() || SP_SNS_Theme::isMainMC() ) {
			wp_enqueue_style( 'sns-filter');
			wp_enqueue_style( 'league-blocks');
			wp_enqueue_style( 'event-rows');
			wp_enqueue_style( 'event-blocks');
		}

		if ( SP_SNS_Theme::isTransfers() ) {
			wp_enqueue_style( 'sns-filter');
			wp_enqueue_style( 'league-blocks');
			wp_enqueue_style( 'transfer-rows');
		}

		if ( SP_SNS_Theme::isLeague() ) {
			wp_enqueue_style( 'sns-filter');
			wp_enqueue_style( 'team-blocks' );
			wp_enqueue_style( 'event-blocks');
			wp_enqueue_style( 'league-header');
			if ($tab == 'main') {
				wp_enqueue_style( 'event-rows');
			}
			if ($tab == 'table') {
				wp_enqueue_style( 'league-table');
				wp_enqueue_style( 'event-rows');
				wp_enqueue_style( 'event-playoff');
			}
			if ($tab == 'calendar') {
				wp_enqueue_style( 'event-rows');
			}
			if ($tab == 'transfers') {
				wp_enqueue_style( 'transfer-rows');
			}
		}	

		if ( is_singular( 'sp_team' ) ) {
			wp_enqueue_style( 'sns-filter');
			wp_enqueue_style( 'event-blocks');
			wp_enqueue_style( 'team-header');
			if ($tab == 'main') {
				wp_enqueue_style( 'event-rows');
				wp_enqueue_style( 'league-team-blocks');
			}
			if ($tab == 'table') {
				wp_enqueue_style( 'league-table');
			}
			if ($tab == 'calendar') {
				wp_enqueue_style( 'event-rows');
			}
			if ($tab == 'transfers') {
				wp_enqueue_style( 'transfer-rows');
			}
			if ($tab == 'squad') {
				wp_enqueue_style( 'league-table');
			}						
		}

		if ( is_singular( 'sp_event' ) ) {

			wp_enqueue_style( 'event-header' );	
			wp_enqueue_style( 'sns-filter');
			wp_enqueue_style( 'event-rows');
			wp_enqueue_style( 'event-predict');
			wp_enqueue_style( 'event-blocks');
								
		}

		if ( is_singular( 'predicts' ) ) {
			wp_enqueue_script( 'sp-sns-script' );

			global $user_ID, $post;
			wp_localize_script( 'sp-sns-script', 'SP_SNS', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'wp_url'   => get_bloginfo( 'wpurl' ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'user_ID'  => $user_ID,
				'post_ID'  => ! empty( $post->ID ) ? $post->ID : 0
			) );			
		}


	}
	public function frontend_enqueue_styles_footer() {
		global $wp_query;

		$tab = 'main';
		if ( isset( $wp_query->query['tab'] ) ) {
			$tab = $wp_query->query['tab'];
		}

		if ( SP_SNS_Theme::isMain() || SP_SNS_Theme::isMainMC() ) {
			wp_enqueue_style( 'transfer-rows' );
			wp_enqueue_style( 'predict-blocks' );
		}

		if ( SP_SNS_Theme::isLeague() ) {
			if ($tab == 'main') {
				wp_enqueue_style( 'transfer-rows');
				wp_enqueue_style( 'league-table');
				wp_enqueue_style( 'predict-blocks' );
			}
			if ($tab == 'calendar') {
				wp_enqueue_style( 'predict-blocks' );
			}	
		}

		if ( is_singular( 'sp_team' ) ) {
			if ($tab == 'main') {
				wp_enqueue_style( 'transfer-rows');
				wp_enqueue_style( 'league-table');
				wp_enqueue_style( 'predict-blocks' );
			}
			if ($tab == 'calendar') {
				wp_enqueue_style( 'predict-blocks' );
			}			
		}

		if ( is_singular( 'sp_event' ) ) {
			wp_enqueue_style( 'event-statistics');
			wp_enqueue_style( 'team-squad');		
		}
					
	}

	public function action_links() {
		global $pagenow, $typenow;
		if ( in_array( $typenow, sp_importable_post_types() ) ) {
			if ( 'sp_event' === $typenow ) {
				if ( 'edit.php' === $pagenow ) {
					?>
					<script type="text/javascript">
					(function($) {
						$(".wrap .page-title-action").first().after(
							$("<a class=\"add-new-h2\" href=\"<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'sp_fixtures_sports' ), 'admin.php' ) ) ); ?>\"><?php esc_html_e( 'Sports API Результаты', 'sportspress' ); ?></a>")
						).after(
							$("<a class=\"add-new-h2\" href=\"<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'sp_event_sports' ), 'admin.php' ) ) ); ?>\"><?php esc_html_e( 'Sports API матчи', 'sportspress' ); ?></a>")
						);

					})(jQuery);
					</script>
					<?php
				}
			} else {
				if ( 'edit.php' === $pagenow ) {
					?>
					<script type="text/javascript">
					(function($) {
						$(".wrap .page-title-action").first().after(
							$("<a class=\"add-new-h2\" href=\"<?php echo esc_url( admin_url( add_query_arg( array( 'page' => $typenow . '_sports' ), 'admin.php' ) ) ); ?>\"><?php esc_html_e( 'Sports API', 'sportspress' ); ?></a>")
						);
					})(jQuery);
					</script>
					<?php
				}
			}
		}
	}


}

endif;


new SportsPress_SNS();


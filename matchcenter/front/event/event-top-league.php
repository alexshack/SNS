<?php
/**
 * Events Top League Block
 *
 * @author      Alex Torbeev
 * @package     SportsPress/Templates
 * @version     2.7.9
 */

$football_page_url = get_permalink( get_page_by_path( get_option('sportspress_sns_main_page', '') ) );
$hockey_page_url   = get_permalink( get_page_by_path( get_option('sportspress_sns_main_hockey_page', '') ) );
$basketball_page_url = get_permalink( get_page_by_path( get_option('sportspress_sns_main_basketball_page', '') ) );

$sport             = get_term_meta( $league->term_id, 'sport_type', true );
$image_id          = get_term_meta( $league->term_id, '_thumbnail_id', 1 );
$image_url         = wp_get_attachment_url( $image_id );

$sport_types = [
	'football' => [
		'name' => 'Футбол',
		'url'  => $football_page_url,
	],
	'hockey'   => [
		'name' => 'Хоккей',
		'url'  => $hockey_page_url
	],
	'basketball'   => [
		'name' => 'Баскетбол',
		'url'  => $basketball_page_url
	]	
];

?>

<div class="sp_event_top_league">
	<div class="sp_event_top_league_header">
		<a href="<?php echo $sport_types[$sport]['url']; ?>">
			<?php echo $sport_types[$sport]['name']; ?>
		</a>
		<div class="sp_event_top_league_toggler">
			<svg class="sp_event_top_league_toggler_open"><use xlink:href="<?php echo get_template_directory_uri() ?>/sportspress/assets/img/toggler.svg#toggler"></use></svg>
		</div>
	</div>
	<a href="<?php echo get_term_link( $league ); ?>" class="sp_event_top_league_content">
		<img class="lozad lazy sp_event_top_league_image" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $image_url; ?>" alt="<?php $league->name; ?>" width="40" height="40">
		<?php echo $league->name; ?>
	</a>
	<div class="sp_event_top_league_links">
		<div class="sp_event_top_league_header">
			<img class="lozad lazy sp_event_top_league_image" src="<?php echo Thumbnail::$lazy_preview; ?>" data-src="<?php echo $image_url; ?>" alt="<?php $league->name; ?>" width="40" height="40">
			<div class="sp_event_top_league_toggler">
				<svg class="sp_event_top_league_toggler_open"><use xlink:href="<?php echo get_template_directory_uri() ?>/sportspress/assets/img/toggler.svg#toggler"></use></svg>
			</div>
		</div>
		<a href="<?php echo get_term_link( $league->term_id, 'sp_league' ) ?>" class="sp_event_top_league_link">Обзор</a>
		<a href="<?php echo get_term_link( $league->term_id, 'sp_league' ) . 'table/' ?>" class="sp_event_top_league_link">Таблица</a>
		<a href="<?php echo get_term_link( $league->term_id, 'sp_league' ) . 'calendar/'; ?>" class="sp_event_top_league_link">Календарь</a>
	</div>
</div>
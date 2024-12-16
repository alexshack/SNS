<?php
/**
 * Template part for League table block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$defaults = array(
	'id'                   => get_the_ID(),
	'number'               => -1,
	'columns'              => null,
	'highlight'            => null,
	'highlight2'           => null,
	'show_full_table_link' => false,
	'title'                => false,
	'show_title'           => get_option( 'sportspress_table_show_title', 'yes' ) == 'yes' ? true : false,
	'show_team_logo'       => get_option( 'sportspress_table_show_logos', 'yes' ) == 'yes' ? true : false,
	'link_posts'           => null,
	'responsive'           => get_option( 'sportspress_enable_responsive_tables', 'no' ) == 'yes' ? true : false,
	'sortable'             => get_option( 'sportspress_enable_sortable_tables', 'yes' ) == 'yes' ? true : false,
	'scrollable'           => get_option( 'sportspress_enable_scrollable_tables', 'yes' ) == 'yes' ? true : false,
	'paginated'            => get_option( 'sportspress_table_paginated', 'yes' ) == 'yes' ? true : false,
	'rows'                 => get_option( 'sportspress_table_rows', 10 ),
	'tab'                  => 'main',
	'show_events'          => false,
);

extract( $defaults, EXTR_SKIP );

if ( ! isset( $link_posts ) ) {
	if ( 'player' === sp_get_post_mode( $id ) ) {
		$link_posts = get_option( 'sportspress_link_players', 'yes' ) == 'yes' ? true : false;
	} else {
		$link_posts = get_option( 'sportspress_link_teams', 'no' ) == 'yes' ? true : false;
	}
}

if ( ! isset( $highlight ) ) {
	$highlight = get_post_meta( $id, 'sp_highlight', true );
}

$table = new SP_League_Table( $id );

if ( $show_title && false === $title && $id ) :
	$caption = $table->caption;
	if ( $caption ) {
		$title = $caption;
	} else {
		$title = get_the_title( $id );
	}
endif;

// Check if we have event status sent from shortcode
if ( isset( $show_published_events ) ) {
	$table->show_published_events = $show_published_events;
}

if ( isset( $show_future_events ) ) {
	$table->show_future_events = $show_future_events;
}

// Create a unique identifier based on the current time in microseconds
$identifier = uniqid( 'table_' );

$output = '';

if ( $title ) {
	$output .= '<div class="sp_block_title">';
	$output .= '<h2>' . $title . '</h2>';

	$output .= '</div>';
}

$output .= '<div class="sp-table-wrapper">';

$output .= '<table class="sp-league-table sp-data-table sp_table_rows' . ( $sortable ? ' sp-sortable-table' : '' ) . ( $responsive ? ' sp-responsive-table ' . $identifier : '' ) . ( $scrollable ? ' sp-scrollable-table' : '' ) . ( $paginated ? ' sp-paginated-table' : '' ) . '" data-sp-rows="' . $rows . '">' . '<thead>' . '<tr>';

$data = $table->data();

// The first row should be column labels
$labels = $data[0];
// If responsive tables are enabled then load the inline css code
if ( true == $responsive ) {
	// sportspress_responsive_tables_css( $identifier );
}
// Remove the first row to leave us with the actual data
unset( $data[0] );

if ( $columns === null ) {
	$columns = get_post_meta( $id, 'sp_columns', true );
}

if ( null !== $columns && ! is_array( $columns ) ) {
	$columns = explode( ',', $columns );
}

$output .= '<th class="data-rank" role="columnheader">' . esc_attr__( '#', 'sportspress' ) . '</th>';

foreach ( $labels as $key => $label ) :
	if ( ! is_array( $columns ) || $key == 'name' || in_array( $key, $columns ) ) {
		$output .= '<th class="data-' . $key . '" role="columnheader">' . $label . '</th>';
	}
endforeach;

$output .= '</tr>' . '</thead>' . '<tbody>';

$i     = 0;
$start = 0;

if ( intval( $number ) > 0 ) :
	$limit = $number;

	// Trim table to center around highlighted team
	if ( $highlight && sizeof( $data ) > $limit && array_key_exists( $highlight, $data ) ) :

		// Number of teams in the table
		$size = sizeof( $data );

		// Position of highlighted team in the table
		$key = array_search( $highlight, array_keys( $data ) );

		// Get starting position
		$start = $key - ceil( $limit / 2 ) + 1;
		if ( $start < 0 ) {
			$start = 0;
		}

		// Trim table using starting position
		$trimmed = array_slice( $data, $start, $limit, true );

		// Move starting position if we are too far down the table
		if ( sizeof( $trimmed ) < $limit && sizeof( $trimmed ) < $size ) :
			$offset = $limit - sizeof( $trimmed );
			$start -= $offset;
			if ( $start < 0 ) {
				$start = 0;
			}
			$trimmed = array_slice( $data, $start, $limit, true );
		endif;

		// Replace data
		$data = $trimmed;
	endif;
endif;

// Loop through the teams
foreach ( $data as $team_id => $row ) :

	if ( isset( $limit ) && $i >= $limit ) {
		continue;
	}

	$name = sp_array_value( $row, 'name', null );
	if ( ! $name ) {
		continue;
	}

	// Generate tags for highlighted team
	$tr_class = $td_class = '';
	if ( $highlight == $team_id || $highlight2 == $team_id) :
		$tr_class = ' highlighted';
		$td_class = ' sp-highlight';
	endif;

	$output .= '<tr class="' . ( $i % 2 == 0 ? 'odd' : 'even' ) . $tr_class . ' sp-row-no-' . $i . '">';

	// Rank
	$output .= '<td class="data-rank' . $td_class . '" data-label="' . $labels['pos'] . '">' . sp_array_value( $row, 'pos' ) . '</td>';

	$name_class = '';

	if ( $show_team_logo ) :
		if ( has_post_thumbnail( $team_id ) ) :
			$logo_url        = get_the_post_thumbnail_url( $team_id, 'sportspress-fit-mini' );

			$logo = '<img class="lozad lazy" src="' . Thumbnail::$lazy_preview . '" data-src="'. $logo_url. '" width="28" height="28" title="' . $name . '">';
			$name        = '<span class="team-logo">' . $logo . '</span>' . $name;
			$name_class .= ' has-logo';
		endif;
	endif;

	if ( $link_posts ) :
		$permalink = get_post_permalink( $team_id );
		$name      = '<a href="' . $permalink . '">' . $name . '</a>';
	endif;

	$output .= '<td class="data-name' . $name_class . $td_class . '" data-label="' . $labels['name'] . '">' . $name . '</td>';

	foreach ( $labels as $key => $value ) :
		if ( in_array( $key, array( 'pos', 'name' ) ) ) {
			continue;
		}
		if ( ! is_array( $columns ) || in_array( $key, $columns ) ) {
			$output .= '<td class="data-' . $key . $td_class . '" data-label="' . $labels[ $key ] . '">' . sp_array_value( $row, $key, '&mdash;' ) . '</td>';
		}
	endforeach;

	$output .= '</tr>';

	$i++;
	$start++;

endforeach;

$output .= '</tbody>' . '</table>';

$output .= '</div>';

	if (SP_SNS_Theme::isLeague()) {
		if ($tab == 'main') {
			$league = get_queried_object();
			$title_link = get_term_link( $league ) . 'table/';
			$title_link_name = 'Турнирная таблица';
			$output .= '<div class="sp_view_all_link sp_block_title"><a href="' . $title_link . '">' . $title_link_name . '</a></div>';
		}
	}

if ( $show_full_table_link ) {
	$output .= '<div class="sp-league-table-link sp-view-all-link"><a href="' . get_permalink( $id ) . '">' . esc_attr__( 'View full table', 'sportspress' ) . '</a></div>';
}
?>
<div class="sp_block sp-template-league-table">
	<?php echo  $output ; ?>
	<?php if ( $show_events ) {
		$events = array_reverse( $table->events );
	    $events_args = array(
			'show_date'     => true,
			'show_time'     => true,
			'show_league'   => false,
			'show_matchday' => false,
			//'orderby'       => 'day',
			'hide_if_empty' => true,
			'data'      => $events 			
		);

	    sp_get_template( 'event-rows.php', $events_args, SP()->template_path() . 'event/'  );
	} ?>
</div>

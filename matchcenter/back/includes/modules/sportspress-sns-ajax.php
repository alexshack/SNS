<?php

function SP_SNS_AjaxOnRest() {
	return SP_SNS_AJAX_REST::getInstance();
}

function SP_SNS_rest_callback( $function_name ) {
	SP_SNS_AjaxOnRest()->init_rest( $function_name );
}

function SP_SNS_rest_action( $callback, $guest_access = false ) {
	SP_SNS_AjaxOnRest()->init_ajax_callback( $callback, $guest_access );
}

SP_SNS_rest_callback( 'sp_ajax_on_rest_call' );
function sp_ajax_on_rest_call() {
	global $user_ID;

	SP_SNS_AjaxOnRest()->verify();

	$callback = $_POST['call_action'];

	$callbackProps = SP_SNS_AjaxOnRest()->get_ajax_callback( $callback );

	if ( ! $callbackProps ) {
		wp_send_json( [
			'error' => __( 'Unregistered callback' )
		] );
	}

	if ( ! $user_ID && ! $callbackProps['guest'] ) {
		wp_send_json( [
			'error' => __( 'Access to callback is forbidden' )
		] );
	}

	if ( ! function_exists( $callback ) ) {
		wp_send_json( [
			'error' => __( 'Function is not found' )
		] );
	}

	$respond = $callback();

	wp_send_json( $respond );

}

SP_SNS_rest_action( 'SP_SNS_main_filter', true );
function SP_SNS_main_filter() {

	$type   = strval( $_POST['type'] );
	$league = intval( $_POST['league'] );
	$offset = intval( $_POST['offset'] );
	$date_from = strval( $_POST['date_from'] );
	$date_to = strval( $_POST['date_to'] );
	$status = strval( $_POST['status'] );
	$team = intval( $_POST['team'] );

	if ( $type ) {
		if ( $type != 'all' ) {
			$filter_args['sports'] = [ new SP_SNS_Sport( $type ) ];
			$league_args = [
			    'taxonomy' => 'sp_league',
			    'meta_query' => [
			    	[
			        	'key' => 'sport_type',
			        	'value' => $type
			    	],		        
			    ]
			];

		} else {
			$league_args = [
			    'taxonomy' => 'sp_league',
			];			
		}
	}

	$leagues = get_terms( $league_args );
	usort( $leagues, 'sp_sort_terms' );
	$league_options = '<option value="">Турнир</option>';
	foreach ( $leagues as $league_term ) {
		$league_options .= '<option value="' . $league_term->term_id . '" ' . selected( $league, $league_term->term_id, false ) . '>' .  $league_term->name . '</option>';
	}

	if ( $league ) {
		$filter_args['leagues'] = [ $league ];
		$filter_args['league_terms'] = true;
	}

	if ( $date_from ) {
		$filter_args['date_from'] = wp_date( 'Y-m-d', strtotime( $date_from ) );
	}

	if ( $date_to ) {
		$filter_args['date_to'] = wp_date( 'Y-m-d', strtotime( $date_to ) );
	}

	if ( $status ) {
		$filter_args['status'] = $status;
	}

	if ( $offset ) {
		$filter_args['show_terms'] = true;
	}

	if ( $team ) {
		$filter_args['team'] = $team;
	}

/*	$filter_args = [
		'types' => [ $type ],
		'leagues' => [ $league ],
		'date' => wp_date( 'Y-m-d', strtotime( $date ) ),
		'status' => $status
	];*/

	ob_start();
	sp_get_template( 'event-filter-main.php', $filter_args, SP()->template_path() . 'event/',  );
	$content = ob_get_clean();

	wp_send_json( [
		'content' => $content,
		'leagues' => $league_options
	] );

}

SP_SNS_rest_action( 'SP_SNS_league_filter', true );
function SP_SNS_league_filter() {

	$league  = intval( $_POST['league'] );
	$season  = intval( $_POST['season'] );
	$team    = intval( $_POST['team'] );
	$predict = strval( $_POST['predict'] );
	$status  = strval( $_POST['status'] );
	$day     = intval( $_POST['day'] );
	$date    = 'default';

	$filter_args = [
		'league_id' => $league,
		'season_id' => $season,
		'date'      => $date,
	];

	if ( $day ) {
		$filter_args['day'] = $day;
	}

	if ( $team ) {
		$filter_args['team'] = $team;
	}

	if ( $predict ) {
		$filter_args['has_predict'] = 'true';
	}

	if ( $status ) {
		$filter_args['status'] = $status;
	}

	ob_start();
	sp_get_template( 'event-filter-league.php', $filter_args, SP()->template_path() . 'event/',  );
	$content = ob_get_clean();

	wp_send_json( [
		'content' => $content
	] );

}

SP_SNS_rest_action( 'SP_SNS_team_filter', true );
function SP_SNS_team_filter() {

	$league  = intval( $_POST['league'] );
	$season  = intval( $_POST['season'] );
	$team    = intval( $_POST['team'] );
	$predict = strval( $_POST['predict'] );
	$status  = strval( $_POST['status'] );
	$date    = strval( $_POST['date'] );

	$filter_args = [
		'team'   => $team,
		'season_id' => $season,
	];

	if ( $date ) {
		$filter_args['date'] = $date;
	}

	if ( $league ) {
		$filter_args['league_id'] = $league;
	}

	if ( $predict ) {
		$filter_args['has_predict'] = 'true';
	}

	if ( $status ) {
		$filter_args['status'] = $status;
	}

	ob_start();
	sp_get_template( 'event-filter-team.php', $filter_args, SP()->template_path() . 'event/',  );
	$content = ob_get_clean();

	wp_send_json( [
		'content' => $content
	] );

}

SP_SNS_rest_action( 'SP_SNS_transfer_filter', true );
function SP_SNS_transfer_filter() {

	$league   = intval( $_POST['league'] );
	$season   = intval( $_POST['season'] );
	$team_in  = intval( $_POST['team_in'] );
	$team_out = intval( $_POST['team_out'] );	
	$type     = intval( $_POST['type'] );
	$status   = strval( $_POST['status'] );

	$filter_args = [
		'season'    => $season,
		'limit'     => 10,
		'show_more' => true
	];

	if ( $league ) {
		$filter_args['league'] = $league;
	}

	if ( $team_in ) {
		$filter_args['team_in'] = $team_in;
	}

	if ( $team_out ) {
		$filter_args['team_out'] = $team_out;
	}

	if ( $type ) {
		$filter_args['type'] = $type;
	}

	if ( $status ) {
		$filter_args['status'] = $status;
	}

	ob_start();
	sp_get_template( 'transfer-rows.php', $filter_args, SP()->template_path() . 'transfer/',  );
	$content = ob_get_clean();

	wp_send_json( [
		'content' => $content
	] );

}

SP_SNS_rest_action( 'SP_SNS_transfer_more', true );
function SP_SNS_transfer_more() {

	$offset   = intval( $_POST['offset'] );
	$league   = intval( $_POST['league'] );
	$season   = intval( $_POST['season'] );
	$team     = intval( $_POST['team'] );
	$team_in  = intval( $_POST['team_in'] );
	$team_out = intval( $_POST['team_out'] );	
	$type     = intval( $_POST['type'] );
	$status   = strval( $_POST['status'] );

	$filter_args = [
		'season'    => $season,
		'limit'     => 10,
		'show_more' => true,
		'offset'    => $offset
	];

	if ( $league ) {
		$filter_args['league'] = $league;
	}

	if ( $team ) {
		$filter_args['team'] = $team;
	}

	if ( $team_in ) {
		$filter_args['team_in'] = $team_in;
	}

	if ( $team_out ) {
		$filter_args['team_out'] = $team_out;
	}

	if ( $type ) {
		$filter_args['type'] = $type;
	}

	if ( $status ) {
		$filter_args['status'] = $status;
	}

	ob_start();
	sp_get_template( 'transfer-rows.php', $filter_args, SP()->template_path() . 'transfer/',  );
	$content = ob_get_clean();

	wp_send_json( [
		'content' => $content
	] );

}

SP_SNS_rest_action( 'SP_SNS_top_filter', true );
function SP_SNS_top_filter() {

	$type     = strval( $_POST['type'] );
	$date     = strval( $_POST['date'] );
	$league   = intval( $_POST['league'] );
	$status   = strval( $_POST['status'] );
	$team     = intval( $_POST['team'] );

	$filter_args = [];

	if ( $type ) {
		$filter_args['sport_type'] = $type;
	}	

	if ( $league ) {
		$filter_args['league'] = $league;
	}

	if ( $date ) {
		$filter_args['date'] = $date;
	}

	if ( $team ) {
		$filter_args['team'] = $team;
	}

	if ( $status == 'league' ) {
		$filter_args['show_league'] = false;
	} else {
		$filter_args['show_league'] = true;
	}



	ob_start();

	sp_get_template( 'event-top.php', $filter_args, SP()->template_path() . 'event/',  );
	$content = ob_get_clean();

	wp_send_json( [
		'content' => $content
	] );

}

SP_SNS_rest_action( 'SP_SNS_vote_event', true );
function SP_SNS_vote_event() {

	$event_id  = intval( $_POST['event_id'] );
	$vote_type = intval( $_POST['vote'] );

	$event = new SP_SNS_Event( $event_id );

	$votes = $event->setVote( $vote_type );

    $user_votes = ( isset( $_COOKIE['sp_user_vote'] ) ) ? explode( ',', $_COOKIE['sp_user_vote'] ) : [];
    if( !in_array( $event_id, $user_votes ) ) {
        $user_votes[] = $event_id;
    }

    if ( function_exists( 'is_user_activated' ) ) {
        $user_activated = is_user_activated();
    } else {
        $user_activated = true;
    }

    if( is_user_logged_in() && $user_activated ) {
        $user_logged_votes = explode( ',', get_user_meta( get_current_user_id(), 'sp_user_vote', true ) );
        $user_logged_votes[] = $event_id;
        update_user_meta( get_current_user_id(), 'sp_user_vote', implode(',', $user_logged_votes));
    }
    setcookie( 'sp_user_vote', implode( ',', $user_votes ), time()+60*60*24*30, '/' );

	wp_send_json( [
		'vote1' => $votes[1],
		'vote2' => $votes[2],
		'vote3' => $votes[3],
	] );

}
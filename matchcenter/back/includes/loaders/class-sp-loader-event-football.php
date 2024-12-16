<?php
/**
 * SNS Event Football importer - import events from Sports API into SportsPress.
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class SP_Loader_Event_Footbal {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	static function init( ) {

	}

	static function import( $season_id, $league_id, $date_start = false, $date_end = false ) {

		if (! $season_id || ! $league_id ) {
			return false;
		}

		$season = new SP_SNS_Season( $season_id );
		$league = new SP_SNS_League( $league_id );

		if ( ! $season->api_id || ! $league->api_id ) {
			return false;
		}

		$feed_league_id = $league->api_id;
		$feed_season_id = $season->api_id;

		$date_parameters = '';
		$timezone = '&timezone=Europe/Moscow';

		if ( $date_start && $date_end ) {
			$date_parameters = '&from=' . $date_start . '&to=' . $date_end;
		}

		if ( $date_start && ! $date_end ) {
			$date_parameters = '&date=' . $date_start;
		}

		$request = 'fixtures?league=' . $feed_league_id . '&season=' . $feed_season_id . $date_parameters . $timezone;

		$feeds = SP_Loader_Functions::getFeeds($request, 'football');

		$imported = 0;
		$updated = 0;
		$skipped = 0;
		$events_array = [];

		$live_statuses = [
			'1H',
			'HT',
			'2H',
			'ET',
			'BT',
			'P',
			'SUSP',
			'INT',
			'LIVE'
		];

		$finish_statuses = [
			'FT',
			'AET',
			'PEN',
		];

		$games = [];
		$update_squad = 'no';

		if ($feeds) {
			
			$league_days = [];
			$league_stages = [];

			foreach ($feeds as $feed) {
				$d_game = $feed['fixture'];
				$games[$d_game['id']] = $d_game;
				$games[$d_game['id']]['home'] = $feed['teams']['home'];
				$games[$d_game['id']]['away'] = $feed['teams']['away'];
				$games[$d_game['id']]['goals'] = $feed['goals'];
				$games[$d_game['id']]['score'] = $feed['score'];
				$games[$d_game['id']]['round'] = $feed['league']['round']; 

				$round = SP_Loader_Functions::translateRound( $feed['league']['round'] );
				$games[$d_game['id']]['round_ru'] = $round;
			}

			foreach ($games as $key => $game) {

				$results     = false;
				$is_finished = in_array( $game['status']['short'], $finish_statuses );
				$is_live     = in_array( $game['status']['short'], $live_statuses );

				$home_team = SP_Loader_Functions::getPostByApiID('sp_team', $game['home']['id']);
				$away_team = SP_Loader_Functions::getPostByApiID('sp_team', $game['away']['id']);
				$venue     = SP_Loader_Functions::getTermByApiID('sp_venue', $game['venue']['id']);

				$stage_id = SP_Loader_Functions::getStage( $game['round'] );

				if (! $home_team ) {
					$home_team = SP_Loader_Functions::addTeam($game['home'], $league->ID, $season->ID);
				} else{
					wp_set_object_terms( $home_team->ID, (int)$league->ID, 'sp_league', true );
					wp_set_object_terms( $home_team->ID, (int)$season->ID, 'sp_season', true );
				}
				if (! $away_team ) {
					$away_team = SP_Loader_Functions::addTeam($game['away'], $league->ID, $season->ID);
				} else {
					wp_set_object_terms( $away_team->ID, (int)$league->ID, 'sp_league', true );
					wp_set_object_terms( $away_team->ID, (int)$season->ID, 'sp_season', true );					
				}						

				$date_str = date('j F Y', $game['timestamp']);
				$date_time = date('Y-m-d H:i:s', $game['timestamp']);

				$event_title = $home_team->post_title . ' - ' . $away_team->post_title . ', ' . $date_str;

				if ( $is_finished || $is_live ) {

					$outcome   = ['draw', 'draw'];
                    $overtime  = [null, null];
                    $penalties = [null, null];

                    $main_score = [
                        $game['score']['fulltime']['home'],
                        $game['score']['fulltime']['away']
                    ];
                    $firsthalf  = [
                        $game['score']['halftime']['home'],
                        $game['score']['halftime']['away']
                    ];
                    $secondhalf = [
                        $main_score[0] - $firsthalf[0],
                        $main_score[1] - $firsthalf[1],
                    ];
                    if ( !is_null($game['score']['extratime']['home']) ) {
                        $overtime = [
                            $game['score']['extratime']['home'],
                            $game['score']['extratime']['away']
                        ];
                    }
                    if ( !is_null($game['score']['penalty']['home']) ) {
                        $penalties = [
                            $game['score']['penalty']['home'],
                            $game['score']['penalty']['away']
                        ];
                    }
                    $goals      = [
                        $main_score[0] + $overtime[0],
                        $main_score[1] + $overtime[1]
                    ];
                    $game_score = $goals;
                    $total_score = [
                        $game_score[0] + $penalties[0],
                        $game_score[1] + $penalties[1],
                    ];

                    if ( (int)$total_score[0] > (int)$total_score[1] ) {
                        $outcome[0] = 'win';
                        $outcome[1] = 'loss';
                    }

                    if ( (int)$total_score[0] < (int)$total_score[1] ) {
                        $outcome[0] = 'loss';
                        $outcome[1] = 'win';
                    }

					$results = [
						$home_team->ID => [
							'firsthalf'  => $firsthalf[0],
							'secondhalf' => $secondhalf[0],
							'overtime'   => $overtime[0],
							'penalties'  => $penalties[0],
							'goals'      => $goals[0],
                            'main'       => $main_score[0],
                            'game'       => $game_score[0],
                            'total'      => $total_score[0],
							'outcome'    => $outcome[0],
						],
						$away_team->ID => [
							'firsthalf'  => $firsthalf[1],
							'secondhalf' => $secondhalf[1],
							'overtime'   => $overtime[1],
							'penalties'  => $penalties[1],										
							'goals'      => $goals[1],
                            'main'       => $main_score[1],
                            'game'       => $game_score[1],
                            'total'      => $total_score[1],
							'outcome'    => $outcome[1]
						]
					];

				}

				if ( ($game_post = SP_Loader_Functions::getPostByApiID('sp_event', (int)$key)) ) {

					$post_id = $game_post->ID;

					if ( $results ) {
						wp_update_post( array(
							'ID' => $post_id,
							'post_status' => 'publish',
							'post_date'   => $date_time,
							'post_title'  => $event_title,
						) );
					} else {
						wp_update_post( array(
							'ID' => $post_id,
							'post_status' => 'future',
							'post_date'   => $date_time,
							'post_title'  => $event_title,
						) );				
					}

					$updated++;

				} else {

					if ( $results ) {
						$post_status = 'publish';
					} else {
						$post_status = 'future';
					}

					$args = [
						'post_type'   => 'sp_event',
						'post_status' => $post_status,
						'post_title'  => $event_title,
						'post_date'   => $date_time,
					];
					$post_id = wp_insert_post( $args );
					
					update_post_meta( $post_id, SP_Loader_Functions::$id_field, $key );
					add_post_meta( $post_id, 'sp_team', $home_team->ID );
					add_post_meta( $post_id, 'sp_team', $away_team->ID );

					$imported++;
				}

				wp_set_object_terms( $post_id, (int)$league->ID, 'sp_league', false );
				wp_set_object_terms( $post_id, (int)$season->ID, 'sp_season', false );
				wp_set_object_terms( $post_id, (int)$stage_id, 'sp_stage', false );

				if ( $venue ) {
					wp_set_object_terms( $post_id, $venue->term_id, 'sp_venue', false );
				}				

				update_post_meta( $post_id, 'sp_format', 'league' );
				update_post_meta( $post_id, 'sp_mode', 'team' );
				update_post_meta( $post_id, 'sp_result_columns', array() );
				update_post_meta( $post_id, 'sp_status', 'ok');	

				update_post_meta( $post_id, 'sp_day', $game['round_ru'] );

				if ( $is_finished || $is_live ) {

					$statistics = SP_Loader_Functions::getStatistics( $key, $results );
					update_post_meta( $post_id, 'sp_results', $results );
					update_post_meta( $post_id, 'sp_statistics', $statistics );
					update_post_meta( $post_id, 'sp_minutes', $game['status']['elapsed'] );

					if ( $is_finished ) {
						update_post_meta( $post_id, 'sp_finished', 'yes' );
						update_post_meta( $post_id, 'sp_event_status', 'finished' );
						$update_squad = 'yes';

                        if ( get_post_meta( $post_id, 'sp_pr_result', true ) != 'yes' ) {
                            if ($predict_id = self::checkPredict($post_id)) {
                               self::setBets( $post_id, $predict_id, $home_team->ID, $away_team->ID );
                            }
                        }

					}
					if ( $is_live ) {
						update_post_meta( $post_id, 'sp_finished', 'no' );
						update_post_meta( $post_id, 'sp_event_status', 'live' );							
					}

				} else {
					update_post_meta( $post_id, 'sp_finished', 'no' );
					update_post_meta( $post_id, 'sp_event_status', $game['status']['short'] );
				}


				if ( ! array_search( $stage_id, $league_stages ) ) {
					$league_stages[] = $stage_id;
				}
				
				$league_days[$season->ID][] = $game['round'];
				$events_array[] = $event_title . '<br>';

			} //end games

			$current_days = get_term_meta( $league->ID, 'sp_days', 1 );
			if ( empty( $current_days ) || !is_array( $current_days ) ) {
				$current_days = [];
			}

			foreach ( $league_days as $season_day => $season_days ) {
				$days = array_unique( $season_days );
				$current_days[ $season_day ] = $days;
			}

			update_term_meta( $league->ID, 'sp_days', $current_days );
			update_term_meta( $league->ID, 'sp_update_squad', $update_squad );


			$all_stages = get_term_meta( $league->ID, 'sp_stages', 1 );
			if ( empty( $all_stages ) || !is_array( $all_stages ) ) {
				$all_stages = [
					$season_id => []
				];
			}

			if ( isset( $all_stages[ $season_id ] ) ) {
				$stages = $all_stages[ $season_id ];
			} else {
				$stages = [];
			}
			
			$stages = array_merge( $stages, $league_stages );
			$stages = array_unique( $stages );
			$all_stages[ $season_id ] = $stages;

			update_term_meta( $league->ID, 'sp_stages', $all_stages );
			

		}

		$data = [
			'request'  => $request,
			'imported' => $imported,
			'updated'  => $updated,
			'skipped'  => $skipped,
			'events'   => $events_array
		];

		return $data;
	}

    static function checkPredict( $event_id ) {
        $predict_type = get_option('sp_sns_predicts_post_type', false);
        $predict_args = [
            'post_type'   => $predict_type,
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key'   => 'sp_event',
                    'value' => $event_id,
                ],
            ]
        ];
        $predicts = get_posts( $predict_args );
        if ( $predicts && count( $predicts ) ) {
            return $predicts[0]->ID;
        }
        return false;
    }

    static function setBets( $event_id, $predict_id, $home_id, $away_id ) {

        $bet_field = get_option('sp_sns_predicts_stake_meta', false);
        $bet_id    = get_post_meta( $predict_id, $bet_field, true );
        $event_date = get_post_meta($predict_id, 'pr_date', true);
        $month   = wp_date('F', $event_date);
        if ( $bet_id ) {
            $predict_result = self::checkBet($event_id, $bet_id, $home_id, $away_id);
            if ($predict_result) {
                update_post_meta($predict_id, 'pr_submited', $predict_result);
                $predict = Predict::setup($predict_id);
                $user_id = $predict->post_author;
                $coef    = $predict->getMaxBet();
                //self::setExpert($user_id, $coef, $predict_result, $month );
            }
        }

        while ($i <= 5) {
            $bet_id = get_post_meta( $predict_id, 'expert_type_bet_' . $i, true );
            if ( $bet_id ) {
                $expert_result = self::checkBet($event_id, $bet_id, $home_id, $away_id);
                if ($expert_result) {
                    update_post_meta($predict_id, 'expert_submited_' . $i, $expert_result);
                    $expert_id = get_post_meta( $predict_id, 'expert_id_' . $i, true );
                    $expert_coef = get_post_meta( $predict_id, 'expert_coef_' . $i, true );
                    //self::setExpert($expert_id, $expert_coef, $expert_result, $month );
                }
            }
            $i++;
        }
    }

    static function setExpert( $user_id, $coef, $result, $month ) {
        $months = get_user_meta($user_id, 'expert_months', true);

        $plus   = 0;
        $draw   = 0;
        $minus  = 0;
        $summ   = 1000;
        $profit = 0;
        $count  = 1;

        switch ($result) {
            case 1:
                $minus = 1;
                $profit = round($coef * -1000, 0);
                break;
            case 2:
                $plus = 1;
                $profit = -1000;
                break;
            case 3:
                $draw = 1;
                break;
        }

        $month_key = -1;
        foreach ($months as $key => $e_month) {
            if ($e_month == $month) {
                $month_key = $key;
            }
        }
        if ( $month_key == -1 ) {
            $months[] = [
                'month'  => $month,
                'plus'   => $plus,
                'draw'   => $draw,
                'minus'  => $minus,
                'summ'   => $summ,
                'profit' => $profit,
                'count'  => $count
            ];
        } else {
            $months[$month_key]['plus']   = $months[$month_key]['plus'] + $plus;
            $months[$month_key]['draw']   = $months[$month_key]['draw'] + $draw;
            $months[$month_key]['minus']  = $months[$month_key]['minus'] + $minus;
            $months[$month_key]['summ']   = $months[$month_key]['summ'] + $summ;
            $months[$month_key]['profit'] = $months[$month_key]['profit'] + $profit;
            $months[$month_key]['count']  = $months[$month_key]['count'] + $count;
        }


    }
    static function checkBet( $event_id, $bet_id, $home_id, $away_id ) {

        $result = 0;

        $is_active                 = get_term_meta( $bet_id, 'is_active', true );
        $is_combine                = get_term_meta( $bet_id, 'is_combine', true );
        $is_rebet                  = get_term_meta( $bet_id, 'is_rebet', true );
        if ( $is_active == 'yes' ) {

                $item_1                    = get_term_meta( $bet_id, 'item_1', true );
                $value_1                   = get_term_meta( $bet_id, 'value_1', true );
                $operator_1                = get_term_meta( $bet_id, 'operator_1', true );
                $value_1_digit             = get_term_meta( $bet_id, 'value_1_digit', true );


                $item_1 = explode(';', $item_1);
                if ( is_array( $item_1 ) && count( $item_1 ) == 3 ) {
                    $result_main = 1;
                    $result      = 1;

                    $item_1_item   = $item_1[0];
                    $item_1_period = $item_1[1];
                    $item_1_team   = $item_1[2];

                    if ( $item_1_item == 'goals' ) {
                        $item_main = self::getStat($event_id, $item_1_period, $item_1_team, $home_id, $away_id);
                    } else {
                        $item_main = self::getStat($event_id, $item_1_item, $item_1_team, $home_id, $away_id, false);
                    }

                    if ( $value_1_digit != 'yes' ) {
                        $value_1 = explode(';', $value_1);
                        if ( is_array( $value_1 ) && count( $value_1 ) == 3 ) {
                            $value_1_item = $value_1[0];
                            $value_1_period = $value_1[1];
                            $value_1_team = $value_1[2];
                            if ( $value_1_item == 'goals' ) {
                                $value_main = self::getStat($event_id, $value_1_period, $value_1_team, $home_id, $away_id);
                            } else {
                                $value_main = self::getStat($event_id, $value_1_item, $value_1_team, $home_id, $away_id, false);
                            }
                        }
                    } else {
                        $value_main = $value_1;
                    }

                    switch ($operator_1) {
                        case '=':
                            if ( $item_main == $value_main ) {
                                $result_main = 2;
                                $result      = 2;
                            }
                            break;
                        case '>':
                            if ( $item_main > $value_main ) {
                                $result_main = 2;
                                $result      = 2;
                            }
                            break;
                        case '<':
                            if ( $item_main < $value_main ) {
                                $result_main = 2;
                                $result      = 2;
                            }
                            break;
                    }

                    if ( $is_combine == 'yes' && $result_main == 2 ) {
                        $result         = 1;

                        $item_2                    = get_term_meta( $bet_id, 'item_2', true );
                        $value_2                   = get_term_meta( $bet_id, 'value_2', true );
                        $operator_2                = get_term_meta( $bet_id, 'operator_2', true );
                        $value_2_digit             = get_term_meta( $bet_id, 'value_2_digit', true );

                        $item_2 = explode(';', $item_2);
                        if ( is_array( $item_2 ) && count( $item_2 ) == 3 ) {
                            $item_2_item = $item_2[0];
                            $item_2_period = $item_2[1];
                            $item_2_team = $item_2[2];

                            if ($item_2_item == 'goals') {
                                $item_combine = self::getStat($event_id, $item_2_period, $item_2_team, $home_id, $away_id);
                            } else {
                                $item_combine = self::getStat($event_id, $item_2_item, $item_2_team, $home_id, $away_id, false);
                            }

                            if ($value_2_digit != 'yes') {
                                $value_2 = explode(';', $value_2);
                                if (is_array($value_2) && count($value_2) == 3) {
                                    $value_2_item = $value_2[0];
                                    $value_2_period = $value_2[1];
                                    $value_2_team = $value_2[2];
                                    if ($value_2_item == 'goals') {
                                        $value_combine = self::getStat($event_id, $value_2_period, $value_2_team, $home_id, $away_id);
                                    } else {
                                        $value_combine = self::getStat($event_id, $value_2_item, $value_2_team, $home_id, $away_id, false);
                                    }
                                }
                            } else {
                                $value_combine = $value_2;
                            }

                            switch ($operator_2) {
                                case '=':
                                    if ($item_combine == $value_combine) {
                                        $result = 2;
                                    }
                                    break;
                                case '>':
                                    if ($item_combine > $value_combine) {
                                        $result = 2;
                                    }
                                    break;
                                case '<':
                                    if ($item_combine < $value_combine) {
                                        $result = 2;
                                    }
                                    break;

                            }
                        }
                    }

                    if ( $is_rebet == 'yes' && $result_main == 1 ) {

                        $value_rebet               = get_term_meta( $bet_id, 'value_rebet', true );
                        $value_rebet_digit         = get_term_meta( $bet_id, 'value_rebet_digit', true );
                        $operator_rebet            = get_term_meta( $bet_id, 'operator_rebet', true );

                        if ($value_rebet_digit != 'yes') {
                            $value_rebet = explode(';', $value_rebet);
                            if (is_array($value_rebet) && count($value_rebet) == 3) {
                                $value_rebet_item = $value_rebet[0];
                                $value_rebet_period = $value_rebet[1];
                                $value_rebet_team = $value_rebet[2];
                                if ($value_rebet_item == 'goals') {
                                    $value_rebet = self::getStat($event_id, $value_rebet_period, $value_rebet_team, $home_id, $away_id);
                                } else {
                                    $value_rebet = self::getStat($event_id, $value_rebet_item, $value_rebet_team, $home_id, $away_id, false);
                                }
                            }
                        }

                        switch ($operator_rebet) {
                            case '=':
                                if ($item_main == $value_rebet) {
                                    $result = 3;
                                }
                                break;
                            case '>':
                                if ($item_main > $value_rebet) {
                                    $result = 3;
                                }
                                break;
                            case '<':
                                if ($item_main < $value_rebet) {
                                    $result = 3;
                                }
                                break;
                        }

                    }
                }

        }

        return $result;

    }

    static function getStat( $event_id, $period, $team, $home_id, $away_id, $goals = true ) {
        $result   = 0;
        if ( $goals ) {
            $results = get_post_meta($event_id, 'sp_results', true);
        } else {
            $results = get_post_meta($event_id, 'sp_statistics', true);
        }
        $home_res = (int)$results[ $home_id ][ $period ];
        $away_res = (int)$results[ $away_id ][ $period ];

        if ( $team == 'home' ) {
            $result = $home_res;
        }
        if ( $team == 'away' ) {
            $result = $away_res;
        }
        if ( $team == 'total' ) {
            $result = $home_res + $away_res;
        }
        if ( $team == 'fora1' ) {
            $result = $home_res - $away_res;
        }
        if ( $team == 'fora2' ) {
            $result = $away_res - $home_res;
        }
        return $result;

    }


}
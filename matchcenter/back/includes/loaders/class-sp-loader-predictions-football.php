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


class SP_Loader_Predictions_Footbal {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	static function init( ) {

	}

	static function import( $event ) {

		if (! $event ) {
			return false;
		}

		if (! $event_api_id = $event->getApiId() ) {
			return false;
		}

		$data = false;

		$request = 'predictions?fixture=' . $event_api_id;

		$feed = SP_Loader_Functions::getFeeds($request, 'football');

		if ($feed) {
			$data = [
				'prediction' => [],
				'last_5'     => [],
				'league'     => []
			];

			$prediction = $feed[0]['predictions'];
			$teams      = $feed[0]['teams'];
			$compare    = $feed[0]['comparison'];
			$games      = $feed[0]['h2h'];

			//победитель
			if ( $event->team_home->api_id == $prediction['winner']['id'] ) {
				if ( $prediction['win_or_draw'] ) {
					$data['prediction']['result'] = [
						'text' => '1X',
						'color' => 'black'
					];
					if ( $event->finished ) {
						if ( $event->team_home->score > $event->team_away->score || $event->team_home->score == $event->team_away->score ) {
							$data['prediction']['result']['color'] = 'green';
						} else {
							$data['prediction']['result']['color'] = 'red';
						}
					}
				} else {
					$data['prediction']['result'] = [
						'text' => 'П1',
						'color' => 'black'
					];					
					if ( $event->finished ) {
						if ( $event->team_home->score > $event->team_away->score ) {
							$data['prediction']['result']['color'] = 'green';
						} else {
							$data['prediction']['result']['color'] = 'red';
						}
					}					
				}
			}

			if ( $event->team_away->api_id == $prediction['winner']['id'] ) {
				if ( $prediction['win_or_draw'] ) {
					$data['prediction']['result'] = [
						'text' => 'X2',
						'color' => 'black'
					];
					if ( $event->finished ) {
						if ( $event->team_home->score < $event->team_away->score || $event->team_home->score == $event->team_away->score ) {
							$data['prediction']['result']['color'] = 'green';
						} else {
							$data['prediction']['result']['color'] = 'red';
						}
					}					
				} else {
					$data['prediction']['result'] = [
						'text' => 'П2',
						'color' => 'black'
					];					
					if ( $event->finished ) {
						if ( $event->team_home->score < $event->team_away->score ) {
							$data['prediction']['result']['color'] = 'green';
						} else {
							$data['prediction']['result']['color'] = 'red';
						}
					}					
				}
			}

			//тотал
			if ( ! is_null( $prediction['under_over'] ) ) {
				$total = (float) $prediction['under_over'];
				if ( $total > 0 ) {
					$data['prediction']['total'] = [
						'text' => 'ТБ(' . abs( $total ) . ')',
						'color' => 'black'
					];
					if ( $event->finished ) {
						if ( $event->team_home->score + $event->team_away->score > abs( $total ) ) {
							$data['prediction']['total']['color'] = 'green';
						} else {
							$data['prediction']['total']['color'] = 'red';
						}
					}					
				} else {
					$data['prediction']['total'] = [
						'text' => 'ТМ(' . abs( $total ) . ')',
						'color' => 'black'
					];
					if ( $event->finished ) {
						if ( $event->team_home->score + $event->team_away->score < abs( $total ) ) {
							$data['prediction']['total']['color'] = 'green';
						} else {
							$data['prediction']['total']['color'] = 'red';
						}
					}					
				}
			}

			// ИТ1
			if ( ! is_null( $prediction['goals']['home'] ) ) {
				$total = (float) $prediction['goals']['home'];
				if ( $total > 0 ) {
					$data['prediction']['total_1'] = [
						'text' => 'ИТ1Б(' . abs( $total ) . ')',
						'color' => 'black'
					];
					if ( $event->finished ) {
						if ( $event->team_home->score > abs( $total ) ) {
							$data['prediction']['total_1']['color'] = 'green';
						} else {
							$data['prediction']['total_1']['color'] = 'red';
						}
					}					
				} else {
					$data['prediction']['total_1'] = [
						'text' => 'ИТ1М(' . abs( $total ) . ')',
						'color' => 'black'
					];
					if ( $event->finished ) {
						if ( $event->team_home->score < abs( $total ) ) {
							$data['prediction']['total_1']['color'] = 'green';
						} else {
							$data['prediction']['total_1']['color'] = 'red';
						}
					}					
				}
			}

			// ИТ2
			if ( ! is_null( $prediction['goals']['away'] ) ) {
				$total = (float) $prediction['goals']['away'];
				if ( $total > 0 ) {
					$data['prediction']['total_2'] = [
						'text' => 'ИТ2Б(' . abs( $total ) . ')',
						'color' => 'black'
					];
					if ( $event->finished ) {
						if ( $event->team_away->score > abs( $total ) ) {
							$data['prediction']['total_2']['color'] = 'green';
						} else {
							$data['prediction']['total_2']['color'] = 'red';
						}
					}					
				} else {
					$data['prediction']['total_2'] = [
						'text' => 'ИТ2М(' . abs( $total ) . ')',
						'color' => 'black'
					];
					if ( $event->finished ) {
						if ( $event->team_away->score < abs( $total ) ) {
							$data['prediction']['total_2']['color'] = 'green';
						} else {
							$data['prediction']['total_2']['color'] = 'red';
						}
					}					
				}
			}

			//Last 5
			$data['last_5']['Игры'] = [
				'home' => $teams['home']['last_5']['played'],
				'away' => $teams['away']['last_5']['played'],
			];			
			$data['last_5']['ГЗ'] = [
				'home' => $teams['home']['last_5']['goals']['for']['total'],
				'away' => $teams['away']['last_5']['goals']['for']['total'],
			];
			$data['last_5']['ГЗ среднее'] = [
				'home' => $teams['home']['last_5']['goals']['for']['average'],
				'away' => $teams['away']['last_5']['goals']['for']['average'],
			];
			$data['last_5']['ГП'] = [
				'home' => $teams['home']['last_5']['goals']['against']['total'],
				'away' => $teams['away']['last_5']['goals']['against']['total'],
			];
			$data['last_5']['ГП среднее'] = [
				'home' => $teams['home']['last_5']['goals']['against']['average'],
				'away' => $teams['away']['last_5']['goals']['against']['average'],
			];
			$data['last_5']['Разница'] = [
				'home' => $data['last_5']['ГЗ']['home'] - $data['last_5']['ГП']['home'],
				'away' => $data['last_5']['ГЗ']['away'] - $data['last_5']['ГП']['away'],
			];
			$data['last_5']['Разница среднее'] = [
				'home' => $teams['home']['last_5']['played'] > 0 ? round( $data['last_5']['Разница']['home'] / $teams['home']['last_5']['played'], 1 ) : 0,
				'away' => $teams['away']['last_5']['played'] > 0 ? round( $data['last_5']['Разница']['away'] / $teams['away']['last_5']['played'], 1 ) : 0,
			];					
			$data['last_5']['Тотал'] = [
				'home' => $data['last_5']['ГЗ']['home'] + $data['last_5']['ГП']['home'],
				'away' => $data['last_5']['ГЗ']['away'] + $data['last_5']['ГП']['away'],
			];
			$data['last_5']['Тотал среднее'] = [
				'home' => $teams['home']['last_5']['played'] > 0 ? round( $data['last_5']['Тотал']['home'] / $teams['home']['last_5']['played'], 1 ) : 0,
				'away' => $teams['away']['last_5']['played'] > 0 ? round( $data['last_5']['Тотал']['away'] / $teams['away']['last_5']['played'], 1 ) : 0,
			];			

			//League
			$data['league']['form'] = [
				'home' => array_slice( str_split( $teams['home']['league']['form'] ), -15 ),
				'away' => array_slice( str_split( $teams['away']['league']['form'] ), -15 ),
			];

			$data['league']['total']['Игры'] = [
				'home' => $teams['home']['league']['fixtures']['played']['total'],
				'away' => $teams['away']['league']['fixtures']['played']['total'],
			];			
			$data['league']['total']['Победы'] = [
				'home' => $teams['home']['league']['fixtures']['wins']['total'],
				'away' => $teams['away']['league']['fixtures']['wins']['total'],
			];
			$data['league']['total']['Ничьи'] = [
				'home' => $teams['home']['league']['fixtures']['draws']['total'],
				'away' => $teams['away']['league']['fixtures']['draws']['total'],
			];
			$data['league']['total']['Поражения'] = [
				'home' => $teams['home']['league']['fixtures']['loses']['total'],
				'away' => $teams['away']['league']['fixtures']['loses']['total'],
			];
			$data['league']['total']['Сумма результатов'] = [
				'home' => $data['league']['total']['Игры']['home'] > 0 ?round( ( $data['league']['total']['Победы']['home'] * 2 + $data['league']['total']['Ничьи']['home'] ) / $data['league']['total']['Игры']['home'] , 1 ) : 0,
				'away' => $data['league']['total']['Игры']['away'] > 0 ? round( ( $data['league']['total']['Победы']['away'] * 2 + $data['league']['total']['Ничьи']['away'] ) / $data['league']['total']['Игры']['away'] , 1 ) : 0,
			];
			$data['league']['total']['ГЗ'] = [
				'home' => $teams['home']['league']['goals']['for']['total']['total'],
				'away' => $teams['away']['league']['goals']['for']['total']['total'],
			];
			$data['league']['total']['ГЗ среднее'] = [
				'home' => $teams['home']['league']['goals']['for']['average']['total'],
				'away' => $teams['away']['league']['goals']['for']['average']['total'],
			];
			$data['league']['total']['ГП'] = [
				'home' => $teams['home']['league']['goals']['against']['total']['total'],
				'away' => $teams['away']['league']['goals']['against']['total']['total'],
			];
			$data['league']['total']['ГП среднее'] = [
				'home' => $teams['home']['league']['goals']['against']['average']['total'],
				'away' => $teams['away']['league']['goals']['against']['average']['total'],
			];
			$data['league']['total']['Разница'] = [
				'home' => $data['league']['total']['ГЗ']['home'] - $data['league']['total']['ГП']['home'],
				'away' => $data['league']['total']['ГЗ']['away'] - $data['league']['total']['ГП']['away'],
			];
			$data['league']['total']['Разница среднее'] = [
				'home' => $data['league']['total']['Игры']['home'] > 0 ? round( $data['league']['total']['Разница']['home'] / $data['league']['total']['Игры']['home'], 1 ) : 0,
				'away' => $data['league']['total']['Игры']['away'] > 0 ? round( $data['league']['total']['Разница']['away'] / $data['league']['total']['Игры']['away'], 1 ) : 0,
			];			
			$data['league']['total']['Тотал'] = [
				'home' => $data['league']['total']['ГЗ']['home'] + $data['league']['total']['ГП']['home'],
				'away' => $data['league']['total']['ГЗ']['away'] + $data['league']['total']['ГП']['away'],
			];
			$data['league']['total']['Тотал среднее'] = [
				'home' => $data['league']['total']['Игры']['home'] > 0 ? round( $data['league']['total']['Тотал']['home'] / $data['league']['total']['Игры']['home'], 1 ) : 0,
				'away' => $data['league']['total']['Игры']['away'] > 0 ? round( $data['league']['total']['Тотал']['away'] / $data['league']['total']['Игры']['away'], 1 ) : 0,
			];

			$data['league']['home']['Игры'] = [
				'home' => $teams['home']['league']['fixtures']['played']['home'],
				'away' => $teams['away']['league']['fixtures']['played']['home'],
			];			
			$data['league']['home']['Победы'] = [
				'home' => $teams['home']['league']['fixtures']['wins']['home'],
				'away' => $teams['away']['league']['fixtures']['wins']['home'],
			];
			$data['league']['home']['Ничьи'] = [
				'home' => $teams['home']['league']['fixtures']['draws']['home'],
				'away' => $teams['away']['league']['fixtures']['draws']['home'],
			];
			$data['league']['home']['Поражения'] = [
				'home' => $teams['home']['league']['fixtures']['loses']['home'],
				'away' => $teams['away']['league']['fixtures']['loses']['home'],
			];
			$data['league']['home']['Сумма результатов'] = [
				'home' => $data['league']['home']['Игры']['home'] > 0 ? round( ( $data['league']['home']['Победы']['home'] * 2 + $data['league']['home']['Ничьи']['home'] ) / $data['league']['home']['Игры']['home'] , 1 ) : 0,
				'away' => $data['league']['home']['Игры']['away'] > 0 ? round( ( $data['league']['home']['Победы']['away'] * 2 + $data['league']['home']['Ничьи']['away'] ) / $data['league']['home']['Игры']['away'] , 1 ) : 0,
			];
			$data['league']['home']['ГЗ'] = [
				'home' => $teams['home']['league']['goals']['for']['total']['home'],
				'away' => $teams['away']['league']['goals']['for']['total']['home'],
			];
			$data['league']['home']['ГЗ среднее'] = [
				'home' => $teams['home']['league']['goals']['for']['average']['home'],
				'away' => $teams['away']['league']['goals']['for']['average']['home'],
			];
			$data['league']['home']['ГП'] = [
				'home' => $teams['home']['league']['goals']['against']['total']['home'],
				'away' => $teams['away']['league']['goals']['against']['total']['home'],
			];
			$data['league']['home']['ГП среднее'] = [
				'home' => $teams['home']['league']['goals']['against']['average']['home'],
				'away' => $teams['away']['league']['goals']['against']['average']['home'],
			];
			$data['league']['home']['Разница'] = [
				'home' => $data['league']['home']['ГЗ']['home'] - $data['league']['home']['ГП']['home'],
				'away' => $data['league']['home']['ГЗ']['away'] - $data['league']['home']['ГП']['away'],
			];
			$data['league']['home']['Разница среднее'] = [
				'home' => $data['league']['home']['Игры']['home'] > 0 ? round( $data['league']['home']['Разница']['home'] / $data['league']['home']['Игры']['home'], 1 ) : 0,
				'away' => $data['league']['home']['Игры']['away'] > 0 ? round( $data['league']['home']['Разница']['away'] / $data['league']['home']['Игры']['away'], 1 ) : 0,
			];			
			$data['league']['home']['Тотал'] = [
				'home' => $data['league']['home']['ГЗ']['home'] + $data['league']['home']['ГП']['home'],
				'away' => $data['league']['home']['ГЗ']['away'] + $data['league']['home']['ГП']['away'],
			];
			$data['league']['home']['Тотал среднее'] = [
				'home' => $data['league']['home']['Игры']['home'] > 0 ? round( $data['league']['home']['Тотал']['home'] / $data['league']['home']['Игры']['home'], 1 ) : 0,
				'away' => $data['league']['home']['Игры']['away'] > 0 ? round( $data['league']['home']['Тотал']['away'] / $data['league']['home']['Игры']['away'], 1 ) : 0,
			];

			$data['league']['away']['Игры'] = [
				'home' => $teams['home']['league']['fixtures']['played']['away'],
				'away' => $teams['away']['league']['fixtures']['played']['away'],
			];			
			$data['league']['away']['Победы'] = [
				'home' => $teams['home']['league']['fixtures']['wins']['away'],
				'away' => $teams['away']['league']['fixtures']['wins']['away'],
			];
			$data['league']['away']['Ничьи'] = [
				'home' => $teams['home']['league']['fixtures']['draws']['away'],
				'away' => $teams['away']['league']['fixtures']['draws']['away'],
			];
			$data['league']['away']['Поражения'] = [
				'home' => $teams['home']['league']['fixtures']['loses']['away'],
				'away' => $teams['away']['league']['fixtures']['loses']['away'],
			];
			$data['league']['away']['Сумма результатов'] = [
				'home' => $data['league']['away']['Игры']['home'] > 0 ? round( ( $data['league']['away']['Победы']['home'] * 2 + $data['league']['away']['Ничьи']['home'] ) / $data['league']['away']['Игры']['home'] , 1 ) : 0,
				'away' => $data['league']['away']['Игры']['away'] > 0 ? round( ( $data['league']['away']['Победы']['away'] * 2 + $data['league']['away']['Ничьи']['away'] ) / $data['league']['away']['Игры']['away'] , 1 ) : 0,
			];
			$data['league']['away']['ГЗ'] = [
				'home' => $teams['home']['league']['goals']['for']['total']['away'],
				'away' => $teams['away']['league']['goals']['for']['total']['away'],
			];
			$data['league']['away']['ГЗ среднее'] = [
				'home' => $teams['home']['league']['goals']['for']['average']['away'],
				'away' => $teams['away']['league']['goals']['for']['average']['away'],
			];
			$data['league']['away']['ГП'] = [
				'home' => $teams['home']['league']['goals']['against']['total']['away'],
				'away' => $teams['away']['league']['goals']['against']['total']['away'],
			];
			$data['league']['away']['ГП среднее'] = [
				'home' => $teams['home']['league']['goals']['against']['average']['away'],
				'away' => $teams['away']['league']['goals']['against']['average']['away'],
			];
			$data['league']['away']['Разница'] = [
				'home' => $data['league']['away']['ГЗ']['home'] - $data['league']['away']['ГП']['home'],
				'away' => $data['league']['away']['ГЗ']['away'] - $data['league']['away']['ГП']['away'],
			];
			$data['league']['away']['Разница среднее'] = [
				'home' => $data['league']['away']['Игры']['home'] > 0 ? round( $data['league']['away']['Разница']['home'] / $data['league']['away']['Игры']['home'], 1 ) : 0,
				'away' => $data['league']['away']['Игры']['away'] > 0 ? round( $data['league']['away']['Разница']['away'] / $data['league']['away']['Игры']['away'], 1 ) : 0,
			];			
			$data['league']['away']['Тотал'] = [
				'home' => $data['league']['away']['ГЗ']['home'] + $data['league']['away']['ГП']['home'],
				'away' => $data['league']['away']['ГЗ']['away'] + $data['league']['away']['ГП']['away'],
			];
			$data['league']['away']['Тотал среднее'] = [
				'home' => $data['league']['away']['Игры']['home'] > 0 ? round( $data['league']['away']['Тотал']['home'] / $data['league']['away']['Игры']['home'], 1 ) : 0,
				'away' => $data['league']['away']['Игры']['away'] > 0 ? round( $data['league']['away']['Тотал']['away'] / $data['league']['away']['Игры']['away'], 1 ) : 0,
			];

			//сравнение

			$data['compare']['Форма'] = [
				'home' => $compare['form']['home'],
				'away' => $compare['form']['away'],				
			];
			$data['compare']['Атака'] = [
				'home' => $compare['att']['home'],
				'away' => $compare['att']['away'],				
			];
			$data['compare']['Защита'] = [
				'home' => $compare['def']['home'],
				'away' => $compare['def']['away'],				
			];
			$data['compare']['Пуассон'] = [
				'home' => $compare['poisson_distribution']['home'],
				'away' => $compare['poisson_distribution']['away'],				
			];
			$data['compare']['h2h'] = [
				'home' => $compare['h2h']['home'],
				'away' => $compare['h2h']['away'],				
			];						
			$data['compare']['Голы'] = [
				'home' => $compare['goals']['home'],
				'away' => $compare['goals']['away'],				
			];
			$data['compare']['Итого'] = [
				'home' => $compare['total']['home'],
				'away' => $compare['total']['away'],				
			];

			$data['games'] = [
				'games' => [],
				'total'  => [
					'Игры' => ['home' => 0, 'away' => 0],
					'Победы' => ['home' => 0, 'away' => 0],
					'Ничьи' => ['home' => 0, 'away' => 0],
					'Поражения' => ['home' => 0, 'away' => 0],
					'Сумма результатов' => ['home' => 0, 'away' => 0],
					'ГЗ' => ['home' => 0, 'away' => 0],
					'ГЗ среднее' => ['home' => 0, 'away' => 0],
					'ГП' => ['home' => 0, 'away' => 0],
					'ГП среднее' => ['home' => 0, 'away' => 0],
					'Разница' => ['home' => 0, 'away' => 0],
					'Разница среднее' => ['home' => 0, 'away' => 0],
					'Тотал' => ['home' => 0, 'away' => 0],
					'Тотал среднее' => ['home' => 0, 'away' => 0],
				],
				'home'  => [
					'Игры' => ['home' => 0, 'away' => 0],
					'Победы' => ['home' => 0, 'away' => 0],
					'Ничьи' => ['home' => 0, 'away' => 0],
					'Поражения' => ['home' => 0, 'away' => 0],
					'Сумма результатов' => ['home' => 0, 'away' => 0],
					'ГЗ' => ['home' => 0, 'away' => 0],
					'ГЗ среднее' => ['home' => 0, 'away' => 0],
					'ГП' => ['home' => 0, 'away' => 0],
					'ГП среднее' => ['home' => 0, 'away' => 0],
					'Разница' => ['home' => 0, 'away' => 0],
					'Разница среднее' => ['home' => 0, 'away' => 0],
					'Тотал' => ['home' => 0, 'away' => 0],
					'Тотал среднее' => ['home' => 0, 'away' => 0],
				],
				'away'  => [
					'Игры' => ['home' => 0, 'away' => 0],
					'Победы' => ['home' => 0, 'away' => 0],
					'Ничьи' => ['home' => 0, 'away' => 0],
					'Поражения' => ['home' => 0, 'away' => 0],
					'Сумма результатов' => ['home' => 0, 'away' => 0],
					'ГЗ' => ['home' => 0, 'away' => 0],
					'ГЗ среднее' => ['home' => 0, 'away' => 0],
					'ГП' => ['home' => 0, 'away' => 0],
					'ГП среднее' => ['home' => 0, 'away' => 0],
					'Разница' => ['home' => 0, 'away' => 0],
					'Разница среднее' => ['home' => 0, 'away' => 0],
					'Тотал' => ['home' => 0, 'away' => 0],
					'Тотал среднее' => ['home' => 0, 'away' => 0],
				],
			];

			foreach ( $games as $game ) {
				if ( $game['fixture']['timestamp'] < strtotime('-730 days') ) {
					continue;
				}

				if ( $game['teams']['home']['id'] == $event->team_home->api_id ) {
					$home = 'home';
					$away = 'away';
				} else {
					$home = 'away';
					$away = 'home';					
				}

				$data['games']['total']['Игры'][$home] = $data['games']['total']['Игры'][$home] + 1;
				$data['games']['total']['Игры'][$away] = $data['games']['total']['Игры'][$away] + 1;

				$data['games']['home']['Игры'][$home] = $data['games']['home']['Игры'][$home] + 1;
				$data['games']['away']['Игры'][$away] = $data['games']['away']['Игры'][$away] + 1;

				if ( $game['goals']['home'] > $game['goals']['away'] ) {
					$data['games']['total']['Победы'][$home] = $data['games']['total']['Победы'][$home] + 1;
					$data['games']['home']['Победы'][$home] = $data['games']['home']['Победы'][$home] + 1;

					$data['games']['total']['Поражения'][$away] = $data['games']['total']['Поражения'][$away] + 1;
					$data['games']['away']['Поражения'][$away] = $data['games']['away']['Поражения'][$away] + 1;					
				}

				if ( $game['goals']['home'] < $game['goals']['away'] ) {
					$data['games']['total']['Победы'][$away] = $data['games']['total']['Победы'][$away] + 1;
					$data['games']['away']['Победы'][$away] = $data['games']['away']['Победы'][$away] + 1;

					$data['games']['total']['Поражения'][$home] = $data['games']['total']['Поражения'][$home] + 1;
					$data['games']['home']['Поражения'][$home] = $data['games']['home']['Поражения'][$home] + 1;					
				}

				if ( $game['goals']['home'] == $game['goals']['away'] ) {
					$data['games']['total']['Ничьи'][$home] = $data['games']['total']['Ничьи'][$home] + 1;
					$data['games']['total']['Ничьи'][$away] = $data['games']['total']['Ничьи'][$away] + 1;

					$data['games']['home']['Ничьи'][$home] = $data['games']['home']['Ничьи'][$home] + 1;
					$data['games']['away']['Ничьи'][$away] = $data['games']['away']['Ничьи'][$away] + 1;
				}				

				$data['games']['total']['ГЗ'][$home] = $data['games']['total']['ГЗ'][$home] + $game['goals']['home'];
				$data['games']['total']['ГЗ'][$away] = $data['games']['total']['ГЗ'][$away] + $game['goals']['away'];

				$data['games']['home']['ГЗ'][$home] = $data['games']['home']['ГЗ'][$home] + $game['goals']['home'];
				$data['games']['away']['ГЗ'][$away] = $data['games']['away']['ГЗ'][$away] + $game['goals']['away'];

				$data['games']['total']['ГП'][$home] = $data['games']['total']['ГП'][$home] + $game['goals']['away'];
				$data['games']['total']['ГП'][$away] = $data['games']['total']['ГП'][$away] + $game['goals']['home'];

				$data['games']['home']['ГП'][$home] = $data['games']['home']['ГП'][$home] + $game['goals']['away'];
				$data['games']['away']['ГП'][$away] = $data['games']['away']['ГП'][$away] + $game['goals']['home'];	

				$data['games']['games'][] = [
					'date' => wp_date( 'd.m.Y', $game['fixture']['timestamp'] ),
					'name' => $game['teams']['home']['name'] . ' - ' . $game['teams']['away']['name'],
					'referee' => $game['fixture']['referee'],
					'score' => $game['goals']['home'] . ':' . $game['goals']['away'],
					'day' => $game['league']['name'] . '. ' . $game['league']['round']
				];			

			}
		
			$data['games']['total']['Разница']['home'] = $data['games']['total']['ГЗ']['home'] - $data['games']['total']['ГП']['home'];
			$data['games']['total']['Тотал']['home'] = $data['games']['total']['ГЗ']['home'] + $data['games']['total']['ГП']['home'];
			if ( $data['games']['total']['Игры']['home'] > 0 ) {
				$data['games']['total']['Сумма результатов']['home'] = round( ( $data['games']['total']['Победы']['home'] * 2 + $data['games']['total']['Ничьи']['home'] ) / $data['games']['total']['Игры']['home'] , 1 );
				$data['games']['total']['ГЗ среднее']['home'] = round( $data['games']['total']['ГЗ']['home'] / $data['games']['total']['Игры']['home'] , 1 );
				$data['games']['total']['ГП среднее']['home'] = round( $data['games']['total']['ГП']['home'] / $data['games']['total']['Игры']['home'] , 1 );
				
				$data['games']['total']['Разница среднее']['home'] = round( $data['games']['total']['Разница']['home'] / $data['games']['total']['Игры']['home'] , 1 );
				
				$data['games']['total']['Тотал среднее']['home'] = round( $data['games']['total']['Тотал']['home'] / $data['games']['total']['Игры']['home'] , 1 ); 
			}

			$data['games']['total']['Разница']['away'] = $data['games']['total']['ГЗ']['away'] - $data['games']['total']['ГП']['away'];
			$data['games']['total']['Тотал']['away'] = $data['games']['total']['ГЗ']['away'] + $data['games']['total']['ГП']['away'];
			if ( $data['games']['total']['Игры']['away'] > 0 ) {
				$data['games']['total']['Сумма результатов']['away'] = round( ( $data['games']['total']['Победы']['away'] * 2 + $data['games']['total']['Ничьи']['away'] ) / $data['games']['total']['Игры']['away'] , 1 );
				$data['games']['total']['ГЗ среднее']['away'] = round( $data['games']['total']['ГЗ']['away'] / $data['games']['total']['Игры']['away'] , 1 );
				$data['games']['total']['ГП среднее']['away'] = round( $data['games']['total']['ГП']['away'] / $data['games']['total']['Игры']['away'] , 1 );			
				$data['games']['total']['Разница среднее']['away'] = round( $data['games']['total']['Разница']['away'] / $data['games']['total']['Игры']['away'] , 1 );			
				$data['games']['total']['Тотал среднее']['away'] = round( $data['games']['total']['Тотал']['away'] / $data['games']['total']['Игры']['away'] , 1 );
			}

			$data['games']['home']['Разница']['home'] = $data['games']['home']['ГЗ']['home'] - $data['games']['home']['ГП']['home'];
			$data['games']['home']['Тотал']['home'] = $data['games']['home']['ГЗ']['home'] + $data['games']['home']['ГП']['home'];
			if ( $data['games']['home']['Игры']['home'] > 0 ) {
				$data['games']['home']['Сумма результатов']['home'] = round( ( $data['games']['home']['Победы']['home'] * 2 + $data['games']['home']['Ничьи']['home'] ) / $data['games']['home']['Игры']['home'] , 1 );

				$data['games']['home']['ГЗ среднее']['home'] = round( $data['games']['home']['ГЗ']['home'] / $data['games']['home']['Игры']['home'] , 1 );
				$data['games']['home']['ГП среднее']['home'] = round( $data['games']['home']['ГП']['home'] / $data['games']['home']['Игры']['home'] , 1 );
				$data['games']['home']['Разница среднее']['home'] = round( $data['games']['home']['Разница']['home'] / $data['games']['home']['Игры']['home'] , 1 );
				$data['games']['home']['Тотал среднее']['home'] = round( $data['games']['home']['Тотал']['home'] / $data['games']['home']['Игры']['home'] , 1 ); 				
			}
			

			$data['games']['home']['Разница']['away'] = $data['games']['home']['ГЗ']['away'] - $data['games']['home']['ГП']['away'];
			$data['games']['home']['Тотал']['away'] = $data['games']['home']['ГЗ']['away'] + $data['games']['home']['ГП']['away'];
			if ( $data['games']['home']['Игры']['away'] > 0 ) {
				$data['games']['home']['Сумма результатов']['away'] = round( ( $data['games']['home']['Победы']['away'] * 2 + $data['games']['home']['Ничьи']['away'] ) / $data['games']['home']['Игры']['away'] , 1 );
				$data['games']['home']['ГЗ среднее']['away'] = round( $data['games']['home']['ГЗ']['away'] / $data['games']['home']['Игры']['away'] , 1 );
				$data['games']['home']['ГП среднее']['away'] = round( $data['games']['home']['ГП']['away'] / $data['games']['home']['Игры']['away'] , 1 );			
				$data['games']['home']['Разница среднее']['away'] = round( $data['games']['home']['Разница']['away'] / $data['games']['home']['Игры']['away'] , 1 );			
				$data['games']['home']['Тотал среднее']['away'] = round( $data['games']['home']['Тотал']['away'] / $data['games']['home']['Игры']['away'] , 1 );
			}


			$data['games']['away']['Разница']['home'] = $data['games']['away']['ГЗ']['home'] - $data['games']['away']['ГП']['home'];
			$data['games']['away']['Тотал']['home'] = $data['games']['away']['ГЗ']['home'] + $data['games']['away']['ГП']['home'];
			if ( $data['games']['away']['Игры']['home'] > 0 ) {
				$data['games']['away']['Сумма результатов']['home'] = round( ( $data['games']['away']['Победы']['home'] * 2 + $data['games']['away']['Ничьи']['home'] ) / $data['games']['away']['Игры']['home'] , 1 );
				$data['games']['away']['ГЗ среднее']['home'] = round( $data['games']['away']['ГЗ']['home'] / $data['games']['away']['Игры']['home'] , 1 );
				$data['games']['away']['ГП среднее']['home'] = round( $data['games']['away']['ГП']['home'] / $data['games']['away']['Игры']['home'] , 1 );			
				$data['games']['away']['Разница среднее']['home'] = round( $data['games']['away']['Разница']['home'] / $data['games']['away']['Игры']['home'] , 1 );			
				$data['games']['away']['Тотал среднее']['home'] = round( $data['games']['away']['Тотал']['home'] / $data['games']['away']['Игры']['home'] , 1 ); 
			}


			$data['games']['away']['Разница']['away'] = $data['games']['away']['ГЗ']['away'] - $data['games']['away']['ГП']['away'];
			$data['games']['away']['Тотал']['away'] = $data['games']['away']['ГЗ']['away'] + $data['games']['away']['ГП']['away'];
			if ( $data['games']['away']['Игры']['away'] > 0 ) {
				$data['games']['away']['Сумма результатов']['away'] = round( ( $data['games']['away']['Победы']['away'] * 2 + $data['games']['away']['Ничьи']['away'] ) / $data['games']['away']['Игры']['away'] , 1 );
				$data['games']['away']['ГЗ среднее']['away'] = round( $data['games']['away']['ГЗ']['away'] / $data['games']['away']['Игры']['away'] , 1 );
				$data['games']['away']['ГП среднее']['away'] = round( $data['games']['away']['ГП']['away'] / $data['games']['away']['Игры']['away'] , 1 );			
				$data['games']['away']['Разница среднее']['away'] = round( $data['games']['away']['Разница']['away'] / $data['games']['away']['Игры']['away'] , 1 );			
				$data['games']['away']['Тотал среднее']['away'] = round( $data['games']['away']['Тотал']['away'] / $data['games']['away']['Игры']['away'] , 1 );
			} 						

			$data['feed']  = $event;

		}

		return $data;
	}


}
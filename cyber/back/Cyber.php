<?php
class Cyber {
	static $options;
	static $posts_sections;
	static $predicts_sections;
	static $websocket;
	
	static $sports;
	static $sports_betboom;
	static $sports_winline;
	static $sports_fonbet;


	static $posts_names = [
		'news',
		'posts'
	];

	static $predicts_names = [
		'predicts'
	];



	static function init() {
		self::loadOptions();
		self::loadSections();
		self::loadSports();
		add_filter('template_include', ['Cyber', 'setTemplates']);
	}

	static function getOptions() {
		return get_option('cyber');
	}

	static function loadOptions() {
		self::$options = self::getOptions();
	}

	static function loadSports() {
		self::$sports = [];
		self::$sports_betboom = [];

		$sports_options = get_option('cyber_sports');

		foreach($sports_options as $sport) {
			self::$sports[$sport['slug']] = [
				'name'    => $sport['name'],
				'betboom' => $sport['betboom'],
				'winline' => $sport['winline'],
				'fonbet'  => $sport['fonbet'],
			];
			if (isset($sport['betboom'])) {
				self::$sports_betboom[$sport['betboom']] = [
					'name' => $sport['name'],
					'slug' => $sport['slug']
				];
			}
			if (isset($sport['winline'])) {
				self::$sports_winline[$sport['winline']] = [
					'name' => $sport['name'],
					'slug' => $sport['slug']
				];
			}
			if (isset($sport['fonbet'])) {
				self::$sports_fonbet[$sport['fonbet']] = [
					'name' => $sport['name'],
					'slug' => $sport['slug']
				];
			}			
		}
	}	

	static function getOption($option) {
		if(is_array(self::$options) && isset(self::$options[$option]) && !empty(self::$options[$option])) {
			return self::$options[$option];
		}
		return false;
	}

	static function loadSections() {
		self::$posts_sections = [];
		foreach (self::$posts_names as $posts_name) {
			if (self::getOption($posts_name . '_cat')) {
				$term = get_term_by('id', self::getOption($posts_name . '_cat'), 'category' );
				$section = [
					'cat_id'    => self::getOption($posts_name . '_cat'),
					'cat_link'  => get_term_link($term),
					'cat_name'  => get_term_by('id', self::getOption($posts_name . '_cat'), 'category' )->name,
					'page_id'   => self::getOption($posts_name . '_page'),
					'page_link' => get_the_permalink(self::getOption($posts_name . '_page')),
					'page_name' => get_the_title(self::getOption($posts_name . '_page')),
				];
				self::$posts_sections[$posts_name] = $section;
				$section_children = get_term_children(self::getOption($posts_name . '_cat'), 'category');
				foreach ($section_children as $section_child) {
					$term = get_term_by('id', $section_child, 'category' );
					$section = [
						'cat_id'    => $section_child,
						'cat_link'  => get_the_permalink(self::getOption($posts_name . '_page')) . $term->slug . '/',
						'cat_name'  => $term->name,
						'page_id'   => self::getOption($posts_name . '_page'),
						'page_link' => get_the_permalink(self::getOption($posts_name . '_page')) . $term->slug . '/',
						'page_name' => $term->name,
					];
					self::$posts_sections[$posts_name]['children'][$term->slug] = $section;					
				}
			}			
		}

		self::$predicts_sections = [];

		foreach (self::$predicts_names as $predicts_name) {
			if (self::getOption($predicts_name . '_cat')) {
				$term_id = self::getOption($predicts_name . '_cat');
				$slice_id = get_term_meta( $term_id, 'term_slice', true );
				$section = [
					'cat_id'     => $term_id,
					'slice_id'   => $slice_id,
					'slice_link' => get_permalink( $slice_id ),
					'page_id'    => self::getOption($predicts_name . '_page'),
					'page_link'  => get_the_permalink(self::getOption($predicts_name . '_page')),
					'page_name'  => get_the_title(self::getOption($predicts_name . '_page')),
				];
				self::$predicts_sections[$predicts_name] = $section;

				$slice_args = [
					'posts_per_page' => -1,
					'post_type' => 'filter_bookmakers',
					'post_status' => 'publish',
					'meta_query' => [
						'relation' => 'AND',
						[
							'key' => 'predicts_type',
							'value' => 'predicts'
						],
						[
							'key' => 'sport_type',
							'value' => $term_id
						],
					]
				];
				$section_children = get_posts( $slice_args );

				foreach ($section_children as $section_child) {
					$child_id = get_post_meta($section_child->ID, 'tournament', 1);
					if ($child_id) {
						$term = get_term_by('id', $child_id, 'tournament' );
						$child_slice_id = get_term_meta( $child_id, 'term_slice', true );
						$section = [
							'cat_id'     => $child_id,
							'slice_id'   => $child_slice_id,
							'slice_link' => get_permalink( $child_slice_id ),
							'page_id'    => self::getOption($predicts_name . '_page'),
							'page_link'  => get_the_permalink(self::getOption($predicts_name . '_page')) . $section_child->post_name . '/',
							'page_name'  => get_the_title($child_slice_id),
						];
						self::$predicts_sections[$predicts_name]['children'][$section_child->post_name] = $section;
					}					
				}				
			}			
		}		
	}

	static function setTemplates($template) {

		if(self::isMain()) {
			$template = get_template_directory() . '/templates/cyber/page-news.php';
		}
		if(self::isPosts()) {
			$template = get_template_directory() . '/templates/cyber/page-posts.php';
		}
		if(self::isPredicts()) {
			$template = get_template_directory() . '/templates/cyber/page-predicts.php';
		}
		if(self::isGames()) {
			$template = get_template_directory() . '/templates/cyber/page-games.php';
		}				
		return $template;
	}

	static function isMain() {
		if(self::getOption('news_page') && is_page() && get_the_ID() == self::getOption('news_page')) {
			return true;
		}
	}

	static function isPosts() {
		if(self::getOption('posts_page') && is_page() && get_the_ID() == self::getOption('posts_page')) {
			return true;
		}
	}	

	static function isPredicts() {
		if(self::getOption('predicts_page') && is_page() && get_the_ID() == self::getOption('predicts_page')) {
			return true;
		}
	}

	static function isGames() {
		if(self::getOption('games_page') && is_page() && get_the_ID() == self::getOption('games_page')) {
			return true;
		}
	}	

	static function template($template, $data = []) {
		$file = get_template_directory() . '/templates/cyber/' . $template . '.php';
		if(file_exists($file)) {
			extract($data);
			include $file;
		}
	}

	static function getMenu() {
		$header_items = [];
		if(self::getOption('news_page')) {
			$header_items[] = [
				'name' => 'Новости',
				'link' => get_the_permalink(self::getOption('news_page')),
				'img'  => 'news'
			];
		}
		if(self::getOption('predicts_page')) {
			$header_items[] = [
				'name' => 'Прогнозы',
				'link' => get_the_permalink(self::getOption('predicts_page')),
				'img'  => 'predicts'
			];
		}		
		if(self::getOption('posts_page')) {
			$header_items[] = [
				'name' => 'Статьи',
				'link' => get_the_permalink(self::getOption('posts_page')),
				'img'  => 'posts'
			];
		}
		if(self::getOption('games_page')) {
			$header_items[] = [
				'name' => 'Матчи',
				'link' => get_the_permalink(self::getOption('games_page')),
				'img'  => 'games'
			];
		}
		return $header_items;		
	}	

	static function redirectCategory($term_id) {
		if (is_category($term_id)) {
			foreach (self::$posts_sections as $posts_section) {
				if ($term_id == $posts_section['cat_id']) {
					wp_redirect($posts_section['page_link']);
				}
				if (isset($posts_section['children'])) {
					foreach ($posts_section['children'] as $child) {
						if ($term_id == $child['cat_id']) {
							wp_redirect($child['page_link']);
						} 
					}
				}
			}
		} else {
			foreach (self::$predicts_sections as $predicts_section) {
				if ($term_id == $predicts_section['slice_id']) {
					wp_redirect($predicts_section['page_link']);
				}
				if (isset($predicts_section['children'])) {
					foreach ($predicts_section['children'] as $child) {
						if ($term_id == $child['slice_id']) {
							wp_redirect($child['page_link']);
						} 
					}
				}				
			}			
		}
	}

	static function isInCategory($post_id) {
		if (is_single($post_id) && get_post_type($post_id) == 'post') {
			foreach (self::$posts_sections as $posts_section) {
				if (get_the_category($post_id)[0]->cat_ID == $posts_section['cat_id']) {
					return $posts_section;
				}
				if (isset($posts_section['children'])) {
					foreach ($posts_section['children'] as $child) {
						if (get_the_category($post_id)[0]->cat_ID == $child['cat_id']) {
							return $posts_section;
						}
					}
				}
			}
		}
		if (is_single($post_id) && get_post_type($post_id) == 'predicts') {
			foreach (self::$predicts_sections as $predicts_section) {
				if (get_post_meta( $post_id, 'pr_sport_type', true ) == $predicts_section['cat_id']) {
					return $predicts_section;
				}
				if (isset($predicts_section['children'])) {
					foreach ($predicts_section['children'] as $child) {
						if (get_post_meta( $post_id, 'pr_sport_type', true ) == $child['cat_id']) {
							return $predicts_section;
						}
					}
				}				
			}
		}		
		return false;
	}

	static function getGames($use_cache = false, $cache_time = 1800) {
		
		$sport = get_query_var('sport');
		$filter = !empty($sport);

		$cache_name = 'cyber_games_' . $sport;

		$cache = new APCache($cache_name, $cache_time);

		if ($use_cache && $cache->file_exists() && ! $cache->need_update() && !is_null( json_decode($cache->get()) )) {
			return json_decode($cache->get(), true);
		} else {
			
			$games = [];
			$games = self::setGamesBetboom($games, $filter, $sport);
			$games = self::setGamesWinline($games, $filter, $sport);
			$games = self::setGamesFonbet($games, $filter, $sport);

			ksort($games);

			$cache->update( json_encode($games) );
			return $games;
		}
	}

	static function setGamesBetboom($games, $filter = false, $sport = '') {

		$betboom_options = new Options('betboom');

		$bk_id 	  = $betboom_options->getOption('bk_id');
		$bk_link  = $betboom_options->getOption('link');
		
		$url      = $betboom_options->getOption('feed');
		$feed_ids = explode(',', $betboom_options->getOption('feeds'));
		$feeds    = [];


		foreach ($feed_ids as $feed_id) {
			$request_array = ['id' => $feed_id];
			$request = json_encode($request_array, JSON_UNESCAPED_UNICODE);
			$feeds[] = self::getCurl($url, $request);
		}

		if (count($feeds)) {
			foreach ($feeds as $feed) {
				if (isset($feed->code) && $feed->code == 200) {
					$feed_games = $feed->data->SportList;
					foreach ($feed_games as $feed_game) {
						if ($filter && $feed_game->RegionList[0]->Id != self::$sports[$sport]['betboom']) {
							continue;
						}
						if (! isset(self::$sports_betboom[$feed_game->RegionList[0]->Id])) {
							continue;
						}
						$game_sport  = $feed_game->RegionList[0];
						$game_tour   = $game_sport->CompetitionModelsList[0];
						$game_match  = $game_tour->MatchModelsList[0];
						$game_stakes = $game_match->StakeModelsList;

						if (count($game_stakes)) {
							usort($game_stakes, fn($a, $b) => $a->TypeId <=> $b->TypeId);
							$game_date = substr($game_match->Date, 6, -10);
							$game_id = $game_date . $game_match->Id;

							$game_stake = [0, 1];

							foreach($game_stakes[0]->OddModelsList as $k => $stake) {
								if (strpos($stake->Type, 'П1')) $game_stake[0] = $k;
								if (strpos($stake->Type, 'П2')) $game_stake[1] = $k;
							}


							$games[$game_id] = [
								'game_id'           => $game_match->Id,
								'game_active'       => $game_match->Date,
								'game_type'         => ($game_date < time()) ? 'live' : false,
								'game_date'         => self::setDate($game_date),
								'game_stream'       => $game_match->IsLiveStreamExists ?? false,
								'game_stream_url'   => '',
								'sport_name'        => $game_sport->Name,
								'sport_img'         => SNS_URL . '/img/cyber/' . self::$sports_betboom[$game_sport->Id]['slug'] . '.png',
								'sport_id'	        => $game_sport->Id,
								'tournament_name'   => $game_tour->Name,
								'tournament_img'    => '',
								'game_home_name'    => $game_match->Home->Name,
								'game_home_score'   => '',
								'game_home_img'     => '',
								'game_home_kf'      => $game_stakes[0]->OddModelsList[$game_stake[0]]->Odd,
								'game_home_kf_name' => $game_stakes[0]->OddModelsList[$game_stake[0]]->Type,
								'game_away_name'    => $game_match->Away->Name,
								'game_away_score'   => '',
								'game_away_img'     => '',
								'game_away_kf'      => $game_stakes[0]->OddModelsList[$game_stake[1]]->Odd,
								'game_away_kf_name' => $game_stakes[0]->OddModelsList[$game_stake[1]]->Type,
								'game_bk_id'        => $bk_id,
								'game_bk_link'      => $bk_link,
							];	
						}				
					}
				}
			}
		}	

		return $games;
	}

	static function setGamesWinline($games, $filter = false, $sport = '') {

		$winline_options = new Options('winline');

		$bk_id 	  = $winline_options->getOption('bk_id');
		$bk_link  = $winline_options->getOption('link');
		
		$url      = $winline_options->getOption('feed');
		$feed_ids = explode(',', $winline_options->getOption('feeds'));
		$feeds    = [];

		foreach ($feed_ids as $feed_id) {
			$feeds[] = self::getContents($url . $feed_id);
		}
		//return $feeds;
		if (count($feeds)) {
			foreach ($feeds as $feed) {
				if (isset($feed['event'])) {

					if(isset($feed['event'][0])) {
						$feed_games = $feed['event'];
					} else {
						$feed_games[0] = $feed['event'];
					}
					foreach ($feed_games as $feed_game) {
						if ($filter && $feed_game['country'] != self::$sports[$sport]['winline']) continue;
						if (! isset(self::$sports_winline[$feed_game['country']])) continue;

						$game_stakes = $feed_game['odds']['line'];

						if (is_array($game_stakes) && count($game_stakes)) {

							$game_date = $feed_game['datetime'];
							$game_id = strtotime($game_date) . $feed_game['@attributes']['id'];
							
							if (isset($game_stakes[0])) {
								$game_stake = $game_stakes[0]['@attributes'];
							} else {
								$game_stake = $game_stakes['@attributes'];
							}
							$stake_name = $game_stake['freetext'];

							$game_stake_values = [
								0 => [
									'odd' => $game_stake['odd1'],
									'name' => $stake_name 
								],
								1 => [
									'odd' => $game_stake['odd2'],
									'name' => $stake_name									 
								],								
							];

							if( strpos($stake_name, '1Х2') !== false || strpos($stake_name, '1X2') !== false) {
								$text = ['1Х2', '1X2'];
								$game_stake_values[0]['name'] = str_replace($text, '', $stake_name) . ' П1';
								$game_stake_values[1]['name'] = str_replace($text, '', $stake_name) . ' П2';
								if (count($game_stake) > 5) {
									$game_stake_values[1]['odd'] = $game_stake['odd3'];
								}
							}
							if(strpos($stake_name, '12') !== false ) {
								$game_stake_values[0]['name'] = trim(str_replace('12', '', $stake_name)) . ' П1';
								$game_stake_values[1]['name'] = trim(str_replace('12', '', $stake_name)) . ' П2';
							}
						
							if(isset($game_stake->value) || strpos($stake_name, 'Точный счет') !== false) {
								$game_stake_values[0]['name'] = $stake_name . ' ' . $game_stake['name1'];
								$game_stake_values[1]['name'] = $stake_name . ' ' . $game_stake['name2'];
							}							


							$games[$game_id] = [
								'game_id'           => $feed_game['@attributes']['id'],
								'game_active'       => $game_date,
								'game_type'         => $feed_game['isLive'] ? 'live' : false,
								'game_date'         => self::setDate(strtotime($game_date)),
								'game_stream'       => '',
								'game_stream_url'   => '',
								'sport_name'        => $feed_game['country'],
								'sport_img'         => SNS_URL . '/img/cyber/' . self::$sports_winline[$feed_game['country']]['slug'] . '.png',
								'sport_id'	        => $feed_game['country'],
								'tournament_name'   => $feed_game['competition'],
								'tournament_img'    => '',
								'game_home_name'    => $feed_game['team1'],
								'game_home_score'   => '',
								'game_home_img'     => 'https://winline.ru/api/cls/event/1/' . $feed_game['@attributes']['id'],
								'game_home_kf'      => $game_stake_values[0]['odd'],
								'game_home_kf_name' => $game_stake_values[0]['name'],
								'game_away_name'    => $feed_game['team2'],
								'game_away_score'   => '',
								'game_away_img'     => 'https://winline.ru/api/cls/event/2/' . $feed_game['@attributes']['id'],
								'game_away_kf'      => $game_stake_values[1]['odd'],
								'game_away_kf_name' => $game_stake_values[1]['name'],
								'game_bk_id'        => $bk_id,
								'game_bk_link'      => $bk_link,
							];	
						}				
					}
				}
			}
		}	

		return $games;
	}

	static function setGamesFonbet($games, $filter = false, $sport = '') {

		$fonbet_options = new Options('fonbet');

		$bk_id 	  = $fonbet_options->getOption('bk_id');
		$bk_link  = $fonbet_options->getOption('link');
		$cyber_ids = explode(',', $fonbet_options->getOption('feeds'));
		
		$url = $fonbet_options->getOption('feed');

	
		$events_object = self::getCurl($url, '', false);
		$events = $events_object->event;

		$feed_games = [];
		foreach($events as $event) {
			if (in_array($event->sport_type_id, $cyber_ids)) {
				$feed_games[] = $event;
			}
		}

		//return $feed_games;
		if (count($feed_games)) {
			foreach ($feed_games as $feed_game) {
				if(isset($feed_game->{'outcome_1'}) && isset($feed_game->{'outcome_1'}->{'@attributes'}) && isset($feed_game->{'outcome_1'}->{'@attributes'}->{'factor_value'}) && $feed_game->{'outcome_1'}->{'@attributes'}->{'factor_value'} != '') {
					
					$game_titles = explode('. ', $feed_game->topic);

					if ($filter && $game_titles[0] != self::$sports[$sport]['fonbet']) {
						continue;
					}
					if (! isset(self::$sports_fonbet[$game_titles[0]])) {
						continue;
					}
					$game_sport  = self::$sports_fonbet[$game_titles[0]]['name'];
					$game_tour   = $game_titles[1];


						$game_date = $feed_game->start_date_timestamp;
						$game_id = $game_date . $feed_game->event_id;

						$game_stakes = [
							0 => $feed_game->outcome_1->{'@attributes'}->factor_value,
							1 => $feed_game->outcome_3->{'@attributes'}->factor_value,
						];

						$games[$game_id] = [
							'game_id'           => $feed_game->event_id,
							'game_active'       => '',
							'game_type'         => $feed_game->place,
							'game_date'         => self::setDate($game_date),
							'game_stream'       => '',
							'game_stream_url'   => '',
							'sport_name'        => $game_sport,
							'sport_img'         => SNS_URL . '/img/cyber/' . self::$sports_fonbet[$game_titles[0]]['slug'] . '.png',
							'sport_id'	        => '',
							'tournament_name'   => $game_tour,
							'tournament_img'    => '',
							'game_home_name'    => $feed_game->team1,
							'game_home_score'   => '',
							'game_home_img'     => 'https://logo.ajaxfeed.com/logos/29086/' . rawurlencode($feed_game->team1) . '.png?left',
							'game_home_kf'      => $game_stakes[0],
							'game_home_kf_name' => 'Исход П1',
							'game_away_name'    => $feed_game->team2,
							'game_away_score'   => '',
							'game_away_img'     => 'https://logo.ajaxfeed.com/logos/29086/' . rawurlencode($feed_game->team2) . '.png?right',
							'game_away_kf'      => $game_stakes[1],
							'game_away_kf_name' => 'Исход П2',
							'game_bk_id'        => $bk_id,
							'game_bk_link'      => $bk_link,
						];	
									
				}
			}
		}	

		return $games;
	}

	static function getCurl($url, $request, $is_post = true) {
		$headers = array(
	       "accept: text/plain",
	       "Content-Type: application/json",
	    );    

	    $curl = curl_init($url);

	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);	    
	    
	    if ($is_post) {	 
	    	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);   	
	    	curl_setopt($curl, CURLOPT_URL, $url);
	    	curl_setopt($curl, CURLOPT_POST, $is_post);
	    	curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
	    } 
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	    $responce = curl_exec($curl);
	    curl_close($curl);
	    return json_decode($responce);
	}

	static function getContents($url, $is_xml = true) {
		$opts = [
			"http" => [
				"method" => "GET",
				"header" => "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9\r\n" .
				            "Accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7\r\n" .
				            "Accept-Encoding: gzip, deflate, br\r\n"
			]
		];
		$context = stream_context_create( $opts );
		if ($file_content = file_get_contents( $url, false, $context )) {
			$xml = preg_replace( '/.+<winline>/', '<winline>', gzdecode( $file_content ) );
			
			$xmlstring = mb_convert_encoding( $xml, 'HTML-ENTITIES', "UTF-8" );
			if ($is_xml) {
				$content = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
				$json = json_encode($content);
			} else {
				$json = $xmlstring;
			}
			
			return json_decode($json, true);
		}

		return false;	

	}

	static function setDate($date) {
		$d = 'd.m.Y';
	  	$date_now = date_format(date_create("now"), $d);
	 	$date_yesterday = date_format(date_create("tomorrow"), $d);
	 	$time_date = intval($date);
		$the_date = wp_date($d, $time_date);

		$post_date = wp_date('j F Y в H:i', $time_date);
		if ($date_now == $the_date) {
		    $post_date = 'Сегодня в ' . wp_date('H:i', $time_date);
		} elseif ($date_yesterday == $the_date) {
		    $post_date = 'Завтра в ' . wp_date('H:i', $time_date);
		}
		return $post_date;
	}

/*	static function setGames($tournaments_names) {
		$sports = [];
		$tours = [];
		$games = [];
		$tours_array = [];	

		foreach ($tournaments_names as $tournament_name ) {
			$data_prematch = [
				'type' => 'subscribe_sport',
				'subscribe_sport' => [
					'type' => 'prematch',
					'sport_id' => $tournament_name,
					'uid' => '4'
				]
			];
			self::$websocket->send(json_encode($data_prematch));
			$result_prematch = json_decode(self::$websocket->receive());
			if (isset($result_prematch->subscribe_sport->sport->tournaments)) {
				$sports = array_merge($sports, $result_prematch->subscribe_sport->sport->tournaments);
			}

			$data_live = [
				'type' => 'subscribe_sport',
				'subscribe_sport' => [
					'type' => 'live',
					'sport_id' => $tournament_name,
					'uid' => '4'
				]
			];
			self::$websocket->send(json_encode($data_live));
			$result_live = json_decode(self::$websocket->receive());
			if (isset($result_live->subscribe_sport->sport->tournaments)) {
				$sports = array_merge($sports, $result_live->subscribe_sport->sport->tournaments);
			}	
		}

		foreach ($sports as $sport) {
			$tours_array[] = [
				'tour_id' => $sport->info->id,
				'sport_id' => $sport->info->sport_id
			];
		}
		array_unique($tours, SORT_REGULAR);

		foreach ($tours_array as $tour_array) {
			$data_tours = [
				'type' => 'subscribe_full_tournament',
				'subscribe_full_tournament' => [
					'tournament_id' => $tour_array['tour_id'],
					'sport_id' => $tour_array['sport_id'],
					'uid' => '5'
				]
			];
			self::$websocket->send(json_encode($data_tours));
			$tours[] = json_decode(self::$websocket->receive());			
		}

		foreach ($tours as $tour) {
			if ( isset($tour->subscribe_full_tournament) && $tour->subscribe_full_tournament->code == 200 ) {
				$tour_info = $tour->subscribe_full_tournament->prematch->tournament->info;
				$sport_info = $tour->subscribe_full_tournament->prematch->sport->info;

				if ( isset($tour->subscribe_full_tournament->live->tournament->matches) && isset($tour->subscribe_full_tournament->prematch->tournament->matches) ) {
					$tour_matches = array_merge($tour->subscribe_full_tournament->live->tournament->matches, $tour->subscribe_full_tournament->prematch->tournament->matches);
				} else {
					if ( isset($tour->subscribe_full_tournament->live->tournament->matches) ) {
						$tour_matches = $tour->subscribe_full_tournament->live->tournament->matches;
					}
					if ( isset($tour->subscribe_full_tournament->prematch->tournament->matches) ) {
						$tour_matches = $tour->subscribe_full_tournament->prematch->tournament->matches;
					}					
				}
				foreach ($tour_matches as $tour_match) {
					if (isset($tour_info->abbreviation)) {
						$games[$tour_match->order] = [
							'game_id'           => $tour_match->id,
							'game_active'       => $tour_match->active,
							'game_type'         => $tour_match->type,
							'game_date'         => self::setDate($tour_match->start_dttm),
							'game_stream'       => $tour_match->stream->name ?? false,
							'game_stream_url'   => $tour_match->stream->stream_url ?? '',
							'sport_name'        => $sport_info->name,
							'sport_img'         => $sport_info->icon_path,
							'tournament_name'   => $tour_info->abbreviation,
							'tournament_img'    => $tour_info->icon_path,
							'game_home_name'    => $tour_match->teams[0]->name,
							'game_home_score'   => $tour_match->teams[0]->score,
							'game_home_img'     => $tour_match->teams[0]->image,
							'game_home_kf'      => $tour_match->stakes[0]->factor,
							'game_home_kf_name' => $tour_match->stakes[0]->stake_type,
							'game_away_name'    => $tour_match->teams[1]->name,
							'game_away_score'   => $tour_match->teams[1]->score,
							'game_away_img'     => $tour_match->teams[1]->image,
							'game_away_kf'      => $tour_match->stakes[1]->factor,
							'game_away_kf_name' => $tour_match->stakes[1]->stake_type,
						];
					}					
				}
			}
		}
		ksort($games);

		return $games;
	}*/

}
Cyber::init();

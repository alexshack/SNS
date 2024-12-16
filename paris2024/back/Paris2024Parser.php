<?php

class Paris2024Parser extends Parser {

	static $url = 'https://olympteka.ru/olymp/paris2024';

	function getSchedule() {

		//Paris2024Event::query()->delete();

		$csv  = file_get_contents( 'https://docs.google.com/spreadsheets/d/1Tf_HNjqmyzTaIfvVVXo7uuVXMqhYFQGZV4aH-J68-Kk/export?format=csv&gid=0' );
		$csv  = explode( "\r\n", $csv );
		$data = array_map( 'str_getcsv', $csv );


		return $data;

	}

	function getScheduleOlymp() {

		$data = [];
		$date = '2024-07-26';

		for ( $d = 1; $d < 18; $d++ ) { 

			$document = $this->getDocument( 'https://olympteka.ru/olymp/paris2024/shedule/day-' . $d . '.html' );
			//$document = $this->getDocument( 'https://olympteka.ru/olymp/paris2024/day/' . $d . '.html' );

			if ( empty( $document ) ) {
				continue;
			}

			$sports = $document->find( '.sh-day' );
			$tables = $document->find( '.tb-eo' );
			

			if ( count ( $sports ) ) {

				for ( $i = 0; $i < count($sports); $i++ ) { 
					
					$sport = $sports[$i]->text();
					$rows  = $tables[$i]->find( 'tr' );

					foreach ( $rows as $row ) {

						$class = $row->getAttribute('class');
						$cols = $row->find( 'td' );
						if ( count( $cols ) ) {

							$text_all = $cols[3]->text();
							$text_all = preg_replace('/\s+/', ' ', $text_all);
							$text_all = trim( $text_all );
							$text_all = ucfirst( $text_all );
							$text_desc = $cols[3]->find( '.sh-rd' )[0]->text();
							$text_desc = trim( $text_desc );
							$text = str_replace( $text_desc, '. ' . $text_desc, $text_all);
							$text = str_replace( ' .', '.', $text);
							$text = str_replace( 'мужчины,', 'Мужчины.', $text);
							$text = str_replace( 'женщины,', 'Женщины.', $text);
							$text = str_replace( 'микст,', 'Микст.', $text);
							$final = '';

							if ( str_contains( $class, 'lgold' ) ) {
								$final = '1';
							}

							$data[] = [
								wp_date('Y-m-d H:i:s', strtotime( $date . ' ' . $cols[1]->text() ) ),
								$sport,
								$text,
								$final
							];
						}

					}

				}
			}

			$date = wp_date('Y-m-d', strtotime( $date . ' +1 day' ) );
		}


		$file = WP_CONTENT_DIR . '/uptimer_parse_2.log';
		$content = '';
		foreach ( $data as $line ) {
			$content .= $line[0] . '***' . $line[1] . '***' . $line[2] . '***'. $line[3] . "\n";
		}
		file_put_contents( $file, $content, FILE_APPEND );	

		return $data;

	}

	function getMedals() {

		$document = $this->getDocument( implode( '/', [ self::$url, 'medals.html' ] ) );

		if ( empty( $document ) ) {
			return false;
		}

		$table = $document->find( '.tb-medals-2' )[0];

		$rows = $table->find( 'tbody tr' );

		$data = [];
		foreach ( $rows as $row ) {

			$cols = $row->find( 'td' );

			$data[] = [
				'country' => $cols[1]->text(),
				'gold'    => $cols[2]->text(),
				'silver'  => $cols[3]->text(),
				'bronze'  => $cols[4]->text(),
				'all'     => $cols[5]->text(),
			];

		}

		return $data;

	}


	static function getStatus($sport_id) {
		$parser_status = get_option('paris2024_parser_status_' . $sport_id);

		if($parser_status !== 'off') {
			$parser_status = 'on';
		}

		return $parser_status;
	}

}

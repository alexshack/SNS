<?php
class Paris2024QuizController extends PageController {

	public $templatesDir = 'inc/paris2024/templates/';
	protected $questions = [
		1 => 'Футбольная сборная какой страны больше всех забьет голов на ОИ-2024?',
		2 => 'Сколько всего медалей выиграет США в плавании (в прошлый раз было 30)?',
		3 => 'Сколько очков в сумме будет набрано в мужском финальном матче по баскетболу (финал 2012 — 207, 2016 — 162, 2020 — 169)?',
		4 => 'Какая из предложенных стран выиграет больше всех медалей в легкой атлетике в сравнении с другими?',
		5 => 'Спортсменка из какой страны окажется выше по очкам в женском современном пятиборье?',
		6 => 'Какая страна станет лидером общего зачета в стрельбе?',
		7 => 'Сколько золотых медалей выиграет Франция (ОИ-2008 — 7 золотых медалей, 2012 — 11, 2016 — 10, 2020 — 10)?',
		8 => 'Кто выиграет золотые медали в мужском волейболе?'
	];
	protected $answers = [
		1 => [
			1 => 'Бразилия',
			2 => 'Франция',
			3 => 'Испания',
			4 => 'Колумбия'
		],
		2 => [
			1 => '24 и менее',
			2 => '25-27',
			3 => '28-30',
			4 => '31 и более'
		],
		3 => [
			1 => '160 и менее',
			2 => '161-170',
			3 => '171-180',
			4 => '181 и более'
		],
		4 => [
			1 => 'Ямайка',
			2 => 'Кения',
			3 => 'Великобритания',
			4 => 'Китай'
		],
		5 => [
			1 => 'Великобритания',
			2 => 'Литва',
			3 => 'Венгрия',
			4 => 'Италия'
		],
		6 => [
			1 => 'Китай',
			2 => 'США',
			3 => 'Италия',
			4 => 'Другая страна'
		],
		7 => [
			1 => '7 и менее',
			2 => '8-10',
			3 => '11-13',
			4 => '14 и более'
		],
		8 => [
			1 => 'Польша',
			2 => 'Италия',
			3 => 'Бразилия',
			4 => 'Франция'
		],												
	];
	protected $bk_id = 12123;

	function setup() {

	}

	function getPage( $page, $answers ) {
		if ( $page == 0) {
			return Template::get( $this->templatesDir . 'quiz-start.php', [] );
		}
		if ( $page > 0 && $page < 9 ) {
			return Template::get( $this->templatesDir . 'quiz-question.php', [
				'question' => $this->questions[ $page ],
				'answers'  => $this->answers[ $page ],
				'page'     => $page
			] );
		}
		if ( $page == 9) {
			return Template::get( $this->templatesDir . 'quiz-submit.php', [
				'question' => 'Для участия в конкурсе оставьте email'
			] );
		}
		if ( $page == 10) {
			$data = $this->getStat( $answers );
			return Template::get( $this->templatesDir . 'quiz-stat.php', [
				'question' => 'Итоги конкурса мы пришлем на почту. Как проголосовали игроки',
				'stats' => $data,
				'bk_id' => $this->bk_id
			] );
		}			
	}

	function getStat( $answers ) {

		$stats = explode(',', $answers);

		Paris2024Quiz::insertData( [
			'email'  => $stats[0],
			'a_date' => wp_date('Y-m-d H:i:s', time()),
			'a_1'    => $stats[1],
			'a_2'    => $stats[2],
			'a_3'    => $stats[3],
			'a_4'    => $stats[4],
			'a_5'    => $stats[5],
			'a_6'    => $stats[6],
			'a_7'    => $stats[7],
			'a_8'    => $stats[8],
		] );

		$quiz_stats = Paris2024Quiz::query()->select([
            'a_1',
            'a_2',
            'a_3',
            'a_4',
            'a_5',
            'a_6',
            'a_7',
            'a_8',
        ])->limit(-1)->get_results();

		$count = count( $quiz_stats );

		$quiz_data = [
			1 => [
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0
			],
			2 => [
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0
			],
			3 => [
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0
			],
			4 => [
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0
			],
			5 => [
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0
			],
			6 => [
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0
			],
			7 => [
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0
			],
			8 => [
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0
			],			
		];

		foreach ( $quiz_stats as $stat ) {
			for ($i = 1; $i < 9; $i++) {
				$field = 'a_' . $i;
				$quiz_data[ $i ][ $stat->{$field} ] = $quiz_data[ $i ][ $stat->{$field} ] + 1;
			}
		}


		$data = [];
		
		foreach ($quiz_data as $key => $qdata ) {
			$max = 0;
	
			for ($i = 1; $i < 5; $i++) {
				if ( $qdata[$i] > $max ) {
					$max = $qdata[$i];
					$max_key = $i;
				}
			}

			$data[$key][0] = round( $qdata[ $stats[ $key ] ] / $count * 100, 0);
			$data[$key][1] = round( $max / $count * 100, 0);
			$data[$key][2] = $this->answers[ $key ][ $stats[ $key ] ];
			$data[$key][3] = $this->answers[ $key ][ $max_key ];
		}

		return $data;

	}

}

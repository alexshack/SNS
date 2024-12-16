<?php

class Paris2024QuizSettings extends AdminPage {

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

    protected $settings;

	function __construct( $id, $args = [] ) {
		parent::__construct( $id, $args );

	}

	function get_form() {
        $this->settings = get_option( 'paris2024_quizsettings' );
		$content = '';

		foreach ( $this->questions as $key => $question ) {
            $content .= APF::setup( 'select', [
                'id'            => 'a_' . $key,
                'title'         => $question,
                'value'         => ( isset( $this->settings['a_' . $key] ) && ! empty( $this->settings['a_' . $key] ) ) ? $this->settings['a_' . $key] : '',
                'search'        => false,
                'values'        => $this->answers[ $key ]
            ] )->get_html();
        }

		return $content;
	}

	function update() {

		$settings = [];
		foreach ( $this->questions as $key => $question ) {

			$value = false;
			if ( ! empty( $_POST[ 'a_' . $key ] ) ) {
				$value = $_POST[ 'a_' . $key ];
			}

			$settings[ 'a_' . $key ] = $value;

		}

		update_option( 'paris2024_quizsettings', $settings );

        $quizes = Paris2024Quiz::query()->select([
            'email',
            'a_1',
            'a_2',
            'a_3',
            'a_4',
            'a_5',
            'a_6',
            'a_7',
            'a_8',
            'points'
        ])->limit(-1)->get_results();

        foreach ( $quizes as $quiz ) {
            $points = 0;
            for ($i = 1; $i < 9; $i++) {
                $field = 'a_' . $i;
                if ( $quiz->{$field} == $settings[ $field ] ) {
                    $points = $points + 1;
                }
            }
            if ( $quiz->points != $points ) {
                Paris2024Quiz::updateData([
                    'points' => $points,
                ], [
                    'email' => $quiz->email,
                ]);
            }
        }

	}

}

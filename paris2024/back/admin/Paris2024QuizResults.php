<?php

class Paris2024QuizResults extends AdminPage {

	function __construct( $id, $args = [] ) {
		parent::__construct( $id, $args );

	}

    function page_content()
    {
        $this->init_scripts();
        $results = Paris2024Quiz::query()->select([
            'email',
            'a_date',
            'a_1',
            'a_2',
            'a_3',
            'a_4',
            'a_5',
            'a_6',
            'a_7',
            'a_8',
            'points'
        ])->limit(-1)->orderby_string( 'sns_paris2024_quiz.points DESC, sns_paris2024_quiz.a_date ASC' )->get_results();

        ?>
        <div class="wrap pkr-page">
            <h1>Квиз - Результаты</h1>
            <div class="postbox" style="padding: 20px;margin:20px 0;">

                <table class="olimpics-table">
                    <tr>
                        <th>Email</th>
                        <th>Очки</th>
                        <th>Дата</th>
                        <th>О1</th>
                        <th>О2</th>
                        <th>О3</th>
                        <th>О4</th>
                        <th>О5</th>
                        <th>О6</th>
                        <th>О7</th>
                        <th>О8</th>
                    </tr>


                <?php
                //print_r( $results );
                foreach ( $results as $result ) {
                    echo '<tr>';
                    echo '<td>';
                    echo '<a href="mailto:' . $result->email . '">' . $result->email . '</a></td>';
                    echo '<td>' . $result->points . '</td>';
                    echo '<td>' . $result->a_date . '</td>';
                    echo '<td>' . $result->a_1 . '</td>';
                    echo '<td>' . $result->a_2 . '</td>';
                    echo '<td>' . $result->a_3 . '</td>';
                    echo '<td>' . $result->a_4 . '</td>';
                    echo '<td>' . $result->a_5 . '</td>';
                    echo '<td>' . $result->a_6 . '</td>';
                    echo '<td>' . $result->a_7 . '</td>';
                    echo '<td>' . $result->a_8 . '</td>';
                    echo '</tr>';
                }

                ?>
                </table>
            </div>
        </div>
        <?php

    }


}

<?php

require_once 'Paris2024Settings.php';
require_once 'Paris2024TablesEditor.php';
require_once 'Paris2024QuizSettings.php';
require_once 'Paris2024QuizResults.php';

new Paris2024Settings('paris2024-settings', [
	'title' => 'Париж 2024'
]);

new Paris2024TablesEditor( 'paris2024-tables-editor', [
	'title' => __( 'Редактор таблиц' ),
	'submenu' => 'paris2024-settings'
] );

new Paris2024QuizSettings('paris2024-quizsettings', [
    'title' => 'Квиз Ответы',
    'submenu' => 'paris2024-settings'
]);

new Paris2024QuizResults('paris2024-quizresults', [
    'title' => 'Квиз Результаты',
    'submenu' => 'paris2024-settings'
]);
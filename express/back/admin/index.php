<?php
require_once __DIR__ . '/update-table.php';
require_once __DIR__ . '/ExpressBookmakerMetaBox.php';
require_once __DIR__ . '/ExpressGamesMetaBox.php';

new ExpressBookmakerMetaBox( 'express-bookmaker-meta-box', [
	'post_types' => ['express'],
	'title'      => __( 'Букмекер экспресса'),
	'context'    => 'side'
] );

new ExpressGamesMetaBox( 'express-games-meta-box', [
	'post_types' => ['express'],
	'title'      => __( 'Матчи экспресса'),
]);

add_action('before_delete_post', ['ExpressTableUpdater', 'removeData']);

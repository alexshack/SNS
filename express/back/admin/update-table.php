<?php
Expresses::load('ExpressTableUpdater');


if(isset($_GET['update_express_table']) && is_admin()) {

	Expresses::load('Express');

	$expresses = (new PostsQuery())->select([
		'ID',
		'post_title',
		'post_name',
		'post_type'
	])->where([
		'post_type' => 'express'
	])->limit(20000)->get_results();


	foreach ($expresses as $express) {

		$express = new Predict($express);

		$express->setupMetaData([
			'express_coef',
			'express_date'
		]);

		$data = [
			'date' => $express->metadata->get('express_date'),
			'coef' => $express->metadata->get('express_coef'),
		];

		$express->updateTable($data);

	}

}


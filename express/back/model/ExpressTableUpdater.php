<?php
class ExpressTableUpdater extends DBUpdate {
	protected $table_name = 'express';
	protected $primary_key = 'post_id';
	protected $columns = [
		'post_id' => 'BIGINT',
		'date'    => 'BIGINT',
		'coef'    => 'FlOAT',
	];

	static function removeData($post_id) {
		if(get_post($post_id)->post_type === 'express' && get_post($post_id)->post_status !== 'publish') {
			(new ExpressTableUpdater())->deleteRow(['post_id' => $post_id]);
		}
	}
}

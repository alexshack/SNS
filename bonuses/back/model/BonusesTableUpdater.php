<?php
class BonusesTableUpdater extends DBUpdate {

	protected $table_name = 'bonuses';
	protected $primary_key = 'post_id';
	protected $columns = [
		'post_id' => 'BIGINT',
		'bookmaker_id' => 'BIGINT',
		'amount' => 'DOUBLE',
		'date_start' => 'DATE',
		'date_end' => 'DATE',
		'date_unlimited' => 'TINYINT',
		'max_bonus' => 'DOUBLE',
		'max_bonus_currency' => 'BIGINT',
		'min_bonus' => 'DOUBLE',
		'min_bonus_currency' => 'BIGINT',
		'achievement' => 'LONGTEXT',
		'promocode' => 'LONGTEXT',
	];

	static function removeData($post_id) {
		(new BonusesTableUpdater())->deleteRow(['post_id' => $post_id]);
	}

}

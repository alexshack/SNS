<?php
class BonusesMigrate {

	static $string_columns = [
		'bookmaker_id' => 'bs_bm_id',
		'max_bonus' => 'bs_max',
		'min_bonus' => 'bs_min',
		'achievement' => 'bonus_achievement',
		'amount' => 'bs_value',
		'promocode' => 'bs_shortcode',
	];

	static $currency_columns = [
		'max_bonus_currency' => 'bs_max_val',
		'min_bonus_currency' => 'bs_min_val'
	];

	static $date_columns = [
		'date_start' => 'bs_date_start',
		'date_end' => 'bs_date_end'
	];

	static $terms_columns = [
		'bonus_type' => 'bonus_type'
	];

	static function migrateMainData($bonus_id) {
		$data = [];
		foreach (self::$string_columns as $column_name => $meta_key) {
			$data[$column_name] = get_post_meta($bonus_id, $meta_key, 1);
		}
		foreach (self::$currency_columns as $column_name => $meta_key) {
			$data[$column_name] = getTermBy('slug', get_post_meta($bonus_id, $meta_key, 1))->term_id;
		}
		foreach (self::$date_columns as $column_name => $meta_key) {
			$date = get_post_meta($bonus_id, $meta_key, 1);
			if($date) {
				$time = strtotime($date);
				$date = date('Y-m-d',$time);
			}
			$data[$column_name] = $date;
		}

		$data['date_unlimited'] = get_post_meta($bonus_id, 'bs_date_no_limit', 1) ? 1 : 0;
		(new BonusesTableUpdater())->updateByPrimaryKey($bonus_id, $data);
	}

	static function migrateTermsData($bonus_id) {
		foreach (self::$terms_columns as $meta_key => $taxonomy) {
			$terms_ids = [];

			$terms = getPostTerms($bonus_id, $taxonomy);

			foreach ($terms as $term) {
				$terms_ids[] = $term->term_id;
			}

			(new BonusesMetaTableUpdater())->updateTerms($bonus_id, $meta_key, $terms_ids);
		}
	}

	static function saveData($post_id) {
		if(isset($_POST)) {
			$data = [];
			foreach (self::$string_columns as $column_name => $key) {
				if(isset($_POST[$key])) {
					$data[$column_name] = $_POST[$key];
				}
			}
			foreach (self::$date_columns as $column_name => $key) {
				if(isset($_POST[$key])) {
					$time = strtotime($_POST[$key]);
					$date = date('Y-m-d',$time);
					$data[$column_name] = $date;
				}
			}
			foreach (self::$currency_columns as $column_name => $key) {
				if(isset($_POST[$key])) {
					$data[$column_name] = getTermBy('slug', $_POST[$key])->term_id;
				}
			}
			if(isset($_POST['bs_date_no_limit'])) {
				$data['date_unlimited'] = 1;
			} else {
				$data['date_unlimited'] = 0;
			}
			(new BonusesTableUpdater())->updateByPrimaryKey($post_id, $data);
		}
	}



}

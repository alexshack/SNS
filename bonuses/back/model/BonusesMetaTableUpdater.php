<?php

class BonusesMetaTableUpdater extends DBUpdate {

	protected $table_name = 'bonuses_meta';
	protected $primary_key = 'mid';
	protected $columns = [
		'mid' => 'BIGINT',
		'post_id' => 'BIGINT',
		'meta_key' => 'TEXT',
		'meta_value' => 'LONGTEXT',
	];

	public function updateMeta($post_id, $meta_key, $meta_value) {
		$this->insert([
			'post_id' => $post_id,
			'meta_key' => $meta_key,
			'meta_value' => $meta_value
		], [
			'post_id' => $post_id,
			'meta_key' => $meta_key,
			'meta_value' => $meta_value
		]);
	}

	public function updateTerms($post_id, $taxonomy, $terms) {
		$this->deleteRow([
			'post_id' => $post_id,
			'meta_key' => $taxonomy
		]);
		foreach ($terms as $term_id) {
			$this->updateMeta($post_id, $taxonomy, $term_id);
		}
	}

	static function removeData($post_id) {
		(new BonusesMetaTableUpdater())->deleteRow(['post_id' => $post_id]);
	}


}

<?php
class Expresses extends PostsAbstract {

	//use ExpressesMarkupRender;

	protected $meta_keys = [
		'bk_id',
		'express_url',
		'express_games',
		'express_coef',
		'express_date',
	];


	protected function setupData() {
		self::load('Express');
		$this->setupExpressData();

		return $this;
	}


	protected function setupExpressData() {
		$expresses = [];

		foreach ($this->posts as $post) {
			$post = new Express($post);

			$post->bookmaker = $this->getExpressBookmaker($post->metadata->get('bk_id'));

			$expresses[] = $post;
		}

		$this->posts = $expresses;

		return $this;
	}

	protected function getExpressBookmaker($bk_id) {
		$bookmaker = Bookmaker::setup($bk_id, [
				'data' => [
					'post_title',
					'post_name',
					'ID',
					'post_type',
					'post_date'
				]
			]);

		return $bookmaker;
	}


	static function load($class_name) {
		require_once get_template_directory() . '/inc/express/model/' . $class_name . '.php';
	}

	static function controller($class_name) {
		require_once get_template_directory() . '/inc/express/controllers/' . $class_name . '.php';
	}

	static function template($template, $attr = []) {
		Template::include('templates/express/' . $template . '.php', $attr);
	}

	static function loadTableUpdater() {
		load_theme_core('db-update');
		self::load('ExpressTableUpdater');
	}
}

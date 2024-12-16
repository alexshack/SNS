<?php
require_once 'model/Bonuses.php';
require_once 'model/Bonus.php';
require_once 'model/BonusesTableUpdater.php';
require_once 'model/BonusesMetaTableUpdater.php';
require_once 'model/BonusFilter.php';
require_once 'model/BonusesFilter.php';
require_once 'model/BonusesMigrate.php';
require_once 'model/BonusesMetaModel.php';
require_once 'model/BonusesModel.php';

if(is_admin()) {
	require_once 'admin/index.php';
}

if(!is_admin()) {
	$sns_settings = new Options('sns_settings');
	RouteControllersManager::addController('bonuses', [
		'controller' => 'BonusesArchiveRouteController',
		'conditions' => [
			[
				'post_type' => 'filter_bookmakers',
				'meta' => [
					'meta_key' => 'predicts_type',
					'meta_value' => 'bonuses'
				]
			],
			[
				'ID' => 9206
			],
		]
	]);

}

function getBonusAchievement($bonus_id) {
	$date = get_post_meta( $bonus_id, 'bs_date_end', 1 );
	$no_date = get_post_meta( $bonus_id, 'bs_date_no_limit', 1 );
	if($no_date !== 'yes' && $date && DateTime::createFromFormat("d.m.Y", $date)->format('Y-m-d') < date('Y-m-d')) {
		return 'Завершен';
	} else {
		$achieve = get_post_meta($bonus_id, 'bonus_achievement', 1);
		return ($achieve) ? $achieve : false;
	}
}

(new AjaxOnRestManager('bonuses'))->callback(function (ArrayHandler $request) {

	if($request->get('bonuses_load_action')) {
		$filter_settings = (array) json_decode(stripslashes($request->get('bonuses_load_action')));
		$offset = $filter_settings['offset'];
		$limit = $filter_settings['limit'];
		$order = $filter_settings['order'];
		$filter = (new BonusesFilter())->where($filter_settings['settings'])->limit($limit)->order($order);

		if($offset > 0) {
			$filter->offset($offset);
		}

		$bonuses = Bonuses::setup($filter->getResults());
		$result['total'] = $filter->getCount();
		$result['html'] = '';
		foreach ($bonuses as $bonus) {
			$result['html'] .= Bonuses::getTemplate('/items/bonus-loop.php', ['bonus' => $bonus, 'ismore' => true]);

			$offset++;
		}

		return $result;

	}

});

add_action( 'init', 'bonuses_filter_rules' );
function bonuses_filter_rules() {
	add_filter('query_vars', function ($vars) {
		$vars[] = 'type';
		$vars[] = 'sport';
		$vars[] = 'bk';
		return $vars;
	});
}

add_action( 'admin_head', 'replace_default_featured_image_meta_bonuses', 100 );
function replace_default_featured_image_meta_bonuses() {
    remove_meta_box( 'postimagediv', ['bonuses'], 'side' );
    add_meta_box('postimagediv', __('Изображение для списка (870х500)'), 'post_thumbnail_meta_box', ['bonuses'], 'side');
}

add_shortcode('bonuses-slider', function($args = []) {

    Enqueue::footer('/bonuses/bonuses-slider.css');
	Enqueue::footer('/bonuses/bonus-item.css');
	Enqueue::footer('/bonuses/bonus-item-loop.css');

	$args = wp_parse_args( $args, [
		'bonus_type' => '',
		'title' => false,
		'type_link' => '',
		'type_text' => 'смотреть все',
		'bookmaker_id' => '',
		'bookmaker_id__not_in' => '',
		'id__in' => '',
		'id__not_in' => '',
		'limit' => 12,
		'show_cats' => false,
		'home_page' => false
	] );

    ob_start();

    Template::render('templates/bonuses/sliders', $args);

    return ob_get_clean();

});

add_shortcode('bonus', function ($args = []) {
    Enqueue::footer('/bonuses/bonus-item.css');
    Enqueue::footer('/bonuses/bonus-item-' . $args['type'] . '.css');
	$filter = (new BonusesFilter())->where(['ID__in' => $args['bonus_id']]);
	$bonus = Bonuses::setup($filter->limit(1)->getResults())[0];
	if($bonus) { 
		ob_start();
		Bonuses::template('items/bonus-item-' . $args['type'] . '.php', ['bonus' => $bonus]);
		return ob_get_clean();
	}
	return false;
});
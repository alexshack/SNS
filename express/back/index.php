<?php
//require_once 'model/ExpressesMarkupRender.php';
require_once 'model/Expresses.php';
require_once 'register-post-type.php';
require_once 'model/ExpressFilter.php';
require_once 'model/Express.php';

if(is_admin()) {
	require_once 'admin/index.php';



} else {
	$express_settings = new Options('express');
	RouteControllersManager::addController('express', [
		'controller' => 'ExpressArchiveRouteController',
		'conditions' => [
			[
				'ID' => intval($express_settings->getOption('express_page'))
			],
		]
	]);
}

add_action( 'admin_head', 'replace_default_featured_image_meta_express', 100 );
function replace_default_featured_image_meta_express() {
    remove_meta_box( 'postimagediv', ['express'], 'side' );
    add_meta_box('postimagediv', __('Лого 1200*800px'), 'post_thumbnail_meta_box', ['express'], 'side');
}
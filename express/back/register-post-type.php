<?php

add_action( 'init', function () {
	register_post_type( 'express', [
		'label'  => null,
		'labels' => [
			'name'               => 'Экспресс дня', // основное название для типа записи
			'singular_name'      => 'Экспресс', // название для одной записи этого типа
			'add_new'            => 'Добавить экспресс', // для добавления новой записи
			'add_new_item'       => 'Добавление экспресса', // заголовка у вновь создаваемой записи в админ-панели.
			'edit_item'          => 'Редактирование экспресса', // для редактирования типа записи
			'new_item'           => 'Новый экспресс', // текст новой записи
			'view_item'          => 'Смотреть экспресс', // для просмотра записи этого типа.
			'search_items'       => 'Искать экспресс', // для поиска по этим типам записи
			'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
			'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
			'parent_item_colon'  => 'Родительский экспресс',
			'menu_name'          => 'Экспресс дня',
		],
		'description'         => '',
		'public'              => true,
		'show_in_menu'        => null, // показывать ли в меню адмнки
		'show_in_rest'        => null, // добавить в REST API. C WP 4.7
		'rest_base'           => null, // $post_type. C WP 4.7
		'menu_position'       => null,
		'menu_icon'           => 'dashicons-calendar-alt',
		'hierarchical'        => false,
		'supports'            => [ 'title', 'editor', 'thumbnail', 'comments', 'author' ], // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
		'taxonomies'          => [],
		'has_archive'         => false,
	] );

} );


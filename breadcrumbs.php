<?php

function breadcrumbs($onlyJson = false) {
	if ( is_category() ) {
		$cat = get_category( get_query_var( 'cat' ), false );
		if ( $cat->parent == 0 ) {
			return false;
		}
	}
	/* === ОПЦИИ === */
	$text['home']     = 'Главная'; // текст ссылки "Главная"
	$text['category'] = '%s'; // текст для страницы рубрики
	$text['search']   = 'Результаты поиска по запросу "%s"'; // текст для страницы с результатами поиска
	$text['tag']      = 'Записи с тегом "%s"'; // текст для страницы тега
	$text['author']   = 'Статьи автора %s'; // текст для страницы автора
	$text['404']      = 'Ошибка 404'; // текст для страницы 404
	$text['page']     = 'Страница %s'; // текст 'Страница N'
	$text['cpage']    = 'Страница комментариев %s'; // текст 'Страница комментариев N'

	$wrap_before    = '<nav class="breadcrumb"><div class="wrapper">'; // открывающий тег обертки
	$wrap_after     = '</div></nav><!-- .breadcrumbs -->'; // закрывающий тег обертки
	$sep            = '<i class="fa-chevron-right"></i>'; // разделитель между "крошками"
	$sep_before     = ''; // тег перед разделителем
	$sep_after      = ''; // тег после разделителя
	$show_home_link = 1; // 1 - показывать ссылку "Главная", 0 - не показывать
	$show_on_home   = 0; // 1 - показывать "хлебные крошки" на главной странице, 0 - не показывать
	$show_current   = 0; // 1 - показывать название текущей страницы, 0 - не показывать
	$before         = '<span class="current">'; // тег перед текущей "крошкой"
	$after          = '</span>'; // тег после текущей "крошки"
	/* === КОНЕЦ ОПЦИЙ === */

	global $post;
	$home_url       = home_url( '/' );
	$link_before    = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	$link_after     = '</span>';
	$link_attr      = ' itemprop="item"';
	$link_in_before = '<span itemprop="name">';
	$link_in_after  = '</span>';
	$link           = $link_before . '<a href="%1$s"' . $link_attr . '>' . $link_in_before . '%2$s' . $link_in_after . '</a>' . '%3$s' . $link_after;
	$frontpage_id   = get_option( 'page_on_front' );
	$parent_id      = ( $post ) ? $post->post_parent : '';
	//$sep = ' ' . $sep_before . $sep . $sep_after . ' ';
	$home_link = $link_before . '<a itemscope itemtype="https://schema.org/WebPage"' . $link_attr . ' itemid="' . $home_url . '" href="' . $home_url . '" class="home">' . $link_in_before . $text['home'] . $link_in_after . '</a><meta itemprop="position" content="1" />' . $link_after;

	$items = [];

	$items[] = [
		'name' => $text['home'],
		'link' => $home_url
	];

	//КАТЕГОРИИ
	if ( is_category() ) {
		$cat = get_category( get_query_var( 'cat' ), false );
		if ( $cat->parent != 0 ) {
			$p_ids = get_ancestors( $cat->term_id, 'category' );
			foreach ( array_reverse( $p_ids ) as $p_id ) {
				$items[] = [
					'name' => get_category( $p_id )->name,
					'link' => get_category_link( $p_id )
				];
			}
		}
		$items[] = [
			'name' => $cat->name,
			'link' => get_category_link( $cat->term_id ),
			'self' => true
		];
	}

	//ПОИСК
	if ( is_search() ) {
		$items[] = [
			'name' => sprintf( $text['search'], get_search_query() ),
			'link' => '',
			'self' => true
		];		
	}


	//БУКМЕКЕРЫ

	//Срезы букмекеров
	if ( get_post_type() == 'filter_bookmakers' ) {
		$predicts_type = get_post_meta( get_the_ID(), 'predicts_type', true );

		if ( $predicts_type == 'bookmakers' ) {

			$items[] = [
				'name' => 'Рейтинг букмекеров',
				'link' => get_permalink( 8953 )
			];
			$items[] = [
				'name' => get_the_title(),
				'link' => get_permalink(),
				'self' => true
			];

		}		
	}

	//Обзор и подстраницы букмекера
	if ( is_single() && get_post_type() == 'bookmakers' ) {
		global $wp_query;
        $bookmaker = Bookmaker::setup( get_the_ID() );

		$items[] = [
			'name' => 'Рейтинг букмекеров',
			'link' => get_permalink( 8953 )
		];

		if ( isset( $wp_query->query['tab'] ) ) {
			$items[] = [
				'name' => $bookmaker->name,
				'link' => $bookmaker->getPermalink(),
			];
			if ( $wp_query->query['tab'] == 'otzyvy') {
				$items[] = [
					'name' => 'Отзывы о БК ' . $bookmaker->name,
					'link' => $bookmaker->getPermalink(),
					'self' => true
				];				
			}
			if ( $wp_query->query['tab'] == 'bonusy' ) {
				$items[] = [
					'name' => 'Бонусы БК ' . $bookmaker->name,
					'link' => $bookmaker->getPermalink(),
					'self' => true
				];				
			}
			if ( $wp_query->query['tab'] == 'novosti' ) {
				$items[] = [
					'name' => 'Новости БК ' . $bookmaker->name,
					'link' => $bookmaker->getPermalink(),
					'self' => true
				];				
			}
			if ( $wp_query->query['tab'] == 'poleznoe' ) {
				$items[] = [
					'name' => 'Полезные статьи про БК ' . $bookmaker->name,
					'link' => $bookmaker->getPermalink(),
					'self' => true
				];				
			}									
		} else {
			$items[] = [
				'name' => $bookmaker->name,
				'link' => $bookmaker->getPermalink(),
				'self' => true
			];
		}		

	}

	if ( $bk_bonus_data = get_bk_bonus_filter_data() ) {

		$bookmaker = Bookmaker::setup( $bk_bonus_data['bk'] );

		$items[] = [
			'name' => 'Рейтинг букмекеров',
			'link' => get_permalink( 8953 )
		];

		$items[] = [
			'name' => $bookmaker->name,
			'link' => $bookmaker->getPermalink()
		];

		if( getBonusesCount($bookmaker->ID) ) {
			$items[] = [
				'name' => 'Бонусы БК ' . $bookmaker->name,
				'link' => home_url() . '/bonusy-' . $bookmaker->post_name . '/'
			];
		}
		$items[] = [
			'name' => get_the_title(),
			'link' => get_permalink(),
			'self' => true
		];

	}

	//Новости букмекера
	if ( ( $promotarget_title = get_post_meta( get_the_ID(), 'promotarget_title', true ) ) && ( count(get_the_category() ) && get_root_term_slug( get_the_category()[0]->term_id ) == 'novosti' ) ) {
        $bookmaker = Bookmaker::setup( $promotarget_title );

		$items[] = [
			'name' => 'Рейтинг букмекеров',
			'link' => get_permalink( 8953 )
		];

		$items[] = [
			'name' => $bookmaker->name,
			'link' => $bookmaker->getPermalink()
		];

		$items[] = [
			'name' => 'Новости БК ' . $bookmaker->name,
			'link' => home_url() . '/novosti-' . $bookmaker->post_name . '/'
		];

		$items[] = [
			'name' => get_the_title(),
			'link' => get_permalink(),
			'self' => true
		];	    
		
	}

	//Статьи букмекера
	if ( ( $promotarget_title = get_post_meta( get_the_ID(), 'promotarget_title', true ) ) && ( count( get_the_category() ) && get_root_term_slug( get_the_category()[0]->term_id ) == 'stati' ) ) {
        $bookmaker = Bookmaker::setup( $promotarget_title );

		$items[] = [
			'name' => 'Рейтинг букмекеров',
			'link' => get_permalink( 8953 )
		];

		$items[] = [
			'name' => $bookmaker->name,
			'link' => $bookmaker->getPermalink()
		];

		$items[] = [
			'name' => 'Полезные статьи про БК ' . $bookmaker->name,
			'link' => home_url() . '/poleznoe-' . $bookmaker->post_name . '/'
		];

		$items[] = [
			'name' => get_the_title(),
			'link' => get_permalink(),
			'self' => true
		]; 
 	}

	//Обзор бонуса
	if ( get_post_type() == 'bonuses' ) {		
		$bk_id     = get_post_meta( get_the_ID(), 'bs_bm_id', true );
		$bookmaker = Bookmaker::setup( $bk_id );

		$items[] = [
			'name' => 'Рейтинг букмекеров',
			'link' => get_permalink( 8953 )
		];

		$items[] = [
			'name' => $bookmaker->name,
			'link' => $bookmaker->getPermalink()
		];

		$items[] = [
			'name' => 'Бонусы БК ' . $bookmaker->name,
			'link' => home_url() . '/bonusy-' . $bookmaker->post_name . '/'
		];		

		$items[] = [
			'name' => get_the_title(),
			'link' => get_permalink(),
			'self' => true
		];

	}

	
	//FAQ
	if ( get_post_type() == 'faq' ) {
		if ( $faq_bookmakers = get_post_meta( get_the_ID(), 'faq_bookmakers', true ) ) {
			$bookmaker = Bookmaker::setup($faq_bookmakers);

			$items[] = [
				'name' => 'Рейтинг букмекеров',
				'link' => get_permalink( 8953 )
			];

			$items[] = [
				'name' => $bookmaker->name,
				'link' => $bookmaker->getPermalink()
			];

			$items[] = [
				'name' => get_the_title(),
				'link' => get_permalink(),
				'self' => true
			];

		} else {

			$post_type = get_post_type_object( get_post_type() );
			$slug      = $post_type->rewrite;

			$items[] = [
				'name' => $post_type->labels->singular_name,
				'link' => home_url() . '/' . $slug['slug'] . '/'
			];

			$items[] = [
				'name' => 'Общие',
				'link' => home_url() . '/obshhie-faq/'
			];			

			$items[] = [
				'name' => get_the_title(),
				'link' => get_permalink(),
				'self' => true
			];		

		}
	}


 	//ПРОГНОЗЫ

	//Срезы прогнозов
	if ( get_post_type() == 'filter_bookmakers' ) {
		
		$predicts_type = get_post_meta( get_the_ID(), 'predicts_type', true );

		if ( $predicts_type == 'predicts' ) {
			
			$items[] = [
				'name' => 'Прогнозы',
				'link' => get_permalink( 9881 )
			];

			$sport_type_id = get_post_meta( get_the_ID(), 'sport_type', true );
			$tournament_id = get_post_meta( get_the_ID(), 'tournament', true );

			if ( $sport_type_id && $tournament_id ) {
				$sport_type_slice = get_term_meta( $sport_type_id, 'term_slice', true );
				if ( $sport_type_slice ) {
					$items[] = [
						'name' => get_term( $sport_type_id )->name,
						'link' => get_permalink( $sport_type_slice )
					];					
				}
			}

			$items[] = [
				'name' => get_the_title(),
				'link' => get_permalink(),
				'self' => true
			];
		}		
	}

	//Прогноз
	if ( get_post_type() == 'predicts' ) {

		$items[] = [
			'name' => 'Прогнозы',
			'link' => get_permalink( 9881 )
		];

		$sport_type_id = get_post_meta( get_the_ID(), 'pr_sport_type', true );
		$tournament_id = get_post_meta( get_the_ID(), 'pr_tournament', true );

		if ( $sport_type_id ) {
			$sport_type_slice = get_term_meta( $sport_type_id, 'term_slice', true );
			if ( $sport_type_slice ) {
				$items[] = [
					'name' => get_term( $sport_type_id )->name,
					'link' => get_permalink( $sport_type_slice )
				];		
			}
		}

		if ( $sport_type_id && $tournament_id ) {
			$tournament_slice = get_term_meta( $tournament_id, 'term_slice', true );
			if ( $sport_type_slice && $tournament_slice ) {
				$items[] = [
					'name' => get_term( $tournament_id )->name,
					'link' => get_permalink( $tournament_slice )
				];				
			}
		}

		$items[] = [
			'name' => get_the_title(),
			'link' => get_permalink(),
			'self' => true
		];				
	}

	//Экспресс
	if ( get_post_type() == 'express' ) {

		$express = new Options('express');
		$express_page = $express->getOption('express_page');
		$items[] = [
			'name' => 'Экспресс дня',
			'link' => get_permalink( $express_page )
		];

		$items[] = [
			'name' => get_the_title(),
			'link' => get_permalink(),
			'self' => true
		];				
	}	

	if ( get_post_type() == 'post' && !get_post_meta( get_the_ID(), 'promotarget_title', true )  ) {
		$cat         = get_the_category();
		$cat         = $cat[0];
		$parent_cats = get_ancestors( $cat->term_id, $cat->taxonomy );

		$in_bet_school = false;

		$bet_school = new Options('bet_school');
		$bet_cats = $bet_school->getArray('category');

		if (in_array($cat->term_id, $bet_cats) ) {
			$in_bet_school = true;
			$cat_bet_school = $cat;
		} 
		foreach ($parent_cats as $parent_cat) {
			if (in_array($parent_cat, $bet_cats)) {
				$in_bet_school = true;
				$cat_bet_school = get_category( $parent_cat );					
			}
		}	


		if ( $in_bet_school ) {
			$bet_id = $bet_school->getOption('page');
			$items[] = [
				'name' => get_the_title( $bet_id ),
				'link' => get_permalink( $bet_id )
			];

			//$bet_cat = $bet_school->getOption('bs_' . $cat->term_id);
			$bet_cat = new Options('bs_' . $cat_bet_school->term_id, $bet_school);

			$items[] = [
				'name' => $bet_cat->getOption('name'),
				'link' => get_permalink( $bet_cat->getOption('page') )
			];	

			$show_current = true;		

		} else {
		
			if(!empty($parent_cats)) {
				foreach (array_reverse($parent_cats) as $parent_cat) {
					$items[] = [
						'name' => get_category( $parent_cat )->name,
						'link' => get_category_link( $parent_cat )
					];	
				}
			}

			$items[] = [
				'name' => $cat->name,
				'link' => get_category_link( $cat->term_id )
			];				
		}


		
		if ( is_array($cyber_section = Cyber::isInCategory(get_the_ID())) ) {
			$post = get_post(get_the_ID());
			$postModel = new PostModel($post);
			if ( $postModel->inCategory(38) ) {
				$items[2]['link'] = $cyber_section['page_link'];
				if (isset($cyber_section['children']) && count($cyber_section['children'])) {
					foreach ($cyber_section['children'] as $child) {
						if (get_the_category(get_the_ID())[0]->cat_ID == $child['cat_id']) { 
							$items[3]['link'] = $child['page_link'];
						}
					}
				}
			} else {
				$items[3]['link'] = $cyber_section['page_link'];
			}	
		}

		$items[] = [
			'name' => get_the_title(),
			'link' => get_permalink(),
			'self' => true
		];		
	}

	if ( is_page() ) {
		if ( $parent_id && $parent_id != $frontpage_id ) {
			$parents = get_ancestors( get_the_ID(), 'page' );
			foreach (array_reverse($parents) as $parent) {
				$items[] = [
					'name' => get_the_title( $parent ),
					'link' => get_permalink( $parent )
				];	
			}			
		}
		if ( is_page_template( 'templates/bk-compare-page.php' ) ) {
			$items[] = [
				'name' => 'Рейтинг букмекеров',
				'link' => get_permalink( 8953 )
			];
		}
		if ( is_page_template( 'templates/olympics-2024-schedule.php' ) ) {
			$paris_options = get_option( 'paris2024_settings' );
			$main_page = get_post( $paris_options['page_main'] );
			$main_page_link = get_the_permalink( $main_page );			
			$items[] = [
				'name' => 'Париж 2024',
				'link' => $main_page_link
			];
		}		
		$items[] = [
			'name' => get_the_title(),
			'link' => get_permalink(),
			'self' => true
		];
	}

	if ( is_author() ) {
		global $author;
		$author = get_userdata( $author );
		$items[] = [
			'name' => sprintf( $text['author'], $author->display_name ),
			'link' => get_permalink(),
			'self' => true
		];		
	}


	if ( $onlyJson ) {
		$schema = new SchemaOrg( $items, 'breadcrumbs' );
        echo $schema->schema;
	} else {
		if ( is_home() || is_front_page() ) {
			if(!$show_on_home) {
				$items = [];
			}
		}

		if (!$show_home_link) {
			array_shift ($items);
		}
		
		$links = [];
		foreach ($items as $item) {
			if ( isset($item['self']) && $item['self'] && !$show_current ) {
				continue;
			}
			if ( isset($item['self']) && $item['self'] && $show_current ) {
				$links[] = '<span>' . $item['name'] . '</span>';
			} else {		
				$links[] = '<a href="' . $item['link'] . '">' . $item['name'] . '</a>';
			}
		}
		echo $wrap_before;
		$schema = new SchemaOrg($items, 'breadcrumbs');
		echo $schema->schema;
		echo implode($sep, $links);
		if ( ! is_page_template('templates/olympics-2024-main.php') && ! test_clear() ) {
			$stories = do_shortcode('[wp_stories]');
			if ( $stories != '[wp_stories]' ) {
				echo '<div>';
				echo $stories;
				echo '</div>';
			}
		}
		echo $wrap_after;
	}


}

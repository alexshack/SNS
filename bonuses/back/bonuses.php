<?php

add_shortcode( 'list_bonuses', 'list_bonuses_func' );

function list_bonuses_func( $attr ) {

	global $wpdb;

	$bonus_type = isset( $attr['bonus_type'] ) ? explode( ',', $attr['bonus_type'] ) : [];



	$id = isset( $attr['id'] ) ? $attr['id'] : false;
	$id = ( isset( $_POST['bk_id'] ) && intval( $_POST['bk_id'] ) > 0 ) ? $_POST['bk_id'] : $id;
	$count = getBonusesCount($id, $bonus_type);

	if($count) {

		$only_list = isset( $attr['only_list'] ) && $attr['only_list'] ? true : false;

		$result = get_filter_bonuses( $id, $bonus_type, false, $attr['limit'] );

		foreach ( $result as $bonus ) {
			$bk[ $bonus->bk_id ] = get_post_meta( $bonus->bk_id, 'bm_main_name', true );
			asort( $bk );
		}

		$result_count = get_filter_bonuses( $id, $bonus_type, true );

		$s = '';

		if ( ! isset( $attr['hide_bk_logo'] ) || $attr['hide_bk_logo'] == false ) {
			$bookmakers_args = array(
				'post_type'      => array( 'bookmakers' ),
				'post_status'    => array( 'publish' ),
				'posts_per_page' => '-1',
				'meta_query'     => array(
					'cupys' => array(
						'key'     => '_cupys_meta_key',
						'compare' => 'EXISTS',
					),
					'cupys' => array(
						'key'     => 'bonuses_count',
						'compare' => '>',
						'value'   => '0',
					),
				),
				'meta_key'       => 'weight',
				'orderby'        => 'meta_value',
				'order'          => 'DESC'
			);
			$bookmakers      = new WP_Query( $bookmakers_args );

			$options = '<option value="">Все БК</option>';
			while ( $bookmakers->have_posts() ) {
				$bookmakers->the_post();
				$bm_main_name = get_post_meta( get_the_ID(), 'bm_main_name', true );
				if ( isset( $_POST['bk_id'] ) && $_POST['bk_id'] == get_the_ID() ) {
					$options .= '<option value="' . get_the_ID() . '" selected="selected">' . $bm_main_name . '</option>';
				} else {
					$options .= '<option value="' . get_the_ID() . '">' . $bm_main_name . '</option>';
				}
			}
			wp_reset_postdata();
		}

		if ( ! $id && ! $only_list ) {

			$bonus_type_header = ( isset( $attr['bonus_type'] ) ) ? $attr['bonus_type'] : '';

			$s .= '<div class="bookmaker-items-subheader" data-pageid="' . get_the_ID() . '" data-id="" data-sort="" data-number="' . $attr['limit'] . '" data-type="' . $bonus_type_header . '">';


			$s .= '<div class="bookmaker-items-subheader__total">Всего в базе <span class="bookmaker-items-subheader__total-count">' . $result_count . ' ' . num_word( $result_count, [
					'бонус',
					'бонуса',
					'бонусов'
				] ) . '</span></div>';

			$s .= '<div class="bookmaker-items-subheader__order">';
			$s .= '<div class="bookmaker-items-subheader__order-container">';
			$s .= '<button class="bookmaker-items-subheader__order-link"><span>По популярности</span></button>';
			$s .= '<div class="bookmaker-items-subheader__order-data-wrapper bookmaker-items-subheader__order-data-wrapper--left">';
			$s .= '<div class="bookmaker-items-subheader__order-data">';
			$s .= '<button data-sort=""  class="bookmaker-items-subheader__order-item sort-bonus-item bookmaker-items-subheader__order-item_active">По популярности</button>';
			$s .= '<button data-sort="1" class="bookmaker-items-subheader__order-item sort-bonus-item">Сначала новые</button>';
			$s .= '<button data-sort="2" class="bookmaker-items-subheader__order-item sort-bonus-item">По сумме бонуса</button>';
			$s .= '<button data-sort="3" class="bookmaker-items-subheader__order-item sort-bonus-item">По типу</button>';
			$s .= '</div>';
			$s .= '</div>';
			$s .= '</div>';
			$s .= '</div>';

			$s .= '<div class="bookmaker-items-subheader__order">';
			$s .= '<div class="bookmaker-items-subheader__order-container">';
			$s .= '<button class="bookmaker-items-subheader__order-link"><span>Все букмекеры</span></button>';
			$s .= '<div class="bookmaker-items-subheader__order-data-wrapper bookmaker-items-subheader__order-data-wrapper--right">';
			$s .= '<div class="bookmaker-items-subheader__order-search">';
			$s .= '<div class="bookmaker-items-subheader__order-search-inside">';
			$s .= '<input type="text" placeholder="Поиск" class="bookmaker-items-subheader__order-search-input" /><i class="fa-search"></i>';
			$s .= '</div>';
			$s .= '</div>';
			$s .= '<div class="bookmaker-items-subheader__order-data">';
			$s .= '<button data-id="" class="bookmaker-items-subheader__order-item sort-bonus-item bookmaker-items-subheader__order-item_active">Все букмекеры</button>';
			if ( ! $bonus_type || count( $bonus_type ) < 1 ) {
				$query = "SELECT DISTINCT bk.meta_value as bk_id, bk_name.meta_value as bk_name FROM wp_posts AS bonus INNER JOIN wp_postmeta AS bk ON bonus.ID = bk.post_id INNER JOIN wp_postmeta AS bk_name ON bk.meta_value = bk_name.post_id WHERE bonus.post_type='bonuses' AND bonus.post_status='publish' AND bk.meta_key = 'bs_bm_id' AND bk_name.meta_key = 'bm_main_name' ORDER BY bk_name.meta_value ASC LIMIT 300";
			} else {
				$query = "SELECT DISTINCT bk.meta_value as bk_id, bk_name.meta_value as bk_name FROM wp_posts AS bonus INNER JOIN wp_postmeta AS bk ON bonus.ID = bk.post_id INNER JOIN wp_postmeta AS bk_name ON bk.meta_value = bk_name.post_id INNER JOIN wp_term_relationships AS bonus_type ON bonus.ID = bonus_type.object_id  WHERE bonus.post_type='bonuses' AND bonus.post_status='publish' AND bk.meta_key = 'bs_bm_id' AND bk_name.meta_key = 'bm_main_name' AND bonus_type.term_taxonomy_id IN (" . implode( ', ', $bonus_type ) . ") ORDER BY bk_name.meta_value ASC LIMIT 300";
			}
			global $wpdb;
			$bks = $wpdb->get_results( $query );
			foreach ( $bks as $bk ) {
				$s .= '<button data-id="' . $bk->bk_id . '" class="bookmaker-items-subheader__order-item sort-bonus-item">' . $bk->bk_name . '</button>';
			}
			$s .= '</div>';
			$s .= '</div>';
			$s .= '</div>';
			$s .= '</div>';

			$s .= '</div>';
		}

		if ( ! $only_list && ( ! isset( $attr['hide_bk_logo'] ) || $attr['hide_bk_logo'] == false ) ) {

			// Inline nav

			$menu_name = 'main-menu';
			$locations = get_nav_menu_locations();
			$parent_id = 9211;

			if ( isset( $locations[ $menu_name ] ) ) {

				$menu       = wp_get_nav_menu_object( $locations[ $menu_name ] );
				$menu_items = wp_get_nav_menu_items( $menu->term_id );

				$first_level = [];

				foreach ( $menu_items as $i ) {
					if ( $i->menu_item_parent == $parent_id ) {
						$first_level[] = $i;
					}
				}
			}

			if ( ( count( $first_level ) > 0 ) ) {

				$all_bonuses_active_class = is_page_template( 'templates/bonuses.php' ) ? ' active active-page' : '';

				$s .= '<div class="bookmaker-items-header__wrapper-menu">';
				$s .= '<div class="bookmaker-items-header__menu">';
				$s .= '<a href="/vse-bonusy-bukmekerov/" class="bookmaker-items-header__menu-item ' . $all_bonuses_active_class . '">Все бонусы <span class="bookmaker-items-header__menu-item-count">' . get_filter_bonuses( false, false, true ) . '</span></a>';

				foreach ( $first_level as $i ) {

					$menu_item_bonuses_count = 0;

					if ( $i->object_id ) {

						$menu_item_post_bookmaker_id = get_post_meta( $i->object_id, 'bookmaker_id', true );
						$menu_item_post_bonus_types  = get_post_meta( $i->object_id, 'bonus_type', true );

						if ( $menu_item_post_bonus_types ) {

							$menu_item_bonuses_count = get_filter_bonuses( $menu_item_post_bookmaker_id, $menu_item_post_bonus_types, true );
						}
					}

					if ( get_the_ID() == $i->object_id ) {
						$s .= '<a class="bookmaker-items-header__menu-item active active-page" data-id="' . $i->ID . '" href="' . $i->url . '">';
					} else {
						$s .= '<a class="bookmaker-items-header__menu-item" data-id="' . $i->ID . '" href="' . $i->url . '">';
					}

					$s .= $i->title;

					if ( $menu_item_bonuses_count > 0 ) {
						$s .= '<span class="bookmaker-items-header__menu-item-count">' . $menu_item_bonuses_count . '</span>';
					}

					$s .= '</a>';
				}

				$s .= '</div>';
				$s .= '</div>';
			}
		}

    }



	if ( isset($result) && $result ) {

		$s_class = ( $id ) ? 'bookmaker-bonuses--bookmaker' : '';

		if ( $id && ! is_singular( 'bookmakers' ) ) {
			$s_class = '';
		}

		$s .= '<div class="progress-b hidden"></div><div class="bookmaker-bonuses ' . $s_class . '">';

		$template_name = 'templates/loop-bonuses.php';

		if ( is_singular( 'bookmakers' ) ) {
			$template_name = 'templates/loop-bonuses-bookmaker.php';
		}

		$i = 0;

		foreach ( $result as $bonus ) {

			ob_start();

			include( locate_template( $template_name, false, false ) );

			$s .= ob_get_clean();

			$i ++;
		}

		$s .= '</div>';

		if ( ! $only_list && ( $result_count > $attr['limit'] ) ) {
			$bonus_type_load_more = ( isset( $attr['bonus_type'] ) ) ? $attr['bonus_type'] : '';

			$s .= '<div class="loadmore-bonuses"><button data-template="' . $template_name . '" data-page="1" data-type="' . $bonus_type_load_more . '" data-total="' . ceil( $result_count / $attr['limit'] ) . '" data-id="' . $id . '" data-sort="" data-number="' . $attr['limit'] . '" data-pageid="' . get_the_ID() . '" class="btn btn-gray">Загрузить еще</button></div>';
		}

	} else {

		if ( $id && in_array( 314, $bonus_type ) ) {
            $s .= Bookmakers::getTemplate('no-bonuses.php', ['text' => 'На данный момент фрибет бонусы у БК ' . get_post_meta(get_the_ID(), 'bm_main_name', 1) . ' отсутствуют']);
		} elseif ( $id && in_array( 2114, $bonus_type ) ) {
			if ( $promo_data = get_promo_code_data( $id ) ) {
				$s .= '<p>Бонусы для новых игроков доступны по промокоду:</p>';
				$s .= '<div class="bookmaker-item-table__promo-code bookmaker-item-table__promo-code--content promo-code-action" ' . get_promo_attr( $promo_data ) . '>
                    ' . $promo_data->getOption( 'promocode' ) . '
                    <img ' . get_promo_attr( $promo_data ) . ' class="promo-code__cursor" src="' . get_template_directory_uri() . '/img/cursor.png" alt="Бонус по ссылке">
                    <svg ' . get_promo_attr( $promo_data ) . ' class="promo-border-svg"><use xlink:href="' . get_template_directory_uri() . '/img/promo-border.svg#promo-border"></use></svg>
                    <svg ' . get_promo_attr( $promo_data ) . ' class="promo-border-svg-2"><use xlink:href="' . get_template_directory_uri() . '/img/promo-border.svg#promo-border"></use></svg>
                </div>';
			} else {
				$s .= Bookmakers::getTemplate('no-bonuses.php', ['text' => 'На данный момент промокоды у БК ' . get_post_meta(get_the_ID(), 'bm_main_name', 1) . ' отсутствуют']);
			}
		} else {
			$s .= Bookmakers::getTemplate('no-bonuses.php', ['text' => 'На данный момент бонусы у БК ' . get_post_meta(get_the_ID(), 'bm_main_name', 1) . ' отсутствуют']);
		}
	}

	return $s;
}

/*
 * Get bonuses
 */
function get_filter_bonuses( $bk_id = false, $bonus_type = false, $get_count = false, $limit = 30 ) {
	$filter = new BonusFilter();
	if ( $bk_id ) {
		$filter->bookmaker( $bk_id );
	}
	if ( $bonus_type ) {
		$filter->type( $bonus_type );
	}
	if ( ! $get_count ) {
		if ( get_post_type( get_the_ID() ) === 'bookmakers' ) {
			$result = $filter->orderByWeight()->orderByDefault()->getResults( $limit );
		} else if ( getFirstBonusesOnPage( get_the_ID() ) ) {
			$filter->bonus( getFirstBonusesOnPage( get_the_ID() ) );
			$result = $filter->orderByID( getFirstBonusesOnPage( get_the_ID() ) )->orderByDefault()->orderByBookmakerRate()->getResults( $limit );
		} else {
			$result = $filter->orderByDefault()->orderByBookmakerRate()->getResults( $limit );
		}
	} else {
		$result = $filter->getCount();
	}

	return $result;
}

function getFirstBonusesOnPage( $post_id ) {
	$first_bonuses = false;
	if ( get_post_type( $post_id ) === 'filter_bookmakers' ) {
		$first_bonuses = get_post_meta( $post_id, 'main_bonuses', true );
	}
	if ( $post_id == 9206 ) {
		$first_bonuses = get_option( 'first_bonuses' );
	}
	if ( $first_bonuses ) {
		return $first_bonuses;
	}

	return false;
}

function get_first_bk_bonus( $id ) {
	global $wpdb;
	$wp = $wpdb->prefix;

	$bonus_type = 313;
	$and_where  = $id ? " AND {$wp}postmeta.meta_value = " . $id : '';

	$result     = [];
	$result_old = [];

	$bonuses = $wpdb->get_results( "SELECT
		{$wp}posts.ID AS bonus_id,
		{$wp}postmeta.meta_value AS bk_id,
		{$wp}posts.post_title AS bonus_title,
		bk_meta.meta_value AS editorial_rating,
		{$wp}term_taxonomy.`order`
	FROM
		{$wp}posts
	INNER JOIN {$wp}postmeta ON {$wp}posts.ID = {$wp}postmeta.post_id
	INNER JOIN {$wp}postmeta AS bk_meta ON {$wp}postmeta.meta_value = bk_meta.post_id
	INNER JOIN {$wp}term_relationships ON {$wp}posts.ID = {$wp}term_relationships.object_id
	INNER JOIN {$wp}term_taxonomy ON {$wp}term_relationships.term_taxonomy_id = {$wp}term_taxonomy.term_taxonomy_id
	WHERE
		{$wp}postmeta.meta_key = 'bs_bm_id' AND
		bk_meta.meta_key = 'bm_editorial_rating'" . $and_where . "
	GROUP BY
		{$wp}posts.ID
	ORDER BY
		editorial_rating DESC,
		{$wp}term_taxonomy.order ASC" );

	foreach ( $bonuses as $bonus ) {
		$date_end = strtotime( esc_attr( get_post_meta( $bonus->bonus_id, 'bs_date_end', true ) ) );
		if ( esc_attr( get_post_meta( $bonus->bonus_id, 'bs_date_no_limit', true ) ) ) {
			$result[] = $bonus;
		} else {
			if ( $date_end > 0 && $date_end <= time() ) {
				$result_old[] = $bonus;
			} else {
				$result[] = $bonus;
			}
		}
	}

	if ( $result_old ) {
		foreach ( $result_old as $bonus ) {
			$result[] = $bonus;
		}
	}

	foreach ( $result as $key => $bonus ) {
		$types = get_the_terms( $bonus->bonus_id, 'bonus_type' );
		if ( $types ) {
			$has_type = false;
			foreach ( $types as $_type ) {
				if ( $_type->term_id == $bonus_type ) {
					$has_type = true;
				}
			}
			if ( ! $has_type ) {
				unset( $result[ $key ] );
			}
		} else {
			unset( $result[ $key ] );
		}
	}

	$result = array_slice( $result, 0, 1 );

	return $result;
}

function get_bk_bonus( $id, $show_type = false, $data_only = false ) {

	global $wpdb;


	$wp    = $wpdb->prefix;
	$result = false;
	$current_date = date( 'd.m.Y' );

	$bonus_query = "SELECT
		{$wp}posts.ID
	FROM
		{$wp}posts
	INNER JOIN {$wp}postmeta ON {$wp}posts.ID = {$wp}postmeta.post_id
	INNER JOIN {$wp}postmeta AS bk_meta ON {$wp}postmeta.meta_value = bk_meta.post_id
	INNER JOIN {$wp}postmeta AS bs_max_meta ON {$wp}posts.ID = bs_max_meta.post_id AND bs_max_meta.meta_key = 'bs_max'
    INNER JOIN {$wp}postmeta AS bs_date_start_meta ON {$wp}posts.ID = bs_date_start_meta.post_id AND bs_date_start_meta.meta_key = 'bs_date_start'
	INNER JOIN {$wp}postmeta AS bs_date_end_meta ON {$wp}posts.ID = bs_date_end_meta.post_id AND bs_date_end_meta.meta_key = 'bs_date_end'
	INNER JOIN {$wp}term_relationships ON {$wp}posts.ID = {$wp}term_relationships.object_id
	INNER JOIN {$wp}term_taxonomy ON {$wp}term_relationships.term_taxonomy_id = {$wp}term_taxonomy.term_taxonomy_id
	WHERE
	    {$wp}posts.post_status = 'publish' AND
		{$wp}postmeta.meta_key = 'bs_bm_id' AND
		bk_meta.meta_key = 'bm_editorial_rating' AND 
		bs_max_meta.meta_value > 0 AND
	    (
	      (
	        bs_date_start_meta.meta_value = '' OR
	        STR_TO_DATE(bs_date_start_meta.meta_value, '%d.%m.%Y') <= STR_TO_DATE('{$current_date}', '%d.%m.%Y')
	      ) AND 
	      (
	        bs_date_end_meta.meta_value = '' OR
	        STR_TO_DATE(bs_date_end_meta.meta_value, '%d.%m.%Y') >= STR_TO_DATE('{$current_date}', '%d.%m.%Y')   
	      )
	    ) AND
		{$wp}postmeta.meta_value = '" . $id . "'
	GROUP BY
		{$wp}posts.ID
	ORDER BY
		{$wp}term_taxonomy.order ASC, bs_max_meta.meta_value ASC";

	$cache    = wp_cache_get('bookmaker_bonus_id_' . $id);
	if ( $cache !== false ) {
		$bonus_id = $cache;
	} else {
		$bonus_id = intval( $wpdb->get_var( $bonus_query ) );
		wp_cache_add( 'bookmaker_bonus_id_' . $id, $bonus_id );
    }

	if ( $bonus_id > 0 ) {


		$posts = wp_cache_get('bookmaker_bonus_' . $id);
		if ( $posts === false ) {

			$args  = [
				'posts_per_page' => 1,
				'post_type'      => 'bonuses',
				'post__in'       => [ $bonus_id ],
				'meta_query'     => [
					'relation' => 'AND',
					[
						'key'   => 'bs_bm_id',
						'value' => $id
					]
				]
			];
			$query = new WP_Query( $args );
			wp_cache_add( 'bookmaker_bonus_' . $id, $query->posts );
			$posts = $query->posts;
		}
        if (is_array($posts) && count($posts)) {
            foreach ($posts as $_bonus ) {
                if ( $data_only ) {
                    $bonus = [];
                    $types = wp_get_post_terms( $_bonus->ID, 'bonus_type' );
                    $type  = '';

                    if ( $types ) {
                        $type_list = [];
                        foreach ( $types as $_type ) {
                            $type_list[] = $_type->name;

                            if ( $_type->name == 'Приветственный бонус' ) {
                                $type_list[0] = $_type->name;
                            }
                            if ( $_type->name == 'Фрибет' && $type_list[0] != 'Приветственный бонус' ) {
                                $type_list[0] = $_type->name;
                            }
                        }
                        $type = $type_list[0];
                    }

                    $bonus['type']     = $type;
                    $bonus['bs_max']   = number_format( floatval( esc_attr( str_replace( ' ', '', get_post_meta( $_bonus->ID, 'bs_max', true ) ) ) ), 0, ',', ' ' );
                    $bonus['currency'] = get_currency_sym( esc_attr( get_post_meta( $_bonus->ID, 'bs_max_val', true ) ) )['symbol'];
                } else {

                    if ( $show_type ) {
                        $types = get_the_terms( $_bonus->ID, 'bonus_type' );
                        $type  = '';

                        if ( $types ) {
                            $type_list = [];
                            foreach ( $types as $_type ) {
                                $type_list[] = $_type->name;
                            }
                            $type = $type_list[0];
                        }
                        $result = $type . ', <span>' . number_format( floatval( esc_attr( str_replace( ' ', '', get_post_meta( $_bonus->ID, 'bs_max', true ) ) ) ), 0, ',', ' ' ) . ' ' . get_currency_sym( esc_attr( get_post_meta( $_bonus->ID, 'bs_max_val', true ) ) )['symbol'] . '</span>';
                    } else {
                        $result = '<span>' . number_format( floatval( esc_attr( str_replace( ' ', '', get_post_meta( $_bonus->ID, 'bs_max', true ) ) ) ), 0, ',', ' ' ) . ' ' . get_currency_sym( esc_attr( get_post_meta( $_bonus->ID, 'bs_max_val', true ) ) )['symbol'] . '</span>';
                    }
                }
            }

        }





	}

	return $result;
}

// A callback function to add a custom field to our "bonus_type" taxonomy
function bonus_type_taxonomy_custom_fields( $tag ) {
	// Check for existing taxonomy meta for the term you're editing
	$t_id      = $tag->term_id; // Get the ID of the term you're editing
	$term_meta = get_option( "taxonomy_term_$t_id" ); // Do the check
	?>

    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="color"><?php _e( 'Цвет' ); ?></label>
        </th>
        <td>
            <input type="text" name="term_meta[color]" id="term_meta[color]" size="25" style="width:60%;"
                   value="<?php echo $term_meta['color'] ? $term_meta['color'] : ''; ?>"><br/>
            <span class="description"><?php _e( 'HEX параметр, пример: #ffffff' ); ?></span>
        </td>
    </tr>

	<?php
}

// A callback function to save our extra taxonomy field(s)
function save_taxonomy_custom_fields( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id      = $term_id;
		$term_meta = get_option( "taxonomy_term_$t_id" );
		$cat_keys  = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset( $_POST['term_meta'][ $key ] ) ) {
				$term_meta[ $key ] = $_POST['term_meta'][ $key ];
			}
		}
		//save the option array
		update_option( "taxonomy_term_$t_id", $term_meta );
	}
}

add_action( 'bonus_type_edit_form_fields', 'bonus_type_taxonomy_custom_fields', 10, 2 );
add_action( 'edited_bonus_type', 'save_taxonomy_custom_fields', 10, 2 );

add_action( 'add_meta_boxes', 'bonus_options_metabox' );
function bonus_options_metabox() {
	$screens = array( 'bonuses' );
	add_meta_box( 'bonus_options', 'Настройки бонуса', 'bonus_options_html', $screens, 'normal', 'high' );
}

function bonus_options_html( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'bonus_is_public_noncename' );
	wp_nonce_field( plugin_basename( __FILE__ ), 'bonus_h1_noncename' );
	$is_public = get_post_meta( $post->ID, 'bonus_is_public', true );
	$h1        = get_post_meta( $post->ID, 'bonus_h1', true ); ?>
    <label style="width: 100%; display: block;">Заголовок h1</label>
    <input style="width: 100%; display: block; margin-bottom: 10px;" type="text" name="bonus_h1"
           value="<?php echo $h1; ?>">
    <label style="width: 100%; display: block;">Бонус имеет свою страницу?</label>
    <select name="bonus_is_public" id="bonus_is_public">
        <option value="no">Нет</option>
        <option<?php if ( $is_public == 'yes' ) {
			echo ' selected="selected"';
		} ?> value="yes">Да
        </option>
    </select>
<?php }

add_action( 'save_post', 'bonus_options_save' );
function bonus_options_save( $post_id ) {
	if ( ! isset( $_POST['bonus_is_public'] ) && ! isset( $_POST['bonus_h1'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['bonus_is_public_noncename'], plugin_basename( __FILE__ ) ) && ! wp_verify_nonce( $_POST['bonus_h1_noncename'], plugin_basename( __FILE__ ) ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, 'bonus_is_public', $_POST['bonus_is_public'] );
	update_post_meta( $post_id, 'bonus_h1', $_POST['bonus_h1'] );
}

/*add_action( 'template_redirect', 'redirect_bonus_to_404' );
function redirect_bonus_to_404() {
	global $post;
	if ( !is_null($post) && $post->post_type == 'bonuses' ) {
		$is_public = get_post_meta( $post->ID, 'bonus_is_public', true );
		if ( $is_public != 'yes' ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			get_template_part( 404 );
			exit();
		}
	}
}*/

function getBonusesCount( $bk_id = false, $type = false ) {
	return (new BonusesFilter())->where([
		'bookmaker_id' => $bk_id,
        'bonus_type' => $type,
        'status' => 'active'
	])->limit(40)->getCount();

}

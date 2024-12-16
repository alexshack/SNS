<?php
class BonusesArchiveRouteController extends PublicRouteController {

	protected $template = '/templates/bonuses/archive.php';

	function index() {

		if($this->object->post_type === 'filter_bookmakers') {
			$second_content = get_post_meta( $this->object->ID, 'casino_bonus_text_after', 1 );
		} else {
			$second_content = get_post_meta($this->object->ID, 'page_append_text', 1);
		}

		$is_filter = false;
		$archive_title = do_shortcode($this->object->post_title);
		$limit = 30;
		$filter_settings = BonusesFilter::getFilterSettings($this->object->ID);
		$filter_settings['status'] = 'active';
		$slice_query = [];
		global $wp_query;
		if ( isset( $wp_query->query['type'] ) || isset( $wp_query->query['bk'] ) ) {
			$is_bk_bonuses = false;
			$slice_args = [
				'posts_per_page' => -1,
				'post_type' => 'filter_bookmakers',
				'post_status' => 'publish',
			];
			$slice_args['meta_query'] = [
				'relation' => 'AND',
				[
					'key' => 'predicts_type',
					'value' => 'bonuses'
				]				
			];
			if ( isset( $wp_query->query['type'] ) ) {
				$filter_settings['bonus_type'] = explode(',', $wp_query->query['type']);
				$archive_title = 'Бонусы по вашим фильтрам';
				$is_filter = true;
				$slice_args['meta_query'][] = [
					'key'   => 'bonus_type',
					'value' => $wp_query->query['type'],
					'compare' => 'LIKE'
				];				
			} 
			if ( isset( $wp_query->query['bk'] ) ) {
				$filter_settings['bookmaker_id'] = explode(',', $wp_query->query['bk']);
				$archive_title = 'Бонусы по вашим фильтрам';
				$is_filter = true;
				$slice_args['meta_query'][] = [
					'key' => 'promotarget_title',
					'value' => $wp_query->query['bk'],
					'compare' => 'LIKE'
				];			
			} else {
				$slice_args['meta_query'][] = [
					'key'   => 'promotarget_title',
					'compare' => 'NOT EXISTS'
				];
			}

			if (isset( $wp_query->query['bk'] ) && ! isset( $wp_query->query['type'] ) ) {
				$slice_args = [
					'posts_per_page' => -1,
					'post_type'      => 'bookmakers',
					'post_status'    => 'publish',
					'include'        => $wp_query->query['bk']
				];
				$is_bk_bonuses = true;
			}
			$slice_query = get_posts( $slice_args );
			if (count($slice_query) == 1) {
				if ($is_bk_bonuses) {
					$slug = basename( get_permalink( $slice_query[0]->ID ) );
					$slice_url = home_url() . '/bonusy-' . $slug . '/';
				} else {
					$slice_url = get_the_permalink($slice_query[0]->ID);
				}
				wp_redirect( $slice_url );
				exit;
			}
		}
		
		$filter = (new BonusesFilter())->where($filter_settings);
		$bonuses_count = $filter->getCount();
		$bonuses = $filter->limit($limit)->order('popular')->getResults();
		$this->head_styles = [
			//get_template_directory() . '/css/blocks/bonus-loop-2.css',
		];

		$buttonText = 'Забрать бонус';

		if(isset($filter_settings['bonus_type']) && is_array($filter_settings['bonus_type'])) {

            if(in_array(2114, $filter_settings['bonus_type'])) {

                $buttonText = 'Получить промокод';

            }

            if(in_array(314, $filter_settings['bonus_type'])) {

                $buttonText = 'Получить фрибет';

            }

        }

        $this->data = [
        	'archive_title' => $archive_title,
	        'second_content' => do_shortcode(apply_filters( 'the_content', $second_content )),
	        'object' => $this->object,
	        'bonuses_objects' => Bonuses::setup($bonuses),
	        'bonuses_count' => $bonuses_count,
            'filter_settings' => $filter_settings,
            'bookmakers' => $filter->getBookmakers(),
            'button_text' => $buttonText,
            'is_filter' => $is_filter,
        ];

		$this->scripts = [
			get_template_directory_uri() . '/js/load-bonuses.js',
			get_template_directory_uri() . '/js/archive-menu.js',
		];

	}

	function footer() {

		$ajax_data = [
			'post_id' => $this->object->ID,
			'settings' => $this->data['filter_settings'],
			'order' => 'popular',
			'limit' => 30,
			'offset' => 0,
			'total_count' => $this->data['bonuses_count'],
			'display_count' => 30
		];
		?>
		<script>
            let bonuses_ajax_data = '<?php echo json_encode($ajax_data); ?>';
		</script>

	<?php }
}

<?php

	function bonuses_save_meta_box($post_id)
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if ($parent_id = wp_is_post_revision($post_id)) {
			$post_id = $parent_id;
		}
		$fields = [
			'bonus_text',
			'bs_bm_id',
			'bs_text_short',
			'bs_date_start',
			'bs_date_end',
			'bs_value',
			'bs_shortcode',
			'bs_max',
			'bs_max_val',
			'bs_min',
			'bs_min_val',
			'bs_wager',
			'bs_wager_time',
			'bs_kf_start',
			'bs_kf_end'
		];
		foreach ($fields as $field) {
			if (array_key_exists($field, $_POST)) {
				update_post_meta($post_id, $field, $_POST[$field]);
			}
		}
		
	    if (array_key_exists('bs_date_no_limit', $_POST)) {
	        update_post_meta( $post_id, 'bs_date_no_limit', $_POST['bs_date_no_limit'] );
	    } else {
		    update_post_meta( $post_id, 'bs_date_no_limit', '' );
	    }
		if(get_post_type($post_id) === 'bonuses') {
			update_post_meta($post_id, 'weight', get_post_meta($post_id, 'weight', true));
			update_post_meta($post_id, 'order', get_post_meta($post_id, 'order', true));
			update_post_meta($post_id, 'bs_date_no_limit', get_post_meta($post_id, 'bs_date_no_limit', true));
			BonusesMigrate::saveData($post_id);
			BonusesMigrate::migrateTermsData($post_id);
		}

	}

	add_action('save_post', 'bonuses_save_meta_box');

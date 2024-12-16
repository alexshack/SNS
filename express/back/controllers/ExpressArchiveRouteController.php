<?php
class ExpressArchiveRouteController extends PublicRouteController {

	protected $template = '/templates/express/archive/archive.php';

	function index() {

		$predicts_list_args = [];
		$author_block_title = get_post_meta($this->object->ID, 'author_block_title', true );
		$faq = get_post_meta( get_the_ID(), '_faq_info', true );
		$faq_info = isset( $faq_info ) && $faq_info['counter__0'][0] != '' ? $faq : false;

		$this->head_styles = [
			get_template_directory() . '/css/blocks/express-list.css',
			get_template_directory() . '/css/blocks/bookmaker-items.css',
		];

		$second_content = get_post_meta($this->object->ID, 'page_append_text', 1);

		$this->data_layer = [
			'event_category' => 'Express',
			'page_category' => 'Экспрссы дня',
			'page_name' => do_shortcode(get_the_title())
		];

		$limit = 15;
		$where = [
		    'post_status' => 'publish', 
		];
		$express_filter = new ExpressFilter($where, ['limit' => $limit]);
		$express_object = new Expresses($express_filter->getResults());
		$expresses = $express_object->getPosts();

		$this->data = [
			'post_content' => do_shortcode(apply_filters( 'the_content', $this->object->post_content )),
			'second_content' => do_shortcode(apply_filters( 'the_content', $second_content )),
			'archive_title' => do_shortcode($this->object->post_title),
			'predicts_list_args' => $predicts_list_args,
			'is_contains_video' => containsVideo($this->object->post_content . $second_content),
			'share_template' => $this->template_dir . '/templates/share.php',
			'author_block_title' => ($author_block_title) ? $author_block_title : 'Материал подготовлен',
			'author_id' => get_the_author_meta( 'ID' ),
			'is_no_comments' => is_user_logged_in() && intval(get_comments_number()) === 0,
			'faq' => $faq_info,
			'faq_schema_template' => $this->template_dir . '/templates/faq-schema.php',
			'expresses' => $expresses,
		];

	}

}

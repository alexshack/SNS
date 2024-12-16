<?php

add_action('admin_init', function() {
	if(isset($_GET['migrate_bonuses'])) {

		$bonuses = (new PostsQuery('p'))->select([
			'ID'
		])->where([
			'post_type' => 'bonuses'
		])->limit(5000)->get_results();


		foreach ($bonuses as $bonus) {
			BonusesMigrate::migrateMainData($bonus->ID);
			BonusesMigrate::migrateTermsData($bonus->ID);
		}

	}

});
add_action('before_delete_post', ['BonusesTableUpdater', 'removeData']);
add_action('before_delete_post', ['BonusesMetaTableUpdater', 'removeData']);

add_action( 'admin_head', 'bonus_meta_box_info', 100 );
function bonus_meta_box_info() {
    add_meta_box('info_doc_bonus', __('Инструкция для КМ'), 'bonus_meta_box_info_render', 'bonuses', 'side', 'high');
}

function bonus_meta_box_info_render() {
	?>
	<div class="custom-meta-box" id="info_doc_bonus">
		<a href="https://docs.google.com/document/d/1QDAA4RA9629JlyWQpJ4CQRwkgGqt7DtI0LGJE8QlnB4/edit" target="_blank">ИНСТРУКЦИЯ БОНУСЫ</a>
	</div>
	<?php
}
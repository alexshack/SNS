<?php get_header(); ?>
<?php global $wp_query;

Enqueue::footer('loadmore-predicts.js');

$term = Cyber::getOption('predicts_cat');

$limit = 29;
$where = [
    'post_status' => 'publish', 
    'sport'       => $term
];

$post_title = get_the_title();
$current_section = ''; 

if (get_query_var('tournament')) {
    $where['tournament'] = Cyber::$predicts_sections['predicts']['children'][get_query_var('tournament')]['cat_id'];
    $post_title .= ' | ' . Cyber::$predicts_sections['predicts']['children'][get_query_var('tournament')]['page_name'];
    $current_section = get_the_permalink(Cyber::getOption('predicts_page'));
    $page_content = apply_filters( 
        'the_content', 
        get_post_meta(Cyber::$predicts_sections['predicts']['children'][get_query_var('tournament')]['slice_id'], 'page_append_text', 1)
    );
} else {
    $page_content = apply_filters( 'the_content', get_the_content() );
}
$predicts_filter = new PredictsFilter($where, ['limit' => $limit]);
$predicts_object = new Predicts($predicts_filter->getResults());
$predicts = $predicts_object->getPosts();
$predicts_count = $predicts_filter->getCount();
$load_arguments = json_encode([
    'where' => $where,
    'args' => [
        'order' => 'date',
        'loop_template' => 'table',
        'limit' => $limit,
        'offset' => 30
    ]
]);
$show_load_more_button = $predicts_count > $limit;

$games = Cyber::getGames(true);
$bookmaker = Bookmaker::setup_all(9047);


$place_mob   = 1;
$place_desk  = 4;
$place_short = 7;

?>
<div class="wrapper wrapper-bookmaker">
    <div class="main main-page-cyber">
        <h1><?php echo $post_title ?></h1>
    </div>
</div>
<div class="wrapper wrapper-bookmaker">
    <div class="main main-page-cyber">
		<?php Template::render('templates/cyber/header', ['current_section' => $current_section]); ?>
        <div class="progress-b hidden"></div>
        <div class="predicts-list predicts-list--table">
            <?php if(count($predicts)):
                $pr_has_day_was = false;
                $cur_time = strtotime(date('Y-m-d H:i:s') ) - 60*60;
                foreach ( $predicts as $i => $predict ):
                    $pr_class = '';
                    $pr_has_day = false;
                    $pr_inside = false;
                    $pr_time = strtotime(date('Y-m-d H:i:s', $predict->date ));   
                    if($predict->hasPromoCode()) {
                        $pr_class .= 'predicts-item__has-promo ';
                    }
                    if (intval($predict->metadata->get('pr_inside')) === 1 ) {
                        $pr_class .= 'predicts-item__has-inside ';
                        $pr_inside = true;
                    }
                    if ($cur_time < $pr_time && !$pr_has_day_was) {
                        if($predict->metadata->get('predict_of_day') == 'on') {
                            $pr_class .= 'predicts-item__has-day ';
                            $pr_has_day = true;
                            $pr_has_day_was = true;
                        }
                    }
                    include 'loop-predicts.php'; 
                    if ($i == $place_mob) {
                        echo '<div class="predicts-item predicts-item-table banner-mob">';
                        echo do_shortcode('[wp_revive_banner zone_id="predicts"]');
                        echo '</div>';
                    }
                    if ($i == $place_desk) {
                        echo '<div class="predicts-item predicts-item-table banner-desc">';
                        echo do_shortcode('[wp_revive_banner zone_id="predicts"]');
                        echo '</div>';
                    }                    
                    if ($i == $place_short) {
                        echo do_shortcode('[online-broadcast type=cyber]');
                    }                  
                endforeach;
            else: ?>
                <p>Нет записей.</p>
            <?php endif; ?>
        </div>
        <?php if ($show_load_more_button) : ?>
            <div class="loadmore-predicts" style="text-align: center; margin-bottom: 20px;">
                <button data-action="load" data-load='<?php echo $load_arguments ?>' class="btn btn-gray predicts-ajax-button">Загрузить еще</button>
            </div>
        <?php endif; ?>        
        <?php Template::render('templates/cyber/footer'); ?> 
        <?php if($page_content) : ?>
            <div class="cyber-content bookmaker-description">
                <?php echo $page_content ?>
            </div>
        <?php endif; ?>                   
    </div>
    <aside class="sidebar">
		<?php Template::render('templates/cyber/sidebar', ['games' => $games, 'bookmaker' => $bookmaker]); ?>
    </aside>
</div>

<?php get_footer(); ?>

<?php get_header(); ?>
<?php global $wp_query;

$limit = get_option('posts_per_page') - 2;

$banner_pos_0 = 1;
$banner_pos_1 = 4;
$banner_pos_2 = 13;
$place_short = 7;

$page_title = get_the_title();
$current_section = ''; 

if (get_query_var('child')) {
    $term = Cyber::$posts_sections['posts']['children'][get_query_var('child')]['cat_id'];
    $page_title .= ' | ' . Cyber::$posts_sections['posts']['children'][get_query_var('child')]['cat_name'];
    $current_section = get_the_permalink(Cyber::getOption('posts_page'));
    $page_content = apply_filters( 'the_content', get_term_meta($term, 'bottom_content', 1 ));

} else {
    $term = Cyber::getOption('posts_cat');       
    $page_content = apply_filters( 'the_content', get_the_content() );
}

$model = new TermsModel($term);
$posts = $model->getPosts(['extra' => ['metadata', 'thumbnail'], 'get_from_cache' => false, 'limit' => $limit ]);

$games = Cyber::getGames(true);
$bookmaker = Bookmaker::setup_all(9047);

?>
<div class="wrapper wrapper-bookmaker">
    <div class="main main-page-cyber">
        <h1><?php echo get_the_title() ?></h1>
    </div>
</div>
<div class="wrapper wrapper-bookmaker">
    <div class="main main-page-cyber">     
        <?php Template::render('templates/cyber/header', ['current_section' => $current_section]); ?>
        <div class="articles-list">
            <?php if(count($posts)):
                foreach ( $posts as $i => $item ):
                    Template::render('templates/cyber/loop-posts', ['item' => (new PostModel($item)), 'model' => $model]);
                    if ( $i == $banner_pos_0 ) {
                        echo '<div class="articles-item banner-mob">';
                        echo do_shortcode('[wp_revive_banner zone_id="articles_1"]');
                        echo '</div>';  
    
                    }                        
                    if ( $i == $banner_pos_1 ) {
                        echo '<div class="articles-item banner-desc">';
                        echo do_shortcode('[wp_revive_banner zone_id="articles_1"]');
                        echo '</div>';  
    
                    }
                    if ( $i == $banner_pos_2 ) {
                        echo '<div class="articles-item banner-desc">';
                        echo do_shortcode('[wp_revive_banner zone_id="articles_2"]');
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
        <?php if ( 1 === 0 && (new TermsModel($term))->getPosts(['get' => 'count']) > get_option('posts_per_page')): ?>
            <div class="loadmore">
                <button class="btn btn-gray">Загрузить еще</button>
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

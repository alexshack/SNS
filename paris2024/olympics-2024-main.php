<?php
/**
 * Template Name: Олимпиада 2024 - Главная 
 * @package stavkinasport.com
 */
?>
<?php
get_header();

Enqueue::footer('predicts-list.css');
Enqueue::footer('article-loop.css');
$controller = Paris2024ScheduleController::get();
$paris_options = get_option( 'paris2024_settings' );

?>

<?php echo $controller->getTopScheduleContent(); ?> 

<?php if ( function_exists( 'breadcrumbs' ) ) {
    breadcrumbs();
} ?>

<div class="wrapper olympics_header">

        <div class="olympics_header-wrapper">
            <div class="olympics_header-logo olympics_header-logo-desktop">
                <img class="olympics_header-logo_image"
                     src="<?php print get_template_directory_uri(); ?>/img/olympics/logo-2024.svg"
                     width="261"
                     height="315"
                     alt="<?php the_title(); ?>">
            </div>         
            <div class="olympics_header-block">
                <div class="olympics_header-logo olympics_header-logo-mobile">
                    <img class="olympics_header-logo_image"
                         src="<?php print get_template_directory_uri(); ?>/img/olympics/logo-2024.svg"
                         width="261"
                         height="315"
                         alt="<?php the_title(); ?>">
                </div>                 
                <div class="olympics_header-block_main">
                    <h1 class="olympics_title"><?php the_title(); ?></h1>
                    <div class="olympics_descr"><?php the_content(); ?></div>
                </div>
                <div class="olympics_header-block_info">
                    <div class="olympics_header-block_info-item">
                        <svg class="olympics_header-block_info-icon">
                            <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/calendar.svg#calendar"></use>
                        </svg>
                        <div class="olympics_header-block_info-title">Даты проведения:</div>
                        <div class="olympics_header-block_info-value">26.07 — 11.08.2024</div>
                    </div>
                    <div class="olympics_header-block_info-item">
                        <svg class="olympics_header-block_info-icon">
                            <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/location.svg#location"></use>
                        </svg>
                        <div class="olympics_header-block_info-title">Место проведения:</div>
                        <div class="olympics_header-block_info-value">Париж</div>
                    </div>
                    <div class="olympics_header-block_info-item">
                        <svg class="olympics_header-block_info-icon">
                            <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/global.svg#global"></use>
                        </svg>
                        <div class="olympics_header-block_info-title">Страны:</div>
                        <div class="olympics_header-block_info-value">31</div>
                    </div>
                    <div class="olympics_header-block_info-item">
                        <svg class="olympics_header-block_info-icon">
                            <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/medal.svg#medal"></use>
                        </svg>
                        <div class="olympics_header-block_info-title">Дисциплины (медали):</div>
                        <div class="olympics_header-block_info-value">109</div>
                    </div>                                                
                </div>
                <div class="olympics_header-buttons">
                    <a href="#calendar" class="olympics_header-buttons_btn">Календарь</a>
                    <a href="#calendar" class="olympics_header-buttons_btn">Медальный зачет</a>
                    <a href="#news" class="olympics_header-buttons_btn">Новости</a>
                    <a href="#predicts" class="olympics_header-buttons_btn">Прогнозы</a>
                </div>                
            </div>

        </div>
</div>

<div class="wrapper">
<?php 
/*echo '<pre>';
print_r($paris_options);
echo '</pre>';*/
?>    
    <div class="olympics">
        <div class="olympics_buttons">
            <a href="<?php echo $paris_options['page_football'] ?>" class="olympics_buttons-btn">
                <svg class="olympics_buttons-btn-icon">
                    <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/football.svg#football"></use>
                </svg> 
                Футбол              
            </a>
            <a href="<?php echo $paris_options['page_basketball'] ?>" class="olympics_buttons-btn">
                <svg class="olympics_buttons-btn-icon">
                    <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/basketball.svg#basketball"></use>
                </svg> 
                Баскетбол              
            </a>
            <a href="<?php echo $paris_options['page_tennis'] ?>" class="olympics_buttons-btn">
                <svg class="olympics_buttons-btn-icon">
                    <use xlink:href="<?php echo SNS_URL;  ?>/img/olympics/tennis.svg#tennis"></use>
                </svg> 
                Тенис              
            </a>                       
        </div>

        <?php if ( $controller->getNewsTotal() ): ?>
            <div class="olympics_block olympics_news" id="news">
                <?php print $controller->getNewsContent(); ?>
            </div>
        <?php endif; ?>

        <div class="olympics_block" id="calendar">
            <h2 class="olympics_block-title">Календарь с медальным зачетом</h2>
            <div class="olympics_block-content tab-wrapper">
                <div class="olympics_block-tabs">
                    <div class="tab-btn active" data-tab="tab-schedule">Календарь</div>
                    <div class="tab-btn" data-tab="tab-medals">Медальный зачет</div>
                </div>
                <div class="tab-content open" id="tab-schedule">
				    <?php echo $controller->getMainScheduleContent(); ?>
                    <div class="olympics_schedule-more">
                        <a href="#full-schedule" class="olympics_schedule-more_button">Смотреть расписание</a>
                    </div>
                </div>
                <div class="tab-content" id="tab-medals">
                    <?php echo $controller->getMedalsContent(); ?>
                </div>
            </div>
        </div>

        <div class="olympics_block">
            <h2 class="olympics_block-title">Турнирная таблица</h2>
            <div class="olympics_block-content tab-wrapper">
                <div class="olympics_block-tabs">
                    <div class="tab-btn active" data-tab="tab-football">Футбол</div>
                    <div class="tab-btn" data-tab="tab-basketball">Баскетбол</div>
                </div>
                <div class="tab-content open" id="tab-football">
                    <?php 
/*                        $tables = $controller->getSportsTables(4919, 4869);
                        if ($tables) { 
                            foreach ($tables as $table) { 
                                $table_args = array(
                                    'id'         => $table->ID,
                                    'show_title' => true,
                                );  
                                sp_get_template( 'league-table.php', $table_args, SP()->template_path() . 'league/'  ); 
                            }
                        }     */                 

                    ?>
                </div>
                <div class="tab-content" id="tab-basketball">
                    <?php 
/*                        $tables = $controller->getSportsTables(4920, 4869);
                        if ($tables) { 
                            foreach ($tables as $table) { 
                                $table_args = array(
                                    'id'         => $table->ID,
                                    'show_title' => true,
                                );  
                                sp_get_template( 'league-table.php', $table_args, SP()->template_path() . 'league/'  ); 
                            }
                        }            */          

                    ?>
                </div>
            </div>
        </div>

        <?php
            $where = [
                'post_status' => 'publish', 
                'tournament'  => $controller->getOption('predicts_term')
            ];
            $predicts_filter = new PredictsFilter($where, ['limit' => 8]);
            $predicts_object = new Predicts($predicts_filter->getResults());
            $predicts = $predicts_object->getPosts();
        ?>
        <?php if ($predicts) : ?>
            <?php 
            $b_count = count( $predicts );
            if ( $b_count > 3 ) {
                $b_count = 4;
            }
            ?>
            <div class="olympics_block" id="predicts">
                <h2 class="olympics_block-title">Прогнозы</h2>
                <div class="bonuses-slider_wrapper">
                    <div class="olympics_posts olympics_overflow">
                        <?php foreach ( $predicts as $i => $predict ) {
                            include 'predicts/archive-list/loop-table.php';
                        } ?>
                    </div>
                    <?php if ( $b_count > 1 ) : ?>
                        <div class="slider_arrow slider_arrow_prev slider_arrow_<?php echo $b_count; ?>"></div>
                        <div class="slider_arrow slider_arrow_next slider_arrow_<?php echo $b_count; ?>"></div>
                    <?php endif; ?>                  
                </div>
            </div>
        <?php endif; ?>

        
        <?php $articles = (new TermsModel( $controller->getOption('articles_term') ))->getPosts(['limit' => 9, 'extra' => ['thumbnail', 'metadata']]); ?>
        <?php if ($articles) : ?>
            <?php 
            $b_count = count( $articles );
            if ( $b_count > 3 ) {
                $b_count = 4;
            }
            ?>            
            <div class="olympics_block">
                <h2 class="olympics_block-title">Статьи</h2>
                <div class="bonuses-slider_wrapper">
                    <div class="olympics_posts olympics_overflow">
                        <?php foreach ($articles as $article) : $article = new PostModel($article);
                            if($article->thumbnail) {
                                include get_template_directory() . '/templates/post/article-loop.php';
                            }
                        endforeach; ?>
                    </div>
                    <?php if ( $b_count > 1 ) : ?>
                        <div class="slider_arrow slider_arrow_prev slider_arrow_<?php echo $b_count; ?>"></div>
                        <div class="slider_arrow slider_arrow_next slider_arrow_<?php echo $b_count; ?>"></div>
                    <?php endif; ?>                     
                </div>
            </div>
        <?php endif; ?>

        <div class="olympics_block">
            <h2 class="olympics_block-title">Бонусы</h2>
            <?php echo do_shortcode( '[bonuses-slider bonus_type="best"]' ); ?>
        </div>

        <div class="olympics_block" id="full-schedule">
            <h2 class="olympics_block-title">Расписание</h2>
            <div class="olympics_block-content olympics_block_inside olympics_block-content-calendar">
                <?php echo $controller->getScheduleContent(); ?>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener( 'click', function( event ) {
    if ( event.target.matches( '.more-btn' ) ) {
        event.preventDefault();
        let parentBlock = event.target.closest('.more-wrapper');
        [].forEach.call( parentBlock.querySelectorAll( '.more-hidden' ), function( el ) {
            el.classList.remove( 'more-hidden' );
        } );
        event.target.parentNode.remove();

    }
} );
document.addEventListener( 'click', function( event ) {
    if ( event.target.matches( '.tab-btn' ) || event.target.parentNode.matches( '.tab-btn' ) ) {
        event.preventDefault();
        let parentBlock = event.target.closest('.tab-wrapper');
        if (event.target.matches( '.tab-btn' )) {
            var tabBtn = event.target;
        } else {
            var tabBtn = event.target.closest('.tab-btn');
        }
        var tab = document.getElementById( tabBtn.getAttribute( 'data-tab' ) );
        [].forEach.call( parentBlock.querySelectorAll( '.tab-content' ), function( el ) {
            el.classList.remove( 'open' );
        } );
        [].forEach.call( parentBlock.querySelectorAll( '.tab-btn' ), function( el ) {
            el.classList.remove( 'active' );
        } );
        tabBtn.classList.add('active');     
        tab.classList.add('open');
    }
} );
</script>

<?php get_footer(); ?>

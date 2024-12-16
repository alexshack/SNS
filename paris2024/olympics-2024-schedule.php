<?php
/**
 * Template Name: Олимпиада 2024 - Расписание 
 * @package stavkinasport.com
 */
?>
<?php
get_header();

Enqueue::footer('predicts-list.css');
Enqueue::footer('article-loop.css');
$controller = Paris2024ScheduleController::get();
$paris_options = get_option( 'paris2024_settings' );

$main_page = get_post( $paris_options['page_main'] );
$main_page_link = get_the_permalink( $main_page );
?>

<?php echo $controller->getTopScheduleContent(); ?> 

<?php if ( function_exists( 'breadcrumbs' ) ) {
    breadcrumbs();
} ?>

<div class="wrapper olympics_header">
        <div class="olympics_header-wrapper">
            <a class="olympics_header-logo olympics_header-logo-desktop" href="<?php echo $main_page_link ?>">
                <img class="olympics_header-logo_image"
                     src="<?php print get_template_directory_uri(); ?>/img/olympics/logo-2024.svg"
                     width="261"
                     height="315"
                     alt="<?php the_title(); ?>">
            </a>         
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
                    <a href="<?php echo $main_page_link ?>#calendar" class="olympics_header-buttons_btn">Календарь</a>
                    <a href="<?php echo $main_page_link ?>#calendar" class="olympics_header-buttons_btn">Медальный зачет</a>
                    <a href="<?php echo $main_page_link ?>#news" class="olympics_header-buttons_btn">Новости</a>
                    <a href="<?php echo $main_page_link ?>#predicts" class="olympics_header-buttons_btn">Прогнозы</a>
                </div>                
            </div>

        </div>
</div>

<div class="wrapper">
    <div class="olympics">


        <div class="olympics_block" >
            <h2 class="olympics_block-title">Расписание</h2>
            <div class="olympics_block-content olympics_block_inside olympics_block-content-calendar">
                <?php echo $controller->getScheduleContentAll(); ?>
                <?php //echo $controller->getScheduleByDateContent(); ?>
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

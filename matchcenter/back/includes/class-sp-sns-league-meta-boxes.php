<?php
/**
 * League Meta Boxes
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SP_SNS_League_Meta_Boxes
 */

class SP_SNS_League_Meta_Boxes {

    public function __construct() {
        add_action( 'sp_league_add_form_fields',  array( $this, 'add_league_fields') ); 
        add_action( 'sp_league_edit_form_fields', array( $this, 'add_league_edit_fields'), 10, 2 ); 
        add_action( 'created_sp_league',          array( $this, 'save_league_fields') );
        add_action( 'edited_sp_league',           array( $this, 'save_league_fields') );
        add_action( 'admin_enqueue_scripts',      array( $this, 'load_media' ) );
        add_action( 'admin_footer',               array( $this, 'add_script' ) );        
    }

    public function load_media() {
        wp_enqueue_media();
    }

    public function add_league_fields($taxonomy) {
        $sports = SP_SNS_Theme::getSports();
        echo '
            <div class="form-field">
                <label for="sport_type">Вид спорта</label>
                <select name="sport_type" id="sport_type">
                    <option value="">Выберите вид спорта</option>';
                    foreach ( $sports as $sport ) {
                        echo '<option value="' . $sport->type . '">' . $sport->name . '</option>';
                    } 
            echo  '</select>
            </div>        
            <div class="form-field">
                <label><input type="checkbox" name="is_national" />Лига сборных команд</label>
            </div>      
            <div class="form-field">
                <label><input type="checkbox" name="hide_transfers" />Скрыть трансферы</label>
            </div>
            <div class="form-field">
                <label for="api_fonbet">Fonbet API ID</label>
                <input type="number" name="api_fonbet" id="api_fonbet" />
            </div>            
            <div class="form-field">
                <label for="seo_title_main">Обзор SEO Title</label>
                <input type="text" name="seo_title_main" id="seo_title_main" />
            </div>
            <div class="form-field">
                <label for="seo_description">Обзор SEO Description</label>
                <input type="text" name="seo_description_main" id="seo_description_main" />
            </div>
            <div class="form-field">
                <label for="seo_title_table">Таблица SEO Title</label>
                <input type="text" name="seo_title_table" id="seo_title_table" />
            </div>
            <div class="form-field">
                <label for="seo_description_table">Таблица SEO Description</label>
                <input type="text" name="seo_description_table" id="seo_description_table" />
            </div>
            <div class="form-field">
                <label for="seo_title_calendar">Календарь SEO Title</label>
                <input type="text" name="seo_title_calendar" id="seo_title_calendar" />
            </div>
            <div class="form-field">
                <label for="seo_description_calendar">Календарь SEO Description</label>
                <input type="text" name="seo_description_calendar" id="seo_description_calendar" />
            </div>
            <div class="form-field">
                <label for="seo_title_transfers">Трансферы SEO Title</label>
                <input type="text" name="seo_title_transfers" id="seo_title_transfers" />
            </div>
            <div class="form-field">
                <label for="seo_description_transfers">Трансферы SEO Description</label>
                <input type="text" name="seo_description_transfers" id="seo_description_transfers" />
            </div>
            <div class="form-field term-group">
                <label for="league_bg_id">Фон для прогноза</label>
                <input type="hidden" id="league_bg_id" name="league_bg_id" class="custom_media_url" value="">
                <div id="category-image-wrapper"></div>
                <p>
                    <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="Загрузить" />
                    <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="Удалить" />
                </p>
            </div>
            ';

    }

    public function add_league_edit_fields( $term, $taxonomy ) {
     
        $is_cron                   = get_term_meta( $term->term_id, 'is_cron', true );
        $is_national               = get_term_meta( $term->term_id, 'is_national', true );
        $hide_transfers            = get_term_meta( $term->term_id, 'hide_transfers', true );
        $seo_title_main            = get_term_meta( $term->term_id, 'seo_title_main', true );
        $seo_description_main      = get_term_meta( $term->term_id, 'seo_description_main', true );
        $seo_title_table           = get_term_meta( $term->term_id, 'seo_title_table', true );
        $seo_description_table     = get_term_meta( $term->term_id, 'seo_description_table', true );
        $seo_title_calendar        = get_term_meta( $term->term_id, 'seo_title_calendar', true );
        $seo_description_calendar  = get_term_meta( $term->term_id, 'seo_description_calendar', true );
        $seo_title_transfers       = get_term_meta( $term->term_id, 'seo_title_transfers', true );
        $seo_description_transfers = get_term_meta( $term->term_id, 'seo_description_transfers', true ); 
        $league_bg_id              = get_term_meta( $term->term_id, 'league_bg_id', true );
        $league_bg_story_id        = get_term_meta( $term->term_id, 'league_bg_story_id', true );
        $content_main              = get_term_meta( $term->term_id, 'content_main', true );
        $content_table             = get_term_meta( $term->term_id, 'content_table', true );
        $content_calendar          = get_term_meta( $term->term_id, 'content_calendar', true );
        $content_transfers         = get_term_meta( $term->term_id, 'content_transfers', true );
        $sport_type                = get_term_meta( $term->term_id, 'sport_type', true );
        $api_fonbet                = get_term_meta( $term->term_id, 'api_fonbet', true );
        $api_winline               = get_term_meta( $term->term_id, 'api_winline', true );
        $api_betboom               = get_term_meta( $term->term_id, 'api_betboom', true );
        $news_term                 = get_term_meta( $term->term_id, 'news_term', true );
        $articles_term             = get_term_meta( $term->term_id, 'articles_term', true );
        $predicts_term             = get_term_meta( $term->term_id, 'predicts_term', true );
        $season_term               = get_term_meta( $term->term_id, 'season_term', true );
        $season_terms              = get_term_meta( $term->term_id, 'season_terms', true );
        $predict_taxonomy          = get_option('sp_sns_predicts_taxonomy_league');
        $default_season_id         = get_option('sportspress_season');
        $default_season            = get_term_by( 'id', $default_season_id, 'sp_season' ); 

        $sports = SP_SNS_Theme::getSports();

        $noimage_src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 1000 1000'%3E%3Cpath fill='%23bbb' d='M430 660 l0 -90 -90 0 -90 0 0 -70 0 -70 90 0 90 0 0 -85 0 -85 70 0 70 0 0 85 0 85 90 0 90 0 0 70 0 70 -90 0 -90 0 0 90 0 90 -70 0 -70 0 0 -90z'%3E%3C/path%3E%3C/svg%3E";
        $league_bg_img = '<img width="150" height="100" alt="" src="' . $noimage_src . '">';
        if ($league_bg_id) {
            $league_bg_img = wp_get_attachment_image ( $league_bg_id, 'thumbnail' );
        }
        $league_bg_story_img = '<img width="150" height="100" alt="" src="' . $noimage_src . '">';
        if ($league_bg_story_id) {
            $league_bg_story_img = wp_get_attachment_image ( $league_bg_story_id, 'thumbnail' );
        }

        echo '
        <tr>
        <th>Вид спорта</th>
        <td>
        <select name="sport_type" id="sport_type">
            <option value="">Выберите вид спорта</option>';
            foreach ( $sports as $sport ) {
                echo '<option value="' . $sport->type . '"' . selected( $sport->type, $sport_type, false ) . '>' . $sport->name . '</option>';
            } 
        echo  '</select>
        </td>
        </tr>        
        <tr>
        <th>Сезоны лиги</th>
        <td>
        <select name="season_terms[]" multiple>
            ' . $this->getTerms($season_terms, 'sp_season', true) . '
        </select>
        </td>
        </tr>        
        <tr>
        <th>Текущий сезон</th>
        <td>
        <select name="season_term" id="season_term">
            <option value="">По умолчанию (' . $default_season->name . ')</option>' .
            $this->getTerms($season_term, 'sp_season') .
        '</select>
        </td>
        </tr> 

        <tr>
        <th>Категория новостей</th>
        <td>
        <select name="news_term" id="news_term">
            <option value="">Не показывать новости</option>' .
            $this->getTerms($news_term, 'category') .
        '</select>
        </td>
        </tr>               
        <tr>
        <th>Категория статей</th>
        <td>
        <select name="articles_term" id="articles_term">
            <option value="">Не показывать статьи</option>' .
            $this->getTerms($articles_term, 'category') .
        '</select>
        </td>
        </tr>               
        <tr>
        <th>Категория прогнозов</th>
        <td>
        <select name="predicts_term" id="predicts_term">
            <option value="">Не показывать прогнозы</option>' .
            $this->getTerms($predicts_term, $predict_taxonomy) .
        '</select>
        </td>
        </tr>               
        <tr>        
        <th>Загружать автоматически</th>
        <td>
        <label><input type="checkbox" name="is_cron" ' . checked( 'yes', $is_cron, false ) . ' /> Да</label>
        </td>
        </tr>
        <tr>        
        <th>Лига сборных команд</th>
        <td>
        <label><input type="checkbox" name="is_national" ' . checked( 'yes', $is_national, false ) . ' /> Да</label>
        </td>
        </tr>                
        <tr>
        <th>Скрыть трансферы</th>
        <td>
        <label><input type="checkbox" name="hide_transfers" ' . checked( 'yes', $hide_transfers, false ) . ' /> Да</label>
        </td>
        </tr>
        <tr class="form-field">
        <th><label for="api_winline">Winline API ID</label></th>
        <td><input name="api_winline" id="api_winline" type="text" value="' . esc_attr( $api_winline ) .'" /></td>
        </tr>         
        <tr class="form-field">
        <th><label for="api_fonbet">Fonbet API ID</label></th>
        <td><input name="api_fonbet" id="api_fonbet" type="text" value="' . esc_attr( $api_fonbet ) .'" /></td>
        </tr>
        <tr class="form-field">
        <th><label for="api_betboom">Betboom API ID</label></th>
        <td><input name="api_betboom" id="api_betboom" type="text" value="' . esc_attr( $api_betboom ) .'" /></td>
        </tr>               
        <tr class="form-field">
        <th><label>Метаполя</label></th>
        <td><label>Можно использовать [season]</label></td>
        </tr>
        <tr class="form-field">
        <th><label for="seo_title_main">Обзор SEO Title</label></th>
        <td><input name="seo_title_main" id="seo_title_main" type="text" value="' . esc_attr( $seo_title_main ) .'" /></td>
        </tr>
        <tr class="form-field">
        <th><label for="seo_description_main">Обзор SEO Description</label></th>
        <td><input name="seo_description_main" id="seo_description_main" type="text" value="' . esc_attr( $seo_description_main ) .'" /></td>
        </tr>'
        ?>
        <tr class="form-field">
            <th><label for="tag_content_main">Обзор текст</label></th>
            <td><?php wp_editor($content_main, 'tag_content_main', array('textarea_name' => 'content_main','editor_css' => '<style> .html-active .wp-editor-area{border:0;}</style>')) ?> 
            </td>
        </tr>
        <?php echo
        '<tr class="form-field">
        <th><label for="seo_title_table">Таблица SEO Title</label></th>
        <td><input name="seo_title_table" id="seo_title_table" type="text" value="' . esc_attr( $seo_title_table ) .'" /></td>
        </tr>
        <tr class="form-field">
        <th><label for="seo_description_table">Таблица SEO Description</label></th>
        <td><input name="seo_description_table" id="seo_description_table" type="text" value="' . esc_attr( $seo_description_table ) .'" /></td>
        </tr>'
        ?>
        <tr class="form-field">
            <th><label for="tag_content_table">Таблица текст</label></th>
            <td><?php wp_editor($content_table, 'tag_content_table', array('textarea_name' => 'content_table','editor_css' => '<style> .html-active .wp-editor-area{border:0;}</style>')) ?> 
            </td>
        </tr>
        <?php echo
        '
        <tr class="form-field">
        <th><label for="seo_title_calendar">Календарь SEO Title</label></th>
        <td><input name="seo_title_calendar" id="seo_title_calendar" type="text" value="' . esc_attr( $seo_title_calendar ) .'" /></td>
        </tr>
        <tr class="form-field">
        <th><label for="seo_description_calendar">Календарь SEO Description</label></th>
        <td><input name="seo_description_calendar" id="seo_description_calendar" type="text" value="' . esc_attr( $seo_description_calendar ) .'" /></td>
        </tr>'
        ?>
        <tr class="form-field">
            <th><label for="tag_content_calendar">Календарь текст</label></th>
            <td><?php wp_editor($content_calendar, 'tag_content_calendar', array('textarea_name' => 'content_calendar','editor_css' => '<style> .html-active .wp-editor-area{border:0;}</style>')) ?> 
            </td>
        </tr>
        <?php echo
        '
        <tr class="form-field">
        <th><label for="seo_title_transfers">Трансферы SEO Title</label></th>
        <td><input name="seo_title_transfers" id="seo_title_transfers" type="text" value="' . esc_attr( $seo_title_transfers ) .'" /></td>
        </tr>
        <tr class="form-field">
        <th><label for="seo_description_transfers">Трансферы SEO Description</label></th>
        <td><input name="seo_description_transfers" id="seo_description_transfers" type="text" value="' . esc_attr( $seo_description_transfers ) .'" /></td>
        </tr>'
        ?>
        <tr class="form-field">
            <th><label for="tag_content_transfers">Трансферы текст</label></th>
            <td><?php wp_editor($content_transfers, 'tag_content_transfers', array('textarea_name' => 'content_transfers','editor_css' => '<style> .html-active .wp-editor-area{border:0;}</style>')) ?> 
            </td>
        </tr>
        <?php echo
        '
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="league_bg_id">Фон для прогноза</label>
            </th>
            <td>
                <input type="hidden" id="league_bg_id" name="league_bg_id" value="' . $league_bg_id . '">
                <div id="category-image-wrapper">' . $league_bg_img . '</div>
                <p>
                    <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="Загрузить" />
                    <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="Удалить" />
                </p>
            </td>
        </tr>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="league_bg_story_id">Фон для сториз</label>
            </th>
            <td>
                <input type="hidden" id="league_bg_story_id" name="league_bg_story_id" value="' . $league_bg_story_id . '">
                <div id="story-image-wrapper">' . $league_bg_story_img . '</div>
                <p>
                    <input type="button" class="button button-secondary story_tax_media_button" id="story_tax_media_button" name="story_tax_media_button" value="Загрузить" />
                    <input type="button" class="button button-secondary story_tax_media_remove" id="story_tax_media_remove" name="story_tax_media_remove" value="Удалить" />
                </p>
            </td>
        </tr>        
         ';
        ?>

        
    <?php       
     
    }

    public function save_league_fields( $term_id ) {
     
        $league_fields = [
            'seo_title_main',
            'seo_description_main',
            'seo_title_table',
            'seo_description_table',
            'seo_title_calendar',
            'seo_description_calendar',
            'seo_title_transfers',
            'seo_description_transfers',
            'league_bg_id',
            'league_bg_story_id',
            'api_fonbet',
            'api_winline',
            'api_betboom',
            'news_term',
            'articles_term',
            'predicts_term',
            'season_term',
        ];

        $league_mce_fields = [
            'content_main',
            'content_table',
            'content_calendar',
            'content_transfers',
        ];

        $multiple_fields = [
            'season_terms',
        ];        

        foreach ($league_fields as $league_field) {
            if( isset( $_POST[ $league_field ] ) ) {
                update_term_meta( $term_id, $league_field, sanitize_text_field( $_POST[ $league_field ] ) );
            } else {
                delete_term_meta( $term_id, $league_field );
            }
        }

        foreach ($league_mce_fields as $league_field) {
            if( isset( $_POST[ $league_field ] ) ) {
                update_term_meta( $term_id, $league_field, $_POST[ $league_field ] );
            } else {
                delete_term_meta( $term_id, $league_field );
            }
        }

        foreach ($multiple_fields as $league_field) {
            if( isset( $_POST[ $league_field ] ) ) {
                update_term_meta( $term_id, $league_field, array_map( 'strip_tags', $_POST[ $league_field ] ) );
            } else {
                delete_term_meta( $term_id, $league_field );
            }
        }                  

        if( isset( $_POST[ 'is_cron' ] ) && 'on' == $_POST[ 'is_cron' ] ) {
            update_term_meta( $term_id, 'is_cron', 'yes' );
        } else {
            delete_term_meta( $term_id, 'is_cron' );
        }

        if( isset( $_POST[ 'is_national' ] ) && 'on' == $_POST[ 'is_national' ] ) {
            update_term_meta( $term_id, 'is_national', 'yes' );
        } else {
            delete_term_meta( $term_id, 'is_national' );
        }

        if( isset( $_POST[ 'hide_transfers' ] ) && 'on' == $_POST[ 'hide_transfers' ] ) {
            update_term_meta( $term_id, 'hide_transfers', 'yes' );
        } else {
            update_term_meta( $term_id, 'hide_transfers', 'no' );
        }       

        if( isset( $_POST[ 'sport_type' ] ) && '-1' != $_POST[ 'sport_type' ] ) {
            update_term_meta( $term_id, 'sport_type', $_POST[ 'sport_type' ] );
        } else {
            delete_term_meta( $term_id, 'sport_type' );
        } 
          
     
 
    }

    public function getTerms( $value, $taxonomy, $is_array = false ) {
        $terms = get_terms( [
          'taxonomy'   => $taxonomy,
          'hide_empty' => false,
        ] );
        $result = '';

        $list_terms = [];
        foreach ( $terms as $term ) {
            $list_terms[ $term->term_id ] = $term->name;
            //$result .= '<option value="' . $term->term_id . '" ' . selected( $value, $term->term_id, false ) . '>' . $term->name . '</option>';
        }

        ksort($list_terms);

        foreach ( $list_terms as $term_id => $term_name ) {
            if ( $taxonomy == 'sp_season' ) {
                $api_id = get_term_meta( $term_id, 'sns_apisport_id', true );
                $descr  = get_term_meta( $term_id, 'sns_description', true );
                $term_title = $term_name . ' (API ID ' . $api_id . ', ' . $descr . ')';
            } else {
                $term_title = $term_name;
            }
            if ( $is_array && is_array( $value ) ) {
                $selected = '';
                if ( in_array( $term_id, $value ) ) {
                    $selected = 'selected';
                }
                $result .= '<option value="' . $term_id . '" ' . $selected . '>' . $term_title . '</option>';
            } else {
                $result .= '<option value="' . $term_id . '" ' . selected( $value, $term_id, false ) . '>' . $term_title . '</option>';
            }
        }

        return $result;
    }

    public function add_script() { ?>
        <script>
            jQuery(document).ready( function($) {
                function ct_media_upload(button_class) {
                    var _custom_media = true,
                        _orig_send_attachment = wp.media.editor.send.attachment;
                    $('body').on('click', button_class, function(e) {
                        var button_id = '#'+$(this).attr('id');
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(button_id);
                        _custom_media = true;
                        wp.media.editor.send.attachment = function(props, attachment){
                            if ( _custom_media ) {
                                $('#league_bg_id').val(attachment.id);
                                $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                                $('#category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
                            } else {
                                return _orig_send_attachment.apply( button_id, [props, attachment] );
                            }
                        }
                        wp.media.editor.open(button);
                        return false;
                    });
                }
                ct_media_upload('.ct_tax_media_button.button');
                $('body').on('click','.ct_tax_media_remove',function(){
                    $('#league_bg_id').val('');
                    $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                });
                $(document).ajaxComplete(function(event, xhr, settings) {
                    var queryStringArr = settings.data.split('&');
                    if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
                        var xml = xhr.responseXML;
                        $response = $(xml).find('term_id').text();
                        if($response!=""){
                            // Clear the thumb image
                            $('#category-image-wrapper').html('');
                        }
                    }
                });

                function story_media_upload(button_class) {
                    var _custom_media = true,
                        _orig_send_attachment = wp.media.editor.send.attachment;
                    $('body').on('click', button_class, function(e) {
                        var button_id = '#'+$(this).attr('id');
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(button_id);
                        _custom_media = true;
                        wp.media.editor.send.attachment = function(props, attachment){
                            if ( _custom_media ) {
                                $('#league_bg_story_id').val(attachment.id);
                                $('#story-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                                $('#story-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
                            } else {
                                return _orig_send_attachment.apply( button_id, [props, attachment] );
                            }
                        }
                        wp.media.editor.open(button);
                        return false;
                    });
                }
                story_media_upload('.story_tax_media_button.button');
                $('body').on('click','.story_tax_media_remove',function(){
                    $('#league_bg_story_id').val('');
                    $('#story-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                });
                $(document).ajaxComplete(function(event, xhr, settings) {
                    var queryStringArr = settings.data.split('&');
                    if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
                        var xml = xhr.responseXML;
                        $response = $(xml).find('term_id').text();
                        if($response!=""){
                            // Clear the thumb image
                            $('#story-image-wrapper').html('');
                        }
                    }
                });
            });
        </script>
    <?php }

}
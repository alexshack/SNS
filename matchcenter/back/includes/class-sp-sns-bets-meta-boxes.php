<?php
/**
 * Bets Meta Boxes
 *
 * @author      Alex Torbeev
 * @category    Admin
 * @package     SportsPress_SNS
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SP_SNS_Bets_Meta_Boxes
 */

class SP_SNS_Bets_Meta_Boxes {

    public function __construct() {
        $bet_type = get_option('sp_sns_predicts_taxonomy_stakes', '');
        if ( $bet_type ) {
            add_action( $bet_type . '_edit_form_fields', array( $this, 'add_bets_edit_fields'), 10, 2 ); 
            add_action( 'edited_' . $bet_type,           array( $this, 'save_bets_fields') );
            add_action( 'admin_footer',                  array( $this, 'add_script' ) );
        }   
    }



    public function add_bets_edit_fields( $term, $taxonomy ) {
     
        $is_active                 = get_term_meta( $term->term_id, 'is_active', true );
        $is_combine                = get_term_meta( $term->term_id, 'is_combine', true );
        $is_rebet                  = get_term_meta( $term->term_id, 'is_rebet', true );

        $item_1                    = get_term_meta( $term->term_id, 'item_1', true );
        $value_1                   = get_term_meta( $term->term_id, 'value_1', true );
        $operator_1                = get_term_meta( $term->term_id, 'operator_1', true );
        $value_1_digit             = get_term_meta( $term->term_id, 'value_1_digit', true );

        $item_2                    = get_term_meta( $term->term_id, 'item_2', true );
        $value_2                   = get_term_meta( $term->term_id, 'value_2', true );
        $operator_2                = get_term_meta( $term->term_id, 'operator_2', true );
        $value_2_digit             = get_term_meta( $term->term_id, 'value_2_digit', true );

        $value_rebet               = get_term_meta( $term->term_id, 'value_rebet', true );
        $value_rebet_digit         = get_term_meta( $term->term_id, 'value_rebet_digit', true );
        $operator_rebet            = get_term_meta( $term->term_id, 'operator_rebet', true );
 
        $is_active == 'yes' ? $bets_class = '' : $bets_class = 'tr_hidden';
        $is_combine == 'yes' ? $combine_class = '' : $combine_class = 'tr_hidden_combine';
        $is_rebet == 'yes' ? $rebet_class = '' : $rebet_class = 'tr_hidden_rebet';

        if ( $value_1_digit == 'yes' ) {
            $digit_1_class  = '';
            $digits_1_class = 'digit_1_hidden';
        } else {
            $digit_1_class  = 'digit_1_hidden';
            $digits_1_class = '';            
        }

        if ( $value_2_digit == 'yes' ) {
            $digit_2_class  = '';
            $digits_2_class = 'digit_2_hidden';
        } else {
            $digit_2_class  = 'digit_2_hidden';
            $digits_2_class = '';            
        }

        if ( $value_rebet_digit == 'yes' ) {
            $digit_rebet_class  = '';
            $digits_rebet_class = 'digit_rebet_hidden';
        } else {
            $digit_rebet_class  = 'digit_rebet_hidden';
            $digits_rebet_class = '';            
        }
        echo '
        <tr style="border-top: 1px solid #000;">
        <th>Настройка результата ставки</th>
        <td>
            <label><input id="is_active" type="checkbox" name="is_active" ' . checked( 'yes', $is_active, false ) . ' /> Включено</label>
        </td>
        </tr>

        <tr class="tr_bets ' . $bets_class . '">
        <th>Значение</th>
        <td>
        <select name="item_1_item" id="item_1_item">
            <option value="">Выберите показатель</option>' .
            $this->getItems($item_1, 'item') .
        '</select>
        <select name="item_1_period" id="item_1_period">
            <option value="">Выберите период</option>' .
            $this->getItems($item_1, 'period') .
        '</select>
        <select name="item_1_team" id="item_1_team">
            <option value="">Выберите команду</option>' .
            $this->getItems($item_1, 'team') .
        '</select>                
        </td>
        </tr>

        <tr class="tr_bets ' . $bets_class . '">
        <th>Условие</th>
        <td>
        <select name="operator_1" id="operator_1">
            <option value="">Оператор</option>' .
            $this->getItems($operator_1, 'operator') .
        '</select>
        <label><input id="value_1_digit" type="checkbox" name="value_1_digit" ' . checked( 'yes', $value_1_digit, false ) . ' /> Цифровое значение</label>
               
        </td>
        </tr>

        <tr class="tr_bets ' . $bets_class . '">
        <th></th>
        <td>
        <input name="value_1" type="text" value="' . esc_attr( $value_1 ) .'" class="digits_1 ' . $digit_1_class . '" />   
        <select name="value_1_item" id="value_1_item" class="digits_1 ' . $digits_1_class . '">
            <option value="">Выберите показатель</option>' .
            $this->getItems($value_1, 'item') .
        '</select>
        <select name="value_1_period" id="value_1_period" class="digits_1 ' . $digits_1_class . '">
            <option value="">Выберите период</option>' .
            $this->getItems($value_1, 'period') .
        '</select>
        <select name="value_1_team" id="value_1_team" class="digits_1 ' . $digits_1_class . '">
            <option value="">Выберите команду</option>' .
            $this->getItems($value_1, 'team') .
        '</select>                
        </td>
        </tr>

        <tr class="tr_bets ' . $bets_class . '" style="border-top: 1px solid #000;">
        <th>Комбинированная ставка</th>
        <td>
            <label><input id="is_combine" type="checkbox" name="is_combine" ' . checked( 'yes', $is_combine, false ) . ' /> Да</label>
        </td>
        </tr>

        <tr class="tr_bets tr_combine ' . $bets_class . ' ' . $combine_class . '">
        <th>Значение 2</th>
        <td>
        <select name="item_2_item" id="item_2_item">
            <option value="">Выберите показатель</option>' .
            $this->getItems($item_2, 'item') .
        '</select>
        <select name="item_2_period" id="item_2_period">
            <option value="">Выберите период</option>' .
            $this->getItems($item_2, 'period') .
        '</select>
        <select name="item_2_team" id="item_2_team">
            <option value="">Выберите команду</option>' .
            $this->getItems($item_2, 'team') .
        '</select>                
        </td>
        </tr>

        <tr class="tr_bets tr_combine ' . $bets_class . ' ' . $combine_class . '">
        <th>Условие 2</th>
        <td>
        <select name="operator_2" id="operator_2">
            <option value="">Оператор</option>' .
            $this->getItems($operator_2, 'operator') .
        '</select>
        <label><input id="value_2_digit" type="checkbox" name="value_2_digit" ' . checked( 'yes', $value_2_digit, false ) . ' /> Цифровое значение</label>
        </td>
        </tr>

        <tr class="tr_bets tr_combine ' . $bets_class . ' ' . $combine_class . '" style="border-bottom: 1px solid #000;">
        <th></th>
        <td>
        <input name="value_2" type="text" value="' . esc_attr( $value_2 ) .'" class="digits_2 ' . $digit_2_class . '" />   
        <select name="value_2_item" id="value_2_item" class="digits_2 ' . $digits_2_class . '">
            <option value="">Выберите показатель</option>' .
            $this->getItems($value_2, 'item') .
        '</select>
        <select name="value_2_period" id="value_2_period" class="digits_2 ' . $digits_2_class . '">
            <option value="">Выберите период</option>' .
            $this->getItems($value_2, 'period') .
        '</select>
        <select name="value_2_team" id="value_2_team" class="digits_2 ' . $digits_2_class . '">
            <option value="">Выберите команду</option>' .
            $this->getItems($value_2, 'team') .
        '</select>                
        </td>
        </tr> 

        <tr class="tr_bets ' . $bets_class . '" style="border-top: 1px solid #000;">
        <th>Возврат</th>
        <td>
            <label><input id="is_rebet" type="checkbox" name="is_rebet" ' . checked( 'yes', $is_rebet, false ) . ' /> Да</label>
        </td>
        </tr>

        <tr class="tr_bets tr_rebet ' . $bets_class . ' ' . $rebet_class . '">
        <th>Условие возврата</th>
        <td>
        <select name="operator_rebet" id="operator_rebet">
            <option value="">Оператор</option>' .
            $this->getItems($operator_rebet, 'operator') .
        '</select>
        <label><input id="value_rebet_digit" type="checkbox" name="value_rebet_digit" ' . checked( 'yes', $value_rebet_digit, false ) . ' /> Цифровое значение</label>
               
        </td>
        </tr>

        <tr class="tr_bets tr_rebet ' . $bets_class . ' ' . $rebet_class . '">
        <th></th>
        <td>
        <input name="value_rebet" type="text" value="' . esc_attr( $value_rebet ) .'" class="digits_rebet ' . $digit_rebet_class . '" />   
        <select name="value_rebet_item" id="value_rebet_item" class="digits_rebet ' . $digits_rebet_class . '">
            <option value="">Выберите показатель</option>' .
            $this->getItems($value_rebet, 'item') .
        '</select>
        <select name="value_rebet_period" id="value_rebet_period" class="digits_rebet ' . $digits_rebet_class . '">
            <option value="">Выберите период</option>' .
            $this->getItems($value_rebet, 'period') .
        '</select>
        <select name="value_rebet_team" id="value_rebet_team" class="digits_rebet ' . $digits_rebet_class . '">
            <option value="">Выберите команду</option>' .
            $this->getItems($value_rebet, 'team') .
        '</select>                
        </td>
        </tr>

        <tr style="border-top: 1px solid #000;">
        <th></th>
        <td></td>
        </tr>                             
         ';
        ?>

        
    <?php       
     
    }

    public function getItems( $values, $type ) {

        $value = '';

        if ( $type == 'operator' ) {
            $items = [
                '=' => 'Равно',
                '>' => 'Больше',
                '<' => 'Меньше',
            ];
            if ( is_string( $values ) ) {
                $value = $values;
            }
        }

        if ( is_string( $values ) ) {
            $values = explode(';', $values);
        } 

        if ( $type == 'item' ) {
            $items = [
                'goals'       => 'Голы',
                'totalshots'  => 'Удары',
                'fouls'       => 'Нарушения',
                'yellowcards' => 'Желтые карточки',
                'redcards'    => 'Красные карточки',
                'cornerkicks' => 'Угловые'
            ];
            if ( is_array( $values ) ) {
                $value = $values[0];
            }
        }

        if ( $type == 'period' ) {
            $items = [
                'main'       => 'Основное время',
                'game    '   => 'Основное + ДВ',
                'total'      => 'Итоговый результат',
                'firsthalf'  => 'Первый отрезок',
                'secondhalf' => 'Второй отрезок',
                'thirdhalf'  => 'Третий отрезок',
                'fourthhalf' => 'Четвертый отрезок',
                'fifthhalf'  => 'Пятый отрезок',
                'overtime'   => 'Дополнительное время'
            ];
            if ( is_array( $values ) && isset( $values[1] ) ) {
                $value = $values[1];
            }
        }

        if ( $type == 'team' ) {
            $items = [
                'home'  => 'Хозяева',
                'away'  => 'Гости',
                'total' => 'Тотал',
                'fora1' => 'Фора 1',
                'fora2' => 'Фора 2'
            ];
            if ( is_array( $values ) && isset( $values[2] ) ) {
                $value = $values[2];
            }
        }

        $result = '';

        foreach ( $items as $key => $item ) {
            $result .= '<option value="' . $key . '" ' . selected( $value, $key, false ) . '>' . $item . '</option>';
        }

        return $result;
    }

    public function save_bets_fields( $term_id ) {
     
        $bool_fields = [
            'is_active',
            'is_combine',
            'is_rebet',
            'value_1_digit',
            'value_2_digit',
            'value_rebet_digit'
        ];

        $operator_fields = [
            'operator_1',
            'operator_2',
            'operator_rebet'
        ];

        $item_fields = [
            'item_1',
            'item_2'
        ];

        $value_fields = [
            'value_1',
            'value_2',
            'value_rebet'
        ];

        foreach ( $bool_fields as $field ) {
            if ( isset( $_POST[ $field ] ) && 'on' == $_POST[ $field ] ) {
                update_term_meta( $term_id, $field, 'yes' );
            } else {
                delete_term_meta( $term_id, $field );
            }            
        }

        foreach ( $operator_fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_term_meta( $term_id, $field, $_POST[ $field ] );
            } else {
                delete_term_meta( $term_id, $field );
            }
        }

        foreach ( $item_fields as $field ) {
            if ( isset( $_POST[ $field . '_item' ] ) && isset( $_POST[ $field . '_period' ] ) && isset( $_POST[ $field . '_team' ] ) ) {
                $item = $_POST[ $field . '_item' ] . ';' . $_POST[ $field . '_period' ] . ';' . $_POST[ $field . '_team' ];
                update_term_meta( $term_id, $field, $item );
            } else {
                delete_term_meta( $term_id, $field );
            }
        }

        foreach ( $value_fields as $field ) {
            if ( isset( $_POST[ $field . '_digit' ] ) && 'on' == $_POST[ $field . '_digit' ] ) {
                if ( isset( $_POST[ $field ] ) ) {
                    update_term_meta( $term_id, $field, $_POST[ $field ] );
                } else {
                    delete_term_meta( $term_id, $field );
                }
            } else {
                if ( isset( $_POST[ $field . '_item' ] ) && isset( $_POST[ $field . '_period' ] ) && isset( $_POST[ $field . '_team' ] ) ) {
                    $item = $_POST[ $field . '_item' ] . ';' . $_POST[ $field . '_period' ] . ';' . $_POST[ $field . '_team' ];
                    update_term_meta( $term_id, $field, $item );
                } else {
                    delete_term_meta( $term_id, $field );
                }                
            }
        }


    }

    public function add_script() { ?>
        <script>
            jQuery(document).ready( function($) {

                $('body').on('click','#is_active',function() {
                    $('.tr_bets').toggleClass("tr_hidden");
                });

                $('body').on('click','#is_combine',function() {
                    $('.tr_combine').toggleClass("tr_hidden_combine");
                });

                $('body').on('click','#is_rebet',function() {
                    $('.tr_rebet').toggleClass("tr_hidden_rebet");
                });

                $('body').on('click','#value_1_digit',function() {
                    $('.digits_1').toggleClass("digit_1_hidden");
                });

                $('body').on('click','#value_2_digit',function() {
                    $('.digits_2').toggleClass("digit_2_hidden");
                });

                $('body').on('click','#value_rebet_digit',function() {
                    $('.digits_rebet').toggleClass("digit_rebet_hidden");
                });                

            });
        </script>
        <style>
            .tr_hidden, 
            .tr_hidden_combine, 
            .tr_hidden_rebet, 
            .digit_1_hidden,
            .digit_2_hidden,
            .digit_rebet_hidden {
                display: none;
            }
        </style>
    <?php }





}
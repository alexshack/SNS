<?php

function bonuses_custom_box_html($post)
{
			$terms = get_terms('currencies');
			$bookmakers = get_posts([
				'post_type' => 'bookmakers',
				'post_status' => 'publish',
				'numberposts' => -1
			]);
        $bonus_id = get_the_ID();
    ?>
    <?php 
    $bonus_text = get_post_meta( $bonus_id, 'bonus_text', true );
    echo '<h3>Краткое описание</h3>';
    wp_editor( $bonus_text, 'wpeditor', array( 'wpautop' => false, 'textarea_name' => 'bonus_text' ) ); 
    ?>   
    <div class="bookmakers_box">
        <div class="bookmakers_group_3">
            <div class="meta-options bookmakers_field">
                <label for="bs_bm_id"><?php echo __('Выбор БК') ?></label>
                <select id="bs_bm_id" class="js-select postform" name="bs_bm_id" required="required">
                    <?php foreach ($bookmakers as $bookmaker): ?>
                        <option value="<?php echo $bookmaker->ID; ?>"<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_bm_id', true))==$bookmaker->ID ? ' selected="selected"' : ''; ?>><?php echo $bookmaker->post_title; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="meta-options bookmakers_field">
                <label for="bs_value"><?php echo __('Размер бонуса в %') ?></label>
                <input id="bs_value"
					type="number"
					min="1"
					step="0.1"
					max="1000"
					name="bs_value"
					value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_value', true)); ?>">
            </div>
            <div class="meta-options bookmakers_field">
                <label for="bs_shortcode"><?php echo __('Промокод') ?></label>
                <input id="bs_shortcode"
                    type="text"
                    name="bs_shortcode"
                    value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_shortcode', true)); ?>">
            </div>            
        </div>
				<div class="bookmakers_group_3">
					<div class="meta-options bookmakers_field">
						<label for="bs_date_start"><?php echo __('Дата начала') ?></label>
						<input id="bs_date_start"
									 class="datepicker"
									 type="text"
									 name="bs_date_start"
									 value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_date_start', true)); ?>">
					</div>
					<div class="meta-options bookmakers_field">
						<label for="bs_date_end"><?php echo __('Дата окончания') ?></label>
						<input id="bs_date_end"
									 class="datepicker"
									 type="text"
									 name="bs_date_end"
									 value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_date_end', true)); ?>">
					</div>
					<div class="meta-options bookmakers_field">
						<label for="bs_date_no_limit"><?php echo __('Бессрочно') ?></label>
						<input type="checkbox" name="bs_date_no_limit" value="yes" <?php if(get_post_meta(get_the_ID(), 'bs_date_no_limit', true) == 'yes') echo 'checked'; ?> />
					</div>
				</div>				
        <div class="bookmakers_group">
            <div class="meta-options bookmakers_field">
                <label for="bs_text_short"><?php echo __('Краткое описание') ?></label>
                <textarea id="bs_text_short"
                       type="text"
                       name="bs_text_short"><?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_text_short', true)); ?></textarea>
            </div>
        </div>
        <div class="bookmakers_group_2">
            <div class="meta-options bookmakers_field">
                <label for="bs_max"><?php echo __('Максимальный бонус') ?></label>
                <span>
                    <input id="bs_max"
                           type="number"
                           step="any"
                           name="bs_max"
                           value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_max', true)); ?>">
                    <select name="bs_max_val" id="bs_max_val" class="bs_max_val">
                        <?php
                        foreach ($terms as $term):
                            echo '<option value="' . $term->slug . '" ' . selected(esc_attr(get_post_meta(get_the_ID(), 'bs_max_val', true)), $term->slug) . ' > ' . $term->name . '</option>';
                        endforeach;
                        ?>
                    </select>
                </span>
            </div>
            <div class="meta-options bookmakers_field">
                <label for="bs_min"><?php echo __('Минимальный депозит') ?></label>
                <span>
                    <input id="bs_min"
                           type="number"
                           step="any"
                           name="bs_min"
                           value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_min', true)); ?>">
                    <select name="bs_min_val" id="bs_min_val" class="bs_min_val">
                        <?php
                        foreach ($terms as $term):
                            echo '<option value="' . $term->slug . '" ' . selected(esc_attr(get_post_meta(get_the_ID(), 'bs_min_val', true)), $term->slug) . ' > ' . $term->name . '</option>';
                        endforeach;
                        ?>
                    </select>
                </span>
            </div>
        </div>
        <div class="bookmakers_group_3">
            <div class="meta-options bookmakers_field">
                <label for="bs_wager"><?php echo __('Отыгрыш') ?></label>
                <input id="bs_wager"
                    type="text"
                    name="bs_wager"
                    value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_wager', true)); ?>">
            </div> 
            <div class="meta-options bookmakers_field">
                <label for="bs_wager_time"><?php echo __('Срок отыгрыша, в днях') ?></label>
                <span>
                    <input id="bs_wager_time"
                           type="number"
                           step="any"
                           name="bs_wager_time"
                           value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_wager_time', true)); ?>">
                </span>
            </div>
            <div class="meta-options bookmakers_field">
                <label for="bs_kf"><?php echo __('Мин. коэффициент, от - до') ?></label>
                <span>
                    <input id="bs_kf_start"
                           type="number"
                           step="any"
                           name="bs_kf_start"
                           value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_kf_start', true)); ?>">
                    <input id="bs_kf_end"
                           type="number"
                           step="any"
                           name="bs_kf_end"
                           value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'bs_kf_end', true)); ?>">                           
                </span>
            </div>            
        </div>        
    </div>
    <?php
}
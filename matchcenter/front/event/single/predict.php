<?php
/**
 * Template part for event predict block SNS.
 *
 * @author      Alex Torbeev
 * @category    Template
 * @package     SportsPress_SNS
 * @version     1.0.0
 */
$experts = [];
if ($predict) {
	$i = 1;
	while ($i <= 5) {
		$expert_id = get_post_meta( $predict->ID, 'expert_id_' . $i, true );
		$expert_coef = get_post_meta( $predict->ID, 'expert_coef_' . $i, true );
		if ($expert_coef) {
			$field_vote = 'user_vote_' . $i;
			$field_yes  = 'expert_likes_' . $i;
			$field_no   = 'expert_dislikes_' . $i;	

			$likes = get_post_meta( $predict->ID, $field_yes, true );
			$dislikes = get_post_meta( $predict->ID, $field_no, true );
			$rating = intval($likes) - intval($dislikes);
			$rate_class = '';
			if ($rating < 0) {
				$rate_class = 'negative';
			}
			if ($rating > 0) {
				$rate_class = 'positive';
			}
		
			$user_logged_vote = '';
			$can_vote = false;
			$vote_class = 'open-auth';

			if ( is_user_logged_in() && is_user_activated() ) {
				$user_logged_vote = get_user_meta( get_current_user_id(), $field_vote, true );
				$user_logged_vote = explode( ',', $user_logged_vote );
				$can_vote = ( ! in_array( $predict->ID, $user_logged_vote ) );
				if ($can_vote) {
					$vote_class = 'predict_expert-likes_send';
				} else {
					$vote_class = 'disabled';
				}
			}

			$experts[$i] = [
				'id' => $expert_id,
				'name' => getAuthorName($expert_id),
				'position' => get_the_author_meta( 'position', $expert_id ),
				'url' => get_author_posts_url( $expert_id ),
				'img' => QAuth_Avatar::get_url( $expert_id, 100 ),
				'bet' => get_post_meta( $predict->ID, 'expert_bet_' . $i, true ),
				'coef' => get_post_meta( $predict->ID, 'expert_coef_' . $i, true ),
				'text' => wpautop(get_post_meta( $predict->ID, 'expert_text_' . $i, true )),
				'likes' => $likes,
				'dislikes' => $dislikes,
				'rating' => $rating,
				'rate_class' => $rate_class,
				'can_vote' => $can_vote,
				'vote_class' => $vote_class
			];
		}
		$i++;
	}

	$post = get_post( $predict->ID );
	$pr_content = get_the_content('...', false, $predict->ID);
	$pr_content = mb_substr($pr_content, 0, 250) . '...   <a href="' . get_permalink($predict->ID) . '">Читать весь прогноз</a>';
	$author_id = $post->post_author;
	$author_name = getAuthorName($author_id);
}


?>

<div class="sp_block">
	<div class="sp_block_title">
		<h2>Прогнозы на матч</h2>
		<div class="sp_event_section_header_descr">*Прогнозы и коэффициенты актуальны на момент публикации прогноза (<?php echo get_the_date('j F Y, h:i', $predict->ID) ?>)</div>		
	</div>
		<div class="sp_inner_block mb-15">
		    <div class="author-block">
		        <div class="author-block__body">
		            <div class="author-block__poster">
		                <a class="author-block__poster-link" href="<?php print get_author_posts_url( $author_id ); ?>">
		           			<img
		                    width="50" height="50"
		                    class="lazy"
		                    src="data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs="
		                    data-src="<?php print QAuth_Avatar::get_url( $author_id, 100 ); ?>"
		                    alt="<?php print $author_name ; ?>">                        	
		                </a>
		            </div>
		            <div class="author-block__info">
		                <div class="author-block__header">
		                    <a class="author-block__title" href="<?php print get_author_posts_url( $author_id ); ?>"><?php print $author_name ; ?></a>
		                    <?php if ( $author_position = get_the_author_meta( 'position', $author_id ) ): ?>
		                    	<div class="author-block__position"><?php echo $author_position; ?></div>
		                    <?php endif; ?>
		                </div>
		            </div>
		        </div>
		    </div>
			<p><?php echo $pr_content;  ?></p>

		    <div class="predict_expert-header">
		    	<div class="predict_expert-header-item">
			    	<div class="predict_expert-value"><?php echo $predict->getBetType(); ?></div>
			    </div>
		    	<div class="predict_expert-link">
		         <?php if($predict->bookmaker) : ?>
		                <a target="_blank" rel="nofollow" href="<?php echo $predict->getBetLink($predict->bookmaker->ID); ?>" class="predict-header__bookmaker-button">
		                    <span class="predict-header__bookmaker-kf" data-kf="КФ <?php echo $predict->getMaxBet(); ?>">
		                        Поставить
		                    </span>
		                </a>
		            <?php endif; ?>
		        </div>   	
		    </div>			
		</div>
<?php if ( count($experts) ) : ?>
	<div class="sp_block_title">
		<h3>Прогнозы от экспертов</h3>
	</div>
<?php foreach ($experts as $k => $expert) : ?>
		<div class="sp_inner_block mb-15">
		    <div class="author-block">
		        <div class="author-block__body">
		            <div class="author-block__poster">
		                <a class="author-block__poster-link" href="<?php echo $expert['url']; ?>">
		           			<img
		                    width="50" height="50"
		                    class="lazy"
		                    src="data:image/gif;base64,R0lGODdhAQABAPAAAP///wAAACwAAAAAAQABAEACAkQBADs="
		                    data-src="<?php echo $expert['img']; ?>"
		                    alt="<?php echo $expert['name']; ?>">                        	
		                </a>
		            </div>
		            <div class="author-block__info">
		                <div class="author-block__header">
		                    <a class="author-block__title" href="<?php echo $expert['url']; ?>"><?php echo $expert['name']; ?></a>
		                    <div class="author-block__position"><?php echo $expert['position']; ?></div>
		                </div>
		            </div>
		        </div>
		    </div>
		    <?php echo do_shortcode($expert['text']); ?>
		    <div class="predict_expert-header">
		    	<div class="predict_expert-header-item">
			    	<div class="predict_expert-value"><?php echo $expert['bet']; ?></div>
			    </div>
		    	<div class="predict_expert-link">
		         <?php if($predict->bookmaker) : ?>
		                <a target="_blank" rel="nofollow" href="<?php echo $predict->getBetLink($predict->bookmaker->ID); ?>" class="predict-header__bookmaker-button">
		                    <span class="predict-header__bookmaker-kf" data-kf="КФ <?php echo $expert['coef']; ?>">
		                        Поставить
		                    </span>
		                </a>
		            <?php endif; ?>
		        </div>   	
		    </div>		    
		</div>

<?php
	endforeach;
endif; 
?>		

</div>




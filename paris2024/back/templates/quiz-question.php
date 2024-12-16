<div class="quiz24_modal_page" style="background-image: url(<?php echo SNS_URL ?>/img/quiz2024/quiz-bg.jpg);">
	<div class="quiz24_modal_page_header">
		<div class="quiz24_modal_page_left">
			<div class="quiz24_modal_page_number">
				Квиз <span><?php echo $page; ?> / 8</span>
			</div>
			<div class="quiz24_modal_page_question"><?php echo $question; ?></div>
		</div>
		<div class="quiz24_modal_page_right">
			<div class="quiz24_modal_page_logo">
                <img class="quiz24_modal_page_logo_image"
                     src="<?php print get_template_directory_uri(); ?>/img/olympics/logo-2024.svg"
                     width="261"
                     height="315"
                     alt="<?php the_title(); ?>">
            </div>
		</div>
	</div>
	<div class="quiz24_modal_page_answers">
		<div class="quiz24_modal_page_answers_title">Варианты ответов</div>
		<?php foreach ( $answers as $key => $answer ) : ?>
			<div class="quiz24_modal_page_answer" onclick="Paris2024.Quiz.page(<?php echo $page + 1; ?>, <?php echo $key; ?>);return false;"><?php echo $answer; ?></div>
		<?php endforeach; ?>
	</div>
</div>

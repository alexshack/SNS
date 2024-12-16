<div class="quiz24_modal_page" style="background-image: url(<?php echo SNS_URL ?>/img/quiz2024/quiz-bg.jpg);">
	<div class="quiz24_modal_page_header">
		<div class="quiz24_modal_page_left">
			<div class="quiz24_modal_page_number">
				Квиз
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
	<div class="quiz24_modal_page_submit">
		<div>
			<div class="quiz24_modal_page_answers_title">Укажите ваш email</div>
			<input type="email" placeholder="Введите email" class="quiz24_modal_page_input" required>
			<div class="quiz24_modal_page_error">Введите корректный адрес электронной почты, чтобы перейти к результатам и выиграть приз</div>
		</div>
		<div class="quiz24_modal_page_buttons">
			<div class="quiz24_modal_page_button" onclick="Paris2024.Quiz.submit()">Перейти к результатам</div>
		</div>
	</div>
</div>

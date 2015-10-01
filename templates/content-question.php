<?php
/**
 * Template part for displaying question content in page.php.
 */
?>

<div class="quiz-wrapper">
	<form method="POST" id="frm-quiz">
		<div class="quiz-question-body">
			<div style="text-align: center"><?php
			if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
				the_post_thumbnail();
			}
			?></div>
			<h3><?php the_title(); ?></h3>
		</div>
		<ul class="quiz-answers-wrapper">
			<li>
				<input id="quiz-answers-yes" name="frm_quiz[answer]" value="Yes" class="form-radio" type="radio">
				<label for="quiz-answers-yes"><?php _e( 'Yes', LM_QUIZ_TEXT_DOMAIN ); ?></label>
			</li>
			<li>
				<input id="quiz-answers-no" name="frm_quiz[answer]" value="No" class="form-radio" type="radio">
				<label for="quiz-answers-no"><?php _e( 'No', LM_QUIZ_TEXT_DOMAIN ); ?></label>
			</li>
		</ul>
		<div class="quiz-submit-action">
			<input id="frm-quiz-submit" name="quiz_submit" value="<?php _e( 'Next', LM_QUIZ_TEXT_DOMAIN ); ?>" class="quiz-submit" type="submit">
			<?php
			if ( ! empty( $_REQUEST['quiz_key'] ) || ! empty( $_COOKIE['lm_quiz_unique_id'] ) ) {
				$quiz_key = ( ! empty( $_REQUEST['quiz_key'] ) ) ? $_REQUEST['quiz_key'] : $_COOKIE['lm_quiz_unique_id'] ;
			} else {
				$quiz_key = md5( uniqid( rand( 100000, 999999 ) ) );
			}
			?>
			<script>
				var d = new Date();
				d.setTime(d.getTime() + (30*24*60*60*1000));
				var expires = "expires="+d.toUTCString();
				document.cookie = 'lm_quiz_unique_id' + "=<?php echo $quiz_key; ?>; " + expires;
			</script>
			<input type="hidden" name="quiz_key" value="<?php echo $quiz_key ?>" />
			<input type="hidden" name="frm_quiz[id]" value="<?php the_ID(); ?>" />
			<?php wp_nonce_field( 'frm_quiz' . $quiz_key ); ?>
		</div>
	</form>
</div>




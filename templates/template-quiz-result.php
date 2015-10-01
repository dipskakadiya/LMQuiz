<?php
/**
 * Created by PhpStorm.
 * User: dips
 * Date: 24/9/15
 * Time: 3:31 PM
 */
?>
<div class="quiz-result-wrapper">
	<p><?php echo $message; ?></p>
	<form method="POST" id="frm-quiz-result">
		<p class="quiz-result-mail">
			<input type="email" id="quiz-email" name="quiz-email" value="" placeholder="Enter Email"
			       required/>
		</p>
		<p class="quiz-submit-action">
			<input id="frm-quiz-reault-submit" name="quiz_result_send"
			       value="<?php _e( 'Send Result', LM_QUIZ_TEXT_DOMAIN ); ?>" class="quiz-submit"
			       type="submit">
			<?php $quiz_key = $_REQUEST['quiz_key']; ?>
			<input type="hidden" name="quiz_key" value="<?php echo $quiz_key ?>"/>
			<?php wp_nonce_field( 'frm_quiz_result' . $quiz_key ); ?>
		</p>
	</form>
</div>

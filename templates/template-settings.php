<?php
/**
 * Created by PhpStorm.
 * User: dips
 * Date: 24/9/15
 * Time: 3:31 PM
 */
?>
<div class="wrap">
	<h1><?php _e( 'Quiz Settings', LM_QUIZ_TEXT_DOMAIN ); ?></h1>

	<form method="POST" action="options.php">
		<?php settings_fields( 'boolean_quiz' );
		do_settings_sections( 'boolean_quiz' );
		submit_button();
		?>
	</form>
</div>

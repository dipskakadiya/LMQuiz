<?php

/**
 * Created by PhpStorm.
 * User: dips
 * Date: 24/9/15
 * Time: 2:47 PM
 */

if ( ! class_exists( 'LM_Quiz_Settings' ) ) {
	class LM_Quiz_Settings {

		/**
		 * construct
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'boolean_quiz_settings_menu' ) );
			add_action( 'admin_init', array( $this, 'boolean_quiz_settings_init' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'boolean_quiz_enqueue_scripts' ) );
		}

		function boolean_quiz_enqueue_scripts() {
			wp_register_script( 'jquery-cloneya', LM_QUIZ_URL . 'js/jquery-cloneya.min.js', array( 'jquery' ) );
			wp_register_script( 'lmquiz-custom-scripts', LM_QUIZ_URL . 'js/lmquiz-custom-scripts.js', array( 'jquery' ), LM_QUIZ_VERSION );
		}

		function boolean_quiz_settings_menu() {
			add_options_page( __( 'Boolean Quiz', LM_QUIZ_TEXT_DOMAIN ), 'Boolean Quiz', 'manage_options', 'boolean_quiz', array( $this, 'boolean_quiz_options_page' ) );
		}

		function boolean_quiz_options_page(){
			include_once( LM_QUIZ_PATH . 'templates/template-settings.php' );

			wp_enqueue_script( 'jquery-cloneya' );
			wp_enqueue_script( 'lmquiz-custom-scripts' );
		}

		function boolean_quiz_settings_init(  ) {
			register_setting( 'boolean_quiz', 'lm_quiz_settings' );

			add_settings_section(
				'lm_quiz_boolean_quiz_section',
				__( 'Configure Answer of your Quiz', 'lm_quiz' ),
				array( $this, 'boolean_quiz_settings_section_callback' ),
				'boolean_quiz'
			);

			add_settings_field(
				'lm_quiz_answer_counter',
				__( 'Number of Yes Answer' , 'lm_quiz' ),
				array( $this, 'boolean_quiz_number_answer_counter_render' ),
				'boolean_quiz',
				'lm_quiz_boolean_quiz_section'
			);

		}

		function boolean_quiz_settings_section_callback(  ) {
			echo __( 'This section description', 'lm_quiz' );
		}

		function boolean_quiz_number_answer_counter_render(  ) {
			$options = get_option( 'lm_quiz_settings' );
			?>
			<div class="status-clone-wrapper">
				<?php
				if ( ! empty( $options ) ) {
					$count = count( $options['lm_quiz_answer_counter'] );
					for ( $a = 0; $a < $count; $a++ ) { ?>
						<div class="toclone" style="margin-bottom: 20px;">
							<div>
								<input style="width: 100px;" type='number' id="lm_quiz_answer_counter" name='lm_quiz_settings[lm_quiz_answer_counter][]' value='<?php echo $options['lm_quiz_answer_counter'][ $a ]; ?>' required>
							</div>
							<div>
								<textarea style="width: 100%;" id="lm_quiz_answer_text" name='lm_quiz_settings[lm_quiz_answer_text][]' required><?php echo $options['lm_quiz_answer_text'][ $a ]; ?></textarea>
							</div>
							<button href="#" class="clone" title="<?php _e( 'Add Another', LM_QUIZ_TEXT_DOMAIN ) ?>">+</button>
							<button href="#" class="delete" title="<?php _e( 'Delete', LM_QUIZ_TEXT_DOMAIN ) ?>">-</button>
						</div>
					<?php }
				} else { ?>
					<div class="toclone" style="margin-bottom: 20px;">
						<div>
							<input style="width: 100px;" type='number' id="lm_quiz_answer_counter" name='lm_quiz_settings[lm_quiz_answer_counter][]' value='' required>
						</div>
						<div>
							<textarea style="width: 100%;" id="lm_quiz_answer_text" name='lm_quiz_settings[lm_quiz_answer_text][]' required></textarea>
						</div>
						<button href="#" class="clone" title="<?php _e( 'Add Another', LM_QUIZ_TEXT_DOMAIN ) ?>">+</button>
						<button href="#" class="delete" title="<?php _e( 'Delete', LM_QUIZ_TEXT_DOMAIN ) ?>">-</button>
					</div>
				<?php } ?>

			</div>
			<?php
		}

	}
}
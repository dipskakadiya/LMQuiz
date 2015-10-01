<?php

/**
 * Created by PhpStorm.
 * User: dips
 * Date: 24/9/15
 * Time: 2:47 PM
 */

if ( ! class_exists( 'LM_Quiz' ) ) {
	class LM_Quiz {

		public static $post_type = 'question';
		public static $menu_name = 'Questions';
		public $labels;

		/**
		 * construct
		 */
		public function __construct() {

			add_action( 'plugins_loaded', array( $this, 'boolean_quiz_update_db_check' ) );

			$this->boolean_quiz_init_labels();

			add_action( 'init', array( $this, 'boolean_quiz_model_init' ) );

			add_action( 'init', array( $this, 'boolean_quiz_register_post_type' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'boolean_quiz_enqueue_scripts' ) );

			add_shortcode( 'boolean_quiz_render', array( $this, 'boolean_quiz_render' ) );
		}

		function boolean_quiz_update_db_check() {
			if ( get_site_option( 'boolean_quiz_db_version' ) != LM_QUIZ_DB_VERSION ) {
				$this->boolean_quiz_custom_table();
			}
		}

		function boolean_quiz_custom_table() {
			global $wpdb;

			$table_name = $wpdb->prefix . 'lm_quiz_results';

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id BIGINT(20) NOT NULL AUTO_INCREMENT,
				uniqueid text NOT NULL,
				question text NOT NULL,
				answer text NOT NULL,
				acount int NOT NULL,
				email text,
				modification_time datetime ON UPDATE CURRENT_TIMESTAMP,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( 'boolean_quiz_db_version', LM_QUIZ_DB_VERSION );
		}

		public function boolean_quiz_model_init(){

			include_once LM_QUIZ_PATH . 'class-lm-quiz-model.php';

			global $quiz_model;
			$quiz_model = new LM_Quiz_Model();
		}

		public function boolean_quiz_init_labels() {
			$this->labels = apply_filters( 'lm_question_labels', array(
				'name'               => __( 'Questions', LM_QUIZ_TEXT_DOMAIN ),
				'singular_name'      => __( 'Question', LM_QUIZ_TEXT_DOMAIN ),
				'menu_name'          => self::$menu_name,
				'all_items'          => __( 'All Questions', LM_QUIZ_TEXT_DOMAIN ),
				'add_new'            => __( 'Add New Question', LM_QUIZ_TEXT_DOMAIN ),
				'add_new_item'       => __( 'Add Question', LM_QUIZ_TEXT_DOMAIN ),
				'edit_item'          => __( 'Edit Question', LM_QUIZ_TEXT_DOMAIN ),
				'new_item'           => __( 'New Question', LM_QUIZ_TEXT_DOMAIN ),
				'view_item'          => __( 'View Question', LM_QUIZ_TEXT_DOMAIN ),
				'search_items'       => __( 'Search Question', LM_QUIZ_TEXT_DOMAIN ),
				'not_found'          => __( 'No Question found', LM_QUIZ_TEXT_DOMAIN ),
				'not_found_in_trash' => __( 'No Question found in Trash', LM_QUIZ_TEXT_DOMAIN ),
			) );
		}

		public function boolean_quiz_register_post_type() {
			$args = apply_filters( 'lm_question_post_type_args', array(
				'labels'              => $this->labels,
				'description'         => __( 'Question for Quiz.', LM_QUIZ_TEXT_DOMAIN ),
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'query_var'           => true,
				'has_archive'         => false,
				'hierarchical'        => false,
				'menu_position'       => 30,
				'exclude_from_search' => true,
				'taxonomies'          => array( 'category' ),
				'supports'            => array( 'title', 'thumbnail', ),
			) );
			register_post_type( self::$post_type, $args );
		}

		function boolean_quiz_enqueue_scripts() {
			wp_register_style( 'lmquiz-style', LM_QUIZ_URL . 'css/lm-quiz.css', array(), time() );
			wp_enqueue_style( 'lmquiz-style' );
		}

		public function boolean_quiz_render(){

			/**
			 * @var LM_Quiz_Model $quiz_model
			 */
			global $quiz_model, $total_question;

			$visited_post = $result = array();

			if ( ! empty( $_REQUEST['quiz_key'] ) || ! empty( $_COOKIE['lm_quiz_unique_id'] ) ) {

				$_REQUEST['quiz_key'] = ( ! empty( $_REQUEST['quiz_key'] ) ) ? $_REQUEST['quiz_key'] : $_COOKIE['lm_quiz_unique_id'] ;

				$result = $quiz_model->get_quiz_result( array( 'uniqueid' => $_REQUEST['quiz_key'] ) );
				if ( ! empty( $result ) ) {
					$result = $result[0];
				}

				//send result on mail
				if ( ! empty( $_POST['quiz_result_send'] ) ) {
					$this->boolean_quiz_send_email( $result );
					return;
				}
				//save data;
				$new_result = $this->boolean_quiz_save( $result );
				if ( $new_result ) {
					$result = $new_result;
				}

				$visited_post = isset( $result->question )? unserialize( $result->question ) : array();
			}

			$space_arg = array(
				'post_type' => LM_Quiz::$post_type,
				'post_status' => 'publish',
				'posts_per_page' => 1,
				'order' => 'ASC',
				'post__not_in' => $visited_post,
			);
			$queryQues = new WP_Query( $space_arg );
			?>
			<div class="row">
			<?php
			if ( $queryQues->have_posts() ) {
				while ( $queryQues->have_posts() ) {
					$queryQues->the_post();
					include_once( LM_QUIZ_PATH . 'templates/content-question.php' );
				}
				wp_reset_postdata();
			} else {
				$this->boolean_quiz_results( $result );
			} ?>
			</div>
			<?php
		}

		function boolean_quiz_save( $result ){

			/**
			 * @var LM_Quiz_Model $quiz_model
			 */
			global $quiz_model;
			if ( ! empty( $_POST['quiz_submit'] ) ) {

				if ( wp_verify_nonce( $_POST['_wpnonce'], 'frm_quiz' . $_POST['quiz_key'] ) ) {

					$option_name = 'quiz_key_' . $_REQUEST['quiz_key'];
					$frm_quiz = $_POST['frm_quiz'];

					$data = array();
					$question = isset( $result->question )? unserialize( $result->question ) : array();
					$answer = isset( $result->answer )? unserialize( $result->answer ) : array();
					$acount = isset( $result->acount )? $result->acount : 0;

					if ( ! in_array( $frm_quiz['id'], $question ) ) {
						$question[] = $frm_quiz['id'];
						$answer[ $frm_quiz['id'] ] = $frm_quiz['answer'];
						if ( 'Yes' === $frm_quiz['answer'] ) {
							$acount += 1;
						}

						$data['question'] = serialize( $question );
						$data['answer'] = serialize( $answer );
						$data['acount'] = $acount;
						if ( empty( $result ) ) {
							$data['uniqueid'] = $_REQUEST['quiz_key'];
							$quiz_model->add_quiz_result( $data );
						} else {
							$where = array( 'uniqueid' => $_REQUEST['quiz_key'] );
							$quiz_model->update_quiz_result( $data, $where );
						}

						$result = $quiz_model->get_quiz_result( array( 'uniqueid' => $_REQUEST['quiz_key'] ) );
						if ( ! empty( $result ) ) {
							$result = $result[0];
						}
					}

					return $result;
				}
			}
		}

		function boolean_quiz_results( $result ){
			/**
			 * @var LM_Quiz_Model $quiz_model
			 */
			global $quiz_model;
			if ( ! empty( $_REQUEST['quiz_key'] ) && ! empty( $result ) ) {

				$options = get_option( 'lm_quiz_settings' );

				if ( ! empty( $options ) && ! empty( $options['lm_quiz_answer_counter'] ) ) {
					$acount = $message = '';
					$index = array_search( $result->acount, $options['lm_quiz_answer_counter'] );

					if ( is_numeric( $index ) ) {
						$acount  = $options['lm_quiz_answer_counter'][ $index ];
						$message = $options['lm_quiz_answer_text'][ $index ];
					} else {
						$message = 'Sorry! You didnt Found appropriate result';
					}

					$space_arg      = array(
						'post_type'      => LM_Quiz::$post_type,
						'post_status'    => 'publish',
						'posts_per_page' => 1,
						'order'          => 'ASC',
					);
					$queryQues      = new WP_Query( $space_arg );
					$total_question = $queryQues->found_posts;
					include_once( LM_QUIZ_PATH . 'templates/template-quiz-result.php' );
				} else {
					echo 'Sorry! You didnt Found appropriate result';
				} ?>
				<script>
					var expires = "expires=-1;";
					document.cookie = 'lm_quiz_unique_id' + "=;" + expires;
				</script>
				<?php
			}
		}

		function boolean_quiz_send_email( $result ){
			global $quiz_model;
			if ( ! empty( $result ) && wp_verify_nonce( $_POST['_wpnonce'], 'frm_quiz_result' . $_POST['quiz_key'] ) ) {
				$options = get_option( 'lm_quiz_settings' );
				if ( ! empty( $options ) && ! empty( $options['lm_quiz_answer_counter'] ) ) {
					$index  = array_search( $result->acount, $options['lm_quiz_answer_counter'] );
					if ( is_numeric( $index ) ) {
						$message = $options['lm_quiz_answer_text'][ $index ];

						$to        = $_POST['quiz-email'];
						$subject   = 'Quiz Result';
						$headers   = array();
						$headers[] = 'From: Quiz<community@demo.com>' . "\r\n";
						add_filter( 'wp_mail_content_type', array( $this, 'boolean_quiz_set_html_content_type' ) );
						$sended = wp_mail( $to, $subject, $message, $headers, array() );
						remove_filter( 'wp_mail_content_type', array( $this, 'boolean_quiz_set_html_content_type' ) );

						if ( $sended ) {
							$data = array();
							$data['email'] = $_POST['quiz-email'];
							$where = array( 'uniqueid' => $_REQUEST['quiz_key'] );
							$quiz_model->update_quiz_result( $data, $where );
							echo 'Email send';
						}
					}
				}
			}
		}

		function boolean_quiz_set_html_content_type(){
			return 'text/html';
		}

	}
}
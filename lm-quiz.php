<?php

/*
Plugin Name: Boolean Quiz
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: Dips
Author URI:
License: A "Slug" license name e.g. GPL2
*/


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! defined( 'LM_QUIZ_VERSION' ) ) {
	define( 'LM_QUIZ_VERSION', '1.0' );
}

if ( ! defined( 'LM_QUIZ_DB_VERSION' ) ) {
	define( 'LM_QUIZ_DB_VERSION', '1.0' );
}

if ( ! defined( 'LM_QUIZ_TEXT_DOMAIN' ) ) {
	define( 'LM_QUIZ_TEXT_DOMAIN', 'lm_quiz' );
}

if ( ! defined( 'LM_QUIZ_PLUGIN_FILE' ) ) {
	define( 'LM_QUIZ_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'LM_QUIZ_PATH' ) ) {
	define( 'LM_QUIZ_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'LM_QUIZ_URL' ) ) {
	define( 'LM_QUIZ_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'LM_QUIZ_BASE_NAME' ) ) {
	define( 'LM_QUIZ_BASE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'LM_QUIZ_PATH_TEMPLATES' ) ) {
	define( 'LM_QUIZ_PATH_TEMPLATES', plugin_dir_path( __FILE__ ) . 'templates/' );
}


register_activation_hook( __FILE__, 'boolean_quiz_update_db_check' );



/**
 * Run Awfis Core base Class
 */
function boolean_quiz_init_quiz() {
	include_once LM_QUIZ_PATH . 'lib/class-rt-db-model.php';
	include_once LM_QUIZ_PATH . 'class-lm-quiz.php';
	include_once LM_QUIZ_PATH . 'class-lm-quiz-settings.php';

	$plugin = new LM_Quiz();
	$plugin_setting = new LM_Quiz_Settings();

}
boolean_quiz_init_quiz();
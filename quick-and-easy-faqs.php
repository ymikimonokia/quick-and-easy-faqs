<?php
/*
 * Plugin Name:         Quick and Easy FAQs
 * Plugin URI:          https://github.com/ymikimonokia/quick-and-easy-faqs
 * GitHub Plugin URI:   https://github.com/ymikimonokia/quick-and-easy-faqs
 * Description:         A quick and easy way to add ARTs to your site.
 * Version:             1.2.7
 * Author:              Mikel Marqués Gallego
 * Author Uri:          https://agencialibre.xyz/arts
 * License:           	GPL-2.0+
 * License URI:       	http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       	quick-and-easy-faqs
 * Domain Path:       	/languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Global Constants to be used throughout the plugin
 */
define( 'QUICK_AND_EASY_FAQS_VERSION', '1.3.6' );

/**
 * The core plugin class that is used to define all site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/autoload.php';

use Quick_And_Easy_FAQs\Includes\FAQs;

/**
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does not affect the page life cycle.
 */
function run_quick_and_easy_faqs() {

	return FAQs::instance();

}
run_quick_and_easy_faqs();


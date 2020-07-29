<?php
/**
 * Plugin Name: LearnDash Questionare Charts
 * Plugin URI: https://nsukonny.ru/learndash-questionare-charts/
 * Description: Display questionare results as charts
 * Version: 1.0.0
 * Author: NSukonny
 * Author URI: https://nsukonny.ru
 * Text Domain: lqcharts
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Learndash_QCharts' ) ) {

	include_once dirname( __FILE__ ) . '/libraries/class-lqcharts.php';

}

/**
 * The main function for returning Learndash_QCharts instance
 *
 * @since 1.0.0
 *
 * @return object The one and only true Learndash_QCharts instance.
 */
function lqcharts_runner() {

	return Learndash_QCharts::instance();
}

lqcharts_runner();
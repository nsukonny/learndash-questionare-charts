<?php
/**
 * Class Learndash_QCharts_Shortcode
 * Contain shoertcode for display chart
 *
 * @since 1.0.0
 */

class Learndash_QCharts_Shortcode {

	/**
	 * Shortcode initialization
	 *
	 * @since 1.0.0
	 */
	public function init() {

		add_shortcode( 'lqcharts', array( $this, 'display_charts' ) );

	}

	public function display_charts() {

		ob_start();
		?>
        
		<?php
		return ob_get_clean();
	}

}

function lqcharts_shortcode_runner() {

	$shortcode = new Learndash_QCharts_Shortcode;
	$shortcode->init();

	return;
}

lqcharts_shortcode_runner();
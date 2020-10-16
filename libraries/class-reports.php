<?php
/**
 * Class Learndash_QCharts_Reports
 * Add new page to admin side for showing reports.
 *
 * @since 1.0.1
 */

class Learndash_QCharts_Reports {

	/**
	 * Reports initialization
	 *
	 * @since 1.0.1
	 */
	public function init() {

		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

	}

	/**
	 * Add link to menu
	 *
	 * @since 1.0.1
	 */
	public function add_menu_link() {

		add_menu_page(
			'Quiz reports',
			'Quiz reports',
			'manage_options',
			'quiz_reports',
			array( $this, 'display_reports' ),
			'dashicons-image-filter',
			6
		);

	}

	/**
	 * Render reports table
	 *
	 * @since 1.0.1
	 */
	public function display_reports() {

		$reports_table = new Learndash_QCharts_Reports_Table();
		$reports_table->prepare_items();

		?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title() ?></h2>
			<?php
			if ( isset( $_REQUEST['xls_report'] ) ) {
				$link = $reports_table->get_report();
				if ( $link ) {
					?>
                    <a href="<?php echo esc_url( $link ); ?>" target="_blank">
						<?php esc_attr_e( 'Download XLSX', 'lqcharts' ); ?>
                    </a>
					<?php
				}
			} else {
				$reports_table->views();
				$reports_table->display();
			}
			?>
        </div>
		<?php
	}

}

/**
 * Runner
 *
 * @since 1.0.1
 */
function lqcharts_reports_runner() {

	$reports = new Learndash_QCharts_Reports;
	$reports->init();

	return;
}

lqcharts_reports_runner();
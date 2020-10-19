<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Learndash_QCharts_Reports_Table extends WP_List_Table {

	public function get_columns() {

		$columns = array(
			'id'          => __( 'User ID', 'lqcharts' ),
			'user_name'   => __( 'Username', 'lqcharts' ),
			'course'      => __( 'Course', 'lqcharts' ),
			'questionare' => __( 'Questionare', 'lqcharts' ),
			'question_1'  => __( 'Question 1 (of 100%)', 'lqcharts' ),
			'question_2'  => __( 'Question 2 (of 100%)', 'lqcharts' ),
			'question_3'  => __( 'Question 3 (of 100%)', 'lqcharts' ),
			'question_4'  => __( 'Question 4 (of 100%)', 'lqcharts' ),
			'question_5'  => __( 'Question 5 (of 100%)', 'lqcharts' ),
		);

		return $columns;
	}

	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'question_1':
				if ( isset( $item['answers'][0] ) && 0 < $item['answers'][0]['points'] ) {
					return ( $item['answers'][0]['points'] * 100 ) / $item['answers'][0]['gpoints'];
				}

				return 0;
				break;
			case 'question_2':
				if ( isset( $item['answers'][1] ) && 0 < $item['answers'][1]['points'] ) {
					return ( $item['answers'][1]['points'] * 100 ) / $item['answers'][1]['gpoints'];
				}

				return 0;
				break;
			case 'question_3':
				if ( isset( $item['answers'][2] ) && 0 < $item['answers'][2]['points'] ) {
					return ( $item['answers'][2]['points'] * 100 ) / $item['answers'][2]['gpoints'];
				}

				return 0;
				break;
			case 'question_4':
				if ( isset( $item['answers'][3] ) && 0 < $item['answers'][3]['points'] ) {
					return ( $item['answers'][3]['points'] * 100 ) / $item['answers'][3]['gpoints'];
				}

				return 0;
				break;
			case 'question_5':
				if ( isset( $item['answers'][4] ) ) {
					return 1 == $item['answers'][4]['points'] ? 1 : 0;
				}

				return 0;
				break;
			default:
				return $item[ $column_name ];
		}

	}

	/**
	 * Display links in top
	 *
	 * @since 1.0.1
	 *
	 * @return array
	 */
	public function get_views() {

		$views               = array();
		$views['xls_report'] = '<a href="' . esc_url( add_query_arg( 'xls_report', 1 ) ) . '">' . __( 'Export XLSX', 'lqcharts' ) . '</a>';

		return $views;
	}

	/**
	 * Prepare items for table
	 *
	 * @since 1.0.1
	 */
	public function prepare_items() {

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->load_items();

	}

	/**
	 * Get items from database
	 *
	 * @since 1.0.1
	 */
	private function load_items() {

		$items = array();

		$query = new WP_Query( array(
			'post_type'   => 'sfwd-quiz',
			'post_status' => 'publish',
			'numberposts' => 32,
			'orderby'     => 'date',
			'order'       => 'DESC',
			's'           => 'woche',
		) );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$quiz_id      = get_the_ID();
				$quiz_pro_id  = learndash_get_setting( $quiz_id, 'quiz_pro' );
				$course_id    = get_post_meta( $quiz_id, 'course_id', true );
				$quiz_results = $this->get_quiz_results( $quiz_pro_id, $course_id, get_the_title() );

				$items = array_merge( $items, $quiz_results );
			}
		}

		$items = $this->get_items_from_history( $items );

		return $items;
	}

	/**
	 * Get quiz users and his results
	 *
	 * @since 1.0.1
	 *
	 * @param $quiz_id
	 *
	 * @return array
	 */
	private function get_quiz_results( $quiz_id, $course_id, $questionare_title ) {

		$quiz_results = array();

		if ( class_exists( 'WpProQuiz_Model_StatisticUserMapper' ) ) {
			$statisticRefMapper  = new WpProQuiz_Model_StatisticRefMapper();
			$statisticUserMapper = new WpProQuiz_Model_StatisticUserMapper();

			$args = array(
				'quizId' => $quiz_id,
				'limit'  => 1000,
			);

			if ( ! current_user_can( 'administrator' ) ) {
				$args['users'] = get_current_user_id();
			}

			$statisticModel = $statisticRefMapper->fetchHistoryWithArgs( $args );

			foreach ( $statisticModel as $model ) {
				if ( isset( $quiz_results[ $model->getUserId() ] ) && $model->getCreateTime() <= $quiz_results[ $model->getUserId() ]['create_time'] ) {
					continue;
				}

				$user_results                = array();
				$user_results['quiz_id']     = $quiz_id;
				$user_results['id']          = $model->getUserId();
				$user_results['user_name']   = $model->getUserName();
				$user_results['create_time'] = $model->getCreateTime();
				$user_results['course_id']   = $course_id;
				$course                      = get_post( $course_id );
				$user_results['course']      = $course ? $course->post_title : '';
				$user_results['questionare'] = $questionare_title;
				$user_results['answers']     = array();

				$statisticUsers = $statisticUserMapper->fetchUserStatistic( $model->getStatisticRefId(), $quiz_id );

				foreach ( $statisticUsers as $statistic_key => $statistic_user ) {
					$user_results['answers'][ $statistic_key ] = array(
						'points'  => $statistic_user->getPoints(),
						'gpoints' => $statistic_user->getGPoints(),
					);
				}

				$quiz_results[ $model->getUserId() ] = $user_results;
			}
		}

		return $quiz_results;
	}

	/**
	 * Send all items to XLSX file
	 *
	 * @since 1.0.1
	 */
	public function get_report() {

		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

		$sheet = $spreadsheet->getActiveSheet();
		$sheet = $this->set_titles( $sheet );
		$sheet = $this->set_rows( $sheet );
		$sheet = $this->set_modifications( $sheet );

		$date   = date( 'd_m_Y' );
		$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx( $spreadsheet );
		$writer->save( LQCHARTS_PLUGIN_PATH . 'temp/quiz_report_' . $date . '.xlsx' );

		return LQCHARTS_PLUGIN_URL . 'temp/quiz_report_' . $date . '.xlsx';
	}

	/**
	 * Set titles for sheet
	 *
	 * @since 1.0.1
	 *
	 * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
	 */
	private function set_titles( \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet ) {

		$row = 1;
		$sheet->setCellValue( 'A' . $row, __( 'User ID', 'lqcharts' ) );
		$sheet->setCellValue( 'B' . $row, __( 'Username', 'lqcharts' ) );
		$sheet->setCellValue( 'C' . $row, __( 'Course', 'lqcharts' ) );
		$sheet->setCellValue( 'D' . $row, __( 'Questionare', 'lqcharts' ) );
		$sheet->setCellValue( 'E' . $row, __( 'Question 1 (of 100%)', 'lqcharts' ) );
		$sheet->setCellValue( 'F' . $row, __( 'Question 2 (of 100%)', 'lqcharts' ) );
		$sheet->setCellValue( 'G' . $row, __( 'Question 3 (of 100%)', 'lqcharts' ) );
		$sheet->setCellValue( 'H' . $row, __( 'Question 4 (of 100%)', 'lqcharts' ) );
		$sheet->setCellValue( 'I' . $row, __( 'Question 5', 'lqcharts' ) );

		return $sheet;
	}

	/**
	 * Add rows
	 *
	 * @since 1.0.1
	 *
	 * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
	 */
	private function set_rows( \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet ) {

		$row = 2;
		if ( 0 < count( $this->items ) ) {
			foreach ( $this->items as $item ) {
				$sheet->setCellValue( 'A' . $row, $item['id'] );
				$sheet->setCellValue( 'B' . $row, $item['user_name'] );
				$sheet->setCellValue( 'C' . $row, $item['course'] );
				$sheet->setCellValue( 'D' . $row, $item['questionare'] );
				$sheet->setCellValue( 'E' . $row, ( $item['answers'][0]['points'] * 100 ) / $item['answers'][0]['gpoints'] );
				$sheet->setCellValue( 'F' . $row, ( $item['answers'][1]['points'] * 100 ) / $item['answers'][1]['gpoints'] );
				$sheet->setCellValue( 'G' . $row, ( $item['answers'][2]['points'] * 100 ) / $item['answers'][2]['gpoints'] );
				$sheet->setCellValue( 'H' . $row, ( $item['answers'][3]['points'] * 100 ) / $item['answers'][3]['gpoints'] );
				$sheet->setCellValue( 'I' . $row, 1 == $item['answers'][4]['points'] ? 1 : 0 );

				$row ++;
			}
		}

		return $sheet;
	}

	/**
	 * Apply column modifications
	 *
	 * @since 1.0.1
	 *
	 * @param $sheet
	 *
	 * @return mixed
	 */
	private function set_modifications( $sheet ) {

		$sheet->setTitle( __( 'Quiz stats', 'lqcharts' ) );

		$sheet->getColumnDimension( 'A' )->setAutoSize( true );
		$sheet->getColumnDimension( 'B' )->setAutoSize( true );
		$sheet->getColumnDimension( 'C' )->setAutoSize( true );
		$sheet->getColumnDimension( 'D' )->setAutoSize( true );
		$sheet->getColumnDimension( 'E' )->setAutoSize( true );
		$sheet->getColumnDimension( 'F' )->setAutoSize( true );
		$sheet->getColumnDimension( 'G' )->setAutoSize( true );
		$sheet->getColumnDimension( 'H' )->setAutoSize( true );
		$sheet->getColumnDimension( 'I' )->setAutoSize( true );

		return $sheet;
	}

	/**
	 * Load items from history
	 *
	 * @since 1.0.2
	 */
	private function get_items_from_history( $items ) {

		$users = get_users( array(
			'meta_key'     => 'lqcharts-history-quizzes',
			'meta_compare' => 'EXISTS',
		) );

		if ( $users ) {
			foreach ( $users as $user ) {
				$quizzes = get_user_meta( $user->ID, 'lqcharts-history-quizzes', true );

				if ( is_array( $quizzes ) && 0 < count( $quizzes ) ) {
					foreach ( $quizzes as $quiz_id => $quiz_users ) {
						$new_items = array();

						foreach ( $quiz_users as $user_data ) {
							if ( isset( $new_items[ $user_data['id'] ] ) && $user_data['create_time'] <= $new_items[ $user_data['id'] ]['create_time'] ) {
								continue;
							}

							$new_items[ $user_data['id'] ]            = $user_data;
							$new_items[ $user_data['id'] ]['answers'] = array_values( $user_data['answers'] );
						}

						$items = array_merge( $items, $new_items );
					}
				}
			}
		}

		return $items;
	}

}
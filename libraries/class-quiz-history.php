<?php
/**
 * Class Learndash_QCharts_Quiz_History
 * Save Quiz data to reserve metafields for show it in reports
 *
 * @since 1.0.2
 */

class Learndash_QCharts_Quiz_History {

	/**
	 * Reports initialization
	 *
	 * @since 1.0.2
	 */
	public function init() {

		add_action( 'learndash_user_course_access_expired', array( $this, 'copy_to_history' ), 10, 2 );

	}

	/**
	 * Save quiz and course backup
	 *
	 * @since 1.0.2
	 *
	 * @param $user_id
	 * @param $course_id
	 */
	public function copy_to_history( $user_id = 1, $course_id = 1731 ) {

		$this->copy_course( $user_id, $course_id );
		$this->copy_quizzes( $user_id, $course_id );

	}

	/**
	 * Save course in history
	 *
	 * @since 1.0.2
	 *
	 * @param $user_id
	 * @param $course_id
	 */
	private function copy_course( $user_id, $course_id ) {

		$history_user_meta = get_user_meta( $user_id, 'lqcharts-history-course_progress', true );
		$user_meta         = get_user_meta( $user_id, '_sfwd-course_progress', true );

		if ( isset( $user_meta[ $course_id ] ) ) {
			if ( ! is_array( $history_user_meta ) || ! $history_user_meta ) {
				$history_user_meta = array();
			}
			$history_user_meta[ $course_id ] = $user_meta[ $course_id ];
			$course_completed                = get_user_meta( $user_id, 'course_completed_' . $course_id, true );

			update_user_meta( $user_id, 'lqcharts-history-course_progress', $history_user_meta );

			if ( $course_completed ) {
				update_user_meta( $user_id, 'lqcharts-history-course_completed_' . $course_id, $course_completed );
			}
		}

	}

	/**
	 * Save Quizzes to history
	 *
	 * @since 1.0.2
	 *
	 * @param $user_id
	 * @param $course_id
	 */
	private function copy_quizzes( $user_id, $course_id ) {

		$quizzes           = array();
		$user_meta_quizzes = get_user_meta( $user_id, '_sfwd-quizzes', true );
		if ( ! is_array( $user_meta_quizzes ) ) {
			$user_meta_quizzes = array();
		}

		if ( ! empty( $user_meta_quizzes ) ) {
			foreach ( $user_meta_quizzes as $quiz_item ) {
				if ( ( isset( $quiz_item['course'] ) ) && ( intval( $course_id ) == intval( $quiz_item['course'] ) ) ) {
					if ( isset( $quiz_item['quiz'] ) ) {
						$quiz_id             = intval( $quiz_item['quiz'] );
						$quizzes[ $quiz_id ] = $quiz_id;
					}
				}
			}
		}

		if ( ! empty( $quizzes ) ) {
			$quizzes_history = array();
			foreach ( $quizzes as $quiz_id ) {
				$quizzes_history[ $quiz_id ] = $this->get_quiz_results( $user_id, $quiz_id );
			}

			update_user_meta( $user_id, 'lqcharts-history-quizzes', $quizzes_history );
		}


	}

	/**
	 * Get results from quiz
	 *
	 * @since 1.0.2
	 *
	 * @param $user_id
	 * @param $quiz_id
	 *
	 * @return array
	 */
	private function get_quiz_results( $user_id, $quiz_id ) {

		$results = array();

		if ( class_exists( 'WpProQuiz_Model_StatisticUserMapper' ) ) {
			$statisticRefMapper  = new WpProQuiz_Model_StatisticRefMapper();
			$statisticUserMapper = new WpProQuiz_Model_StatisticUserMapper();
			$quiz                = get_post( $quiz_id );
			$course_id           = get_post_meta( $quiz_id, 'course_id', true );
			$course              = get_post( $course_id );
			$quiz_pro_id         = learndash_get_setting( $quiz_id, 'quiz_pro' );
			$args                = array(
				'quizId' => $quiz_pro_id,
				'users'  => $user_id,
				'limit'  => 1000,
			);
			$statisticModel      = $statisticRefMapper->fetchHistoryWithArgs( $args );

			foreach ( $statisticModel as $model ) {

				$user_results                = array();
				$user_results['quiz_id']     = $quiz_id;
				$user_results['id']          = $model->getUserId();
				$user_results['user_name']   = $model->getUserName();
				$user_results['create_time'] = $model->getCreateTime();
				$user_results['course_id']   = $course_id;
				$user_results['course']      = $course ? $course->post_title : '';
				$user_results['questionare'] = $quiz ? $quiz->post_title : '';
				$user_results['answers']     = array();

				$statisticUsers = $statisticUserMapper->fetchUserStatistic( $model->getStatisticRefId(), $quiz_pro_id );

				foreach ( $statisticUsers as $statistic_key => $statistic_user ) {
					$question_id                             = $statistic_user->getQuestionId();
					$user_results['answers'][ $question_id ] = array(
						'points'  => $statistic_user->getPoints(),
						'gpoints' => $statistic_user->getGPoints(),
					);
				}

				$results[ $model->getStatisticRefId() ] = $user_results;
			}
		}

		return $results;
	}

}

/**
 * Runner
 *
 * @since 1.0.2
 */
function lqcharts_quiz_history_runner() {

	$history = new Learndash_QCharts_Quiz_History;
	$history->init();

	return;
}

lqcharts_quiz_history_runner();
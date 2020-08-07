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
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ), 10 );

	}

	/**
	 * Add scripts and styles for frontend
	 *
	 * @since 1.0.1
	 */
	public function add_scripts() {

		wp_enqueue_style( 'lqcharts-styles', LQCHARTS_PLUGIN_URL . '/assets/style.css', array(), time() );
		wp_enqueue_style( 'lqcharts-styles', LQCHARTS_PLUGIN_URL . '/assets/js-libraries/chart/chart.min.css', array(), time() );

		wp_enqueue_script( 'lqcharts-library-scripts', LQCHARTS_PLUGIN_URL . '/assets/js-libraries/chart/chart.min.js', array(
			'jquery',
		), time(), true );

		wp_enqueue_script( 'lqcharts-scripts', LQCHARTS_PLUGIN_URL . '/assets/scripts.js', array(
			'jquery',
			'lqcharts-library-scripts',
		), time(), true );

	}

	/**
	 * Render all charts byshortcode
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function display_charts() {

		ob_start();
		$weeks      = $this->get_weeks();
		$points     = $this->get_total_points( $weeks );
		$percents   = $this->get_percents( $points );
		$total      = $this->get_total_percents( $percents );
		$graph_data = $this->get_graph_data( $weeks );
		$last_week  = $this->get_last_week_growth( $weeks );
		$speech     = $this->get_speech( $total );
		$thumb      = $this->get_thumbs( $weeks );
		?>
        <div class="lqcharts">

            <div class="lqcharts-left-side">
                <div class="lqcharts-last-week">

                    <div class="lqcharts-last-week__title">
                        <span class="lqcharts-last-week__icon lqcharts-last-week__icon_info">i</span>Gesamtbewertung
                        <span class="lqcharts-last-week__stat"><?php esc_attr_e( $last_week ); ?>%</span>
                    </div>

                    <div class="lqcharts-last-week__body">
                        <div class="lqcharts-last-week__body-left"><b>Die Gesamtbewertung deines Trainingzustandes</b>
                            <hr>
							<?php esc_attr_e( $speech ); ?>
                        </div>

                        <div class="lqcharts-last-week__body-right">
                            <canvas class="lqcharts-rounded" data-percent="<?php esc_attr_e( $total ); ?>" width="100"
                                    height="100"></canvas>
                            <div class="lqcharts-rounded-center">
                                <div class="lqcharts-rounded-center__title"><?php esc_attr_e( $total ); ?></div>
                                <div class="lqcharts-rounded-center__sub">von 100</div>
                            </div>
                        </div>
                        <div class="lqcharts-clear">&nbsp;</div>
                    </div>
                </div>

                <div class="lqcharts-score">
                    <div class="lqcharts-score__title">Dein Empfinden</div>
                    <div class="lqcharts-score__body">
                        <div class="lqcharts-score__chart">
                            <div class="lqcharts-score__chart-wrapper">
                                <canvas class="lqcharts-rounded"
                                        data-percent="<?php esc_attr_e( $percents[0]['percents'] ); ?>"
                                        width="100" height="100"></canvas>
                                <div class="lqcharts-rounded-center">
                                    <div class="lqcharts-rounded-center__title"><?php esc_attr_e( $percents[0]['percents'] ); ?></div>
                                    <div class="lqcharts-rounded-center__sub">von 100</div>
                                </div>
                            </div>
                            Allg. Empﬁnden
                        </div>
                        <div class="lqcharts-score__chart">
                            <div class="lqcharts-score__chart-wrapper">
                                <canvas class="lqcharts-rounded"
                                        data-percent="<?php esc_attr_e( $percents[1]['percents'] ); ?>"
                                        width="100" height="100"></canvas>
                                <div class="lqcharts-rounded-center">
                                    <div class="lqcharts-rounded-center__title"><?php esc_attr_e( $percents[1]['percents'] ); ?></div>
                                    <div class="lqcharts-rounded-center__sub">von 100</div>
                                </div>
                            </div>
                            Beweglichkeit
                        </div>
                        <div class="lqcharts-score__chart">
                            <div class="lqcharts-score__chart-wrapper">
                                <canvas class="lqcharts-rounded"
                                        data-percent="<?php esc_attr_e( $percents[2]['percents'] ); ?>"
                                        width="100" height="100"></canvas>
                                <div class="lqcharts-rounded-center">
                                    <div class="lqcharts-rounded-center__title"><?php esc_attr_e( $percents[2]['percents'] ); ?></div>
                                    <div class="lqcharts-rounded-center__sub">von 100</div>
                                </div>
                            </div>
                            Stabilität
                        </div>
                    </div>
                </div>
            </div>

            <div class="lqcharts-right-side">

                <div class="lqcharts-graph"
                     data-emb="<?php esc_attr_e( $graph_data['emb'] ); ?>"
                     data-bef="<?php esc_attr_e( $graph_data['bef'] ); ?>"
                     data-stab="<?php esc_attr_e( $graph_data['stab'] ); ?>">
                    <div class="lqcharts-graph__title">
                        <h3>Wochenverlauf</h3>
                        <span>Feedback im Wochenverlauf</span>
                    </div>
                    <div class="lqcharts-graph__chart">
                        <canvas id="lqcharts-graph"></canvas>
                    </div>
                </div>

                <div class="lqcharts-graph-footer">
                    <h3>Favoriten</h3>

					<?php echo $thumb; ?>
                    <img src="<?php echo LQCHARTS_PLUGIN_URL . '/assets/img/heart.png' ?>">
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get statistic from quiz
	 *
	 * @since 1.0.0
	 *
	 * @param $quiz_id
	 *
	 * @return array
	 */
	private function get_points( $quiz_id ) {

		$points = array();

		if ( class_exists( 'WpProQuiz_Model_StatisticUserMapper' ) ) {
			$statisticRefMapper  = new WpProQuiz_Model_StatisticRefMapper();
			$statisticUserMapper = new WpProQuiz_Model_StatisticUserMapper();

			$args = array(
				'quizId' => $quiz_id,
				'limit'  => 1000,
			);

			if ( ! current_user_can( 'administrator' ) ) {
				$args['users'] = array( get_current_user_id() );
			}

			$statisticModel = $statisticRefMapper->fetchHistoryWithArgs( $args );

			foreach ( $statisticModel as $model ) {
				$statisticUsers = $statisticUserMapper->fetchUserStatistic( $model->getStatisticRefId(), $quiz_id );
				$point_key      = 0;

				foreach ( $statisticUsers as $statistic_user ) {
					if ( ! isset( $points[ $point_key ] ) ) {
						$points[ $point_key ] = array(
							'points'  => 0,
							'gpoints' => $statistic_user->getGPoints(),
							'votes'   => 0,
						);
					}

					$points[ $point_key ]['points'] += $statistic_user->getPoints();
					$points[ $point_key ]['votes'] ++;

					$point_key ++;
				}
			}
		}

		return $points;
	}

	/**
	 * Get points in percents
	 *
	 * @since 1.0.0
	 *
	 * @param $points
	 *
	 * @return mixed
	 */
	private function get_percents( $points ) {

		if ( count( $points ) ) {
			foreach ( $points as $point_key => $point ) {
				$max_points                       = $point['gpoints'] * $point['votes'];
				$points[ $point_key ]['percents'] = ceil( ( $point['points'] * 100 ) / $max_points );
			}
		}

		return $points;
	}

	/**
	 * Get points by weeks
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_weeks() {

		$the_query = new WP_Query( array(
			'post_type'   => 'sfwd-quiz',
			'numberposts' => 8,
			'orderby'     => 'date',
			'order'       => 'DESC',
			's'           => 'woche',
		) );

		$weeks_point = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$quiz_pro_id   = learndash_get_setting( get_the_ID(), 'quiz_pro', true );
				$weeks_point[] = $this->get_points( $quiz_pro_id );
			}
		}

		return $weeks_point;
	}

	/**
	 * Calculate total percents
	 *
	 * @since 1.0.0
	 *
	 * @param array $questions
	 *
	 * @return int
	 */
	private function get_total_percents( array $questions ) {

		$percent = 0;
		if ( count( $questions ) ) {
			$points     = 0;
			$max_points = 0;
			foreach ( $questions as $question_key => $question ) {
				if ( 3 > $question_key ) {
					$points     += $question['points'];
					$max_points += $question['gpoints'] * $question['votes'];
				}
			}

			$percent = ceil( ( $points * 100 ) / $max_points );
		}

		if ( ! is_numeric( $percent ) || 1 > $percent ) {
			$percent = 0;
		}

		return $percent;
	}

	/**
	 * get total points for all weeks
	 *
	 * @since 1.0.0
	 *
	 * @param array $weeks
	 *
	 * @return array
	 */
	private function get_total_points( array $weeks ) {

		$points = array();
		if ( count( $weeks ) ) {
			foreach ( $weeks as $week ) {
				$question_key = 0;
				foreach ( $week as $question ) {
					if ( ! isset( $points[ $question_key ] ) ) {
						$points[ $question_key ] = array(
							'points'  => 0,
							'gpoints' => $question['gpoints'],
							'votes'   => 0,
						);
					}

					$points[ $question_key ]['points'] += $question['points'];
					$points[ $question_key ]['votes']  += $question['votes'];
					$question_key ++;
				}
			}
		}

		return $points;
	}

	/**
	 * Prepare data for graph
	 *
	 * @since 1.0.0
	 *
	 * @param array $weeks
	 *
	 * @return array
	 */
	private function get_graph_data( array $weeks ) {

		$graph_data = array(
			'emb'  => '',
			'bef'  => '',
			'stab' => '',
		);

		for ( $week_key = count( $weeks ) - 1; 0 <= $week_key; $week_key -- ) {
			$graph_data['emb']  .= $weeks[ $week_key ][0]['points'] . ( 0 < $week_key ? ',' : '' );
			$graph_data['bef']  .= $weeks[ $week_key ][1]['points'] . ( 0 < $week_key ? ',' : '' );
			$graph_data['stab'] .= $weeks[ $week_key ][2]['points'] . ( 0 < $week_key ? ',' : '' );
		}

		return $graph_data;
	}

	/**
	 * Get percent growth from last week
	 *
	 * @since 1.0.0
	 *
	 * @param array $weeks
	 *
	 * @return float
	 */
	private function get_last_week_growth( array $weeks ) {

		$week          = 0;
		$week_max      = 0;
		$last_week     = 0;
		$last_week_max = 0;

		for ( $question_key = 0; 3 > $question_key; $question_key ++ ) {
			$week          += $weeks[0][ $question_key ]['points'];
			$week_max      += $weeks[0][ $question_key ]['gpoints'] * $weeks[0][ $question_key ]['votes'];
			$last_week     += $weeks[1][ $question_key ]['points'];
			$last_week_max += $weeks[1][ $question_key ]['gpoints'] * $weeks[1][ $question_key ]['votes'];
		}

		$week      = ceil( $week * 100 / $week_max );
		$last_week = ceil( $last_week * 100 / $last_week_max );

		return $week - $last_week;
	}

	/**
	 * Generate speech y percents
	 *
	 * @since 1.0.0
	 *
	 * @param $total
	 *
	 * @return mixed
	 */
	private function get_speech( $total ) {

		$speeches = array(
			'Du fühlst dich noch nicht fit. 😟 Bleib dran und schon bald wirst deine Besserung verspüren.',
			'Du fühlst dich noch nicht so fit. 😐 Bleib dran und schon bald bist du im grünen Bereich!',
			'Super! Du fühlst dich fit. 🙂 Mach weiter so!',
			'Hervorragend! 😁 Du fühlst dich sehr fit, beweglich und stabil. Weiter so!',
		);
		$key      = 0;
		if ( 25 < $total && 50 >= $total ) {
			$key = 1;
		} else if ( 50 < $total && 75 >= $total ) {
			$key = 2;
		} else if ( 75 < $total ) {
			$key = 3;
		}

		return $speeches[ $key ];
	}

	/**
	 * Get positive or negative thumbs
	 *
	 * @since 1.0.0
	 *
	 * @param array $weeks
	 *
	 * @return string
	 */
	private function get_thumbs( array $weeks ) {

		$thumb = '';

		if ( isset( $weeks[0][4] ) ) {
			if ( $weeks[0][4] % 2 ) {
				$thumb = '<i>👍</i> : Super! Du hast diese Woche Übungen zu deinen Favoriten hinzugefügt. Du kannst deine ' .
				         'bisherigen <a href="/favoriten/">hier</a> einsehen.';
			} else {
				$thumb = '<i>👎</i> : Schade. Du hast diese Woche keine Übung zu deinen Favoriten hinzugefügt. Du kannst deine ' .
				         'bisherigen <a href="/favoriten/">hier</a> einsehen.';
			}
		}

		return $thumb;
	}

}

/**
 * Runner
 *
 * @since 1.0.0
 */
function lqcharts_shortcode_runner() {

	$shortcode = new Learndash_QCharts_Shortcode;
	$shortcode->init();

	return;
}

lqcharts_shortcode_runner();
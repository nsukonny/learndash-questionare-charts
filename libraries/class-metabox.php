<?php
/**
 * Class Learndash_QCharts_Metabox
 * Add metaboxes for questionare
 *
 * @since 1.0.0
 */

class Learndash_QCharts_Metabox {

	/**
	 * Metabox initialization
	 *
	 * @since 1.0.0
	 */
	public function init() {

		add_action( 'save_post', array( $this, 'save_data' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_week_meta_box' ) );

	}

	/**
	 * Init metabox for select week
	 *
	 * @since 1.0.0
	 */
	public function add_week_meta_box() {

		add_meta_box(
			'lqcharts',
			'That is week quiz?',
			array( $this, 'week_meta_box_callback' ),
			null,
			'side',
			'high',
			array(
				'__back_compat_meta_box' => true,
			)
		);

	}

	/**
	 * Callback for week meta box
	 *
	 * @since 1.0.0
	 *
	 * @param $post
	 * @param $meta
	 */
	public function week_meta_box_callback( $post, $meta ) {

		$screens = $meta['args'];

		// Используем nonce для верификации
		wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

		// значение поля
		$value = get_post_meta( $post->ID, 'my_meta_key', 1 );

		// Поля формы для введения данных
		echo '<script>console.log("loaded");</script>';
		echo '<label for="myplugin_new_field">77777' . __( "Description for this field", 'myplugin_textdomain' ) . '</label> ';
		echo '<input type="text" id="myplugin_new_field" name="myplugin_new_field" value="' . $value . '" size="25" />';

	}

	/**
	 * Save metabox data
	 *
	 * @since 1.0.0
	 *
	 * @param $post_id
	 */
	public function save_data( $post_id ) {
		// Убедимся что поле установлено.
		if ( ! isset( $_POST['myplugin_new_field'] ) ) {
			return;
		}

		// проверяем nonce нашей страницы, потому что save_post может быть вызван с другого места.
		if ( ! wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		// если это автосохранение ничего не делаем
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// проверяем права юзера
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Все ОК. Теперь, нужно найти и сохранить данные
		// Очищаем значение поля input.
		$my_data = sanitize_text_field( $_POST['myplugin_new_field'] );

		// Обновляем данные в базе данных.
		update_post_meta( $post_id, 'my_meta_key', $my_data );

	}

}

/**
 * Runner
 *
 * @since 1.0.0
 */
function lqcharts_metabox_runner() {

	$metabox = new Learndash_QCharts_Metabox;

	$metabox->init();

	return;
}

//lqcharts_metabox_runner();
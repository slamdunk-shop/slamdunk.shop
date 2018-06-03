<?php

defined( 'ABSPATH' ) or exit;

if( ! class_exists( 'MC4WP_Form_Widget' ) ) {
	return;
}

/**
 * Adds Wowmall_MC4WP_Form_Widget widget.
 *
 * @ignore
 */
class Wowmall_MC4WP_Form_Widget extends MC4WP_Form_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		parent::__construct();
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array   $args     Widget arguments.
	 * @param array   $instance_settings Saved values from database.
	 */
	public function widget( $args, $instance_settings ) {

		if( ! empty( $instance_settings['pretext'] ) ) {

			$pretext = '<div class=wowmall-mc4wp-form-widget-pretext>' . $instance_settings['pretext'] . '</div>';

			$args['after_title'] .= $pretext;
		}


		parent::widget( $args, $instance_settings );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $settings Previously saved values from database.
	 *
	 * @return string|void
	 */
	public function form( $settings ) {

		parent::form( $settings );

		$settings = wp_parse_args( (array) $settings, array( 'pretext' => '' ) );

		?>
		<p>
			<label for=<?php echo esc_attr( $this->get_field_id( 'pretext' ) ); ?>><?php esc_html_e( 'Pretext:', 'wowmall' ); ?></label>
			<textarea class=widefat rows=16 cols=20 id=<?php echo esc_attr( $this->get_field_id( 'pretext' ) ); ?> name=<?php echo esc_attr( $this->get_field_name( 'pretext' ) ); ?>><?php echo esc_attr( $settings['pretext'] ); ?></textarea>
		</p>

		<?php
	}

	/**
	 * Validates widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array   $new_settings Values just sent to be saved.
	 * @param array   $old_settings Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_settings, $old_settings ) {

		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$new_settings['text'] = wp_kses_post( $new_settings['text'] );
		}

		return parent::update( $new_settings, $old_settings );
	}

} // class Wowmall_MC4WP_Form_Widget

unregister_widget( 'MC4WP_Form_Widget' );
register_widget( 'Wowmall_MC4WP_Form_Widget' );

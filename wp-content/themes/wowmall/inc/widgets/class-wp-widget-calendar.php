<?php
/**
 * Widget API: WP_Widget_Calendar class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement the Calendar widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Wowmall_Widget_Calendar extends WP_Widget_Calendar {
	/**
	 * Ensure that the ID attribute only appears in the markup once
	 *
	 * @since 4.4.0
	 *
	 * @static
	 * @access private
	 * @var int
	 */
	private static $instance = 0;

	/**
	 * Outputs the content for the current Calendar widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$week_begins = 'starts_from_' . (int) get_option( 'start_of_week' );

		echo '' . $args['before_widget'];
		if ( $title ) {
			echo '' . $args['before_title'] . $title . $args['after_title'];
		}
		if ( 0 === self::$instance ) {
			echo '<div id=calendar_wrap class="calendar_wrap ' . $week_begins . '">';
		} else {
			echo '<div class=calendar_wrap>';
		}
		get_calendar( false );
		echo '</div>';
		echo '' . $args['after_widget'];

		self::$instance++;
	}
}
<?php
/**
 * Widget API: WP_Widget_Recent_Comments class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Recent Comments widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Wowmall_Widget_Recent_Comments extends WP_Widget_Recent_Comments {

	/**
	 * Outputs the content for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Comments widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		$output = array();

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Recent Comments', 'wowmall' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;

		/**
		 * Filters the arguments for the Recent Comments widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Comment_Query::query() for information on accepted arguments.
		 *
		 * @param array $comment_args An array of arguments used to retrieve the recent comments.
		 */
		$comments = get_comments( apply_filters( 'widget_comments_args', array(
			'number'      => $number,
			'status'      => 'approve',
			'post_status' => 'publish'
		) ) );

		$output[] = $args['before_widget'];
		if ( $title ) {
			$output[] = $args['before_title'] . $title . $args['after_title'];
		}

		$output[] = '<ul id=recentcomments>';
		if ( is_array( $comments ) && $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

			foreach ( (array) $comments as $comment ) {
				$output[] = '<li class=recentcomments>';
				/* translators: comments widget: 1: comment author, 2: post link */
				$output[] = sprintf( esc_html_x( '%1$s on %2$s', 'widgets', 'wowmall' ),
					'<span class=comment-author-link>' . get_comment_author_link( $comment ) . '</span>',
					'<h6><a href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a></h6>'
				);
				$output[] = '</li>';
			}
		}
		$output[] = '</ul>';
		$output[] = $args['after_widget'];

		echo join( '', $output );
	}
}

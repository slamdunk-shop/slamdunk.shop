<?php
/**
 * Widget API: WP_Widget_Recent_Posts class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Recent Posts widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Wowmall_Widget_Recent_Posts extends WP_Widget_Recent_Posts {

	/**
	 * Outputs the content for the current Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Posts widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Recent Posts', 'wowmall' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;
		$show_date           = isset( $instance['show_date'] )           ? $instance['show_date']           : false;
		$show_author         = isset( $instance['show_author'] )         ? $instance['show_author']         : false;
		$show_cats           = isset( $instance['show_cats'] )           ? $instance['show_cats']           : false;
		$show_excerpt        = isset( $instance['show_excerpt'] )        ? $instance['show_excerpt']        : false;
		$show_comments_count = isset( $instance['show_comments_count'] ) ? $instance['show_comments_count'] : false;

		/**
		 * Filters the arguments for the Recent Posts widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Query::get_posts()
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true
		) ) );

		if ($r->have_posts()) :
		?>
		<?php echo '' . $args['before_widget']; ?>
		<?php if ( $title ) {
			echo  '' . $args['before_title'] . $title . $args['after_title'];
		} ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<div class=post-meta>
				<?php if ( $show_cats ) :
					echo get_the_category_list();
				endif;
				if ( $show_author ) :
					echo get_the_author_posts_link();
				endif;
				if ( $show_date ) :
					printf( '<time class="entry-date published" datetime="%1$s"><a href="%3$s">%2$s</a></time>',
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date() ),
					get_the_permalink()
					);
				endif; ?>
				</div>
				<h6><a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a></h6>
				<?php if ( $show_excerpt ) :
					global $post;
					$post->wowmall_in_widget = true; ?>
				<div class=post-excerpt>
					<?php the_excerpt(); ?>
				</div>
				<?php endif; ?>
				<?php if ( $show_comments_count ) : ?>
				<span class=comments-link>
					<?php comments_popup_link( '0', '1', '%' ); ?>
				</span>
				<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo  '' . $args['after_widget']; ?>
		<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;
	}

	/**
	 * Handles updating the settings for the current Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['show_author']         = isset( $new_instance['show_author'] )         ? (bool) $new_instance['show_author']         : false;
		$instance['show_cats']           = isset( $new_instance['show_cats'] )           ? (bool) $new_instance['show_cats']           : false;
		$instance['show_excerpt']        = isset( $new_instance['show_excerpt'] )        ? (bool) $new_instance['show_excerpt']        : false;
		$instance['show_comments_count'] = isset( $new_instance['show_comments_count'] ) ? (bool) $new_instance['show_comments_count'] : false;
		return parent::update( $new_instance, $instance );
	}

	/**
	 * Outputs the settings form for the Recent Posts widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		parent::form( $instance );
		$show_cats           = isset( $instance['show_cats'] )           ? (bool) $instance['show_cats']           : false;
		$show_author         = isset( $instance['show_author'] )         ? (bool) $instance['show_author']         : false;
		$show_excerpt        = isset( $instance['show_excerpt'] )        ? (bool) $instance['show_excerpt']        : false;
		$show_comments_count = isset( $instance['show_comments_count'] ) ?(bool)  $instance['show_comments_count'] : false;
?>
		<p><input class=checkbox type=checkbox<?php checked( $show_author ); ?> id=<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?> name=<?php echo esc_attr( $this->get_field_name( 'show_author' ) ); ?>>
			<label for=<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>><?php esc_html_e( 'Display post author?', 'wowmall' ); ?></label></p>

		<p><input class=checkbox type=checkbox<?php checked( $show_cats ); ?> id=<?php echo esc_attr( $this->get_field_id( 'show_cats' ) ); ?> name=<?php echo esc_attr( $this->get_field_name( 'show_cats' ) ); ?>>
			<label for=<?php echo esc_attr( $this->get_field_id( 'show_cats' ) ); ?>><?php esc_html_e( 'Display post cats?', 'wowmall' ); ?></label></p>

		<p><input class=checkbox type=checkbox<?php checked( $show_excerpt ); ?> id=<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?> name=<?php echo esc_attr( $this->get_field_name( 'show_excerpt' ) ); ?>>
			<label for=<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>><?php esc_html_e( 'Display post excerpt?', 'wowmall' ); ?></label></p>

		<p><input class=checkbox type=checkbox<?php checked( $show_comments_count ); ?> id=<?php echo esc_attr( $this->get_field_id( 'show_comments_count' ) ); ?> name=<?php echo esc_attr( $this->get_field_name( 'show_comments_count' ) ); ?>>
			<label for=<?php echo esc_attr( $this->get_field_id( 'show_comments_count' ) ); ?>><?php esc_html_e( 'Display post comments count?', 'wowmall' ); ?></label></p>
<?php
	}
}

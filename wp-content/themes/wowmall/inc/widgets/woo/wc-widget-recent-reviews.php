<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recent Reviews Widget.
 *
 * @author   WooThemes
 * @category Widgets
 * @package  WooCommerce/Widgets
 * @version  2.3.0
 * @extends  WC_Widget
 */
class Wowmall_WC_Widget_Recent_Reviews extends WC_Widget_Recent_Reviews {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	* Output widget.
	*
	* @see WP_Widget
	*
	* @param array $args
	* @param array $instance
	*/
	public function widget( $args, $instance ) {
		global $comments, $comment;

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}
		ob_start();

		$number   = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$comments = get_comments( array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish', 'post_type' => 'product' ) );

		if ( $comments ) {
			$this->widget_start( $args, $instance ); ?>

			<ul class=product_list_widget>

			<?php foreach ( (array) $comments as $comment ) {

				$_product    = wc_get_product( $comment->comment_post_ID );
				$rating      = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
				$rating_html = wc_get_rating_html( $rating );
				?>

				<li>
					<div class=widget-product-thumb>
						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
							<?php echo '' . $_product->get_image( 'woo_img_size_minicart' ); ?>
						</a>
					</div>
					<div class=widget-product-content>
						<h6 class=widget-product-title><?php echo esc_html( $_product->get_title() ); ?></h6>
						<?php echo '' . $rating_html;
						printf( '<span class=reviewer>' . _x( 'by %1$s', 'by comment author', 'wowmall' ) . '</span>', get_comment_author() );
						?>
					</div>
				</li>
				<?php
			} ?>
			</ul>
			<?php $this->widget_end( $args );
		}

		$content = ob_get_clean();
		echo '' . $content;

		$this->cache_widget( $args, $content );
	}
}

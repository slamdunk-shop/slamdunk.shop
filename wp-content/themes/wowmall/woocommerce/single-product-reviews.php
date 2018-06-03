<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div class=woocommerce-Reviews>
	<div id=comments>
		<h4 class=woocommerce-Reviews-title><?php
			if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && ( $count = $product->get_review_count() ) ) {
				if( wp_is_mobile() ) {
					printf( _n( '%s review', '%s reviews', $count, 'wowmall' ), $count );
				} else {
					printf( _n( '%s review for %s%s%s', '%s reviews for %s%s%s', $count, 'wowmall' ), $count, '<span>', get_the_title(), '</span>' );
				}
			} else
				esc_html_e( 'Reviews', 'wowmall' );
		?></h4>

		<?php if ( have_comments() ) : ?>

			<ol class=commentlist>
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class=woocommerce-pagination>';
				paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

			<p class=woocommerce-noreviews><?php esc_html_e( 'There are no reviews yet.', 'wowmall' ); ?></p>

		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>

		<div id=review_form_wrapper>
			<div id=review_form>
				<?php
					$commenter = wp_get_current_commenter();

					$comment_form = array(
						'title_reply'          => have_comments() ? esc_html__( 'Add a review', 'wowmall' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'wowmall' ), get_the_title() ),
						'title_reply_before'   => '<h4 id=reply-title class=comment-reply-title>',
						'title_reply_after'    => '</h4>',
						'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'wowmall' ),
						'comment_notes_before' => '',
						'comment_notes_after'  => '',
						'fields'               => array(
							'author' => '<p class=comment-form-author>' . '<label for=author>' . esc_html__( 'Name', 'wowmall' ) . ' <span class=required>*</span></label> ' .
										'<input id=author name=author type=text value="' . esc_attr( $commenter['comment_author'] ) . '" size=30 aria-required=true required placeholder="' . esc_html__( 'Enter your name, please', 'wowmall' ) . '"></p>',
							'email'  => '<p class=comment-form-email><label for=email>' . esc_html__( 'E-mail', 'wowmall' ) . ' <span class=required>*</span></label> ' .
										'<input id=email name=email type=email value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size=30 aria-required=true required placeholder="' . esc_html__( 'Enter your e-mail, please', 'wowmall' ) . '"></p>',
						),
						'label_submit'  => esc_html__( 'Submit', 'wowmall' ),
						'logged_in_as'  => '',
						'comment_field' => '<p class=comment-form-comment><label for=comment>' . esc_html__( 'Your Review', 'wowmall' ) . ' <span class=required>*</span></label><textarea id=comment name=comment cols=45 rows=8 required placeholder="' . esc_html__( 'Enter your review, please', 'wowmall' ) . '"></textarea></p>',
						'class_submit'  => 'submit btn btn-primary btn-sm',
						'submit_button' => '<button name=%1$s type=submit id=%2$s class="%3$s">%4$s</button>',
					);

					if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
						$comment_form['must_log_in'] = '<p class=must-log-in>' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'wowmall' ), esc_url( $account_page_url ) ) . '</p>';
					}

					if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
						$rating_field = '<p class=comment-form-rating>
							<label for="rating">' . esc_html__( 'Your Rating', 'wowmall' ) .'</label>
							<span class="stars"><i>1</i><i>2</i><i>3</i><i>4</i><i>5</i></span>
							<select name="rating" id="rating" required style="display:none">
								<option value="">' . esc_html__( 'Rate&hellip;', 'wowmall' ) . '</option>
								<option value=5>' . esc_html__( 'Perfect', 'wowmall' ) . '</option>
								<option value=4>' . esc_html__( 'Good', 'wowmall' ) . '</option>
								<option value=3>' . esc_html__( 'Average', 'wowmall' ) . '</option>
								<option value=2>' . esc_html__( 'Not that bad', 'wowmall' ) . '</option>
								<option value=1>' . esc_html__( 'Very Poor', 'wowmall' ) . '</option>
							</select>
							</p>';
						if( is_user_logged_in() ) {
							$comment_form['comment_field'] = $rating_field . $comment_form['comment_field'];
						} else {
							$comment_form['fields'] = array(
								'rating' => $rating_field
							) + $comment_form['fields'];
						}
					}

					comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>

	<?php else : ?>

		<p class=woocommerce-verification-required><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'wowmall' ); ?></p>

	<?php endif; ?>

	<div class="clear"></div>
</div>

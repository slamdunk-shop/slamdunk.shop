<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
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
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wowmall_options;

if ( $cross_sells ) :
	$GLOBALS['wowmall_wc_loop_condition'] = 'grid';
	if ( wp_is_mobile() ) {
		$GLOBALS['wowmall_wc_loop_condition'] = 'big';
	}
	?>

	<div class="cross-sells">

		<h4><?php _e( 'You may be interested in&hellip;', 'woocommerce' ) ?></h4>

		<div id="cross-sells" class="swiper-container" data-visible="4">
			<ul class="swiper-wrapper products">

			<?php foreach ( $cross_sells as $cross_sell ) : ?>

				<?php
				 	$post_object = get_post( $cross_sell->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );

					wc_get_template_part( 'content', 'product' ); ?>

			<?php endforeach; ?>

			</ul>
		</div>
		<?php if( ! empty( $wowmall_options['related_arrows'] ) ) { ?>
			<div class=swiper-button-prev></div>
			<div class=swiper-button-next></div>
		<?php } ?>
	</div>

<?php
	unset( $GLOBALS['wowmall_wc_loop_condition'] );
endif;

wp_reset_postdata();

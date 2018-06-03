<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
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

if ( $upsells ) :
	$GLOBALS['wowmall_wc_loop_condition'] = 'grid';
	if ( wp_is_mobile() ) {
		$GLOBALS['wowmall_wc_loop_condition'] = 'big';
	}
	?>

	<section class="up-sells upsells products">

		<h4><?php esc_html_e( 'You may also like&hellip;', 'woocommerce' ) ?></h4>

		<div id="up-sells" class="swiper-container" data-visible="3">
			<ul class="swiper-wrapper products">

			<?php foreach ( $upsells as $upsell ) : ?>

				<?php
				 	$post_object = get_post( $upsell->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );

					wc_get_template_part( 'content', 'product' ); ?>

			<?php endforeach; ?>

			</ul>
		</div>
		<?php if( ! empty( $wowmall_options['related_arrows'] ) ) { ?>
			<div class=swiper-button-prev></div>
			<div class=swiper-button-next></div>
		<?php } ?>

	</section>

<?php
	unset( $GLOBALS['wowmall_wc_loop_condition'] );
endif;

wp_reset_postdata();

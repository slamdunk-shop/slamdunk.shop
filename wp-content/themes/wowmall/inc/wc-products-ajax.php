<?php

/**
 * Class for including page templates.
 *
 * @since 1.0.0
 */
class WowmallWcAjaxProducts {

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 */
	protected static $_instance;

	public function __construct() {

		add_action( 'woocommerce_before_shop_loop', array(
			$this,
			'products_wrapper_start',
		), - 999 );

		add_action( 'woocommerce_after_shop_loop', array(
			$this,
			'products_wrapper_end',
		), 999 );

		add_action( 'woocommerce_before_template_part', array(
			$this,
			'woocommerce_before_template_part',
		), 10, 4 );

		add_action( 'woocommerce_after_template_part', array(
			$this,
			'woocommerce_after_template_part',
		), 10, 4 );

		if ( is_main_query() && ( is_shop() || is_product_taxonomy() ) ) {

			add_filter( 'loop_end', array(
				$this,
				'loop_end',
			), 9 );
		}
	}

	public static function instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function woocommerce_before_template_part( $template_name, $template_path, $located, $args ) {

		if ( 'loop/no-products-found.php' === $template_name ) {

			$this->products_wrapper_start();
		}
	}

	public function woocommerce_after_template_part( $template_name, $template_path, $located, $args ) {

		if ( 'loop/no-products-found.php' === $template_name ) {

			$this->products_wrapper_end();
		}
	}

	public function products_wrapper_start() {
		$class = '';
		if ( 'list' === wowmall_get_condition() ) {
			$class = ' list';
		}

		echo '<div class="wowmall-wc-ajax-products-wrapper' . $class . '">' . "\n";
	}

	public function products_wrapper_end() {
		global $wowmall_options;
		$color = ! empty( $wowmall_options['accent_color_1'] ) ? $wowmall_options['accent_color_1'] : '#fc6f38';
		echo '<div class=ajax-page-loader>
			<svg width=100 height=100 xmlns=http://www.w3.org/2000/svg><circle cx=50% cy=50% r=40 stroke=' . $color . ' fill=none stroke-width=5><animate attributeName=stroke-dashoffset dur=2s repeatCount=indefinite from=0 to=502 attributeType=XML></animate><animate attributeName=stroke-dasharray dur=2s repeatCount=indefinite values="150.6 100.4;1 250;150.6 100.4" attributeType=XML></animate></circle></svg>
</div>';

		echo '</div>' . "\n";
	}

	public function loop_end() {

		global $woocommerce_loop;

		$this->loop = $woocommerce_loop['loop'];
	}
}

function tm_wc_ajax() {

	return WowmallWcAjaxProducts::instance();
}

tm_wc_ajax();
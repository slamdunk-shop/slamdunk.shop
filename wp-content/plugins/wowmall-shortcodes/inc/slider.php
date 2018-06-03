<?php

if ( ! class_exists( 'wowmallSlider' ) ) {

	class wowmallSlider {

		protected static $_instance = null;

		public $slide_number;

		public function __construct() {

			add_shortcode( 'wowmall_slider', array(
				$this,
				'slider_shortcode',
			) );

			add_shortcode( 'wowmall_slider_item', array(
				$this,
				'slide_shortcode',
			) );
		}

		public function slider_shortcode( $atts = array(), $content = null ) {

			$atts = shortcode_atts( array(
				'full_window' => false,
				'css'         => '',
				'el_class'    => '',
			), $atts );

			ob_start();

			if ( ! empty( $content ) ) {

				$this->slide_number = 1;

				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script( 'wowmall-slider', wowmallShortcodes::$pluginurl . '/assets/js/slider' . $min . '.js', array( 'wowmall-theme-script' ), '1.1', true );

				$content = apply_filters( 'the_content', $content );

				$id = uniqid();
				if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) {
					$class = '';
					if ( '' !== $atts['css'] && function_exists( 'vc_shortcode_custom_css_class' ) ) {
						$class .= vc_shortcode_custom_css_class( $atts['css'], ' ' );
					}
					if ( ! empty( $atts['el_class'] ) ) {
						$class .= ' ' . $atts['el_class'];
					};
					?>
					<div class="<?php echo $class; ?>">
				<?php }
				?>

				<div class="wowmall-slider swiper-container<?php if ( $atts['full_window'] ) {
					echo ' wowmall-slider-full_window'; } ?>" id=<?php echo $id; ?>>
					<div class=swiper-wrapper>
						<?php echo $content; ?>
					</div>
					<?php if ( ! wp_is_mobile() ) { ?>
						<div class=swiper-button-prev id=swiper-button-prev<?php echo $id; ?>></div>
						<div class=swiper-button-next id=swiper-button-next<?php echo $id; ?>></div>
					<?php } ?>
				</div>
				<?php if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) { ?>
					</div>
				<?php }
			}

			return ob_get_clean();
		}

		public function slide_shortcode( $atts = array(), $content = null ) {

			$atts = shortcode_atts( array(
				'slide' => '',
			), $atts );

			$slide = $atts['slide'];

			if ( ! empty( $slide ) ) {

				ob_start();

				global $wowmall_options;

				$slides = get_transient( 'wowmall_slides' );
				if( ! $slides ) {
					$slides = array();
				}
				if( ! isset( $slides[$slide] ) ) {
					$slides[$slide] = wp_get_attachment_image_src( $slide, 'full' );
					set_transient( 'wowmall_slides', $slides );
				}

				$src = $slides[$slide];

				$height = $src[2] * 100 / $src[1]; ?><div class="swiper-slide wowmall-slide__<?php echo $this->slide_number; ?>" style="height:auto;padding-top:<?php echo $height; ?>%">
				<div class="wowmall-slide-img"<?php
				if ( ! empty( $wowmall_options['lazy'] ) ) {
					?> data-src="<?php echo $src[0]; ?>"<?php
				} else {
					?> style="background-image: url(<?php echo $src[0]; ?>);"<?php
				}
				?>></div>
				<div class="wowmall-slide-caption">
					<div class="wowmall-slide-caption__inner"><?php echo $content; ?></div>
				</div>
				</div><?php

				++$this->slide_number;

				return ob_get_clean();
			}

			return '';
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallSlider::instance();
}
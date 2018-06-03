<?php

if ( ! class_exists( 'wowmallSlider' ) ) {

	class wowmallSlider {

		protected static $_instance = null;

		public static $placeholder, $front_placeholder;

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

			if ( is_admin() ) {
				add_action( 'vc_before_init', array(
					$this,
					'vc_map',
				) );
			}

			add_action( 'save_post', array( $this, 'reset_transients' ) );

			add_action( 'trashed_post', array( $this, 'reset_transients' ) );

			self::$placeholder = 'https://placeholdit.imgix.net/~text?txtsize=15&txt=IMG&w=60&h=60';
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

		public function reset_transients() {
			delete_transient( 'wowmall_slides' );
		}

		public function vc_map() {

			$params = array(
				'name'                    => esc_html__( 'Wowmall Slider', 'wowmall-shortcodes' ),
				'base'                    => 'wowmall_slider',
				"as_parent"               => array( 'only' => 'wowmall_slider_item' ),
				"show_settings_on_create" => false,
				'category'                => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				"is_container"            => true,
				'params'                  => array(
					array(
						'type'       => 'checkbox',
						'heading'    => esc_html__( 'Full window mode', 'wowmall-shortcodes' ),
						'param_name' => 'full_window',
					),
					array(
						'type'        => 'textfield',
						'heading'     => esc_html__( 'Extra class name', 'wowmall-shortcodes' ),
						'param_name'  => 'el_class',
						'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wowmall-shortcodes' ),
					),
					array(
						'type'       => 'css_editor',
						'heading'    => esc_html__( 'Css Box', 'wowmall-shortcodes' ),
						'param_name' => 'css',
						'group'      => esc_html__( 'Design options', 'wowmall-shortcodes' ),
					),
				),
				"js_view"                 => 'VcColumnView',
			);

			vc_map( $params );

			$params = array(
				'name'     => esc_html__( 'Wowmall Slider Item', 'wowmall-shortcodes' ),
				'base'     => 'wowmall_slider_item',
				'category' => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				"as_child" => array( 'only' => 'wowmall_slider' ),
				'params'   => array(
					array(
						'type'       => 'attach_image',
						'heading'    => esc_html__( 'Slide Image', 'wowmall-shortcodes' ),
						'param_name' => 'slide',
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => esc_html__( 'Slide Text', 'wowmall-shortcodes' ),
						'param_name' => 'content',
					),
				),
			);

			vc_map( $params );
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

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_Wowmall_Slider extends WPBakeryShortCodesContainer {

	}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_Wowmall_Slider_Item extends WPBakeryShortCode {

	}
}
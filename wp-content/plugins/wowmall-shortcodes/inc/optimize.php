<?php

class wowmallOptimizer {

	protected static $_instance = null, $is_wc_activated;

	var $latest_scripts = array(), $lazy_img = null, $preloader_imgs = array(), $banners = array(), $framework;

	public function __construct() {

		add_action( 'redux/loaded', array(
			$this,
			'redux_construct',
		) );

		if ( ! is_admin() ) {
			remove_action( 'plugins_loaded', array(
				'RevSliderFront',
				'createDBTables',
			) );
			remove_action( 'plugins_loaded', array(
				'RevSliderPluginUpdate',
				'do_update_checks',
			) );
			remove_action( 'plugins_loaded', '_wp_customize_include' );
		}

		add_action( 'init', array(
			$this,
			'init',
		), 10 );

		add_filter( 'pre_transient_bsf_check_product_updates', array(
			$this,
			'bsf_check_product_updates',
		), 10, 2 );
	}

	public static function instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function bsf_check_product_updates( $pre_transient, $transient ) {

		if ( is_admin() ) {
			return $pre_transient;
		}

		return true;
	}

	public function init() {

		global $wowmall_options;

		remove_shortcode( 'rev_slider' );

		add_shortcode( 'rev_slider', array(
			$this,
			'rev_slider_shortcode',
		) );

		if ( ! empty( $wowmall_options['optimize'] ) ) {

			if ( ! empty( $wowmall_options['cdn'] ) ) {

				add_action( 'wp_enqueue_scripts', array(
					$this,
					'redirect_assets_to_cdn',
				), 999 );
			}

			add_action( 'wp_enqueue_scripts', array(
				$this,
				'contact_form_assets',
			), -999 );

			add_action( 'wp_enqueue_scripts', array(
				$this,
				'scripts_to_footer',
			), 999 );

			add_action( 'wp_head', array(
				$this,
				'remove_emoji_detection_script',
			), 6 );

			add_action( 'wp_print_footer_scripts', array(
				$this,
				'optimize_wc_scripts',
			), 6 );

			add_filter( 'the_content', array(
				$this,
				'pre_parse_shortcodes',
			), -9999 );

			add_filter( 'the_content', array(
				$this,
				'parse_shortcodes',
			), 9999 );

			add_filter( 'script_loader_src', array(
				$this,
				'script_loader_src',
			), 10, 2 );

			add_filter( 'revslider_getUsedFonts', array(
				$this,
				'revslider_getUsedFonts',
			) );

			add_action( 'wp_print_footer_scripts', array(
				$this,
				'print_latest_scripts',
			), 25 );
		}

		add_filter( 'woocommerce_single_product_image_thumbnail_html', array(
			$this,
			'woocommerce_single_product_image_html',
		), 9 );

		add_filter( 'woocommerce_placeholder_img', array(
			$this,
			'woocommerce_placeholder_img',
		), 10, 3 );

		add_filter( 'woocommerce_placeholder_img_src', array(
			$this,
			'woocommerce_placeholder_img_src',
		) );

		if ( ! is_admin() ) {
			if ( ! empty( $wowmall_options['lazy'] ) ) {
				if ( ! ( function_exists( 'vc_is_page_editable' ) && function_exists( 'vc_enabled_frontend' ) && vc_is_page_editable() && vc_enabled_frontend() ) ) {
					add_filter( 'wp_get_attachment_image_attributes', array(
						$this,
						'wp_get_attachment_image_attributes',
					), 10, 3 );

					add_filter( 'wp_get_attachment_image_src', array(
						$this,
						'wp_get_attachment_image_src',
					), 10, 4 );

					add_filter( 'ultimate_images', array(
						$this,
						'ultimate_images',
					) );
				}
			}
		}
	}

	public function redux_construct( $framework ) {
		$this->framework = $framework;
	}

	public function pre_parse_shortcodes( $content ) {

		if ( wp_doing_ajax() ) {
			return $content;
		}
		if ( preg_match_all( '/' . get_shortcode_regex( array( 'interactive_banner' ) ) . '/s', $content, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $shortcode ) {

				$shortcode_attrs = shortcode_parse_atts( $shortcode[3] );
				if ( ! is_array( $shortcode_attrs ) ) {
					$shortcode_attrs = array();
				}

				if ( ! empty( $shortcode_attrs['banner_image'] ) ) {
					$banner      = explode( '|', $shortcode_attrs['banner_image'] );
					$banner_atts = array();
					foreach ( $banner as $item ) {
						$attr                  = explode( '^', $item );
						$banner_atts[$attr[0]] = $attr[1];
					}
					if ( ! empty( $banner_atts['url'] ) && ! empty( $banner_atts['id'] ) ) {
						$this->banners[$banner_atts['url']] = $banner_atts['id'];
					}
				}
			}
		}

		return $content;
	}

	public function parse_shortcodes( $content ) {

		if ( wp_doing_ajax() ) {
			return $content;
		}

		global $is_edge, $is_IE, $wowmall_options;

		if ( ! empty( $wowmall_options['lazy'] ) ) {

			$re = '/<img[^>]*? src=[\"\']([^\"\']*[?&]wowmall_lazy)[\"\']/mi';

			preg_match_all( $re, $content, $matches, PREG_SET_ORDER, 0 );

			if ( ! empty( $matches ) ) {
				foreach ( $matches as $match ) {

					$url     = remove_query_arg( 'wowmall_lazy', $match[1] );
					$img_id  = $this->get_image_id_by_url( $url );
					$img_src = wp_get_attachment_image_src( $img_id, 'full' );

					if ( $img_id && $img_src ) {
						$width  = $img_src[1];
						$height = $img_src[2];
						$atts   = '" width="' . $width . '" height="' . $height;
						$key    = $width . '-' . $height;
						if ( empty( $this->preloader_imgs[$key] ) ) {
							$svg = $this->wowmall_svg_placeholder_base64( $width, $height );
							if ( $is_edge || $is_IE ) {
								$svg = 'data:image/svg+xml,%3Csvg%20width%3D%22' . $width . '%22%20height%3D%22' . $height . '%22%20xmlns%3D%22http:%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3C%2Fsvg%3E';
							}
							$this->preloader_imgs[$key]['img'] = $svg;
						}
						$atts    .= '" data-wowmall-lazy="swiper-lazy';
						$before  = $this->preloader_imgs[$key]['img'] . '" data-wowmall-src="';
						$img     = $before . $url . $atts;
						$content = str_replace( $match[1], $img, $content );
					}
				}
			}
		}

		if ( ! wp_script_is( 'jquery', 'done' ) ) {
			preg_match_all( "/<script((?:(?!src=).)*?)>(.*?)<\/script>/smix", $content, $matches );
			if ( ! empty( $matches[0] ) && ! empty( $matches[2] ) ) {
				$this->latest_scripts = array_merge( $this->latest_scripts, $matches[2] );
				foreach ( $matches[0] as $match ) {
					$content = str_replace( $match, '', $content );
				}
			}
		}

		return $content;
	}

	public function get_image_id_by_url( $url ) {

		if ( isset( $this->banners[$url] ) ) {
			return $this->banners[$url];
		}

		global $wpdb;

		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid = %s";
		$id    = $wpdb->get_var( $wpdb->prepare( $query, esc_url( $url ) ) );

		return $id;
	}

	public function wowmall_svg_placeholder_base64( $width = 100, $height = 100 ) {
		return 'data:image/svg+xml;base64,' . base64_encode( $this->wowmall_svg_placeholder( $width, $height ) );
	}

	public function wowmall_svg_placeholder( $width = 100, $height = 100 ) {
		global $wowmall_options;
		$color = ! empty( $wowmall_options['accent_color_1'] ) ? str_replace( '#', '', $wowmall_options['accent_color_1'] ) : '000';
		$box   = '';
		if ( 90 > min( $width, $height ) ) {
			$box = " viewBox='0 0 100 100'";
		}

		return "<svg width='" . $width . "' height='" . $height . "'" . $box . " xmlns='http://www.w3.org/2000/svg'><circle cx='50%' cy='50%' r='40' stroke='#" . $color . "' fill='none' stroke-width='5'><animate attributeName='stroke-dashoffset' dur='2s' repeatCount='indefinite' from='0' to='502' attributeType='XML'/><animate attributeName='stroke-dasharray' dur='2s' repeatCount='indefinite' values='150.6 100.4;1 250;150.6 100.4' attributeType='XML'/></circle></svg>";
	}

	public function remove_emoji_detection_script() {
		global $wowmall_options;
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		if ( isset( $wowmall_options['remove_emoji'] ) && ! $wowmall_options['remove_emoji'] ) {
			add_action( 'wp_footer', 'print_emoji_detection_script', 7 );
		}
	}

	public function woocommerce_single_product_image_html( $image ) {

		global $product, $wowmall_options;

		if ( has_post_thumbnail() ) {
			return $image;
		}

		$color = ! empty( $wowmall_options['accent_color_2'] ) ? str_replace( '#', '', $wowmall_options['accent_color_2'] ) : '222';

		$text = '';
		if ( ! empty( $product ) ) {
			$text = get_the_title();
		}
		if ( ! empty( $wowmall_options['product_page_layout'] ) && '1' === $wowmall_options['product_page_layout'] ) {
			$size = ! empty( $wowmall_options['woo_img_size_single_1'] ) ? $wowmall_options['woo_img_size_single_1'] : array(
				'width'  => '830',
				'height' => '966',
			);
		}
		else {
			$size = ! empty( $wowmall_options['woo_img_size_single_2'] ) ? $wowmall_options['woo_img_size_single_2'] : array(
				'width'  => '830',
				'height' => '966',
			);
		}

		return '<img width=' . esc_attr( $size['width'] ) . ' height=' . esc_attr( $size['height'] ) . ' src="https://placehold.it/' . esc_attr( $size['width'] ) . 'x' . esc_attr( $size['height'] ) . '/a3a3a3/' . $color . '/?text=' . esc_attr( $text ) . '" alt="%3$s">';

	}

	public function woocommerce_placeholder_img( $image, $size, $dimensions ) {
		return str_replace( 'placehold.it/300', 'placehold.it/' . esc_attr( $dimensions['width'] ) . 'x' . esc_attr( $dimensions['height'] ), $image );
	}

	public function woocommerce_placeholder_img_src() {
		global $wowmall_options, $product;

		$color = ! empty( $wowmall_options['accent_color_2'] ) ? str_replace( '#', '', $wowmall_options['accent_color_2'] ) : '222';

		$text = get_bloginfo( 'name' );

		if ( ! empty( $product ) && $product instanceof WC_Product ) {
			$text = $product->get_name();
		}

		$text = preg_replace( '/\s+/', '+', $text );

		return 'https://placehold.it/300/a3a3a3/' . $color . '/?text=' . $text;
	}

	public function rev_slider_shortcode( $atts ) {
		if ( function_exists( 'rev_slider_shortcode' ) ) {
			$slider = rev_slider_shortcode( $atts );
			$slider = preg_replace( '/"\s*(\n)/', "\" \r\n\t", $slider );

			return $slider;
		}

		return null;
	}

	public function revslider_getUsedFonts( $fonts ) {

		if ( empty( $this->framework->typography ) || empty( $fonts ) ) {
			return $fonts;
		}

		foreach ( $this->framework->typography as $font => $params ) {
			$font = str_replace( '+', ' ', $font );
			if ( ! empty( $fonts[$font] ) ) {
				if ( isset( $params['all-styles'] ) ) {
					unset( $fonts[$font] );
				}
			}
		}

		return $fonts;
	}

	public function redirect_assets_to_cdn() {

		if ( is_admin() ) {
			return;
		}

		$this->wowmall_local_to_cdn_script( 'jquery-core' );
		$this->wowmall_local_to_cdn_script( 'jquery-migrate' );
		$this->wowmall_local_to_cdn_script( 'jquery-blockui' );
		$this->wowmall_local_to_cdn_script( 'jquery-cookie', 'jquery-cookie' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-core', 'jqueryui', 'jquery-ui', true );
		$this->wowmall_local_to_cdn_script( 'jquery_ui', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-core', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-blind', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-bounce', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-clip', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-drop', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-explode', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-fade', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-fold', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-highlight', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-puff', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-pulsate', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-scale', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-shake', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-size', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-slide', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-effects-transfer', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-accordion', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-autocomplete', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-button', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-datepicker', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-dialog', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-draggable', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-droppable', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-menu', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-mouse', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-position', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-progressbar', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-resizable', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-selectable', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-selectmenu', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-slider', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-sortable', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-spinner', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-tabs', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-tooltip', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'jquery-ui-widget', 'jqueryui', 'jquery-ui' );
		$this->wowmall_local_to_cdn_script( 'underscore', 'underscore.js', false, false, '-min' );
		$this->wowmall_local_to_cdn_script( 'wc-jquery-ui-touchpunch', 'jqueryui-touch-punch', false, false, '', '0.2.3', 'jquery.ui.touch-punch.min' );
		$this->wowmall_local_to_cdn_script( 'masonry', 'masonry', false, false, '.min', '4.1.1', 'masonry.pkgd' );
		$this->wowmall_local_to_cdn_script( 'swiper', 'Swiper', false, false, '.min', '3.4.1/js' );
		$this->wowmall_local_to_cdn_script( 'magnific-popup', 'magnific-popup.js', false, false, '.min', '1.1.0', 'jquery.magnific-popup' );
		$this->wowmall_local_to_cdn_script( 'jquery-zoom', 'jquery-zoom', false, false, '.min', '1.7.18' );
		$this->wowmall_local_to_cdn_script( 'wowmall-lazy-load', 'jquery_lazyload' );
		$this->wowmall_local_to_cdn_script( 'jquery-throttle-debounce', 'jquery-throttle-debounce', false, false, '.min', '1.1' );
		$this->wowmall_local_to_cdn_script( 'touch-swipe', 'jquery.touchswipe', false, false, '.min', '1.6.18' );
		$this->wowmall_local_to_cdn_style( 'swiper', '3.4.1/css', 'Swiper' );
		$this->wowmall_local_to_cdn_style( 'magnific-popup', '1.1.0', 'magnific-popup.js' );
	}

	public function wowmall_local_to_cdn_script( $script, $path = null, $group = false, $main_in_group = false, $min = '.min', $version = false, $name = false ) {

		global $wp_scripts;

		$registered = $wp_scripts->registered;

		if ( isset( $registered[$script] ) ) {

			if ( $group ) {
				$name = $group;
			}
			elseif ( ! $name && ! empty( $registered[$script]->src ) ) {
				$name = basename( $registered[$script]->src );
				$name = str_replace( array(
					'.min.js',
					'.js',
				), '', $name );
			}
			if ( ! $name ) {
				return;
			}
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : $min;
			$ver = $version ? $version : $registered[$script]->ver;
			if ( ! $path ) {
				$path = $name;
			}
			$url = '//cdnjs.cloudflare.com/ajax/libs/' . $path . '/' . $ver . '/' . $name . $min . '.js';
			if ( $group && ! $main_in_group ) {
				$url = false;
			}
			$registered[$script]->src            = $url;
			$registered[$script]->extra['group'] = 1;
		}
	}

	public function wowmall_local_to_cdn_style( $style, $version = false, $path = null, $min = '.min', $name = false ) {
		global $wp_styles;
		$registered = $wp_styles->registered;

		if ( isset( $registered[$style] ) ) {
			if ( ! $name && ! empty( $registered[$style]->src ) ) {
				$name = basename( $registered[$style]->src );
				$name = str_replace( array(
					'.min.css',
					'.css',
				), '', $name );
			}
			if ( ! $name ) {
				return;
			}
			if ( ! $path ) {
				$path = $name;
			}
			$ver                     = $version ? $version : $registered[$style]->ver;
			$url                     = '//cdnjs.cloudflare.com/ajax/libs/' . $path . '/' . $ver . '/' . $name . $min . '.css';
			$registered[$style]->src = $url;
		}
	}

	public function contact_form_assets() {
		global $post;

		if ( defined( 'WPCF7_VERSION' ) && isset( $post->post_content ) ) {

			if ( ! has_shortcode( $post->post_content, 'contact-form-7' ) ) {
				add_filter( 'wpcf7_load_css', '__return_false' );
				add_filter( 'wpcf7_load_js', '__return_false' );
			}
		}
	}

	public function scripts_to_footer() {

		global $wp_scripts;

		$registered_scripts = $wp_scripts->registered;

		if ( isset( $registered_scripts['jquery'] ) ) {
			$registered_scripts['jquery']->extra['group'] = 1;
		}
		if ( isset( $registered_scripts['jquery-core'] ) ) {
			$registered_scripts['jquery-core']->extra['group'] = 1;
		}
		if ( isset( $registered_scripts['jquery-migrate'] ) ) {
			$registered_scripts['jquery-migrate']->extra['group'] = 1;
		}
		if ( isset( $registered_scripts['ultimate-script'] ) ) {
			$registered_scripts['ultimate-script']->extra['group'] = 1;
		}
		if ( isset( $registered_scripts['wc-add-to-cart'] ) ) {
			$registered_scripts['wc-add-to-cart']->extra['group'] = 1;
		}
		if ( isset( $registered_scripts['vc_woocommerce-add-to-cart-js'] ) ) {
			$registered_scripts['vc_woocommerce-add-to-cart-js']->extra['group'] = 1;
		}
		if ( isset( $registered_scripts['revmin'] ) ) {
			$registered_scripts['revmin']->deps[] = 'tp-tools';
			array_unique( $registered_scripts['revmin']->deps );
		}
		if ( isset( $registered_scripts['revmin-actions'] ) ) {
			$registered_scripts['revmin-actions']->deps[] = 'revmin';
			array_unique( $registered_scripts['revmin-actions']->deps );
		}
	}

	public function optimize_wc_scripts() {
		global $wp_scripts;
		$queue = $wp_scripts->queue;
		if ( ! wp_script_is( 'jquery', 'done' ) ) {
			$registered = $wp_scripts->registered;
			$all_data   = array();
			foreach ( $queue as $script ) {
				if ( isset( $registered[$script]->extra['data'] ) ) {
					$all_data[] = preg_replace( '/<!--(.*)-->/Uis', '', $registered[$script]->extra['data'] );
					unset( $registered[$script]->extra['data'] );
				}
				if ( ! empty( $registered[$script]->deps ) ) {
					foreach ( $registered[$script]->deps as $dep ) {
						if ( isset( $registered[$dep]->extra['data'] ) ) {
							$all_data[] = preg_replace( '/<!--(.*)-->/Uis', '', $registered[$dep]->extra['data'] );
							unset( $registered[$dep]->extra['data'] );
						}
					}
				}
			}
			if ( isset( $registered['jquery-core']->extra['before'] ) ) {
				$all_data = array_merge( $registered['jquery-core']->extra['before'], $all_data );
			}
			$all_data                                   = array_unique( $all_data );
			$registered['jquery-core']->extra['before'] = $all_data;
		}
	}

	public function print_latest_scripts() {
		if ( ! empty( $this->latest_scripts ) ) {
			$this->latest_scripts = array_unique( $this->latest_scripts );
			?>
			<script><?php echo join( '', $this->latest_scripts ) ?></script><?php }
	}

	public function script_loader_src( $src, $name ) {
		if ( 'concatemoji' === $name ) {
			wp_enqueue_script( 'wp-emoji-release', $src, array(), null, true );

			return false;
		}

		return $src;
	}

	public function wp_get_attachment_image_src( $image, $attachment_id, $size, $icon ) {
		if ( ! $icon ) {
			$this->lazy_img = $image;
		}

		return $image;
	}

	public function wp_get_attachment_image_attributes( $attr ) {

		global $is_edge, $is_IE;

		if ( function_exists( 'is_product' ) && is_product() ) {
			global $product;
			if ( ! empty( $product ) && $product instanceof WC_Product && $product->is_type( 'variable' ) ) {
				return $attr;
			}
		}

		global $wowmall_wc_quick_view;

		if ( ! empty( $wowmall_wc_quick_view ) ) {
			return $attr;
		}

		if ( is_array( $this->lazy_img ) && $this->lazy_img[0] === $attr['src'] ) {

			$width  = $this->lazy_img[1];
			$height = $this->lazy_img[2];
			$key    = $width . '-' . $height;

			$attr['data-src'] = $attr['src'];
			if ( isset( $attr['sizes'] ) ) {
				$attr['data-sizes'] = $attr['sizes'];
			}
			if ( isset( $attr['srcset'] ) ) {
				$attr['data-srcset'] = $attr['srcset'];
			}
			$attr['class'] .= ' swiper-lazy';
			$attr['class'] .= ' ' . wp_doing_ajax();
			unset( $attr['sizes'], $attr['srcset'] );

			if ( empty( $this->preloader_imgs[$key] ) ) {
				$svg = $this->wowmall_svg_placeholder_base64( $width, $height );
				if ( $is_edge || $is_IE ) {
					$svg = 'data:image/svg+xml,%3Csvg%20width%3D%22' . $width . '%22%20height%3D%22' . $height . '%22%20xmlns%3D%22http:%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3C%2Fsvg%3E';
				}
				$this->preloader_imgs[$key]['img'] = $svg;
			}
			$attr['src'] = $this->preloader_imgs[$key]['img'];
			if ( $is_edge || $is_IE ) {
				if ( 100 > min( $width, $height ) ) {
					$attr['class'] .= ' ie-lazy-small';
				}
			}
		}
		unset( $GLOBALS['wowmall_lazy_img'] );

		return $attr;
	}

	public function ultimate_images( $img ) {

		return add_query_arg( array( 'wowmall_lazy' => '' ), $img );
	}
}

wowmallOptimizer::instance();
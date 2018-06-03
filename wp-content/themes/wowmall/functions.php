<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wowmall functions and definitions.
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 * @package Wowmall
 */

if ( ! class_exists( 'WowMall' ) ) {

	class WowMall {

		protected static $_instance = null;

		public static $mc4wp_forms, $is_woocommerce_activated, $page_has_wc_shortcode, $version;

		public function __construct() {

			define( 'WOWMALL_THEME_URI', get_template_directory_uri() );
			define( 'WOWMALL_THEME_DIR', trailingslashit( get_template_directory() ) );
			define( 'WOWMALL_THEME_ADMIN_DIR', trailingslashit( WOWMALL_THEME_DIR . 'admin' ) );
		}

		public function newsletter_forms() {
			if ( ! is_null( self::$mc4wp_forms ) ) {
				return self::$mc4wp_forms;
			}
			if ( ! function_exists( 'mc4wp_get_forms' ) ) {
				return null;
			}
			self::$mc4wp_forms = get_transient( 'wowmall_mc4wp_forms' );
			if ( self::$mc4wp_forms ) {
				return self::$mc4wp_forms;
			}
			$forms = mc4wp_get_forms();
			if ( empty( $forms ) ) {
				return null;
			}
			$parsed_forms = array();
			foreach ( $forms as $form ) {
				$parsed_forms[ $form->ID ] = $form->name . ' (' . $form->ID . ')';
			}
			set_transient( 'wowmall_mc4wp_forms', $parsed_forms );
			self::$mc4wp_forms = $parsed_forms;

			return $parsed_forms;
		}

		public function subscribe_form( $form_id = '' ) {

			$forms = $this->newsletter_forms();
			if ( ! ( ! empty( $form_id ) && ! empty( $forms ) && array_search( (int) $form_id, $forms ) ) ) {
				$form_id = '';
			}
			if ( function_exists( 'mc4wp_show_form' ) ) {
				mc4wp_show_form( $form_id );
			}
			else {
				esc_html_e( 'Plugin "MailChimp for WP" not activated', 'wowmall' );
			} ?>
		<?php }

		public function footer_text() {
			global $wowmall_options;
			if ( ! empty( $wowmall_options['footer_text'] ) && $wowmall_options['footer_text'] ) {
				$text   = trim( $wowmall_options['footer_text'] );
				$offset = strpos( $text, '%s' );
				if ( false !== $offset ) {
					$link = '';
					if ( ! empty( $wowmall_options['privacy_page'] ) ) {
						$id = (int) $wowmall_options['privacy_page'];
						if ( get_post_status( $id ) ) {
							$link = get_transient( 'wowmall_privacy_link' );
							if ( false === $link ) {
								$link['url']   = get_page_link( $id );
								$link['title'] = get_the_title( $id );
								set_transient( 'wowmall_privacy_link', $link );
							}
							$link_class = 'wowmall-privacy-link';
							if ( 0 === $offset ) {
								$link_class .= ' at_start';
							}
							else {
								$link_class .= ' at_end';
							}
							$link = sprintf( '<a href="%s" class="%s">%s</a>', esc_url( $link['url'] ), esc_attr( $link_class ), $link['title'] );
						}
					}
					$text = sprintf( $wowmall_options['footer_text'], $link );
				}
				?>
				<div class="footer-text">
					<?php echo wp_kses_post( $text ); ?>
				</div>
			<?php }
		}

		public function is_woocommerce_activated() {
			if ( ! is_null( self::$is_woocommerce_activated ) ) {
				return self::$is_woocommerce_activated;
			}

			self::$is_woocommerce_activated = class_exists( 'woocommerce' );

			return self::$is_woocommerce_activated;
		}

		public function page_has_wc_shortcode() {

			if ( is_null( self::$page_has_wc_shortcode ) ) {
				self::$page_has_wc_shortcode = false;
				if ( class_exists( 'WC_Shortcodes' ) && is_page() ) {

					global $post;

					$woo_shortcodes = array(
						'wowmall_products_carousel',
						'wowmall_compare_table',
						'wowmall_wishlist_table',
						'wowmall_lookbook',
						'product',
						'product_page',
						'product_category',
						'product_categories',
						'add_to_cart',
						'products',
						'recent_products',
						'sale_products',
						'best_selling_products',
						'top_rated_products',
						'featured_products',
						'product_attribute',
						'related_products',
					);
					foreach ( $woo_shortcodes as $shortcode ) {
						if ( has_shortcode( $post->post_content, $shortcode ) ) {
							self::$page_has_wc_shortcode = true;
							break;
						}
					}
				}
			}

			return self::$page_has_wc_shortcode;
		}

		public function get_version() {

			if ( ! is_null( self::$version ) ) {
				return self::$version;
			}
			$theme         = wp_get_theme( get_template() );
			self::$version = $theme->get( 'Version' );
			unset( $theme );

			return self::$version;
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	WowMall::instance();
}

if ( ! function_exists( 'wowmall' ) ) {
	function wowmall() {
		if ( method_exists( 'WowMall', 'instance' ) ) {
			return WowMall::instance();
		}

		return null;
	}
}

require_once( WOWMALL_THEME_ADMIN_DIR . 'core-init.php' );

if ( ! function_exists( 'snapppt_embed_settings' ) ) {
	function snapppt_embed_settings() {
		return array(
			'name'     => esc_html__( 'Snapppt Embed', 'wowmall' ),
			'base'     => 'snapppt_embed',
			'class'    => '',
			'category' => esc_html__( 'Wowmall', 'wowmall' ),
			'params'   => array(
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Embed type', 'v' ),
					"param_name" => 'embed_type',
					'value'      => array(
						esc_html__( 'Grid', 'wowmall' )     => 'grid',
						esc_html__( 'Carousel', 'wowmall' ) => 'carousel',
					),
				),
			),
		);
	}
}
if ( function_exists( 'vc_lean_map' ) ) {
	vc_lean_map( 'snapppt_embed', 'snapppt_embed_settings' );
}

if ( ! function_exists( 'wowmall_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function wowmall_setup() {

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 */
		load_theme_textdomain( 'wowmall', WOWMALL_THEME_DIR . 'languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary' => 'Primary Menu',
			'social'  => 'Social Media Profiles',
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 * See https://developer.wordpress.org/themes/functionality/post-formats/
		 */
		add_theme_support( 'post-formats', array(
			'gallery',
			'link',
			'image',
			'quote',
			'video',
			'audio',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'wowmall_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		add_theme_support( 'woocommerce' );

		// woocommerce 3.x
		/*add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );*/

		if ( is_admin() ) {

			require_once( WOWMALL_THEME_ADMIN_DIR . 'admin-init.php' );
		}

		add_action( 'wp_enqueue_scripts', 'wowmall_fonts', 100 );

		wowmall_set_image_sizes();

		wowmall_maintenance();

		wowmall_includes();

	}
endif; // wowmall_setup

function wowmall_maintenance() {
	if ( is_user_logged_in() || is_admin() || $GLOBALS['pagenow'] === 'wp-login.php' ) {
		return;
	}
	global $wowmall_options;

	if ( ! empty( $wowmall_options['maintenance_mode'] ) ) {

		remove_action( 'init', 'wowmall_compare_start_session', 1 );

		remove_action( 'wp_head', 'wp_generator' );

		remove_action( 'wp_footer', 'print_emoji_detection_script', 7 );

		remove_action( 'wp_footer', 'woocommerce_demo_store' );

		remove_action( 'wp_enqueue_scripts', 'wowmall_compare_setup_plugin' );

		remove_action( 'wp_enqueue_scripts', 'wowmall_wishlist_setup_plugin' );

		remove_action( 'wp_print_styles', 'print_emoji_styles' );

		remove_action( 'wp_enqueue_scripts', 'ultimate_tabs', 1 );

		add_action( 'wp_enqueue_scripts', 'wowmall_maintenance_assets' );

		locate_template( array( 'maintenance.php' ), true );

		return;
	}
}

function wowmall_maintenance_assets() {
	$version = wowmall()->get_version();
	wp_enqueue_style( 'wowmall-maintenance', WOWMALL_THEME_URI . '/assets/css/maintenance.css', array(), $version );

	wp_enqueue_script( 'countdown', '//cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', array( 'jquery' ), '2.2.0', true );
	wp_enqueue_script( 'wowmall-maintenance', WOWMALL_THEME_URI . '/assets/js/min/maintenance.min.js', array( 'countdown' ), $version, true );
}

add_action( 'after_setup_theme', 'wowmall_setup' );

add_action( 'pre_get_posts', 'wowmall_set_image_sizes' );

add_action( 'pre_get_posts', 'wowmall_dynamic_options' );

add_filter( 'redux/options/wowmall_options/options', 'wowmall_dynamic_options' );

function wowmall_dynamic_options( $options ) {
	$filter = current_filter();
	if ( 'pre_get_posts' === $filter ) {
		global $wowmall_options;
	}
	else {
		if ( is_admin() ) {
			return $options;
		}
		$wowmall_options = $options;
	}
	unset( $filter );
	if ( empty( $wowmall_options['url_options'] ) ) {
		return $wowmall_options;
	}
	if ( ! is_user_logged_in() && ! empty( $wowmall_options['maintenance_mode'] ) ) {
		return $wowmall_options;
	}
	if ( ! empty( $_GET['is_front_page'] ) ) {
		add_filter( 'pre_option_page_on_front', 'wowmall_set_front_page' );
		add_filter( 'redirect_canonical', '__return_false' );
	}
	if ( ! empty( $_GET['wc_shop_layout'] ) && '2' === $_GET['wc_shop_layout'] ) {
		add_filter( 'pre_option_woocommerce_shop_page_display', 'wowmall_set_shop_page_collection' );
	}
	if ( ! empty( $wowmall_options ) && ! empty( $_GET ) ) {
		foreach ( $_GET as $key => $value ) {
			if ( array_key_exists( $key, $wowmall_options ) ) {
				$option = Redux::getField( 'wowmall_options', $key );
				$value  = sanitize_text_field( $value );
				if ( ! empty( $option ) ) {
					if ( ! empty( $option['options'] ) ) {
						$options = $option['options'];
						if ( array_key_exists( $value, $options ) ) {
							$wowmall_options[ $key ] = $value;
						}
					}
					elseif ( ! empty( $option['type'] ) && 'color' === $option['type'] ) {
						$wowmall_options[ $key ] = '#' . $value;
					}
					else {
						$wowmall_options[ $key ] = $value;
					}
				}

			}
		}
	}

	return $wowmall_options;
}

function wowmall_set_shop_page_collection() {
	return 'subcategories';
}

function wowmall_set_image_sizes() {

	$filter = current_filter();
	if ( 'pre_get_posts' === $filter && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}
	global $wowmall_options;

	if ( ! empty( $wowmall_options ) ) {

		foreach ( $wowmall_options as $option_name => $option_value ) {

			if ( 0 === strpos( $option_name, 'woo_img_size_' ) || 0 === strpos( $option_name, 'blog_img_size_' ) || 0 === strpos( $option_name, 'gallery_img_size_' ) ) {

				$crop = true;
				if ( in_array( '9999', $option_value ) ) {
					$crop = false;
				}
				switch ( $option_name ) {
					case 'woo_img_size_cart':
						add_image_size( 'shop_thumbnail', $option_value['width'], $option_value['height'], $crop );
						break;
					case 'woo_img_size_small':
						add_image_size( 'shop_catalog', $option_value['width'], $option_value['height'], $crop );
						break;
					case 'woo_img_size_single_2':
						add_image_size( 'shop_single', $option_value['width'], $option_value['height'], $crop );
						break;
					case 'blog_img_size_list':
						add_image_size( 'medium_large', $option_value['width'], $option_value['height'], $crop );
						break;
					case 'blog_img_size_single':
						add_image_size( 'large', $option_value['width'], $option_value['height'], $crop );
						break;
					case 'blog_img_size_small':
						add_image_size( 'thumbnail', $option_value['width'], $option_value['height'], $crop );
						break;
					case 'blog_img_size_grid':
						add_image_size( 'medium', $option_value['width'], $option_value['height'], $crop );
						set_post_thumbnail_size( $option_value['width'], $option_value['height'], $crop );
						break;
				}
				if ( 'blog_img_size_grid' !== $option_name ) {
					add_image_size( $option_name, $option_value['width'], $option_value['height'], $crop );
				}
				add_filter( 'woocommerce_get_image_size_' . $option_name, function ( $size ) use ( $option_name, $crop ) {

					global $wowmall_options;

					return array(
						'width'  => $wowmall_options[ $option_name ]['width'],
						'height' => $wowmall_options[ $option_name ]['height'],
						'crop'   => $crop,
					);
				} );
			}
		}
	}
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 * Priority 0 to make it available to lower priority callbacks.
 * @global int $content_width
 */
function wowmall_content_width() {
	$GLOBALS['content_width'] = 1136;
}

add_action( 'after_setup_theme', 'wowmall_content_width', 0 );

/**
 * Register widget area.
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wowmall_sidebars_register() {
	global $wowmall_options;

	require_once 'inc/widgets/class-wowmall-abstract-widget.php';
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'wowmall' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
	if ( wowmall()->is_woocommerce_activated() ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Shop Sidebar', 'wowmall' ),
			'id'            => 'sidebar-shop',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
		if ( ! empty( $wowmall_options['header_currency_enable'] ) ) {
			register_sidebar( array(
				'name'          => esc_html__( 'Top Panel Currency Switcher Sidebar', 'wowmall' ),
				'id'            => 'sidebar-currency',
				'description'   => 'Not for standard widgets',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<span class="hidden-xxl-down">',
				'after_title'   => '</span>',
			) );
		}

		if ( class_exists( 'WC_Widget_Price_Filter' ) ) {
			require_once 'inc/widgets/woo/wc-widget-price-filter.php';
			unregister_widget( 'WC_Widget_Price_Filter' );
			register_widget( 'Wowmall_WC_Widget_Price_Filter' );
		}
		if ( class_exists( 'WC_Widget_Layered_Nav' ) ) {
			require_once 'inc/widgets/woo/wc-widget-layered-nav.php';
			unregister_widget( 'WC_Widget_Layered_Nav' );
			register_widget( 'Wowmall_WC_Widget_Layered_Nav' );
		}
		if ( class_exists( 'WC_Widget_Recent_Reviews' ) ) {
			require_once 'inc/widgets/woo/wc-widget-recent-reviews.php';
			unregister_widget( 'WC_Widget_Recent_Reviews' );
			register_widget( 'Wowmall_WC_Widget_Recent_Reviews' );
		}
		if ( class_exists( 'WC_Widget_Rating_Filter' ) ) {
			require_once 'inc/widgets/woo/wc-widget-rating-filter.php';
			unregister_widget( 'WC_Widget_Rating_Filter' );
			register_widget( 'Wowmall_WC_Widget_Rating_Filter' );
		}
		require_once 'inc/widgets/woo/wc-widget-clear-all.php';
		register_widget( 'Wowmall_WC_Widget_Clear_All' );
	}
	if ( class_exists( 'WP_Widget_Recent_Posts' ) ) {
		require_once 'inc/widgets/class-wp-widget-recent-posts.php';
		unregister_widget( 'WP_Widget_Recent_Posts' );
		register_widget( 'Wowmall_Widget_Recent_Posts' );
	}
	if ( class_exists( 'WP_Widget_Recent_Comments' ) ) {
		require_once 'inc/widgets/class-wp-widget-recent-comments.php';
		unregister_widget( 'WP_Widget_Recent_Comments' );
		register_widget( 'Wowmall_Widget_Recent_Comments' );
	}
	if ( class_exists( 'WP_Widget_Calendar' ) ) {
		require_once 'inc/widgets/class-wp-widget-calendar.php';
		unregister_widget( 'WP_Widget_Calendar' );
		register_widget( 'Wowmall_Widget_Calendar' );
	}
	require_once 'inc/widgets/class-wp-widget-follow.php';
	register_widget( 'Wowmall_Widget_Follow' );

	require_once 'inc/widgets/class-wp-widget-instagram.php';
	register_widget( 'Wowmall_Widget_Instagram' );

	require_once 'inc/widgets/class-wp-widget-about.php';
	register_widget( 'Wowmall_Widget_About' );

	require_once 'inc/widgets/class-mc4wp-form-widget.php';
}

add_action( 'widgets_init', 'wowmall_sidebars_register' );

function wowmall_fonts() {

	$version = wowmall()->get_version();

	if ( ! wp_style_is( 'bsf-myfont', 'enqueued' ) ) {

		wp_enqueue_style( 'wowmall-myfont', WOWMALL_THEME_URI . '/assets/css/myfont.css', array(), $version );
	}
}

/**
 * Enqueue scripts and styles.
 */
function wowmall_scripts() {

	if ( is_admin() ) {
		return;
	}
	global $wowmall_options, $post;

	$version  = wowmall()->get_version();
	$min      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$min_path = empty( $min ) ? '' : '/min';

	$main_script_deps = array(
		'jquery',
		'jquery-throttle-debounce',
	);
	$theme_shortcodes = array(
		'wowmall_posts_carousel',
		'wowmall_brands',
		'wowmall_slider',
		'wowmall_gallery',
	);
	$color            = ! empty( $wowmall_options['accent_color_1'] ) ? $wowmall_options['accent_color_1'] : '#fc6f38';

	wp_register_style( 'swiper', WOWMALL_THEME_URI . '/assets/css/swiper.min.css', array(), '3.4.2' );

	wp_register_script( 'swiper', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/swiper.jquery' . $min . '.js', array( 'jquery' ), '3.4.2', true );

	wp_register_style( 'magnific-popup', WOWMALL_THEME_URI . '/assets/css/magnific-popup.min.css', array(), '1.1.0' );

	wp_register_script( 'magnific-popup', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/jquery.magnific-popup' . $min . '.js', array( 'jquery' ), '1.1.0', true );

	wp_register_script( 'jquery-throttle-debounce', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/jquery.ba-throttle-debounce' . $min . '.js', array( 'jquery' ), '1.1', true );

	wp_register_script( 'hc-sticky', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/jquery.hc-sticky' . $min . '.js', array( 'jquery' ), '1.2.43', true );
	wp_register_script( 'bootstrap-transition', WOWMALL_THEME_URI . '/assets/js/bootstrap' . $min_path . '/transition' . $min . '.js', array( 'jquery' ), '3.3.7', true );
	wp_register_script( 'bootstrap-collapse', WOWMALL_THEME_URI . '/assets/js/bootstrap' . $min_path . '/collapse' . $min . '.js', array(
		'jquery',
		'bootstrap-transition',
	), '3.3.7', true );

	wp_register_style( 'wowmall-wc-single', WOWMALL_THEME_URI . '/assets/css/single.css', array(), $version );

	if ( wp_is_mobile() || is_home() || is_archive() || is_search() || is_singular( 'post' ) ) {
		wp_enqueue_style( 'swiper' );
		$main_script_deps[] = 'swiper';
	}

	if ( is_home() || is_archive() || is_search() || is_singular( 'post' ) ) {
		if ( wowmall()->is_woocommerce_activated() ) {
			$main_script_deps[] = 'select2';
			$assets_path        = str_replace( array(
					'http:',
					'https:',
				), '', WC()->plugin_url() ) . '/assets/';
			wp_enqueue_style( 'select2', $assets_path . 'css/select2.css' );
		} else {
			$main_script_deps[] = 'jquery-ui-selectmenu';
		}
	}

	if ( is_page() ) {
		foreach ( $theme_shortcodes as $shortcode ) {
			if ( has_shortcode( $post->post_content, $shortcode ) ) {
				wp_enqueue_style( 'swiper' );
				if ( 'wowmall_gallery' === $shortcode ) {
					wp_enqueue_style( 'magnific-popup' );
				}
				$main_script_deps[] = 'swiper';
				break;
			}
		}
	}

	if ( wowmall()->is_woocommerce_activated() ) {

		$is_loop        = is_shop() || is_product_taxonomy();
		$wc_ajax_deps   = array(
			'jquery-ui-selectmenu',
			'jquery-cookie',
			'wowmall-theme-script',
			'woocommerce',
		);
		$enqueue_loop   = false;
		$enqueue_single = false;
		$wc_loop_deps   = array(
			'wowmall-theme-script',
		);
		$single_deps    = array(
			'bootstrap-collapse',
			'select2',
			'hc-sticky',
			'wowmall-theme-script',
		);

		if ( $is_loop ) {
			wp_enqueue_script( 'wowmall-wc-ajax', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/wc-ajax' . $min . '.js', $wc_ajax_deps, $version, true );
			wp_localize_script( 'wowmall-wc-ajax', 'wowmallWcAjax', array(
				'ajaxOrderby'    => true,
				'ajaxPagination' => true,
			) );
		}
		if ( is_page() ) {
			if ( has_shortcode( $post->post_content, 'wowmall_collection' ) ) {
				$enqueue_loop   = true;
				$wc_loop_deps[] = 'masonry';
			}
		}

		if ( $is_loop || is_product() || is_account_page() || is_cart() || wowmall()->page_has_wc_shortcode() ) {
			wp_enqueue_style( 'swiper' );
			wp_enqueue_style( 'magnific-popup' );
			$wc_shop_layout     = ! empty( $wowmall_options['wc_shop_layout'] ) ? $wowmall_options['wc_shop_layout'] : 1;
			$enqueue_loop       = true;
			$main_script_deps[] = 'swiper';
			$main_script_deps[] = 'magnific-popup';
			$main_script_deps[] = 'select2';
			$assets_path        = str_replace( array(
					'http:',
					'https:',
				), '', WC()->plugin_url() ) . '/assets/';
			wp_enqueue_style( 'select2', $assets_path . 'css/select2.css' );
			if ( ! isset( $wowmall_options['quick_view_enable'] ) || $wowmall_options['quick_view_enable'] ) {
				wp_enqueue_style( 'wowmall-wc-single' );
				$enqueue_single = true;
				wp_enqueue_script( 'wc-add-to-cart-variation' );
			}

			if ( '2' === $wc_shop_layout && is_shop() && ! is_search() ) {
				$wc_loop_deps[] = 'masonry';
			}
			if ( is_product_category() ) {
				$term         = get_queried_object();
				$display_type = get_term_meta( $term->term_id, 'display_type', true );

				if ( '' === $display_type ) {
					$display_type = get_option( 'woocommerce_category_archive_display' );
				}
				if ( 'subcategories' === $display_type ) {
					$wc_loop_deps[] = 'masonry';
				}
			}
		}
		if ( is_cart() ) {
			$enqueue_loop = true;
		}
		if ( $enqueue_loop ) {
			wp_enqueue_script( 'wowmall-wc-loop', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/wc-loop' . $min . '.js', $wc_loop_deps, $version, true );
			$wowmall_ajax       = WOWMALL_THEME_DIR . 'admin/ajax.php';
			$file_perms         = substr( sprintf( '%o', fileperms( $wowmall_ajax ) ), -4 );
			$shortinit_ajax_url = WOWMALL_THEME_URI . '/admin/ajax.php';
			if ( '0644' !== $file_perms ) {
				$shortinit_ajax_url = admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' );
			}

			wp_localize_script( 'wowmall-wc-loop', 'wowmallWcLoop', array(
				'added_to_cart'      => esc_html__( 'Added', 'wowmall' ),
				'thumbsHover'        => ! empty( $wowmall_options['wc_loop_thumb_swiper'] ) ? 1 : false,
				'effect'             => empty( $wowmall_options['wc_loop_thumb_effect'] ) ? 'fade' : $wowmall_options['wc_loop_thumb_effect'],
				'shortinit_ajax_url' => $shortinit_ajax_url,
			) );
		}
		if ( is_product() || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) {
			wp_enqueue_style( 'wowmall-wc-single' );
			$enqueue_single = true;
			$product        = wc_get_product( $post->ID );
			if ( $product instanceof WC_Product && $product->is_type( 'variable' ) ) {
				$single_deps[] = 'wc-add-to-cart-variation';
			}
			if ( ! empty( $wowmall_options['product_zoom'] ) && ! wp_is_mobile() ) {
				add_theme_support( 'wc-product-gallery-zoom' );
				$single_deps[] = 'zoom';
			}
			if ( ! wp_is_mobile() && ! empty( $wowmall_options['product_page_layout'] ) && '1' === $wowmall_options['product_page_layout'] ) {
				wp_register_script( 'wowmall-wc-single-product-gallery', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/wc-single-product-gallery' . $min . '.js', array(
					'wowmall-theme-script',
				), $version, true );
				$effect = ! empty( $wowmall_options['wc_single_swiper_effect'] ) ? $wowmall_options['wc_single_swiper_effect'] : 'slide';
				wp_localize_script( 'wowmall-wc-single-product-gallery', 'singleSwipeEffect', $effect );
				$single_deps[] = 'wowmall-wc-single-product-gallery';
			}
			wp_register_script( 'wowmall-wc-single-product-related', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/wc-single-product-related' . $min . '.js', array(
				'wowmall-theme-script',
			), $version, true );
			$single_deps[] = 'wowmall-wc-single-product-related';
			wp_register_script( 'single-product-lightbox', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/single-product-lightbox' . $min . '.js', array( 'wowmall-theme-script' ), $version, true );
			$single_deps[] = 'single-product-lightbox';
		}
		if ( $enqueue_single ) {
			if ( wp_script_is( 'wc-single-product', 'registered' ) ) {
				wp_deregister_script( 'wc-single-product' );
			}
			wp_register_script( 'wc-single-product', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/wc-single-product' . $min . '.js', $single_deps, $version, true );
			wp_localize_script( 'wc-single-product', 'wcSingleParams', array(
				'scroll_to_tab' => ! empty( $wowmall_options['product_scroll_to_tab'] ) ? 1 : false,
			) );
			wp_enqueue_script( 'wc-single-product' );
		}
		if ( is_cart() ) {
			wp_enqueue_script( 'wowmall-wc-cart', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/cart' . $min . '.js', array(
				'jquery-ui-selectmenu',
				'swiper',
				'wowmall-theme-script',
			), $version, true );
		}
		if ( is_checkout() ) {
			wp_enqueue_script( 'wowmall-wc-checkout', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/checkout' . $min . '.js', array(
				'jquery',
				'wowmall-theme-script',
			), $version, true );
		}
		if ( is_checkout() || is_cart() || is_account_page() ) {
			wp_enqueue_style( 'swiper' );
			wp_enqueue_style( 'magnific-popup' );
			wp_enqueue_style( 'wowmall-wc-cart', WOWMALL_THEME_URI . '/assets/css/cart.css', array(), $version );
			wp_enqueue_style( 'wowmall-wc-checkout', WOWMALL_THEME_URI . '/assets/css/checkout.css', array(), $version );
			wp_enqueue_style( 'wowmall-wc-account', WOWMALL_THEME_URI . '/assets/css/account.css', array(), $version );
		}
	}
	if ( ! empty( $wowmall_options['lazy'] ) ) {
		wp_register_script( 'wowmall-lazy-load', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/jquery.lazyload' . $min . '.js', array( 'jquery' ), '1.9.7', true );
		$main_script_deps[] = 'wowmall-lazy-load';
	}
	$main_script_deps = array_unique( $main_script_deps );
	wp_enqueue_script( 'wowmall-theme-script', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/theme-script' . $min . '.js', $main_script_deps, $version, true );

	wp_localize_script( 'wowmall-theme-script', 'wowmallParams', array(
		'ajax_url'               => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
		'preloader'              => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50%" cy="50%" r="40" stroke="' . $color . '" fill="none" stroke-width="5"><animate attributeType="XML" attributeName="stroke-dashoffset" dur="2s" repeatCount="indefinite" from="0" to="502"/><animate attributeName="stroke-dasharray" attributeType="XML" dur="2s" repeatCount="indefinite" values="150.6 100.4;1 250;150.6 100.4"/></circle></svg>',
		'ajax_search'            => ! empty ( $wowmall_options['header_search_ajax'] ),
		'ajax_search_min_length' => isset( $wowmall_options['ajax_search_min_length'] ) ? $wowmall_options['ajax_search_min_length'] : 3,
	) );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	if ( wp_is_mobile() ) {
		wp_enqueue_script( 'touch-swipe', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/jquery.touchSwipe' . $min . '.js', array( 'jquery' ), '1.6.18', true );
		wp_enqueue_script( 'wowmall-theme-script-mobile', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/mobile' . $min . '.js', array( 'wowmall-theme-script' ), $version, true );
		wp_localize_script( 'wowmall-theme-script-mobile', 'wowmallMobileParams', array(
			'readmore_text' => esc_html__( 'Read more', 'wowmall' ),
			'readless_text' => esc_html__( 'Read less', 'wowmall' ),
		) );
	}
}

function wowmall_main_styles() {
	global $is_edge, $is_IE;
	$version  = wowmall()->get_version();
	$min      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$min_path = empty( $min ) ? '' : '/min';
	wp_enqueue_style( 'wowmall-style', WOWMALL_THEME_URI . '/style.css', array(), $version );
	if ( $is_IE ) {
		wp_enqueue_style( 'wowmall-style-ie', WOWMALL_THEME_URI . '/assets/css/ie.css', array( 'wowmall-style' ), $version );
	}
	if ( $is_edge || $is_IE ) {
		wp_enqueue_style( 'wowmall-style-ie-all', WOWMALL_THEME_URI . '/assets/css/ie-all.css', array( 'wowmall-style' ), $version );
		wp_enqueue_script( 'smil-user', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/smil.user' . $min . '.js', array(), '0.3.0', true );
		wp_enqueue_script( 'wowmall-ie', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/ie' . $min . '.js', array( 'jquery' ), $version, true );
	}
}

function wowmall_set_front_page() {
	global $wp_query;
	$page_obj = $wp_query->get_queried_object();
	if ( $page_obj && ! empty ( $page_obj->ID ) ) {
		return $page_obj->ID;
	}

	return false;
}

function wowmall_includes() {

	add_action( 'wp_enqueue_scripts', 'wowmall_scripts' );

	add_action( 'wp_enqueue_scripts', 'wowmall_main_styles', 11 );

	require_once( WOWMALL_THEME_DIR . 'inc/walkers/main-menu-walker.php' );

	require_once( WOWMALL_THEME_DIR . 'inc/walkers/walker-social-media.php' );

	require_once( WOWMALL_THEME_DIR . 'inc/functions.php' );

	require_once( WOWMALL_THEME_DIR . 'inc/hooks.php' );

	/**
	 * Custom template tags for this theme.
	 */
	require_once( WOWMALL_THEME_DIR . 'inc/template-tags.php' );

	if ( wowmall()->is_woocommerce_activated() ) {
		require_once( WOWMALL_THEME_DIR . 'inc/wc-products-ajax.php' );
	}

}

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WowMallFunc' ) ) {

	class WowMallFunc {

		protected static $_instance = null;

		public static $menu_locations, $menu_items;

		public $processed_megamenus = array(), $custom_css, $include_ultimate_assets, $detect;

		public function __construct() {

			add_action( 'wp_enqueue_scripts', array(
				$this,
				'print_custom_css',
			), 1000 );

			add_action( 'wp_enqueue_scripts', array(
				$this,
				'print_ultimate_assets',
			), 100 );

			add_action( 'save_post', array(
				$this,
				'clear_megamenu_transients',
			) );
		}

		public function after_setup_theme() {

			if ( wowmall()->is_woocommerce_activated() ) {
				require_once( WOWMALL_THEME_DIR . 'inc/woo/hooks.php' );
			}

			if ( is_admin() && ! wp_doing_ajax() ) {
				return;
			}
			remove_action( 'wpcf7_init', 'wpcf7_add_form_tag_submit' );
			add_action( 'wpcf7_init', 'wowmall_wpcf7_add_form_tag_submit' );
		}

		public function aio_front_scripts() {

			if ( is_admin() || wp_is_mobile() ) {
				return;
			}
			$this->custom_css              = get_transient( 'wowmall_menu_custom_css' );
			$this->include_ultimate_assets = get_transient( 'wowmall_include_ultimate_assets' );

			if ( false === $this->custom_css || false === $this->include_ultimate_assets ) {

				$this->custom_css = array();

				if ( is_null( self::$menu_locations ) ) {
					self::$menu_locations = get_nav_menu_locations();
				}
				if ( is_array( self::$menu_locations ) && isset( self::$menu_locations['primary'] ) ) {
					if ( is_null( self::$menu_items ) ) {
						$menu = get_term( self::$menu_locations['primary'], 'nav_menu' );
						if ( is_wp_error( $menu ) ) {
							return;
						}
						$menu_id          = $menu->term_id;
						self::$menu_items = wp_get_nav_menu_items( $menu_id );
						unset( $menu, $menu_id );
					}
					if ( is_array( self::$menu_items ) ) {
						foreach ( self::$menu_items as $item ) {
							if ( empty( $item->menu_item_parent ) && ! empty( $item->wowmall_megamenu_page ) ) {
								$page_id = (int) $item->wowmall_megamenu_page;
								if ( 'page' === get_post_type( $page_id ) ) {
									$content = apply_filters( 'the_content', get_post_field( 'post_content', $page_id ) );
									if ( ! empty( $content ) ) {
										if ( ! in_array( $page_id, $this->processed_megamenus ) ) {
											$this->processed_megamenus[] = $page_id;
											if ( class_exists( 'Vc_Base' ) ) {
												$custom_css = get_post_meta( $page_id, '_wpb_shortcodes_custom_css', true );
												if ( ! empty( $custom_css ) ) {
													$this->custom_css[] = strip_tags( $custom_css );
												}
												unset( $custom_css );
											}
										}
									}
									unset( $content );
								}
								unset( $page_id );
								wp_reset_postdata();
							}
						}
						$this->custom_css = join( '', $this->custom_css );
						set_transient( 'wowmall_menu_custom_css', $this->custom_css );
						set_transient( 'wowmall_include_ultimate_assets', ! empty( $this->processed_megamenus ) );
					}
				}
			}
		}

		public function print_custom_css() {
			if ( ! empty( $this->custom_css ) ) {
				wp_add_inline_style( 'wowmall-style', $this->custom_css );
			}
		}

		public function print_ultimate_assets() {
			if ( ! empty( $this->include_ultimate_assets ) ) {
				wp_enqueue_script( 'ultimate-script' );
				wp_enqueue_style( 'ultimate-style-min' );
			}
		}

		public function clear_megamenu_transients() {
			delete_transient( 'wowmall_menu_custom_css' );
			delete_transient( 'wowmall_include_ultimate_assets' );
		}

		public function mobile_detect() {

			if ( is_null( $this->detect ) ) {

				if ( ! class_exists( 'Mobile_Detect' ) ) {

					require_once( WOWMALL_THEME_DIR . 'inc/Mobile_Detect.php' );
				}

				$this->detect = new Mobile_Detect;
			}

			return $this->detect;
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

if ( ! function_exists( 'wowmall_func' ) ) {
	function wowmall_func() {
		if ( method_exists( 'WowMallFunc', 'instance' ) ) {
			return WowMallFunc::instance();
		}

		return null;
	}
}

if ( wowmall()->is_woocommerce_activated() ) {
	require_once( WOWMALL_THEME_DIR . 'inc/woo/functions.php' );
}

function wowmall_add_preloader_styles() {
	global $wowmall_options;
	if ( ! empty( $wowmall_options['page_preloader'] ) ) {
		wp_add_inline_style( 'wowmall-style', "body.wowmall-page-preloader:before{content:'';width: 100%;height:100%;top:0;left:0;position:fixed;z-index:99999999;background:#fff;transition:opacity .5s ease, top 0s linear .5s}body.wowmall-page-preloader.preloaded:before{top:-100%;opacity:0}body.wowmall-page-preloader:after{content:'';border-radius:50%;width:90px;height:90px;position:fixed;border:5px solid transparent;left:50%;margin-top:-45px;margin-left:-45px;animation:wowmall-page-loader 1.1s infinite linear;z-index:999999999;transition:opacity .5s ease, top 0s linear .5s;top:50%;}body.wowmall-page-preloader.preloaded:after{top:-50%;opacity:0}@keyframes wowmall-page-loader{0%{transform:rotate(0deg)}100%{transform: rotate(360deg)}}" );
	}
}

function wowmall_enqueue_styles() {
	global $wowmall_options;
	if ( empty( $wowmall_options['header_sticky_color']['active'] ) ) {
		$wowmall_options['header_sticky_color']['active'] = '#222';
	}
	if ( empty( $wowmall_options['body_typography']['color'] ) ) {
		$wowmall_options['body_typography']['color'] = '#888';
	}
	if ( empty( $wowmall_options['cat_banner_height'] ) ) {
		$wowmall_options['cat_banner_height'] = 580;
	}
	if ( empty( $wowmall_options['cat_banner_over_background']['color'] ) ) {
		$wowmall_options['cat_banner_over_background']['color'] = '#f2f1f6';
	}
	if ( empty( $wowmall_options['cat_banner_over_background']['alpha'] ) ) {
		$wowmall_options['cat_banner_over_background']['alpha'] = .7;
	}
	if ( empty( $wowmall_options['cat_banner_over_background']['rgba'] ) ) {
		$wowmall_options['cat_banner_over_background']['rgba'] = 'rgba(242, 241, 246, 0.7)';
	}
	$top_panel_btns_color_hover   = empty( $wowmall_options['top_panel_btns_color']['hover'] ) ? Redux::getOption( 'wowmall_options', 'accent_color_2' ) : $wowmall_options['top_panel_btns_color']['hover'];
	$top_panel_btns_color_active  = empty( $wowmall_options['top_panel_btns_color']['active'] ) ? Redux::getOption( 'wowmall_options', 'accent_color_2' ) : $wowmall_options['top_panel_btns_color']['active'];
	$top_panel_btns_color_regular = empty( $wowmall_options['top_panel_btns_color']['regular'] ) ? '#fff' : $wowmall_options['top_panel_btns_color']['regular'];
	list( $r, $g, $b ) = sscanf( $wowmall_options['cat_banner_over_background']['color'], "#%02x%02x%02x" );
	$css = '.header-sticky-wrapper #primary-menu>li.current-menu-item>a{color:' . $wowmall_options['header_sticky_color']['active'] . '}.woocommerce ul.products li.product .price del,.price del,.woocommerce ul.product_list_widget li .widget-product-content .amount del{color:' . $wowmall_options['body_typography']['color'] . '}.term-description .term-description-col .term-description-outer{min-height:' . $wowmall_options['cat_banner_height'] . 'px}.term-description .term-description-col:after{background-image: linear-gradient(to right, ' . $wowmall_options['cat_banner_over_background']['rgba'] . ' 0%, rgba(' . $r . ', ' . $g . ', ' . $b . ', 0) 100%)}#content>.term-description{background-color:' . $wowmall_options['cat_banner_over_background']['color'] . '}.header-text ul li span[class*=myfont]{color:' . $top_panel_btns_color_regular . '}.header-tools-wrapper .header-tools .header-cart-wrapper.active > a,.header-tools-wrapper .header-tools .header-currency-wrapper.active > a,.header-layout-5 .wowmall-top-search-wrapper .wowmall-top-search.expanded button[type=submit],.header-layout-5 #primary-menu>li.current-menu-item>a{color:' . $top_panel_btns_color_active . '}.header-layout-5 .wowmall-top-search-wrapper .wowmall-top-search.expanded button[type=submit]:hover,.header-layout-5 #primary-menu>li:hover>a{color:' . $top_panel_btns_color_hover . '}';
	if ( ! empty( $wowmall_options['cat_banner_color'] ) && 'dark' !== $wowmall_options['cat_banner_color'] ) {

		$css .= '#content>.term-description,.term-description .term-description-col h2,.term-description .wc-loop-product-categories li a:not(:hover){color:#fff}';
	}
	if ( ! empty( $wowmall_options['extra_css'] ) ) {
		$css .= $wowmall_options['extra_css'];
	}
	echo '<style>' . $css . '</style>';
}

function wowmall_enqueue_extra_scripts() {
	global $wowmall_options;
	if ( ! empty( $wowmall_options['extra_js'] ) ) {
		echo '<script>' . $wowmall_options['extra_js'] . '</script>';
	}
}

function wowmall_get_site_icon_url( $url, $size ) {
	if ( 512 === $size && empty( $url ) ) {
		return true;
	}

	return $url;
}

function wowmall_site_icon_meta_tags( $meta_tags ) {
	if ( empty( $meta_tags ) || ( 1 === $meta_tags && is_customize_preview() ) ) {
		remove_filter( 'get_site_icon_url', 'wowmall_get_site_icon_url' );
		global $wowmall_options;
		$url = ! empty( $wowmall_options['favicon']['url'] ) ? $wowmall_options['favicon']['url'] : '';
		if ( ! empty( $url ) ) {
			$meta_tags = array(
				sprintf( '<link rel=icon href="%s" type=image/x-icon>', esc_attr( $url ) ),
				// need to check proper tag
			);
		}
		unset( $url );
	}

	return $meta_tags;
}

function wowmall_default_option_site_icon() {
	global $wowmall_options;
	$id = ! empty( $wowmall_options['favicon']['id'] ) ? $wowmall_options['favicon']['id'] : false;
	if ( ! $id && ! empty( $wowmall_options ) ) {
		foreach ( $wowmall_options as $option_name => $option ) {
			if ( false !== strpos( $option_name, 'favicon_' ) ) {
				$id = ! empty( $option['id'] ) ? $option['id'] : false;
				break;
			}
		}
	}

	return $id;
}

function wowmall_body_class( $classes ) {
	$classes = (array) $classes;
	if ( is_admin() ) {
		return $classes;
	}
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}
	if ( wp_is_mobile() ) {
		$classes[] = 'mobile';
	}
	else {
		$classes[] = 'desktop';
	}
	global $wowmall_options;
	if ( ( is_active_sidebar( 'sidebar-shop' ) && ! is_singular( 'product' ) ) && ! ( ! empty( $wowmall_options['blog_layout_type'] ) && 'list' !== $wowmall_options['blog_layout_type'] ) ) {
		$classes[] = 'with-sidebar';
	}
	if ( is_page() ) {
		global $post;
		if ( ! empty( $post->post_name ) ) {
			$classes[] = 'page-' . $post->post_name;
		}
	}
	if ( ( is_home() || is_archive() || is_search() ) && ! empty( $wowmall_options['blog_layout_type'] ) ) {
		$classes[] = 'blog-layout-' . $wowmall_options['blog_layout_type'];
	}
	if ( isset( $wowmall_options['lazy'] ) && $wowmall_options['lazy'] ) {
		$classes[] = 'lazy-enabled';
	}
	if ( isset( $wowmall_options['page_preloader'] ) && $wowmall_options['page_preloader'] ) {
		$classes[] = 'wowmall-page-preloader';
	}
	if ( isset( $wowmall_options['header_sticky_enable'] ) && $wowmall_options['header_sticky_enable'] ) {
		$classes[] = 'header-sticky-enable';
	}

	return array_unique( $classes );
}

function wowmall_comment_form_fields( $comment_fields ) {

	$comment_fields['comment'] = array_shift( $comment_fields );

	return $comment_fields;
}

function wowmall_wpcf7_add_form_tag_submit() {
	wpcf7_add_form_tag( 'submit', 'wowmall_wpcf7_submit_form_tag_handler' );
}

function wowmall_wpcf7_submit_form_tag_handler( $tag ) {
	$tag = new WPCF7_FormTag( $tag );

	$class = wpcf7_form_controls_class( $tag->type );

	$atts = array();

	$atts['class']    = $tag->get_class_option( $class );
	$atts['id']       = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $value ) ) {
		$value = esc_html__( 'Send', 'wowmall' );
	}

	$atts['type'] = 'submit';

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf( '<button %1$s><span class=wpcf7-submit-loader></span><span class=wpcf7-submit-text>%2$s</span></button>', $atts, $value );

	return $html;
}

function wowmall_excerpt_length() {

	global $post, $wowmall_options;

	if ( ! empty( $post->wowmall_in_widget ) && $post->wowmall_in_widget ) {
		return 25;
	}
	if ( ! empty( $wowmall_options['blog_layout_type'] ) && 'list' === $wowmall_options['blog_layout_type'] ) {
		return 35;
	}

	return 28;
}

function wowmall_excerpt_more() {
	return '';
}

function wowmall_post_format_link() {

	preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', make_clickable( get_the_content() ), $matches );

	$content_url = ! empty( $matches[1] ) ? esc_url_raw( $matches[1] ) : '';
	$url         = ! empty( $content_url ) ? $content_url : get_permalink();

	printf( '<a href="%1$s" class=post-format-link><span>%1$s</span></a>', esc_url( $url ) );
}

function wowmall_post_format_quote() {

	$format = get_post_format();
	if ( 'quote' === $format ) {

		// Catch links that are not wrapped in an '<a>' tag.
		preg_match( '/<blockquote[^>]*>(.*?)<\/blockquote>/is', get_the_content(), $matches );

		$quote = ! empty( $matches[1] ) ? wp_kses_post( $matches[1] ) : '';
		$quote = ! empty( $quote ) ? $quote : get_the_excerpt();

		preg_match( '/<cite[^>]*>.*?<\/cite>/im', $quote, $matches );

		$cite = '';

		if ( ! empty( $matches[0] ) ) {
			$cite  = $matches[0];
			$quote = str_replace( $cite, '', $quote );
		}

		printf( '<blockquote class=post-format-quote__quote><div class=post-format-quote__quote-text>%1$s</div> %2$s</blockquote>', $quote, $cite );
	}
}

function wowmall_post_format_audio() {
	$embeds = get_media_embedded_in_content( apply_filters( 'the_content', get_the_content() ), array( 'audio' ) );

	if ( empty( $embeds ) ) {
		return;
	}
	if ( false == preg_match( '/<audio[^>]*>(.*?)<\/audio>/', $embeds[0], $matches ) ) {
		return;
	}
	echo '' . $matches[0];
}

function wowmall_post_format_video() {

	$content = apply_filters( 'the_content', get_the_content() );
	$result  = false;

	$format = '<div class="entry-video embed-responsive embed-responsive-16by9" %1$s>%2$s</div>';

	if ( has_shortcode( $content, 'video' ) ) {
		$format = '%s';
	}

	/** This filter is documented in wp-includes/post-template.php */
	$types  = array(
		'video',
		'object',
		'embed',
		'iframe',
	);
	$embeds = get_media_embedded_in_content( $content, $types );

	if ( empty( $embeds ) ) {
		return;
	}

	foreach ( $types as $tag ) {
		if ( preg_match( "/<{$tag}[^>]*>.*?<\/{$tag}>/", $embeds[0], $matches ) ) {
			$result = $matches[0];
			break;
		}
	}

	if ( false === $result ) {
		return false;
	}

	$regex = array(
		'/width=[\'\"](\d+)[\'\"]/',
		'/height=[\'\"](\d+)[\'\"]/',
	);

	$replace = array(
		'width="600"',
		'height="400"',
	);

	$padding = '';

	global $wowmall_options;

	if ( ! is_single() && ! empty( $wowmall_options['blog_layout_type'] ) && 'grid' === $wowmall_options['blog_layout_type'] ) {
		if ( ! empty( $wowmall_options['blog_img_size_grid'] ) ) {
			$replace = array(
				'width=' . $wowmall_options['blog_img_size_grid']['width'],
				'height=' . $wowmall_options['blog_img_size_grid']['height'],
			);

			$padding = esc_attr( ' style="padding-top:' . 100 * $wowmall_options['blog_img_size_grid']['height'] / $wowmall_options['blog_img_size_grid']['width'] . '%"' );
		}
	}

	$result = preg_replace( $regex, $replace, $result );

	printf( $format, $padding, $result );
}

/**
 * Print HTML with a share buttons.
 * @since  1.0.0
 */
function wowmall_share_buttons() {

	/**
	 * Default social networks.
	 * %1$s - `id`
	 * %2$s - `type`
	 * %3$s - `url`
	 * %4$s - `title`
	 * %4$s - `summary`
	 * %5$s - `thumbnail`
	 */
	$networks = apply_filters( 'wowmall_sociail_share_btns', array(
		'facebook'    => array(
			'icon'      => 'myfont-facebook',
			'name'      => esc_html__( 'Facebook', 'wowmall' ),
			'share_url' => '//www.facebook.com/sharer/sharer.php?u=%3$s&t=%4$s',
		),
		'twitter'     => array(
			'icon'      => 'myfont-twitter',
			'name'      => esc_html__( 'Twitter', 'wowmall' ),
			'share_url' => '//twitter.com/intent/tweet?url=%3$s&text=%4$s',
		),
		'google-plus' => array(
			'icon'      => 'myfont-gplus',
			'name'      => esc_html__( 'Google+', 'wowmall' ),
			'share_url' => '//plus.google.com/share?url=%3$s',
		),
		'linkedin'    => array(
			'icon'      => 'myfont-linkedin',
			'name'      => esc_html__( 'LinkedIn', 'wowmall' ),
			'share_url' => '//www.linkedin.com/shareArticle?mini=true&url=%3$s&title=%4$s&summary=%5$s&source=%3$s',
		),
		'pinterest'   => array(
			'icon'      => 'myfont-pinterest-circled',
			'name'      => esc_html__( 'Pinterest', 'wowmall' ),
			'share_url' => '//www.pinterest.com/pin/create/button/?url=%3$s&description=%4$s&media=%6$s',
		),
	) );

	// Prepare a data for sharing.
	$id           = get_the_ID();
	$type         = get_post_type( $id );
	$url          = get_permalink( $id );
	$title        = get_the_title( $id );
	$summary      = get_the_excerpt();
	$thumbnail_id = get_post_thumbnail_id( $id );
	$thumbnail    = '';

	if ( ! empty( $thumbnail_id ) ) {
		$thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'full' );
		$thumbnail = $thumbnail[0];
	}

	$share_item_html = '<div class="share-btns__item %2$s-item"><a class=share-btns__link href="%1$s" target=_blank rel=nofollow title="%3$s"><i class="%4$s"></i><span class="share-btns__label screen-reader-text">%5$s</span></a></div>';
	$share_buttons   = '';

	foreach ( (array) $networks as $id => $network ) {

		if ( empty( $network['share_url'] ) ) {
			continue;
		}

		$share_url = sprintf( $network['share_url'], urlencode( $id ), urlencode( $type ), urlencode( $url ), urlencode( $title ), urlencode( $summary ), urlencode( $thumbnail ) );

		$share_buttons .= sprintf( $share_item_html, esc_url( $share_url ), sanitize_html_class( $id ), esc_html__( 'Share on ', 'wowmall' ) . $network['name'], esc_attr( $network['icon'] ), esc_attr( $network['name'] ) );

	}

	printf( '<button class=entry-share-btns_holder></button><div class=share-btns__list><div class=share-btns__list-inner>%s</div></div>', $share_buttons );
}

function wowmall_post_gallery( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post = get_post( $post_id );
	$ids  = array();

	if ( preg_match_all( '/' . get_shortcode_regex( array( 'gallery' ) ) . '/s', $post->post_content, $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $shortcode ) {

			$shortcode_attrs = shortcode_parse_atts( $shortcode[3] );
			if ( ! is_array( $shortcode_attrs ) ) {
				$shortcode_attrs = array();
			}

			if ( ! empty( $shortcode_attrs['ids'] ) ) {
				$ids = explode( ',', $shortcode_attrs['ids'] );
			}
		}
	}

	if ( empty( $ids ) ) {

		global $wpdb;

		$post_gallery = array_unique( get_post_galleries( $post_id ) );
		$post_gallery = join( '', $post_gallery );

		preg_match_all( '/<img.+?>/', $post_gallery, $imgs );

		$imgs = array_unique( $imgs[0] );

		if ( ! empty( $imgs ) ) {
			foreach ( $imgs as $img ) {
				preg_match_all( '/src=([\'"])(.+?)\1/', $img, $match, PREG_SET_ORDER );
				if ( isset( $match[1] ) ) {
					$image_src = $match[1][2];
				}
				else {
					$image_src = $match[0][2];
				}
				$image_src = preg_replace( '/^(.+)(-\d+x\d+)(\..+)$/', '$1$3', $image_src );
				$ids[]     = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s", $image_src ) );
			}
		}
	}

	$post_gallery = array_unique( $ids );

	// If can't try to catch images inserted into post.
	if ( ! $post_gallery ) {
		$content = get_the_content();

		// Gets images from content.
		preg_match_all( '/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $matches );

		$post_gallery = false;

		if ( ! empty( $matches[1] ) ) {

			$result = array();

			for ( $i = 0; $i < 15; $i++ ) {

				if ( empty( $matches[1][ $i ] ) ) {
					continue;
				}
				$image_src = esc_url( $matches[1][ $i ] );
				$image_src = preg_replace( '/^(.+)(-\d+x\d+)(\..+)$/', '$1$3', $image_src );

				// Try to get current image ID.
				$id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s", $image_src ) );

				if ( ! $id ) {
					$result[] = $image_src;
				}
				else {
					$result[] = (int) $id;
				}
			}
			$post_gallery = $result;
		}
	}

	// And if not find any images - try to get images attached to post.
	if ( ! $post_gallery || empty( $post_gallery ) ) {

		$attachments = get_children( array(
			'post_parent'    => $post_id,
			'posts_per_page' => 3,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
		) );

		if ( $attachments && is_array( $attachments ) ) {
			$post_gallery = array_keys( $attachments );
		}
	}
	if ( is_array( $post_gallery ) ) {
		$post_gallery = array_unique( $post_gallery );
	}

	return $post_gallery;
}

function wowmall_post_format_gallery() {

	$post_gallery = wowmall_post_gallery();

	if ( empty( $post_gallery ) ) {
		return;
	}

	$args = array(
		'class'     => 'swiper',
		'slider_id' => uniqid(),
		'size'      => 'post-thumbnail',
	);

	global $post, $wowmall_options;

	if ( ! empty( $wowmall_options['blog_layout_type'] ) && 'grid' !== $wowmall_options['blog_layout_type'] ) {
		$args['size'] = 'blog_img_size_' . $wowmall_options['blog_layout_type'];
	}

	if ( is_single() ) {
		$args['size'] = 'blog_img_size_single';
	}

	if ( ! empty( $post->is_related ) && $post->is_related ) {
		$args['size'] = 'blog_img_size_related';
	}

	$css = array(
		'container' => $args['class'] . '-container',
		'wrapper'   => $args['class'] . '-wrapper',
		'slide'     => $args['class'] . '-slide',
		'button'    => $args['class'] . '-button ' . $args['class'] . '-button-',
	);

	$items = array();

	foreach ( $post_gallery as $img ) {

		if ( 0 < intval( $img ) ) {
			$img = wp_get_attachment_image( $img, $args['size'] );
		}
		$items[] = sprintf( '<div class="%2$s">%1$s</div>', $img, $css['slide'] );
	}

	$items  = implode( "\r\n", $items );
	$slider = sprintf( '<div class="%2$s">%1$s</div>', $items, esc_attr( $css['wrapper'] ) );

	$slider_id = wowmall_prepare_atts( array(
		'data-id' => $args['slider_id'],
	) );

	foreach ( array(
		          'prev',
		          'next',
	          ) as $button ) {
		$slider .= sprintf( '<div class="%1$s" %2$s></div>', esc_attr( $css['button'] . $button ), $slider_id );
	}

	$result = sprintf( '<div class="%2$s" %3$s>%1$s</div>', $slider, esc_attr( $css['container'] ), $slider_id );

	if ( ! is_single() || $post->is_related ) {
		$result = '<a href="' . esc_url( get_permalink() ) . '">' . $result . '</a>';
	}

	$theme    = wp_get_theme();
	$version  = $theme->get( 'Version' );
	$min      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$min_path = empty( $min ) ? '' : '/min';

	wp_enqueue_script( 'wowmall-post-gallery', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/post-gallery' . $min . '.js', array(
		'jquery',
		'swiper',
	), $version, true );

	echo '<div class=post-gallery-wrapper>' . $result . '</div>';
}

function wowmall_prepare_atts( $atts ) {

	$result = '';

	if ( empty( $atts ) ) {
		return '';
	}
	foreach ( $atts as $attr => $value ) {
		$result .= ' ' . $attr . '=\'' . esc_attr( $value ) . '\'';
	}

	return $result;
}

function wowmall_post_format_image() {

	global $wpdb, $post, $wowmall_options;

	$content = get_the_content();

	if ( is_single() ) {
		preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
		$image = false;
		if ( ! empty( $matches ) ) {
			foreach ( $matches as $shortcode ) {
				if ( 'caption' === $shortcode[2] ) {
					$image = apply_filters( 'the_content', $shortcode[0] );
					break;
				}
			}
		}
		if ( $image ) {
			echo '<div class=>' . $image . '</div>';

			return;
		}
	}

	// Gets images from content.
	preg_match( '/<img.+?>/', $content, $matches );

	if ( ! empty( $matches[0] ) ) {
		preg_match( '/src=([\'"])(.+?)\1/', $matches[0], $match );

		$image_src = esc_url( $match[2] );
		$image_src = preg_replace( '/^(.+)(-\d+x\d+)(\..+)$/', '$1$3', $image_src );
		$image_src = explode( '?', $image_src );
		$image_src = $image_src[0];

		$size = 'post-thumbnail';

		if ( ! empty( $wowmall_options['blog_layout_type'] ) && 'grid' !== $wowmall_options['blog_layout_type'] ) {
			$size = 'blog_img_size_' . $wowmall_options['blog_layout_type'];
		}

		if ( is_single() ) {
			$size = 'blog_img_size_single';
		}

		if ( ! empty( $post->is_related ) && $post->is_related ) {
			$size = 'blog_img_size_related';
		}

		// Try to get current image ID.
		$id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s", $image_src ) );

		if ( ! $id ) {
			$result = '<img src="' . $image_src . '" alt="">';
		}
		else {
			$result = wp_get_attachment_image( $id, $size );
		}
		if ( ! is_single() || $post->is_related ) {
			$result = '<a href="' . esc_url( get_permalink() ) . '">' . $result . '</a>';
		}

		echo '<div class=entry-thumb>' . $result . '</div>';
	}
}

function wowmall_post_link( $output, $format, $link, $post, $adjacent ) {

	if ( empty( $post ) ) {
		return $output;
	}
	$id = $post->ID;

	if ( 'post' !== get_post_type( $id ) ) {
		return $output;
	}
	$html = '<div class=post-content-wrap>';
	if ( has_post_thumbnail( $id ) ) {
		$html .= '<div class=post-thumb>' . get_the_post_thumbnail( $id, 'blog_img_size_small' ) . '</div>';
	}
	else {
		$gallery = wowmall_post_gallery( $id );
		if ( ! empty( $gallery ) ) {
			$img  = $gallery[0];
			$html .= '<div class=post-thumb>' . wp_get_attachment_image( $img, 'blog_img_size_small' ) . '</div>';
		}
	}
	$meta              = array();
	$meta['cats']      = '<ul class=post-cats><li>' . join( '</li><li>', wp_get_post_categories( $id, array( 'fields' => 'names' ) ) ) . '</li></ul>';
	$meta['byline']    = '<span class="author vcard">' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . '</span>';
	$time_string       = sprintf( '<time class="entry-date published" datetime="%1$s">%2$s</time>', esc_attr( get_the_date( 'c', $id ) ), esc_html( get_the_date( null, $id ) ) );
	$meta['posted_on'] = '<span class=posted-on>' . $time_string . '</span>';

	$html .= '<div class=post-content><div class=post-meta>' . join( '', $meta ) . '</div>';
	$html .= '<h6>' . $post->post_title . '</h6></div></div>';
	$html .= 'previous' === $adjacent ? '<span class=post-nav-navigator><span class=myfont-left-open-big></span>' . esc_html__( 'Prev Post', 'wowmall' ) . '</span>' : '<span class=post-nav-navigator>' . esc_html__( 'Next Post', 'wowmall' ) . '<span class=myfont-right-open-big></span></span>';

	$rel    = 'previous' === $adjacent ? 'prev' : 'next';
	$link   = '<a href="' . esc_url( get_permalink( $post ) ) . '" rel="' . esc_attr( $rel ) . '">' . $html . '</a>';
	$output = str_replace( '%link', $link, $format );

	return $output;
}

function wowmall_navigation_template() {

	return '
	<nav class="navigation %1$s">
		<h2 class=screen-reader-text>%2$s</h2>
		<div class=nav-links>%3$s</div>
	</nav>
	';
}

function wowmall_add_newsletter_popup() {
	if ( ! empty( $_COOKIE['wowmall-dont-show-subscribe-popup'] ) && $_COOKIE['wowmall-dont-show-subscribe-popup'] ) {
		return;
	}
	if ( ! empty( $_COOKIE['wowmall-dont-show-subscribe-popup-in-current-session'] ) && $_COOKIE['wowmall-dont-show-subscribe-popup-in-current-session'] ) {
		return;
	}
	if ( ! function_exists( '_mc4wp_load_plugin' ) ) {
		return;
	}
	global $wowmall_options;
	if ( empty( $wowmall_options['popup'] ) ) {
		return;
	}
	$min      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$min_path = empty( $min ) ? '' : '/min';

	wp_enqueue_style( 'magnific-popup' );

	if ( ! wp_script_is( 'jquery-cookie', 'registered' ) ) {
		wp_register_script( 'jquery-cookie', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/jquery.cookie' . $min . '.js', array(
			'jquery',
		), '1.4.1', true );
	}

	wp_enqueue_script( 'wowmall-subscribe-popup', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/wowmall-subscribe-popup' . $min . '.js', array(
		'jquery-cookie',
		'magnific-popup',
		'wowmall-theme-script',
	), wowmall()->get_version(), true );
	wp_localize_script( 'wowmall-subscribe-popup', 'wowmallSubscribePopup', array( 'delay' => ! empty( $wowmall_options['popup_delay'] ) ? (int) $wowmall_options['popup_delay'] : 2000 ) );
}

function wowmall_wp_setup_nav_menu_item( $item ) {
	$item->wowmall_megamenu_page = get_post_meta( $item->ID, '_menu_item_wowmall_megamenu_page', true );

	return $item;
}

function wowmall_enqueue_gallery_styles() {
	wp_enqueue_style( 'swiper' );
	wp_enqueue_style( 'magnific-popup' );
}

function wowmall_enqueue_gallery_assets() {
	$version  = wowmall()->get_version();
	$min      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$min_path = empty( $min ) ? '' : '/min';

	wp_enqueue_script( 'magnific-popup' );
	wp_enqueue_script( 'swiper' );

	wp_enqueue_script( 'wowmall-gallery', WOWMALL_THEME_URI . '/assets/js' . $min_path . '/gallery' . $min . '.js', array( 'wowmall-theme-script' ), $version, true );

	$magnific_params = array();

	if ( ! wp_is_mobile() ) {

		global $wp_query;
		$posts  = $wp_query;
		$thumbs = array();
		while ( $posts->have_posts() ) {

			$posts->the_post();

			if ( has_post_thumbnail() ) {
				$thumbs[] = '<img src="' . esc_url( wp_get_attachment_image_url( get_post_thumbnail_id(), 'gallery_img_size_lightbox_thumb' ) ) . '" class=mfp-prevent-close>';
			}
		}
		$magnific_params['thumbs_swiper'] = false;

		if ( 1 < count( $thumbs ) ) {

			$magnific_params['thumbs_swiper'] = '<div class="swiper-container mfp-prevent-close" id=mfp-swiper><div class="swiper-wrapper mfp-prevent-close"><div class="swiper-slide mfp-prevent-close">' . join( '</div><div class="swiper-slide mfp-prevent-close">', $thumbs ) . '</div></div><div class="swiper-scrollbar mfp-prevent-close"></div></div>';
		}
	}
	wp_localize_script( 'wowmall-gallery', 'wowmall_gallery', $magnific_params );
}

function wowmall_generate_tag_cloud( $return, $tags, $args ) {

	if ( isset( $args['topic_count_text'] ) ) {
		// First look for nooped plural support via topic_count_text.
		$translate_nooped_plural = $args['topic_count_text'];
	}
	elseif ( ! empty( $args['topic_count_text_callback'] ) ) {
		// Look for the alternative callback style. Ignore the previous default.
		if ( $args['topic_count_text_callback'] === 'default_topic_count_text' ) {
			$translate_nooped_plural = _n_noop( '%s topic', '%s topics', 'wowmall' );
		}
		else {
			$translate_nooped_plural = false;
		}
	}
	elseif ( isset( $args['single_text'] ) && isset( $args['multiple_text'] ) ) {
		// If no callback exists, look for the old-style single_text and multiple_text arguments.
		$translate_nooped_plural = _n_noop( $args['single_text'], $args['multiple_text'], 'wowmall' );
	}
	else {
		// This is the default for when no callback, plural, or argument is passed in.
		$translate_nooped_plural = _n_noop( '%s topic', '%s topics', 'wowmall' );
	}

	$real_counts = array(); // For the alt tag
	foreach ( (array) $tags as $key => $tag ) {
		$real_counts[ $key ] = $tag->count;
	}

	$tags_data = array();
	foreach ( $tags as $key => $tag ) {
		$tag_id     = isset( $tag->id ) ? $tag->id : $key;
		$real_count = $real_counts[ $key ];

		if ( $translate_nooped_plural ) {
			$title = sprintf( translate_nooped_plural( $translate_nooped_plural, $real_count ), number_format_i18n( $real_count ) );
		}
		else {
			$title = call_user_func( $args['topic_count_text_callback'], $real_count, $tag, $args );
		}
		$tags_data[] = array(
			'id'         => $tag_id,
			'url'        => '#' != $tag->link ? $tag->link : '#',
			'role'       => '#' != $tag->link ? '' : ' role=button',
			'name'       => $tag->name,
			'title'      => $title,
			'slug'       => $tag->slug,
			'real_count' => $real_count,
			'class'      => 'tag-link-' . $tag_id,
		);
	}

	/**
	 * Filters the data used to generate the tag cloud.
	 * @since 4.3.0
	 *
	 * @param array $tags_data An array of term data for term used to generate the tag cloud.
	 */
	$tags_data = apply_filters( 'wp_generate_tag_cloud_data', $tags_data );
	$a         = array();

	// generate the output links array
	foreach ( $tags_data as $key => $tag_data ) {
		$a[] = "<a href='" . esc_url( $tag_data['url'] ) . "'" . $tag_data['role'] . " title='" . esc_attr( $tag_data['title'] ) . "'>" . esc_html( $tag_data['name'] ) . "</a>";
	}

	switch ( $args['format'] ) {
		case 'array' :
			$return =& $a;
			break;
		case 'list' :
			$return = "<ul class=wp-tag-cloud><li>";
			$return .= join( "</li><li>", $a );
			$return .= "</li></ul>";
			break;
		default :
			$return = join( $args['separator'], $a );
			break;
	}

	return $return;
}

function wowmall_shortcode_atts_vc_progress_bar( $out ) {
	$values       = $out['values'];
	$values_array = json_decode( urldecode( $values ), true );
	foreach ( $values_array as $key => $value ) {
		if ( ! empty( $value['label'] ) ) {
			$values_array[ $key ]['label'] = '<span class=vc_label_inner>' . $value['label'] . '</span>';
		}
	}
	$out['values'] = urlencode( json_encode( $values_array ) );

	return $out;
}

function wowmall_get_search_form() {
	$form = '<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
		<label class=screen-reader-text for=search-field>' . _x( 'Search for:', 'label', 'wowmall' ) . '</label>
		<input type="search" class="search-field" placeholder="' . esc_attr_x( 'Search&hellip;', 'placeholder', 'wowmall' ) . '" value="' . get_search_query() . '" name="s">
		<button type=submit class=search-submit><span class=myfont-search-circled></span></button>
	</form>';

	return $form;
}

function wowmall_the_password_form() {
	$post  = get_post();
	$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
	$form  = '<div class=post-password-form-wrapper><p>' . esc_html__( 'This content is password protected. To view it please enter your password below:', 'wowmall' ) . '</p><form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
	<label for=' . esc_attr( $label ) . '>' . esc_html__( 'Password:', 'wowmall' ) . '</label><input name="post_password" id=' . esc_attr( $label ) . ' type="password" size="20"><input type="submit" class="btn btn-sm btn-dark" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form', 'wowmall' ) . '"></form></div>';

	return $form;
}

function wowmall_exclude_pages_from_search( $q ) {
	if ( $q->is_search ) {
		$post_type = $q->get( 'post_type' );
		if ( empty( $post_type ) ) {
			$q->set( 'post_type', array(
				'post',
				'product',
			) );
		}
	}

	return $q;
}

function wowmall_monster_widget_config( $widgets ) {
	$new_widgets = array(
		'WP_Widget_Calendar'        => 'Wowmall_Widget_Calendar',
		'WP_Widget_Recent_Comments' => 'Wowmall_Widget_Recent_Comments',
		'WP_Widget_Recent_Posts'    => 'Wowmall_Widget_Recent_Posts',
	);

	return wowmall_update_onser_cofig( $widgets, $new_widgets );
}

function wowmall_wc_monster_widget_config( $widgets ) {
	$new_widgets = array(
		'WC_Widget_Price_Filter'   => 'Wowmall_WC_Widget_Price_Filter',
		'WC_Widget_Layered_Nav'    => 'Wowmall_WC_Widget_Layered_Nav',
		'WC_Widget_Recent_Reviews' => 'Wowmall_WC_Widget_Recent_Reviews',
		'WC_Widget_Rating_Filter'  => 'Wowmall_WC_Widget_Rating_Filter',
	);

	return wowmall_update_onser_cofig( $widgets, $new_widgets );
}

function wowmall_update_onser_cofig( $widgets, $new_widgets ) {
	foreach ( $widgets as $key => $widget ) {
		foreach ( $new_widgets as $old_widget => $new_widget ) {
			if ( in_array( $old_widget, $widget ) ) {
				$widgets[] = array(
					$new_widget,
					$widget[1],
				);
				unset( $widgets[ $key ] );
			}
		}
	}

	return $widgets;
}

if ( ! function_exists( 'wowmall_share_meta_tags' ) ) {
	function wowmall_share_meta_tags() {
		global $post, $wowmall_options;
		if ( isset( $wowmall_options['is_product_share_enable'] ) && ! $wowmall_options['is_product_share_enable'] ) {
			return;
		}
		if ( is_singular( 'product' ) ) {
			$excerpt = trim( preg_replace( '/\s\s+/', '', strip_tags( apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ) ) );
			$size    = 'woo_img_size_single_2';
		}
		else {
			if ( is_single() ) {
				ob_start();
				the_excerpt();
				$excerpt = trim( preg_replace( '/\s\s+/', '', strip_tags( ob_get_clean() ) ) );
				$size    = 'blog_img_size_single';
			}
		}
		if ( is_singular( 'product' ) || is_single() ) {
			echo '<meta property=og:title content="' . esc_attr( get_the_title() ) . '">' . "\n";
			$thumb_id = get_post_thumbnail_id();
			if ( (bool) $thumb_id ) {
				$img_src = wp_get_attachment_image_src( $thumb_id, $size );
				echo '<meta property=og:image:url content="' . $img_src[0] . '">' . "\n";
				echo '<meta property=og:image:width content="' . $img_src[1] . '">' . "\n";
				echo '<meta property=og:image:height content="' . $img_src[2] . '">' . "\n";
			}
		}
		if ( ! empty( $excerpt ) ) {
			echo '<meta property=og:description content="' . $excerpt . '">' . "\n";
		}
	}
}
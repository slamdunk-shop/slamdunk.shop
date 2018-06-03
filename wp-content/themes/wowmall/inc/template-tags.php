<?php
/**
 * Custom template tags for this theme.
 * Eventually, some of the functionality here could be replaced by core features.
 * @package Wowmall
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WowMallTags' ) ) {

	class WowMallTags {

		protected static $_instance = null;

		public static $menu_outputs, $wc_account_urls;

		public $wowmall_options;

		public function __construct() {

			global $wowmall_options;
			$this->wowmall_options = $wowmall_options;
			self::$wc_account_urls = get_transient( 'wowmall_wc_account_urls' );

			add_action( 'save_post', array(
				$this,
				'reset_transients',
			) );

			add_action( 'trashed_post', array(
				$this,
				'reset_transients',
			) );

			add_action( 'woocommerce_update_options', array(
				$this,
				'reset_transients',
			) );

			add_action( 'after_switch_theme', array(
				$this,
				'reset_transients',
			) );
		}

		public function reset_transients() {
			delete_transient( 'wowmall_wc_account_urls' );
		}

		public function nav( $menu_id = 'primary-menu' ) {

			if ( is_null( self::$menu_outputs ) ) {
				ob_start();
				wp_nav_menu( array(
					'container'       => 'nav',
					'theme_location'  => 'primary',
					'container_class' => 'main-menu',
					'menu_id'         => $menu_id,
					'items_wrap'      => '<ul id=%1$s class=%2$s>%3$s</ul>',
					'walker'          => new WowmallMainMenuWalker,
				) );
				self::$menu_outputs[$menu_id] = ob_get_clean();
			}
			echo self::$menu_outputs[$menu_id];
		}

		public function orders() {
			if ( empty( $this->wowmall_options['header_orders_enable'] ) || ! wowmall()->is_woocommerce_activated() ) {
				return;
			}
			if ( ! is_array( self::$wc_account_urls ) ) {
				self::$wc_account_urls = array();
			}
			if ( ! isset( self::$wc_account_urls['orders'] ) ) {
				self::$wc_account_urls['orders'] = esc_url( wc_get_account_endpoint_url( 'orders' ) );
			}
			set_transient( 'wowmall_wc_account_urls', self::$wc_account_urls );
			$url = self::$wc_account_urls['orders'];
			?>
			<a class=orders-link href="<?php echo esc_attr( $url ); ?>">
				<?php if ( wp_is_mobile() ) { ?>
					<span class=wowmall-header-tools-label>
				<?php esc_html_e( 'Orders', 'wowmall' ); ?>
			</span>
				<?php } ?>
			</a>
			<?php
			unset( $url );
		}

		public function account() {
			if ( empty( $this->wowmall_options['header_account_enable'] ) ) {
				return;
			}
			$label = esc_html__( 'Login \ Register', 'wowmall' );
			$class = '';
			if ( is_user_logged_in() ) {
				if ( wowmall()->is_woocommerce_activated() ) {
					$class = ' logged_in';
					$label = esc_html__( 'My Account', 'wowmall' );
				}
				else {
					return;
				}
			}
			if ( wowmall()->is_woocommerce_activated() ) {
				if ( ! is_array( self::$wc_account_urls ) ) {
					self::$wc_account_urls = array();
				}
				if ( ! isset( self::$wc_account_urls['dashboard'] ) ) {
					self::$wc_account_urls['dashboard'] = esc_url( wc_get_account_endpoint_url( 'dashboard' ) );
					set_transient( 'wowmall_wc_account_urls', self::$wc_account_urls );
				}
				$url = self::$wc_account_urls['dashboard'];
			}
			else {
				$url = wp_login_url();
			}
			?>
			<a class="myaccount-link<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $url ); ?>">
				<?php if ( wp_is_mobile() ) { ?>
					<span class=wowmall-header-tools-label>
				<?php echo '' . $label; ?>
				</span>
				<?php } ?>
			</a>
			<?php
			unset( $url, $class, $label );
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

if ( ! function_exists( 'wowmall_tags' ) ) {
	function wowmall_tags() {
		if ( method_exists( 'WowMallTags', 'instance' ) ) {
			return WowMallTags::instance();
		}

		return null;
	}
}

wowmall_tags();

function wowmall_mobile_nav() {
	echo '<nav class=navbar>';
	wowmall_mobile_menu_button();
	echo '<div id=mobile-menu-wrapper>';
	wowmall_top_search_mobile();
	wp_nav_menu( array(
		'theme_location'  => 'primary',
		'container_class' => 'main-menu',
		'menu_id'         => 'primary-menu-mobile',
		'items_wrap'      => '<ul id=%1$s class=%2$s>%3$s</ul>',
		'walker'          => new WowmallMainMenuWalker,
	) ); ?>
	<div class=header-tools>
		<?php wowmall_tags()->orders();
		wowmall_compare();
		wowmall_wishlist();
		wowmall_tags()->account(); ?>
	</div>
	<?php wowmall_currency();
	echo '</div></nav>';
}

function wowmall_mobile_menu_button() {
	?>
	<button id=mobile-menu-open>
		<span class=menu-text><?php esc_html_e( 'Menu', 'wowmall' ) ?></span>
		<span class=myfont-menu></span>
	</button>
	<button id=mobile-menu-close>
		<span class=myfont-cancel-1></span>
	</button>
	<?php
}

function wowmall_social_nav() { ?>
	<div class=social-media-profiles-menu>
		<?php wp_nav_menu( array(
			'theme_location' => 'social',
			'items_wrap'     => '%3$s',
			'container'      => false,
			'walker'         => new Walker_Social_Media,
			'fallback_cb'    => '__return_empty_string',
		) ); ?>
	</div>
<?php }

/**
 * Display the header logo.
 * @since  1.3.3
 * @return void
 */
function wowmall_get_logo( $in_footer = false, $mobile = false, $in_stiky = false ) {
	$tag = 'div';
	if ( ( is_front_page() ) && ! $in_footer && ! $in_stiky ) {
		$tag = 'h1';
	}
	global $wowmall_options;

	if ( isset( $wowmall_options['header_logo_tag'] ) && 'div' === $wowmall_options['header_logo_tag'] ) {
		$tag = 'div';
	}

	$header_layout = empty( $wowmall_options['header_layout'] ) ? '1' : $wowmall_options['header_layout'];

	if ( $in_footer ) {
		$logo = array(
			! empty( $wowmall_options['footer_logo'] ) ? $wowmall_options['footer_logo'] : '',
			'2x' => ! empty( $wowmall_options['footer_logo_2x'] ) ? $wowmall_options['footer_logo_2x'] : '',
			'3x' => ! empty( $wowmall_options['footer_logo_3x'] ) ? $wowmall_options['footer_logo_3x'] : '',
		);
	}
	elseif ( $mobile ) {
		$logo = array(
			! empty( $wowmall_options['header_logo_mobile'] ) ? $wowmall_options['header_logo_mobile'] : '',
			'2x' => ! empty( $wowmall_options['header_logo_mobile_2x'] ) ? $wowmall_options['header_logo_mobile_2x'] : '',
			'3x' => ! empty( $wowmall_options['header_logo_mobile_3x'] ) ? $wowmall_options['header_logo_mobile_3x'] : '',
		);
	}
	else {
		$logo = array(
			! empty( $wowmall_options['header_logo'] ) ? $wowmall_options['header_logo'] : '',
			'2x' => ! empty( $wowmall_options['header_logo_2x'] ) ? $wowmall_options['header_logo_2x'] : '',
			'3x' => ! empty( $wowmall_options['header_logo_3x'] ) ? $wowmall_options['header_logo_3x'] : '',
		);
		if ( '3' === $header_layout && ! $in_stiky ) {
			$logo = array(
				! empty( $wowmall_options['header_logo_alt'] ) ? $wowmall_options['header_logo_alt'] : '',
				'2x' => ! empty( $wowmall_options['header_logo_alt_2x'] ) ? $wowmall_options['header_logo_alt_2x'] : '',
				'3x' => ! empty( $wowmall_options['header_logo_alt_3x'] ) ? $wowmall_options['header_logo_alt_3x'] : '',
			);
		}
	}

	foreach ( array(
		          0,
		          '2x',
		          '3x',
	          ) as $multiplier ) {
		if ( empty( $logo[$multiplier]['url'] ) ) {
			if ( empty( $logo[$multiplier]['id'] ) ) {
				$suffix = empty( $multiplier ) ? '' : '_' . $multiplier;
				if ( $mobile ) {
					$logo[$multiplier]['url'] = WOWMALL_THEME_URI . '/assets/images/logo_mobile' . $suffix . '.png';
				}
				else {
					$logo[$multiplier]['url'] = WOWMALL_THEME_URI . '/assets/images/logo' . $suffix . '.png';
					if ( '3' === $header_layout && ! $in_stiky ) {
						$logo[$multiplier]['url'] = WOWMALL_THEME_URI . '/assets/images/logo-2' . $suffix . '.png';
					}
				}
			}
			else {
				$logo[$multiplier]['url'] = wp_get_attachment_image_url( $logo[$multiplier]['id'], 'full' );
			}
		}
	}
	$atts = ' srcset="' . $logo['2x']['url'] . ' 2x, ' . $logo['3x']['url'] . ' 3x"';
	if ( ! empty( $logo[0]['width'] ) ) {
		$atts .= ' width="' . $logo[0]['width'] . '"';
	}
	if ( ! empty( $logo[0]['height'] ) ) {
		$atts .= '  height="' . $logo[0]['height'] . '"';
	}
	$atts   = apply_filters( 'wowmall_logo_img_atts', $atts );
	$logo   = '<img src="' . esc_url( $logo[0]['url'] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" class="logo-img"' . $atts . '>';
	$format = '<%1$s class=logo><a class=logo-link href="%2$s" rel=home>%3$s</a></%1$s>';

	printf( $format, $tag, esc_url( home_url( '/' ) ), $logo );
	unset( $tag, $logo, $atts, $format );
}

function wowmall_payment_methods() {
	global $wowmall_options;
	if ( ! empty( $wowmall_options['payment_methods'] ) ) {
		$img = $wowmall_options['payment_methods_img'];
		if ( ! isset( $img['url'] ) ) {
			$img['url'] = WOWMALL_THEME_URI . '/assets/images/payment-methods.png';
		}
		$atts = '';
		if ( ! empty( $img['width'] ) ) {
			$atts .= ' width="' . esc_attr( $img['width'] ) . '"';
		}
		if ( ! empty( $img['height'] ) ) {
			$atts .= '  height="' . esc_attr( $img['height'] ) . '"';
		}
		echo '<img src="' . esc_attr( $img['url'] ) . '" alt="" class="wowmall-payment-methods swiper-lazy"' . $atts . '>';
		unset( $img, $atts );
	}
}

function wowmall_header_text() {
	global $wowmall_options;

	if ( ! empty( $wowmall_options['header_text'] ) ) {
		echo wp_kses_post( $wowmall_options['header_text'] );
	}
}

/**
 * Display Header Cart
 * @since  1.0.0
 * @uses   wowmall()->is_woocommerce_activated() check if WooCommerce is activated
 * @return void
 */
function wowmall_cart() {
	global $wowmall_options;
	if ( ! empty( $wowmall_options['header_cart_enable'] ) && wowmall()->is_woocommerce_activated() ) { ?>
		<div class=header-cart-wrapper>
			<?php wowmall_cart_link();
			if ( ! is_cart() ) {
				the_widget( 'WC_Widget_Cart', 'title=' );
			} ?>
		</div>
		<?php
	}
}

function wowmall_cart_placeholder() {
	global $wowmall_options;
	if ( ! empty( $wowmall_options['header_cart_enable'] ) && wowmall()->is_woocommerce_activated() ) {
		?>
		<div class=header-cart-wrapper></div><?php
	}
}

/**
 * Cart Fragments
 * Ensure cart contents update when products are added to the cart via AJAX
 *
 * @param  array $fragments Fragments to refresh via AJAX.
 *
 * @return array            Fragments to refresh via AJAX
 */
function wowmall_cart_link_fragment( $fragments ) {

	ob_start();
	wowmall_cart_link();
	$fragments['a.cart-contents'] = ob_get_clean();

	return $fragments;
}

add_filter( 'woocommerce_add_to_cart_fragments', 'wowmall_cart_link_fragment' );

/**
 * Cart Link
 * Displayed a link to the cart including the number of items present and the cart total
 * @return void
 * @since  1.0.0
 */
function wowmall_cart_link() {
	$url   = esc_url( wc_get_cart_url() );
	$count = WC()->cart->get_cart_contents_count();
	?>
	<a class=cart-contents href="<?php echo esc_url( $url ); ?>">
		<?php
		if ( $count ) {
			if ( 99 < $count ) {
				$count = '99+';
			} ?>
			<span class=count><?php echo esc_html( $count ); ?></span>
		<?php } ?>
	</a>
	<?php
	unset( $url, $count );
}

function wowmall_top_search() {
	global $wowmall_options;
	if ( ! isset( $wowmall_options['header_search_enable'] ) || $wowmall_options['header_search_enable'] ) {
		?>
		<div class=wowmall-top-search>
			<form role=search method=get action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class=screen-reader-text
					   for=search-field><?php esc_html_e( 'Search products&hellip;', 'wowmall' ); ?></label>
				<input type="search" id=search-field
					   class=search-field<?php if ( wowmall()->is_woocommerce_activated() ) { ?> placeholder="<?php echo esc_attr_x( 'Search products&hellip;', 'placeholder', 'wowmall' ); ?>"<?php } ?>
					   name=s
					   title="<?php echo esc_attr_x( 'Search products&hellip;', 'label', 'wowmall' ); ?>" autocomplete=off>
				<span class=wowmall-sep></span>
				<button type=submit class=search-submit><span class=myfont-search-circled></span></button>
				<button type=button class=search-close><span class=myfont-cancel-circled></span></button>
				<input type=hidden name=action value=wowmall_ajax_search>
				<?php if ( wowmall()->is_woocommerce_activated() ) { ?>
					<input type=hidden name=post_type value=product>
				<?php } ?>
				<div class=wowmall-search-results></div>
			</form>
		</div>
		<?php
	}
}

function wowmall_search() {
	?>
	<div class=wowmall-search>
		<form role=search method=get action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class=screen-reader-text for=search-field><?php esc_html_e( 'Search for:', 'wowmall' ); ?></label>
			<input type="search" id=search-field class=search-field value="<?php echo get_search_query(); ?>" name=s
				   title="<?php echo esc_attr_x( 'Search for:', 'label', 'wowmall' ); ?>" autocomplete=off>
			<button type=submit class="btn btn-primary btn-sm"><?php esc_html_e( 'Search', 'wowmall' ); ?></button>
		</form>
	</div>
	<?php
}

function wowmall_top_search_mobile() {
	global $wowmall_options;
	if ( ! isset( $wowmall_options['header_search_enable'] ) || $wowmall_options['header_search_enable'] ) {
		$placeholder = wowmall()->is_woocommerce_activated() ? esc_attr_x( 'Search products&hellip;', 'placeholder', 'wowmall' ) : esc_attr_x( 'Search&hellip;', 'placeholder', 'wowmall' );
		?>
		<div class=wowmall-top-search>
			<form role=search method=get action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class=screen-reader-text
					   for=search-field><?php esc_html_e( 'Search for:', 'wowmall' ); ?></label>
				<input type="search" id=search-field class=search-field
					   placeholder="<?php echo esc_attr( $placeholder ); ?>"
					   value="<?php echo esc_attr( get_search_query() ); ?>" name=s
					   title="<?php echo esc_attr_x( 'Search for:', 'label', 'wowmall' ); ?>">
				<button type=submit class=search-submit><span class=myfont-search-circled></span></button>
				<?php if ( wowmall()->is_woocommerce_activated() ) { ?>
					<input type=hidden name=post_type value=product>
				<?php } ?>
			</form>
		</div>
		<?php
	}
}

function wowmall_compare() {
	global $wowmall_options;
	if ( wowmall()->is_woocommerce_activated() && ! empty( $wowmall_options['header_prod_compare_enable'] ) && ! empty( $wowmall_options['header_prod_compare_page'] ) ) {
		$page = $wowmall_options['header_prod_compare_page'];
		if ( ! get_post_status( $page ) ) {
			return;
		}
		$url = get_page_link( $page ); ?>
		<a class=header-compare-link href="<?php echo esc_url( $url ); ?>">
			<?php if ( wp_is_mobile() ) { ?>
				<span class=wowmall-header-tools-label>
				<?php esc_html_e( 'Compare', 'wowmall' ); ?>
			</span>
			<?php } ?>
		</a>
	<?php }
	unset( $url, $page );
}

function wowmall_wishlist() {
	global $wowmall_options;
	if ( wowmall()->is_woocommerce_activated() && ! empty( $wowmall_options['header_prod_wishlist_enable'] ) && ! empty( $wowmall_options['header_prod_wishlist_page'] ) ) {
		$page = $wowmall_options['header_prod_wishlist_page'];
		if ( ! $page || ! get_post_status( $page ) ) {
			return;
		}
		$url = get_page_link( $page ); ?>
		<a class=header-wishlist-link href="<?php echo esc_url( $url ); ?>">
			<?php if ( wp_is_mobile() ) { ?>
				<span class=wowmall-header-tools-label>
				<?php esc_html_e( 'Wishlist', 'wowmall' ); ?>
			</span>
			<?php } ?>
		</a>
	<?php }
	unset( $url, $page );
}

function wowmall_currency() {
	global $wowmall_options, $wp_registered_widgets;

	if ( wowmall()->is_woocommerce_activated() && ! empty( $wowmall_options['header_currency_enable'] ) ) {
		?>
		<div class=header-currency-wrapper>
			<?php
			$sidebar_not_empty = false;
			$sidebar_content   = '';
			if ( is_active_sidebar( 'sidebar-currency' ) ) {
				$sidebars_widgets   = wp_get_sidebars_widgets();
				$restricted_widgets = array(
					'widget_meta',
					'widget_archive',
					'widget_calendar',
					'widget_categories',
					'widget_pages',
					'widget_recent_comments',
					'widget_recent_entries',
					'widget_search',
					'widget_text',
					'widget_nav_menu',
					'widget_mc4wp_form_widget',
					'widget_rss',
					'widget_revslider',
					'woocommerce wowmall_widget_clear_all',
					'woocommerce widget_shopping_cart',
					'woocommerce widget_layered_nav',
					'woocommerce widget_rating_filter',
					'widget_tag_cloud',
					'woocommerce widget_recently_viewed_products',
					'woocommerce widget_top_rated_products',
					'wowmall-widget-about',
					'wowmall-widget-instagram',
					'wowmall-widget-follow',
					'woocommerce widget_recent_reviews',
					'woocommerce widget_product_tag_cloud',
					'woocommerce widget_product_search',
					'woocommerce widget_price_filter',
					'woocommerce widget_layered_nav_filters',
					'woocommerce widget_product_categories',
					'woocommerce widget_products',
					'monster',
					'wc_monster_widget',
				);
				if ( ! empty( $sidebars_widgets['sidebar-currency'] ) ) {
					foreach ( $sidebars_widgets['sidebar-currency'] as $widget_id ) {
						if ( ! isset( $wp_registered_widgets[$widget_id] ) ) {
							continue;
						}
						$widget    = $wp_registered_widgets[$widget_id];
						$classname = $widget['classname'];
						if ( in_array( $classname, $restricted_widgets ) ) {
							unset( $wp_registered_widgets[$widget_id] );
						}
					}
				}
				ob_start();
				$sidebar_not_empty = dynamic_sidebar( 'sidebar-currency' );
				$sidebar_content   = ob_get_clean();
			}
			if ( $sidebar_not_empty ) {
				echo $sidebar_content;
			}
			else {
				$currencies = array(
					esc_html__( 'GBP - &pound;', 'wowmall' ),
					esc_html__( 'EUR - &euro;', 'wowmall' ),
					esc_html__( 'USD - &#36;', 'wowmall' ),
				);
				if ( wp_is_mobile() ) {
					$currencies = array(
						esc_html__( 'GBP Pounds &pound;', 'wowmall' ),
						esc_html__( 'EUR Euro &euro;', 'wowmall' ),
						esc_html__( 'USD Dollars &#36;', 'wowmall' ),
					);
				}
				?>
				<a class=dropdown-toggle data-toggle=dropdown href=#>
					<?php if ( wp_is_mobile() ) {
						esc_html_e( 'USD Dollars &#36;', 'wowmall' );
					}
					else {
						esc_html_e( '&#36;', 'wowmall' );
					} ?>
				</a>
				<ul class=dropdown-menu>
					<?php foreach ( $currencies as $currency ) { ?>
						<li>
							<a href=#>
								<?php echo esc_html( $currency ); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
				<?php
				unset( $currencies, $currency );
			} ?>
		</div>
		<?php
	}
}

function wowmall_post_thumb() {
	$format = get_post_format();
	if ( 'video' === $format ) {
		wowmall_post_format_video();

		return;
	}
	if ( 'gallery' === $format ) {
		wowmall_post_format_gallery();

		return;
	}
	if ( 'image' === $format ) {
		wowmall_post_format_image();

		return;
	}
	if ( has_post_thumbnail() ) {
		global $post, $wowmall_options; ?>
		<div class=entry-thumb>
		<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
			<?php
			if ( ! empty( $post->is_related ) && $post->is_related ) {
				$size = 'blog_img_size_related';
				the_post_thumbnail( $size );
			}
			elseif ( ! empty( $wowmall_options['blog_layout_type'] ) && 'grid' !== $wowmall_options['blog_layout_type'] ) {
				$size = 'blog_img_size_' . $wowmall_options['blog_layout_type'];
				the_post_thumbnail( $size );
			}
			else {
				the_post_thumbnail();
			} ?>
		</a>
		<?php
	}
	if ( 'link' === $format ) { ?>
		<div class=post-format-link__link>
			<?php do_action( 'wowmall_post_format_link' ); ?>
		</div>
	<?php }
	if ( has_post_thumbnail() ) {
		if ( 'audio' === $format ) { ?>
			<div class=post-format-audio__audio>
				<?php do_action( 'wowmall_post_format_audio' ); ?>
			</div>
		<?php }
		?>
		</div>
	<?php }
}

function wowmall_single_thumb() {
	$format = get_post_format();
	if ( 'video' === $format ) {
		wowmall_post_format_video();

		return;
	}
	if ( 'gallery' === $format ) {
		wowmall_post_format_gallery();

		return;
	}
	if ( 'image' === $format ) {
		wowmall_post_format_image();

		return;
	}
	if ( has_post_thumbnail() ) { ?>
		<div class=entry-thumb>
		<?php global $wowmall_options;
		$size = 'large';
		if ( ! empty( $wowmall_options['blog_img_size_single'] ) ) {
			$size = 'blog_img_size_single';
		} ?>
		<div class=single-thumb>
			<?php the_post_thumbnail( $size ); ?>
		</div>
		<?php
	}
	if ( 'link' === $format ) { ?>
		<div class=post-format-link__link>
			<?php do_action( 'wowmall_post_format_link' ); ?>
		</div>
	<?php }
	if ( has_post_thumbnail() ) {
		if ( 'audio' === $format ) { ?>
			<div class=post-format-audio__audio>
				<?php do_action( 'wowmall_post_format_audio' ); ?>
			</div>
		<?php }
		?>
		</div>
	<?php }
}

function wowmall_content_single() {
	$content = get_the_content();
	if ( 'video' === get_post_format() ) {
		$content = apply_filters( 'the_content', $content );
		$types   = array(
			'video',
			'object',
			'embed',
			'iframe',
		);
		$embeds  = get_media_embedded_in_content( $content, $types );

		if ( ! empty( $embeds ) ) {
			foreach ( $types as $tag ) {
				$content = preg_replace( "/<{$tag}[^>]*>.*?<\/{$tag}>/", '', $content );
			}
		}
	}
	if ( 'image' === get_post_format() ) {
		preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
		if ( ! empty( $matches ) ) {
			foreach ( $matches as $shortcode ) {
				if ( 'caption' === $shortcode[2] ) {
					$pos = strpos( $content, $shortcode[0] );
					if ( $pos !== false ) {
						$content = substr_replace( $content, '', $pos, strlen( $shortcode[0] ) );
					}
				}
			}
		}
	}
	if ( 'gallery' === get_post_format() ) {
		preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );
		if ( ! empty( $matches ) ) {
			foreach ( $matches as $shortcode ) {
				if ( 'gallery' === $shortcode[2] ) {
					$pos = strpos( $content, $shortcode[0] );
					if ( $pos !== false ) {
						$content = substr_replace( $content, '', $pos, strlen( $shortcode[0] ) );
					}
				}
			}
		}
	}
	echo apply_filters( 'the_content', $content );
	wp_link_pages( array(
		'before'           => '<div class=page-numbers>',
		'after'            => '</div>',
		'link_before'      => '<span>',
		'link_after'       => '</span>',
		'nextpagelink'     => '<i class=myfont-right-open-2></i>',
		'previouspagelink' => '<i class=myfont-left-open-2></i>',
	) );
}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function wowmall_post_meta() {

	if ( ! in_array( get_post_type(), array(
		'post',
		'gallery',
	) )
	) {
		return;
	}
	$meta = array();
	$cats = get_the_category_list();

	if ( ! empty( $cats ) ) {
		$meta[] = '<div class=entry-meta-cats>' . get_the_category_list() . '</div>';
	}
	$meta[] = '<span class="author vcard">' . get_the_author_posts_link() . '</span>';

	$time_string = sprintf( '<time class="entry-date published" datetime="%1$s">%2$s</time>', esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ) );
	$meta[]      = '<span class=posted-on><a href="' . esc_url( get_permalink() ) . '" rel=bookmark>' . $time_string . '</a></span>';

	echo join( '', $meta );
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function wowmall_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' !== get_post_type() ) {
		return;
	}
	global $wowmall_options;
	if ( ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) || ( ! empty( $wowmall_options['blog_meta_tags'] ) && $wowmall_options['blog_meta_tags'] ) || ( ! empty( $wowmall_options['blog_share'] ) && $wowmall_options['blog_share'] ) ) { ?>
		<div class=entry-meta-footer>
	<?php }
	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class=comments-link>';
		comments_popup_link( '0', '1', '%' );
		echo '</span>';
	}

	if ( ! empty( $wowmall_options['blog_meta_tags'] ) && $wowmall_options['blog_meta_tags'] ) {

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'wowmall' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tags: %1$s', 'wowmall' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}
	if ( ! empty( $wowmall_options['blog_share'] ) && $wowmall_options['blog_share'] ) { ?>
		<div class=entry-share-btns>
			<?php wowmall_share_buttons(); ?>
		</div>
	<?php }
	if ( ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) || ( ! empty( $wowmall_options['blog_meta_tags'] ) && $wowmall_options['blog_meta_tags'] ) || ( ! empty( $wowmall_options['blog_share'] ) && $wowmall_options['blog_share'] ) ) { ?>
		</div>
	<?php }
	if ( ! empty( $wowmall_options['blog_readmore'] ) && $wowmall_options['blog_readmore'] ) {
		$text = esc_html__( 'Read more', 'wowmall' );
		if ( ! empty( $wowmall_options['blog_readmore_label'] ) && $wowmall_options['blog_readmore_label'] ) {
			$text = $wowmall_options['blog_readmore_label'];
		} ?>
		<div class=readmore_wrapper>
			<?php printf( '<a href="%s" class="btn btn-sm btn-default">%s</a>', esc_url( get_permalink() ), esc_html( $text ) ); ?>
		</div>
	<?php }
}

function wowmall_post_footer() {
	// Hide category and tag text for pages.
	if ( ! in_array( get_post_type(), array(
		'post',
		'gallery',
	) )
	) {
		return;
	} ?>
	<div class=entry-meta-footer>
		<?php
		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class=comments-link>';
			comments_popup_link( '0', '1', '%' );
			echo '</span>';
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'wowmall' ) );
		if ( $tags_list ) {
			printf( '<span class=tags-links>' . esc_html__( 'Tags: %1$s', 'wowmall' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		} ?>
		<div class=entry-share-btns>
			<?php wowmall_share_buttons(); ?>
		</div>
	</div>
	<?php
}

function wowmall_breadcrumb() {
	global $wowmall_options;
	if ( ! is_front_page() && ( ! isset( $wowmall_options['breadcrumbs'] ) || $wowmall_options['breadcrumbs'] ) ) {
		get_template_part( 'template-parts/breadcrumb' );
	}
}

/**
 * Dispaply box with information about author.
 * @since  1.0.0
 * @return void
 */
function wowmall_post_author_bio() {
	$desc = get_the_author_meta( 'description' );
	if ( get_post_format() || empty( $desc ) ) {
		return;
	}
	get_template_part( 'template-parts/content', 'author-bio' );
}

function wowmall_terms_slug( $term ) {
	return $term->slug;
}

function wowmall_related_posts() {

	if ( ! is_singular( 'post' ) ) {
		return;
	}
	if ( 'post' !== get_post_type() ) {
		return;
	}
	if ( get_post_format() ) {
		return;
	}
	global $post;

	$terms = get_the_terms( $post, 'post_tag' );

	if ( ! $terms ) {
		return;
	}
	$terms = array_map( 'wowmall_terms_slug', $terms );

	$post_args = array(
		'post_type'      => 'post',
		'tag_slug__in'   => $terms,
		'posts_per_page' => 2,
		'post__not_in'   => array( $post->ID ),
	);
	$posts     = new WP_Query( $post_args );

	if ( ! $posts ) {
		return;
	} ?>
	<div class="related-posts posts-list">
		<h4 class=entry-title><?php esc_html_e( 'You might  also be interesed in:', 'wowmall' ); ?></h4>
		<div class=row>
			<?php while ( $posts->have_posts() ) {
				$posts->the_post();
				?>
				<div class=col-md-6>
					<?php
					$post->is_related = true;
					get_template_part( 'template-parts/content', get_post_format() ); ?>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php wp_reset_postdata();
}

function wowmall_comment_callback( $comment, $args, $depth ) {
	if ( class_exists( 'WC' ) ) { ?>
		<li <?php comment_class(); ?> id=li-comment-<?php comment_ID() ?>>
		<div id=comment-<?php comment_ID(); ?> class=comment_container>
			<?php do_action( 'woocommerce_review_before', $comment ); ?>
			<div class=comment-text>
				<?php do_action( 'woocommerce_review_before_comment_meta', $comment );
				do_action( 'woocommerce_review_meta', $comment );
				do_action( 'woocommerce_review_before_comment_text', $comment );
				do_action( 'woocommerce_review_comment_text', $comment );
				do_action( 'woocommerce_review_after_comment_text', $comment ); ?>
			</div>
		</div>
	<?php }
	else {
		$children = $comment->get_children();
		?>
		<li id=li-comment-<?php comment_ID(); ?><?php comment_class( ! empty( $children ) ? 'parent' : '', $comment ); ?>>
		<article id=comment-<?php comment_ID(); ?> class=comment_container>
			<?php if ( 0 != $args['avatar_size'] ) {
				echo get_avatar( $comment, $args['avatar_size'] );
			} ?>
			<div class=comment-text>
				<div class=meta>
					<?php printf( '<strong class=fn>%s</strong>', get_comment_author_link( $comment ) ); ?>
					<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
						<time datetime="<?php comment_time( 'c' ); ?>"><?php
							/* translators: 1: comment date, 2: comment time */
							echo get_comment_date( '', $comment );
							?></time>
					</a>:
					<?php edit_comment_link( esc_html__( 'Edit', 'wowmall' ), '<span class=edit-link>', '</span>' ); ?>

					<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class=comment-awaiting-moderation><?php esc_html_e( 'Your comment is awaiting moderation.', 'wowmall' ); ?></p>
				<?php endif; ?>
				</div>

				<div class=description>
					<?php comment_text(); ?>
				</div>

				<?php
				comment_reply_link( array_merge( $args, array(
					'add_below' => 'comment',
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
					'before'    => '<div class=reply>',
					'after'     => '</div>',
				) ) );
				?>
			</div>
		</article>
	<?php }
}

function wowmall_to_top() {
	global $wowmall_options;
	if ( ! isset( $wowmall_options['to_top'] ) || $wowmall_options['to_top'] ) { ?>
		<a class=wowmall-to-top href=#><?php echo esc_html_x( 'Top', 'to-top-button', 'wowmall' ); ?></a>
	<?php }
}

function wowmall_footer() {
	global $wowmall_options;
	$footer_layout = ! empty( $wowmall_options['footer_layout'] ) ? $wowmall_options['footer_layout'] : 1;
	if ( wp_is_mobile() ) {
		$footer_layout = '5' === $footer_layout ? 'mobile-minimal' : 'mobile';
	}
	get_template_part( 'template-parts/footer/footer', $footer_layout );
	unset( $footer_layout );
}

function wowmall_footer_content( $layout ) {
	global $wowmall_options, $sitepress;
	$page_id = ! empty( $wowmall_options['footer_' . $layout . '_page'] ) ? (int) $wowmall_options['footer_' . $layout . '_page'] : 0;

	if ( isset( $sitepress ) ) {

		$page_id = icl_object_id( $page_id, 'page', true, $sitepress->get_current_language() );
	}
	if ( $page_id && 'page' === get_post_type( $page_id ) ) {
		remove_filter( 'the_content', 'prepend_attachment' );
		echo apply_filters( 'the_content', get_post_field( 'post_content', $page_id ) );
	}
	unset( $page_id );
}

function wowmall_footer_subscribe( $args = array() ) {
	global $wowmall_options;

	$args = wp_parse_args( $args, array(
		'form'    => ! empty( $wowmall_options['footer_newsletter_form'] ) ? $wowmall_options['footer_newsletter_form'] : '',
		'title'   => isset( $wowmall_options['footer_newsletter_title'] ) ? $wowmall_options['footer_newsletter_title'] : esc_html__( 'Newsletter Signup', 'wowmall' ),
		'pretext' => isset( $wowmall_options['footer_newsletter_text'] ) ? $wowmall_options['footer_newsletter_text'] : esc_html__( 'Sign up for our e-mail and be the first who know our special offers! Furthermore, we will give a 15% discount on the next order after you sign up.', 'wowmall' ),
	) );

	?>
	<div class=wowmall-newsletter>
		<?php if ( ! empty( $args['title'] ) ) { ?>
			<h4><?php echo esc_html( $args['title'] ); ?></h4>
		<?php }
		if ( ! empty( $args['pretext'] ) ) { ?>
			<div class=wowmall-mc4wp-form-widget-pretext><?php echo wp_kses_post( $args['pretext'] ); ?></div>
		<?php }
		wowmall()->subscribe_form( $args['form'] ); ?>
	</div>
	<?php
	unset( $form_id, $title, $pretext );
}
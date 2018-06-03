<?php

class WowmallWcAjaxProductsAdmin {

	/**
	 * The single instance of the class.
	 * @since 1.0.0
	 */
	protected static $_instance;

	public function __construct() {

		add_filter( 'woocommerce_pagination_args', array(
			$this,
			'pagination_args',
		) );

		add_action( 'wp_ajax_nopriv_wowmall_wc_rebuild_products', array(
			$this,
			'process_ajax',
		) );
		add_action( 'wp_ajax_wowmall_wc_rebuild_products', array(
			$this,
			'process_ajax',
		) );

		add_action( 'wp_ajax_nopriv_wowmall_wc_load_more', array(
			$this,
			'process_load_more_ajax',
		) );
		add_action( 'wp_ajax_wowmall_wc_load_more', array(
			$this,
			'process_load_more_ajax',
		) );
	}

	public static function instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function process_load_more_ajax() {

		$page_url       = $_POST['pageUrl'];
		$products_count = $_POST['productsCount'];

		$_SERVER['REQUEST_URI'] = parse_url( $page_url, PHP_URL_PATH );
		$_SERVER['PHP_SELF']    = str_replace( 'wp-admin/admin-ajax', 'index', $_SERVER['PHP_SELF'] );

		wp();

		global $wp;

		$defaults = array(
			'post_type'   => 'product',
			'post_status' => 'publish',
		);

		$args = wp_parse_args( $wp->query_vars, $defaults );

		parse_str( parse_url( $page_url, PHP_URL_QUERY ), $_GET );

		$args = wp_parse_args( $_GET, $args );

		$wcquery = new TM_WC_Query();
		$posts   = new WP_Query( $args );

		$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'] = $posts;

		ob_start();

		if ( have_posts() ) :

			while ( have_posts() ) : the_post();

				global $woocommerce_loop;

				$woocommerce_loop['loop'] = ! empty( $woocommerce_loop['loop'] ) ? $woocommerce_loop['loop'] : ( int ) $products_count;

				add_filter( 'post_class', function ( $classes ) {
					$classes[] = 'wowmall-product-animate-appearance';

					return $classes;
				}, 20, 3 );

				wc_get_template_part( 'content', 'product' );

			endwhile;

		endif;

		$content = ob_get_clean();

		ob_start();
		woocommerce_pagination();
		$button = ob_get_clean();

		$json = array(
			'products' => $content,
			'button'   => $button,
		);

		wp_reset_query();

		wp_send_json_success( $json );

	}

	public function process_ajax() {

		$page_url               = $_POST['pageUrl'];
		$_SERVER['REQUEST_URI'] = parse_url( $page_url, PHP_URL_PATH );
		$_SERVER['PHP_SELF']    = str_replace( 'wp-admin/admin-ajax', 'index', $_SERVER['PHP_SELF'] );

		wp();

		global $wp;

		$defaults = array(
			'post_type' => 'product',
			'post_status' => 'publish',
		);

		$args = wp_parse_args( $wp->query_vars, $defaults );

		parse_str( parse_url( $page_url, PHP_URL_QUERY ), $_GET );

		$args = wp_parse_args( $_GET, $args );

		do_action( 'before_wowmall_wc_loop_ajax_query', $_GET );

		$wcquery = new TM_WC_Query();
		$posts   = new WP_Query( $args );

		$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'] = $posts;

		ob_start();

		if ( have_posts() ) {

			do_action( 'woocommerce_before_shop_loop' );

			woocommerce_product_loop_start();

			while ( have_posts() ) {

				the_post();

				wc_get_template_part( 'content', 'product' );
			}
			woocommerce_product_loop_end();

			do_action( 'woocommerce_after_shop_loop' );

		}
		else {

			wc_get_template( 'loop/no-products-found.php' );
		}

		$content = ob_get_clean();

		$json = array(
			'products' => $content,
		);
		wp_reset_query();

		wp_send_json_success( $json );
	}

	public function pagination_args( $args = array() ) {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			$args['base'] = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', $this->get_pagenum_link( 999999999, false ) ) ) );
		}

		return $args;
	}

	public static function get_pagenum_link( $pagenum = 1, $escape = true ) {

		global $wp_rewrite;

		$pagenum   = (int) $pagenum;
		$parse_url = parse_url( $_POST['pageUrl'] );
		$page_url  = str_replace( $parse_url['scheme'] . '://' . $parse_url['host'], '', $_POST['pageUrl'] );
		$request   = remove_query_arg( 'paged', $page_url );
		$home_root = parse_url( home_url() );
		$home_root = ( isset( $home_root['path'] ) ) ? $home_root['path'] : '';
		$home_root = preg_quote( $home_root, '|' );
		$request   = preg_replace( '|^' . $home_root . '|i', '', $request );
		$request   = preg_replace( '|^/+|', '', $request );

		if ( ! $wp_rewrite->using_permalinks() ) {

			$base = trailingslashit( home_url() );

			if ( 1 < $pagenum ) {

				$result = add_query_arg( 'paged', $pagenum, $base . $request );
			}
			else {

				$result = $base . $request;
			}
		}
		else {

			$qs_regex = '|\?.*?$|';

			preg_match( $qs_regex, $request, $qs_match );

			if ( ! empty( $qs_match[0] ) ) {

				$query_string = $qs_match[0];
				$request      = preg_replace( $qs_regex, '', $request );

			}
			else {

				$query_string = '';
			}
			$request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request );
			$request = preg_replace( '|^' . preg_quote( $wp_rewrite->index, '|' ) . '|i', '', $request );
			$request = ltrim( $request, '/' );
			$base    = trailingslashit( home_url() );

			if ( $wp_rewrite->using_index_permalinks() && ( 1 < $pagenum || '' != $request ) ) {
				$base .= $wp_rewrite->index . '/';
			}

			if ( 1 < $pagenum ) {

				$request = ( ( ! empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( $wp_rewrite->pagination_base . "/" . $pagenum, 'paged' );
			}
			$result = $base . $request . $query_string;
		}

		/**
		 * Filter the page number link for the current request.
		 * @since 1.0.0
		 *
		 * @param string $result The page number link.
		 */
		$result = apply_filters( 'get_pagenum_link', $result );

		if ( $escape ) {
			return esc_url( $result );
		}
		else {
			return esc_url_raw( $result );
		}
	}
}

function tm_wc_ajax_admin() {

	return WowmallWcAjaxProductsAdmin::instance();
}

tm_wc_ajax_admin();

if ( ! class_exists( 'WC_Query' ) ) {

	require_once ABSPATH . 'wp-content/plugins/woocommerce/includes/class-wc-query.php';
}

class TM_WC_Query extends WC_Query {

	public function __construct() {

		add_action( 'init', array(
			$this,
			'add_endpoints',
		) );
		add_action( 'wp_loaded', array(
			$this,
			'get_errors',
		), 20 );
		add_filter( 'query_vars', array(
			$this,
			'add_query_vars',
		), 0 );
		add_action( 'parse_request', array(
			$this,
			'parse_request',
		), 0 );
		add_action( 'pre_get_posts', array(
			$this,
			'pre_get_posts',
		) );
		add_action( 'wp', array(
			$this,
			'remove_product_query',
		) );
		add_action( 'wp', array(
			$this,
			'remove_ordering_args',
		) );

		$this->init_query_vars();
	}

	public function pre_get_posts( $q ) {

		$GLOBALS['wp_the_query'] = $q;

		parent::pre_get_posts( $q );
	}
}
<?php
/**
 * Pagination - Show numbered pagination for catalog pages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/pagination.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query, $wowmall_options;

$total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
$current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
$format  = isset( $format ) ? $format : '';

if ( $total <= 1 ) {
	return;
}

if( ! empty( $wowmall_options[ 'shop_pagination' ] ) ) {

	$args = apply_filters( 'woocommerce_pagination_args', array(
		'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
		'format'       => '',
		'add_args'     => false,
		'current'      => max( 1, get_query_var( 'paged' ) ),
		'total'        => $wp_query->max_num_pages,
		'add_fragment' => ''
	) );

	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );

	if ( $args['current'] && ( $args['current'] < $args['total'] || - 1 == $args['total'] ) ) {

		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $args['current'] + 1, $link );

		if ( ! is_array( $args['add_args'] ) ) {
			$args['add_args'] = array();
		}

		if ( isset( $url_parts[1] ) ) {
			$format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
			$format_query = isset( $format[1] ) ? $format[1] : '';
			wp_parse_str( $format_query, $format_args );
			wp_parse_str( $url_parts[1], $url_query_args );
			foreach ( $format_args as $format_arg => $format_arg_value ) {

				unset( $url_query_args[ $format_arg ] );
			}
			$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
		}

		$add_args = $args['add_args'];

		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}

		$link .= $args['add_fragment'];

		if( 'load_more' === $wowmall_options[ 'shop_pagination' ] ) { ?>
		<a href="<?php echo $link; ?>"
		        class="btn btn-sm btn-primary wowmall-wc-ajax-load-more-button"><span class="wowmall-wc-ajax-load-more-button-loader"></span><span class="wowmall-wc-ajax-load-more-button-text"><?php esc_html_e( 'Load more', 'wowmall' ); ?></span></a>
	<?php }
		elseif( 'infinite' === $wowmall_options[ 'shop_pagination' ] ) {
			$color            = ! empty( $wowmall_options['accent_color_1'] ) ? $wowmall_options['accent_color_1'] : '#fc6f38';
			?>
		<div data-href="<?php echo $link; ?>" class="wowmall-infinite-preloader"><svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50%" cy="50%" r="40" stroke="<?php echo $color; ?>" fill="none" stroke-width="5"><animate attributeType="XML" attributeName="stroke-dashoffset" dur="2s" repeatCount="indefinite" from="0" to="502"/><animate attributeName="stroke-dasharray" attributeType="XML" dur="2s" repeatCount="indefinite" values="150.6 100.4;1 250;150.6 100.4"/></circle></svg></div>
		<?php }
	}
	return;
}
?>

<nav class=pagination>
	<?php
		echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
			'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
			'format'       => $format,
			'add_args'     => false,
			'current'      => max( 1, $current ),
			'total'        => $total,
			'prev_text'    => '<i class=myfont-left-open-2></i>',
			'next_text'    => '<i class=myfont-right-open-2></i>',
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3
		) ) );
	?>
</nav>

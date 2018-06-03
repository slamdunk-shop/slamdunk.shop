<?php

require_once( WOWMALL_THEME_ADMIN_DIR . 'mega-menu.php' );
require_once( WOWMALL_THEME_ADMIN_DIR . 'nav-menu-walker.php' );

/*
 * Enable support TGM configuration.
 */
require_once( WOWMALL_THEME_ADMIN_DIR . 'tgm/tgm-init.php' );

function wowmall_admin_html_font_size() {

	wp_add_inline_style( 'dashboard', 'html{font-size:18px}' );
}

/**
 * Flush out the transients used in wowmall_categorized_blog.
 */
function wowmall_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'wowmall_categories' );
}

function wowmall_add_product_category_size_field() {
	?>
	<div class="form-field collection-size-wrap">
		<label for=collection_size><?php esc_html_e( 'Thumb Size for Collection Page', 'wowmall' ) ?></label>
		<select name=collection_size id=collection_size>
			<option value=0><?php esc_html_e( '1x1', 'wowmall' ); ?></option>
			<option value=1><?php esc_html_e( '1x2', 'wowmall' ); ?></option>
			<option value=2><?php esc_html_e( '2x1', 'wowmall' ); ?></option>
			<option value=3><?php esc_html_e( '2x2', 'wowmall' ); ?></option>
		</select>
	</div>
	<?php
}

function wowmall_edit_product_category_size_field( $term ) {
	$value = get_term_meta( $term->term_id, 'collection_size', true );
	?>
	<tr class="form-field collection-size-wrap">
		<th scope=row>
			<label for=collection_size><?php esc_html_e( 'Thumb Size for Collection Page', 'wowmall' ) ?></label>
		</th>
		<td>
			<select name=collection_size id=collection_size>
				<option value=0 <?php selected( $value, 0 ); ?>><?php esc_html_e( '1x1', 'wowmall' ); ?></option>
				<option value=1 <?php selected( $value, 1 ); ?>><?php esc_html_e( '1x2', 'wowmall' ); ?></option>
				<option value=2 <?php selected( $value, 2 ); ?>><?php esc_html_e( '2x1', 'wowmall' ); ?></option>
				<option value=3 <?php selected( $value, 3 ); ?>><?php esc_html_e( '2x2', 'wowmall' ); ?></option>
			</select>
		</td>
	</tr>
	<?php
}

function wowmall_save_product_category_field( $term_id, $tt_id = '', $taxonomy = '' ) {
	if ( isset( $_POST['collection_size'] ) && 'product_cat' === $taxonomy ) {
		update_term_meta( $term_id, 'collection_size', esc_attr( $_POST['collection_size'] ) );
	}
}

function wowmall_product_cat_columns( $columns ) {

	$columns['collection_size'] = esc_html__( 'Collection Size', 'wowmall' );

	return $columns;
}

function wowmall_product_cat_column( $columns, $column, $id ) {

	if ( 'collection_size' == $column ) {

		$collection_size = get_term_meta( $id, 'collection_size', true );

		$collection_size = $collection_size ? $collection_size : 0;

		switch ( $collection_size ) {
			case 1 :
				$size = '1x2';
				break;
			case 2 :
				$size = '2x1';
				break;
			case 3 :
				$size = '2x2';
				break;
			default :
				$size = '1x1';
		}
		$columns .= $size;
	}

	return $columns;
}

function wowmall_admin_scripts( $hook ) {

	$version = wowmall()->get_version();
	$min     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	if ( 'widgets.php' === $hook ) {
		wp_enqueue_style( 'wowmall-admin', WOWMALL_THEME_URI . '/admin/assets/css/admin-styles.css', array(), $version );
		wp_enqueue_script( 'wowmall-media', WOWMALL_THEME_URI . '/admin/assets/js/media' . $min . '.js', array( 'jquery' ), $version, true );
		$translation_media = array(
			'mediaFrameTitle' => esc_html__( 'Choose image', 'wowmall' ),
		);
		wp_localize_script( 'wowmall-media', 'wowmallMediaTranslation', $translation_media );
	}
	if ( ! in_array( $hook, array(
		'toplevel_page_revslider',
		'slider-revolution_page_revslider_navigation',
	) ) ) {
		return;
	}
	wp_enqueue_style( 'wowmall-myfont', WOWMALL_THEME_URI . '/assets/css/myfont.css', array(), $version );
}

add_action( 'admin_enqueue_scripts', 'wowmall_admin_html_font_size', 1000 );

add_action( 'edit_category', 'wowmall_category_transient_flusher' );
add_action( 'save_post', 'wowmall_category_transient_flusher' );

add_action( 'product_cat_add_form_fields', 'wowmall_add_product_category_size_field' );

add_action( 'product_cat_edit_form_fields', 'wowmall_edit_product_category_size_field' );

add_action( 'created_term', 'wowmall_save_product_category_field', 10, 3 );
add_action( 'edit_term', 'wowmall_save_product_category_field', 10, 3 );

add_filter( 'manage_edit-product_cat_columns', 'wowmall_product_cat_columns' );
add_filter( 'manage_product_cat_custom_column', 'wowmall_product_cat_column', 10, 3 );

add_action( 'admin_enqueue_scripts', 'wowmall_admin_scripts' );

add_filter( 'woocommerce_product_data_tabs', 'wowmall_woocommerce_product_data_tabs' );

add_action( 'woocommerce_product_data_panels', 'wowmall_woocommerce_product_data_panels' );

add_action( 'woocommerce_process_product_meta', 'wowmall_woocommerce_process_product_meta', 10, 2 );

function wowmall_woocommerce_product_data_tabs( $tabs ) {
	$tabs = array_merge( $tabs, array(
		'video' => array(
			'label'  => esc_html__( 'Video', 'wowmall' ),
			'target' => 'video_product_data',
			'class'  => array(),
		),
	) );

	return $tabs;
}

function wowmall_woocommerce_product_data_panels() { ?>
	<div id="video_product_data" class="panel woocommerce_options_panel hidden">

		<div class="options_group">
			<?php

			global $post;

			$video_url = isset( $post->_video_url ) ? esc_url( $post->_video_url ) : '';

			woocommerce_wp_text_input( array(
				'id'    => '_video_url',
				'label' => esc_html__( 'Video url', 'wowmall' ),
				'value' => $video_url,
			) );
			?>
		</div>

	</div>

<?php }

function wowmall_woocommerce_process_product_meta( $post_id, $post ) {
	$video_url = isset( $_POST['_video_url'] ) ? esc_url( $_POST['_video_url'] ) : '';
	update_post_meta( $post_id, '_video_url', $video_url );
}

function wowmall_get_mobile_layout() {

	$_SERVER['HTTP_USER_AGENT'] = 'Mobile';

	ob_start();

	if ( class_exists( 'Vc_Base' ) ) {
		$vc = new Vc_Base();
		$vc->initPage();
	}
	if ( class_exists( 'WPBMap' ) ) {
		$wpbmap = new WPBMap();
		$wpbmap->addAllMappedShortcodes();
	}
	get_template_part( 'template-parts/header/header', 'mobile' );

	$header = ob_get_clean();

	wp_send_json_success( array( 'header' => $header ) );
}

function wowmall_subscribe_popup() {
	global $wowmall_options;
	ob_start();
	if ( empty( $wowmall_options['popup'] ) ) {
		return;
	}
	if ( ! empty( $_COOKIE['wowmall-dont-show-subscribe-popup'] ) ) {
		return;
	}
	$in_list = false;
	if ( ! empty( $wowmall_options['popup_check_is_subscribed'] ) && is_user_logged_in() ) {
		$user = wp_get_current_user();
		if ( class_exists( 'MC4WP_MailChimp' ) ) {
			$mailchimp = new MC4WP_MailChimp;
			$lists     = $mailchimp->get_lists();
			$in_list   = true;
			if ( ! empty( $lists ) ) {
				foreach ( $lists as $id => $list ) {
					try {
						$in_current_list = $mailchimp->list_has_subscriber( $id, $user->data->user_email );
					}
					catch ( MC4WP_API_Resource_Not_Found_Exception $e ) {
						$in_list = false;
						continue;
					}
					catch ( MC4WP_API_Exception $e ) {
						$in_list = false;
						continue;
					}
					$in_list = $in_current_list;
					break;
				}
			}
		}
	}
	if ( ! $in_list ) {
		$form_id = empty( $wowmall_options['popup_form'] ) ? '' : $wowmall_options['popup_form'];
		$pretext = ! empty( $wowmall_options['popup_pretext'] ) ? $wowmall_options['popup_pretext'] : __( '<h2>BE THE FIRST<br>TO KNOW.</h2><p class=discount>15% off</p><h4>your next purchase when you sign up.</h4><p>By signing up, you accept the <a href=#>terms & Privacy Policy</p></a>', 'wowmall' );
		?>
		<div id=wowmall-subscribe-popup class="mfp-hide mfp-with-anim">
			<div class=wowmall-subscribe-popup-pretext>
				<?php echo wp_kses_post( $pretext ); ?>
			</div>
			<?php wowmall()->subscribe_form( $form_id ); ?>
			<?php wowmall_social_nav(); ?>
		</div>
		<?php
		if ( ! empty( $wowmall_options['popup_dont_show_again'] ) ) { ?>
			<div id=wowmall-subscribe-close-popup class=mfp-hide>
				<div class=wowmall-subscribe-close-popup-btns>
					<button
							class="btn btn-dark btn-sm wowmall-dont-show-again"><?php esc_html_e( "Don't show it again", 'wowmall' ); ?></button>
					<button class="btn btn-dark btn-sm"><?php esc_html_e( "Close", 'wowmall' ); ?></button>
				</div>

			</div>
		<?php }
	}
	$popup = ob_get_clean();
	if ( $popup ) {
		wp_send_json_success( $popup );
	}
	wp_send_json_error();
}

function wowmall_ajax_search() {
	$args = array();

	if ( ! empty( $_REQUEST['s'] ) ) {
		$args['s'] = sanitize_text_field( $_REQUEST['s'] );
	}
	if ( ! empty( $_REQUEST['post_type'] ) ) {
		$args['post_type']           = $_REQUEST['post_type'];
		$args['post_status']         = 'publish';
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();
		$args['posts_per_page']      = -1;
		$terms_to_exclude            = array(
			$product_visibility_term_ids['exclude-from-search'],
		);
		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$terms_to_exclude[] = $product_visibility_term_ids['outofstock'];
		}
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $terms_to_exclude,
				'operator' => 'NOT IN',
			),
		);
	}
	$query = new WP_Query( $args );
	ob_start();
	?>
	<div class=wowmall-search-results-inner>
		<?php if ( ! empty( $query->posts ) ) {
			foreach ( $query->posts as $post ) {
				$GLOBALS['post'] = $post;
				setup_postdata( $post ); ?>
				<a href="<?php echo esc_url( get_permalink() ); ?>" <?php post_class(); ?>>
					<?php if ( has_post_thumbnail() ) {
						$size = 'blog_img_size_small';
						if ( ! empty( $_REQUEST['post_type'] ) ) {
							$size = 'woo_img_size_minicart';
						}
						the_post_thumbnail( $size );
					}
					the_title( '<h6 class=wowmall-ajax-search-item-title>', '</h6>' );
					if ( ! empty( $_REQUEST['post_type'] ) ) {
						woocommerce_template_loop_price();
					} ?>
				</a>
			<?php }
		}
		else {
			echo esc_html_x( 'Nothing found', 'top-search', 'wowmall' );
		} ?>
	</div>
	<?php $content = ob_get_clean();
	wp_reset_query();

	wp_send_json_success( $content );
}

function wowmall_get_mc4wp_form() {
	if ( isset( $_POST['_mc4wp_form_id'] ) ) {
		$form_id = (int) $_POST['_mc4wp_form_id'];
		ob_start();
		wowmall()->subscribe_form( $form_id );
		$form = ob_get_clean();
		wp_send_json_success( $form );
	}
	wp_send_json_error();
}

function wowmall_wc_quick_view() {

	$url                              = urldecode( filter_input( INPUT_GET, 'url' ) );
	$id                               = url_to_postid( $url );
	$GLOBALS['product']               = wc_get_product( $id );
	$GLOBALS['post']                  = get_post( $id );
	$GLOBALS['wowmall_wc_quick_view'] = 'true';

	ob_start();

	wc_get_template_part( 'content', 'quick-view-product' );

	$content = '<div class="woomal-wc-quick-view-popup-content woocommerce mfp-with-anim">' . ob_get_clean() . '</div>';

	unset( $GLOBALS['wowmall_wc_quick_view'] );

	wp_send_json_success( $content );
}

function wowmall_ajax_get_loop_thumb() {
	$size           = filter_input( INPUT_GET, 'size', FILTER_SANITIZE_STRING );
	$id             = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
	$attachment_ids = array_filter( explode( ',', get_post_meta( $id, '_product_image_gallery', true ) ) );
	if ( ! empty( $attachment_ids ) ) {
		$id    = array_shift( $attachment_ids );
		$image = wp_get_attachment_image_src( $id, $size, 0 );
		if ( $image ) {
			$srcset     = '';
			$sizes      = '';
			$image_meta = wp_get_attachment_metadata( $id );

			if ( is_array( $image_meta ) ) {
				$size_array = array(
					absint( $image[1] ),
					absint( $image[2] ),
				);
				$srcset     = wp_calculate_image_srcset( $size_array, $image[0], $image_meta, $id );
				$sizes      = wp_calculate_image_sizes( $size_array, $image[0], $image_meta, $id );
			}
			$img = array(
				'src'    => $image[0],
				'width'  => $image[1],
				'height' => $image[2],
				'srcset' => $srcset,
				'sizes'  => $sizes,
			);
			wp_send_json_success( $img );
		}
	}
	die();
}

function wowmall_reset_transients() {
	delete_transient( 'wowmall_privacy_link' );
	delete_transient( 'wowmall_mc4wp_forms' );
}

add_action( 'wp_ajax_wowmall_get_mobile_layout', 'wowmall_get_mobile_layout' );
add_action( 'wp_ajax_nopriv_wowmall_get_mobile_layout', 'wowmall_get_mobile_layout' );

add_action( 'wp_ajax_wowmall_subscribe_popup', 'wowmall_subscribe_popup' );
add_action( 'wp_ajax_nopriv_wowmall_subscribe_popup', 'wowmall_subscribe_popup' );

add_action( 'wp_ajax_wowmall_ajax_search', 'wowmall_ajax_search' );
add_action( 'wp_ajax_nopriv_wowmall_ajax_search', 'wowmall_ajax_search' );

add_action( 'wp_ajax_wowmall_get_mc4wp_form', 'wowmall_get_mc4wp_form' );
add_action( 'wp_ajax_nopriv_wowmall_get_mc4wp_form', 'wowmall_get_mc4wp_form' );

add_action( 'wp_ajax_wowmall_wc_quick_view', 'wowmall_wc_quick_view' );
add_action( 'wp_ajax_nopriv_wowmall_wc_quick_view', 'wowmall_wc_quick_view' );

add_action( 'wp_ajax_wowmall_get_loop_thumb', 'wowmall_ajax_get_loop_thumb' );
add_action( 'wp_ajax_nopriv_wowmall_get_loop_thumb', 'wowmall_ajax_get_loop_thumb' );


add_action( "redux/options/wowmall_options/saved", 'wowmall_reset_transients' );

add_action( "redux/options/wowmall_options/reset", 'wowmall_reset_transients' );

add_action( "redux/options/wowmall_options/section/reset", 'wowmall_reset_transients' );

add_action( 'save_post', 'wowmall_reset_transients' );

add_action( 'trashed_post', 'wowmall_reset_transients' );

add_action( 'after_switch_theme', 'wowmall_reset_transients' );

if ( wowmall()->is_woocommerce_activated() ) {
	require_once( WOWMALL_THEME_ADMIN_DIR . 'wc-products-ajax.php' );
}
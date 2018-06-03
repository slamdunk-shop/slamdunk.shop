<?php

if ( ! class_exists( 'wowmallLookbook' ) ) {

	class wowmallLookbook {

		protected static $_instance = null;

		public static $posts = null;

		public function __construct() {

			add_action( 'init', array(
				$this,
				'register_post_type',
			) );

			add_action( 'add_meta_boxes', array(
				$this,
				'add_meta_boxes',
			), 30 );

			add_action( 'wp_ajax_wowmall_lookbook_set_product', array(
				$this,
				'set_product',
			) );

			add_action( 'wp_ajax_wowmall_lookbook_unset_product', array(
				$this,
				'unset_product',
			) );

			add_action( 'wp_ajax_wowmall_lookbook_get_product', array(
				$this,
				'get_product',
			) );

			add_action( 'wp_ajax_nopriv_wowmall_lookbook_get_product', array(
				$this,
				'get_product',
			) );

			add_shortcode( 'wowmall_lookbook', array(
				$this,
				'shortcode',
			) );

			if ( is_admin() ) {
				add_action( 'vc_before_init', array(
					$this,
					'vc_map',
				) );
			}

			add_action( 'admin_enqueue_scripts', array(
				$this,
				'admin_enqueue_scripts',
			) );

			add_action( 'wp_enqueue_scripts', array(
				$this,
				'wp_enqueue_scripts',
			) );
		}

		public function wp_enqueue_scripts() {
			if( is_page() ) {
				global $post;
				if ( has_shortcode( $post->post_content, 'wowmall_lookbook' ) ) {
					wp_enqueue_style( 'wowmall-lookbook-post' );
					wp_enqueue_script( 'wowmall-lookbook-post-front' );
				}
			}
		}

		public function admin_enqueue_scripts() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';
			if ( current_user_can( 'edit_posts' ) && $screen_id === 'lookbook' ) {
				wp_enqueue_style( 'wowmall-lookbook-post' );
				wp_enqueue_script( 'wowmall-lookbook-post' );
			}
		}

		public function register_post_type() {

			$labels = array(
				'name'               => esc_html__( 'Lookbook', 'wowmall-shortcodes' ),
				'singular_name'      => esc_html__( 'Lookbook', 'wowmall-shortcodes' ),
				'add_new'            => esc_html__( 'Add New', 'wowmall-shortcodes' ),
				'add_new_item'       => esc_html__( 'Add New Lookbook', 'wowmall-shortcodes' ),
				'edit_item'          => esc_html__( 'Edit Lookbook', 'wowmall-shortcodes' ),
				'new_item'           => esc_html__( 'New Lookbook', 'wowmall-shortcodes' ),
				'view_item'          => esc_html__( 'View Lookbook', 'wowmall-shortcodes' ),
				'search_items'       => esc_html__( 'Search Lookbook', 'wowmall-shortcodes' ),
				'not_found'          => esc_html__( 'No Lookbook found', 'wowmall-shortcodes' ),
				'not_found_in_trash' => esc_html__( 'No Lookbook found in Trash', 'wowmall-shortcodes' ),
				'parent_item_colon'  => '',
			);

			$args = array(
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'query_var'          => true,
				'capability_type'    => 'post',
				'hierarchical'       => false,
				'menu_position'      => 10,
				'supports'           => array(
					'title',
					'editor',
					'thumbnail',
				),
			);

			register_post_type( 'lookbook', $args );
		}

		public function add_meta_boxes() {
			add_meta_box( 'wowmall-lookbook-slide', __( 'Lookbook Slider', 'wowmall-shortcodes' ), array(
				$this,
				'metabox_output',
			), 'lookbook', 'normal', 'high' );
		}

		public function metabox_output( $post ) {
			if ( has_post_thumbnail( $post ) ) { ?>
				<div class="wowmall-lookbook-slide-wrapper">
					<?php echo get_the_post_thumbnail( $post, 'original' );
					$lookbook = get_post_meta( $post->ID, 'wowmall_lookbook', true );
					if ( ! empty( $lookbook ) ) {
						foreach ( $lookbook as $product_id => $place ) { ?>
							<span class="wowmall-lookbook-point" id="<?php echo $product_id; ?>"
							      style="left: <?php echo $place[0]; ?>%; top: <?php echo $place[1]; ?>%;"></span>
						<?php }
					}
					?>
					<div class="wowmall-lookbook-popup">
						<p>
							<label
								for="wowmall-lookbook-popup-select"><?php esc_html_e( 'Select Product', 'wowmall-shortcodes' ); ?></label>
						</p>
						<p>
							<select id="wowmall-lookbook-popup-select">
								<?php
								$products = get_posts( array(
									'post_type'      => 'product',
									'posts_per_page' => - 1,
								) );
								foreach ( $products as $product ) {
									$disabled = '';
									if ( ! empty( $lookbook ) && array_key_exists( $product->ID, $lookbook ) ) {
										$disabled = ' disabled';
									}
									?>
									<option
										value="<?php echo $product->ID; ?>"<?php echo $disabled; ?>><?php echo $product->post_title . ' (' . $product->ID . ')'; ?></option>
								<?php }
								?>
							</select>
							<input type="hidden" id="wowmall-lookbook-popup-prev-id" value="">
						</p>
						<p>
							<button class="button btn-cancel"
							        type="button"><?php esc_html_e( 'Cancel', 'wowmall-shortcodes' ); ?></button>
							<button class="button button-primary btn-ok"
							        type="button"><?php esc_html_e( 'Ok', 'wowmall-shortcodes' ); ?></button>
							<button class="button button-danger btn-remove"
							        type="button"><?php esc_html_e( 'Remove', 'wowmall-shortcodes' ); ?></button>
						</p>
					</div>
				</div>
			<?php }
		}

		public function set_product() {
			$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error();
			}
			$prev_id    = isset( $_POST['prev_id'] ) ? absint( $_POST['prev_id'] ) : 0;
			$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
			$placeX     = isset( $_POST['placeX'] ) ? filter_var( $_POST['placeX'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : 0;
			$placeY     = isset( $_POST['placeY'] ) ? filter_var( $_POST['placeY'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : 0;
			$lookbook   = get_post_meta( $id, 'wowmall_lookbook', true );
			$value      = array();
			if ( $lookbook ) {
				$value = $lookbook;
				if ( $prev_id && $prev_id !== $product_id ) {
					unset( $value[ $prev_id ] );
				}
			}
			$value[ $product_id ] = array(
				$placeX,
				$placeY,
			);
			$update               = update_post_meta( $id, 'wowmall_lookbook', $value );
			if ( $update ) {
				wp_send_json_success();
			}
			wp_send_json_error();
		}

		public function unset_product() {
			$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error();
			}
			$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
			$lookbook   = get_post_meta( $id, 'wowmall_lookbook', true );
			if ( empty( $lookbook[ $product_id ] ) ) {
				wp_send_json_error();
			}
			unset( $lookbook[ $product_id ] );
			$update = update_post_meta( $id, 'wowmall_lookbook', $lookbook );
			if ( $update ) {
				wp_send_json_success();
			}
			wp_send_json_error();
		}

		public function shortcode( $atts = array() ) {

			$atts = shortcode_atts( array(
				'id' => 0,

			), $atts );

			if ( empty( $atts['id'] ) ) {
				return '';
			}

			$post = get_post( $atts['id'] );

			if ( ! has_post_thumbnail( $post ) ) {
				return '';
			}
			ob_start();
			?>
			<div class="wowmall-lookbook-slide-wrapper">
				<?php echo get_the_post_thumbnail( $post, 'original' );
				$lookbook = get_post_meta( $post->ID, 'wowmall_lookbook', true );
				if ( ! empty( $lookbook ) ) {
					foreach ( $lookbook as $product_id => $place ) { ?>
						<span class="wowmall-lookbook-point" id="<?php echo $product_id; ?>"
						      style="left: <?php echo $place[0]; ?>%; top: <?php echo $place[1]; ?>%;"></span>
					<?php }
				}
				?>
			</div>
			<?php
			return ob_get_clean();
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function vc_map() {

			$params = array(
				'name'        => esc_html__( 'Wowmall LookBook', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_lookbook',
				'description' => esc_html__( 'Set LookBook', 'wowmall-shortcodes' ),
				'category'    => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'params'      => array(
					array(
						'type'        => 'dropdown',
						'heading'     => esc_html__( 'LookBook Post', 'wowmall-shortcodes' ),
						'param_name'  => 'id',
						'value'       => $this->get_lookbook_posts(),
						'description' => esc_html__( 'Select LookBook Post', 'wowmall-shortcodes' ),
					),
				),
			);

			vc_map( $params );
		}

		public function get_lookbook_posts() {
			if ( is_null( self::$posts ) ) {

				$posts = get_posts( array(
					'post_type'      => 'lookbook',
					'posts_per_page' => - 1,
				) );

				if ( ! empty( $posts ) ) {
					foreach ( $posts as $post ) {
						self::$posts[ $post->post_title . ' (' . $post->ID . ')' ] = $post->ID;
					}
				}

			}

			return self::$posts;
		}

		public function get_product() {
			$id                               = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
			$product                          = wc_get_product( $id );
			$GLOBALS['product']               = $product;
			$GLOBALS['post']                  = $product->post;
			$GLOBALS['wowmall_wc_quick_view'] = 'true';
			$GLOBALS['wowmall_wc_lookbook']   = 'true';
			ob_start(); ?>
			<div class="wowmall-lookbook-popup-content woocommerce" data-id="<?php the_ID(); ?>">
				<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>"
				     id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
					<a href="<?php the_permalink(); ?>" class="product-thumb">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'woo_img_size_cart' );
						} else {
							echo wc_placeholder_img( 'woo_img_size_cart' );
						} ?>
					</a>
					<div class="product-content">
						<?php wowmall_woocommerce_show_product_flashes(); ?>
						<h6 class="wc-loop-product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h6>
						<?php
						woocommerce_template_loop_price();
						if ( $product->is_purchasable() && $product->is_in_stock() && 'simple' === $product->get_type() ) { ?>
							<p class="qty_p"><?php esc_html_e( 'Qty:', 'wowmall-shortcodes' ); ?></p>
							<?php
							woocommerce_template_single_add_to_cart();
						}
						?>
					</div>
				</div><!-- #product-<?php the_ID(); ?> -->
				<button type="button" class="close"></button>
			</div>
			<?php $content = ob_get_clean();

			unset( $GLOBALS['product'] );
			unset( $GLOBALS['post'] );
			unset( $GLOBALS['wowmall_wc_quick_view'] );
			unset( $GLOBALS['wowmall_wc_lookbook'] );
			wp_send_json_success( $content );
		}
	}

	wowmallLookbook::instance();
}
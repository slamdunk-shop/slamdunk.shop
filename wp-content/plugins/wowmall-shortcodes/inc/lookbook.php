<?php

if ( ! class_exists( 'wowmallLookbook' ) ) {

	class wowmallLookbook {

		protected static $_instance = null;

		public function __construct() {

			add_action( 'init', array(
				$this,
				'register_post_type',
			) );

			add_shortcode( 'wowmall_lookbook', array(
				$this,
				'shortcode',
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
	}

	wowmallLookbook::instance();
}
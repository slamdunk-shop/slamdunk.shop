<?php

if ( ! class_exists( 'wowmallGallery' ) ) {

	class wowmallGallery {

		protected static $_instance = null;

		public static $front_placeholder;

		public function __construct() {

			add_action( 'init', array(
				$this,
				'register_post_type',
			) );
			add_action( 'init', array(
				$this,
				'register_taxonomy',
			) );
			add_action( 'init', array(
				$this,
				'add_tags_gallery',
			) );
			add_shortcode( 'wowmall_gallery', array(
				$this,
				'shortcode',
			) );

			add_action( 'wowmall_gallery_subcategories', array(
				$this,
				'gallery_subcategories',
			) );

			add_action( 'wowmall_gallery_subcategory_thumbnail', array(
				$this,
				'category_thumbnail',
			) );

			add_action( 'wowmall_gallery_subcategory_caption', array(
				$this,
				'category_caption',
			) );

			add_action( 'wowmall_gallery_item_thumbnail', array(
				$this,
				'item_thumbnail',
			) );

			add_action( 'wowmall_gallery_item_caption', array(
				$this,
				'item_caption',
			) );

			add_action( 'pre_get_posts', array(
				$this,
				'pre_get_posts',
			) );

		}

		public function setup_front_placeholder() {

			if( is_null( self::$front_placeholder ) ) {

				global $wowmall_options;

				$color1 = ! empty( $wowmall_options['accent_color_1'] ) ? str_replace( '#', '', $wowmall_options['accent_color_1'] ) : 'fc6f38';

				$color2 = ! empty( $wowmall_options['accent_color_1'] ) ? str_replace( '#', '', $wowmall_options['accent_color_2'] ) : '222';

				self::$front_placeholder = apply_filters( 'wowmall_gallery_placeholder', '<img width="%1$s" height="%2$s" src="https://placeholdit.imgix.net/~text?txtsize=30&bg=' . $color2 . '&txtclr=' . $color1 . '&w=%1$s&h=%2$s&txt=%3$s" alt="%3$s">' );
			}
		}

		public function pre_get_posts( $q ) {

			global $wowmall_options;

			$vars = $q->query_vars;
			if ( ( ! empty( $vars['post_type'] ) && 'gallery' === $vars['post_type'] ) && ( ! empty( $vars['page'] ) && '' !== $vars['page'] ) && ( ! empty( $vars['name'] ) && 'page' === $vars['name'] ) && ( isset( $vars['pagename'] ) && '' === $vars['pagename'] ) ) {
				$page = array_search( 'page', $vars );
				$q->set( 'page', '' );
				$q->set( 'pagename', $page );
				$q->set( 'name', '' );
				$q->set( 'post_type', 'page' );
				$q->is_single         = false;
				$q->is_page           = true;
				$q->queried_object    = get_page_by_path( $page );
				$q->queried_object_id = (int) $q->queried_object->ID;
			}
			if ( ! empty( $vars['gallery-cat'] ) ) {
				if ( ! empty( $wowmall_options['gallery_posts_per_page'] ) ) {
					$q->set( 'posts_per_page', $wowmall_options['gallery_posts_per_page'] );
				}
				$orderby = ! empty( $wowmall_options['gallery_orderby'] ) ? $wowmall_options['gallery_orderby'] : 'menu_order';
				$order   = ! empty( $wowmall_options['gallery_order'] ) ? $wowmall_options['gallery_order'] : 'ASC';
				$q->set( 'orderby', $orderby );
				$q->set( 'order', $order );
			}
		}

		public function register_post_type() {

			$labels = array(
				'name'               => esc_html__( 'Gallery', 'wowmall-shortcodes' ),
				'singular_name'      => esc_html__( 'Gallery', 'wowmall-shortcodes' ),
				'add_new'            => esc_html__( 'Add New', 'wowmall-shortcodes' ),
				'add_new_item'       => esc_html__( 'Add New Gallery', 'wowmall-shortcodes' ),
				'edit_item'          => esc_html__( 'Edit Gallery', 'wowmall-shortcodes' ),
				'new_item'           => esc_html__( 'New Gallery', 'wowmall-shortcodes' ),
				'view_item'          => esc_html__( 'View Gallery', 'wowmall-shortcodes' ),
				'search_items'       => esc_html__( 'Search Gallery', 'wowmall-shortcodes' ),
				'not_found'          => esc_html__( 'No Gallery found', 'wowmall-shortcodes' ),
				'not_found_in_trash' => esc_html__( 'No Gallery found in Trash', 'wowmall-shortcodes' ),
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

			register_post_type( 'gallery', $args );
		}

		public function add_tags_gallery() {
			register_taxonomy_for_object_type( 'post_tag', 'gallery' );
		}

		public function register_taxonomy() {
			$labels = array(
				'name'              => esc_html_x( 'Gallery Categories', 'gallery categories', 'wowmall-shortcodes' ),
				'singular_name'     => esc_html_x( 'Gallery Category', 'gallery category', 'wowmall-shortcodes' ),
				'search_items'      => esc_html__( 'Search Gallery Categories', 'wowmall-shortcodes' ),
				'all_items'         => esc_html__( 'All Gallery Categories', 'wowmall-shortcodes' ),
				'parent_item'       => esc_html__( 'Parent Gallery Category', 'wowmall-shortcodes' ),
				'parent_item_colon' => esc_html__( 'Parent Gallery Category:', 'wowmall-shortcodes' ),
				'edit_item'         => esc_html__( 'Edit Gallery Category', 'wowmall-shortcodes' ),
				'update_item'       => esc_html__( 'Update Gallery Category', 'wowmall-shortcodes' ),
				'add_new_item'      => esc_html__( 'Add New Gallery Category', 'wowmall-shortcodes' ),
				'new_item_name'     => esc_html__( 'New Gallery Category Name', 'wowmall-shortcodes' ),
				'menu_name'         => esc_html__( 'Gallery Category', 'wowmall-shortcodes' ),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
			);

			register_taxonomy( 'gallery-cat', array( 'gallery' ), $args );
		}

		public function gallery_subcategories( $args = array() ) {

			global $wp_query, $wowmall_options;

			$defaults = array(
				'size'          => 'gallery_img_size_masonry_small',
				'element_class' => '',
				'display_type'  => ! empty( $wowmall_options['gallery_display_type'] ) ? $wowmall_options['gallery_display_type'] : 'both',
				'orderby'       => ! empty( $wowmall_options['gallery_orderby'] ) ? $wowmall_options['gallery_orderby'] : 'menu_order',
				'order'         => ! empty( $wowmall_options['gallery_order'] ) ? $wowmall_options['gallery_order'] : 'ASC',
			);

			$args = wp_parse_args( $args, $defaults );

			// Main query only
			if ( ! is_main_query() ) {
				return;
			}

			// Don't show when filtering, searching or when on page > 1 and ensure we're on a product archive
			if ( is_search() || is_filtered() || is_paged() ) {
				return;
			}

			$term      = get_queried_object();
			$parent_id = '';
			if ( $term ) {

				$parent_id            = empty( $term->term_id ) ? 0 : $term->term_id;
				$args['display_type'] = get_term_meta( $parent_id, 'display_type', true );
			}

			switch ( $args['display_type'] ) {
				case 'images' :
					return;
					break;
				case '' :
					if ( ! empty( $wowmall_options['gallery_display_type'] ) && 'images' === $wowmall_options['gallery_display_type'] ) {
						return;
					}
					break;
			}

			$list_args = array(
				'parent'       => $parent_id,
				'hide_empty'   => 0,
				'hierarchical' => 1,
				'taxonomy'     => 'gallery-cat',
				'pad_counts'   => 1,
			);

			if ( $args['orderby'] == 'menu_order' ) {
				$list_args['menu_order'] = $args['order'];
			} else {
				$list_args['order']   = $args['order'];
				$list_args['orderby'] = $args['orderby'];
			}

			$cats = get_categories( $list_args );

			if ( ! empty( $cats ) ) {
				foreach ( $cats as $cat ) { ?>
					<div class="wowmall-gallery-cat-item wowmall-gallery-item<?php echo $args['element_class']; ?>">
						<?php if ( locate_template( 'template-parts/wowmall-gallery-cat.php' ) != '' ) {
							$cat->thumb_size             = $args['size'];
							$GLOBALS['wowmall_category'] = $cat;
							get_template_part( 'template-parts/wowmall-gallery-cat' );
						} else { ?>
							<figure>
								<?php do_action( 'wowmall_gallery_subcategory_thumbnail', $cat ); ?>
								<?php do_action( 'wowmall_gallery_subcategory_caption', $cat ); ?>
							</figure>
							<?php
						} ?>
					</div>
				<?php }
			}

			switch ( $args['display_type'] ) {
				case 'subcategories' :
					$wp_query->post_count    = 0;
					$wp_query->max_num_pages = 0;
					break;
				case '' :
					if ( ! empty( $wowmall_options['gallery_display_type'] ) && 'subcategories' === $wowmall_options['gallery_display_type'] ) {
						$wp_query->post_count    = 0;
						$wp_query->max_num_pages = 0;
					}
					break;
			}
		}

		public function category_thumbnail( $cat ) {
			$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
			if ( $thumbnail_id ) {
				$image = wp_get_attachment_image( $thumbnail_id, $cat->thumb_size );
			} else {
				global $_wp_additional_image_sizes;
				$size   = $_wp_additional_image_sizes[ $cat->thumb_size ];
				$width  = $size['width'];
				$height = 9999 !== $size['height'] ? $size['height'] : $width;

				$image = sprintf( self::$front_placeholder, $width, $height, $cat->name );
			} ?>
			<a class="zoom-cat-gallery" href="<?php echo esc_url( get_term_link( $cat, 'gallery-cat' ) ); ?>"
			   data-cid="<?php echo esc_attr( $cat->term_id ); ?>">
				<?php echo $image; ?>
			</a>
		<?php }

		public function category_caption( $cat ) { ?>
			<figcaption>
				<?php
				if ( 0 < $cat->count ) { ?>
					<div class="gallery-cat-count">
						<?php $count_text = _n( '%s Photo', '%s Photos', $cat->count, 'wowmall-shortcodes' );
						printf( $count_text, $cat->count ); ?>
					</div>
				<?php } ?>
				<div class="item-content">
					<h3 class="title">
						<a href="<?php echo esc_url( get_term_link( $cat, 'gallery-cat' ) ); ?>"><?php echo $cat->name; ?></a>
					</h3>
				</div>
			</figcaption>
		<?php }

		public function item_thumbnail() {
			global $post;
			if ( has_post_thumbnail() ) { ?>
				<a href="<?php echo esc_url( wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' ) ); ?>"
				   class="zoom-gallery" title="<?php echo esc_attr( get_the_title() ); ?>">
					<?php the_post_thumbnail( $post->wowmall_thumb_size ); ?>
				</a>
			<?php }
		}

		public function item_caption() {
			add_filter( 'tag_link', array(
				$this,
				'tag_link',
			), 10, 2 );
			?>
			<figcaption>
				<div class="item-content">
					<h3 class="title">
						<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a>
					</h3>
					<?php the_tags( '<div class="gallery-tags">', ', ', '</div>' ); ?>
				</div>
			</figcaption>
			<?php
			remove_filter( 'tag_link', array(
				$this,
				'tag_link',
			), 10 );
		}

		public function tag_link( $termlink, $term_id ) {
			$termlink = add_query_arg( array( 'post_type' => 'gallery' ), $termlink );

			return $termlink;
		}

		public function shortcode( $atts = array() ) {

			global $wp_query, $wp_the_query, $wowmall_options;

			$atts = shortcode_atts( array(
				'layout'         => ! empty( $wowmall_options['gallery_layout_type'] ) ? $wowmall_options['gallery_layout_type'] : 'masonry',
				'cols'           => ! empty( $wowmall_options['gallery_columns'] ) ? $wowmall_options['gallery_columns'] : 4,
				'posts_per_page' => ! empty( $wowmall_options['gallery_posts_per_page'] ) ? $wowmall_options['gallery_posts_per_page'] : '',
				'display_type'   => ! empty( $wowmall_options['gallery_display_type'] ) ? $wowmall_options['gallery_display_type'] : '',
				'orderby'        => ! empty( $wowmall_options['gallery_orderby'] ) ? $wowmall_options['gallery_orderby'] : 'menu_order',
				'order'          => ! empty( $wowmall_options['gallery_order'] ) ? $wowmall_options['gallery_order'] : 'ASC',

			), $atts );

			$atts['cols'] = (int) $atts['cols'];

			if ( 4 < $atts['cols'] ) {
				$atts['cols'] = 4;
			} elseif ( 3 > $atts['cols'] ) {
				$atts['cols'] = 3;
			}
			$element_class   = '';
			$container_class = 'wowmall-gallery-container ';

			if ( ! in_array( $atts['layout'], array(
				'grid',
				'masonry',
			) )
			) {
				$atts['layout'] = 'masonry';
			}

			if ( 'grid' === $atts['layout'] ) {
				$element_class .= ' col-sm-' . 12 / $atts['cols'];
				$container_class .= 'row wowmall-gallery-container-grid';
			} elseif ( 'masonry' === $atts['layout'] ) {
				$container_class .= 'wowmall-gallery-container-masonry cols-' . $atts['cols'];
			}

			$size = 'gallery_img_size_' . $atts['layout'] . '_';
			$size = 4 === $atts['cols'] ? $size . 'small' : $size . 'medium';

			$args           = array(
				'post_type' => 'gallery',
				'orderby'   => $atts['orderby'],
				'order'     => $atts['order'],
			);

			if( '' !== $atts['posts_per_page'] && 'inherit' !== $atts['posts_per_page'] ) {
				$args['posts_per_page'] = (int) $atts['posts_per_page'];
			}
			$main_query     = $wp_query;
			$main_the_query = $wp_the_query;

			if ( ! empty( $main_query->query['post_type'] ) && 'gallery' === $main_query->query['post_type'] && ! empty( $main_query->query['page'] ) ) {
				$args['paged'] = $main_query->query['page'];
			}

			$posts    = new WP_Query( $args );
			$wp_query = $wp_the_query = $posts;
			$this->setup_front_placeholder();
			do_action('wowmall_before_gallery');
			ob_start();
			?>

			<div id="wowmall-gallery">
				<div class="wowmall-gallery-wrapper">
					<div class="<?php echo $container_class; ?>" data-cols="<?php echo $atts['cols']; ?>">
						<?php
						do_action( 'wowmall_gallery_subcategories', array(
							'size'          => $size,
							'element_class' => $element_class,
							'display_type'  => $atts['display_type'],
							'orderby'       => $atts['orderby'],
							'order'         => $atts['order'],
						) );
						if ( have_posts() ) {
							while ( have_posts() ) {

								the_post();
								global $post;
								$post->wowmall_thumb_size = $size; ?>
								<div class="wowmall-gallery-item<?php echo $element_class; ?>">
									<?php if ( locate_template( 'template-parts/wowmall-gallery-item.php' ) != '' ) {
										get_template_part( 'template-parts/wowmall-gallery-item' );
									} else {
										?>
										<figure>
											<?php do_action( 'wowmall_gallery_item_thumbnail' ); ?>
											<?php do_action( 'wowmall_gallery_item_caption' ); ?>
										</figure>
										<?php
									} ?>
								</div>
							<?php }
						}
						?></div>
				</div>
			</div>
			<?php
			the_posts_pagination( array(
				'prev_text' => '<i class="myfont-left-open-2"></i>',
				'next_text' => '<i class="myfont-right-open-2"></i>',
				'type'      => 'list',
			) ); ?>
			<?php 
			$content = ob_get_clean();
			wp_reset_query();
			$wp_query     = $main_query;
			$wp_the_query = $main_the_query;

			return $content;
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallGallery::instance();
}
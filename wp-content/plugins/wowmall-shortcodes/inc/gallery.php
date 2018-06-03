<?php

if ( ! class_exists( 'wowmallGallery' ) ) {

	class wowmallGallery {

		protected static $_instance = null;

		public static $placeholder, $front_placeholder;

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
			add_action( 'gallery-cat_add_form_fields', array(
				$this,
				'add_gallery_cat_fields',
			) );
			add_action( 'gallery-cat_edit_form_fields', array(
				$this,
				'edit_gallery_cat_fields',
			) );
			add_action( 'created_term', array(
				$this,
				'save_gallery_cat_fields',
			), 10, 3 );
			add_action( 'edit_term', array(
				$this,
				'save_gallery_cat_fields',
			), 10, 3 );
			add_filter( 'manage_edit-gallery-cat_columns', array(
				$this,
				'gallery_cat_columns',
			) );
			add_filter( 'manage_gallery-cat_custom_column', array(
				$this,
				'gallery_cat_column',
			), 10, 3 );
			add_action( 'create_term', array(
				$this,
				'create_term',
			), 5, 3 );
			add_action( 'wp_ajax_wowmall_gallery_term_ordering', array(
				$this,
				'term_ordering',
			) );
			add_action( 'wp_ajax_nopriv_wowmall_gallery_term_ordering', array(
				$this,
				'term_ordering',
			) );
			add_action( 'wp_ajax_wowmall_gallery_ordering', array(
				$this,
				'gallery_ordering',
			) );
			add_action( 'wp_ajax_nopriv_wowmall_gallery_ordering', array(
				$this,
				'gallery_ordering',
			) );
			add_filter( 'terms_clauses', array(
				$this,
				'terms_clauses',
			), 10, 3 );

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

			if( is_admin() ) {
				add_action( 'vc_before_init', array(
					$this,
					'vc_map',
				) );
			}

			add_action( 'pre_get_posts', array(
				$this,
				'pre_get_posts',
			) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			add_filter( 'views_edit-gallery', array( $this, 'gallery_sorting_link' ) );

			add_filter( 'manage_gallery_posts_columns', array( $this, 'gallery_columns' ) );

			add_action( 'manage_gallery_posts_custom_column', array( $this, 'render_gallery_columns' ), 2 );

			self::$placeholder = 'https://placeholdit.imgix.net/~text?txtsize=15&txt=IMG&w=60&h=60';

		}

		public function setup_front_placeholder() {

			if( is_null( self::$front_placeholder ) ) {

				global $wowmall_options;

				$color1 = ! empty( $wowmall_options['accent_color_1'] ) ? str_replace( '#', '', $wowmall_options['accent_color_1'] ) : 'fc6f38';

				$color2 = ! empty( $wowmall_options['accent_color_1'] ) ? str_replace( '#', '', $wowmall_options['accent_color_2'] ) : '222';

				self::$front_placeholder = apply_filters( 'wowmall_gallery_placeholder', '<img width="%1$s" height="%2$s" src="https://placeholdit.imgix.net/~text?txtsize=30&bg=' . $color2 . '&txtclr=' . $color1 . '&w=%1$s&h=%2$s&txt=%3$s" alt="%3$s">' );
			}
		}

		public function admin_enqueue_scripts() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';
			if ( current_user_can( 'edit_posts' ) && $screen_id == 'edit-gallery' ) {
				wp_enqueue_script( 'wowmall-gallery-ordering', wowmallShortcodes::$pluginurl . '/assets/js/gallery-sort.js', array( 'jquery-ui-sortable' ), null, true );
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

		public function start_script() { ?>

			function wowmall_reset_gallery_cat_image() {
			jQuery( '#gallery_cat_thumbnail' ).attr( 'src', '<?php echo self::$placeholder; ?>' );
			jQuery( '#gallery_cat_thumbnail_id' ).val( '' );
			jQuery( '.remove_image_button' ).hide();
			}

			if ( jQuery( '#gallery_cat_thumbnail_id' ).val() ) {
			jQuery( '.remove_image_button' ).show();
			}

			var file_frame;

			jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

			event.preventDefault();

			if ( file_frame ) {
			file_frame.open();
			return;
			}

			file_frame = wp.media.frames.downloadable_file = wp.media({
			title: '<?php esc_html_e( 'Choose an image', 'wowmall-shortcodes' ); ?>',
			multiple: false
			});

			file_frame.on( 'select', function() {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON();

			jQuery( '#gallery_cat_thumbnail_id' ).val( attachment.id );
			jQuery( '#gallery_cat_thumbnail' ).attr( 'src', attachment.sizes.thumbnail.url );
			jQuery( '.remove_image_button' ).show();
			}).open();
			});

			jQuery( document ).on( 'click', '.remove_image_button', function() {
			wowmall_reset_gallery_cat_image();
			return false;
			});
		<?php }

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

		function add_gallery_cat_fields() {
			wp_enqueue_script( 'wowmall-shortcodes-sort', wowmallShortcodes::$pluginurl . '/assets/js/sort.js', array( 'jquery-ui-selectmenu' ), null, true );
			?>
			<div class="form-field term-display-type-wrap">
				<label for="display_type"><?php esc_html_e( 'Display type', 'wowmall-shortcodes' ); ?></label>
				<select id="display_type" name="display_type" class="postform">
					<option value=""><?php esc_html_e( 'Default', 'wowmall-shortcodes' ); ?></option>
					<option value="images"><?php esc_html_e( 'Images', 'wowmall-shortcodes' ); ?></option>
					<option value="subcategories"><?php esc_html_e( 'Subcategories', 'wowmall-shortcodes' ); ?></option>
					<option value="both"><?php esc_html_e( 'Both', 'wowmall-shortcodes' ); ?></option>
				</select>
			</div>
			<div class="form-field term-thumbnail-wrap">
				<label><?php esc_html_e( 'Thumbnail', 'wowmall-shortcodes' ); ?></label>
				<img src="<?php echo self::$placeholder; ?>" width="60" height="60" id="gallery_cat_thumbnail" style="float:left;margin-right:10px">
				<div style="line-height:60px">
					<input type="hidden" id="gallery_cat_thumbnail_id" name="gallery_cat_thumbnail_id">
					<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'wowmall-shortcodes' ); ?></button>
					<button type="button" class="remove_image_button button" style="display:none"><?php esc_html_e( 'Remove image', 'wowmall-shortcodes' ); ?></button>
				</div>
				<script type="text/javascript">
					<?php $this->start_script(); ?>
					jQuery( document ).ajaxComplete( function ( event, request, options ) {
						if ( request && 4 === request.readyState && 200 === request.status
							&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {
							var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
							if ( ! res || res.errors ) {
								return;
							}
							wowmall_reset_gallery_cat_image();
							jQuery( '#display_type' ).val( '' );
							return;
						}
					} );
				</script>
				<div class="clear"></div>
			</div>
			<?php
		}

		public function edit_gallery_cat_fields( $term ) {
			$display_type = get_term_meta( $term->term_id, 'display_type', true );
			$thumbnail_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );

			if ( $thumbnail_id ) {
				$image = esc_url( wp_get_attachment_thumb_url( $thumbnail_id ) );
			} else {
				$image = self::$placeholder;
			} ?>
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php esc_html_e( 'Display type', 'wowmall-shortcodes' ); ?></label></th>
				<td>
					<select id="display_type" name="display_type" class="postform">
						<option
							value="" <?php selected( '', $display_type ); ?>><?php esc_html_e( 'Default', 'wowmall-shortcodes' ); ?></option>
						<option
							value="images" <?php selected( 'images', $display_type ); ?>><?php esc_html_e( 'Images', 'wowmall-shortcodes' ); ?></option>
						<option
							value="subcategories" <?php selected( 'subcategories', $display_type ); ?>><?php esc_html_e( 'Subcategories', 'wowmall-shortcodes' ); ?></option>
						<option
							value="both" <?php selected( 'both', $display_type ); ?>><?php esc_html_e( 'Both', 'wowmall-shortcodes' ); ?></option>
					</select>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php esc_html_e( 'Thumbnail', 'wowmall-shortcodes' ); ?></label></th>
				<td>
					<img src="<?php echo $image; ?>" width="60" height="60" id="gallery_cat_thumbnail"
					     style="float:left;margin-right:10px">
					<div style="line-height:60px">
						<input type="hidden" id="gallery_cat_thumbnail_id" name="gallery_cat_thumbnail_id"
						       value="<?php echo $thumbnail_id; ?>">
						<button type="button"
						        class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'wowmall-shortcodes' ); ?></button>
						<button type="button" class="remove_image_button button"
						        style="display:none"><?php esc_html_e( 'Remove image', 'wowmall-shortcodes' ); ?></button>
					</div>
					<script type="text/javascript">
						<?php $this->start_script(); ?>
					</script>
					<div class="clear"></div>
				</td>
			</tr>
			<?php
		}

		public function save_gallery_cat_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
			if ( isset( $_POST['display_type'] ) && 'gallery-cat' === $taxonomy ) {
				update_term_meta( $term_id, 'display_type', esc_attr( $_POST['display_type'] ) );
			}
			if ( isset( $_POST['gallery_cat_thumbnail_id'] ) && 'gallery-cat' === $taxonomy ) {
				update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['gallery_cat_thumbnail_id'] ) );
			}
		}

		public function gallery_cat_columns( $columns ) {
			$new_columns = array();

			if ( isset( $columns['cb'] ) ) {
				$new_columns['cb'] = $columns['cb'];
				unset( $columns['cb'] );
			}

			$new_columns['thumb'] = esc_html__( 'Image', 'wowmall-shortcodes' );

			return array_merge( $new_columns, $columns );
		}

		public function gallery_cat_column( $columns, $column, $id ) {

			if ( 'thumb' == $column ) {

				$thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

				if ( $thumbnail_id ) {
					$image = esc_url( wp_get_attachment_thumb_url( $thumbnail_id ) );
				} else {
					$image = self::$placeholder;
				}

				$columns .= '<img src="' . $image . '" alt="' . esc_attr__( 'Thumbnail', 'wowmall-shortcodes' ) . '" class="wp-post-image" height="48" width="48">';

			}

			return $columns;
		}

		public function create_term( $term_id, $tt_id = '', $taxonomy = '' ) {

			if ( 'gallery-cat' != $taxonomy ) {
				return;
			}

			update_term_meta( $term_id, 'order', 0 );
		}

		public static function term_ordering() {

			// check permissions again and make sure we have what we need
			if ( ! current_user_can( 'edit_posts' ) || empty( $_POST['id'] ) ) {
				die( - 1 );
			}

			$id      = $_POST['id'];
			$next_id = isset( $_POST['nextid'] ) && $_POST['nextid'] ? $_POST['nextid'] : null;
			$term    = get_term_by( 'term_taxonomy_id', $id );

			if ( ! $id || ! $term ) {
				die( 0 );
			}

			self::reorder_terms( $term, $next_id );

			$children = get_terms( 'gallery-cat', "child_of=$id&menu_order=ASC&hide_empty=0" );

			if ( $term && sizeof( $children ) ) {
				echo 'children';
				die();
			}
		}

		public function reorder_terms( $the_term, $next_id, $index = 0, $terms = null ) {
			if ( ! $terms ) {
				$terms = get_terms( 'gallery-cat', 'menu_order=ASC&hide_empty=0&parent=0' );
			}
			if ( empty( $terms ) ) {
				return $index;
			}

			$id = $the_term->term_id;

			$term_in_level = false; // flag: is our term to order in this level of terms

			foreach ( $terms as $term ) {

				if ( $term->term_id == $id ) { // our term to order, we skip
					$term_in_level = true;
					continue; // our term to order, we skip
				}
				// the nextid of our term to order, lets move our term here
				if ( null !== $next_id && $term->term_id == $next_id ) {
					$index ++;
					$index = $this->set_term_order( $id, $index, true );
				}

				// set order
				$index ++;
				$index = $this->set_term_order( $term->term_id, $index );

				// if that term has children we walk through them
				$children = get_terms( 'gallery-cat', "parent={$term->term_id}&menu_order=ASC&hide_empty=0" );
				if ( ! empty( $children ) ) {
					$index = $this->reorder_terms( $the_term, $next_id, $index, $children );
				}
			}

			// no nextid meaning our term is in last position
			if ( $term_in_level && null === $next_id ) {
				$index = $this->set_term_order( $id, $index + 1, true );
			}

			return $index;
		}

		public function set_term_order( $term_id, $index, $recursive = false ) {

			$term_id = (int) $term_id;
			$index   = (int) $index;

			update_term_meta( $term_id, 'order', $index );

			if ( ! $recursive ) {
				return $index;
			}

			$children = get_terms( 'gallery-cat', "parent=$term_id&menu_order=ASC&hide_empty=0" );

			foreach ( $children as $term ) {
				$index ++;
				$index = $this->set_term_order( $term->term_id, $index, true );
			}

			clean_term_cache( $term_id, 'gallery-cat' );

			return $index;
		}

		function terms_clauses( $clauses, $taxonomies, $args ) {
			global $wpdb;

			// No sorting when menu_order is false.
			if ( isset( $args['menu_order'] ) && $args['menu_order'] == false ) {
				return $clauses;
			}

			// No sorting when orderby is non default.
			if ( isset( $args['orderby'] ) && $args['orderby'] != 'name' ) {
				return $clauses;
			}

			// No sorting in admin when sorting by a column.
			if ( is_admin() && isset( $_GET['orderby'] ) ) {
				return $clauses;
			}

			// No need to filter counts
			if ( strpos( 'COUNT(*)', $clauses['fields'] ) !== false ) {
				return $clauses;
			}

			// Wordpress should give us the taxonomies asked when calling the get_terms function. Only apply to categories and pa_ attributes.
			$found = false;
			foreach ( (array) $taxonomies as $taxonomy ) {
				if ( 'gallery-cat' === $taxonomy ) {
					$found = true;
					break;
				}
			}
			if ( ! $found ) {
				return $clauses;
			}

			// Query fields.
			$clauses['fields'] = 'DISTINCT ' . $clauses['fields'];

			// Query join.
			$clauses['join'] .= " LEFT JOIN {$wpdb->termmeta} AS tmgallery ON (t.term_id = tmgallery.term_id AND tmgallery.meta_key = 'order') ";

			// Default to ASC.
			if ( ! isset( $args['menu_order'] ) || ! in_array( strtoupper( $args['menu_order'] ), array(
					'ASC',
					'DESC',
				) )
			) {
				$args['menu_order'] = 'ASC';
			}

			$order = "ORDER BY tmgallery.meta_value+0 " . $args['menu_order'];

			if ( $clauses['orderby'] ) {
				$clauses['orderby'] = str_replace( 'ORDER BY', $order . ',', $clauses['orderby'] );
			} else {
				$clauses['orderby'] = $order;
			}

			return $clauses;
		}

		public function gallery_ordering() {
			global $wpdb;

			ob_start();

			// check permissions again and make sure we have what we need
			if ( ! current_user_can('edit_posts') || empty( $_POST['id'] ) || ( ! isset( $_POST['previd'] ) && ! isset( $_POST['nextid'] ) ) ) {
				die(-1);
			}

			// real post?
			if ( ! $post = get_post( $_POST['id'] ) ) {
				die(-1);
			}

			$previd  = isset( $_POST['previd'] ) ? $_POST['previd'] : false;
			$nextid  = isset( $_POST['nextid'] ) ? $_POST['nextid'] : false;
			$new_pos = array(); // store new positions for ajax

			$siblings = $wpdb->get_results( $wpdb->prepare( "
			SELECT ID, menu_order FROM {$wpdb->posts} AS posts
			WHERE 	posts.post_type 	= 'gallery'
			AND 	posts.post_status 	IN ( 'publish', 'pending', 'draft', 'future', 'private' )
			AND 	posts.ID			NOT IN (%d)
			ORDER BY posts.menu_order ASC, posts.ID DESC
		", $post->ID ) );

			$menu_order = 0;

			foreach ( $siblings as $sibling ) {

				// if this is the post that comes after our repositioned post, set our repositioned post position and increment menu order
				if ( $nextid == $sibling->ID ) {
					$wpdb->update(
						$wpdb->posts,
						array(
							'menu_order' => $menu_order
						),
						array( 'ID' => $post->ID ),
						array( '%d' ),
						array( '%d' )
					);
					$new_pos[ $post->ID ] = $menu_order;
					$menu_order++;
				}

				// if repositioned post has been set, and new items are already in the right order, we can stop
				if ( isset( $new_pos[ $post->ID ] ) && $sibling->menu_order >= $menu_order ) {
					break;
				}

				// set the menu order of the current sibling and increment the menu order
				$wpdb->update(
					$wpdb->posts,
					array(
						'menu_order' => $menu_order
					),
					array( 'ID' => $sibling->ID ),
					array( '%d' ),
					array( '%d' )
				);
				$new_pos[ $sibling->ID ] = $menu_order;
				$menu_order++;

				if ( ! $nextid && $previd == $sibling->ID ) {
					$wpdb->update(
						$wpdb->posts,
						array(
							'menu_order' => $menu_order
						),
						array( 'ID' => $post->ID ),
						array( '%d' ),
						array( '%d' )
					);
					$new_pos[$post->ID] = $menu_order;
					$menu_order++;
				}

			}

			wp_send_json( $new_pos );
		}

		public function gallery_sorting_link( $views ) {
			global $wp_query;

			if ( ! current_user_can( 'edit_others_pages' ) ) {
				return $views;
			}

			$class            = ( isset( $wp_query->query['orderby'] ) && $wp_query->query['orderby'] == 'menu_order title' ) ? 'current' : '';
			$query_string     = remove_query_arg(array( 'orderby', 'order' ));
			$query_string     = add_query_arg( 'orderby', urlencode('menu_order title'), $query_string );
			$query_string     = add_query_arg( 'order', urlencode('ASC'), $query_string );
			$views['byorder'] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $class ) . '">' . esc_html__( 'Sort Gallery', 'wowmall-shortcodes' ) . '</a>';

			return $views;
		}

		public function gallery_columns( $existing_columns ) {
			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
				$existing_columns = array();
			}

			$columns          = array();
			$columns['cb']    = '<input type="checkbox">';
			$columns['thumb'] = '<span class=wc-image>' . esc_html__( 'Image', 'wowmall-shortcodes' ) . '</span>';

			return array_merge( $columns, $existing_columns );
		}

		public function render_gallery_columns( $column ) {
			global $post;

			switch ( $column ) {
				case 'thumb' :
					echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . get_the_post_thumbnail( $post->ID, 'thumbnail' ) . '</a>';
					break;
				default :
					break;
			}
		}

		public function vc_map() {

			$params = array(
				'name'        => esc_html__( 'Wowmall Gallery', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_gallery',
				'description' => esc_html__( 'Set Gallery', 'wowmall-shortcodes' ),
				'category'    => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'params'      => array(
					array(
						'type'        => 'dropdown',
						'heading'     => esc_html__( 'Gallery Layout Type', 'wowmall-shortcodes' ),
						'param_name'  => 'layout',
						'value'       => array(
							esc_html__( 'Masonry', 'wowmall-shortcodes' ) => 'masonry',
							esc_html__( 'Grid', 'wowmall-shortcodes' )    => 'grid',
						),
						'description' => esc_html__( 'Gallery Layout Type. Grid or Masonry', 'wowmall-shortcodes' ),
						'group' => esc_html__( 'Layout', 'wowmall-shortcodes' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => esc_html__( 'Gallery Columns', 'wowmall-shortcodes' ),
						'param_name'  => 'columns',
						'value'       => array(
							sprintf( esc_html__( '%s Columns', 'wowmall-shortcodes' ), 4 ) => '4',
							sprintf( esc_html__( '%s Columns', 'wowmall-shortcodes' ), 3 ) => '3',
						),
						'description' => esc_html__( 'Gallery Columns. 3 or 4', 'wowmall-shortcodes' ),
						'group' => esc_html__( 'Layout', 'wowmall-shortcodes' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => esc_html__( 'Display Type', 'wowmall-shortcodes' ),
						'param_name'  => 'display_type',
						'value'       => array(
							esc_html__( 'Images and Subcategories', 'wowmall-shortcodes' ) => 'both',
							esc_html__( 'Images', 'wowmall-shortcodes' )                 => 'images',
							esc_html__( 'Subcategories', 'wowmall-shortcodes' )          => 'subcategories',
						),
						'description' => esc_html__( 'Display Type. Can be Images, Subcategories or Both.', 'wowmall-shortcodes' ),
						'group' => esc_html__( 'Layout', 'wowmall-shortcodes' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => esc_html__( 'Order By', 'wowmall-shortcodes' ),
						'param_name'  => 'orderby',
						'value'       => array(
							esc_html__( 'Menu order', 'wowmall-shortcodes' ) => 'menu_order',
							esc_html__( 'Date', 'wowmall-shortcodes' )       => 'date',
							esc_html__( 'ID', 'wowmall-shortcodes' )         => 'ID',
							esc_html__( 'Title', 'wowmall-shortcodes' )      => 'title',
						),
						'description' => esc_html__( 'Select order type.', 'wowmall-shortcodes' ),
						'group' => esc_html__( 'Ordering', 'wowmall-shortcodes' ),
					),
					array(
						'type'        => 'dropdown',
						'heading'     => esc_html__( 'Sort Order', 'wowmall-shortcodes' ),
						'param_name'  => 'order',
						'value'       => array(
							esc_html__( 'Ascending', 'wowmall-shortcodes' )  => 'ASC',
							esc_html__( 'Descending', 'wowmall-shortcodes' ) => 'DESC',
						),
						'description' => esc_html__( 'Select sorting order.', 'wowmall-shortcodes' ),
						'group' => esc_html__( 'Ordering', 'wowmall-shortcodes' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => esc_html__( 'Posts per Page', 'wowmall-shortcodes' ),
						'param_name'  => 'posts_per_page',
						'value'       => '',
						'description' => esc_html__( 'Leave blank to use default value. To show all posts insert "-1"', 'wowmall-shortcodes' ),
						'group' => esc_html__( 'Ordering', 'wowmall-shortcodes' ),
					),
				),
			);

			vc_map( $params );
		}
	}

	wowmallGallery::instance();
}
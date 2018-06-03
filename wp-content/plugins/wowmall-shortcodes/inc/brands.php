<?php

if ( ! class_exists( 'wowmallBrands' ) ) {

	class wowmallBrands {

		protected static $_instance = null;

		public static $placeholder, $front_placeholder;

		public function __construct() {

			add_action( 'init', array(
				$this,
				'register_taxonomy',
			) );
			add_action( 'brand_add_form_fields', array(
				$this,
				'add_brand_fields',
			) );
			add_action( 'brand_edit_form_fields', array(
				$this,
				'edit_brand_fields',
			) );
			add_action( 'created_term', array(
				$this,
				'save_brand_fields',
			), 10, 3 );
			add_action( 'edit_term', array(
				$this,
				'save_brand_fields',
			), 10, 3 );
			add_filter( 'manage_edit-brand_columns', array(
				$this,
				'brand_columns',
			) );
			add_filter( 'manage_brand_custom_column', array(
				$this,
				'brand_column',
			), 10, 3 );
			add_action( 'create_term', array(
				$this,
				'create_term',
			), 5, 3 );
			add_action( 'wp_ajax_wowmall_brand_term_ordering', array(
				$this,
				'term_ordering',
			) );
			add_action( 'wp_ajax_nopriv_wowmall_brand_term_ordering', array(
				$this,
				'term_ordering',
			) );
			add_filter( 'terms_clauses', array(
				$this,
				'terms_clauses',
			), 10, 3 );

			add_filter( 'manage_product_posts_columns', array(
				$this,
				'brand_product_columns',
			), 11 );

			add_action( 'manage_product_posts_custom_column', array(
				$this,
				'render_brand_columns',
			), 2 );

			add_shortcode( 'wowmall_brands', array(
				$this,
				'shortcode',
			) );

			if ( is_admin() ) {
				add_action( 'vc_before_init', array(
					$this,
					'vc_map',
				) );
			}

			add_action( 'after_setup_theme', array(
				$this,
				'add_image_sizes',
			), 11 );

			add_action( 'widgets_init', array(
				$this,
				'widgets_init',
			) );

            add_action( 'wc_single_variation_after_attribute_label_size', array(
                $this,
                'wc_single_variation_after_attribute_label_size',
            ) );

			self::$placeholder = 'https://placeholdit.imgix.net/~text?txtsize=15&txt=IMG&w=60&h=60';
		}

		public function add_image_sizes() {

			add_image_size( 'wowmall-brand', 100, 9999 );
			add_image_size( 'wowmall-brand-2x', 200, 9999 );
			add_image_size( 'wowmall-brand-3x', 300, 9999 );
		}

		public function setup_front_placeholder() {

			if ( is_null( self::$front_placeholder ) ) {

				global $wowmall_options;

				$color1 = ! empty( $wowmall_options['accent_color_1'] ) ? str_replace( '#', '', $wowmall_options['accent_color_1'] ) : 'fc6f38';

				$color2 = ! empty( $wowmall_options['accent_color_1'] ) ? str_replace( '#', '', $wowmall_options['accent_color_2'] ) : '222';

				self::$front_placeholder = apply_filters( 'wowmall_gallery_placeholder', '<img width=%1$s height=%2$s src="https://placeholdit.imgix.net/~text?txtsize=30&bg=' . $color2 . '&txtclr=' . $color1 . '&w=%1$s&h=%2$s&txt=%3$s" alt="%3$s">' );
			}
		}

		public function register_taxonomy() {
			$labels = array(
				'name'          => _x( 'Brands', 'taxonomy general name', 'wowmall-shortcodes' ),
				'singular_name' => _x( 'Brand', 'taxonomy singular name', 'wowmall-shortcodes' ),
				'search_items'  => __( 'Search Brands', 'wowmall-shortcodes' ),
				'all_items'     => __( 'All Brands', 'wowmall-shortcodes' ),
				'edit_item'     => __( 'Edit Brand', 'wowmall-shortcodes' ),
				'update_item'   => __( 'Update Brand', 'wowmall-shortcodes' ),
				'add_new_item'  => __( 'Add New Brand', 'wowmall-shortcodes' ),
				'new_item_name' => __( 'New Brand Name', 'wowmall-shortcodes' ),
				'menu_name'     => __( 'Brands', 'wowmall-shortcodes' ),
			);

			$args = array(
				'hierarchical' => false,
				'labels'       => $labels,
				'show_ui'      => true,
				'rewrite'      => array(
					'with_front' => false,
				),
				'query_var'    => true,
			);

			if ( post_type_exists( 'product' ) ) {

				register_taxonomy( 'brand', array( 'product' ), $args );
			}
		}

		public function start_script() { ?>

			function wowmall_reset_brand_image() {
			jQuery( '#brand_thumbnail' ).attr( 'src', '<?php echo self::$placeholder; ?>' );
			jQuery( '#brand_thumbnail_id' ).val( '' );
			jQuery( '.remove_image_button' ).hide();
			}

			function wowmall_reset_brand_sizes_image() {
			jQuery( '#brand_sizes' ).attr( 'src', '<?php echo self::$placeholder; ?>' );
			jQuery( '#brand_sizes_id' ).val( '' );
			jQuery( '.remove_sizes_image_button' ).hide();
			}

			if ( jQuery( '#brand_thumbnail_id' ).val() ) {
			jQuery( '.remove_image_button' ).show();
			}

			if ( jQuery( '#brand_sizes_id' ).val() ) {
			jQuery( '.remove_sizes_image_button' ).show();
			}

			var file_frame;

			jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

			event.preventDefault();

			if ( file_frame ) {
			file_frame.off( 'select' ).on( 'select', function() {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON(),
			url = ( 'undefined' !== typeof attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url : attachment.sizes.full.url;

			jQuery( '#brand_thumbnail_id' ).val( attachment.id );
			jQuery( '#brand_thumbnail' ).attr( 'src', url );
			jQuery( '.remove_image_button' ).show();
			}).open();
			return;
			}

			file_frame = wp.media.frames.downloadable_file = wp.media({
			title: '<?php esc_html_e( 'Choose an image', 'wowmall-shortcodes' ); ?>',
			multiple: false
			});

			file_frame.off( 'select' ).on( 'select', function() {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON(),
			url = ( 'undefined' !== typeof attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url : attachment.sizes.full.url;

			jQuery( '#brand_thumbnail_id' ).val( attachment.id );
			jQuery( '#brand_thumbnail' ).attr( 'src', url );
			jQuery( '.remove_image_button' ).show();
			}).open();
			});

			jQuery( document ).on( 'click', '.upload_sizes_image_button', function( event ) {

			event.preventDefault();

			if ( file_frame ) {
			file_frame.off( 'select' ).on( 'select', function() {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON(),
			url = ( 'undefined' !== typeof attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url : attachment.sizes.full.url;

			jQuery( '#brand_sizes_id' ).val( attachment.id );
			jQuery( '#brand_sizes' ).attr( 'src', url );
			jQuery( '.remove_sizes_image_button' ).show();
			}).open();
			return;
			}

			file_frame = wp.media.frames.downloadable_file = wp.media({
			title: '<?php esc_html_e( 'Choose an image', 'wowmall-shortcodes' ); ?>',
			multiple: false
			});

			file_frame.off( 'select' ).on( 'select', function() {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON(),
			url = ( 'undefined' !== typeof attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url : attachment.sizes.full.url;

			jQuery( '#brand_sizes_id' ).val( attachment.id );
			jQuery( '#brand_sizes' ).attr( 'src', url );
			jQuery( '.remove_sizes_image_button' ).show();
			}).open();
			});

			jQuery( document ).on( 'click', '.remove_image_button', function() {
			wowmall_reset_brand_image();
			return false;
			});

			jQuery( document ).on( 'click', '.remove_sizes_image_button', function() {
			wowmall_reset_brand_sizes_image();
			return false;
			});
		<?php }

		function add_brand_fields() {
			wp_enqueue_script( 'wowmall-brands-sort', wowmallShortcodes::$pluginurl . '/assets/js/brand-sort.js', array( 'jquery-ui-selectmenu' ), null, true );
			?>
			<div class="form-field term-thumbnail-wrap">
				<label><?php esc_html_e( 'Thumbnail', 'wowmall-shortcodes' ); ?></label>
				<img src="<?php echo self::$placeholder; ?>" width=60 id=brand_thumbnail
				     style="float:left;margin-right:10px">
				<div style="line-height:60px">
					<input type=hidden id=brand_thumbnail_id name=brand_thumbnail_id>
					<button type=button
					        class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'wowmall-shortcodes' ); ?></button>
					<button type=button class="remove_image_button button"
					        style="display:none"><?php esc_html_e( 'Remove image', 'wowmall-shortcodes' ); ?></button>
				</div>
				<script type=text/javascript>
					<?php $this->start_script(); ?>
					jQuery( document ).ajaxComplete( function ( event, request, options ) {
						if ( request && 4 === request.readyState && 200 === request.status
							&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {
							var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
							if ( ! res || res.errors ) {
								return;
							}
							wowmall_reset_brand_image();
							return;
						}
					} );
				</script>
				<div class=clear></div>
			</div>
			<?php if( current_theme_supports( 'wowmall_beta_features' ) ) { ?>
				<div class="form-field term-sizes-wrap">
					<label><?php esc_html_e( 'Sizes table', 'wowmall-shortcodes' ); ?></label>
					<img src="<?php echo self::$placeholder; ?>" width=60 id=brand_sizes
					     style="float:left;margin-right:10px">
					<div style="line-height:60px">
						<input type=hidden id=brand_sizes_id name=brand_sizes_id>
						<button type=button
						        class="upload_sizes_image_button button"><?php esc_html_e( 'Upload/Add image', 'wowmall-shortcodes' ); ?></button>
						<button type=button class="remove_sizes_image_button button"
						        style="display:none"><?php esc_html_e( 'Remove image', 'wowmall-shortcodes' ); ?></button>
					</div>
					<script type=text/javascript>
						<?php $this->start_script(); ?>
						jQuery( document ).ajaxComplete( function ( event, request, options ) {
							if ( request && 4 === request.readyState && 200 === request.status
								&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {
								var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
								if ( ! res || res.errors ) {
									return;
								}
								wowmall_reset_brand_sizes_image();
								return;
							}
						} );
					</script>
					<div class=clear></div>
				</div>
				<?php
			}
		}

		public function edit_brand_fields( $term ) {
			$thumbnail_id   = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
			$sizes_table_id = (int) get_term_meta( $term->term_id, 'sizes_id', true );

			if ( $thumbnail_id ) {
				$image = esc_url( wp_get_attachment_thumb_url( $thumbnail_id ) );
			} else {
				$image = self::$placeholder;
			}
			if ( $sizes_table_id ) {
				$sizes = esc_url( wp_get_attachment_thumb_url( $sizes_table_id ) );
			} else {
				$sizes = self::$placeholder;
			} ?>
			<tr class=form-field>
				<th scope=row valign=top><label><?php esc_html_e( 'Thumbnail', 'wowmall-shortcodes' ); ?></label></th>
				<td>
					<img src="<?php echo $image; ?>" width=60 id=brand_thumbnail style="float:left;margin-right:10px">
					<div style="line-height:60px">
						<input type=hidden id=brand_thumbnail_id name=brand_thumbnail_id
						       value=<?php echo $thumbnail_id; ?>>
						<button type=button
						        class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'wowmall-shortcodes' ); ?></button>
						<button type=button class="remove_image_button button"
						        style="display:none"><?php esc_html_e( 'Remove image', 'wowmall-shortcodes' ); ?></button>
					</div>
					<script type=text/javascript>
						<?php $this->start_script(); ?>
					</script>
					<div class=clear></div>
				</td>
			</tr>
			<?php if( current_theme_supports( 'wowmall_beta_features' ) ) { ?>
				<tr class=form-field>
					<th scope=row valign=top><label><?php esc_html_e( 'Sizes table', 'wowmall-shortcodes' ); ?></label>
					</th>
					<td>
						<img src="<?php echo $sizes; ?>" width=60 id=brand_sizes style="float:left;margin-right:10px">
						<div style="line-height:60px">
							<input type=hidden id=brand_sizes_id name=brand_sizes_id
							       value=<?php echo $sizes; ?>>
							<button type=button
							        class="upload_sizes_image_button button"><?php esc_html_e( 'Upload/Add image', 'wowmall-shortcodes' ); ?></button>
							<button type=button class="remove_sizes_image_button button"
							        style="display:none"><?php esc_html_e( 'Remove image', 'wowmall-shortcodes' ); ?></button>
						</div>
						<script type=text/javascript>
							<?php $this->start_script(); ?>
						</script>
						<div class=clear></div>
					</td>
				</tr>
				<?php
			}
		}

		public function save_brand_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
			if ( 'brand' === $taxonomy ) {
				if ( isset( $_POST['brand_thumbnail_id'] ) ) {
					update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['brand_thumbnail_id'] ) );
				}
				if ( isset( $_POST['brand_sizes_id'] ) ) {
					update_term_meta( $term_id, 'sizes_id', absint( $_POST['brand_sizes_id'] ) );
				}
			}
		}

		public function brand_columns( $columns ) {
			$new_columns = array();

			if ( isset( $columns['cb'] ) ) {
				$new_columns['cb'] = $columns['cb'];
				unset( $columns['cb'] );
			}

			$new_columns['thumb'] = esc_html__( 'Image', 'wowmall-shortcodes' );

			return array_merge( $new_columns, $columns );
		}

		public function brand_column( $columns, $column, $id ) {

			if ( 'thumb' == $column ) {

				$thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

				if ( $thumbnail_id ) {
					$image = esc_url( wp_get_attachment_thumb_url( $thumbnail_id ) );
				} else {
					$image = self::$placeholder;
				}

				$columns .= '<img src="' . $image . '" alt="' . esc_attr__( 'Thumbnail', 'wowmall-shortcodes' ) . '" class=wp-post-image width=48>';

			}

			return $columns;
		}

		public function create_term( $term_id, $tt_id = '', $taxonomy = '' ) {

			if ( 'brand' != $taxonomy ) {
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
		}

		public function reorder_terms( $the_term, $next_id, $index = 0, $terms = null ) {
			if ( ! $terms ) {
				$terms = get_terms( 'brand', 'menu_order=ASC&hide_empty=0&parent=0' );
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

			clean_term_cache( $term_id, 'brand' );

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
				if ( 'brand' === $taxonomy ) {
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
			$clauses['join'] .= " LEFT JOIN {$wpdb->termmeta} AS brandtm ON (t.term_id = brandtm.term_id AND brandtm.meta_key = 'order') ";

			// Default to ASC.
			if ( ! isset( $args['menu_order'] ) || ! in_array( strtoupper( $args['menu_order'] ), array(
					'ASC',
					'DESC',
				) )
			) {
				$args['menu_order'] = 'ASC';
			}

			$order = "ORDER BY brandtm.meta_value+0 " . $args['menu_order'];

			if ( $clauses['orderby'] ) {
				$clauses['orderby'] = str_replace( 'ORDER BY', $order . ',', $clauses['orderby'] );
			} else {
				$clauses['orderby'] = $order;
			}

			return $clauses;
		}

		public function brand_product_columns( $existing_columns ) {
			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
				$existing_columns = array();
			}

			$key    = array_search( 'featured', array_keys( $existing_columns ), true );
			$before = array();
			$after  = array();
			if ( $key !== false ) {
				$before = array_slice( $existing_columns, 0, $key, true );
				$after  = array_slice( $existing_columns, $key, null, true );
			}
			$brand            = array();
			$brand['brand']   = '<span class="wc-brand">' . esc_html__( 'Brand', 'wowmall-shortcodes' ) . '</span>';
			$existing_columns = $before + $brand + $after;

			return $existing_columns;
		}

		public function render_brand_columns( $column ) {
			global $post;

			switch ( $column ) {
				case 'brand' :
					if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						$termlist = array();
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=product' ) . ' ">' . $term->name . '</a>';
						}

						echo implode( ', ', $termlist );
					}
					break;
				default :
					break;
			}
		}

		public function shortcode( $atts = array() ) {

			$atts = shortcode_atts( array(
				'title'       => '',
				'title_align' => 'left',
				'visible'     => 8,
				'css'         => '',
				'el_class'    => '',
			), $atts );

			$list_args = array(
				'hide_empty'   => 0,
				'hierarchical' => 0,
				'taxonomy'     => 'brand',
				'pad_counts'   => 1,
				'menu_order'   => 'ASC',
			);

			$cats = get_categories( $list_args );

			ob_start();

			if ( ! empty( $cats ) ) {

				$this->setup_front_placeholder();

				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script( 'wowmall-brands-carousel', wowmallShortcodes::$pluginurl . '/assets/js/brands-carousel' . $min . '.js', array( 'wowmall-theme-script' ), null, true );

				$id = uniqid();
				if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) {
					$class = '';
					if ( '' !== $atts['css'] && function_exists( 'vc_shortcode_custom_css_class' ) ) {
						$class .= vc_shortcode_custom_css_class( $atts['css'], ' ' );
					}
					if ( ! empty( $atts['el_class'] ) ) {
						$class .= ' ' . $atts['el_class'];
					}; ?>
					<div class="<?php echo $class; ?>">
				<?php }
				?>

				<div class=wowmall-brands-carousel>
					<?php if ( ! empty( $atts['title'] ) ) {
						$style = '';
						if ( 'left' !== $atts['title_align'] ) {
							$style = ' style="text-align:' . $atts['title_align'] . '"';
						}
						echo '<h4' . $style . '>' . $atts['title'] . '</h4>';
					} ?>
					<div class=swiper-container id=<?php echo $id; ?> data-visible=<?php echo $atts['visible']; ?>>
						<div class=swiper-wrapper>

							<?php foreach ( $cats as $cat ) { ?>
								<a class="wowmall-brand-item swiper-slide"
								   href="<?php echo esc_url( get_term_link( $cat, 'brand' ) ); ?>">
									<?php
									$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
									if ( $thumbnail_id ) {
										$image = wp_get_attachment_image( $thumbnail_id, 'wowmall-brand' );
									} else {

										$image = sprintf( self::$front_placeholder, 100, 100, $cat->name );
									} ?>
									<?php echo $image; ?>
								</a>
							<?php } ?>
						</div>
					</div>
					<?php if ( ! wp_is_mobile() && ! empty( $atts['title'] ) ) { ?>
						<div class=swiper-button-prev id=swiper-button-prev<?php echo $id; ?>></div>
						<div class=swiper-button-next id=swiper-button-next<?php echo $id; ?>></div>
					<?php } ?>
				</div>
				<?php if ( '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) { ?>
					</div>
				<?php }
			}

			return ob_get_clean();
		}

		public function widgets_init() {
			if ( current_theme_supports( 'wowmall_beta_features' ) && class_exists( 'WC_Widget_Layered_Nav' ) && ! class_exists( 'Wowmall_WC_Widget_Brands_Filter' ) ) {
				require_once 'widgets/brands-filter.php';
				register_widget( 'Wowmall_WC_Widget_Brands_Filter' );
				if( class_exists( 'WC_Widget_Layered_Nav_Filters' ) && ! class_exists( 'Wowmall_Shortcodes_WC_Widget_Layered_Nav_Filters' ) ) {
					require_once 'widgets/class-wc-widget-layered-nav-filters.php';
					unregister_widget( 'WC_Widget_Layered_Nav_Filters' );
					register_widget( 'Wowmall_Shortcodes_WC_Widget_Layered_Nav_Filters' );
				}
			}
		}

		public function wc_single_variation_after_attribute_label_size() {

            if( ! current_theme_supports( 'wowmall_beta_features' ) ) {
                return;
            }
			global $product;
			if( empty( $product ) ) {
				return;
			}

			$brands = get_the_terms( $product->get_id(), 'brand' );
			if( empty( $brands ) ) {
				return;
			}

			$guides = array();

			foreach ( $brands as $brand ) {
				$guide = (int) get_term_meta( $brand->term_id, 'sizes_id', true );
				if( ! $guide ) {
					continue;
				}
				$guide = wp_get_attachment_url( $guide );
				if( ! $guide ) {
					continue;
				}
				$guides[ $brand->name ] = $guide;
			}
			if( empty( $guides ) ) {
				return;
			}
			$count = count( $guides );
			echo '<span class="wowmall-size-guides">';
			if( 1 < $count ) {
				echo _n( 'Size Guide', 'Size Guides: ', $count, 'wowmall-shortcodes' );
				foreach ( $guides as $name => $guide ) {
					echo '<a href="' . $guide . '">' . $name . '</a> ';
				}
			} else {
				echo '<a href="' . array_shift( $guides ) . '">' . _n( 'Size Guide', 'Size Guides:', $count, 'wowmall-shortcodes' ) . '</a> ';
			}
			echo '</span>';

		}

		public function vc_map() {

			$params = array(
				'name'        => esc_html__( 'Wowmall Brands Carousel', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_brands',
				'description' => esc_html__( 'Add Brands Carousel shortcode', 'wowmall-shortcodes' ),
				'category'    => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'weight'      => - 999,
				'params'      => array(
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Title', 'wowmall-shortcodes' ),
						'param_name' => 'title',
						'value'      => '',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Title Align', 'wowmall-shortcodes' ),
						'param_name' => 'title_align',
						'value'      => array(
							esc_html__( 'Left', 'wowmall-shortcodes' )   => 'left',
							esc_html__( 'Center', 'wowmall-shortcodes' ) => 'center',
							esc_html__( 'Right', 'wowmall-shortcodes' )  => 'right',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Number of products to show', 'wowmall-shortcodes' ),
						'param_name' => 'visible',
						'value'      => array(
							'1' => '1',
							'2' => '2',
							'3' => '3',
							'4' => '4',
							'5' => '5',
							'6' => '6',
							'7' => '7',
							'8' => '8',
						),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra class name', 'wowmall-shortcodes' ),
						'param_name'  => 'el_class',
						'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'wowmall-shortcodes' ),
					),
					array(
						'type'       => 'css_editor',
						'heading'    => __( 'Css Box', 'wowmall-shortcodes' ),
						'param_name' => 'css',
						'group'      => __( 'Design options', 'wowmall-shortcodes' ),
					),
				),
			);

			vc_map( $params );
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallBrands::instance();
}
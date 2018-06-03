<?php

if ( ! class_exists( 'wowmallBrandsAdmin' ) ) {

	class wowmallBrandsAdmin {

		protected static $_instance = null;

		public static $placeholder;

		public function __construct() {

			add_action( 'admin_enqueue_scripts', array(
				$this,
				'admin_styles',
			), 11 );

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

			add_filter( 'manage_product_posts_columns', array(
				$this,
				'brand_product_columns',
			), 11 );

			add_action( 'manage_product_posts_custom_column', array(
				$this,
				'render_brand_columns',
			), 2 );

			add_action( 'vc_before_init', array(
				$this,
				'vc_map',
			) );

			self::$placeholder = 'https://placeholdit.imgix.net/~text?txtsize=15&txt=IMG&w=60&h=60';
		}

		public function admin_styles() {

			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( 'edit-brand' === $screen_id ) {
				wp_enqueue_style( 'woocommerce_admin_styles' );
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
				<script>
					<?php $this->start_script(); ?>
					jQuery( document ).ajaxComplete( function ( event, request, options ) {
						if ( request && 4 === request.readyState && 200 === request.status
							&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {
							var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
							if ( !res || res.errors ) {
								return;
							}
							wowmall_reset_brand_image();
							return;
						}
					} );
				</script>
				<div class=clear></div>
			</div>
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
				<script>
					<?php $this->start_script(); ?>
					jQuery( document ).ajaxComplete( function ( event, request, options ) {
						if ( request && 4 === request.readyState && 200 === request.status
							&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {
							var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
							if ( !res || res.errors ) {
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

		public function edit_brand_fields( $term ) {
			$thumbnail_id   = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
			$sizes_table_id = (int) get_term_meta( $term->term_id, 'sizes_id', true );

			if ( $thumbnail_id ) {
				$image = esc_url( wp_get_attachment_thumb_url( $thumbnail_id ) );
			}
			else {
				$image = self::$placeholder;
			}
			if ( $sizes_table_id ) {
				$sizes = esc_url( wp_get_attachment_thumb_url( $sizes_table_id ) );
			}
			else {
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
				}
				else {
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
					}
					else {
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

		public function vc_map() {

			$params = array(
				'name'        => esc_html__( 'Wowmall Brands Carousel', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_brands',
				'description' => esc_html__( 'Add Brands Carousel shortcode', 'wowmall-shortcodes' ),
				'category'    => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'weight'      => -999,
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

	wowmallBrandsAdmin::instance();
}
<?php

if ( ! class_exists( 'wowmallNewProducts' ) ) {

	class wowmallNewProducts {

		protected static $_instance = null;

		public function __construct() {

			add_action( 'woocommerce_product_quick_edit_end', array( $this, 'new_checkbox' ) );

			add_action( 'manage_product_posts_custom_column', array( $this, '_columns_new' ), 3 );

			add_action( 'save_post', array( $this, '_bulk_and_quick_edit_save_post' ), 11, 2 );

			if ( class_exists( 'WC_Admin_Post_Types' ) ) {

				global $wp_filter;

				foreach ( $wp_filter['post_submitbox_misc_actions'][10] as $hook ) {
					if ( is_array( $hook['function'] ) && $hook['function'][0] instanceof WC_Admin_Post_Types ) {
						remove_action( 'post_submitbox_misc_actions', array(
							$hook['function'][0],
							'product_data_visibility',
						) );
					}
				}
			}

			add_action( 'post_submitbox_misc_actions', array( $this, 'product_data_visibility' ) );

			add_action( 'woocommerce_process_product_meta', array( $this, 'save' ), 9, 2 );

			add_filter( 'manage_product_posts_columns', array( $this, 'product_columns' ) );

			add_action( 'wp_ajax_woocommerce_new_product', array( $this, 'new_product' ) );
		}

		public function new_checkbox() { ?>
			<br class=clear>
			<label class="alignleft new">
				<span class=title><?php esc_html_e( 'New', 'wowmall-shortcodes' ); ?></span>
				<span class=input-text-wrap>
		<input type=checkbox name=_new value=1>
	</span>
			</label>
			<br class=clear>
		<?php }

		public function _columns_new( $column ) {
			global $post, $the_product;
			if ( empty( $the_product ) || $the_product->get_id() != $post->ID ) {
				$the_product = wc_get_product( $post );
			}

			$new = get_post_meta( $the_product->get_id(), '_new', true );
			switch ( $column ) {
				case 'name' :
					/* Custom inline data for woocommerce. */
					echo '
					<div class=hidden id=woocommerce_inline_' . $post->ID . '_new>' . $new . '</div>
				';
					break;
				case 'new' :
					$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_new_product&product_id=' . $post->ID ), 'woocommerce-new-product' );
					echo '<a href="' . esc_url( $url ) . '" title="'. __( 'Toggle new', 'woocommerce' ) . '">';
					if ( 'yes' === $new ) {
						echo '<span class="wc-new tips" data-tip="' . esc_attr__( 'Yes', 'woocommerce' ) . '">' . __( 'Yes', 'woocommerce' ) . '</span>';
					} else {
						echo '<span class="wc-new not-new tips" data-tip="' . esc_attr__( 'No', 'woocommerce' ) . '">' . __( 'No', 'woocommerce' ) . '</span>';
					}
					echo '</a>';
				break;
				default :
					break;
			}
		}

		public function _bulk_and_quick_edit_save_post( $post_id, $post ) {

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			// Don't save revisions and autosaves
			if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
				return $post_id;
			}

			// Check post type is product
			if ( 'product' != $post->post_type ) {
				return $post_id;
			}

			// Check user permission
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			// Check nonces
			if ( ! isset( $_REQUEST['woocommerce_quick_edit_nonce'] ) && ! isset( $_REQUEST['woocommerce_bulk_edit_nonce'] ) ) {
				return $post_id;
			}
			if ( isset( $_REQUEST['woocommerce_quick_edit_nonce'] ) && ! wp_verify_nonce( $_REQUEST['woocommerce_quick_edit_nonce'], 'woocommerce_quick_edit_nonce' ) ) {
				return $post_id;
			}
			if ( isset( $_REQUEST['woocommerce_bulk_edit_nonce'] ) && ! wp_verify_nonce( $_REQUEST['woocommerce_bulk_edit_nonce'], 'woocommerce_bulk_edit_nonce' ) ) {
				return $post_id;
			}

			if ( isset( $_REQUEST['_new'] ) ) {
				update_post_meta( $post_id, '_new', 'yes' );
			} else {
				update_post_meta( $post_id, '_new', 'no' );
			}

			// Clear transient
			wc_delete_product_transients( $post_id );

			return $post_id;
		}

		public function product_data_visibility() {
			global $post, $thepostid, $product_object;

			if ( 'product' !== $post->post_type ) {
				return;
			}

			$thepostid          = $post->ID;
			$product_object     = $thepostid ? wc_get_product( $thepostid ) : new WC_Product;
			$current_visibility = $product_object->get_catalog_visibility();
			$current_featured   = wc_bool_to_string( $product_object->get_featured() );
			$visibility_options = wc_get_product_visibility_options();
			$current_new   = ( $current_new = get_post_meta( $post->ID, '_new', true ) ) ? $current_new : 'no';
			?>
			<div class="misc-pub-section" id="catalog-visibility">
				<?php _e( 'Catalog visibility:', 'woocommerce' ); ?> <strong id="catalog-visibility-display"><?php
					echo isset( $visibility_options[ $current_visibility ] ) ? esc_html( $visibility_options[ $current_visibility ] ) : esc_html( $current_visibility );

					if ( 'yes' === $current_featured ) {
						echo ', ' . __( 'Featured', 'woocommerce' );
					}
					if ( 'yes' == $current_new ) {
						echo ', ' . esc_html__( 'New', 'wowmall-shortcodes' );
					}
					?></strong>

				<a href="#catalog-visibility" class="edit-catalog-visibility hide-if-no-js"><?php _e( 'Edit', 'woocommerce' ); ?></a>

				<div id="catalog-visibility-select" class="hide-if-js">

					<input type="hidden" name="current_visibility" id="current_visibility" value="<?php echo esc_attr( $current_visibility ); ?>" />
					<input type="hidden" name="current_featured" id="current_featured" value="<?php echo esc_attr( $current_featured ); ?>" />

					<?php
					echo '<p>' . __( 'Choose where this product should be displayed in your catalog. The product will always be accessible directly.', 'woocommerce' ) . '</p>';

					foreach ( $visibility_options as $name => $label ) {
						echo '<input type="radio" name="_visibility" id="_visibility_' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '" ' . checked( $current_visibility, $name, false ) . ' data-label="' . esc_attr( $label ) . '" /> <label for="_visibility_' . esc_attr( $name ) . '" class="selectit">' . esc_html( $label ) . '</label><br />';
					}

					echo '<p>' . __( 'Enable this option to feature this product.', 'woocommerce' ) . '</p>';

					echo '<input type="checkbox" name="_featured" id="_featured" ' . checked( $current_featured, 'yes', false ) . ' /> <label for="_featured">' . __( 'Featured product', 'woocommerce' ) . '</label><br />';
					echo '<p>' . esc_html__( 'Enable this option to mark this as a new product.', 'wowmall-shortcodes' ) . '</p>';
					echo '<input type="checkbox" name="_new" id="_new" ' . checked( $current_new, 'yes', false ) . ' /> <label for="_new">' . esc_html__( 'New Product', 'wowmall-shortcodes' ) . '</label><br />';
					?>
					<p>
						<a href="#catalog-visibility" class="save-post-visibility hide-if-no-js button"><?php _e( 'OK', 'woocommerce' ); ?></a>
						<a href="#catalog-visibility" class="cancel-post-visibility hide-if-no-js"><?php _e( 'Cancel', 'woocommerce' ); ?></a>
					</p>
				</div>
			</div>
			<?php
		}

		public static function save( $post_id, $post ) {
			update_post_meta( $post_id, '_new', isset( $_POST['_new'] ) ? 'yes' : 'no' );
		}

		public function product_columns( $existing_columns ) {
			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
				$existing_columns = array();
			}
			$columns['new']     = '<span class="wc-new parent-tips" data-tip="' . esc_attr__( 'New', 'wowmall-shortcodes' ) . '">' . __( 'New', 'wowmall-shortcodes' ) . '</span>';

			return array_merge( $existing_columns, $columns );

		}

		public static function new_product() {
			if ( current_user_can( 'edit_products' ) && check_admin_referer( 'woocommerce-new-product' ) ) {
				$product_id = absint( $_GET['product_id'] );

				if ( 'product' === get_post_type( $product_id ) ) {
					update_post_meta( $product_id, '_new', get_post_meta( $product_id, '_new', true ) === 'yes' ? 'no' : 'yes' );
				}
			}

			wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php?post_type=product' ) );
			die();
		}

		public static function instance() {
	
			if ( is_null( self::$_instance ) ) {
	
				self::$_instance = new self();
			}
	
			return self::$_instance;
		}
	}
	wowmallNewProducts::instance();
}
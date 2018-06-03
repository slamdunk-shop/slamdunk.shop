<?php

class wowmallVariations {

	protected static $_instance = null;

	public function __construct() {

		global $wowmall_options;

		add_filter( 'product_attributes_type_selector', array( $this, 'product_attributes_type_selector' ) );

		add_action( 'woocommerce_product_option_terms', array( $this, 'product_option_terms' ), 10, 2 );

		if( ! isset( $wowmall_options['custom_variations_color'] ) || $wowmall_options['custom_variations_color'] ) {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ), 10 );

			$attribute_taxonomies = wc_get_attribute_taxonomies();

			if ( ! empty( $attribute_taxonomies ) ) {
				foreach ( $attribute_taxonomies as $attribute ) {
					if ( 'color' === $attribute->attribute_type ) {
						add_action( 'pa_' . $attribute->attribute_name . '_add_form_fields', array(
							$this,
							'add_product_attribute_color_field'
						) );

						add_action( 'pa_' . $attribute->attribute_name . '_edit_form_fields', array(
							$this,
							'edit_product_attribute_color_field'
						) );
					}
				}
			}

			add_action( 'created_term', array(
				$this,
				'save_attribute_color'
			), 10, 3 );
			add_action( 'edit_term', array(
				$this,
				'save_attribute_color'
			), 10, 3 );
		}
	}

	public function product_attributes_type_selector( $types ) {
		global $wowmall_options;
		if( ! isset( $wowmall_options['custom_variations_color'] ) || $wowmall_options['custom_variations_color'] ) {
			$types['color'] = esc_html__( 'Color', 'wowmall-shortcodes' );
		}
		if( ! isset( $wowmall_options['custom_variations_size'] ) || $wowmall_options['custom_variations_size'] ) {
			$types['size'] = esc_html__( 'Size', 'wowmall-shortcodes' );
		}
		return $types;
	}

	public function product_option_terms( $attribute_taxonomy, $i ) {
		global $wowmall_options;
		$taxonomy = wc_attribute_taxonomy_name($attribute_taxonomy->attribute_name);
		if ( ( 'color' === $attribute_taxonomy->attribute_type && ( ! isset( $wowmall_options['custom_variations_color'] ) || $wowmall_options['custom_variations_color'] ) ) || ( 'size' === $attribute_taxonomy->attribute_type && ( ! isset( $wowmall_options['custom_variations_size'] ) || $wowmall_options['custom_variations_size'] ) ) ) { ?>

			<select multiple=multiple data-placeholder="<?php esc_attr_e( 'Select terms', 'wowmall-shortcodes' ); ?>" class="multiselect attribute_values wc-enhanced-select" name=attribute_values[<?php echo $i; ?>][]>
				<?php
				$args = array(
					'orderby'    => 'name',
					'hide_empty' => 0
				);
				$all_terms = get_terms( $taxonomy, apply_filters( 'woocommerce_product_attribute_terms', $args ) );
				if ( $all_terms ) {
					foreach ( $all_terms as $term ) {
						echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( has_term( $term->term_id, $taxonomy ), true, false ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
					}
				}
				?>
			</select>
			<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'wowmall-shortcodes' ); ?></button>
			<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'wowmall-shortcodes' ); ?></button>
			<button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'wowmall-shortcodes' ); ?></button>

		<?php }
	}

	public function add_product_attribute_color_field( $term ) {
		?>
		<div class="form-field color-wrap">
			<label for=color><?php esc_html_e( 'Color', 'wowmall-shortcodes' ) ?></label>
			<input name=color id=color type=text value="" />
		</div>
		<?php
	}

	public function edit_product_attribute_color_field( $term ) {
		$value = get_term_meta( $term->term_id, 'color', true );
		?>
		<tr class="form-field color-wrap">
			<th scope=row>
				<label for=color><?php esc_html_e( 'Color', 'wowmall-shortcodes' ) ?></label>
			</th>
			<td>
				<input name=color id=color type=text value="<?php echo $value; ?>" />
			</td>
		</tr>
		<?php
	}

	public function save_attribute_color( $term_id, $tt_id = '', $taxonomy = '' ) {
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $attribute ) {
				if( 'color' === $attribute->attribute_type ) {
					if ( isset( $_POST['color'] ) && 'pa_' . $attribute->attribute_name === $taxonomy ) {
						update_term_meta( $term_id, 'color', esc_attr( $_POST['color'] ) );
					}
				}
			}
		}
	}

	public function admin_assets() {
		$screen = get_current_screen();
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		$enqueue = false;

		if ( ! empty( $attribute_taxonomies ) && ! empty( $screen ) && isset( $screen->taxonomy ) ) {
			foreach ( $attribute_taxonomies as $attribute ) {
				if( 'color' === $attribute->attribute_type && $screen->taxonomy === 'pa_' . $attribute->attribute_name ) {
					$enqueue = true;
				}
			}
		}

		if( $enqueue ) {
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script('wowmall-color-picker');
		}
	}

	public static function instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}
}
wowmallVariations::instance();
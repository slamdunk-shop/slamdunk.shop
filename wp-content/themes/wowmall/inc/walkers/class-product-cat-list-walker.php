<?php
/**
 * WC_Product_Cat_List_Walker class
 *
 * @extends 	Walker
 * @class 		WC_Product_Cat_Dropdown_Walker
 * @version		2.3.0
 * @package		WooCommerce/Classes/Walkers
 * @author 		WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WC_Product_Cat_List_Walker' ) ) :

class Wowmall_WC_Product_Cat_List_Walker extends WC_Product_Cat_List_Walker {

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category in reference to parents.
	 * @param integer $current_object_id
	 */
	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {

		$output .= '<li class="cat-item cat-item-' . $cat->term_id;

		if ( $args['current_category'] === $cat->term_id ) {
			$output .= ' current-cat';
		}

		if ( $args['has_children'] && $args['hierarchical'] ) {
			$output .= ' cat-parent';
		}

		if ( $args['current_category_ancestors'] && $args['current_category'] && in_array( $cat->term_id, $args['current_category_ancestors'] ) ) {
			$output .= ' current-cat-parent';
		}

		$output .=  '"><div class=cat_data><a href="' . esc_url( get_term_link( (int) $cat->term_id, $this->tree_type ) ) . '">' . $cat->name . '</a>';

		if ( $args['show_count'] ) {
			$output .= ' <span class=count>' . $cat->count . '</span>';
		}
		$output .= '</div>';
	}
}

endif;

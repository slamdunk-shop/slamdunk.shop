<?php

class WowmallMainMenuWalker extends Walker_Nav_Menu {

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( '', $depth ) : '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param WP_Post  $item  Menu item data object.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		$content      = '';
		$has_children = false;

		if ( ! empty( $args ) ) {

			if ( is_array( $args ) ) {
				$has_children = $args['walker']->has_children;
			} elseif ( is_object( $args ) ) {
				$has_children = $args->walker->has_children;
			}

			if ( ! $has_children && 0 === $depth && ! empty( $item->wowmall_megamenu_page ) ) {
				$page_id   = (int) $item->wowmall_megamenu_page;
				$classes[] = 'menu-item-wowmall-megamenu';
				if ( wp_is_mobile() || apply_filters( 'wowmall_render_megamenus', true ) ) {
					if ( 'page' === get_post_type( $page_id ) ) {
						global $post;
						$default_post = $post;
						$GLOBALS['wowmall_mega_page'] = 1;
						$post         = get_post( $page_id );
						$content      = apply_filters( 'the_content', $post->post_content );
						$post         = $default_post;
						unset($GLOBALS['wowmall_mega_page'], $default_post);
					}
				}
			}
		}

		/**
		 * Filters the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$class_names .= ' data-id="' . esc_attr( $item->ID ) . '"';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id=' . esc_attr( $id ) . '' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array    $atts   {
		 *                         The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 * @type string    $title  Title attribute.
		 * @type string    $target Target attribute.
		 * @type string    $rel    The rel attribute.
		 * @type string    $href   The href attribute.
		 * }
		 *
		 * @param WP_Post  $item   The current menu item.
		 * @param stdClass $args   An object of wp_nav_menu() arguments.
		 * @param int      $depth  Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string   $title The menu item's title.
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$title       = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
		$item_output = '';
		if ( ! empty( $args ) ) {
			if ( is_array( $args ) ) {
				$item_output .= $args['before'];
			} elseif ( is_object( $args ) ) {
				$item_output .= $args->before;
			}
		}
		$item_output .= '<a' . $attributes . '>';
		if ( ! empty( $args ) ) {
			if ( is_array( $args ) ) {
				$item_output .= $args['link_before'] . $title . $args['link_after'];
			} elseif ( is_object( $args ) ) {
				$item_output .= $args->link_before . $title . $args->link_after;
			}
		}
		if ( wp_is_mobile() && ( $has_children || ! empty( $content ) ) ) {
			$item_output .= '<span class=menu-item-toggle></span>';
		}
		$item_output .= '</a>';
		if ( ! empty( $args ) ) {
			if ( is_array( $args ) ) {
				$item_output .= $args['after'];
			} elseif ( is_object( $args ) ) {
				$item_output .= $args->after;
			}
		}

		if ( ! empty( $content ) ) {
			$content = '<div class=wowmall-mega-sub>' . $content . '</div>';
			global $wowmall_options;
			if ( wp_is_mobile() || empty( $wowmall_options['optimize'] ) ) {
				$item_output .= $content;
			} else {
				global $wp_scripts;
				$registered = $wp_scripts->registered;
				if ( ! ( isset( $registered['wowmall-theme-script']->extra['data'] ) && strpos( $registered['wowmall-theme-script']->extra['data'], 'var megamenu_' . $item->ID ) ) ) {
					wp_localize_script( 'wowmall-theme-script', 'megamenu_' . $item->ID, $content );
				}
			}
		}

		/**
		 * Filters a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $item_output The menu item's starting HTML output.
		 * @param WP_Post  $item        Menu item data object.
		 * @param int      $depth       Depth of menu item. Used for padding.
		 * @param stdClass $args        An object of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
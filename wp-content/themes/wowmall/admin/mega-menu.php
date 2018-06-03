<?php

add_filter( 'wp_edit_nav_menu_walker', 'wowmall_wp_edit_nav_menu_walker', 10, 2 );

function wowmall_wp_edit_nav_menu_walker( $walker, $menu_id ) {

	$menu_locations = get_nav_menu_locations();

	if( isset( $menu_locations['primary'] ) && $menu_locations['primary'] == $menu_id ) {
		return 'Wowmall_Walker_Nav_Menu_Edit';
	}
	return $walker;
}

add_action( 'wp_update_nav_menu_item', 'wowmall_wp_update_nav_menu_item',10, 3 );

function wowmall_wp_update_nav_menu_item( $menu_id, $menu_item_db_id ) {
	if ( isset( $_REQUEST['menu-item-wowmall-megamenu-page'] ) && is_array( $_REQUEST['menu-item-wowmall-megamenu-page'] ) ) {
		$page = isset( $_REQUEST['menu-item-wowmall-megamenu-page'][ $menu_item_db_id ] ) ? $_REQUEST['menu-item-wowmall-megamenu-page'][ $menu_item_db_id ] : '';
		update_post_meta( $menu_item_db_id, '_menu_item_wowmall_megamenu_page', $page );
	}
}
<?php
/**
 * Widget API: Wowmall_Widget_Follow class
 *
 * @package Wowmall
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Wowmall_Widget_Follow extends Wowmall_Abstract_Widget {

	public function __construct() {
		
		$this->widget_cssclass    = 'wowmall-widget-follow';
		$this->widget_description = esc_html__( 'Display Social Media Profiles', 'wowmall' );
		$this->widget_id          = 'wowmall_widget_follow';
		$this->widget_name        = esc_html__( 'Wowmall Follow', 'wowmall' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'value' => '',
				'label' => esc_html__( 'Title', 'wowmall' ),
				'std'   => esc_html__( 'Follow us', 'wowmall' ),
			),
		);
		parent::__construct();

		add_action( 'wp_update_nav_menu', array( $this, 'flush_cache_after_update_menu' ), 10, 2 );
	}

	public function widget( $args, $instance ) {

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}
		ob_start();

		$this->setup_widget_data( $args, $instance );

		$this->widget_start( $args, $instance );

		wowmall_social_nav();

		$this->widget_end( $args );
		$this->reset_widget_data();

		echo '' . $this->cache_widget( $args, ob_get_clean() );
	}

	public function flush_cache_after_update_menu( $menu_id, $menu_data = array() ) {
		$menus = get_registered_nav_menus();
		if( ! empty( $menu_data['menu-name'] ) && $menus['social'] === $menu_data['menu-name'] ) {
			$this->flush_cache();
		}
	}
}

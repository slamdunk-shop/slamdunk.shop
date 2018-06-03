<?php
/**
 * Widget API: Wowmall_Widget_About class
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
class Wowmall_Widget_About extends Wowmall_Abstract_Widget {

	public function __construct() {

		$this->widget_cssclass    = 'wowmall-widget-about';
		$this->widget_description = esc_html__( 'Display an information about your site.', 'wowmall' );
		$this->widget_id          = 'wowmall_widget_about';
		$this->widget_name        = esc_html__( 'Wowmall About', 'wowmall' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'value' => '',
				'label' => esc_html__( 'Title', 'wowmall' ),
				'std'   => esc_html__( 'About us', 'wowmall' ),
			),
			'media_id' => array(
				'type'               => 'media',
				'value'              => '',
				'label'              => esc_html__( 'Choose image', 'wowmall' ),
			),
			'content'  => array(
				'type'              => 'textarea',
				'placeholder'       => esc_html__( 'Text or HTML', 'wowmall' ),
				'value'             => '',
				'label'             => esc_html__( 'Content:', 'wowmall' ),
				'sanitize_callback' => 'wp_filter_post_kses',
			),
			'button' => array(
				'type'  => 'checkbox',
				'value' => 0,
				'label' => esc_html__( 'Enable button', 'wowmall' ),
			),
			'button_page' => array(
				'type'  => 'pages',
				'value' => '',
				'label' => esc_html__( 'Button linked page', 'wowmall' ),
			),
			'button_url' => array(
				'type'  => 'text',
				'value' => '',
				'label' => esc_html__( 'Button link(leave blank to use pages)', 'wowmall' ),
			),
			'button_label' => array(
				'type'  => 'text',
				'value' => esc_html__( 'Read more', 'wowmall' ),
				'label' => esc_html__( 'Button label', 'wowmall' ),
			),
		);

		add_action( 'wowmall_widget_after_update', array( $this, 'delete_cache' ) );

		parent::__construct();
	}

	public function widget( $args, $instance ) {

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$this->setup_widget_data( $args, $instance );

		ob_start();

		$this->widget_start( $args, $instance );

		$media_id     = ! empty( $instance['media_id'] ) ? absint( $instance['media_id'] ) : false;
		$img          = wp_get_attachment_image( $media_id, array( 204, 204 ) );
		$button       = ! empty( $instance['button'] ) ? $instance['button'] : $this->settings['button']['value'];
		$content      = apply_filters( 'widget_text', wp_unslash( $this->use_wpml_translate( 'content' ) ) );
		$button_url   = false;
		$button_label = false;

		if ( $button ) {
			$button_label = ! empty( $instance['button_label'] ) ? $instance['button_label'] : $this->settings['button_label']['value'];
			$button_url   = ! empty( $instance['button_url'] )   ? $instance['button_url']   : $this->settings['button_url']['value'];
			if ( empty( $button_url ) ) {
				$button_page = ! empty( $instance['button_page'] )   ? $instance['button_page']   : $this->settings['button_page']['value'];
				if ( ! empty( $button_page ) ) {
					$button_url = get_permalink( $button_page );
				}
			}
		}
		if ( ! empty( $img ) ) { ?>
			<div class=widget-about-thumb>
				<?php if ( ! empty( $button_url ) && ! empty( $button_label ) ) {
					$img = sprintf( '<a class=widget-about-thumb-link href="%2$s">%1$s</a>', $img, esc_url( $button_url ) );
				}
				echo '' . $img; ?>
			</div>
		<?php } ?>

		<div class=widget-about-content><?php echo wp_kses_post( $content ); ?></div>
		<?php
		if ( ! empty( $button_url ) && ! empty( $button_label ) ) { ?>
			<a class="btn btn-primary btn-sm" href="<?php echo esc_url( $button_url ); ?>"><?php echo esc_html( $button_label ); ?></a>
		<?php }

		$this->widget_end( $args );

		$this->reset_widget_data();

		echo '' . $this->cache_widget( $args, ob_get_clean() );
	}
}

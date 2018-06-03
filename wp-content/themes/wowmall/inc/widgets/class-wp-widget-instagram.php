<?php
/**
 * Widget API: Wowmall_Widget_Instagram class
 *
 * @package Wowmall
 * @subpackage Widgets
 */

class Wowmall_Widget_Instagram extends Wowmall_Abstract_Widget {

	/**
	 * Instagram API server.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $service_url = 'https://www.instagram.com/';

	public function __construct() {

		$this->widget_cssclass    = 'wowmall-widget-instagram';
		$this->widget_description = esc_html__( 'Display Instagram Feed', 'wowmall' );
		$this->widget_id          = 'wowmall_widget_instagram';
		$this->widget_name        = esc_html__( 'Wowmall Instagram', 'wowmall' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'value' => '',
				'label' => esc_html__( 'Title', 'wowmall' ),
				'std'   => esc_html__( 'Instagram', 'wowmall' ),
			),
			'tag' => array(
				'type'  => 'text',
				'value' => '',
				'label' => esc_html__( 'Hashtag (enter without `#` symbol)', 'wowmall' ),
			),
			'image_counter' => array(
				'type'  => 'number',
				'value' => '6',
				'max'   => '12',
				'min'   => '1',
				'step'  => '1',
				'label' => esc_html__( 'Number of photos', 'wowmall' ),
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

		$tag = isset( $instance['tag'] ) ? esc_attr( $instance['tag'] ) : false;

		if ( ! $tag ) {
			echo '' . $args['before_widget'] . esc_html__( 'Enter a valid hashtag, please.', 'wowmall' ) . $args['after_widget'];
			return;
		}
		$image_counter = isset( $instance['image_counter'] ) ? absint( $instance['image_counter'] ) : $this->settings['image_counter']['value'];
		$photos        = $this->get_photos( $tag, $image_counter );

		if ( ! $photos ) {
			echo '' . $args['before_widget'] . esc_html__( 'No photos. Maybe you entered a invalid hashtag.', 'wowmall' ) . $args['after_widget'];
			return;
		}
		ob_start();

		$this->widget_start( $args, $instance ); ?>

		<div class=instagram-items>
		<?php foreach ( (array) $photos as $photo ) { ?>
			<div class=instagram-item>
				<?php echo '' . $this->get_image( $photo ); ?>
			</div>
		<?php } ?>
		</div>
		<?php $this->widget_end( $args );

		$this->reset_widget_data();

		echo '' . $this->cache_widget( $args, ob_get_clean() );
	}

	/**
	 * Get transient key to cache photos by key.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_transient_key( $instance = null ) {

		if ( ! isset( $instance['image_counter'] ) || ! isset( $instance['tag'] ) ) {
			return '';
		}
		return md5( $instance['tag'] . $instance['image_counter'] );
	}

	/**
	 * Retrieve a photos.
	 *
	 * @since  1.0.0
	 * @since  1.0.1  Removed `$clint_id` param. Changed Instagram URL to retrieve.
	 * @param  string $data        Hashtag.
	 * @param  int    $img_counter Number of images.
	 * @return mixed
	 */
	public function get_photos( $data, $img_counter ) {
		$transient_key = $this->get_transient_key(
			array( 'image_counter' => $img_counter, 'tag' => $data )
		);
		$cached = get_transient( $transient_key );

		if ( false !== $cached ) {
			return $cached;
		}
		$url = add_query_arg(
			array( '__a' => 1 ),
			sprintf( $this->get_tags_url(), $data )
		);
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) || empty( $response ) || '200' != $response ['response']['code'] ) {
			return false;
		}
		$result = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $result ) ) {
			return false;
		}
		if ( empty( $result['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
			return false;
		}
		/**
		 * Filter a order param for search photos by tag - `top_posts` or `media` (most recent).
		 *
		 * @since 1.0.1
		 * @var string
		 */
		$nodes   = $result['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
		$counter = 1;
		$photos  = array();

		foreach ( $nodes as $photo ) {
			array_push( $photos, array(
				'link'   => $photo['node']['shortcode'],
				'images' => $photo['node']['thumbnail_resources'],
			) );
			$counter++;
			if ( $counter > $img_counter ) {
				break;
			}
		}
		set_transient( $transient_key, $photos, HOUR_IN_SECONDS );

		return $photos;
	}

	/**
	 * Retrieve a HTML link with image.
	 *
	 * @since  1.0.0
	 * @since  1.0.1  Changed link `href` attribute.
	 * @param  array  $photo Item photo data.
	 * @return string
	 */
	public function get_image( $photo ) {
		$link   = sprintf( $this->get_post_url(), $photo['link'] );
		$width  = 150;
		$height = 150;
		$image  = $photo['images'][0]['src'];

		return sprintf( '<a class=instagram-link href="%s" target=_blank rel=nofollow><img class=instagram-img src="%s" alt="" width=%s height=%s></a>', esc_url( $link ), esc_url( $image ), $width, $height );
	}

	/**
	 * Retrieve a URL for post.
	 *
	 * @since  1.0.1
	 * @return string
	 */
	public function get_post_url() {
		return $this->service_url . 'p/%s/';
	}

	/**
	 * Retrieve a URL for tags.
	 *
	 * @since  1.0.1
	 * @return string
	 */
	public function get_tags_url() {
		return $this->service_url . 'explore/tags/%s/';
	}

	/**
	 * Clear cache.
	 *
	 * @since 1.0.0
	 */
	public function delete_cache( $instance ) {
		delete_transient( $this->get_transient_key( $instance ) );
	}
}

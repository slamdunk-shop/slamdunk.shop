<?php
if ( ! class_exists( 'wowmallInstagram' ) ) {

	class wowmallInstagram {

		private $service_url = 'https://www.instagram.com/', $atts;

		protected static $_instance = null;

		public function __construct() {

			add_shortcode( 'wowmall_instagram', array(
				$this,
				'shortcode',
			) );

			add_action( 'save_post', array(
				$this,
				'delete_cache',
			) );

			add_action( 'trashed_post', array(
				$this,
				'delete_cache',
			) );

			add_action( 'after_switch_theme', array(
				$this,
				'delete_cache',
			) );
		}

		public function shortcode( $atts = array() ) {

			$atts = shortcode_atts( array(
				'title'       => '',
				'title_align' => 'left',
				'rows'        => 1,
				'columns'     => 4,
				'search_by'   => 'hashtag',
				'search'      => '',
				'likes'       => false,
				'comments'    => false,
				'css'         => '',
				'el_class'    => '',
			), $atts );

			if ( empty( $atts['search'] ) ) {
				return esc_html__( 'Enter a valid hashtag or username, please.', 'wowmall-shortcodes' );
			}
			$this->atts = $atts;
			$photos     = $this->get_photos();

			if ( ! $photos ) {
				return esc_html__( 'No photos. Maybe you entered a invalid hashtag or username.', 'wowmall-shortcodes' );
			}
			$width = round( 1920 / $atts['columns'] );
			ob_start(); ?>
			<div class=wowmall-instagram>
				<div class="instagram-items instagram-items__cols-<?php echo $atts['columns']; ?>">
					<?php foreach ( (array) $photos as $photo ) { ?>
						<div class=instagram-item>
							<?php echo '' . $this->get_image( $photo, $width, $atts ); ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php return ob_get_clean();
		}

		public function get_transient_key() {

			if ( ! isset( $this->atts['rows'] ) || ! isset( $this->atts['columns'] ) || ! isset( $this->atts['search'] ) ) {
				return '';
			}

			return md5( $this->atts['search'] . $this->atts['rows'] . $this->atts['columns'] );
		}

		/**
		 * @since  1.0.0
		 * @since  1.4.8  Changed api for user.
		 */

		public function get_photos() {
			$transient_key = $this->get_transient_key();
			$cached        = get_transient( $transient_key );

			if ( false !== $cached ) {
				return $cached;
			}
			$result = false;
			if ( 'user' === $this->atts['search_by'] ) {
				$name = $this->atts['search'];
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "$this->service_url$name/");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				$response = curl_exec($ch);
				$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				if($http=="200") {
					$doc = new DOMDocument();
					$doc->loadHTML($response);
					$xpath = new DOMXPath($doc);
					$js = $xpath->query('//body/script[@type="text/javascript"]')->item(0)->nodeValue;
					$start = strpos($js, '{');
					$end = strrpos($js, ';');
					$json = substr($js, $start, $end - $start);
					$data = json_decode($json, true);
					$result = $data["entry_data"]["ProfilePage"][0];
				}
			} else {
				$url = $this->service_url . 'explore/tags/' . $this->atts['search'] . '/?__a=1';
				$response = wp_remote_get( $url );
				if ( is_wp_error( $response ) || empty( $response ) || 200 !== $response ['response']['code'] ) {
					return false;
				}
				$result = json_decode( wp_remote_retrieve_body( $response ), true );
			}

			if ( ! is_array( $result ) ) {
				return false;
			}

			$photos      = array();
			$counter     = 1;
			$img_counter = $this->atts['rows'] * $this->atts['columns'];

			if ( ! empty( $result['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
				$nodes = $result['graphql']['user']['edge_owner_to_timeline_media']['edges'];
			}

			elseif ( ! empty( $result['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
				$nodes = $result['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
			}

			else {
				return false;
			}
			foreach ( $nodes as $node ) {
				$node = $node['node'];
				if ( ! $node['is_video'] ) {
					array_push( $photos, array(
						'link'     => $node['shortcode'],
						'thumbs'   => $node['thumbnail_resources'],
						'likes'    => $node['edge_liked_by']['count'],
						'comments' => $node['edge_media_to_comment']['count'],
					) );
					$counter++;
					if ( $counter > $img_counter ) {
						break;
					}
				}
			}
			set_transient( $transient_key, $photos, HOUR_IN_SECONDS );

			return $photos;
		}

		public function get_image( $photo, $width ) {
			global $wowmall_options;

			$link      = sprintf( $this->get_post_url(), $photo['link'] );
			$thumbs    = $photo['thumbs'];
			$src_key   = $this->getClosest( $width, $thumbs );
			$sizes_arr = array();

			foreach ( $photo['thumbs'] as $thumb ) {
				$sizes_arr[] = $thumb['src'] . ' ' . $thumb['config_width'] . 'w';
			}

			$sizes = join( ', ', $sizes_arr );

			if ( ! empty( $wowmall_options['lazy'] ) ) {
				global $is_edge, $is_IE;
				$optimize = wowmallOptimizer::instance();
				$svg      = $optimize->wowmall_svg_placeholder_base64( $width, $width );
				if ( $is_edge || $is_IE ) {
					$svg = 'data:image/svg+xml,%3Csvg%20width%3D%22' . $width . '%22%20height%3D%22' . $width . '%22%20xmlns%3D%22http:%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3C%2Fsvg%3E';
				}

				$image = sprintf( '<img class="instagram-img swiper-lazy" data-src="%1$s" src="%2$s" alt data-sizes="(max-width: %3$spx) 100vw, %3$spx" width=%3$s height=%3$s data-srcset="%4$s">', esc_url( $thumbs[ $src_key ]['src'] ), $svg, $thumbs[ $src_key ]['config_width'], $sizes );
			}
			else {
				$image = sprintf( '<img class=instagram-img src="%1$s" alt sizes="(max-width: %2$spx) 100vw, %2$spx" width=%2$s height=%2$s data-srcset="%3$s">', esc_url( $thumbs[ $src_key ]['src'] ), $thumbs[ $src_key ]['config_width'], $sizes );
			}

			$class = '';

			if ( $this->atts['likes'] || $this->atts['comments'] ) {
				$class = ' with-over';
				$image .= '<div class=instagram-item__over>';
				if ( $this->atts['likes'] ) {
					$image .= '<span class=instagram-item__likes>' . $photo['likes'] . '</span>';
				}

				if ( $this->atts['comments'] ) {
					$image .= '<span class=instagram-item__comments>' . $photo['comments'] . '</span>';
				}
				$image .= '</div>';
			}

			return sprintf( '<a class="instagram-link%s" href="%s" target=_blank rel=nofollow>%s</a>', $class, esc_url( $link ), $image );
		}

		public function get_post_url() {
			return $this->service_url . 'p/%s/';
		}

		public function getClosest( $search, $arr ) {
			$closest = null;
			foreach ( $arr as $key => $item ) {
				if ( $closest === null || abs( $search - $arr[ $closest ]['config_width'] ) > abs( $item['config_width'] - $search ) ) {
					$closest = $key;
				}
			}

			return $closest;
		}

		public function delete_cache() {
			delete_transient( $this->get_transient_key() );
		}

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallInstagram::instance();
}
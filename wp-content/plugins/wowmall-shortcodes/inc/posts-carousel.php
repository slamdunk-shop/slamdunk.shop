<?php

if ( ! class_exists( 'wowmallPostsCarousel' ) ) {

	class wowmallPostsCarousel {

		protected static $_instance = null;

		public function __construct() {

			add_shortcode( 'wowmall_posts_carousel', array(
				$this,
				'shortcode',
			) );
		}

		public function shortcode( $atts = array() ) {

			$atts = shortcode_atts( array(
				'title'   => '',
				'title_align' => 'left',
				'visible' => 4,
				'show'    => 'all',
				'format'    => 'all',
				'orderby' => 'Random',
				'order'   => 'ASC',
				'css' => '',
				'el_class' => '',

			), $atts );

			$atts['format'] = explode( ',', $atts['format'] );

			$query_args = array(
				'post_type' => 'post',
				'posts_per_page' => $atts['visible']*2,
				'post_status'    => 'publish',
				'no_found_rows'  => 1,
				'order'          => $atts['order'],
				'meta_query'     => array()
			);

			if( ! in_array( 'all', $atts['format'] ) ) {

				$atts['format'] = array_map( array( $this, 'rename_post_format' ), $atts['format'] );
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'post_format',
						'field'    => 'slug',
						'terms'    => $atts['format'],
						'operator' => 'NOT IN'
					)
				);
			}

			$query_args['meta_query']   = array_filter( $query_args['meta_query'] );

			switch ( $atts['show'] ) {
				case 'featured' :
					$query_args['meta_query'][] = array(
						'key'   => '_featured',
						'value' => 'yes'
					);
					break;
			}

			switch ( $atts['orderby'] ) {
				case 'rand' :
					$query_args['orderby']  = 'rand';
					break;
				default :
					$query_args['orderby']  = 'date';
			}

			$posts = new WP_Query( $query_args );

			if( $posts->have_posts() ) {

				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script( 'wowmall-posts-carousel', wowmallShortcodes::$pluginurl . '/assets/js/posts-carousel' . $min . '.js', array( 'wowmall-theme-script' ), null, true );

				ob_start();
					if(  '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) {
						$class = '';
						if( '' !== $atts['css'] && function_exists( 'vc_shortcode_custom_css_class' ) ) {
							$class .= vc_shortcode_custom_css_class( $atts['css'], ' ' );
						}
						if( ! empty( $atts['el_class'] ) ) {
							$class .= ' ' . $atts['el_class'];
						} ?>
						<div class="<?php echo $class; ?>">
					<?php } ?>
					<div class=wowmall-posts-carousel>
					<?php if( ! empty( $atts['title'] ) ) {
							$style = '';
							if( 'left' !== $atts['title_align'] ) {
								$style = ' style="text-align:' . $atts['title_align'] . '"';
							}
							echo '<h4' . $style . '>' . $atts['title'] . '</h4>';
						}
							$id = uniqid();
							?>
							<div class=swiper-container id=<?php echo $id; ?> data-visible=<?php echo $atts['visible']; ?>>
								<div class="swiper-wrapper">
									<?php
									while ( $posts->have_posts() ) {
										$posts->the_post();
										include( wowmall_shortcodes()->plugin_path() . '/templates/posts-carousel-post.php' );
									}
									?>
								</div>
							</div>
							<?php if ( ! wp_is_mobile() && ! empty( $atts['title'] ) ) { ?>
								<div class=swiper-button-prev id=swiper-button-prev<?php echo $id; ?>></div>
								<div class=swiper-button-next id=swiper-button-next<?php echo $id; ?>></div>
							<?php } ?>
						</div>
					<?php if(  '' !== $atts['css'] || ! empty( $atts['el_class'] ) ) { ?>
							</div>
						<?php }

					wp_reset_query();

					return ob_get_clean();
				}
				return '';
			}

			public function rename_post_format( $item ) {
				return 'post-format-' . $item;
			}

			public static function instance() {

				if ( is_null( self::$_instance ) ) {

					self::$_instance = new self();
				}

				return self::$_instance;
			}
		}
	wowmallPostsCarousel::instance();
	}
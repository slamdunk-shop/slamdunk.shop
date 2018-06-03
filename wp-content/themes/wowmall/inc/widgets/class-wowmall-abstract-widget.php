<?php
/**
 * Abstract widget class.
 * @package    Wowmall
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Wowmall_Abstract_Widget' ) ) {

	/**
	 * Define Cherry_Abstract_Widget class
	 */
	abstract class Wowmall_Abstract_Widget extends WP_Widget {

		/**
		 * CSS class
		 * @var string
		 */
		public $widget_cssclass;

		/**
		 * Widget description
		 * @var string
		 */
		public $widget_description;

		/**
		 * Widget ID
		 * @var string
		 */
		public $widget_id;

		/**
		 * Widget name
		 * @var string
		 */
		public $widget_name;

		/**
		 * Settings
		 * @var array
		 */
		public $settings;

		/**
		 * Existing field types
		 * @var array
		 */
		public $field_types = array();

		/**
		 * Temporary arguments holder
		 * @var array
		 */
		public $args;

		/**
		 * Temporary instance holder
		 * @var array
		 */
		public $instance;

		/**
		 * Constructor
		 */
		public function __construct() {

			$widget_ops = array(
				'classname'   => $this->widget_cssclass,
				'description' => $this->widget_description,
			);
			parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

			add_action( 'save_post', array(
				$this,
				'flush_cache',
			) );
			add_action( 'deleted_post', array(
				$this,
				'flush_cache',
			) );
			add_action( 'switch_theme', array(
				$this,
				'flush_cache',
			) );
			add_filter( 'widget_display_callback', array(
				$this,
				'prepare_instance',
			), 10, 2 );
		}

		/**
		 * Get default widget instance from settings
		 * @since  1.0.0
		 *
		 * @param  array     $instance The current widget instance's settings.
		 * @param  WP_Widget $widget   The current widget instance.
		 *
		 * @return array
		 */
		public function prepare_instance( $instance, $widget ) {

			if ( ! empty( $instance ) ) {
				return $instance;
			}
			$instance = array();

			if ( empty( $widget->settings ) ) {
				return $instance;
			}
			foreach ( $widget->settings as $key => $data ) {

				if ( ! isset( $data['value'] ) ) {
					$instance[ $key ] = '';
				}
				else {
					$instance[ $key ] = $data['value'];
				}
			}

			return $instance;
		}

		/**
		 * Get cached widget from WordPress object cache
		 * @since  1.0.0
		 *
		 * @param  array $args widget arguments array.
		 *
		 * @return bool
		 */
		public function get_cached_widget( $args ) {

			$cache = wp_cache_get( $this->widget_id, 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}
			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				echo '' . $cache[ $args['widget_id'] ];

				return true;
			}

			return false;
		}

		/**
		 * Save widget into WordPress object cache
		 * @since  1.0.0
		 *
		 * @param  array $args widget arguments.
		 * @param  [type] $content widget content.
		 *
		 * @return string the content that was cached
		 */
		public function cache_widget( $args, $content ) {
			wp_cache_set( $this->widget_id, array( $args['widget_id'] => $content ), 'widget' );

			return $content;
		}

		/**
		 * Flush the cache
		 * @since  1.0.0
		 * @return void
		 */
		public function flush_cache() {
			wp_cache_delete( $this->widget_id, 'widget' );
		}

		/**
		 * Output the html at the start of a widget
		 * @since  1.0.0
		 *
		 * @param  array $args     widget arguments.
		 * @param  array $instance widget instance.
		 *
		 * @return void
		 */
		public function widget_start( $args, $instance ) {

			echo '' . $args['before_widget'];

			$default = empty( $this->settings['title']['std'] ) ? '' : $this->settings['title']['std'];
			$title   = empty( $instance['title'] ) ? $default : $instance['title'];
			$title   = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			if ( $title ) {
				echo '' . $args['before_title'] . $title . $args['after_title'];
			}
		}

		/**
		 * Output the html at the end of a widget
		 * @since  1.0.0
		 *
		 * @param  array $args widget arguments.
		 *
		 * @return void
		 */
		public function widget_end( $args ) {
			echo '' . $args['after_widget'];
		}

		/**
		 * Update function.
		 * @since  1.0.0
		 * @see    WP_Widget->update
		 *
		 * @param  array $new_instance new widget instance, passed from widget form.
		 * @param  array $old_instance old instance, saved in database.
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

			if ( empty( $this->settings ) ) {
				return $instance;
			}
			// Loop settings and get values to save.
			foreach ( $this->settings as $key => $setting ) {
				if ( ! isset( $setting['type'] ) ) {
					continue;
				}
				// Format the value based on settings type.
				switch ( $setting['type'] ) {
					case 'number' :
						$instance[ $key ] = absint( $new_instance[ $key ] );

						if ( isset( $setting['min'] ) && '' !== $setting['min'] ) {
							$instance[ $key ] = max( $instance[ $key ], $setting['min'] );
						}
						if ( isset( $setting['max'] ) && '' !== $setting['max'] ) {
							$instance[ $key ] = min( $instance[ $key ], $setting['max'] );
						}
						break;
					case 'textarea' :
						$instance[ $key ] = wp_kses( trim( wp_unslash( $new_instance[ $key ] ) ), wp_kses_allowed_html( 'post' ) );
						break;
					case 'checkbox' :
						$instance[ $key ] = empty( $new_instance[ $key ] ) ? 0 : 1;
						break;
					default:
						$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
						break;
				}
				/**
				 * Sanitize the value of a setting.
				 */
				$instance[ $key ] = apply_filters( 'woocommerce_widget_settings_sanitize_option', $instance[ $key ], $new_instance, $key, $setting );
			}
			$this->flush_cache();

			do_action( 'wowmall_widget_after_update', $instance );

			return $instance;
		}

		/**
		 * Sanitize widget instance item
		 * @since  1.0.0
		 *
		 * @param  mixed $input instance item to sanitize.
		 *
		 * @return mixed
		 */
		public function sanitize_instance_item( $input ) {
			if ( is_array( $input ) ) {
				return array_filter( $input );
			}
			else {
				return sanitize_text_field( $input );
			}
		}

		/**
		 * Show widget form
		 * @since  1.0.0
		 * @see    WP_Widget->form
		 *
		 * @param  array $instance current widget instance.
		 *
		 * @return void
		 */
		public function form( $instance ) {

			if ( empty( $this->settings ) ) {
				return;
			}
			foreach ( $this->settings as $key => $setting ) {

				$class = isset( $setting['class'] ) ? esc_attr( $setting['class'] ) : '';
				$value = isset( $instance[ $key ] ) ? 'textarea' === $setting['type'] ? esc_textarea( $instance[ $key ] ) : 'checkbox' === $setting['type'] ? $instance[ $key ] : esc_attr( $instance[ $key ] ) : $setting['value'];
				$id    = $this->get_field_id( $key );
				$name  = $this->get_field_name( $key );
				$label = '<label for=' . $id . '>' . $setting['label'] . '</label>';

				switch ( $setting['type'] ) {

					case 'text' :
						?>
						<p>
							<?php echo '' . $label; ?>
							<input class="widefat <?php echo esc_attr( $class ); ?>"
								   id=<?php echo esc_attr( $id ); ?> name=<?php echo esc_attr( $name ); ?> type=text
								   value="<?php echo esc_attr( $value ); ?>"/>
						</p>
						<?php
						break;

					case 'number' :
						?>
						<p>
							<?php echo '' . $label; ?>
							<input class="widefat <?php echo esc_attr( $class ); ?>"
								   id=<?php echo esc_attr( $id ); ?> name=<?php echo esc_attr( $name ); ?> type=number
								   step=<?php echo esc_attr( $setting['step'] ); ?> min=<?php echo esc_attr( $setting['min'] ); ?>
								   max=<?php echo esc_attr( $setting['max'] ); ?> value=<?php echo esc_attr( $value ); ?>>
						</p>
						<?php
						break;

					case 'select' :
						?>
						<p>
							<?php echo '' . $label; ?>
							<select class="widefat <?php echo esc_attr( $class ); ?>"
									id=<?php echo esc_attr( $id ); ?> name=<?php echo esc_attr( $name ); ?>>
								<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
									<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
								<?php endforeach; ?>
							</select>
						</p>
						<?php
						break;

					case 'textarea' :
						?>
						<p>
							<?php echo '' . $label; ?>
							<textarea class="widefat <?php echo esc_attr( $class ); ?>"
									  id=<?php echo esc_attr( $id ); ?> name=<?php echo esc_attr( $name ); ?> cols=20
									  rows=3><?php echo esc_attr( $value ); ?></textarea>
							<?php if ( isset( $setting['desc'] ) ) : ?>
								<small><?php echo esc_html( $setting['desc'] ); ?></small>
							<?php endif; ?>
						</p>
						<?php
						break;

					case 'checkbox' :
						?>
						<p>
							<input class="checkbox <?php echo esc_attr( $class ); ?>"
								   id=<?php echo esc_attr( $id ); ?> name=<?php echo esc_attr( $name ); ?> type=checkbox
								   value=1 <?php checked( $value, 1 ); ?> />
							<?php echo '' . $label; ?>
						</p>
						<?php
						break;

					case 'pages' :
						?>
						<p>
							<?php echo '' . $label;
							wp_dropdown_pages( array(
								'class'    => 'widefat ' . esc_attr( $class ),
								'name'     => $name,
								'id'       => $id,
								'selected' => $value,
							) ); ?>
						</p>
						<?php
						break;

					case 'media' :
						$img = empty( $value ) ? '' : wp_get_attachment_image_src( $value, 'thumbnail' );
						?>
						<p>
						<div class=wowmall-admin-media-wrapper>
							<div class=wowmall-admin-media-image style="<?php if ( is_array( $img ) ) {
								echo 'background-image:url(' . $img[0] . ')"';
							} else { ?>display:none<?php } ?>">
								<span class="dashicons dashicons-dismiss"></span>
							</div>
							<div class=wowmall-admin-media-add<?php if ( is_array( $img ) ) { ?> style="display: none;"<?php } ?>>
								<span><?php echo '' . $label; ?></span>
							</div>
							<input autocomplete=off type=hidden
								   id=<?php echo esc_attr( $id ); ?> name=<?php echo esc_attr( $name ); ?>
								   value="<?php echo esc_attr( $value ); ?>">
						</div>
						</p>
						<?php
						break;
					default :
						break;
				}
			}
		}

		/**
		 * Parse callback data.
		 * @since  1.0.0
		 *
		 * @param  array $options_callback Callback data.
		 *
		 * @return array
		 */
		public function get_callback_data( $options_callback ) {

			if ( 2 === count( $options_callback ) ) {

				$callback = array(
					'callback' => $options_callback,
					'args'     => array(),
				);

				return $callback;
			}
			$callback = array(
				'callback' => array_slice( $options_callback, 0, 2 ),
				'args'     => $options_callback[2],
			);

			return $callback;
		}

		/**
		 * Save current widget data to property object properties
		 * @since  1.0.0
		 *
		 * @param  array $args     widget arguments.
		 * @param  array $instance current widget instance.
		 */
		public function setup_widget_data( $args, $instance ) {
			$this->args     = $args;
			$this->instance = $instance;
		}

		/**
		 * Clear current widget data.
		 * @since  1.0.0
		 */
		public function reset_widget_data() {

			$this->args     = null;
			$this->instance = null;
		}

		/**
		 * Add widget_id-related CSS selector
		 * @since  1.2.0
		 *
		 * @param  string $selector Selector inside widget.
		 * @param  array  $args     widget arguments (optional, pass it only setup_widget_data not called before).
		 *
		 * @return string|bool
		 */
		public function add_selector( $selector = null, $args = array() ) {

			if ( null == $this->args && empty( $args ) ) {
				return false;
			}
			$args = null !== $this->args ? $this->args : $args;

			return sprintf( '#%1$s %2$s', $args['widget_id'], $selector );
		}

		/**
		 * Retrieve a string translation via WPML.
		 * @since  1.0.1
		 *
		 * @param  [type] $id Widget setting ID.
		 */
		public function use_wpml_translate( $id ) {
			return ! empty( $this->instance[ $id ] ) ? apply_filters( 'wpml_translate_single_string', $this->instance[ $id ], 'Widgets', $this->widget_name . ' - ' . $id ) : '';
		}
	}
}

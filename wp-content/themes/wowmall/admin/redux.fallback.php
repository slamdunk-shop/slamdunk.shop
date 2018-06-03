<?php
/**
 * fallback redux class
 */
if ( ! class_exists( 'Redux' ) && ! class_exists( 'ReduxFramework' ) ) {
	global $wowmall_options;

	class Redux {
		public static $hasOptions = false;

		public static function setArgs( $option, $args ) {
			$options = get_option( $option, false );
			if ( ! empty( $options ) ) {
				self::$hasOptions = true;
			}
		}

		public static function setSections( $option, $args_arr ) {
			global $wowmall_options;
			$options = get_option( $option, false );
			if ( ! empty( $options ) ) {
				$wowmall_options = $options;

				return;
			}
			foreach ( $args_arr as $args ) {
				if ( isset( $args['fields'] ) && ! empty( $args['fields'] ) ) {
					foreach ( $args['fields'] as $field ) {
						if ( isset( $field['default'] ) && isset( $field['id'] ) ) {
							$id                   = $field['id'];
							$wowmall_options[$id] = $field['default'];
						}
					}
				}
			}
		}

		public static function getOption( $option, $key ) {
			$options = get_option( $option, false );
			if ( isset( $options[$key] ) ) {
				return $options[$key];
			}

			return null;
		}

		public static function setOption( $option, $key, $value ) {
			$options = get_option( $option, false );
			if ( is_array( $options ) ) {
				$options[$key] = $value;
			}
			else {
				$options = array(
					$key => $value,
				);
			}
			update_option( $option, $options );
		}
	}

	function wowmall_fallback_assets() {
		wp_enqueue_style( 'google-font', 'http://fonts.googleapis.com/css?family=PT+Sans+Narrow%3A400%2C700&#038;subset=latin&#038;ver=1487929595' );
		wp_enqueue_style( 'wowmall-custom-css', WOWMALL_THEME_URI . '/assets/css/fallback-styles.css', array() );
	}

	add_action( 'wp_enqueue_scripts', 'wowmall_fallback_assets', 12 );
}
<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_license' ) ) {

	/**
	 * Main ReduxFramework_license class
	 *
	 * @since       1.0.0
	 */
	class ReduxFramework_license extends ReduxFramework {

		/**
		 * Field Constructor.
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		function __construct( $field = array(), $value = '', $parent ) {

			$this->parent = $parent;
			$this->field  = $field;
			$this->value  = $value;

			if ( empty( $this->extension_dir ) ) {
				$this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
				$this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
			}

			$defaults    = array(
				'options'          => array(),
				'stylesheet'       => '',
				'output'           => true,
				'enqueue'          => true,
				'enqueue_frontend' => true,
			);
			$this->field = wp_parse_args( $this->field, $defaults );

		}

		/**
		 * Field Render Function.
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {

			$activated = wowmall_is_activated();
			$value     = esc_attr( $this->value );

			if ( $activated ) {

				echo '<input disabled readonly value="' . $value . '" type="text" id="' . $this->field['id'] . '" class="regular-text">';

			} else {

				wp_nonce_field( 'wowmall_activate_license', '_ajax_nonce', false );

				echo '<input type="text" id="' . $this->field['id'] . '" class="regular-text wowmall-license noUpdate"><br><br>
				<div class="wowmall-activate-license-wrapper">
				<button type="button" class="wowmall-activate-license button-primary">' . esc_html__( 'Activate', 'wowmall' ) . '</button><span class="spinner"></span></div>';
			}
		}

		/**
		 * Enqueue Function.
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {

			wp_enqueue_script( 'redux-field-icon-select-js', $this->extension_url . 'field_license.js', array( 'jquery' ), time(), true );

			wp_enqueue_style( 'redux-field-icon-select-css', $this->extension_url . 'field_license.css', time(), true );

		}
	}
}

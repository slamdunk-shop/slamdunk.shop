<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_extension_license' ) ) {

	/**
	 * Main ReduxFramework_extension_license extension class
	 *
	 * @since       3.1.6
	 */
	class ReduxFramework_extension_license extends ReduxFramework {

		// Protected vars
		protected     $parent;
		public        $extension_url;
		public        $extension_dir;
		public static $theInstance;

		/**
		 * Class Constructor. Defines the args for the extions class
		 *
		 * @since       1.0.0
		 * @access      public
		 *
		 * @param       array $sections   Panel sections.
		 * @param       array $args       Class constructor arguments.
		 * @param       array $extra_tabs Extra panel tabs.
		 *
		 * @return      void
		 */
		public function __construct( $parent ) {

			$this->parent = $parent;
			if ( empty( $this->extension_dir ) ) {
				$this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
			}
			$this->field_name = 'license';

			self::$theInstance = $this;

			add_filter( 'redux/' . $this->parent->args['opt_name'] . '/field/class/' . $this->field_name, array(
				&$this,
				'overload_field_path',
			) );

			add_action( 'wp_ajax_wowmall_activate_license', array(
				$this,
				'wowmall_activate_license',
			) );
		}

		public function getInstance() {
			return self::$theInstance;
		}

		public function overload_field_path( $field ) {
			return dirname( __FILE__ ) . '/' . $this->field_name . '/field_' . $this->field_name . '.php';
		}

		public function wowmall_activate_license() {

			check_ajax_referer( 'wowmall_activate_license' );

			if ( isset( $_POST['code'] ) ) {

				$theme = wp_get_theme( get_template() );

				$response = wp_safe_remote_post( 'https://lic.tonytemplates.com/', array(
					'body' => array(
						'code'         => esc_html( $_POST['code'] ),
						'item_id'      => '19395344',
						'item_version' => $theme->get( 'Version' ),
						'wp_version'   => get_bloginfo( 'version' ),
						'host'         => get_bloginfo( 'url' ),
						'name'         => get_bloginfo( 'name' ),
					),
				) );

				$response_body = wp_remote_retrieve_body( $response );

				if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
					wp_send_json_error( $response_body );
				}
				$response_body = json_decode( $response_body, true );
				if ( isset( $response_body['error'] ) ) {
					if( 404 === $response_body['error'] ) {
						wp_send_json_error( esc_html__( 'Not valid code', 'wowmall' ) );
					} else {
						wp_send_json_error( $response_body['description'] );
					}
				}
				Redux::setOption( 'wowmall_options', $_POST['field_id'], $_POST['code'] );
				wp_send_json_success( $response_body['description'] );
			}
			wp_send_json_error( esc_html__( 'Activation failed. Please try again later.', 'wowmall' ) );
		}

	}
}

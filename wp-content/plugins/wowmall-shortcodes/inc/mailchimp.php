<?php

if ( ! class_exists( 'wowmallMailChimp' ) ) {

	class wowmallMailChimp {

		protected static $_instance = null;

		public function __construct () {

			add_shortcode( 'wowmall_mailchimp', array(
				$this,
				'shortcode',
			) );
			if ( is_admin() ) {
				add_action( 'vc_before_init', array(
					$this,
					'vc_map',
				) );
			}
		}

		public function shortcode ( $atts = array(), $content = null ) {
			if( isset( $atts['form'] ) ) {
				$atts['pretext'] = $content;
			}
			ob_start();
			if ( function_exists( 'wowmall_footer_subscribe' ) ) {
				wowmall_footer_subscribe( $atts );
			}

			return ob_get_clean();
		}

		public function vc_map () {

			global $wowmall_options;
			
			$forms = wowmall()->newsletter_forms();
			
			if( empty( $forms ) ) {
				$forms = array();
			}

			$params = array(
				'name'        => esc_html__( 'Wowmall MailChimp', 'wowmall-shortcodes' ),
				'base'        => 'wowmall_mailchimp',
				'description' => esc_html__( 'Add MailChimp form (for footer)', 'wowmall-shortcodes' ),
				'category'    => esc_html__( 'Wowmall', 'wowmall-shortcodes' ),
				'weight'      => 1000,
				'params'      => array(
					array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Title', 'wowmall-shortcodes' ),
						'param_name' => 'title',
						'value'      => isset( $wowmall_options['footer_newsletter_title'] ) ? $wowmall_options['footer_newsletter_title'] : '',
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => esc_html__( 'Pretext', 'wowmall-shortcodes' ),
						'param_name' => 'content',
						'value'      => isset( $wowmall_options['footer_newsletter_text'] ) ? $wowmall_options['footer_newsletter_text'] : '',
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'MailChimp Form', 'wowmall-shortcodes' ),
						'param_name' => 'form',
						'value'      => array_flip( $forms ),
					),
				),
			);

			vc_map( $params );
		}

		public static function instance () {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallMailChimp::instance();
}
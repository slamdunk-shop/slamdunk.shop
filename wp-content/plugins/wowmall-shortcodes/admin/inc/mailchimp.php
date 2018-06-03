<?php

if ( ! class_exists( 'wowmallMailChimpAdmin' ) ) {

	class wowmallMailChimpAdmin {

		protected static $_instance = null;

		public function __construct() {

			add_action( 'vc_before_init', array(
				$this,
				'vc_map',
			) );
		}

		public function vc_map() {

			if ( ! function_exists( 'wowmall_footer_subscribe' ) ) {
				return;
			}

			global $wowmall_options;

			$forms = function_exists( 'wowmall' )  ? wowmall()->newsletter_forms() : array();

			if ( empty( $forms ) ) {
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
						'value'      => ! empty( $wowmall_options['footer_newsletter_title'] ) ? $wowmall_options['footer_newsletter_title'] : '',
					),
					array(
						'type'       => 'textarea_html',
						'heading'    => esc_html__( 'Pretext', 'wowmall-shortcodes' ),
						'param_name' => 'content',
						'value'      => ! empty( $wowmall_options['footer_newsletter_text'] ) ? $wowmall_options['footer_newsletter_text'] : '',
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

		public static function instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	wowmallMailChimpAdmin::instance();
}
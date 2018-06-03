<?php
/*
	Plugin Name: Wowmall Fontello Icons Importer
	Version: 1.0.0
	Author:
	Author URI:
*/

if ( ! defined( 'ABSPATH' ) ) {

	header( 'HTTP/1.0 404 Not Found', true, 404 );

	exit;
}

class Wowmall_Icons_Importer {

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Wowmall_Icons_Importer Instance.
	 *
	 * Ensures only one instance of Wowmall_Icons_Importer is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see wowmall_icons_importer()
	 * @return Wowmall_Icons_Importer - Main instance.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Sets up needed actions/filters for the theme to initialize.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if( ! is_admin() ) {
			return;
		}

		$url = $_SERVER['REQUEST_URI'];

		if( false !== strpos( $url, '/wp-admin/admin.php?page=bsf-font-icon-manager' ) || ( defined('DOING_AJAX') && DOING_AJAX ) ) {

			add_action( 'after_setup_theme', array(
				$this,
				'fontello_importer'
			), 11 );
		}
	}

	function fontello_importer() {

		if ( class_exists( 'AIO_Icon_Manager' ) ) {
			require_once 'inc/fontello-importer.php';
		}
	}
}

Wowmall_Icons_Importer::instance();

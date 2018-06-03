<?php

/**
 * Class Radium_Theme_Importer
 *
 * This class provides the capability to import demo content as well as import widgets and WordPress menus
 *
 * @since    2.2.0
 *
 * @category RadiumFramework
 * @package  NewsCore WP
 * @author   Franklin M Gitonga
 * @link     http://radiumthemes.com/
 *
 *
 * Modified to work with Reduxframework/Wbc_importer extension
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'Radium_Theme_Importer' ) ) {

	class Radium_Theme_Importer {

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 2.2.0
		 *
		 * @var object
		 */
		public $theme_options_file;

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 2.2.0
		 *
		 * @var object
		 */
		public $widgets;

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 2.2.0
		 *
		 * @var object
		 */
		public $content_demo;

		/**
		 * Flag imported to prevent duplicates
		 *
		 * @since 2.2.0
		 *
		 * @var object
		 */
		public $flag_as_imported = array();

		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 2.2.0
		 *
		 * @var object
		 */
		private static $instance;
		public         $demo_files_path;
		public         $theme_options_file_name;
		public         $widgets_file_name;
		public         $content_demo_file_name;
		public         $active_import;
		public         $active_import_id;
		public         $theme_option_name;

		/**
		 * Constructor. Hooks all interactions to initialize the class.
		 *
		 * @since 2.2.0
		 */
		public function __construct() {

			self::$instance = $this;

			$this->theme_options_file = $this->demo_files_path . $this->theme_options_file_name;
			$this->widgets            = $this->demo_files_path . $this->widgets_file_name;
			$this->content_demo       = $this->demo_files_path . $this->content_demo_file_name;

			add_filter( 'add_post_metadata', array(
				$this,
				'check_previous_meta',
			), 10, 5 );

			add_action( 'import_end', array(
				$this,
				'after_wp_importer',
			) );

			$this->process_imports();

		}

		/**
		 * Avoids adding duplicate meta causing arrays in arrays from WP_importer
		 *
		 * @param null $continue
		 * @param      $post_id
		 * @param      $meta_key
		 * @param      $meta_value
		 * @param      $unique
		 *
		 * @return
		 */
		public function check_previous_meta( $continue, $post_id, $meta_key, $meta_value, $unique ) {

			$old_value = get_metadata( 'post', $post_id, $meta_key );

			if ( count( $old_value ) == 1 ) {
				if ( $old_value[0] === $meta_value ) {
					return false;
				} elseif ( $old_value[0] !== $meta_value ) {
					update_post_meta( $post_id, $meta_key, $meta_value );

					return false;
				}
			}

			return null;
		}

		public function after_wp_importer() {

			$this->active_import[$this->active_import_id]['imported'] = 'imported';

			do_action( 'wbc_importer_after_content_import', $this->active_import, $this->demo_files_path );

			update_option( 'wbc_imported_demos', $this->active_import );
		}

		public function process_imports() {

			if ( ! empty( $this->content_demo ) && is_file( $this->content_demo ) ) {
				$this->set_demo_data( $this->content_demo );
			}

			if ( ! empty( $this->theme_options_file ) && is_file( $this->theme_options_file ) ) {
				$this->set_demo_theme_options( $this->theme_options_file );
			}

			if ( ! empty( $this->widgets ) && is_file( $this->widgets ) ) {
				$this->process_widget_import_file( $this->widgets );
			}

		}

		public function set_demo_data( $file ) {

			require_once dirname( __FILE__ ) . '/wordpress-importer.php';

			if ( ! is_file( $file ) ) {
				esc_html_e( 'Content data file containing the dummy content is not available or could not be read.', 'wowmall' );
			} else {
				$wowmall_import = new Wowmall_Importer();
				$wowmall_import->import( $file );
			}
		}

		function get_content( $filename ) {

			$content = file_get_contents($filename);

			return $content;

			/*global $wp_filesystem;

			if ( $wp_filesystem->exists( $filename ) ) {
				$text = $wp_filesystem->get_contents( $filename );
				if ( ! $text ) {
					return "";
				} else {
					return $text;
				}
			} else {
				return new WP_Error( "filesystem_error", "File doesn't exist" );
			}*/
		}

		public function set_demo_theme_options( $file ) {

			// File exists?
			if ( ! file_exists( $file ) ) {
				wp_die( esc_html__( 'Theme options Import file could not be found. Please try again.', 'wowmall' ), '', array( 'back_link' => true ) );
			}

			// Get file contents and decode
			$data = $this->get_content( $file );
			$data = json_decode( $data, true );
			$data = maybe_unserialize( $data );

			global $wowmall_base_site_url;

			if ( is_multisite() ) {
				$site_url = network_home_url();
			} // WordPress (single site): the blog URL.
			else {
				$site_url = get_bloginfo_rss( 'url' );
			}

			// Only if there is data
			if ( ! empty( $data ) || is_array( $data ) ) {

				foreach ( $data as $key => $option ) {
					if( is_array( $option ) && isset( $option['url'] ) && ! empty( $wowmall_base_site_url ) ) {
						$data[ $key ]['url'] = str_replace( $wowmall_base_site_url, $site_url, $option['url'] );
					}
					Redux::setOption( $this->theme_option_name, $key, $option );
				}

				//update_option( $this->theme_option_name, $data );
			}

			do_action( 'wbc_importer_after_theme_options_import', $this->active_import, $this->demo_files_path );

		}

		/**
		 * Available widgets
		 *
		 * Gather site's widgets into array with ID base, name, etc.
		 * Used by export and import functions.
		 *
		 * @since 2.2.0
		 *
		 * @global array $wp_registered_widget_updates
		 * @return array Widget information
		 */
		function available_widgets() {

			global $wp_registered_widget_controls;

			$widget_controls = $wp_registered_widget_controls;

			$available_widgets = array();

			foreach ( $widget_controls as $widget ) {

				if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[ $widget['id_base'] ] ) ) { // no dupes

					$available_widgets[ $widget['id_base'] ]['id_base'] = $widget['id_base'];
					$available_widgets[ $widget['id_base'] ]['name']    = $widget['name'];

				}

			}

			return $available_widgets;

		}

		/**
		 * Process import file
		 *
		 * This parses a file and triggers importation of its widgets.
		 *
		 * @since 2.2.0
		 *
		 * @param string  $file Path to .wie file uploaded
		 *
		 * @global string $widget_import_results
		 */
		function process_widget_import_file( $file ) {
			// File exists?
			if ( ! file_exists( $file ) ) {
				wp_die( esc_html__( 'Widget Import file could not be found. Please try again.', 'wowmall' ), '', array( 'back_link' => true ) );
			}

			// Get file contents and decode
			$data = $this->get_content( $file );

			$data = json_decode( $data );

			// Delete import file
			//unlink( $file );

			// Import the widget data
			// Make results available for display on import/export page
			$this->import_widgets( $data );

		}

		/**
		 * Import widget JSON data
		 *
		 * @since 2.2.0
		 * @global array $wp_registered_sidebars
		 *
		 * @param object $data JSON widget data from .wie file
		 */
		public function import_widgets( $data ) {

			global $wp_registered_sidebars;

			// Have valid data?
			// If no data or could not decode
			if ( empty( $data ) || ! is_object( $data ) ) {
				return;
			}

			// Hook before import
			$data = apply_filters( 'radium_theme_import_widget_data', $data );

			// Get all available widgets site supports
			$available_widgets = $this->available_widgets();

			// Get all existing widget instances
			$widget_instances = array();
			foreach ( $available_widgets as $widget_data ) {
				$widget_instances[ $widget_data['id_base'] ] = get_option( 'widget_' . $widget_data['id_base'] );
			}

			// Loop import data's sidebars
			foreach ( $data as $sidebar_id => $widgets ) {

				// Skip inactive widgets
				// (should not be in export file)
				if ( 'wp_inactive_widgets' == $sidebar_id ) {
					continue;
				}

				// Check if sidebar is available on this site
				// Otherwise add widgets to inactive, and say so
				if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
					$use_sidebar_id = $sidebar_id;
				} else {
					$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
				}

				// Loop widgets
				foreach ( $widgets as $widget_instance_id => $widget ) {

					$fail = false;

					// Get id_base (remove -# from end) and instance ID number
					$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );

					// Does site support this widget?
					if ( ! $fail && ! isset( $available_widgets[ $id_base ] ) ) {
						$fail = true;
					}

					// Filter to modify settings before import
					// Do before identical check because changes may make it identical to end result (such as URL replacements)
					$widget = apply_filters( 'radium_theme_import_widget_settings', $widget );

					// Does widget with identical settings already exist in same sidebar?
					if ( ! $fail && isset( $widget_instances[ $id_base ] ) ) {

						// Get existing widgets in this sidebar
						$sidebars_widgets = get_option( 'sidebars_widgets' );
						$sidebar_widgets  = isset( $sidebars_widgets[ $use_sidebar_id ] ) ? $sidebars_widgets[ $use_sidebar_id ] : array(); // check Inactive if that's where will go

						// Loop widgets with ID base
						$single_widget_instances = ! empty( $widget_instances[ $id_base ] ) ? $widget_instances[ $id_base ] : array();
						foreach ( $single_widget_instances as $check_id => $check_widget ) {

							// Is widget in same sidebar and has identical settings?
							if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

								$fail = true;

								break;

							}

						}

					}

					// No failure
					if ( ! $fail ) {

						// Add widget instance
						$single_widget_instances   = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
						$single_widget_instances   = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
						$single_widget_instances[] = (array) $widget; // add it

						// Get the key it was given
						end( $single_widget_instances );
						$new_instance_id_number = key( $single_widget_instances );

						// If key is 0, make it 1
						// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
						if ( '0' === strval( $new_instance_id_number ) ) {
							$new_instance_id_number                             = 1;
							$single_widget_instances[ $new_instance_id_number ] = $single_widget_instances[0];
							unset( $single_widget_instances[0] );
						}

						// Move _multiwidget to end of array for uniformity
						if ( isset( $single_widget_instances['_multiwidget'] ) ) {
							$multiwidget = $single_widget_instances['_multiwidget'];
							unset( $single_widget_instances['_multiwidget'] );
							$single_widget_instances['_multiwidget'] = $multiwidget;
						}

						// Update option with new widget
						update_option( 'widget_' . $id_base, $single_widget_instances );

						// Assign widget instance to sidebar
						$sidebars_widgets                      = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
						$new_instance_id                       = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
						$sidebars_widgets[ $use_sidebar_id ][] = $new_instance_id; // add new instance to sidebar
						update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

					}

				}

			}

			// Hook after import
			do_action( 'radium_theme_import_widget_after_import', $this->active_import, $this->demo_files_path );

		}

	}//class
}//function_exists
?>

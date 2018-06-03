<?php
/**
 * Extension-Boilerplate
 * @link        https://github.com/ReduxFramework/extension-boilerplate
 * Radium Importer - Modified For ReduxFramework
 * @link        https://github.com/FrankM1/radium-one-click-demo-install
 * @package     WBC_Importer - Extension for Importing demo content
 * @author      Webcreations907
 * @version     1.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_extension_wbc_importer' ) ) {

	class ReduxFramework_extension_wbc_importer {

		public static $instance;

		static $version = "1.0.2";

		protected $parent;

		private $filesystem = array();

		public $extension_url;

		public $extension_dir;

		public $demo_data_dir;

		public $wbc_import_files = array();

		public $active_import_id;

		public $active_import;

		/**
		 * Class Constructor
		 * @since       1.0
		 * @access      public
		 * @return      void
		 */
		public function __construct( $parent ) {

			$this->parent = $parent;

			if ( ! is_admin() ) {
				return;
			}

			//Hides importer section if anything but true returned. Way to abort :)
			if ( true !== apply_filters( 'wbc_importer_abort', true ) ) {
				return;
			}

			if ( empty( $this->extension_dir ) ) {
				$this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
				$this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
				$this->demo_data_dir = apply_filters( "wbc_importer_dir_path", $this->extension_dir . 'demo-data/' );
			}

			//Delete saved options of imported demos, for dev/testing purpose
			// delete_option('wbc_imported_demos');

			$this->getImports();

			$this->field_name = 'wbc_importer';

			self::$instance = $this;

			add_filter( 'redux/' . $this->parent->args['opt_name'] . '/field/class/' . $this->field_name, array(
				&$this,
				'overload_field_path',
			) );

			add_action( 'wp_ajax_redux_wbc_importer', array(
				$this,
				'ajax_importer',
			) );

			add_filter( 'redux/' . $this->parent->args['opt_name'] . '/field/wbc_importer_files', array(
				$this,
				'addImportFiles',
			) );

			//Adds Importer section to panel
			$this->add_importer_section();

			add_action( 'radium_theme_import_widget_after_import', array(
				$this,
				'additional_import',
			), 10, 2 );

			add_filter( 'avf_file_upload_capability', array(
				$this,
				'avf_file_upload_capability',
			) );

		}

		/**
		 * Get the demo folders/files
		 * Provided fallback where some host require FTP info
		 * @return array list of files for demos
		 */
		public function demoFiles() {

			$this->filesystem = $this->parent->filesystem->execute( 'object' );
			$dir_array        = $this->filesystem->dirlist( $this->demo_data_dir, false, true );

			if ( ! empty( $dir_array ) && is_array( $dir_array ) ) {

				uksort( $dir_array, 'strcasecmp' );

				return $dir_array;

			}
			else {

				$dir_array = array();

				$demo_directory = array_diff( scandir( $this->demo_data_dir ), array(
					'..',
					'.',
				) );

				if ( ! empty( $demo_directory ) && is_array( $demo_directory ) ) {
					foreach ( $demo_directory as $key => $value ) {
						if ( is_dir( $this->demo_data_dir . $value ) ) {

							$dir_array[ $value ] = array(
								'name'  => $value,
								'type'  => 'd',
								'files' => array(),
							);

							$demo_content = array_diff( scandir( $this->demo_data_dir . $value ), array(
								'..',
								'.',
							) );

							foreach ( $demo_content as $d_key => $d_value ) {
								if ( is_file( $this->demo_data_dir . $value . '/' . $d_value ) ) {
									$dir_array[ $value ]['files'][ $d_value ] = array(
										'name' => $d_value,
										'type' => 'f',
									);
								}
							}
						}
					}

					uksort( $dir_array, 'strcasecmp' );
				}
			}

			return $dir_array;
		}

		public function getImports() {

			if ( ! empty( $this->wbc_import_files ) ) {
				return $this->wbc_import_files;
			}

			$imports  = $this->demoFiles();
			$imported = get_option( 'wbc_imported_demos' );

			if ( ! empty( $imports ) && is_array( $imports ) ) {
				$x = 1;
				foreach ( $imports as $import ) {

					if ( ! isset( $import['files'] ) || empty( $import['files'] ) ) {
						continue;
					}
					if ( $import['type'] == "d" && ! empty( $import['name'] ) ) {
						$this->wbc_import_files[ 'wbc-import-' . $x ]              = isset( $this->wbc_import_files[ 'wbc-import-' . $x ] ) ? $this->wbc_import_files[ 'wbc-import-' . $x ] : array();
						$this->wbc_import_files[ 'wbc-import-' . $x ]['directory'] = $import['name'];

						if ( ! empty( $imported ) && is_array( $imported ) ) {
							if ( array_key_exists( 'wbc-import-' . $x, $imported ) ) {
								$this->wbc_import_files[ 'wbc-import-' . $x ]['imported'] = 'imported';
							}
						}
						foreach ( $import['files'] as $file ) {
							switch ( $file['name'] ) {
								case 'content.json':
									$this->wbc_import_files[ 'wbc-import-' . $x ]['content_file'] = $file['name'];
									break;

								case 'theme-options.txt':
								case 'theme-options.json':
									$this->wbc_import_files[ 'wbc-import-' . $x ]['theme_options'] = $file['name'];
									break;

								case 'widgets.json':
								case 'widgets.txt':
									$this->wbc_import_files[ 'wbc-import-' . $x ]['widgets'] = $file['name'];
									break;

								case 'screen-image.png':
								case 'screen-image.jpg':
								case 'screen-image.gif':
									$this->wbc_import_files[ 'wbc-import-' . $x ]['image'] = $file['name'];
									break;
							}

						}
						if ( ! isset( $this->wbc_import_files[ 'wbc-import-' . $x ]['content_file'] ) ) {
							unset( $this->wbc_import_files[ 'wbc-import-' . $x ] );
							if ( $x > 1 ) {
								$x--;
							}
						}
					}
					$x++;
				}
			}
		}

		public function addImportFiles( $wbc_import_files ) {

			if ( ! is_array( $wbc_import_files ) || empty( $wbc_import_files ) ) {
				$wbc_import_files = array();
			}

			$wbc_import_files = wp_parse_args( $wbc_import_files, $this->wbc_import_files );

			return $wbc_import_files;
		}

		public function ajax_importer() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], "redux_{$this->parent->args['opt_name']}_wbc_importer" ) ) {
				die( 0 );
			}
			if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == "import-demo-content" && array_key_exists( $_REQUEST['demo_import_id'], $this->wbc_import_files ) ) {

				wp_raise_memory_limit( 'admin' );

				$reimporting = false;

				if ( isset( $_REQUEST['wbc_import'] ) && $_REQUEST['wbc_import'] == 're-importing' ) {
					$reimporting = true;
				}

				$this->active_import_id = $_REQUEST['demo_import_id'];

				$import_parts = $this->wbc_import_files[ $this->active_import_id ];

				$this->active_import = array( $this->active_import_id => $import_parts );

				$content_file  = $import_parts['directory'];
				$demo_data_loc = $this->demo_data_dir . $content_file;

				if ( file_exists( $demo_data_loc . '/' . $import_parts['content_file'] ) && is_file( $demo_data_loc . '/' . $import_parts['content_file'] ) ) {

					if ( ! isset( $import_parts['imported'] ) || true === $reimporting ) {
						include $this->extension_dir . 'inc/init-installer.php';
						$installer = new Radium_Theme_Demo_Data_Importer( $this, $this->parent );
					}
					else {
						echo esc_html__( "Demo Already Imported", 'wowmall' );
					}
				}

				die();
			}

			die();
		}

		public static function get_instance() {
			return self::$instance;
		}

		// Forces the use of the embeded field path vs what the core typically would use
		public function overload_field_path() {
			return dirname( __FILE__ ) . '/' . $this->field_name . '/field_' . $this->field_name . '.php';
		}

		function add_importer_section() {
			// Checks to see if section was set in config of redux.
			for ( $n = 0; $n <= count( $this->parent->sections ); $n++ ) {
				if ( isset( $this->parent->sections[ $n ]['id'] ) && $this->parent->sections[ $n ]['id'] == 'wbc_importer_section' ) {
					return;
				}
			}

			$wbc_importer_label = trim( esc_html( apply_filters( 'wbc_importer_label', esc_html__( 'Demo Importer', 'wowmall' ) ) ) );

			$wbc_importer_label = ( ! empty( $wbc_importer_label ) ) ? $wbc_importer_label : esc_html__( 'Demo Importer', 'wowmall' );

			$this->parent->sections[] = array(
				'id'     => 'wbc_importer_section',
				'title'  => $wbc_importer_label,
				'desc'   => '<p class="description">' . apply_filters( 'wbc_importer_description', esc_html__( 'Works best to import on a new install of WordPress', 'wowmall' ) ) . '</p>',
				'icon'   => 'el-icon-website',
				'fields' => array(
					array(
						'id'   => 'wbc_demo_importer',
						'type' => 'wbc_importer',
					),
				),
			);
		}

		function additional_import( $demo_active_import, $demo_directory_path ) {
			reset( $demo_active_import );
			$current_key = key( $demo_active_import );

			$current_import = ! empty( $demo_active_import[ $current_key ]['directory'] ) ? $demo_active_import[ $current_key ]['directory'] : false;

			/************************************************************************
			 * Import slider(s) for the current demo being imported
			 *************************************************************************/
			if ( class_exists( 'RevSlider' ) ) {
				$wbc_sliders_array = array(
					'Home 1'            => 'slider-01.zip',
					'Home 2'            => 'slider-02.zip',
					'Home 3'            => 'slider-03.zip',
					'Home 4'            => 'slider-04.zip',
					'Home 7'            => 'slider-05.zip',
					'Home 8'            => 'slider-06.zip',
					'Electronics'       => 'slider-01.zip',
					'Fashion'           => 'slider-01.zip',
					'Lingerie'          => 'slider-01.zip',
					'Furniture'         => 'slider-01.zip',
					'Organic Cosmetics' => 'slider-01.zip',
					'Jewelry'           => 'slider-01.zip',
					'Glasses'           => 'slider-01.zip',
					'Tools'             => 'slider-01.zip',
					'Bikes'             => 'slider-01.zip',
					'Watches'           => 'slider-watches.zip',
					'Cars'              => 'slider-cars.zip',
					'Bathroom'          => 'slider-bathroom.zip',
					'Spices'            => 'slider-spices.zip',
					'Make Up'           => 'slider-make-up.zip',
					'Drone'             => 'slider-drone.zip',
					'Decor'             => 'slider-decor.zip',
				);
				if ( $current_import && array_key_exists( $current_import, $wbc_sliders_array ) ) {
					$wbc_slider_import = $wbc_sliders_array[ $current_import ];
					if ( file_exists( $demo_directory_path . $wbc_slider_import ) ) {
						$slider = new RevSlider();
						$slider->importSliderFromPost( true, true, $demo_directory_path . $wbc_slider_import );
						$options   = array(
							'includes_globally'   => 'off',
							'js_to_footer'        => 'on',
							'load_all_javascript' => 'on',
						);
						$arrValues = get_option( 'revslider-global-settings', '' );

						$options_exist = maybe_unserialize( $arrValues );
						if ( is_array( $options_exist ) ) {
							$options = array_merge( $options_exist, $options );
						}
						$strSettings = serialize( $options );
						update_option( 'revslider-global-settings', $strSettings );
					}
				}
			}
			/************************************************************************
			 * Setting Menus
			 *************************************************************************/
			$locations = array();
			$main_menu = get_term_by( 'name', 'Primary Menu', 'nav_menu' );
			if ( isset( $main_menu->term_id ) ) {
				$locations['primary'] = $main_menu->term_id;
			}
			$social_menu = get_term_by( 'name', 'Social Media Profiles', 'nav_menu' );
			if ( isset( $social_menu->term_id ) ) {
				$locations['social'] = $social_menu->term_id;
			}
			if ( ! empty( $locations ) ) {
				set_theme_mod( 'nav_menu_locations', $locations );
			}
			/************************************************************************
			 * Set HomePage
			 *************************************************************************/
			// array of demos/homepages to check/select from
			$wbc_home_pages = array(
				'Home 1'            => esc_html__( 'Home &#8212; Variant 1', 'wowmall' ),
				'Home 2'            => esc_html__( 'Home &#8212; Variant 2', 'wowmall' ),
				'Home 3'            => esc_html__( 'Home &#8212; Variant 3', 'wowmall' ),
				'Home 4'            => esc_html__( 'Home &#8212; Variant 4', 'wowmall' ),
				'Home 5'            => esc_html__( 'Home &#8212; Variant 5', 'wowmall' ),
				'Home 6'            => esc_html__( 'Home &#8212; Variant 6', 'wowmall' ),
				'Home 7'            => esc_html__( 'Home &#8212; Variant 7', 'wowmall' ),
				'Home 8'            => esc_html__( 'Home &#8212; Variant 8', 'wowmall' ),
				'Home Instagram'    => esc_html__( 'Home Instagram', 'wowmall' ),
				'Electronics'       => esc_html__( 'Home', 'wowmall' ),
				'Fashion'           => esc_html__( 'Home', 'wowmall' ),
				'Lingerie'          => esc_html__( 'Home', 'wowmall' ),
				'Furniture'         => esc_html__( 'Home', 'wowmall' ),
				'Organic Cosmetics' => esc_html__( 'Home', 'wowmall' ),
				'Jewelry'           => esc_html__( 'Home', 'wowmall' ),
				'Glasses'           => esc_html__( 'Home', 'wowmall' ),
				'Tools'             => esc_html__( 'Home', 'wowmall' ),
				'Bikes'             => esc_html__( 'Home', 'wowmall' ),
				'Watches'           => esc_html__( 'Home', 'wowmall' ),
				'Cars'              => esc_html__( 'Home', 'wowmall' ),
				'Bathroom'          => esc_html__( 'Home', 'wowmall' ),
				'Spices'            => esc_html__( 'Home', 'wowmall' ),
				'Make Up'           => esc_html__( 'Home', 'wowmall' ),
				'Drone'             => esc_html__( 'Home', 'wowmall' ),
				'Decor'             => esc_html__( 'Home', 'wowmall' ),
			);
			$wbc_pages      = array(
				'blog'      => 'Blog',
				'shop'      => 'Shop',
				'cart'      => 'Shopping Cart',
				'checkout'  => 'Checkout',
				'myaccount' => 'My Account',
			);
			if ( $current_import ) {
				if ( array_key_exists( $current_import, $wbc_home_pages ) ) {
					$page_title = html_entity_decode( $wbc_home_pages[ $current_import ] );
					$home_page  = get_page_by_title( $page_title );
					if ( isset( $home_page->ID ) ) {
						update_option( 'page_on_front', $home_page->ID );
						update_option( 'show_on_front', 'page' );
					}
					foreach ( $wbc_pages as $page => $title ) {
						$page_obj = get_page_by_title( $title );
						if ( isset( $page_obj->ID ) ) {
							if ( 'blog' === $page ) {
								update_option( 'page_for_posts', $page_obj->ID );
							}
							else {
								update_option( 'woocommerce_' . $page . '_page_id', $page_obj->ID );
							}
						}
					}
				}

				$wbc_options = array(
					'woocommerce_currency'                 => 'USD',
					//'woocommerce_price_num_decimals'       => '0',
					'woocommerce_shop_page_display'        => 'both',
					'woocommerce_category_archive_display' => 'both',
				);
				foreach ( $wbc_options as $option => $value ) {
					update_option( $option, $value );
				}
				$snapppt_settings = get_option( 'snapppt', array() );
				if ( ! isset( $snapppt_settings['account_id'] ) || empty( $snapppt_settings['account_id'] ) ) {
					$snapppt_settings['account_id'] = 'a2815624-f230-4274-afa2-81cdb5e698a8';
					update_option( 'snapppt', $snapppt_settings );
				}
			}
		}

		function avf_file_upload_capability( $cap ) {
			add_filter( 'get_attached_file', array(
				$this,
				'import_fonts_path',
			), 10, 2 );

			return $cap;
		}

		function import_fonts_path() {
			remove_filter( 'get_attached_file', array(
				$this,
				'import_fonts_path',
			), 10 );

			return $this->extension_dir . 'font/font.zip';
		}

	} // class
} // if
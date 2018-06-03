<?php

if( class_exists( 'AIO_Icon_Manager' ) ) {

	class Fontello_Importer extends AIO_Icon_Manager {

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
		 * @return Fontello_Importer - Main instance.
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
			parent::__construct();
			$remove = false;
			global $wp_filter;
			foreach ( $wp_filter['wp_ajax_smile_ajax_add_zipped_font'][10] as $key => $filter ) {
				if ( $filter['function'][0] instanceof AIO_Icon_Manager ) {
					unset( $wp_filter['wp_ajax_smile_ajax_add_zipped_font'][10] );
					$remove = true;
				}
			}
			if( $remove ) {
				add_action( 'wp_ajax_smile_ajax_add_zipped_font', array( $this, 'add_zipped_font' ) );
			}
		}

		//iterate over xml file and extract the glyphs for the font
		function create_config() {
			$this->json_file = $this->find_json();
			$this->svg_file  = $this->find_svg();
			if ( empty( $this->json_file ) || empty( $this->svg_file ) ) {
				$this->delete_folder( $this->paths['tempdir'] );
				die( __( 'selection.json or SVG file not found. Was not able to create the necessary config files', 'ultimate_vc' ) );
			}
			$response = wp_remote_fopen( trailingslashit( $this->paths['tempurl'] ) . $this->svg_file );
			//if wordpress wasnt able to get the file which is unlikely try to fetch it old school
			$json = file_get_contents( trailingslashit( $this->paths['tempdir'] ) . $this->json_file );
			if ( empty( $response ) ) {
				$response = file_get_contents( trailingslashit( $this->paths['tempdir'] ) . $this->svg_file );
			}
			if ( ! is_wp_error( $json ) && ! empty( $json ) ) {
				$xml             = simplexml_load_string( $response );
				$font_attr       = $xml->defs->font->attributes();
				$glyphs          = $xml->defs->font->children();
				$this->font_name = (string) $font_attr['id'];
				$unicodes        = array();
				foreach ( $glyphs as $item => $glyph ) {
					if ( $item == 'glyph' ) {
						$attributes = $glyph->attributes();
						$unicode    = (string) $attributes['unicode'];
						array_push( $unicodes, $unicode );
					}
				}
				$font_folder = trailingslashit( $this->paths['fontdir'] ) . $this->font_name;
				if ( is_dir( $font_folder ) ) {
					$this->delete_folder( $this->paths['tempdir'] );
					die( __( "It seems that the font with the same name is already exists! Please upload the font with different name.", "ultimate_vc" ) );
				}
				$file_contents = json_decode( $json );
				if ( ! isset( $file_contents->IcoMoonType ) && ! isset( $file_contents->glyphs ) ) {
					$this->delete_folder( $this->paths['tempdir'] );
					die( __( 'Uploaded font is not from IcoMoon or Fontello. Please upload fonts created with the IcoMoon or Fontello App Only.', 'ultimate_vc' ) );
				}
				if ( ! isset( $file_contents->IcoMoonType ) ) {
					$icons = $file_contents->glyphs;
					$n = 0;
					foreach ( $icons as $icon ) {
						$icon_name                                           = $icon->css;
						$icon_class                                          = $icon_name;
						$this->json_config[ $this->font_name ][ $icon_name ] = array(
							"class"   => $icon_class,
							"tags"    => $icon_name,
							"unicode" => $unicodes[ $n ]
						);
						$n ++;
					}
					if ( ! empty( $this->json_config ) && $this->font_name != 'unknown' ) {
						$this->write_config();
						$this->re_write_fontello_css();
						$this->rename_fontello_files();
						$this->rename_folder();
						$this->add_font();
					}
				}
				if ( ! isset( $file_contents->glyphs ) ) {
					$icons = $file_contents->icons;
					unset( $unicodes[0] );
					$n = 1;
					foreach ( $icons as $icon ) {
						$icon_name                                           = $icon->properties->name;
						$icon_class                                          = str_replace( ' ', '', $icon_name );
						$icon_class                                          = str_replace( ',', ' ', $icon_class );
						$tags                                                = implode( ",", $icon->icon->tags );
						$this->json_config[ $this->font_name ][ $icon_name ] = array(
							"class"   => $icon_class,
							"tags"    => $tags,
							"unicode" => $unicodes[ $n ]
						);
						$n ++;
					}
					if ( ! empty( $this->json_config ) && $this->font_name != 'unknown' ) {
						$this->write_config();
						$this->re_write_css();
						$this->rename_files();
						$this->rename_folder();
						$this->add_font();
					}
				}
			}

			return false;
		}

		//re-writes the php config file for the font
		function re_write_fontello_css() {
			$style = $this->paths['tempdir'] . '/' . $this->font_name . '.css';
			$file  = @file_get_contents( $style );
			if ( $file ) {
				$str = str_replace( '../font/', '', $file );
				$str = str_replace( 'icon-', $this->font_name . '-', $str );


				$str = str_replace( array( "!/\*[^*]*\*+([^/][^*]*\*+)*/!", "\r\n", "\r", "\n", "\t", '  ', '    ', '    ', ' ' ), '', $str );

				$str = str_replace( array( ';}' ), array( '}' ), $str );

				@file_put_contents( $style, $str );
			} else {
				die( __( 'Unable to write css. Upload icons downloaded only from icomoon', 'ultimate_vc' ) );
			}
		}

		function rename_fontello_files() {
			$extensions = array( 'eot', 'svg', 'ttf', 'woff', 'css' );
			$folder     = trailingslashit( $this->paths['tempdir'] );
			foreach ( glob( $folder . '*' ) as $file ) {
				$path_parts = pathinfo( $file );
				if ( strpos( $path_parts['filename'], '.dev' ) === false && in_array( $path_parts['extension'], $extensions ) ) {
					if ( $path_parts['filename'] !== $this->font_name ) {
						if ( 'css' === $path_parts['extension'] ) {
							unlink( $file );
						} else {
							rename( $file, trailingslashit( $path_parts['dirname'] ) . $this->font_name . '.' . $path_parts['extension'] );
						}
					}
				}
			}
		}
	}
	function wowmall_fontello_importer() {
		Fontello_Importer::instance();
	}
	wowmall_fontello_importer();
}
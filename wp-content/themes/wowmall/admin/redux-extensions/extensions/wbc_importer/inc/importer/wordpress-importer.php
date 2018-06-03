<?php
/*
Plugin Name: WordPress Importer
Plugin URI: http://wordpress.org/extend/plugins/wordpress-importer/
Description: Import posts, pages, comments, custom fields, categories, tags and more from a WordPress export file.
Author: wordpressdotorg
Author URI: http://wordpress.org/
Version: 0.6.3 - modified
Text Domain: wowmall
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
	return;
}

/** Display verbose errors */
define( 'IMPORT_DEBUG', false );

class Wowmall_Importer {

	var $id; // WXR attachment ID
	var $posts                = array();
	var $media                = array();
	var $media_metadata       = array();
	var $terms                = array();
	var $categories           = array();
	var $tags                 = array();
	var $menus                = array();
	var $processed_terms      = array();
	var $existed_menus        = array();
	var $processed_posts      = array();
	var $processed_media      = array();
	var $processed_menu_items = array();
	var $megamenu_pages       = array();
	var $base_site_url;
	var $menu_pattern;

	/**
	 * The main controller for the actual import stage.
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	function import( $file ) {

		set_time_limit( 0 );
		error_reporting( 0 );

		try {

			$this->menu_pattern = get_shortcode_regex( array( 'vc_wp_custommenu' ) );
			add_filter( 'http_request_timeout', array(
				&$this,
				'bump_request_timeout',
			) );

			$this->import_start( $file );

			wp_suspend_cache_invalidation( true );

			if ( ! isset( $_SESSION['wowmall_import_posts_part'] ) && ! isset( $_SESSION['wowmall_import_posts_type'] ) ) {

				if ( ! isset( $_SESSION['wowmall_import_media_metadata_part'] ) ) {

					$this->process_media();
				}

				$this->process_media_metadata();
				$this->process_categories();
				$this->process_tags();
				$this->process_terms();
			}

			$this->process_posts();

			wp_suspend_cache_invalidation( false );

			$this->import_end();

		}
		catch ( Exception $e ) {
			die( json_encode( array( 'error' => '<b><span style="color: #DD3D36;">' . $e->getMessage() . '</span></b>' ) ) );
		}
	}

	/**
	 * Parses the WXR file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	function import_start( $file ) {
		if ( ! is_file( $file ) ) {
			echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'wowmall' ) . '</strong><br />';
			echo esc_html__( 'The file does not exist, please try again.', 'wowmall' ) . '</p>';
			die();
		}

		$import_data = $this->parse( $file );

		if ( is_wp_error( $import_data ) ) {
			echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'wowmall' ) . '</strong><br />';
			echo esc_html( $import_data->get_error_message() ) . '</p>';
			die();
		}
		$this->base_site_url              = $import_data['base_site_url'];
		$this->posts                      = $import_data['posts'];
		$GLOBALS['wowmall_base_site_url'] = $this->base_site_url;
		if ( ! isset( $_SESSION['wowmall_import_posts_part'] ) && ! isset( $_SESSION['wowmall_import_posts_type'] ) ) {
			$this->media      = $import_data['media'];
			$this->terms      = $import_data['terms'];
			$this->categories = $import_data['categories'];
			$this->tags       = $import_data['tags'];

			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );
		}
		if ( ! session_id() ) {
			session_start();
		}
		do_action( 'import_start' );
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	function import_end() {
		wp_import_cleanup( $this->id );

		wp_cache_flush();

		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}
		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		echo 'Have fun!';

		do_action( 'import_end' );
	}

	/**
	 * Create new categories based on import information
	 * Doesn't create a new category if its slug already exists
	 */
	function process_categories() {

		if ( empty( $this->categories ) ) {
			return;
		}
		foreach ( $this->categories as $cat_id => $cat ) {
			$slug = isset( $cat['s'] ) ? $cat['s'] : sanitize_title( $cat['n'] );
			// if the category already exists leave it alone
			$term_id = term_exists( $slug, 'category' );
			if ( $term_id ) {
				if ( is_array( $term_id ) ) {
					$term_id = $term_id['term_id'];
				}
				$this->processed_terms[intval( $cat_id )] = (int) $term_id;
				continue;
			}
			$catarr = array(
				'cat_name' => $cat['n'],
			);
			if ( isset( $cat['p'] ) ) {
				$catarr['category_parent'] = category_exists( $cat['p'] );
			}
			if ( isset( $cat['s'] ) ) {
				$catarr['category_nicename'] = $cat['s'];
			}
			if ( isset( $cat['d'] ) ) {
				$catarr['category_description'] = $cat['d'];
			}
			$catarr = wp_slash( $catarr );
			$id     = wp_insert_category( $catarr );

			if ( ! is_wp_error( $id ) ) {
				$this->processed_terms[intval( $cat_id )] = $id;
				$this->process_termmeta( $cat, $id['term_id'] );
			}
			else {
				if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
					printf( esc_html__( 'Failed to import category %s', 'wowmall' ), esc_html( $cat['s'] ) );
					echo ': ' . $id->get_error_message() . "\n";
				}
				continue;
			}
		}
		unset( $this->categories );
	}

	/**
	 * Create new post tags based on import information
	 * Doesn't create a tag if its slug already exists
	 */
	function process_tags() {

		if ( empty( $this->tags ) ) {
			return;
		}
		foreach ( $this->tags as $tag_id => $tag ) {
			$slug = isset( $tag['s'] ) ? $tag['s'] : sanitize_title( $tag['n'] );
			// if the tag already exists leave it alone
			$term_id = term_exists( $slug, 'post_tag' );
			if ( $term_id ) {
				if ( is_array( $term_id ) ) {
					$term_id = $term_id['term_id'];
				}
				$this->processed_terms[intval( $tag_id )] = (int) $term_id;
				continue;
			}
			$tag    = wp_slash( $tag );
			$tagarr = array();
			if ( isset( $tag['s'] ) ) {
				$tagarr['slug'] = $tag['s'];
			}
			if ( isset( $tag['d'] ) ) {
				$tagarr['description'] = $tag['d'];
			}
			$id = wp_insert_term( $tag['n'], 'post_tag', $tagarr );

			if ( ! is_wp_error( $id ) ) {
				$this->processed_terms[intval( $tag_id )] = $id['term_id'];
				$this->process_termmeta( $tag, $id['term_id'] );
			}
			else {
				if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
					printf( esc_html__( 'Failed to import post tag %s', 'wowmall' ), esc_html( $tag['n'] ) );
					echo ': ' . $id->get_error_message() . "\n";
				}
				continue;
			}
		}
		unset( $this->tags );
	}

	/**
	 * Create new terms based on import information
	 * Doesn't create a term its slug already exists
	 */
	function process_terms() {

		if ( empty( $this->terms ) ) {
			return;
		}
		foreach ( $this->terms as $t_id => $term ) {
			$slug = isset( $term['s'] ) ? $term['s'] : sanitize_title( $term['n'] );
			// if the term already exists in the correct taxonomy leave it alone
			$term_id = get_term_by( 'slug', $slug, $term['t'], ARRAY_A );
			if ( $term_id ) {
				if ( is_array( $term_id ) ) {
					$term_id = $term_id['term_id'];
				}
				if ( 'nav_menu' === $term['t'] ) {
					$this->existed_menus[] = $term_id;
					$this->menus[$slug]    = $term_id;
				}
				$this->processed_terms[intval( $t_id )] = (int) $term_id;
				continue;
			}
			$term    = wp_slash( $term );
			$termarr = array();
			if ( isset( $term['s'] ) ) {
				$termarr['slug'] = $term['s'];
			}
			if ( isset( $term['d'] ) ) {
				$termarr['description'] = $term['d'];
			}
			if ( isset( $term['p'] ) ) {
				$parent = term_exists( $term['p'], $term['t'] );
				if ( is_array( $parent ) ) {
					$parent = $parent['term_id'];
				}
				$termarr['parent'] = intval( $parent );
			}
			$id = wp_insert_term( $term['n'], $term['t'], $termarr );

			if ( ! is_wp_error( $id ) ) {
				if ( 'nav_menu' === $term['t'] ) {
					$this->menus[$slug] = $id['term_id'];
				}
				$this->processed_terms[intval( $t_id )] = $id['term_id'];
				$this->process_termmeta( $term, $id['term_id'] );
			}
			else {
				if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
					printf( esc_html__( 'Failed to import %s %s', 'wowmall' ), esc_html( $term['t'] ), esc_html( $term['n'] ) );
					echo ': ' . $id->get_error_message() . "\n";
				}
				continue;
			}
		}
		unset( $this->terms );
	}

	/**
	 * Add metadata to imported term.
	 * @since 0.6.2
	 *
	 * @param array $term    Term data from WXR import.
	 * @param int   $term_id ID of the newly created term.
	 */
	protected function process_termmeta( $term, $term_id ) {
		if ( empty( $term['m'] ) || ! is_array( $term['m'] ) ) {
			return;
		}
		foreach ( $term['m'] as $key => $meta ) {
			$meta = array_filter( $meta );
			if ( ! empty( $meta ) ) {
				foreach ( $meta as $value ) {
					// Export gets meta straight from the DB so could have a serialized string
					$value = maybe_unserialize( $value );
					if ( 'thumbnail_id' === $key && ! empty( $value ) ) {
						if ( isset( $this->processed_media[$value] ) ) {
							$value = $this->processed_media[$value];
						}
					}
					add_term_meta( $term_id, $key, $value );
				}
			}
		}
	}

	function process_media() {

		if ( empty( $this->media ) ) {
			return;
		}
		$all_media = array_chunk( $this->media, 1, true );

		$p_type = 'attachment';
		if ( isset( $_SESSION['wowmall_import_media_part'] ) ) {
			$this->processed_media = $_SESSION['wowmall_import_processed_media'];
			$this->media_metadata  = $_SESSION['wowmall_import_media_metadata'];
			$all_media_key         = (int) $_SESSION['wowmall_import_media_part'];
			++$all_media_key;
		}
		else {
			$all_media_key = 0;
			$count         = 0;
			foreach ( $this->posts as $type ) {
				$count += count( $type );
			}
			$count = floor( $count / 1 );
			$count += count( $all_media ) * 2;
			echo '{{wowmall_all_progress=' . $count . '}}';
		}
		$posts = $all_media[$all_media_key];

		foreach ( $posts as $p_id => $post ) {
			$title = isset( $post['title'] ) ? $post['title'] : '';

			$post_exists = post_exists( $title, '', $post['date'] );
			$post_type   = get_post_type( $post_exists );

			if ( $post_exists && ( $post_type === $p_type ) ) {
				$post_id = $post_exists;
			}
			else {
				$postdata = array(
					'import_id'      => $p_id,
					'post_date_gmt'  => $post['d'],
					'post_name'      => $post['n'],
					'comment_status' => $post['comment_status'],
					'ping_status'    => $post['ping_status'],
					'post_type'      => $p_type,
				);
				if ( isset( $post['title'] ) ) {
					$postdata['post_title'] = $post['title'];
				}
				if ( isset( $post['content'] ) ) {
					$postdata['post_content'] = $post['content'];
				}
				if ( isset( $post['e'] ) ) {
					$postdata['post_excerpt'] = $post['e'];
				}
				if ( isset( $post['status'] ) ) {
					$postdata['post_status'] = $post['status'];
				}
				$postdata = wp_slash( $postdata );
				$post_id  = $this->process_attachment( $postdata, $post );

				if ( is_wp_error( $post_id ) ) {
					if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
						$post_type_object = get_post_type_object( $p_type );
						printf( esc_html__( 'Failed to import %s &#8220;%s&#8221;', 'wowmall' ), $post_type_object->labels->singular_name, esc_html( $post['title'] ) );
						echo ': ' . $post_id->get_error_message() . "\n";
					}
					continue;
				}
			}
			if ( $p_id !== $post_id ) {
				$this->processed_media[$p_id] = $post_id;
			}
			// add/update post meta
			if ( isset( $post['m'] ) ) {
				foreach ( $post['m'] as $meta_key => $meta ) {
					$meta = array_filter( $meta );

					if ( ! empty( $meta ) ) {
						foreach ( $meta as $meta_value ) {
							if ( '_wp_attachment_metadata' === $meta_key ) {
								continue;
							}
							// export gets meta straight from the DB so could have a serialized string
							$meta_value = maybe_unserialize( $meta_value );
							add_post_meta( $post_id, $meta_key, $meta_value );
						}
					}
				}
			}
		}
		$_SESSION['wowmall_import_processed_media'] = $this->processed_media;
		if ( $all_media_key < ( count( $all_media ) - 1 ) ) {
			$_SESSION['wowmall_import_media_metadata'] = $this->media_metadata;
			$_SESSION['wowmall_import_media_part']     = $all_media_key;
			echo 'wowmall_import_posts_part';
			wp_die();
		}
		unset( $_SESSION['wowmall_import_media_part'], $_SESSION['wowmall_import_media_metadata'], $this->media, $all_media_key, $all_media, $posts );
	}

	function process_media_metadata() {

		if ( isset( $_SESSION['wowmall_import_media_metadata'] ) ) {
			$this->media_metadata = $_SESSION['wowmall_import_media_metadata'];
		}

		if ( empty( $this->media_metadata ) ) {
			return;
		}
		$all_media_metadata = array_chunk( $this->media_metadata, 1, true );

		if ( isset( $_SESSION['wowmall_import_media_metadata_part'] ) ) {
			$this->media_metadata   = $_SESSION['wowmall_import_media_metadata'];
			$all_media_metadata_key = (int) $_SESSION['wowmall_import_media_metadata_part'];
			++$all_media_metadata_key;
		}
		else {
			$all_media_metadata_key = 0;
		}
		$posts = $all_media_metadata[$all_media_metadata_key];

		foreach ( $posts as $media_id => $media_file ) {
			$metadata = wp_generate_attachment_metadata( $media_id, $media_file );
			if ( is_wp_error( $metadata ) ) {
				throw new Exception( $metadata->get_error_message() );
			}
			if ( empty( $metadata ) ) {
				throw new Exception( __( 'Unknown failure reason.', 'wowmall' ) );
			}
			wp_update_attachment_metadata( $media_id, $metadata );
		}
		if ( $all_media_metadata_key < ( count( $all_media_metadata ) - 1 ) ) {
			$_SESSION['wowmall_import_media_metadata']      = $this->media_metadata;
			$_SESSION['wowmall_import_media_metadata_part'] = $all_media_metadata_key;
			echo 'wowmall_import_posts_part';
			wp_die();
		}
		unset( $_SESSION['wowmall_import_media_metadata_part'], $_SESSION['wowmall_import_media_metadata'], $this->media_metadata, $all_media_metadata_key, $all_media_metadata, $posts );
	}

	/**
	 * Create new posts based on import information
	 * Posts marked as having a parent which doesn't exist will become top level items.
	 * Doesn't create a new post if: the post type doesn't exist, the given post ID
	 * is already noted as imported or a post with the same title and date already exists.
	 * Note that new/updated terms, comments and meta are imported for the last of the above.
	 */
	function process_posts() {

		if ( empty( $this->posts ) ) {
			return;
		}
		if ( isset( $_SESSION['wowmall_import_posts_type'] ) ) {
			$this->processed_posts      = $_SESSION['wowmall_import_processed_posts'];
			$this->processed_media      = $_SESSION['wowmall_import_processed_media'];
			$this->processed_terms      = $_SESSION['wowmall_import_processed_terms'];
			$this->menus                = $_SESSION['wowmall_import_menus'];
			$this->existed_menus        = $_SESSION['wowmall_import_existed_menus'];
			$this->processed_menu_items = $_SESSION['wowmall_import_processed_menu_items'];
			$this->megamenu_pages       = $_SESSION['wowmall_import_megamenu_pages'];

			while ( key( $this->posts ) !== $_SESSION['wowmall_import_posts_type'] ) {

				next( $this->posts );
			}
			$posts = next( $this->posts );

			if ( ! $posts ) {
				unset( $_SESSION['wowmall_import_posts_type'], $_SESSION['wowmall_import_processed_posts'], $_SESSION['wowmall_import_processed_media'], $_SESSION['wowmall_import_missed_menu_items'], $_SESSION['wowmall_import_menus'], $_SESSION['wowmall_import_processed_terms'], $_SESSION['wowmall_import_existed_menus'], $_SESSION['wowmall_import_processed_menu_items'], $_SESSION['wowmall_import_megamenu_pages'], $this->posts, $this->existed_menus );

				return;
			}
			$p_type = key( $this->posts );

		}
		else {
			$posts  = current( $this->posts );
			$p_type = key( $this->posts );
		}
		if ( isset( $_SESSION['wowmall_import_posts_part'] ) ) {
			$this->processed_posts      = $_SESSION['wowmall_import_processed_posts'];
			$this->processed_media      = $_SESSION['wowmall_import_processed_media'];
			$this->processed_terms      = $_SESSION['wowmall_import_processed_terms'];
			$this->menus                = $_SESSION['wowmall_import_menus'];
			$this->existed_menus        = $_SESSION['wowmall_import_existed_menus'];
			$this->processed_menu_items = $_SESSION['wowmall_import_processed_menu_items'];
			$this->megamenu_pages       = $_SESSION['wowmall_import_megamenu_pages'];
			$all_posts_key              = (int) $_SESSION['wowmall_import_posts_part'];
			++$all_posts_key;
		}
		else {
			$all_posts_key = 0;
		}
		$all_posts = array_chunk( $posts, 1, true );
		$posts     = $all_posts[$all_posts_key];

		foreach ( $posts as $p_id => $post ) {
			$title = isset( $post['title'] ) ? $post['title'] : '';
			if ( ! post_type_exists( $p_type ) ) {
				printf( esc_html__( 'Failed to import &#8220;%s&#8221;: Invalid post type %s', 'wowmall' ), esc_html( $post['title'] ), esc_html( $p_type ) );
				echo "\n";
				continue;
			}
			if ( 'product_variation' === $p_type ) {
				continue;
			}
			if ( 'nav_menu_item' === $p_type ) {
				$this->process_menu_item( $post, $p_id );
				continue;
			}
			$post_exists = post_exists( $title, '', $post['date'] );
			$post_type   = get_post_type( $post_exists );

			if ( $post_exists && ( $post_type === $p_type ) ) {
				$post_id = $post_exists;
			}
			else {
				if ( isset( $post['parent'] ) ) {
					$post_parent = (int) $post['parent'];
					// if we already know the parent, map it to the new local ID
					if ( isset( $this->processed_posts[$post_parent] ) ) {
						$post_parent = $this->processed_posts[$post_parent];
						// otherwise record the parent for later
					}
					else {
						$post_parent = 0;
					}
				}
				$postdata = array(
					'import_id'      => $p_id,
					'post_date_gmt'  => $post['d'],
					'post_name'      => $post['n'],
					'comment_status' => $post['comment_status'],
					///
					'ping_status'    => $post['ping_status'],
					'post_type'      => $p_type,
				);
				if ( isset( $post['title'] ) ) {
					$postdata['post_title'] = $post['title'];
				}
				if ( isset( $post_parent ) ) {
					$postdata['post_parent'] = $post_parent;
				}
				if ( isset( $post['content'] ) ) {
					if ( in_array( $p_type, array(
						'post',
						'page',
					) ) ) {
						$post['content'] = str_replace( $this->base_site_url, site_url(), $post['content'] );
					}
					if ( 'page' === $p_type ) {
						preg_match_all( '/' . $this->menu_pattern . '/s', $post['content'], $matches );
						if ( ! empty( $matches[0] ) ) {
							$new_shortcodes = array();
							foreach ( $matches[0] as $key => $menu_shortcode ) {
								$atts    = shortcode_parse_atts( $menu_shortcode );
								$menu_id = 0;
								if ( array_key_exists( 'nav_menu', $atts ) ) {
									$menu_id = $atts['nav_menu'];
								}
								else {
									foreach ( $atts as $attr ) {
										if ( false !== strpos( $attr, 'nav_menu' ) ) {
											$menu_id = filter_var( $attr, FILTER_SANITIZE_NUMBER_INT );
										}
									}
								}
								if ( ! in_array( $menu_id, $this->existed_menus ) && array_key_exists( $menu_id, $this->processed_terms ) ) {
									$new_id           = $this->processed_terms[$menu_id];
									$new_shortcodes[] = str_replace( $menu_id, $new_id, $menu_shortcode );
								}
								else {
									unset( $matches[0][$key] );
								}
							}
							$post['content'] = str_replace( $matches[0], $new_shortcodes, $post['content'] );
						}
					}
					$postdata['post_content'] = $post['content'];
				}
				if ( isset( $post['e'] ) ) {
					if ( in_array( $p_type, array(
						'post',
						'page',
					) ) ) {
						$post['e'] = str_replace( $this->base_site_url, site_url(), $post['e'] );
					}
					$postdata['post_excerpt'] = $post['e'];
				}
				if ( isset( $post['status'] ) ) {
					$postdata['post_status'] = $post['status'];
				}
				if ( isset( $post['o'] ) ) {
					$postdata['menu_order'] = $post['o'];
				}
				if ( isset( $post['password'] ) ) {
					$postdata['post_password'] = $post['password'];
				}
				$postdata = wp_slash( $postdata );
				$post_id  = wp_insert_post( $postdata, true );
				if ( is_wp_error( $post_id ) ) {
					if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
						$post_type_object = get_post_type_object( $p_type );
						printf( esc_html__( 'Failed to import %s &#8220;%s&#8221;', 'wowmall' ), $post_type_object->labels->singular_name, esc_html( $post['title'] ) );
						echo ': ' . $post_id->get_error_message() . "\n";
					}
					continue;
				}
				if ( isset( $post['is_sticky'] ) && $post['is_sticky'] == 1 ) {
					stick_post( $post_id );
				}
			}
			if ( 'mc4wp-form' === $p_type ) {
				$default_form_id = (int) get_option( 'mc4wp_default_form_id', 0 );

				if ( empty( $default_form_id ) ) {
					update_option( 'mc4wp_default_form_id', $post_id );
				}
			}
			// map pre-import ID to local ID
			$this->processed_posts[$p_id] = $post_id;
			// add categories, tags and other terms
			if ( ! empty( $post['t'] ) ) {
				$terms_to_set = array();
				foreach ( $post['t'] as $taxonomy => $terms ) {
					foreach ( $terms as $slug => $name ) {
						if ( is_int( $slug ) ) {
							$slug = sanitize_title( $name );
						}
						$term_exists = term_exists( $slug, $taxonomy );
						$term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;

						if ( ! $term_id ) {
							$t = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );

							if ( ! is_wp_error( $t ) ) {
								$term_id = $t['term_id'];
							}
							else {
								if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
									printf( esc_html__( 'Failed to import %s %s', 'wowmall' ), esc_html( $taxonomy ), esc_html( $name ) );
									echo ': ' . $t->get_error_message() . "\n";
								}
								continue;
							}
						}
						$terms_to_set[$taxonomy][] = intval( $term_id );
					}
				}
				foreach ( $terms_to_set as $tax => $ids ) {
					wp_set_post_terms( $post_id, $ids, $tax );
				}
				unset( $post['t'], $terms_to_set );
			}
			if ( ! isset( $post['comments'] ) ) {
				$post['comments'] = array();
			}
			// add/update comments
			if ( ! empty( $post['comments'] ) ) {
				$inserted_comments = array();
				$newcomments       = array();
				foreach ( $post['comments'] as $comment_id => $comment ) {

					$newcomments[$comment_id] = array(
						'comment_post_ID' => $post_id,
						'comment_author'  => $comment['a'],
						'comment_date'    => $comment['d'],
						'comment_content' => $comment['c'],
					);
					if ( isset( $comment['t'] ) ) {
						$newcomments[$comment_id]['comment_type'] = $comment['t'];
					}
					if ( isset( $comment['m'] ) ) {
						$newcomments[$comment_id]['commentmeta'] = $comment['m'];
					}
					if ( isset( $comment['p'] ) ) {
						$newcomments[$comment_id]['comment_parent'] = $comment['p'];
					}
				}
				ksort( $newcomments );

				foreach ( $newcomments as $key => $comment ) {
					// if this is a new post we can skip the comment_exists() check
					if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
						if ( isset( $comment['comment_parent'] ) && isset( $inserted_comments[$comment['comment_parent']] ) ) {
							$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
						}
						$comment                 = wp_filter_comment( $comment );
						$inserted_comments[$key] = wp_insert_comment( $comment );

						foreach ( $comment['commentmeta'] as $meta_key => $meta ) {
							foreach ( $meta as $meta_value ) {
								$meta_value = maybe_unserialize( $meta_value );
								add_comment_meta( $inserted_comments[$key], $meta_key, $meta_value );
							}
						}
					}
				}
				unset( $newcomments, $inserted_comments, $post['comments'] );
			}
			// add/update post meta
			if ( isset( $post['m'] ) ) {
				foreach ( $post['m'] as $meta_key => $meta ) {
					$meta = array_filter( $meta );
					if ( ! empty( $meta ) ) {
						foreach ( $meta as $meta_value ) {
							// export gets meta straight from the DB so could have a serialized string
							$meta_value = maybe_unserialize( $meta_value );
							// if the post has a featured image, take note of this in case of remap
							if ( '_product_image_gallery' === $meta_key ) {
								$gallery = explode( ',', $meta_value );
								if ( ! empty( $gallery ) ) {
									foreach ( $gallery as $key => $image ) {
										if ( isset( $this->processed_media[$image] ) ) {
											$gallery[$key] = $this->processed_media[$image];
										}
									}
									$meta_value = join( ',', $gallery );
								}
							}
							elseif ( '_thumbnail_id' === $meta_key ) {
								if ( isset( $this->processed_media[$meta_value] ) ) {
									$meta_value = $this->processed_media[$meta_value];
								}
							}
							add_post_meta( $post_id, $meta_key, $meta_value );
						}
					}
				}
			}
		}

		$_SESSION['wowmall_import_processed_posts']      = $this->processed_posts;
		$_SESSION['wowmall_import_processed_media']      = $this->processed_media;
		$_SESSION['wowmall_import_menus']                = $this->menus;
		$_SESSION['wowmall_import_processed_terms']      = $this->processed_terms;
		$_SESSION['wowmall_import_existed_menus']        = $this->existed_menus;
		$_SESSION['wowmall_import_processed_menu_items'] = $this->processed_menu_items;
		$_SESSION['wowmall_import_megamenu_pages']       = $this->megamenu_pages;
		if ( $all_posts_key < ( count( $all_posts ) - 1 ) ) {
			$_SESSION['wowmall_import_posts_part'] = $all_posts_key;
			echo 'wowmall_import_posts_part';
			wp_die();
		}
		unset( $_SESSION['wowmall_import_posts_part'], $this->posts, $all_posts_key, $all_posts, $posts );
		$_SESSION['wowmall_import_posts_type'] = $p_type;
		echo 'wowmall_import_posts_part';
		wp_die();
	}

	/**
	 * Attempt to create a new menu item from import data
	 * Fails for draft, orphaned menu items and those without an associated nav_menu
	 * or an invalid nav_menu term. If the post type or term object which the menu item
	 * represents doesn't exist then the menu item will not be imported (waits until the
	 * end of the import to retry again before discarding).
	 *
	 * @param array $item Menu item details from WXR file
	 */
	function process_menu_item( $item, $item_id ) {
		$menu_slug = false;

		if ( isset( $item['t'] ) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['t'] as $taxonomy => $term ) {
				if ( 'nav_menu' === $taxonomy ) {
					foreach ( $term as $slug => $name ) {
						$menu_slug = $slug;
						if ( is_int( $slug ) ) {
							$menu_slug = sanitize_title( $name );
						}
						break;
					}
					break;
				}
			}
		}
		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			esc_html_e( 'Menu item skipped due to missing menu slug', 'wowmall' );
			echo "\n";

			return;
		}
		if ( ! array_key_exists( $menu_slug, $this->menus ) ) {
			printf( esc_html__( 'Menu item skipped due to invalid menu slug: %s', 'wowmall' ), esc_html( $menu_slug ) );
			echo "\n";

			return;
		}
		else {
			$menu_id = $this->menus[$menu_slug];
		}
		foreach ( $item['m'] as $meta_key => $meta ) {
			${$meta_key} = $meta[0];
		}
		if ( 'taxonomy' === $_menu_item_type && isset( $this->processed_terms[intval( $_menu_item_object_id )] ) ) {
			$_menu_item_object_id = $this->processed_terms[intval( $_menu_item_object_id )];
		}
		else {
			if ( 'post_type' === $_menu_item_type && isset( $this->processed_posts[intval( $_menu_item_object_id )] ) ) {
				$_menu_item_object_id = $this->processed_posts[intval( $_menu_item_object_id )];
			}
			else {
				if ( 'custom' !== $_menu_item_type ) {
					return;
				}
			}
		}
		$args = array(
			'menu-item-object-id' => $_menu_item_object_id,
			'menu-item-object'    => $_menu_item_object,
			'menu-item-type'      => $_menu_item_type,
		);
		if ( isset( $_menu_item_menu_item_parent ) ) {
			if ( isset( $this->processed_menu_items[intval( $_menu_item_menu_item_parent )] ) ) {
				$_menu_item_menu_item_parent = $this->processed_menu_items[intval( $_menu_item_menu_item_parent )];
			}
			else {
				if ( $_menu_item_menu_item_parent ) {
					$_menu_item_menu_item_parent = 0;
				}
			}
			$args['menu-item-parent-id'] = $_menu_item_menu_item_parent;
		}
		if ( isset( $_menu_item_classes ) ) {
			if ( is_array( $_menu_item_classes ) ) {
				$_menu_item_classes = implode( ' ', $_menu_item_classes );
			}
			$args['menu-item-classes'] = $_menu_item_classes;
		}
		if ( isset( $_menu_item_target ) ) {
			$args['menu-item-target'] = $_menu_item_target;
		}
		if ( isset( $_menu_item_xfn ) ) {
			$args['menu-item-xfn'] = $_menu_item_xfn;
		}
		if ( isset( $_menu_item_url ) ) {
			$args['menu-item-url'] = $_menu_item_url;
		}
		if ( isset( $item['title'] ) ) {
			$args['menu-item-title'] = $item['title'];
		}
		if ( isset( $item['content'] ) ) {
			$args['menu-item-description'] = $item['content'];
		}
		if ( isset( $item['e'] ) ) {
			$args['menu-item-attr-title'] = $item['e'];
		}
		if ( isset( $item['status'] ) ) {
			$args['menu-item-status'] = $item['status'];
		}
		if ( isset( $item['o'] ) ) {
			$args['menu-item-position'] = intval( $item['o'] );
		}
		$menu_item_db_id = $item_id;

		if ( ! is_nav_menu_item( $menu_item_db_id ) ) {
			$post_args = array(
				'import_id'      => $item_id,
				'post_date_gmt'  => $item['d'],
				'post_name'      => $item['n'],
				'comment_status' => $item['comment_status'],
				///
				'ping_status'    => $item['ping_status'],
				'post_type'      => 'nav_menu_item',
			);
			if ( isset( $item['title'] ) ) {
				$post_args['post_title'] = $item['title'];
			}
			if ( isset( $post_parent ) ) {
				$post_args['post_parent'] = $post_parent;
			}
			if ( isset( $item['content'] ) ) {
				$post_args['post_content'] = $item['content'];
			}
			if ( isset( $item['e'] ) ) {
				$post_args['post_excerpt'] = $item['e'];
			}
			if ( isset( $item['status'] ) ) {
				$post_args['post_status'] = $item['status'];
			}
			if ( isset( $item['o'] ) ) {
				$post_args['menu_order'] = intval( $item['o'] );
			}
			if ( isset( $item['password'] ) ) {
				$post_args['post_password'] = $item['password'];
			}
			$post_args       = wp_slash( $post_args );
			$menu_item_db_id = wp_insert_post( $post_args );
		}
		if ( isset( $_menu_item_wowmall_megamenu_page ) && (bool) $_menu_item_wowmall_megamenu_page ) {
			$this->megamenu_pages[$menu_item_db_id]      = $_menu_item_wowmall_megamenu_page;
			$_REQUEST['menu-item-wowmall-megamenu-page'] = $this->megamenu_pages;
		}

		$id = wp_update_nav_menu_item( $menu_id, $menu_item_db_id, $args );
		if ( isset( $_menu_item_wowmall_megamenu_page ) && (bool) $_menu_item_wowmall_megamenu_page ) {
			unset( $_REQUEST['menu-item-wowmall-megamenu-page'] );
		}
		if ( $id && ! is_wp_error( $id ) ) {
			$this->processed_menu_items[intval( $item_id )] = (int) $id;
		}
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment
	 *
	 * @param array  $post Attachment post details from WXR
	 * @param string $url  URL to fetch attachment from
	 *
	 * @return int|WP_Error Post ID on success, WP_Error otherwise
	 */
	function process_attachment( $postdata, $post ) {

		$upload_date = $post['d'];

		if ( isset( $post['u'] ) ) {
			$file_name = $url = $post['u'];
		}
		else {
			$img_meta  = $post['m']['_wp_attachment_metadata'][0];
			$file_name = $img_meta['f'];
			$width     = $img_meta['w'];
			$height    = $img_meta['h'];
			if( 2000 < $width ) {
				$width = 2000;
				$height = round( $width*$height/$img_meta['w'] );
			}
			if( 2000 < $height ) {
				$height = 2000;
				$width = round( $width*$height/$img_meta['h'] );
			}
			$url = 'https://placehold.it/' . $width . 'x' . $height . '.' . pathinfo( $img_meta['f'], PATHINFO_EXTENSION );
			if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $file_name, $matches ) ) {
				$upload_date = $matches[0];
			}
		}
		$postdata['status'] = 'inherit';

		$file_name = basename( $file_name );

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, null, '', $upload_date );

		if ( $upload['error'] ) {
			return new WP_Error( 'upload_dir_error', $upload['error'] );
		}
		// fetch the remote url and write it to the placeholder file

		$headers = wp_safe_remote_get( $url, array(
			'stream'   => true,
			'filename' => $upload['file'],
		) );
		// make sure the fetch was successful
		if ( is_wp_error( $headers ) ) {
			@unlink( $upload['file'] );

			return new WP_Error( 'import_file_error', sprintf( esc_html__( 'Remote server returned error response %1$d %2$s', 'wowmall' ), esc_html( $headers['response'] ), get_status_header_desc( $headers['response'] ) ) );
		}
		$info = wp_check_filetype( $upload['file'] );

		if ( $info ) {
			$postdata['post_mime_type'] = $info['type'];
		}
		else {
			return new WP_Error( 'attachment_processing_error', esc_html__( 'Invalid file type', 'wowmall' ) );
		}

		$postdata['guid'] = $upload['url'];
		// as per wp-admin/includes/upload.php
		$post_id = wp_insert_attachment( $postdata, $upload['file'] );

		if ( ! is_wp_error( $post_id ) ) {
			$this->media_metadata[$post_id] = $upload['file'];
		}

		unset( $upload );

		return $post_id;
	}

	/**
	 * Parse a WXR file
	 *
	 * @param string $file Path to WXR file for parsing
	 */
	function parse( $file ) {

		$content = file_get_contents( $file );

		return json_decode( $content, true );

		/*global $wp_filesystem;

		if ( $wp_filesystem->exists( $file ) ) {
			$text = $wp_filesystem->get_contents( $file );
			if ( ! $text ) {
				return new WP_Error( 'filesystem_error', 'File empty' );
			} else {
				return json_decode( $text, true );
			}
		} else {
			return new WP_Error( 'filesystem_error', 'File doesn\'t exist' );
		}*/
	}

	/**
	 * Added to http_request_timeout filter to force timeout at 60 seconds during import
	 * @return int 60
	 */
	function bump_request_timeout() {
		return 60;
	}

	// return the difference in length between two strings
	function cmpr_strlen( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}
}

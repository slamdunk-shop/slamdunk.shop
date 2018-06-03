<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Wowmall
 */

global $wowmall_options;

$wowmall_404_bg = ! empty( $wowmall_options['bg_404']['url'] ) ? $wowmall_options['bg_404']['url'] : WOWMALL_THEME_URI . '/assets/images/bg_404.jpg';

get_header(); ?>
	<div id=primary class=content-area style="background-image:url(<?php echo esc_url( $wowmall_404_bg ); ?>)">
		<main id=main class=site-main>
			<section class="error-404 not-found">
				<header class=page-header>
					<h1 class=page-title-404><?php esc_html_e( '404', 'wowmall' ); ?></h1>
					<h2 class=page-title><?php esc_html_e( 'That page can&rsquo;t be found.', 'wowmall' ); ?></h2>
				</header><!-- .page-header -->

				<div class=page-content>
					<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'wowmall' ); ?></p>
					<?php wowmall_search(); ?>
				</div>
			</section>
		</main>
	</div>
<?php get_footer(); ?>

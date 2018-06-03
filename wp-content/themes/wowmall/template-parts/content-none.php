<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wowmall
 */

?>

<section class="no-results not-found">
	<div class=row>
		<div class="col-xl-4 offset-xl-4 col-lg-8 offset-lg-2">

			<div class=page-content>
				<p class=no-search-results><?php esc_html_e( 'Nothing Found', 'wowmall' ); ?></p>
				<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

					<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'wowmall' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

				<?php elseif ( is_search() ) : ?>

					<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wowmall' ); ?></p>
					<?php get_search_form(); ?>

				<?php else : ?>

					<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wowmall' ); ?></p>
					<?php get_search_form(); ?>

				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

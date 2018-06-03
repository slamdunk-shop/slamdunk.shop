<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wowmall
 */

get_header();

global $wowmall_options;


?>
<div class=container>
	<?php if( ( ! isset( $wowmall_options['page_title'] ) || $wowmall_options['page_title'] ) && ! is_front_page() &&
	          ! ( function_exists( 'is_cart' ) && is_cart() ) &&
	          ! ( function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) ) { ?>
		<header class=page-header>
			<?php the_title( '<h1 class=page-title>', '</h1>' ); ?>
		</header>
	<?php } ?>
	<div class=row>
		<main id=primary class="content-area col-xl-12 site-main">

			<?php while ( have_posts() ) { the_post();

				get_template_part( 'template-parts/content', 'page' );

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}

			} ?>

		</main>
	</div>
</div>

<?php get_footer(); ?>

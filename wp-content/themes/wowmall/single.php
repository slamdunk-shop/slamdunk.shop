<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Wowmall
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();
$col = 'col-xl-8';
$header_col = 'col-xl-8 offset-xl-2';
global $wowmall_options;
if( ! is_active_sidebar( 'sidebar-1' ) || ( isset( $wowmall_options['blog_sidebar_single'] ) && ! $wowmall_options['blog_sidebar_single'] ) ) {
	$col .= ' offset-xl-2';
} else {
	$col .= ' with-slidebar';
	$header_col = 'col-xl-10 offset-xl-1';
}
?>
<div class=container>
	<div class=row>
		<div class="<?php echo esc_attr( $header_col ); ?>">
			<header class=page-header>
				<?php
				the_title( '<h1 class=page-title>', '</h1>' ); ?>
			</header>
			<?php while ( have_posts() ) : the_post(); ?>
			<div class=entry-meta>
				<?php
				wowmall_post_meta(); ?>
			</div>
		</div>
	</div>
	<div class=row>
		<main id=primary class="content-area site-main <?php echo esc_attr( $col ); ?>">

				<?php get_template_part( 'template-parts/content', 'single' ); ?>

				<?php wowmall_post_author_bio(); ?>

				<?php
				if ( ! get_post_format() ) {
					the_post_navigation();
				} ?>


				<?php wowmall_related_posts(); ?>

				<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				?>

		</main>

		<?php get_sidebar(); ?>
	</div>

	<?php endwhile; // End of the loop. ?>
</div>
<?php get_footer(); ?>

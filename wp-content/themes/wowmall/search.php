<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Wowmall
 */

get_header();
$col      = 'col-xl-12';
$post_col = 'col-xl-3 col-lg-4 col-md-6 col-sm-12 col-xs-12';
$template = '';
global $wp_query,$wowmall_options;
if( ! empty( $wowmall_options['blog_layout_type'] ) && ! in_array( $wowmall_options['blog_layout_type'], array( 'grid', 'masonry' ) ) ) {
	$col      = 'col-xl-10 col-lg-9 col-md-8';
	$post_col = 'col-xs-12';
	$template = '-list';
}
?>
<div class="container">

	<header class=page-header>
		<h1 class="page-title page-title-search"><?php printf( esc_html__( 'Search Results for %s', 'wowmall' ), '<span class=wowmall-search-query-title>&#x27;' . get_search_query() . '&#x27;</span>' ); ?></h1>
	</header><!-- .page-header -->
	<div class="row">
		<main id=primary class="content-area site-main <?php echo esc_attr( $col ); ?>">
			<?php if ( have_posts() ) : ?>
			<div class="page-content">
				<div class="row posts-<?php echo count( $wp_query->posts ); ?>">

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="<?php echo esc_attr( $post_col ); ?>">
					<?php
					/**
					 * Run the loop for the search to output the results.
					 * If you want to overload this in a child theme then include a file
					 * called content-search.php and that will be used instead.
					 */
					get_template_part( 'template-parts/content', '' );
					?>
					</div>
				<?php endwhile; ?>
				</div>
			</div>
			<?php the_posts_pagination( array(
				'prev_text' => '<i class=myfont-left-open-2></i>',
				'next_text' => '<i class=myfont-right-open-2></i>',
				'type'      => 'list',
			) ); ?>

			<?php else : ?>

				<?php get_template_part( 'template-parts/content', 'none' ); ?>

			<?php endif; ?>

		</main>

		<?php get_sidebar(); ?>
	</div>
</div>

<?php get_footer(); ?>
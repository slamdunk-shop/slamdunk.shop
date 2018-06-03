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

get_header(); ?>
<div class=container>
	<div class=row>
		<div id=primary class="content-area col-xl-8 offset-xl-2 site-main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'single-gallery' ); ?>

			<?php endwhile; // End of the loop. ?>

		</div>
	</div>
</div>
<?php get_footer(); ?>

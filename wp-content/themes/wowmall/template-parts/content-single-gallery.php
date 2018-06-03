<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wowmall
 */

?>

<article id=post-<?php the_ID(); ?> <?php post_class('post-single'); ?>>

	<?php wowmall_single_thumb(); ?>

	<div class=entry-content>
		<?php the_content(); ?>
	</div>

	<footer class=entry-footer>
		<?php wowmall_post_footer(); ?>
	</footer>
</article>


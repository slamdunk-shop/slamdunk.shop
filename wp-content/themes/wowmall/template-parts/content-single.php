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
		<?php $format = get_post_format();
		if( 'audio' === $format && ! has_post_thumbnail() ) { ?>
		<div class=post-format-audio__audio>
			<?php do_action( 'wowmall_post_format_audio' ); ?>
		</div>
		<?php } else {
			wowmall_content_single();
		}
		?>
	</div>

	<footer class=entry-footer>
		<?php wowmall_post_footer(); ?>
	</footer>
</article>


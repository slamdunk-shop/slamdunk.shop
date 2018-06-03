<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wowmall
 */
?>
<article id=post-<?php the_ID(); ?> <?php post_class(); ?>>
	<?php wowmall_post_thumb();
	if ( 'post' === get_post_type() ) : ?>
		<div class=entry-meta>
			<?php wowmall_post_meta(); ?>
		</div>
	<?php endif; ?>
	<header class=entry-header>
		<?php the_title( sprintf( '<h3 class=entry-title><a href="%s" rel=bookmark>', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
	</header>

	<div class=entry-content>
		<?php do_action( 'wowmall_post_format_quote' );
		$format = get_post_format();
		if( 'audio' === $format && ! has_post_thumbnail() ) { ?>
			<div class=post-format-audio__audio>
				<?php do_action( 'wowmall_post_format_audio' ); ?>
			</div>
		<?php }
		the_excerpt(); ?>
	</div>

	<footer class=entry-footer>
		<?php wowmall_entry_footer(); ?>
	</footer>
</article>

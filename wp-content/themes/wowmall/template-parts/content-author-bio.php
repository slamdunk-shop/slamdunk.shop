<?php
/**
 * The template for displaying author bio.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Wowmall
 * @subpackage widgets
 */
?>
<div class=post-author-bio>
	<div class=post-author__avatar><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php
		echo get_avatar( get_the_author_meta( 'user_email' ), 204, '', esc_attr( get_the_author_meta( 'nickname' ) ) );
	?></a></div>
	<div class=post-author__content-wrapper>
		<h4 class=post-author__title><?php
			printf( esc_html__( 'Written by %s', 'wowmall' ), get_the_author_posts_link() );
		?></h4>
		<div class=post-author__content><?php
			echo get_the_author_meta( 'description' );
		?></div>
	</div>
</div>

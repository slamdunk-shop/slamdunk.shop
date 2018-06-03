<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wowmall
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id=comments class=comments-area>

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h4 class=comments-title>
			<?php
				printf( // WPCS: XSS OK.
					esc_html( _nx( '%1$s response', '%1$s responses', get_comments_number(), 'comments title', 'wowmall' ) ),
					number_format_i18n( get_comments_number() ),
					'<span>' . get_the_title() . '</span>'
				);
			?>
		</h4>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id=comment-nav-above class="navigation comment-navigation" role=navigation>
			<h2 class=screen-reader-text><?php esc_html_e( 'Comment navigation', 'wowmall' ); ?></h2>
			<div class=nav-links>

				<div class=nav-previous><?php previous_comments_link( esc_html__( 'Older Comments', 'wowmall' ) ); ?></div>
				<div class=nav-next><?php next_comments_link( esc_html__( 'Newer Comments', 'wowmall' ) ); ?></div>

			</div>
		</nav>
		<?php endif; ?>

		<ol class=commentlist>
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 72,
					'callback'    => 'wowmall_comment_callback',
				) );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id=comment-nav-below class="navigation comment-navigation" role=navigation>
			<h4 class=screen-reader-text><?php esc_html_e( 'Comment navigation', 'wowmall' ); ?></h4>
			<div class=nav-links>

				<div class=nav-previous><?php previous_comments_link( esc_html__( 'Older Comments', 'wowmall' ) ); ?></div>
				<div class=nav-next><?php next_comments_link( esc_html__( 'Newer Comments', 'wowmall' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php endif; // Check for comment navigation. ?>

	<?php endif; // Check for have_comments(). ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class=no-comments><?php esc_html_e( 'Comments are closed.', 'wowmall' ); ?></p>
	<?php endif; ?>

	<?php
	$commenter = wp_get_current_commenter();

	$comment_form = array(
		'title_reply_before'   => '<h4 id=reply-title class=comment-reply-title>',
		'title_reply_after'    => '</h4>',
		'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'wowmall' ),
		'comment_notes_after'  => '',
		'fields'               => array(
			'author' => '<p class=comment-form-author>' . '<label for=author>' . esc_html__( 'Name', 'wowmall' ) . ': <span class=required>*</span></label> ' .
			            '<input id=author name=author type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" required placeholder="' . esc_html__( 'Enter your name, please', 'wowmall' ) . '"></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'E-mail', 'wowmall' ) . ': <span class="required">*</span></label> ' .
			            '<input id="email" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" required placeholder="' . esc_html__( 'Enter your e-mail, please', 'wowmall' ) . '"></p>',
		),
		'label_submit'  => esc_html__( 'Submit', 'wowmall' ),
		'logged_in_as'  => '',
		'comment_field' => '<p class=comment-form-comment><label for=comment>' . esc_html__( 'Your Comment', 'wowmall' ) . ': <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required placeholder="' . esc_html__( 'Enter your review, please', 'wowmall' ) . '"></textarea></p>',
		//'class_submit'  => 'submit btn btn-primary btn-sm',
		'class_submit'  => 'button alt',
		//'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
	);

	comment_form( $comment_form ); ?>

</div><!-- #comments -->

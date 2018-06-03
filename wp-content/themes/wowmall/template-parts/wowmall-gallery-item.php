<?php
/**
 * Template part for displaying gallery items.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wowmall
 */
?>
<figure>
	<?php do_action( 'wowmall_gallery_item_thumbnail' );
	do_action( 'wowmall_gallery_item_caption' ); ?>
</figure>
<?php
global $wowmall_options;
$footer_layout = ! empty( $wowmall_options['footer_layout'] ) ? (int) $wowmall_options['footer_layout'] : 1;
?>
<footer id=colophon class="site-footer site-footer-<?php echo $footer_layout; ?>">
	<?php
	if( 4 === $footer_layout ) {
		get_template_part( 'template-parts/footer/top-panel', '1' );
	}
	?>
	<div class=footer-inner>
		<div class=container>
			<?php wowmall_footer_content($footer_layout); ?>
		</div>
		<?php
		get_template_part( 'template-parts/footer/bottom-panel', 'mobile' );
		?>
	</div>
</footer><!-- #colophon -->
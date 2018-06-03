
<footer id=colophon class="site-footer site-footer-4">
	<?php
	get_template_part( 'template-parts/footer/top-panel', '1' );
	?>
	<div class=footer-inner>
		<?php ob_start();
		wowmall_footer_content('4');
		$content = ob_get_clean();
		if( ! empty( $content ) ) { ?>
			<div class=container>
				<?php echo $content ?>
			</div>
			<?php
		}
		get_template_part( 'template-parts/footer/bottom-panel', '2' );
		?>
	</div>
</footer>
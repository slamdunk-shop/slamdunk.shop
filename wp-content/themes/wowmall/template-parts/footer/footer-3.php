<footer id=colophon class="site-footer site-footer-3">
	<div class=footer-inner>
		<?php ob_start();
		wowmall_footer_content('3');
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
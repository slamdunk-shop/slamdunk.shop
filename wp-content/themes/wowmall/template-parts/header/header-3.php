<?php
get_template_part( 'template-parts/header/top-panel', '3' );
?>
<header id=header class=header-layout-3>
	<div class=container>
		<div class=wowmall-main-navigation-wrapper>
			<div class=header-logo-wrapper>
				<?php wowmall_get_logo(); ?>
			</div>
			<?php wowmall_tags()->nav(); ?>
		</div>
	</div>
</header>
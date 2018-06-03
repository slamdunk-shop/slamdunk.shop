<?php
get_template_part( 'template-parts/header/top-panel', '2' );
?>
<header id=header class=header-layout-4>
	<div class=container>
		<div class=wowmall-main-navigation-wrapper>
			<div class=row>
				<div class="col-sm-3 col-lg-3 col-xl-3 header-logo-wrapper">
					<?php wowmall_get_logo(); ?>
				</div>
				<div class="col-sm-9 col-md-9 col-lg-9 wowmall-main-menu-wrapper">
					<?php wowmall_tags()->nav(); ?>
					<div class=wowmall-top-search-wrapper>
						<?php wowmall_top_search(); ?>
					</div>
			</div>
			</div>
		</div>
	</div>
</header>
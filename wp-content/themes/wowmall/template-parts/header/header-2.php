<?php
get_template_part( 'template-parts/header/top-panel', '2' );
?>
<header id=header class=header-layout-2>
	<div class=container>
		<div class=wowmall-main-navigation-wrapper>
			<div class=row>
				<div class="col-sm-3 col-lg-3 col-xl header-logo-wrapper">
					<?php wowmall_get_logo(); ?>
				</div>
				<div class="col-sm-6 col-md-8 col-lg-7 wowmall-main-menu-wrapper">
					<?php wowmall_tags()->nav(); ?>
				</div>
				<div class="col-sm-3 col-md wowmall-top-search-wrapper">
					<?php wowmall_top_search(); ?>
				</div>
			</div>
		</div>
	</div>
</header>
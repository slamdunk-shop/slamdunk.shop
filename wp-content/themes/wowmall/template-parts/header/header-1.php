<?php
get_template_part( 'template-parts/header/top-panel', '1' );
?>
<header id=header class=header-layout-1>
	<div class=container>
		<div class=wowmall-main-navigation-wrapper>
			<div class=row>
				<div class="col-sm-9 col-md-11">
                    <?php wowmall_tags()->nav(); ?>
				</div>
				<div class="col-sm-3 col-md-1 wowmall-top-search-wrapper">
                    <?php wowmall_top_search(); ?>
				</div>
			</div>
		</div>
	</div>
</header>
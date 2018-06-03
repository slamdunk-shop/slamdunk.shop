<header id=header class=header-layout-5>
	<div class=container>
		<div class=wowmall-main-navigation-wrapper>
			<div class=row>
				<div class="col-sm-3 col-lg-3 col-xl-3 header-logo-wrapper">
					<?php wowmall_get_logo(); ?>
				</div>
				<div class="col-sm-9 col-md-9 col-lg-9 combined_nav">
					<?php wowmall_tags()->nav(); ?>
					<div class=header-tools-wrapper>
						<div class=header-tools>
							<?php wowmall_tags()->orders(); ?>
							<?php wowmall_compare(); ?>
							<?php wowmall_wishlist(); ?>
							<?php wowmall_tags()->account(); ?>
							<?php wowmall_cart(); ?>
							<?php wowmall_currency(); ?>
							<div class=wowmall-top-search-wrapper>
							<?php wowmall_top_search(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
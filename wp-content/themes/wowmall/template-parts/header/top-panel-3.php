<div id=top-panel class="top-panel top-panel-3">
	<div class=container>
		<div class=top-panel-container>
			<div class=row>
				<div class="col-sm-6 col-xl">
					<?php wowmall_social_nav(); ?>
				</div>
				<div class="col-xl-8 col-xxl-7 header-text-wrapper">
					<div class=header-text>
						<?php wowmall_header_text(); ?>
					</div>
				</div>
				<div class="col-sm-6 col-xl header-tools-wrapper">
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
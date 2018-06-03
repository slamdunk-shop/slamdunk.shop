<div id=top-panel class="top-panel top-panel-1">
	<div class=container>
		<div class=top-panel-inner-wrapper>
			<div class=row>
				<div class="col-sm-6 col-lg-6 col-xl header-logo-wrapper">
					<?php wowmall_get_logo(); ?>
				</div>
				<div class="col-xl-8 header-text-wrapper">
					<div class=header-text>
						<?php wowmall_header_text(); ?>
					</div>
				</div>
				<div class="col-sm-6 col-lg-6 col-xl header-tools-wrapper">
					<div class=header-tools>
						<?php wowmall_tags()->orders(); ?>
						<?php wowmall_compare(); ?>
						<?php wowmall_wishlist(); ?>
						<?php wowmall_tags()->account(); ?>
						<?php wowmall_cart(); ?>
						<?php wowmall_currency(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
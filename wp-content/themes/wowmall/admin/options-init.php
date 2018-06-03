<?php

/**
 * For full documentation, please visit: http://docs.reduxframework.com/
 * For a more extensive sample-config file, you may look at:
 * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
 */

if ( ! class_exists( 'Redux' ) ) {
	return;
}

// This is your option name where all the Redux data is stored.
$opt_name = 'wowmall_options';

$args = array(
	// Show the panel pages on the admin bar
	'admin_bar_icon' => 'dashicons-cart',
	//'async_typography' => true,
	// Set a different name for your global variable other than the opt_name
	'dev_mode'       => false,
	// Permissions needed to access the options panel.
	'menu_icon'      => WOWMALL_THEME_URI . '/admin/assets/images/admin-logo.png',
	// This variable sets the text to display as the admin menu’s label, and only when the admin menu is available.
	'menu_title'     => esc_html__( 'Wowmall Options', 'wowmall' ),
	// TYPICAL -> Change these values as you need/desire
	'opt_name'       => $opt_name,

	'page_title'    => esc_html__( 'Wowmall Options', 'wowmall' ),
	'customizer'    => true,
	'save_defaults' => true,
);

Redux::setArgs( $opt_name, $args );

$color_1     = Redux::getOption( 'wowmall_options', 'accent_color_1' );
$color_2     = Redux::getOption( 'wowmall_options', 'accent_color_2' );
$body_color  = Redux::getOption( 'wowmall_options', 'body_typography' );
$tools_color = Redux::getOption( 'wowmall_options', 'top_panel_btns_color' );
$variations  = Redux::getOption( 'wowmall_options', 'custom_variations' );
if ( '0' === $variations ) {
	Redux::setOption( 'wowmall_options', 'custom_variations_color', 0 );
	Redux::setOption( 'wowmall_options', 'custom_variations_size', 0 );
}

if ( empty( $color_1 ) ) {
	$color_1 = '#fc6f38';
}
if ( empty( $color_2 ) ) {
	$color_2 = '#222';
}
if ( empty( $body_color ) ) {
	$body_color = '#888';
}
else {
	$body_color = $body_color['color'];
}

$old_tools_color = empty( $tools_color ) || is_array( $tools_color ) ? '#fff' : $tools_color;
$new_tools_color = array(
	'regular' => $old_tools_color,
	'hover'   => $color_1,
	'active'  => $color_1,
);

$colors = array(
	'1' => array(
		'color' => 'ul.styled>li:before,.site-breadcrumb .breadcrumb_text,ol>li:before,blockquote:before,.header-tools-wrapper a:hover,body.focus-styles .header-tools-wrapper a:focus,.header-tools-wrapper .header-cart-wrapper:hover .cart-contents,body.focus-styles .header-tools-wrapper .header-cart-wrapper:focus .cart-contents,.header-tools-wrapper .header-currency-wrapper>a:hover,body.focus-styles .header-tools-wrapper .header-currency-wrapper>a:focus,h1 a:hover,h2 a:hover,h3 a:hover,h4 a:hover,h5 a:hover,h6 a:hover,a:hover>h5,.site-breadcrumb a:hover,.site-breadcrumb a:hover *,.contacts-list .smile_icon_list.with_bg li .icon_list_icon,.btn.btn-inline,.btn.btn-icon.active,mark,ins,.entry-footer .entry-share-btns .share-btns__list .share-btns__link:hover,body.focus-styles .entry-footer .entry-share-btns .share-btns__list .share-btns__link:focus,.post-navigation .post-content-wrap .post-cats,.post-navigation .nav-links a:hover .post-nav-navigator,body.focus-styles .post-navigation .nav-links a:focus .post-nav-navigator,#comments ol.commentlist li .comment-text div.meta a:hover,#comments ol.commentlist li .comment-text .reply a:hover,body.focus-styles #comments ol.commentlist li .comment-text .reply a:focus,.widget.widget_calendar .calendar_wrap.starts_from_0 table#wp-calendar th:first-child,.widget.widget_calendar .calendar_wrap.starts_from_0 table#wp-calendar td:first-child,.widget.widget_calendar .calendar_wrap.starts_from_1 table#wp-calendar th:last-child,.widget.widget_calendar .calendar_wrap.starts_from_1 table#wp-calendar td:last-child,.widget.widget_calendar .calendar_wrap.starts_from_2 table#wp-calendar th:nth-child(6),.widget.widget_calendar .calendar_wrap.starts_from_2 table#wp-calendar td:nth-child(6),.widget.widget_calendar .calendar_wrap.starts_from_3 table#wp-calendar th:nth-child(5),.widget.widget_calendar .calendar_wrap.starts_from_3 table#wp-calendar td:nth-child(5),.widget.widget_calendar .calendar_wrap.starts_from_4 table#wp-calendar th:nth-child(4),.widget.widget_calendar .calendar_wrap.starts_from_4 table#wp-calendar td:nth-child(4),.widget.widget_calendar .calendar_wrap.starts_from_5 table#wp-calendar th:nth-child(3),.widget.widget_calendar .calendar_wrap.starts_from_5 table#wp-calendar td:nth-child(3),.widget.widget_calendar .calendar_wrap.starts_from_6 table#wp-calendar th:nth-child(2),.widget.widget_calendar .calendar_wrap.starts_from_6 table#wp-calendar td:nth-child(2),.wowmall-gallery-container .wowmall-gallery-item .item-content h3 a:hover,body.focus-styles .wowmall-gallery-container .wowmall-gallery-item .item-content h3 a:focus,.wowmall-gallery-container .wowmall-gallery-item .gallery-cat-count:before,#footer-bottom-panel .wowmall-to-top:hover,#footer-bottom-panel a:hover,#colophon .wowmall-toggle-btn,#primary-menu .wowmall-mega-sub .wpb_wrapper .wowmall-big-list ul.menu li a:hover,body.focus-styles #primary-menu .wowmall-mega-sub .wpb_wrapper .wowmall-big-list ul.menu li a:focus,#primary-menu li.buy-theme>a,.header-sticky-wrapper #primary-menu li.buy-theme>a,#primary-menu-mobile li.buy-theme>a,.wowmall-top-search button[type=submit],.header-layout-mobile .main-menu #primary-menu-mobile .active>a>.menu-item-toggle,.header-layout-mobile .main-menu #primary-menu-mobile .wpb_wrapper.active .widgettitle>.menu-item-toggle,.header-tools-wrapper .header-tools .header-currency-wrapper.active>a,.wowmall-search-query-title,body.error404 .page-title-404,.header-layout-6 .wowmall-top-search-wrapper .wowmall-top-search.expanded button[type=submit],.wpb-js-composer .wpb_wrapper .vc_tta-color-white.vc_tta-style-flat .vc_tta-panel.vc_active .vc_tta-panel-title>a,.wpb-js-composer .wpb_wrapper .vc_tta-color-white.vc_tta-style-flat .vc_tta-panel .vc_tta-panel-title>a:hover,.wpb-js-composer .wpb_wrapper .vc_tta-color-white.vc_tta-style-flat .vc_tta-tab.vc_active>a,.wpb-js-composer .vc_tta-color-white.vc_tta-style-flat .vc_tta-tab>a:hover,.wowmall-testimonials:before,.header-sticky-wrapper .header-tools-wrapper .header-tools .header-cart-wrapper .cart-contents:not(:hover),h1 a.default-link, h2 a.default-link,h3 a.default-link,h4 a.default-link,h5 a.default-link,h6 a.default-link,.widget_search .search-submit,.page-content .search-submit',

		'background-color' => '.dropcap:first-letter,.entry-content ins,input[type="submit"].button.alt,.btn.btn-primary,.btn.btn-gray:hover,body.focus-styles .btn.btn-gray:focus,#secondary .widget.widget_mc4wp_form_widget .btn,.ui-selectmenu-menu .ui-menu-item.ui-state-focus,.ui-selectmenu-button.ui-state-focus,.ui-selectmenu-button.ui-state-hover,input[type=radio]:checked,input[type=radio]:hover,.page-numbers > span,.page-numbers span.current,.page-numbers a.current,.post-format-link__link a:after,.hentry.sticky:after,.post-format-audio__audio,.single-post .post-single .entry-content > p:first-child:first-letter,.widget.widget_calendar table#wp-calendar tbody td a:hover,body.focus-styles .widget.widget_calendar table#wp-calendar tbody td a:focus,.social-media-profiles-menu a:hover,body.focus-styles .social-media-profiles-menu a:focus,.widget.wowmall-widget-instagram .instagram-items .instagram-item .instagram-link,.mfp-wrap.wowmall-gallery-lightbox .mfp-image-holder .mfp-close,.mfp-wrap.wowmall-gallery-lightbox .mfp-container .swiper-container.swiper-container-horizontal>.swiper-scrollbar .swiper-scrollbar-drag,#colophon.site-footer-1 .mc4wp-form-fields .btn.btn-dark:hover,body.focus-styles #colophon.site-footer-1 .mc4wp-form-fields .btn.btn-dark:focus,#colophon.site-footer-2 .mc4wp-form-fields .btn.btn-dark:hover,body.focus-styles #colophon.site-footer-2 .mc4wp-form-fields .btn.btn-dark:focus,#colophon.site-footer-3 .mc4wp-form-fields .btn.btn-dark:hover,body.focus-styles #colophon.site-footer-3 .mc4wp-form-fields .btn.btn-dark:focus,#footer-bottom-panel .wowmall-to-top,#wowmall-subscribe-popup,#wowmall-subscribe-popup .mfp-close,.body-maintenance .mc4wp-form-fields .btn,#primary .social-media-profiles-menu a:hover,.collection-banner-with-desc.ult-banner-block h3.bb-top-title:before,.ult-banner-block.banner_style_4 .mask .bb-link,.ult-banner-block.banner_style_2,.ult-banner-block.banner_style_4 .bb-top-title div:before,.wowmall-instagram .instagram-items .instagram-item a:before',

		'background' => '.wowmall-top-search .wowmall-search-results .wowmall-search-results-inner::-webkit-scrollbar-thumb',

		'border-color' => 'input[type=radio]:checked,input[type=radio]:hover,#footer-bottom-panel .wowmall-to-top',

		'border-left-color' => 'body.wowmall-page-preloader:after,.wowmall-top-search-wrapper .wowmall-top-search .wowmall-search-results .wowmall-search-loading',
	),
	'2' => array(
		'color' => '.btn.btn-default,ul.styled ul>li:before,ol ol>li:before,blockquote,label,.btn,.btn.btn-border,.dark-row .btn.btn-border.btn-light:hover,.btn.btn-inline:not(.ajax_add_to_cart):hover,body.desktop .btn.btn-inline:hover,.btn.btn-icon:hover,body.focus-styles .btn.btn-inline:focus,body.focus-styles .btn.btn-icon:focus,fieldset legend,.entry-meta>* a:hover,body.focus-styles .entry-meta>* a:focus,.entry-footer .comments-link a:hover,body.focus-styles .entry-footer .comments-link a:focus,.widget.widget_recent_entries .comments-link a:hover,body.focus-styles .widget.widget_recent_entries .comments-link a:focus,.page-numbers a:hover,body.focus-styles .page-numbers a:focus,.entry-footer .entry-share-btns .share-btns__list .share-btns__link,.post-navigation a:hover .post-content-wrap .post-meta,.body.focus-styles .post-navigation a:focus .post-content-wrap .post-meta,.post-navigation .nav-links .post-nav-navigator,.widget.widget_recent_entries .post-meta a:hover,body.focus-styles .widget.widget_recent_entries .post-meta a:focus,.widget.widget_tag_cloud .tagcloud a:hover,body.focus-styles .widget.widget_tag_cloud .tagcloud a:focus,.widget.widget_calendar table#wp-calendar tbody td,.widget.widget_calendar table#wp-calendar thead th,.widget.widget_categories>ul li a:hover,.widget.widget_archive>ul li a:hover,.widget.widget_meta>ul li a:hover,.widget.widget_pages>ul li a:hover,body.focus-styles .widget.widget_categories>ul li a:focus,body.focus-styles .widget.widget_archive>ul li a:focus,body.focus-styles .widget.widget_meta>ul li a:focus,body.focus-styles .widget.widget_pages>ul li a:focus,.mfp-wrap.wowmall-gallery-lightbox .mfp-image-holder .mfp-close:hover,body.focus-styles .mfp-wrap.wowmall-gallery-lightbox .mfp-image-holder .mfp-close:focus,#colophon .social-media-profiles-menu a:hover,body.focus-styles #colophon .social-media-profiles-menu a:focus,#colophon .mc4wp-form .subscribe-mail-input:before,#wowmall-subscribe-popup .social-media-profiles-menu a:hover,#wowmall-subscribe-popup .mc4wp-form .subscribe-mail-input:before,#primary-menu li>a:hover,#primary-menu li:hover>a,body.focus-styles #primary-menu li>a:focus,#primary-menu li.current-menu-item>a,#primary-menu .wowmall-mega-sub .wpb_wrapper .wowmall-big-list ul.menu li a,.wowmall-top-search button:hover,body.focus-styles .wowmall-top-search button:focus,.top-panel .social-media-profiles-menu a:hover,body.focus-styles .top-panel .social-media-profiles-menu a:focus,.header-layout-mobile nav.navbar #mobile-menu-wrapper .header-tools>a:hover,.header-layout-mobile nav.navbar #mobile-menu-wrapper .header-currency-wrapper a:hover,.body-maintenance .social-media-profiles-menu ul.menu>li a:hover,.body-maintenance .mc4wp-form-fields .subscribe-mail-input:before,.wpb_wrapper ul#menu-sitemap>li>a,.wpb_wrapper ul#menu-sitemap .sub-menu li a[href]:hover,.header-layout-6 .wowmall-top-search-wrapper .wowmall-top-search.expanded button[type=submit]:hover,#primary-menu-mobile li.active>a,#primary-menu-mobile .wpb_wrapper.active .widgettitle,.btn.btn-primary.disabled,.vc_progress_bar .vc_single_bar .vc_label_units,.banner-only-button .mask,.wowmall-testimonials cite,#primary-menu-mobile li.current-menu-item>a,.widget_search .search-submit:hover,.page-content .search-submit:hover,.ult-banner-block.banner_style_4 .bb-top-title,.entry-content table th',

		'background-color' => '.btn:hover,input[type="submit"].button.alt:hover,body.focus-styles .btn:focus,body.focus-styles input[type="submit"].button.alt:focus,#secondary .widget.widget_mc4wp_form_widget .btn:hover,body.focus-styles #secondary .widget.widget_mc4wp_form_widget .btn:focus,.btn.btn-dark,input[type=checkbox]:checked,body.desktop input[type=checkbox]:hover,.post-format-link__link a:hover:after,body.focus-styles .post-format-link__link a:focus:after,.widget.widget_calendar table#wp-calendar caption,.mfp-wrap.wowmall-gallery-lightbox .mfp-container .swiper-container.swiper-container-horizontal > .swiper-scrollbar,#colophon .mc4wp-form-fields .btn.btn-dark:hover,body.focus-styles #colophon .mc4wp-form-fields .btn.btn-dark:focus,#wowmall-subscribe-popup .mc4wp-form .btn-dark:hover,#wowmall-subscribe-popup .mfp-close:hover,.top-panel-3 .wowmall-top-search.expanded form,.header-layout-mobile .main-menu #primary-menu-mobile .menu-item-toggle:active,.header-tools-wrapper .header-tools .header-currency-wrapper .dropdown-menu a:hover,body.focus-styles .header-tools-wrapper .header-tools .header-currency-wrapper .dropdown-menu a:focus,.vc_tta-pageable.vc_tta.vc_general .vc_pagination-item.vc_active .vc_pagination-trigger,.ult-banner-block.banner_style_4 .mask .bb-link:hover',

		'border-color' => 'input[type=checkbox]:checked,body.desktop input[type=checkbox]:hover,.widget.widget_tag_cloud .tagcloud a:hover,body.focus-styles .widget.widget_tag_cloud .tagcloud a:focus',

		'outline-color' => '.btn-border:hover,.btn-border:focus,.page-numbers a:hover,.page-numbers a:focus',
	),
);

if ( wowmall()->is_woocommerce_activated() ) {
	$colors['1']['color'] .= ',.woocommerce .star-rating,.comment-form-rating span.stars:hover i,.comment-form-rating span.stars.selected i,.comment-form-rating span.stars.selected:hover i,.woocommerce div.product .posted_in a:hover,.woocommerce ul.products li.product .loop-product-categories a:hover,.woocommerce-product-rating,.wc-loop-rating,label .required,.wc-grid-list-button:not(.active):hover,.header-cart-wrapper .widget_shopping_cart .cart_list li.empty:before,.woocommerce a.remove:hover:before,body.focus-styles .woocommerce a.remove:focus:before,body.focus-styles.woocommerce a.remove:focus:before, .widget_shopping_cart .mini-cart-posttext,.header-cart-wrapper .widget_shopping_cart .cart_list li a:hover,body.focus-styles .header-cart-wrapper .widget_shopping_cart .cart_list li a:focus,.woocommerce-checkout-review-order h6 a:hover,.woocommerce form .form-row .required,.wc-update-cart:not([disabled]):hover,.wc-update-cart:not([disabled]):hover:before,.woocommerce-MyAccount-navigation ul li a:hover,.woocommerce-Address-title a.edit:hover,.woocommerce #reviews #comments ol.commentlist li .comment-text div.meta a:hover,body.focus-styles #comments ol.commentlist li .comment-text div.meta a:focus,body.focus-styles .woocommerce #reviews #comments ol.commentlist li .comment-text div.meta a:focus,body.focus-styles.woocommerce #reviews #comments ol.commentlist li .comment-text div.meta a:focus,.wc-product-collapse .collapse-panel a[data-toggle=collapse].collapsed:hover,body.focus-styles .wc-product-collapse .collapse-panel a[data-toggle=collapse].collapsed:focus,.wowmall-compare-cell .wowmall-compare-remove:hover,.wowmall-wishlist-remove:hover,.wowmall-products-carousel .swiper-button-prev:hover,.wowmall-products-carousel .swiper-button-next:hover,.related.products .swiper-button-prev:hover,.related.products .swiper-button-next:hover,.upsells.products .swiper-button-prev:hover,.upsells.products .swiper-button-next:hover,.cross-sells .swiper-button-prev:hover,.cross-sells .swiper-button-next:hover,.wowmall-brands-carousel .swiper-button-prev:hover,.wowmall-brands-carousel .swiper-button-next:hover,.btn-icon.wowmall-wishlist-button.added:before,.btn-icon.wowmall-wishlist-button.in_wishlist:before,.btn-icon.wowmall-compare-button.added:before,.btn-icon.wowmall-compare-button.in_compare:before,body.woocommerce-page.mobile.wowmall-filters-shown #secondary .wowmall-filters-btn.btn,body.woocommerce-page.mobile.wowmall-filters-shown #secondary .wowmall-filters-btn.btn:hover,.widget_product_search .search-submit,.widget.widget_layered_nav ul li.chosen a:hover:before,.widget.widget_layered_nav_filters ul li.chosen a:hover:before,.widget.widget_rating_filter ul li.chosen a:hover:before,.woocommerce-info:before,.wc-loop-product-categories li a:hover,div.woocommerce ul.products .product-category a:hover .wc-loop-product-title,#mfp-swiper .swiper-slide .mfp-slide-video,.woocommerce div.product .summary .loop-product-categories a:hover,.widget .wowmall-color-select a.selected:before,.widget .wowmall-size-select a.selected:before,.header-cart-wrapper .woocommerce.widget_shopping_cart ul.cart_list li a:hover .cart-product-title';

	$colors['1']['background-color'] .= ',.woocommerce .product-status>span.featured,body .woocommerce.widget.widget_price_filter .ui-slider .ui-slider-handle:hover,body .woocommerce.widget.widget_price_filter .ui-slider .ui-slider-handle.ui-state-active,body .woocommerce.widget.widget_product_categories .product-categories .cat-item.current-cat>.cat_data>a:before,.woocommerce.woomal-wc-quick-view-popup-content .mfp-close,.woocommerce div.product .share-btns__list .share-btns__link:hover,body.focus-styles .woocommerce div.product .share-btns__list .share-btns__link:focus,body.focus-styles.woocommerce div.product .share-btns__list .share-btns__link:focus,.mfp-wrap.wowmall-single-product-lightbox .mfp-close,.mfp-wrap.wowmall-single-product-lightbox .mfp-container .swiper-container.swiper-container-horizontal>.swiper-scrollbar .swiper-scrollbar-drag,.woocommerce ul.products .product-category>a .wc-loop-cat-title-count h2:before,.wowmall-lookbook-slide-wrapper .wowmall-lookbook-point:hover:before,.wowmall-lookbook-slide-wrapper .wowmall-lookbook-point.active:before,.wowmall-lookbook-popup-content .close,.btn.wowmall-filters-btn,.btn.wowmall-filters-btn:hover,.woocommerce-message .woocommerce-message-inner-added';

	$colors['1']['background'] .= ',.header-cart-wrapper .widget_shopping_cart .cart_list::-webkit-scrollbar-thumb';

	$colors['1']['border-color'] .= ',body .woocommerce.widget.widget_price_filter .ui-slider .ui-slider-handle,body .woocommerce.widget.widget_product_categories .product-categories .cat-item a:hover:before,body .woocommerce.widget.widget_product_categories .product-categories .cat-item.current-cat>.cat_data>a:before,.wowmall-lookbook-slide-wrapper .wowmall-lookbook-point:hover:after,.wowmall-lookbook-slide-wrapper .wowmall-lookbook-point.active:after';

	$colors['1']['border-left-color'] .= ',.wowmall-compare-list .wowmall-compare-loader,.wowmall-wishlist .wowmall-wishlist-loader,body .woocommerce .blockUI.blockOverlay:before, body .woocommerce .woocommerce .loader:before, body.woocommerce .blockUI.blockOverlay:before, body.woocommerce .woocommerce .loader:before,.wowmall-compare-loader,.wowmall-wishlist-loader';

	$colors['2']['color'] .= ',.woocommerce table.shop_attributes th,.images.product_page_layout_1,.woocommerce ul.products li.product .price,.woocommerce-page ul.products li.product .price,.wowmall-search-results-inner > a .price,.woocommerce div.product p.price,.woocommerce div.product span.price,.woocommerce td.product .price,.woocommerce th.product .price,.woocommerce-ordering .ui-selectmenu-text,.wc-grid-list-button.active,.header-cart-wrapper .btn.btn-border.btn-light:hover,.widget.widget_product_categories .product-categories .cat-item a:hover,.widget.widget_product_categories .product-categories .cat-item.current-cat>.cat_data>a,.widget.widget_layered_nav ul li a:hover,.widget.widget_layered_nav ul li.chosen a,.widget.widget_layered_nav_filters ul li a:hover,.widget.widget_layered_nav_filters ul li.chosen a,.header-cart-wrapper .btn.btn-primary:hover,body.focus-styles .header-cart-wrapper .btn.btn-primary:focus,.woocommerce-error .wc-forward:hover,.woocommerce-info .wc-forward:hover,.woocommerce-message .wc-forward:hover,.woocommerce div.product .group_table td.price>div,.woocommerce table.shop_table th,.woocommerce table.shop_table tr td.product-price,.woocommerce table.shop_table tr td.product-subtotal,.woocommerce table.shop_table tr.cart-subtotal td,.woocommerce table.shop_table tr.order-total td,.woocommerce-checkout-review-order h6 a,.woocommerce-checkout-review-order .cart_item .amount,.woocommerce table.shop_table_responsive tr td[data-title]:before,.woocommerce-page table.shop_table_responsive tr td[data-title]:before,.widget.widget_product_tag_cloud .tagcloud a:hover,body.focus-styles .widget.widget_product_tag_cloud .tagcloud a:focus,.mfp-wrap.wowmall-single-product-lightbox .mfp-close:hover,body.focus-styles .mfp-wrap.wowmall-single-product-lightbox .mfp-close:focus,.wowmall-lookbook-popup-content .qty_p,.wowmall-compare-heading-cell,.tablesaw-bar .tablesaw-advance a.tablesaw-nav-btn:hover,.woocommerce table.cart td .qty,.woocommerce div.product form.cart div.quantity .qty,.wowmall-products-carousel .swiper-button-prev,.wowmall-products-carousel .swiper-button-next,.related.products .swiper-button-prev,.related.products .swiper-button-next,.upsells.products .swiper-button-prev,.upsells.products .swiper-button-next,.cross-sells .swiper-button-prev,.cross-sells .swiper-button-next,.wowmall-brands-carousel .swiper-button-prev,.wowmall-brands-carousel .swiper-button-next,.page-my-account li.active .entry-title a,.page-my-account li:hover .entry-title a,.wowmall-size-select button.selected,.wowmall-size-select a.selected,.wowmall-size-select button:hover,.wowmall-size-select a:hover,.wc-update-cart,.widget_product_search .search-submit:hover,.woocommerce #secondary .widget_shopping_cart .total,#secondary .woocommerce.widget_shopping_cart .total,.woocommerce #secondary .widget_shopping_cart ul.cart_list li .quantity .amount,#secondary .woocommerce.widget_shopping_cart ul.cart_list li .quantity .amount,.woocommerce ul.product_list_widget li .widget-product-content .amount,.term-description .wc-loop-product-categories li a:not(:hover),.woocommerce div.product.sale .woocommerce-variation-price span.price,.wowmall-product-video span';

	$colors['2']['background-color'] .= ',body .woocommerce.widget.widget_price_filter .ui-slider .ui-slider-range,.woocommerce.woomal-wc-quick-view-popup-content .mfp-close:hover,.header-cart-wrapper .widget_shopping_cart .mini-cart-posttext,.mfp-wrap.wowmall-single-product-lightbox .mfp-container .swiper-container.swiper-container-horizontal > .swiper-scrollbar,.wowmall-lookbook-popup-content .close:hover,body.focus-styles .wowmall-lookbook-popup-content .close:focus,.woocommerce .product-status > span.new';

	$colors['2']['border-color'] .= ',.widget.widget_product_tag_cloud .tagcloud a:hover,body.focus-styles .widget.widget_product_tag_cloud .tagcloud a:focus,.wowmall-color-select button.selected,.wowmall-color-select button:hover,.wowmall-size-select button.selected,.wowmall-size-select button:hover,.wowmall-color-select a.selected,.wowmall-color-select a:hover,.wowmall-size-select a.selected,.wowmall-size-select a:hover';

	$colors['2']['outline-color'] .= ',.tablesaw-bar .tablesaw-advance a.tablesaw-nav-btn:hover';
}

$sections = array(
	array(
		'title'            => esc_html__( 'General Settings', 'wowmall' ),
		'id'               => 'general_settings',
		'customizer_width' => '400px',
		'icon'             => 'el el-wrench-alt',
		'fields'           => array(
			array(
				'id'       => 'page_preloader',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display preloader before page load', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable site preloader', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'lazy',
				'type'     => 'switch',
				'title'    => esc_html__( 'Images Lazy Loading', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable lazy loading on images', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'bg_404',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload background using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( '404 Page background', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/bg_404.jpg',
				),
			),
			array(
				'id'       => 'url_options',
				'type'     => 'switch',
				'title'    => esc_html__( 'Overwrite options from url', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable overwrite theme options from url query string. Not recommend for real sites.', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'optimize',
				'type'     => 'switch',
				'title'    => esc_html__( 'Optimize scripts & styles', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable site optimisation. Work better with W3 Total Cache Plugin', 'wowmall' ),
				'default'  => false,
			),
			array(
				'id'       => 'cdn',
				'type'     => 'switch',
				'title'    => esc_html__( 'CDN for 3rd party scripts & styles', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable CDN for 3rd party scripts & styles. Unnecessary if you are using W3 Total Cache Plugin minification', 'wowmall' ),
				'default'  => false,
			),
			array(
				'id'      => 'remove_emoji',
				'type'    => 'switch',
				'title'   => esc_html__( 'Remove WordPress emoji', 'wowmall' ),
				'default' => false,
			),
			array(
				'id'      => 'page_title',
				'type'    => 'switch',
				'title'   => esc_html__( 'Page Titles', 'wowmall' ),
				'default' => true,
			),
		),
	),
	array(
		'title'            => esc_html__( 'Color Settings', 'wowmall' ),
		'id'               => 'color_settings',
		'customizer_width' => '400px',
		'icon'             => 'el el-adjust',
		'fields'           => array(
			array(
				'id'          => 'accent_color_1',
				'type'        => 'color',
				'title'       => esc_html__( 'Accent color (1)', 'wowmall' ),
				'default'     => '#fc6f38',
				'validate'    => 'color',
				'transparent' => false,
				'output'      => array(
					'outline-color'     => 'body.focus-styles :focus,body.focus-styles .ui-state-focus',
					'color'             => $colors['1']['color'],
					'background-color'  => $colors['1']['background-color'],
					'background'        => $colors['1']['background'],
					'border-color'      => $colors['1']['border-color'],
					'border-left-color' => $colors['1']['border-left-color'],
				),
			),
			array(
				'id'          => 'accent_color_2',
				'type'        => 'color',
				'title'       => esc_html__( 'Accent color (2)', 'wowmall' ),
				'default'     => '#222',
				'validate'    => 'color',
				'transparent' => false,
				'output'      => array(
					'color'            => $colors['2']['color'],
					'background-color' => $colors['2']['background-color'],
					'outline-color'    => '.btn-border:hover,.btn-border:focus,.page-numbers a:hover,.page-numbers a:focus,.tablesaw-bar .tablesaw-advance a.tablesaw-nav-btn:hover',
					'border-color'     => $colors['2']['border-color'],
				),
			),
			array(
				'id'      => 'link_color',
				'type'    => 'link_color',
				'title'   => esc_html__( 'Link color', 'wowmall' ),
				'visited' => false,
				'active'  => false,
				'default' => array(
					'regular' => '#fc6f38',
					'hover'   => '#888',
				),
				'output'  => array(
					'link_color' => 'a',
				),
			),
		),
	),
	array(
		'title'            => esc_html__( 'Header Settings', 'wowmall' ),
		'id'               => 'header',
		'desc'             => esc_html__( 'All header settings', 'wowmall' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-website',
		'fields'           => array(
			array(
				'id'       => 'favicon',
				'type'     => 'media',
				'url'      => true,
				'title'    => esc_html__( 'Favicon URL', 'wowmall' ),
				'subtitle' => esc_html__( 'Upload favicon for the theme', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/favicon.ico',
				),
			),
			array(
				'id'       => 'header_logo',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload logo using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Site Logo', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo.png',
				),
			),
			array(
				'id'       => 'header_logo_2x',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload retina 2x logo using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Site Retina 2x Logo', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_2x.png',
				),
			),
			array(
				'id'       => 'header_logo_3x',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload retina 3x logo using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Site Retina 3x Logo', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_3x.png',
				),
			),
			array(
				'id'       => 'header_logo_alt',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload alternative logo using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Site Alternative Logo', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo-2.png',
				),
			),
			array(
				'id'       => 'header_logo_alt_2x',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload alternative retina 2x logo using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Site Alternative Retina 2x Logo', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo-2_2x.png',
				),
			),
			array(
				'id'       => 'header_logo_alt_3x',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload alternative retina 3x logo using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Site Alternative Retina 3x Logo', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo-2_3x.png',
				),
			),
			array(
				'id'       => 'header_logo_mobile',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload logo for mobile devices using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Site Logo for Mobile', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_mobile.png',
				),
			),
			array(
				'id'       => 'header_logo_mobile_2x',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload retina 2x logo for mobile devices using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Site Retina 2x Logo for Mobile', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_mobile_2x.png',
				),
			),
			array(
				'id'       => 'header_logo_mobile_3x',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload retina 3x logo for mobile devices using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Site Retina 3x Logo for Mobile', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_mobile_3x.png',
				),
			),
			array(
				'id'      => 'header_logo_tag',
				'type'    => 'select',
				'title'   => esc_html__( 'Header Logo Tag on the Front Page', 'wowmall' ),
				'default' => 'h1',
				'options' => array(
					'h1'  => 'h1',
					'div' => 'div',
				),
			),
			array(
				'id'       => 'header_layout',
				'type'     => 'image_select',
				'title'    => esc_html__( 'Site Header', 'wowmall' ),
				'subtitle' => esc_html__( 'Select which type of header you want to show', 'wowmall' ),
				'default'  => '1',
				'options'  => array(
					'1' => array(
						'alt' => 'Header One',
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/header-layout-1.jpg',
					),
					'2' => array(
						'alt' => 'Header two',
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/header-layout-2.jpg',
					),
					'3' => array(
						'alt' => 'Header three',
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/header-layout-3.jpg',
					),
					'4' => array(
						'alt' => 'Header four',
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/header-layout-4.jpg',
					),
					'5' => array(
						'alt' => 'Header five',
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/header-layout-5.jpg',
					),
					'6' => array(
						'alt' => 'Header six',
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/header-layout-6.jpg',
					),
				),
			),
			array(
				'id'       => 'header_prod_compare_enable',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display Compare on header', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable Compare on header', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'      => 'header_prod_compare_page',
				'type'    => 'select',
				'title'   => esc_html__( 'Compare Page', 'wowmall' ),
				'data'    => 'pages',
				'default' => '696',
			),
			array(
				'id'       => 'header_prod_wishlist_enable',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display Wishlist on header', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable Wishlist on header', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'      => 'header_prod_wishlist_page',
				'type'    => 'select',
				'title'   => esc_html__( 'Wishlist Page', 'wowmall' ),
				'data'    => 'pages',
				'default' => '697',
			),
			array(
				'id'       => 'header_currency_enable',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display currency dropdown on header', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable currency dropdown on header', 'wowmall' ),
				'default'  => false,
			),
			array(
				'id'       => 'header_cart_enable',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display cart dropdown on header', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable cart dropdown on header', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'header_orders_enable',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display orders link on header', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable orders link on header', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'header_account_enable',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display account link on header', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable account link on header', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'header_search_enable',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display search bar on header', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable search on header', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'      => 'header_search_ajax',
				'type'    => 'switch',
				'title'   => esc_html__( 'Ajax Search Results', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'ajax_search_min_length',
				'type'    => 'slider',
				'title'   => esc_html__( 'Header search phrase minimum length, in symbols', 'wowmall' ),
				"default" => 3,
				"min"     => 1,
				"step"    => 1,
				"max"     => 10,
			),
			array(
				'id'       => 'header_sticky_enable',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display sticky header', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable sticky header', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'header_sticky_background',
				'type'     => 'background',
				'title'    => esc_html__( 'Sticky Header Background', 'wowmall' ),
				'subtitle' => esc_html__( 'Sticky Header background with image, color, etc.', 'wowmall' ),
				'default'  => array(
					'background-color' => '#fff',
				),
				'output'   => array( '.header-sticky-wrapper' ),
			),
			array(
				'id'      => 'header_sticky_color',
				'type'    => 'link_color',
				'title'   => esc_html__( 'Sticky Header Color', 'wowmall' ),
				'visited' => false,
				'default' => array(
					'regular' => '#888',
					'hover'   => '#222',
					'active'  => '#222',
				),
				'output'  => array(
					'link_color' => '.header-sticky-wrapper #primary-menu>li>a',
				),
			),
			array(
				'id'      => 'header_mobile_background',
				'type'    => 'background',
				'title'   => esc_html__( 'Mobile Header Background', 'wowmall' ),
				'default' => array(
					'background-color' => '#222',
				),
				'output'  => array( '.header-layout-mobile,#mobile-menu-close' ),
			),
			array(
				'id'          => 'header_mobile_color',
				'type'        => 'color',
				'title'       => esc_html__( 'Mobile Header Color', 'wowmall' ),
				'default'     => '#fff',
				'validate'    => 'color',
				'transparent' => false,
				'output'      => array(
					'color' => '.header-layout-mobile #mobile-menu-open,.header-layout-mobile .header-cart-wrapper a.cart-contents,.header-layout-mobile #mobile-menu-close',
				),
			),
		),
	),
	array(
		'title'      => 'Top Panel Settings',
		'id'         => 'top_panel_settings',
		'subsection' => true,
		'desc'       => '',
		'fields'     => array(
			array(
				'id'       => 'top_panel_background',
				'type'     => 'background',
				'output'   => array( '#top-panel,body:not(.home) .header-layout-5' ),
				'title'    => esc_html__( 'Top Panel Background', 'wowmall' ),
				'subtitle' => esc_html__( 'Top Panel background with image, color, etc.', 'wowmall' ),
				'default'  => array(
					'background-color' => '#222',
				),
			),
			array(
				'id'       => 'header_text',
				'type'     => 'editor',
				'title'    => esc_html__( 'Header Text', 'wowmall' ),
				'subtitle' => esc_html__( 'It will show at top.', 'wowmall' ),
				'default'  => __( '<ul>
	    <li><span class="myfont-location-1"></span>Address: 7563 St. Vicent Place, Glasgow</li>
	    <li><span class="myfont-phone"></span>Phone: +777 2345 7885:&nbsp; +777 2345 7886</li>
	    <li><span class="myfont-clock"></span>Hours: 7 Days a week from 10-00 am to 6-00 pm</li>
	</ul>', 'wowmall' ),
			),
			array(
				'id'       => 'header_cart_text',
				'type'     => 'text',
				'title'    => esc_html__( 'Header Cart Text', 'wowmall' ),
				'subtitle' => esc_html__( 'It will show at bottom of header cart.', 'wowmall' ),
				'default'  => esc_html__( 'Free Shipping on All Orders over $99!', 'wowmall' ),
			),
			array(
				'id'          => 'top_panel_color',
				'type'        => 'color',
				'title'       => esc_html__( 'Top Panel Text Color', 'wowmall' ),
				'default'     => '#b4b4b4',
				'validate'    => 'color',
				'transparent' => false,
				'output'      => array(
					'color' => '.top-panel',
				),
			),
			array(
				'id'          => 'top_panel_btns_color',
				'type'        => 'link_color',
				'title'       => esc_html__( 'Top Panel Buttons and Icons Color', 'wowmall' ),
				'validate'    => 'color',
				'transparent' => false,
				'visited'     => false,
				'default'     => $new_tools_color,
				'output'      => array(
					'link_color' => '.header-tools-wrapper .header-tools > a,.header-tools-wrapper .header-tools .header-cart-wrapper > a,.header-tools-wrapper .header-tools .header-currency-wrapper > a,.header-layout-5 .wowmall-top-search-wrapper .wowmall-top-search button[type=submit],.header-layout-5 .wowmall-top-search-wrapper .wowmall-top-search button.search-close,.header-layout-5 #primary-menu>li>a',
				),
			),
		),
	),
	array(
		'title'            => esc_html__( 'Breadcrumbs Settings', 'wowmall' ),
		'id'               => 'breadcrumb',
		'desc'             => esc_html__( 'All breadcrumbs settings', 'wowmall' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-compass',
		'fields'           => array(
			array(
				'id'       => 'breadcrumbs',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display breadcrumbs', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable breadcrumbs', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'      => 'breadcrumb_bg',
				'type'    => 'background',
				'title'   => esc_html__( 'Breadcrumbs Background', 'wowmall' ),
				'default' => array(
					'background-color' => '#f7f7f7',
				),
				'output'  => array(
					'.site-breadcrumb,.wc-loop-sorting-wrapper,.woocommerce-ordering .ui-selectmenu-menu',
				),
			),
		),
	),
	array(
		'title'            => esc_html__( 'Blog Settings', 'wowmall' ),
		'id'               => 'blog_settings',
		'customizer_width' => '400px',
		'icon'             => 'el el-th-large',
		'fields'           => array(
			array(
				'id'       => 'blog_layout_type',
				'type'     => 'image_select',
				'title'    => esc_html__( 'Blog Layout Type', 'wowmall' ),
				'subtitle' => esc_html__( 'Blog Layout Type. List, Grid or Masonry', 'wowmall' ),
				'default'  => 'list',
				'options'  => array(
					'list'    => array(
						'alt' => esc_html__( 'List', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/blog-layout-1.jpg',
					),
					'grid'    => array(
						'alt' => esc_html__( 'Grid', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/blog-layout-2.jpg',
					),
					'masonry' => array(
						'alt' => esc_html__( 'Masonry', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/blog-layout-3.jpg',
					),
				),
			),
			array(
				'id'       => 'blog_meta_tags',
				'type'     => 'switch',
				'title'    => esc_html__( 'Blog Tags', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable tags in posts', 'wowmall' ),
				'default'  => false,
			),
			array(
				'id'       => 'blog_readmore',
				'type'     => 'switch',
				'title'    => esc_html__( 'Blog Read More Button', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable Read More button in posts', 'wowmall' ),
				'default'  => false,
			),
			array(
				'id'      => 'blog_readmore_label',
				'type'    => 'text',
				'title'   => esc_html__( 'Blog Read More Button Text', 'wowmall' ),
				'default' => esc_html__( 'Read more', 'wowmall' ),
			),
			array(
				'id'       => 'blog_share',
				'type'     => 'switch',
				'title'    => esc_html__( 'Blog Social Share Buttons', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable Social Share buttons in posts', 'wowmall' ),
				'default'  => false,
			),
			array(
				'id'       => 'blog_sidebar_single',
				'type'     => 'switch',
				'title'    => esc_html__( 'Single Post Sidebar', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable Single Post Sidebar', 'wowmall' ),
				'default'  => true,
			),
		),
	),
	array(
		'title'      => 'Blog Media Settings',
		'id'         => 'blog_media_settings',
		'subsection' => true,
		'desc'       => '',
		'fields'     => array(
			array(
				'id'       => 'blog_img_size_small',
				'type'     => 'dimensions',
				'title'    => esc_html__( 'Blog Image Small Size', 'wowmall' ),
				'subtitle' => esc_html__( 'Images for Posts Links Nav, etc.', 'wowmall' ),
				'units'    => false,
				'default'  => array(
					'width'  => '105',
					'height' => '84',
				),
			),
			array(
				'id'      => 'blog_img_size_related',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Blog Image Related Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '548',
					'height' => '440',
				),
			),
			array(
				'id'      => 'blog_img_size_grid',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Blog Image Grid Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '401',
					'height' => '322',
				),
			),
			array(
				'id'      => 'blog_img_size_masonry',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Blog Image Masonry Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '401',
					'height' => '9999',
				),
			),
			array(
				'id'      => 'blog_img_size_list',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Blog Image List Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '1430',
					'height' => '636',
				),
			),
			array(
				'id'      => 'blog_img_size_single',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Blog Image Single Post Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '1920',
					'height' => '853',
				),
			),
		),
	),
	array(
		'title'            => esc_html__( 'Gallery Settings', 'wowmall' ),
		'id'               => 'gallery_settings',
		'customizer_width' => '400px',
		'icon'             => 'el el-th-large',
		'fields'           => array(
			array(
				'id'       => 'gallery_layout_type',
				'type'     => 'image_select',
				'title'    => esc_html__( 'Gallery Layout Type', 'wowmall' ),
				'subtitle' => esc_html__( 'Gallery Layout Type. Grid or Masonry', 'wowmall' ),
				'default'  => 'masonry',
				'options'  => array(
					'grid'    => array(
						'alt' => esc_html__( 'Grid', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/gallery-layout-1.jpg',
					),
					'masonry' => array(
						'alt' => esc_html__( 'Masonry', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/gallery-layout-2.jpg',
					),
				),
			),
			array(
				'id'      => 'gallery_columns',
				'type'    => 'image_select',
				'title'   => esc_html__( 'Gallery Columns', 'wowmall' ),
				'default' => '4',
				'options' => array(
					'3' => array(
						'alt' => sprintf( esc_html__( '%s Columns', 'wowmall' ), 3 ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/gallery-layout-1.jpg',
					),
					'4' => array(
						'alt' => sprintf( esc_html__( '%s Columns', 'wowmall' ), 4 ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/gallery-layout-3.jpg',
					),
				),
			),
			array(
				'id'      => 'gallery_display_type',
				'type'    => 'select',
				'title'   => esc_html__( 'Gallery Display Type', 'wowmall' ),
				'default' => 'both',
				'options' => array(
					'both'          => esc_html__( 'Images & Subcategories', 'wowmall' ),
					'images'        => esc_html__( 'Images', 'wowmall' ),
					'subcategories' => esc_html__( 'Subcategories', 'wowmall' ),
				),
			),
			array(
				'id'       => 'gallery_orderby',
				'type'     => 'select',
				'title'    => esc_html__( 'Order By', 'wowmall' ),
				'default'  => 'menu_order',
				'options'  => array(
					'menu_order' => esc_html__( 'Menu Oreder', 'wowmall' ),
					'date'       => esc_html__( 'Date', 'wowmall' ),
					'ID'         => esc_html__( 'ID', 'wowmall' ),
					'title'      => esc_html__( 'Title', 'wowmall' ),
				),
				'subtitle' => esc_html__( 'Select order type.', 'wowmall' ),
			),
			array(
				'id'       => 'gallery_order',
				'type'     => 'select',
				'title'    => esc_html__( 'Sort Order', 'wowmall' ),
				'default'  => 'ASC',
				'options'  => array(
					'ASC'  => esc_html__( 'Ascending', 'wowmall' ),
					'DESC' => esc_html__( 'Descending', 'wowmall' ),
				),
				'subtitle' => esc_html__( 'Select sorting order.', 'wowmall' ),
			),
			array(
				'id'          => 'gallery_posts_per_page',
				'type'        => 'text',
				'title'       => esc_html__( 'Posts per Page', 'wowmall' ),
				'default'     => '',
				'description' => esc_html__( 'Leave blank to use default value. To show all posts insert "-1"', 'wowmall' ),
			),
		),
	),
	array(
		'title'            => esc_html__( 'Gallery Media Settings', 'wowmall' ),
		'id'               => 'gallery_media_settings',
		'customizer_width' => '400px',
		'icon'             => 'el el-th-large',
		'subsection'       => true,
		'fields'           => array(
			array(
				'id'      => 'gallery_img_size_grid_small',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Gallery Image Grid Small Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '480',
					'height' => '480',
				),
			),
			array(
				'id'      => 'gallery_img_size_grid_medium',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Gallery Image Grid Medium Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '640',
					'height' => '640',
				),
			),
			array(
				'id'      => 'gallery_img_size_masonry_small',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Gallery Image Masonry Small Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '480',
					'height' => '9999',
				),
			),
			array(
				'id'      => 'gallery_img_size_masonry_medium',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Gallery Image Masonry Medium Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '640',
					'height' => '9999',
				),
			),
			array(
				'id'      => 'gallery_img_size_lightbox_thumb',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Gallery Lightbox Thumbs Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '9999',
					'height' => '92',
				),
			),
		),
	),
	array(
		'title'            => esc_html__( 'Typography', 'wowmall' ),
		'id'               => 'typography',
		'desc'             => esc_html__( 'Theme all font options', 'wowmall' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-font',
		'fields'           => array(
			array(
				'id'         => 'body_typography',
				'type'       => 'typography',
				'title'      => esc_html__( 'Body Typography', 'wowmall' ),
				'subtitle'   => esc_html__( 'Select body font family, size, line height, color and weight.', 'wowmall' ),
				'output'     => array(
					'html,button,input[type="button"],input[type="reset"],input[type="submit"],.btn,.mini-cart-posttext,input[type="text"],input[type="email"],input[type="url"],input[type="tel"],input[type="password"],input[type="search"],input[type="date"],input[type="number"],textarea',
				),
				'all_styles' => true,
				'default'    => array(
					'color'       => '#888',
					'font-weight' => '400',
					'font-family' => 'PT Sans Narrow',
					'google'      => true,
					'font-size'   => '18px',
					'line-height' => '26px',
					'subsets'     => 'latin',
					'text-align'  => 'left',
				),
			),
			array(
				'id'             => 'h1',
				'type'           => 'typography',
				'title'          => esc_html__( 'H1', 'wowmall' ),
				'subtitle'       => esc_html__( 'H1 typography settings', 'wowmall' ),
				'text-align'     => false,
				'letter-spacing' => true,
				'default'        => array(
					'color'          => '#222',
					'font-weight'    => '700',
					'font-family'    => 'PT Sans Narrow',
					'google'         => true,
					'font-size'      => '60px',
					'line-height'    => '86px',
					'subsets'        => 'latin',
					'letter-spacing' => '3.6px',
				),
				'output'         => array( 'h1' ),
			),
			array(
				'id'             => 'h2',
				'type'           => 'typography',
				'title'          => esc_html__( 'H2', 'wowmall' ),
				'subtitle'       => esc_html__( 'H2 typography settings', 'wowmall' ),
				'text-align'     => false,
				'letter-spacing' => true,
				'default'        => array(
					'font-weight'    => '700',
					'font-family'    => 'PT Sans Narrow',
					'google'         => true,
					'font-size'      => '40px',
					'line-height'    => '58px',
					'color'          => '#222',
					'subsets'        => 'latin',
					'letter-spacing' => '2.34px',
				),
				'output'         => array( 'h2,h1.page-title' ),
			),
			array(
				'id'             => 'h3',
				'type'           => 'typography',
				'title'          => esc_html__( 'H3', 'wowmall' ),
				'subtitle'       => esc_html__( 'H3 typography settings', 'wowmall' ),
				'text-align'     => false,
				'letter-spacing' => true,
				'default'        => array(
					'font-weight'    => '700',
					'font-family'    => 'PT Sans Narrow',
					'google'         => true,
					'font-size'      => '26px',
					'line-height'    => '37px',
					'color'          => '#222',
					'subsets'        => 'latin',
					'letter-spacing' => '1.44px',
				),
				'output'         => array(
					'h3,.woocommerce ul.products li.product h3,h1.product_title,ul[class=products] > .product-list h2.wc-loop-product-title',
				),
			),
			array(
				'id'             => 'h4',
				'type'           => 'typography',
				'title'          => esc_html__( 'H4', 'wowmall' ),
				'subtitle'       => esc_html__( 'H4 typography settings', 'wowmall' ),
				'text-align'     => false,
				'letter-spacing' => true,
				'default'        => array(
					'font-weight'    => '700',
					'font-family'    => 'PT Sans Narrow',
					'google'         => true,
					'font-size'      => '18px',
					'line-height'    => '26px',
					'color'          => '#222',
					'subsets'        => 'latin',
					'letter-spacing' => '1.08px',
				),
				'output'         => array( 'h4,.widgettitle,.aio-icon-title' ),
			),
			array(
				'id'             => 'h5',
				'type'           => 'typography',
				'title'          => esc_html__( 'H5', 'wowmall' ),
				'subtitle'       => esc_html__( 'H5 typography settings', 'wowmall' ),
				'text-align'     => false,
				'output'         => array( 'h5' ),
				'letter-spacing' => true,
				'default'        => array(
					'font-weight'    => '700',
					'font-family'    => 'PT Sans Narrow',
					'google'         => true,
					'font-size'      => '16px',
					'line-height'    => '23px',
					'color'          => '#222',
					'subsets'        => 'latin',
					'letter-spacing' => '0.9px',
				),
			),
			array(
				'id'             => 'h6',
				'type'           => 'typography',
				'title'          => esc_html__( 'H6', 'wowmall' ),
				'subtitle'       => esc_html__( 'H6 typography settings', 'wowmall' ),
				'text-align'     => false,
				'letter-spacing' => true,
				'default'        => array(
					'font-weight'    => '400',
					'font-family'    => 'PT Sans Narrow',
					'google'         => true,
					'font-size'      => '18px',
					'line-height'    => '26px',
					'color'          => '#222',
					'subsets'        => 'latin',
					'letter-spacing' => '0px',
				),
				'output'         => array( 'h6,h2.wc-loop-product-title' ),
			),
		),
	),
	array(
		'title'            => esc_html__( 'WooCommerce Settings', 'wowmall' ),
		'id'               => 'wc_settings',
		'customizer_width' => '400px',
		'icon'             => 'el el-shopping-cart-sign',
		'fields'           => array(
			array(
				'id'      => 'custom_variations_color',
				'type'    => 'switch',
				'title'   => esc_html__( 'Color Attribute Functionality', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'custom_variations_size',
				'type'    => 'switch',
				'title'   => esc_html__( 'Size Attribute Functionality', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'shop_page_title',
				'type'    => 'switch',
				'title'   => esc_html__( 'Shop Page Title', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'colors',
				'type'    => 'switch',
				'title'   => esc_html__( 'Color Variations in the Loop', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'review_count',
				'type'    => 'switch',
				'title'   => esc_html__( 'Review count in the Loop Grid Mode', 'wowmall' ),
				'default' => false,
			),
			array(
				'id'      => 'shop_pagination',
				'type'    => 'button_set',
				'title'   => esc_html__( 'Pagination Variant', 'wowmall' ),
				'default' => '',
				'options' => array(
					''          => esc_html__( 'Pagination', 'wowmall' ),
					'load_more' => esc_html__( 'Load More Button', 'wowmall' ),
					'infinite'  => esc_html__( 'Infinite scroll', 'wowmall' ),
				),
			),
			array(
				'id'       => 'posts_per_page_sidebar',
				'type'     => 'text',
				'title'    => esc_html__( 'Posts per page for Listing with Sidebar', 'wowmall' ),
				'validate' => 'numeric',
				'default'  => '15',
			),
			array(
				'id'       => 'posts_per_page_no_sidebar',
				'type'     => 'text',
				'title'    => esc_html__( 'Posts per page for Listing without Sidebar', 'wowmall' ),
				'validate' => 'numeric',
				'default'  => '18',
			),
			array(
				'id'       => 'posts_per_page_average',
				'type'     => 'text',
				'title'    => esc_html__( 'Posts per page for Listing Average Previews', 'wowmall' ),
				'validate' => 'numeric',
				'default'  => '8',
			),
			array(
				'id'       => 'posts_per_page_big',
				'type'     => 'text',
				'title'    => esc_html__( 'Posts per page for Listing Average Previews', 'wowmall' ),
				'validate' => 'numeric',
				'default'  => '6',
			),
			array(
				'id'       => 'wc_shop_layout',
				'type'     => 'image_select',
				'title'    => esc_html__( 'Shop page layout', 'wowmall' ),
				'subtitle' => esc_html__( 'Set shop page layout', 'wowmall' ),
				'default'  => '1',
				'options'  => array(
					'1' => array(
						'alt' => esc_html__( 'Standard', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/shop-layout-1.jpg',
					),
					'2' => array(
						'alt' => esc_html__( 'Categories Collection', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/shop-layout-2.jpg',
					),
				),
			),
			array(
				'id'       => 'wc_loop_layout',
				'type'     => 'image_select',
				'title'    => esc_html__( 'Product grid columns for loop', 'wowmall' ),
				'subtitle' => esc_html__( 'Set Product grid columns for loop', 'wowmall' ),
				'default'  => '1',
				'options'  => array(
					'1' => array(
						'alt' => esc_html__( 'Listing with Sidebar', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/shop-layout-1.jpg',
					),
					'2' => array(
						'alt' => esc_html__( 'Listing Catalogue', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/loop-layout-2.jpg',
					),
					'3' => array(
						'alt' => esc_html__( 'Listing without Columns', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/loop-layout-3.jpg',
					),
					'4' => array(
						'alt' => esc_html__( 'Listing Average Previews', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/loop-layout-4.jpg',
					),
					'5' => array(
						'alt' => esc_html__( 'Listing Big Previews', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/loop-layout-5.jpg',
					),
				),
			),
			array(
				'id'       => 'wc_loop_cat_layout',
				'type'     => 'image_select',
				'title'    => esc_html__( 'Layout for shop category page', 'wowmall' ),
				'subtitle' => esc_html__( 'Set layout for shop category page', 'wowmall' ),
				'default'  => '1',
				'options'  => array(
					'1' => array(
						'alt' => esc_html__( 'Standard', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/cat-page-1.jpg',
					),
					'2' => array(
						'alt' => esc_html__( 'With category banner', 'wowmall' ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/cat-page-2.jpg',
					),
				),
			),
			array(
				'id'      => 'cat_banner_height',
				'type'    => 'slider',
				'title'   => esc_html__( "Category Banner Min Height, in px", 'wowmall' ),
				'default' => 580,
				'min'     => 200,
				'step'    => 10,
				'max'     => 800,
			),
			array(
				'id'      => 'cat_banner_over_background',
				'type'    => 'color_rgba',
				'title'   => esc_html__( 'Category Banner Overlay', 'wowmall' ),
				'default' => array(
					'color' => '#f2f1f6',
					'alpha' => .7,
				),
				'output'  => array(
					'background-color' => '.term-description .term-description-col:before, .term-description .term-description-col',
				),
			),
			array(
				'id'          => 'cat_banner_color',
				'type'        => 'palette',
				'title'       => esc_html__( 'Category Banner Color', 'wowmall' ),
				'default'     => 'dark',
				'transparent' => false,
				'palettes'    => array(
					'dark'  => array(
						$color_2,
						$body_color,
					),
					'light' => array(
						'#fff',
					),
				),
			),
			array(
				'id'       => 'wc_loop_thumb_swiper',
				'type'     => 'switch',
				'title'    => esc_html__( 'Loop Thumbnails Hover', 'wowmall' ),
				'subtitle' => esc_html__( 'Display second thumbnail on hover', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'      => 'wc_loop_thumb_zoom',
				'type'    => 'switch',
				'title'   => esc_html__( 'Loop Thumbnails Hover Zoom', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'wc_loop_thumb_effect',
				'type'    => 'button_set',
				'title'   => esc_html__( 'Loop Thumb Effect', 'wowmall' ),
				'default' => 'fade',
				'options' => array(
					'fade'  => esc_html__( 'Fade', 'wowmall' ),
					'slide' => esc_html__( 'Slide', 'wowmall' ),
					'cube'  => esc_html__( 'Cube', 'wowmall' ),
					'flip'  => esc_html__( 'Flip', 'wowmall' ),
				),
			),
			array(
				'id'      => 'compare_enable',
				'type'    => 'switch',
				'title'   => esc_html__( 'Compare', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'wishlist_enable',
				'type'    => 'switch',
				'title'   => esc_html__( 'Wishlist', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'quick_view_enable',
				'type'    => 'switch',
				'title'   => esc_html__( 'Quick View', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'wc_quick_view_thumb_effect',
				'type'    => 'button_set',
				'title'   => esc_html__( 'Quick View Thumb Effect', 'wowmall' ),
				'default' => 'slide',
				'options' => array(
					'fade'  => esc_html__( 'Fade', 'wowmall' ),
					'slide' => esc_html__( 'Slide', 'wowmall' ),
					'cube'  => esc_html__( 'Cube', 'wowmall' ),
					'flip'  => esc_html__( 'Flip', 'wowmall' ),
				),
			),
		),
	),
	array(
		'title'      => 'WooCommerce Media Settings',
		'id'         => 'woo_media_settings',
		'subsection' => true,
		'desc'       => '',
		'fields'     => array(
			array(
				'id'      => 'woo_img_size_single_thumb',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Single Product Thumb Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '65',
					'height' => '76',
				),
			),
			array(
				'id'      => 'woo_img_size_minicart',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Mini-cart Thumb Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '81',
					'height' => '94',
				),
			),
			array(
				'id'      => 'woo_img_size_cart',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Cart Thumb Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '138',
					'height' => '160',
				),
			),
			array(
				'id'      => 'woo_img_size_small',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Catalog Image Small Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '254',
					'height' => '295',
				),
			),
			array(
				'id'      => 'woo_img_size_average',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Catalog Image Average Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '327',
					'height' => '380',
				),
			),
			array(
				'id'      => 'woo_img_size_big',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Catalog Image Big Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '450',
					'height' => '523',
				),
			),
			array(
				'id'      => 'woo_img_size_list',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Catalog Image List Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '548',
					'height' => '636',
				),
			),
			array(
				'id'      => 'woo_img_size_single_1',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Single Product v1 Image Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '747',
					'height' => '869',
				),
			),
			array(
				'id'      => 'woo_img_size_single_2',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Single Product v2 Image Size', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '830',
					'height' => '966',
				),
			),
			array(
				'id'      => 'woo_img_size_collection_0',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Collection Image Size 1x1', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '401',
					'height' => '401',
				),
			),
			array(
				'id'      => 'woo_img_size_collection_1',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Collection Image Size 1x2', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '401',
					'height' => '842',
				),
			),
			array(
				'id'      => 'woo_img_size_collection_2',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Collection Image Size 2x1', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '842',
					'height' => '401',
				),
			),
			array(
				'id'      => 'woo_img_size_collection_3',
				'type'    => 'dimensions',
				'title'   => esc_html__( 'Collection Image Size 2x2', 'wowmall' ),
				'units'   => false,
				'default' => array(
					'width'  => '842',
					'height' => '842',
				),
			),
		),
	),
	'woo_single_settings' => array(
		'title'      => 'WooCommerce Single Product Settings',
		'id'         => 'woo_single_settings',
		'subsection' => true,
		'desc'       => '',
		'fields'     => array(
			array(
				'id'       => 'instock_label',
				'type'     => 'switch',
				'title'    => esc_html__( 'In Stock Label', 'wowmall' ),
				'subtitle' => esc_html__( 'Always display Instock label', 'wowmall' ),
				'default'  => false,
			),
			array(
				'id'       => 'sku',
				'type'     => 'switch',
				'title'    => esc_html__( 'SKU Label', 'wowmall' ),
				'subtitle' => esc_html__( 'Display SKU label if available', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'product_page_layout',
				'type'     => 'image_select',
				'title'    => esc_html__( 'Product Page Layout', 'wowmall' ),
				'subtitle' => esc_html__( 'Select product page layout', 'wowmall' ),
				'default'  => '2',
				'height'   => '150',
				'options'  => array(
					'1' => array(
						'alt' => 'Layout 1',
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/product-page-1.jpg',
					),
					'2' => array(
						'alt' => 'Layout 2',
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/product-page-2.jpg',
					),
				),
			),
			array(
				'id'       => 'wc_single_swiper_effect',
				'type'     => 'button_set',
				'title'    => esc_html__( 'Swiper Effect', 'wowmall' ),
				'subtitle' => esc_html__( 'For Page Layout 1', 'wowmall' ),
				'default'  => 'slide',
				'options'  => array(
					'fade'  => esc_html__( 'Fade', 'wowmall' ),
					'slide' => esc_html__( 'Slide', 'wowmall' ),
					'cube'  => esc_html__( 'Cube', 'wowmall' ),
					'flip'  => esc_html__( 'Flip', 'wowmall' ),
				),
			),
			array(
				'id'      => 'wc_excerpt_mobile',
				'type'    => 'switch',
				'title'   => esc_html__( 'Short Description on Mobile', 'wowmall' ),
				'default' => false,
			),
			array(
				'id'       => 'is_product_share_enable',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display Product Share', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable Product Share on product page', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'product_lightbox',
				'type'     => 'switch',
				'title'    => esc_html__( 'Lightbox', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable Lightbox on product images', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'product_zoom',
				'type'     => 'switch',
				'title'    => esc_html__( 'Zoom', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable Zoom on product images', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'product_mobile_arrows',
				'type'     => 'switch',
				'title'    => esc_html__( 'Gallery Arrows on Mobile Devices', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable gallery arrows on mobile devices', 'wowmall' ),
				'default'  => false,
			),
			array(
				'id'       => 'product_stiky',
				'type'     => 'switch',
				'title'    => esc_html__( 'Sticky Right Column', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable sticky right column', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'product_scroll_to_tab',
				'type'     => 'switch',
				'title'    => esc_html__( 'Scroll to Opened Tab', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable scroll to opened tab', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'      => 'desc_tab_opened',
				'type'    => 'switch',
				'title'   => esc_html__( 'Opened Description Tab', 'wowmall' ),
				'default' => false,
			),
			array(
				'id'      => 'related_arrows',
				'type'    => 'switch',
				'title'   => esc_html__( 'Arrows for Related Products, Up-Sells, Cross-Sells', 'wowmall' ),
				'default' => false,
			),
			array(
				'id'      => 'related_hide_outofstock',
				'type'    => 'switch',
				'title'   => esc_html__( 'Hide out of stock items on Related Products', 'wowmall' ),
				'default' => false,
			),
			/*array(
				'id'       => 'related',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show Related Products', 'wowmall' ),
				'default'  => true,
			),*/
		),
	),
	array(
		'title'      => 'WooCommerce Search Settings',
		'id'         => 'woo_search_settings',
		'subsection' => true,
		'desc'       => '',
		'fields'     => array(
			array(
				'id'      => 'product_search_in_tags',
				'type'    => 'switch',
				'title'   => esc_html__( 'Search in Tags', 'wowmall' ),
				'default' => true,
			),
			array(
				'id'      => 'product_search_in_sku',
				'type'    => 'switch',
				'title'   => esc_html__( 'Search in SKU', 'wowmall' ),
				'default' => true,
			),
		),
	),
	array(
		'title'            => esc_html__( 'Popup Settings', 'wowmall' ),
		'id'               => 'popup_settings',
		'desc'             => esc_html__( 'Popup for email subscription', 'wowmall' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-website-alt',
		'fields'           => array(
			array(
				'id'       => 'popup',
				'type'     => 'switch',
				'title'    => esc_html__( 'Popup', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable popup on site', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'      => 'popup_pretext',
				'type'    => 'editor',
				'title'   => esc_html__( 'Popup Pretext', 'wowmall' ),
				'default' => __( '<h2>BE THE FIRST<br>TO KNOW.</h2><p class="discount">15% off</p><h4>your next purchase when you sign up.</h4><p>By signing up, you accept the <a href="#">terms & Privacy Policy</a></p>', 'wowmall' ),
			),
			array(
				'id'      => 'popup_form',
				'type'    => 'select',
				'title'   => esc_html__( 'Popup subscribe form', 'wowmall' ),
				'options' => wowmall()->newsletter_forms(),
			),
			array(
				'id'       => 'popup_check_is_subscribed',
				'type'     => 'switch',
				'title'    => esc_html__( 'Check if user is subscribed', 'wowmall' ),
				'subtitle' => esc_html__( "Don't show popup if user is subscribed", 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'      => 'popup_dont_show_again',
				'type'    => 'switch',
				'title'   => esc_html__( "Don't show again button", 'wowmall' ),
				'default' => true,
			),
			array(
				'id'       => 'popup_delay',
				'type'     => 'slider',
				'title'    => esc_html__( "Delay, in ms", 'wowmall' ),
				'subtitle' => esc_html__( "Delay between page loaded and popup shown", 'wowmall' ),
				"default"  => 2000,
				"min"      => 0,
				"step"     => 100,
				"max"      => 5000,
			),
		),
	),
	array(
		'title'            => esc_html__( 'Footer Settings', 'wowmall' ),
		'id'               => 'footer_settings',
		'customizer_width' => '400px',
		'icon'             => 'el el-share',
		'fields'           => array(
			array(
				'id'      => 'footer_layout',
				'type'    => 'image_select',
				'title'   => esc_html__( 'Footer Layout', 'wowmall' ),
				'default' => '1',
				'options' => array(
					'1' => array(
						'alt' => sprintf( esc_html__( 'Layout %s', 'wowmall' ), 1 ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/footer-layout-1.jpg',
					),
					'2' => array(
						'alt' => sprintf( esc_html__( 'Layout %s', 'wowmall' ), 2 ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/footer-layout-2.jpg',
					),
					'3' => array(
						'alt' => sprintf( esc_html__( 'Layout %s', 'wowmall' ), 3 ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/footer-layout-3.jpg',
					),
					'4' => array(
						'alt' => sprintf( esc_html__( 'Layout %s', 'wowmall' ), 4 ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/footer-layout-4.jpg',
					),
					'5' => array(
						'alt' => sprintf( esc_html__( 'Layout %s', 'wowmall' ), 5 ),
						'img' => WOWMALL_THEME_URI . '/admin/assets/images/footer-layout-5.jpg',
					),
				),
			),
			array(
				'id'       => 'footer_background',
				'type'     => 'background',
				'output'   => array( '#colophon, #colophon .footer-inner' ),
				'title'    => esc_html__( 'Footer Background', 'wowmall' ),
				'subtitle' => esc_html__( 'Footer background with image, color, etc.', 'wowmall' ),
				'default'  => array(
					'background-color' => '#222',
				),
			),
			array(
				'id'          => 'footer_color',
				'type'        => 'color',
				'validate'    => 'color',
				'transparent' => false,
				'output'      => array(
					'color' => '#colophon',
				),
				'title'       => esc_html__( 'Footer Color', 'wowmall' ),
				'default'     => '#b4b4b4',
			),
			array(
				'id'          => 'footer_titles_color',
				'type'        => 'color',
				'validate'    => 'color',
				'transparent' => false,
				'output'      => array(
					'color' => '#colophon .footer-inner h1, #colophon .footer-inner h2, #colophon .footer-inner h3, #colophon .footer-inner h4, #colophon .footer-inner h5, #colophon .footer-inner h6',
				),
				'title'       => esc_html__( 'Footer Titles Color', 'wowmall' ),
				'default'     => '#fff',
			),
			array(
				'id'       => 'footer_top_background',
				'type'     => 'background',
				'output'   => array( '#footer-top-panel' ),
				'title'    => esc_html__( 'Footer Top Panel Background', 'wowmall' ),
				'subtitle' => esc_html__( 'Footer Top Panel background with image, color, etc.', 'wowmall' ),
				'default'  => array(
					'background-color' => '#fc6f38',
				),
			),
			array(
				'id'          => 'footer_top_color',
				'type'        => 'color',
				'validate'    => 'color',
				'transparent' => false,
				'output'      => array(
					'color' => '#footer-top-panel',
				),
				'title'       => esc_html__( 'Footer Top Panel Color', 'wowmall' ),
				'default'     => '#fff',
			),
			array(
				'id'       => 'footer_bottom_background',
				'type'     => 'background',
				'output'   => array( '#footer-bottom-panel' ),
				'title'    => esc_html__( 'Footer Bottom Panel Background', 'wowmall' ),
				'subtitle' => esc_html__( 'Footer Bottom Panel background with image, color, etc.', 'wowmall' ),
				'default'  => array(
					'background-color' => '#141414',
				),
			),
			array(
				'id'          => 'footer_bottom_color',
				'type'        => 'color',
				'validate'    => 'color',
				'transparent' => false,
				'output'      => array(
					'color' => '#footer-bottom-panel',
				),
				'title'       => esc_html__( 'Footer Bottom Panel Color', 'wowmall' ),
				'default'     => '#b4b4b4',
			),
			array(
				'id'       => 'footer_logo',
				'type'     => 'media',
				'url'      => true,
				'title'    => esc_html__( 'Footer Logo', 'wowmall' ),
				'subtitle' => esc_html__( 'Add/Upload  footer logo using the WordPress native uploader', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo.png',
				),
			),
			array(
				'id'       => 'footer_logo_2x',
				'type'     => 'media',
				'url'      => true,
				'title'    => esc_html__( 'Footer Retina 2x Logo', 'wowmall' ),
				'subtitle' => esc_html__( 'Add/Upload footer retina 2x logo using the WordPress native uploader', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_2x.png',
				),
			),
			array(
				'id'       => 'footer_logo_3x',
				'type'     => 'media',
				'url'      => true,
				'title'    => esc_html__( 'Footer Retina 3x Logo', 'wowmall' ),
				'subtitle' => esc_html__( 'Add/Upload footer retina 3x logo using the WordPress native uploader', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_3x.png',
				),
			),
			array(
				'id'       => 'footer_text',
				'type'     => 'editor',
				'title'    => esc_html__( 'Footer Text', 'wowmall' ),
				'subtitle' => esc_html__( 'It will show at top.', 'wowmall' ),
				'default'  => esc_html__( 'WOWMALL is proudly powered by WordPress Entries (RSS) and Comments (RSS) %s', 'wowmall' ),
			),
			array(
				'id'      => 'privacy_page',
				'type'    => 'select',
				'title'   => esc_html__( 'Privacy Policy Page', 'wowmall' ),
				'data'    => 'pages',
				'default' => '568',
			),
			array(
				'id'       => 'payment_methods',
				'type'     => 'switch',
				'title'    => esc_html__( 'Payment Methods', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable Payments Methods image', 'wowmall' ),
				'default'  => true,
			),
			array(
				'id'       => 'payment_methods_img',
				'type'     => 'media',
				'url'      => true,
				'title'    => esc_html__( 'Payment Methods Image', 'wowmall' ),
				'subtitle' => esc_html__( 'Add/Upload Payment Methods image using the WordPress native uploader', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/payment-methods.png',
				),
			),
			array(
				'id'       => 'to_top',
				'type'     => 'switch',
				'title'    => esc_html__( 'To Top button', 'wowmall' ),
				'subtitle' => esc_html__( 'Enable or Disable To Top button', 'wowmall' ),
				'default'  => true,
			),
		),
	),
	array(
		'title'            => esc_html__( 'Top panel', 'wowmall' ),
		'id'               => 'footer_top_panel_settings',
		'customizer_width' => '400px',
		'subsection'       => true,
		'fields'           => array(
			array(
				'id'      => 'footer_newsletter_title',
				'type'    => 'text',
				'title'   => esc_html__( 'Top panel newsletter title', 'wowmall' ),
				'default' => esc_html__( 'Newsletter Signup', 'wowmall' ),
			),
			array(
				'id'      => 'footer_newsletter_text',
				'type'    => 'editor',
				'title'   => esc_html__( 'Top panel newsletter text', 'wowmall' ),
				'default' => esc_html__( 'Sign up for our e-mail and be the first who know our special offers! Furthermore, we will give a 15% discount on the next order after you sign up.', 'wowmall' ),
			),
			array(
				'id'      => 'footer_newsletter_form',
				'type'    => 'select',
				'title'   => esc_html__( 'Top panel newsletter', 'wowmall' ),
				'options' => wowmall()->newsletter_forms(),
			),
		),
	),
	array(
		'title'            => esc_html__( 'Footer Layout 1', 'wowmall' ),
		'id'               => 'footer_1_settings',
		'customizer_width' => '400px',
		'subsection'       => true,
		'fields'           => array(
			array(
				'id'      => 'footer_1_page',
				'type'    => 'select',
				'title'   => esc_html__( 'Footer Layout 1 Page', 'wowmall' ),
				'data'    => 'pages',
				'default' => '566',
			),
		),
	),
	array(
		'title'            => esc_html__( 'Footer Layout 2', 'wowmall' ),
		'id'               => 'footer_2_settings',
		'customizer_width' => '400px',
		'subsection'       => true,
		'fields'           => array(
			array(
				'id'      => 'footer_2_page',
				'type'    => 'select',
				'title'   => esc_html__( 'Footer Layout 2 Page', 'wowmall' ),
				'data'    => 'pages',
				'default' => '569',
			),
		),
	),
	array(
		'title'            => esc_html__( 'Footer Layout 3', 'wowmall' ),
		'id'               => 'footer_3_settings',
		'customizer_width' => '400px',
		'subsection'       => true,
		'fields'           => array(
			array(
				'id'      => 'footer_3_page',
				'type'    => 'select',
				'title'   => esc_html__( 'Footer Layout 3 Page', 'wowmall' ),
				'data'    => 'pages',
				'default' => '581',
			),
		),
	),
	array(
		'title'            => esc_html__( 'Footer Layout 4', 'wowmall' ),
		'id'               => 'footer_4_settings',
		'customizer_width' => '400px',
		'subsection'       => true,
		'fields'           => array(
			array(
				'id'      => 'footer_4_page',
				'type'    => 'select',
				'title'   => esc_html__( 'Footer Layout 4 Page', 'wowmall' ),
				'data'    => 'pages',
				'default' => '582',
			),
		),
	),
	array(
		'title'            => esc_html__( 'Extra Settings', 'wowmall' ),
		'id'               => 'extra_settings',
		'customizer_width' => '400px',
		'icon'             => 'el el-share',
		'fields'           => array(
			array(
				'id'       => 'extra_css',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'Extra CSS', 'wowmall' ),
				'subtitle' => esc_html__( 'Extra CSS just after theme styles', 'wowmall' ),
				'mode'     => 'css',
			),
			array(
				'id'       => 'extra_js',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'Extra JS', 'wowmall' ),
				'subtitle' => esc_html__( 'Extra JS before closing body tag', 'wowmall' ),
				'mode'     => 'javascript',
			),
		),
	),
	array(
		'icon'   => 'el-icon-cogs',
		'title'  => esc_html__( 'Maintenance', 'wowmall' ),
		'fields' => array(
			array(
				'id'       => 'maintenance_mode',
				'type'     => 'switch',
				'title'    => esc_html__( 'Enable/Disable maintenance mode', 'wowmall' ),
				'subtitle' => '',
				'default'  => 0,
			),
			array(
				'id'       => 'bg_maintenance',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload background using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Maintenance Page background', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/bg_maintenance.jpg',
				),
			),
			array(
				'id'       => 'logo_maintenance',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload Logo using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Maintenance Page Logo', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_maintenance.png',
				),
			),
			array(
				'id'       => 'logo_maintenance_2x',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload retina 2x Logo using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Maintenance  Retina 2x Logo', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_maintenance_2x.png',
				),
			),
			array(
				'id'       => 'logo_maintenance_3x',
				'type'     => 'media',
				'url'      => true,
				'subtitle' => esc_html__( 'Add/Upload retina 3x Logo using the WordPress native uploader', 'wowmall' ),
				'title'    => esc_html__( 'Maintenance Page Retina 3x Logo', 'wowmall' ),
				'default'  => array(
					'url' => WOWMALL_THEME_URI . '/assets/images/logo_maintenance_3x.png',
				),
			),
			array(
				'id'       => 'maintenance_page_title',
				'type'     => 'text',
				'title'    => esc_html__( 'Page Title', 'wowmall' ),
				'subtitle' => '',
				'desc'     => '',
				'default'  => esc_html__( 'We&#39;re Coming Soon', 'wowmall' ),
			),
			array(
				'id'       => 'maintenance_newsletter_pretext',
				'type'     => 'editor',
				'title'    => esc_html__( 'Newsletter Pretext', 'wowmall' ),
				'subtitle' => '',
				'desc'     => '',
				'default'  => __( '<h3>Subscribe to Our Newsletter</h3> BE THE FIRST TO KNOW. Get 15% off your next purchase when you sign up. By signing up, you accept the terms & Privacy Policy', 'wowmall' ),
			),
			array(
				'id'      => 'maintenance_newsletter_form',
				'type'    => 'select',
				'title'   => esc_html__( 'Maintenance Page newsletter form', 'wowmall' ),
				'options' => wowmall()->newsletter_forms(),
			),
			array(
				'id'       => 'maintenance_countdown',
				'type'     => 'switch',
				'title'    => esc_html__( 'Enable/Disable countdown', 'wowmall' ),
				'subtitle' => '',
				'default'  => 1,
			),
			array(
				'id'          => 'maintenance_date',
				'type'        => 'date',
				'title'       => esc_html__( 'Maintenance Page Date', 'wowmall' ),
				'placeholder' => esc_html__( 'Click to enter a date', 'wowmall' ),
			),
			array(
				'id'      => 'maintenance_hours',
				'type'    => 'slider',
				'title'   => esc_html__( "Maintenance Page Time, in hrs", 'wowmall' ),
				"default" => 12,
				"min"     => 0,
				"step"    => 1,
				"max"     => 23,
			),
			array(
				'id'       => 'maintenance_demo_mode',
				'type'     => 'switch',
				'title'    => esc_html__( 'Enable/Disable maintenance demo mode', 'wowmall' ),
				'subtitle' => esc_html__( 'Countdown will always shows approximately 6 month', 'wowmall' ),
				'default'  => 1,
			),
		),
	),
	array(
		'title'            => esc_html__( 'License', 'wowmall' ),
		'id'               => 'license',
		'customizer_width' => '400px',
		'icon'             => 'el el-share',
		'fields'           => array(
			array(
				'id'       => 'license',
				'type'     => 'license',
				'title'    => esc_html__( 'Purchase code', 'wowmall' ),
				'subtitle' => esc_html__( 'Enter your purchchase code', 'wowmall' ),
				'hint'     => array(
					'title'   => esc_html__( 'How to find your ThemeForest Item Purchase Code' ),
					'content' => esc_attr__( '<ol>
<li>Login into your ThemeForest account and go to the "Downloads" page.</li>
<li>Download the License Certificate & purchase code.</li>
<li>Open the downloaded file and inside you’ll find the Item Purchase Code</li>
</ol>' ),
				),
			),
		),
	),
);

if ( current_theme_supports( 'wowmall_beta_features', false ) ) {
	$sections['woo_single_settings']['fields'][] = array(
		'id'      => 'product_sidebar',
		'type'    => 'switch',
		'title'   => esc_html__( 'Show Sidebar on Product Page', 'wowmall' ),
		'default' => false,
	);
}

Redux::setSections( $opt_name, $sections );

add_filter( "redux/options/{$opt_name}/options", 'wowmall_get_wp_site_icon' );

add_action( "redux/options/{$opt_name}/saved", 'wowmall_set_site_icon' );

add_action( "redux/options/{$opt_name}/reset", 'wowmall_reset_site_icon' );

add_action( "redux/options/{$opt_name}/section/reset", 'wowmall_reset_site_icon' );

add_action( 'customize_save_after', 'wowmall_set_site_icon_options' );

function wowmall_get_wp_site_icon( $options ) {
	if ( ! is_admin() ) {
		return $options;
	}
	if ( ! empty( $options['favicon']['id'] ) ) {
		return $options;
	}
	$icon = get_option( 'site_icon' );
	if ( $icon ) {
		$options['favicon'] = array(
			'url' => '',
			'id'  => $icon,
		);
	}
	elseif ( ! empty( $options ) ) {
		foreach ( $options as $option_name => $option ) {
			if ( false !== strpos( $option_name, 'favicon_' ) ) {
				$options['favicon'] = $option;
				break;
			}
		}
	}

	return $options;
}

function wowmall_set_site_icon( $options ) {
	if ( ! empty( $options['favicon']['id'] ) ) {
		update_option( 'site_icon', $options['favicon']['id'] );
	}
	else {
		delete_option( 'site_icon' );
	}
	if ( wowmall()->is_woocommerce_activated() ) {

		if ( isset( $options['wc_shop_layout'] ) ) {
			if ( '2' === $options['wc_shop_layout'] ) {
				$default_value = get_option( 'woocommerce_shop_page_display', '' );
				if ( 'subcategories' === $default_value ) {
					$default_value = '';
				}
				update_option( 'woocommerce_shop_page_display_default', $default_value );
				update_option( 'woocommerce_shop_page_display', 'subcategories' );
			}
			if ( '1' === $options['wc_shop_layout'] ) {
				$default_value = get_option( 'woocommerce_shop_page_display_default', '' );
				if ( 'subcategories' === $default_value ) {
					$default_value = '';
				}
				delete_option( 'woocommerce_shop_page_display_default' );
				update_option( 'woocommerce_shop_page_display', $default_value );
			}
		}
	}
}

function wowmall_reset_site_icon( $options ) {
	if ( ! empty( $options->parent->transients['changed_values'] ) ) {
		$changed = $options->parent->transients['changed_values'];
		if ( isset( $changed['favicon'] ) ) {
			delete_option( 'site_icon' );
		}
	}
}

function wowmall_set_site_icon_options() {
	$icon = get_option( 'site_icon' );
	if ( $icon ) {
		$icon_option['id']  = $icon;
		$icon_option['url'] = '';
	}
	else {
		$icon_option['url'] = WOWMALL_THEME_URI . '/assets/images/favicon.ico';
	}
	Redux::setOption( 'wowmall_options', 'favicon', $icon_option );
	$shop_page_display = get_option( 'woocommerce_shop_page_display', '' );
	if ( 'subcategories' !== $shop_page_display ) {
		update_option( 'woocommerce_shop_page_display_default', $shop_page_display );
		Redux::setOption( 'wowmall_options', 'wc_shop_layout', '1' );
	}
	if ( $shop_page_display === 'subcategories' ) {
		Redux::setOption( 'wowmall_options', 'wc_shop_layout', '2' );
	}
}

add_action( "redux/options/{$opt_name}/saved", 'fallback_rem_to_px' );

function fallback_rem_to_px( $options ) {
	$tag = 1;
	while ( $tag <= 6 ) {
		if ( isset( $options[ 'h' . $tag ] ) ) {
			$update = false;
			foreach ( $options[ 'h' . $tag ] as $option => $value ) {
				if ( false !== strpos( $value, 'rem' ) ) {
					$update                           = true;
					$options[ 'h' . $tag ][ $option ] = $options[ 'h' . $tag ][ $option ] * $options['body_typography']['font-size'] . 'px';
				}
			}
			if ( $update ) {
				Redux::setOption( 'wowmall_options', 'h' . $tag, $options[ 'h' . $tag ] );
			}
		}
		++$tag;
	}
}

add_action( "redux/options/{$opt_name}/saved", 'clear_related_transients' );

function clear_related_transients() {
	$products = new WP_Query( array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );
	foreach ( $products->posts as $product ) {
		delete_transient( 'wc_related_' . $product );
	}
}

function wowmall_is_activated() {

	global $wowmall_options;

	$activated = false;
	$value     = empty( $wowmall_options['license'] ) ? '' : esc_attr( $wowmall_options['license'] );

	if ( ! empty( $value ) ) {

		$db_activated = get_transient( 'wowmall_licanse_activated' );

		if ( $db_activated ) {
			$activated = true;
		}
		else {
			$response = wp_safe_remote_post( 'https://lic.tonytemplates.com/?check_license', array(
				'body' => array(
					'code'    => $value,
					'item_id' => '19395344',
					'host'    => get_bloginfo( 'url' ),
				),
			) );

			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {

				$response_body = wp_remote_retrieve_body( $response );
				$response_body = json_decode( $response_body, true );

				if ( isset( $response_body['success'] ) ) {
					$activated = true;
					set_transient( 'wowmall_licanse_activated', true, DAY_IN_SECONDS );
				}
				else {
					Redux::setOption( 'wowmall_options', 'license', '' );
					delete_transient( 'wowmall_licanse_activated' );
				}
			}
		}
	}

	return $activated;
}

//remove_all_actions( 'redux/loaded' );

add_action( 'redux/loaded', 'wowmall_remove_demo' );

function wowmall_remove_demo() {
	// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
	if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
		remove_filter( 'plugin_row_meta', array(
			ReduxFrameworkPlugin::instance(),
			'plugin_metalinks',
		), null );

		// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
		remove_action( 'admin_notices', array(
			ReduxFrameworkPlugin::instance(),
			'admin_notices',
		) );
	}
}

unset( $colors, $opt_name, $sections, $sites );
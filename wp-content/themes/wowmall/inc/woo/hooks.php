<?php

//remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description' );

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
add_action( 'woocommerce_before_main_content', 'wowmall_wc_output_content_wrapper' );

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

add_filter( 'woocommerce_show_page_title', 'wowmall_wc_show_page_title' );

add_action( 'woocommerce_sidebar', 'wowmall_wc_output_wrapper_end', 20 );

remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );
add_action( 'woocommerce_after_main_content', 'wowmall_wc_output_content_wrapper_end' );

add_filter( 'single_product_large_thumbnail_size', 'wowmall_single_product_large_thumbnail_size' );

add_action( 'woocommerce_before_single_product_summary', 'wowmall_wc_single_grid_start', 5 );

add_action( 'woocommerce_before_single_product_summary', 'wowmall_woocommerce_show_product_images', 20 );

add_action( 'woocommerce_before_single_product_summary', 'wowmall_woocommerce_single_grid_middle', 30 );

add_action( 'woocommerce_before_single_product_summary', 'wowmall_woocommerce_show_product_flashes', 35 );

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

add_action( 'woocommerce_single_product_summary', 'wowmall_woocommerce_template_single_title', 5 );

add_action( 'woocommerce_single_product_summary', 'wowmall_woocommerce_template_single_cats' );

add_action( 'woocommerce_single_product_summary', 'wowmall_wc_template_single_excerpt', 20 );

add_action( 'woocommerce_single_product_summary', 'wowmall_wc_price_rating_wrapper', 24 );

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );

add_action( 'woocommerce_single_product_summary', 'wowmall_woocommerce_template_single_rating', 26 );

add_action( 'woocommerce_single_product_summary', 'wowmall_wc_price_rating_wrapper_end', 27 );

add_action( 'woocommerce_single_product_summary', 'wowmall_wc_single_add_to_cart_buttons_wrapper', 28 );

add_action( 'woocommerce_single_product_summary', 'wowmall_wc_single_btns_wrapper', 31 );

add_action( 'woocommerce_single_product_summary', 'wowmall_wc_single_btns_wrapper_end', 39 );

add_action( 'woocommerce_single_product_summary', 'wowmall_woocommerce_template_single_tags', 40 );

add_action( 'woocommerce_single_product_summary', 'wowmall_woocommerce_template_single_share', 50 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating' );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

add_action( 'woocommerce_after_single_product_summary', 'wowmall_woocommerce_product_collapse' );

add_action( 'woocommerce_after_single_product_summary', 'wowmall_woocommerce_single_grid_end', 30 );

add_filter( 'woocommerce_single_product_image_html', 'wowmall_wc_single_product_image_html' );

add_filter( 'woocommerce_single_product_image_thumbnail_html', 'wowmall_wc_single_product_image_html' );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' );

add_filter( 'woocommerce_output_related_products_args', 'wowmall_wc_output_related_products_args' );

add_filter( 'woocommerce_product_description_heading', '__return_false' );

add_filter( 'woocommerce_product_additional_information_heading', '__return_false' );

add_filter( 'woocommerce_review_gravatar_size', 'wowmall_woocommerce_review_gravatar_size' );

remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
add_action( 'woocommerce_product_thumbnails', 'wowmall_woocommerce_show_product_thumbnails', 20 );

add_filter( 'single_product_archive_thumbnail_size', 'wowmall_single_product_archive_thumbnail_size' );

add_filter( 'post_class', 'wowmall_wc_product_post_class', 20, 3 );

add_filter( 'woocommerce_enqueue_styles', 'wowmall_woocommerce_enqueue_styles' );

add_action( 'woocommerce_before_shop_loop_item', 'wowmall_wc_loop_product_wrapper', 5 );

add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 15 );

add_action( 'woocommerce_before_shop_loop_item_title', 'wowmall_wc_loop_product_content_wrapper', 20 );

add_action( 'woocommerce_before_shop_loop_item_title', 'wowmall_woocommerce_show_product_flashes', 25 );

add_action( 'woocommerce_before_shop_loop_item_title', 'wowmall_wc_loop_cats_rating_wrapper', 30 );

add_action( 'woocommerce_before_shop_loop_item_title', 'wowmall_woocommerce_template_single_cats', 35 );

add_action( 'woocommerce_before_shop_loop_item_title', 'wowmall_wc_template_loop_rating', 40 );

add_action( 'woocommerce_before_shop_loop_item_title', 'wowmall_wc_loop_cats_rating_wrapper_end', 45 );

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
add_action( 'woocommerce_shop_loop_item_title', 'wowmall_woocommerce_template_loop_product_title' );

add_action( 'woocommerce_shop_loop_item_title', 'wowmall_wc_loop_product_excerpt', 20 );

add_action( 'woocommerce_after_shop_loop_item_title', 'wowmall_wc_price_rating_wrapper', 5 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );
add_action( 'woocommerce_after_shop_loop_item_title', 'wowmall_wc_template_loop_price' );

add_filter( 'woocommerce_empty_price_html', 'wowmal_wc_empty_price_html' );
add_filter( 'woocommerce_variable_empty_price_html', 'wowmal_wc_empty_price_html' );
add_filter( 'woocommerce_grouped_empty_price_html', 'wowmal_wc_empty_price_html' );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', 'wowmall_wc_template_loop_rating', 15 );

add_action( 'woocommerce_after_shop_loop_item_title', 'wowmall_wc_price_rating_wrapper_end', 20 );

add_filter( 'woocommerce_loop_add_to_cart_link', 'wowmall_wc_loop_add_to_cart_link', 9, 2 );

add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_loop_product_variables', 4 );

add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_loop_product_add_to_cart_wrapper', 5 );

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_template_loop_add_to_cart' );

add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_loop_product_quick_view_button', 14 );

add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_loop_product_add_to_cart_wrapper_end', 17 );

add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_loop_product_wrapper_end', 25 );

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_wishlist_add_button_grid', 12 );
add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_compare_add_button_grid', 12 );
add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_loop_list_compare_wishlist_btns_wrapper', 15 );
add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_wishlist_add_button_list', 15 );
add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_compare_add_button_list', 15 );
add_action( 'woocommerce_after_shop_loop_item', 'wowmall_wc_loop_list_compare_wishlist_btns_wrapper_end', 16 );

add_filter( 'woocommerce_sale_flash', 'wowmall_wc_sale_flash' );

add_filter( 'woocommerce_product_subcategories_hide_empty', '__return_true' );

add_action( 'woocommerce_before_shop_loop', 'wowmall_wc_loop_sorting_wrapper' );

add_action( 'woocommerce_before_shop_loop', 'wowmall_wc_loop_grid_list_button', 40 );

add_action( 'woocommerce_before_shop_loop', 'wowmall_wc_loop_sorting_wrapper_end', 50 );

add_filter( 'woocommerce_product_categories_widget_args', 'wowmall_wc_product_categories_widget_args' );

add_filter( 'woocommerce_cart_item_thumbnail', 'wowmall_wc_cart_item_thumbnail', 10, 3 );

add_filter( 'woocommerce_cart_item_name', 'wowmall_wc_cart_item_name', 10, 3 );

add_action( 'woocommerce_after_mini_cart', 'wowmall_minicart_posttext' );

add_filter( 'wc_add_to_cart_message_html', 'wowmall_wc_add_to_cart_message_html', 10, 2 );

add_filter( 'woocommerce_order_button_html', 'wowmall_wc_order_button_html' );

add_filter( 'woocommerce_default_address_fields', 'wowmall_wc_default_address_fields' );

add_filter( 'woocommerce_get_country_locale', 'wowmall_wc_get_country_locale' );

add_filter( 'woocommerce_cart_shipping_method_full_label', 'wowmall_wc_cart_shipping_method_full_label', 10, 2 );

add_filter( 'woocommerce_account_menu_item_classes', 'wowmall_wc_account_menu_item_classes', 10, 2 );

add_filter( 'woocommerce_order_item_quantity_html', 'wowmall_wc_order_item_quantity_html', 10, 2 );

add_filter( 'template_include', 'wowmall_template_loader', 11 );

remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail' );
add_action( 'woocommerce_before_subcategory_title', 'wowmall_wc_subcategory_thumbnail' );

add_filter( 'product_cat_class', 'wowmall_product_cat_class', 10, 3 );

remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title' );
add_action( 'woocommerce_shop_loop_subcategory_title', 'wowmall_wc_template_loop_category_title' );

add_filter( 'woocommerce_page_title', 'wowmall_wc_page_title' );

add_filter( 'woocommerce_available_variation', 'wowmall_wc_available_variation', 10, 3 );

add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'wowmall_wc_dropdown_variation_attribute_options_html', 10, 2 );

add_filter( 'loop_shop_per_page', 'wowmall_loop_shop_per_page' );

add_filter( 'woocommerce_add_success', 'wowmall_wc_add_success' );

add_filter( 'wc_price', 'wowmall_wc_price', 10, 3 );

add_filter( 'woocommerce_layered_nav_count', 'wowmall_wc_layered_nav_count', 10, 3 );

add_filter( 'woocommerce_product_thumbnails_columns', 'wowmall_wc_product_thumbnails_columns' );

add_action( 'woocommerce_after_cart_table', 'woocommerce_cross_sell_display' );

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message' );
add_action( 'woocommerce_cart_is_empty', 'wowmall_wc_empty_cart_message' );

//add_filter( 'wc_additional_variation_images_main_images_class', 'wowmall_wc_additional_variation_images_main_images_class' );

add_filter( 'wc_additional_variation_images_gallery_images_class', 'wowmall_wc_additional_variation_images_gallery_images_class' );

remove_all_actions( 'wp_ajax_wc_additional_variation_images_load_frontend_images_ajax' );

remove_all_actions( 'wp_ajax_nopriv_wc_additional_variation_images_load_frontend_images_ajax' );

add_action( 'wp_ajax_wc_additional_variation_images_load_frontend_images_ajax', 'wowmall_load_images_ajax' );

add_action( 'wp_ajax_nopriv_wc_additional_variation_images_load_frontend_images_ajax', 'wowmall_load_images_ajax' );

add_filter( 'wc_additional_variation_images_custom_swap', '__return_true' );

add_filter( 'woocommerce_single_product_image_thumbnail_html', 'wowmall_wcavi_single_product_image_html', 10, 4 );

add_action( 'pre_get_posts', 'wowmall_wc_tags_sku_search' );

add_filter( 'woocommerce_widget_cart_item_quantity', 'wowmall_wc_widget_cart_item_quantity', 10, 3 );

add_action('wp_print_footer_scripts', 'wowmall_footer_scripts', 999 );
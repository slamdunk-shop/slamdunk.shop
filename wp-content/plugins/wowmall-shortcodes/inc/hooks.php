<?php
add_action( 'wowmall_posts_carousel_item', 'wowmall_posts_carousel_item_thumb', 10 );

add_action( 'wowmall_posts_carousel_item', 'wowmall_posts_carousel_item_date', 20 );

add_action( 'wowmall_posts_carousel_item', 'wowmall_posts_carousel_item_title', 30 );
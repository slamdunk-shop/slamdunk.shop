<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'wowmall-style','wowmall-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 21 );

// END ENQUEUE PARENT ACTION


/**
 * Локализация языков темы
 */

function slamdunk_theme_setup() {
   load_child_theme_textdomain( 'wowmall', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'slamdunk_theme_setup' );



/**
 * Кастомные фунцкии их сновной темы
 */

// Our hooked in function - $fields is passed via the filter!
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {

    unset($fields['billing']['billing_company']);// компания
    unset($fields['billing']['billing_postcode']);// индекс
    unset($fields['billing']['billing_address_2']); //адрес строка 2
    unset($fields['account']['account_username']);// аккаунт
    unset($fields['billing']['billing_email']);//
    unset($fields['account']['account_password']);//
    unset($fields['account']['account_password-2']);//
    return $fields;
}

// Делаем поля необязательными
add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );

// Наша перехваченная функция - $fields проходит через фильтр
function custom_override_default_address_fields( $address_fields ) {
    $address_fields['company']['required'] = false; // Адрес
    $address_fields['postcode']['required'] = false; // Индекс
    $address_fields['address_2']['required'] = false; // Населённый пункт
    $address_fields['address_1']['required'] = false; // адрес 1

    return $address_fields;
}

/* END  Кастомные фунцкии их сновной темы */


<?php

// Asegúrate de que el archivo no sea accedido directamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function my_child_theme_vc_setup() {
    // Incluir el archivo que contiene el bloque personalizado
    require_once get_stylesheet_directory() . '/inc/vc-roster-raiderio-block.php';
    require_once get_stylesheet_directory() . '/inc/vc-raidleader-raiderio-block.php';

    wp_enqueue_style( 'alchemists-menus-styles', get_stylesheet_directory_uri() . '/assets/css/artic.css' );

}
add_action( 'vc_before_init', 'my_child_theme_vc_setup' );

<?php
/**
 * @package c*map
*/

/*
Plugin Name: c*map
Plugin URI: https://github.com/chfinke/cmap-wp
Description: Generate a crowdsourced/cooperative map
Version: 0.0.3
Author: Christoph Finke
Author URI: https://chfinke.github.io/
License: GPLv2 or later
Text Domain: chfinke
*/


function register_cpt_cmap() {

    $labels = array(
        'name' => _x( 'c*map', 'cmap' ),
        'singular_name' => _x( 'c*map', 'cmap' ),
        'add_new' => _x( 'Add New', 'cmap' ),
        'add_new_item' => _x( 'Add New Item', 'cmap' ),
        'edit_item' => _x( 'Edit Item', 'cmap' ),
        'new_item' => _x( 'New Item', 'cmap' ),
        'view_item' => _x( 'View Item', 'cmap' ),
        'search_items' => _x( 'Search Item', 'cmap' ),
        'not_found' => _x( 'No item found', 'cmap' ),
        'not_found_in_trash' => _x( 'No item found in Trash', 'cmap' ),
        'parent_item_colon' => _x( 'Parent Item:', 'cmap' ),
        'menu_name' => _x( 'c*map', 'cmap' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'c*map items filterable by group',
        'supports' => array( 'title', 'editor', 'author', 'custom-fields' ),
        'taxonomies' => array( 'layers' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-sticky',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'cmap', $args );
}

add_action( 'init', 'register_cpt_cmap' );


function cmap_plu_activate() {
    cmap_plu_rewrite();
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'cmap_plu_activate' );

function cmap_plu_deactivate() {
    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'cmap_plu_deactivate' );

function cmap_plu_rewrite() {
    add_rewrite_rule( 'cmap/?$', 'wp-content/plugins/cmap/map.php', 'top' );
    add_rewrite_rule( 'cmap/data.json/?$', 'wp-content/plugins/cmap/data.php', 'top' );
    add_rewrite_rule( 'cmap/functions/?$', 'wp-content/plugins/cmap/functions.php', 'top' );
    add_rewrite_rule( 'cmap/edit/?$', 'wp-content/plugins/cmap/edit.php', 'top' );
    add_rewrite_rule( 'cmap/edit_map/?$', 'wp-content/plugins/cmap/edit_map.php', 'top' );
    add_rewrite_rule( 'cmap/list/?$', 'wp-content/plugins/cmap/list.php', 'top' );
}

add_action( 'init', 'cmap_plu_rewrite' );

<?php
/**
 * @package c*map
*/

/*
Plugin Name: c*map
Plugin URI: https://github.com/chfinke/c_map-wp
Description: Generate a crowdsourced/cooperative map
Version: 0.0.2
Author: Christoph Finke
Author URI: https://chfinke.github.io/
License: GPLv2 or later
Text Domain: chfinke
*/


function register_cpt_c_map() {
 
    $labels = array(
        'name' => _x( 'c*map', 'c_map' ),
        'singular_name' => _x( 'c*map', 'c_map' ),
        'add_new' => _x( 'Add New', 'c_map' ),
        'add_new_item' => _x( 'Add New Item', 'c_map' ),
        'edit_item' => _x( 'Edit Item', 'c_map' ),
        'new_item' => _x( 'New Item', 'c_map' ),
        'view_item' => _x( 'View Item', 'c_map' ),
        'search_items' => _x( 'Search Item', 'c_map' ),
        'not_found' => _x( 'No item found', 'c_map' ),
        'not_found_in_trash' => _x( 'No item found in Trash', 'c_map' ),
        'parent_item_colon' => _x( 'Parent Item:', 'c_map' ),
        'menu_name' => _x( 'c*map', 'c_map' ),
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
 
    register_post_type( 'c_map', $args );
}

add_action( 'init', 'register_cpt_c_map' );


function c_map_plu_activate() {
	c_map_plu_rewrite();
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'c_map_plu_activate' );

function c_map_plu_deactivate() {
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'c_map_plu_deactivate' );

function c_map_plu_rewrite() {
    add_rewrite_rule( 'c_map/?$', 'wp-content/plugins' . '/c_map/map.php', 'top' );
	add_rewrite_rule( 'c_map/data.json/?$', 'wp-content/plugins' . '/c_map/data.php', 'top' );
	add_rewrite_rule( 'c_map/functions/?$', 'wp-content/plugins' . '/c_map/functions.php', 'top' );
	add_rewrite_rule( 'c_map/edit/?$', 'wp-content/plugins' . '/c_map/edit.php', 'top' );
    add_rewrite_rule( 'c_map/edit_map/?$', 'wp-content/plugins' . '/c_map/edit_map.php', 'top' );
    add_rewrite_rule( 'c_map/list/?$', 'wp-content/plugins' . '/c_map/list.php', 'top' );
}

add_action( 'init', 'c_map_plu_rewrite' );

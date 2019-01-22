<?php
/**
 * @package Crowd Map
*/

/*
Plugin Name: Crowd Map
Plugin URI: https://github.com/chfinke/wp-crowdmap
Description: Generate a crowdsourced map
Version: 0.0.2
Author: Christoph Finke
Author URI: https://chfinke.github.io/
License: GPLv2 or later
Text Domain: chfinke
*/


function register_cpt_crowdmap() {
 
    $labels = array(
        'name' => _x( 'crowdmap', 'crowdmap' ),
        'singular_name' => _x( 'crowdmap', 'crowdmap' ),
        'add_new' => _x( 'Add New', 'crowdmap' ),
        'add_new_item' => _x( 'Add New Item', 'crowdmap' ),
        'edit_item' => _x( 'Edit Item', 'crowdmap' ),
        'new_item' => _x( 'New Item', 'crowdmap' ),
        'view_item' => _x( 'View Item', 'crowdmap' ),
        'search_items' => _x( 'Search Item', 'crowdmap' ),
        'not_found' => _x( 'No item found', 'crowdmap' ),
        'not_found_in_trash' => _x( 'No item found in Trash', 'crowdmap' ),
        'parent_item_colon' => _x( 'Parent Item:', 'crowdmap' ),
        'menu_name' => _x( 'Crowd Map', 'crowdmap' ),
    );
 
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => 'Crowd Map items filterable by group',
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
 
    register_post_type( 'crowdmap', $args );
}

add_action( 'init', 'register_cpt_crowdmap' );


function crowdmap_plu_activate() {
	crowdmap_plu_rewrite();
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'crowdmap_plu_activate' );

function crowdmap_plu_deactivate() {
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'crowdmap_plu_deactivate' );

function crowdmap_plu_rewrite() {
    add_rewrite_rule( 'crowdmap/?$', 'wp-content/plugins' . '/crowdmap/map.php', 'top' );
	add_rewrite_rule( 'crowdmap/data.json/?$', 'wp-content/plugins' . '/crowdmap/data.php', 'top' );
	add_rewrite_rule( 'crowdmap/functions/?$', 'wp-content/plugins' . '/crowdmap/functions.php', 'top' );
	add_rewrite_rule( 'crowdmap/edit/?$', 'wp-content/plugins' . '/crowdmap/edit.php', 'top' );
    add_rewrite_rule( 'crowdmap/edit_map/?$', 'wp-content/plugins' . '/crowdmap/edit_map.php', 'top' );
    add_rewrite_rule( 'crowdmap/list/?$', 'wp-content/plugins' . '/crowdmap/list.php', 'top' );
}

add_action( 'init', 'crowdmap_plu_rewrite' );

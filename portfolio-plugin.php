<?php
/*
Plugin Name: Portfolio Plugin
Description: A plugin to create a custom post type called Portfolio with a custom taxonomy.
Version: 1.0
Author: Oluwafemi Oluwatobi Best
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register Portfolio Custom Post Type
function portfolio_plugin_register_post_type() {
    $labels = array(
        'name'               => 'Portfolios',
        'singular_name'      => 'Portfolio',
        'menu_name'          => 'Portfolio',
        'name_admin_bar'     => 'Portfolio',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Portfolio',
        'new_item'           => 'New Portfolio',
        'edit_item'          => 'Edit Portfolio',
        'view_item'          => 'View Portfolio',
        'all_items'          => 'All Portfolios',
        'search_items'       => 'Search Portfolios',
        'not_found'          => 'No portfolios found.',
        'not_found_in_trash' => 'No portfolios found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array('title', 'editor', 'thumbnail'),
        'show_in_rest'       => true, // Enable block editor
    );

    register_post_type('portfolio', $args);
}
add_action('init', 'portfolio_plugin_register_post_type');

// Register Portfolio Category Taxonomy
function portfolio_plugin_register_taxonomy() {
    $labels = array(
        'name'              => 'Portfolio Categories',
        'singular_name'     => 'Portfolio Category',
        'search_items'      => 'Search Portfolio Categories',
        'all_items'         => 'All Portfolio Categories',
        'edit_item'         => 'Edit Portfolio Category',
        'update_item'       => 'Update Portfolio Category',
        'add_new_item'      => 'Add New Portfolio Category',
        'new_item_name'     => 'New Portfolio Category Name',
        'menu_name'         => 'Portfolio Categories',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'public'            => true,
    );

    register_taxonomy('portfolio_category', 'portfolio', $args);
}
add_action('init', 'portfolio_plugin_register_taxonomy');

// Shortcode to Display Portfolio Posts
function portfolio_plugin_shortcode($atts) {
    $args = array(
        'post_type'      => 'portfolio',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );

    $query = new WP_Query($args);
    $output = '';

    if ($query->have_posts()) {
        $output .= '<div class="portfolio-list">';
        $categories = get_terms('portfolio_category');

        foreach ($categories as $category) {
            $output .= '<h3>' . esc_html($category->name) . '</h3>';
            $output .= '<ul>';

            while ($query->have_posts()) {
                $query->the_post();

                if (has_term($category->term_id, 'portfolio_category')) {
                    $output .= '<li>';
                    $output .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                    $output .= '</li>';
                }
            }

            $output .= '</ul>';
        }
        $output .= '</div>';
    }

    wp_reset_postdata();

    return $output;
}
add_shortcode('portfolio', 'portfolio_plugin_shortcode');

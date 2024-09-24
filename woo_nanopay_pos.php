<?php
/*
Plugin Name: WooCommerce NanoPay POS
Description: A WooCommerce plugin that turns a page into a point of sale using NanoPay for Nano payments.
Version: 2.5
Author: mnpezz
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include the admin settings
include_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

// Include the POS page
include_once plugin_dir_path(__FILE__) . 'includes/pos-page.php';

function woo_nanopay_pos_create_page() {
    $page_title = 'Nano Point of Sale';
    $page_content = '[nanopay_pos]';
    $page_template = ''; // Leave empty if you don't want to use a custom page template

    // Check if the page already exists using WP_Query
    $query = new WP_Query(array(
        'post_type' => 'page',
        'title' => $page_title,
        'post_status' => 'publish',
        'posts_per_page' => 1
    ));
    $page = $query->have_posts() ? $query->posts[0] : null;

    if (!$page) {
        // Create post object
        $page_data = array(
            'post_title'    => $page_title,
            'post_content'  => $page_content,
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
            'post_template' => $page_template
        );

        // Insert the post into the database
        $page_id = wp_insert_post($page_data);
    } else {
        $page_id = $page->ID;
    }

    return get_permalink($page_id);
}
register_activation_hook(__FILE__, 'woo_nanopay_pos_create_page');
?>

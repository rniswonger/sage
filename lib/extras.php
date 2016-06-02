<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;

/**
 * Bootstrap nav walker
 */
// require_once('wp-bootstrap-navwalker-master/wp_bootstrap_navwalker.php');


/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Add class if sidebar is active
  if (Setup\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

/**
 * Are we on a blog-releated page?
 */
function is_blog() {
  global $post;
  $posttype = get_post_type($post);
  return ( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post')  ) ? true : false ;
}

/**
 * Enable shortcodes in widgets
 */
add_filter( 'widget_text', 'do_shortcode' );


/**
 * Saves post type and taxonomy data to JSON files in the theme directory.
 * @param array $data Array of post type data that was just saved.
 */
function cptui_local_json( $data = array() ) {
  $theme_dir = get_stylesheet_directory();
  // Create our directory if it doesn't exist
  if ( ! is_dir( $theme_dir .= '/cptui-json' ) ) {
    mkdir( $theme_dir );
  }
  if ( array_key_exists( 'cpt_custom_post_type', $data ) ) {
    // Fetch all of our post types and encode into JSON.
    $cptui_post_types = get_option( 'cptui_post_types', array() );
    $content          = json_encode( $cptui_post_types );
    // Save the encoded JSON to a primary file holding all of them.
    file_put_contents( get_stylesheet_directory() . '/cptui-json/' . 'post_type_data.json', $content );
  }
  if ( array_key_exists( 'cpt_custom_tax', $data ) ) {
    // Fetch all of our taxonomies and encode into JSON.
    $cptui_taxonomies = get_option( 'cptui_taxonomies', array() );
    $content          = json_encode( $cptui_taxonomies );
    // Save the encoded JSON to a primary file holding all of them.
    file_put_contents( get_stylesheet_directory() . '/cptui-json/' . 'taxonomy_data.json', $content );
  }
}
add_action( 'cptui_after_update_post_type', __NAMESPACE__ . '\\cptui_local_json' );
add_action( 'cptui_after_update_taxonomy', __NAMESPACE__ . '\\cptui_local_json' );

<?php

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

if ( !function_exists('get_term_id') ) {

     /**
      * taxonomy_tags.php - Additional taxonomy tags for RecipePress
      *
      * @package RecipePress
      * @subpackage includes
      * @author GrandSlambert
      * @copyright 2009-2011
      * @access public
      * @since 2.0.6
      */
     /* Tags below this are for term templates */

     function get_term_id($term) {
          if ( !is_object($term) ) {
               $term = get_term($term);
          }

          return $term->term_id;
     }

}

if ( !function_exists('the_term_id') ) {

     /**
      * Echo the term id.
      * 
      * @param integer/object $term 
      */
     function the_term_id($term) {
          echo get_term_id($term);
     }

}

if ( !function_exists('get_term_name') ) {

     /**
      * Get the taxonomy name.
      *
      * @param string $term    The taxonomy name.
      * @param array $args     Display arguments.
      * @return string
      */
     function get_term_name($term, $args = array()) {
          $defaults = array(
               'style' => NULL,
          );

          $args = wp_parse_args($args, $defaults);

          if ( !is_object($term) ) {
               $term = get_term($term);
          }

          switch ($args['style']) {
               case 'list':
                    $output = '<li class="recipe-category-title ' . $args['class'] . '"><a href="' . get_term_permalink($term) . '">' . $term->name . '</a></li>';
                    break;
               case 'definition':
                    $output = '<dt class="recipe-category-title ' . $args['class'] . '"><a href="' . get_term_permalink($term) . '">' . $term->name . '</a></dt>';
                    break;
               default:
                    $output = $term->name;
                    break;
          }

          return apply_filters('recipe_press_term_name', $output);
     }

}

if ( !function_exists('the_term_name') ) {

     /**
      * Display the taxonomy name.
      *
      * @param string $term    The taxonomy name.
      * @param array $args     Display arguments.
      */
     function the_term_name($term, $args = array()) {
          echo get_term_name($term, $args);
     }

}

if ( !function_exists('get_term_description') ) {

     /**
      * Get the taxonomy description.
      *
      * @param string $term    The taxonomy name.
      * @param array $args     Display arguments.
      * @return string
      */
     function get_term_description($term, $args = array()) {
          $defaults = array(
               'style' => 'default',
               'class' => 'recipe-term-description'
          );

          $args = wp_parse_args($args, $defaults);

          if ( !is_object($term) ) {
               $term = get_term($term);
               wp_die('FUCK IT ALL');
          }

          switch ($args['style']) {
               case 'list':
                    $output = '<li class="recipe-category-descritpion ' . $args['class'] . '">' . $term->description . '</li>';
                    break;
               case 'definition':
                    $output = '<dd class="recipe-category-descritpion ' . $args['class'] . '">' . $term->description . '</dd>';
                    break;
               default:
                    $output = $term->description;
          }

          if ( isset($output) ) {
               return apply_filters('recipe_press_term_description', $output);
          }
     }

}

if ( !function_exists('the_term_description') ) {

     /**
      * Display the taxonomy description.
      *
      * @param string $term    The taxonomy name.
      * @param array $args     Display arguments.
      */
     function the_term_description($term, $args = array()) {
          echo get_term_description($term, $args);
     }

}

/* The following template tags are for listing recipes */

if ( !function_exists('get_recipes_list') ) {

     /**
      * Get the list of recipes.
      *
      * @param string $term    The taxonomy name.
      * @param array $args     Display arguments.
      * @return string
      */
     function get_recipes_list($term, $args = array()) {
          if ( !is_object($term) ) {
               $term = get_term($term);
          }

          $defaults = array(
               'limit' => 5,
               'orderby' => 'title',
               'order' => 'asc',
               'after_link' => ' | ',
               'style' => NULL,
               'before-title' => '',
               'after-title' => '',
               'before-link' => '',
               'after-link' => '',
          );

          $args = wp_parse_args($args, $defaults);

          $req = array(
               'post_type' => 'recipe',
               'posts_per_page' => $args['limit'],
               'orderby' => $args['orderby'],
               'order' => $args['order'],
               "$term->taxonomy" => "$term->slug"
          );

          $posts = get_posts($req);

          $countPosts = 1;

          $post_list = '';

          foreach ( $posts as $post ) {
               switch ($args['style']) {
                    default:
                         if ( $countPosts > 1 ) {
                              $post_list.= $args['before-link'];
                         }
                         $post_list.= '<a href="' . get_permalink($post->ID) . '">' . $args['before-title'] . $post->post_title . $args['after-title'] . '</a>';

                         if ( $countPosts < count($posts) ) {
                              $post_list.= $args['after-link'];
                         }
                         break;
               }
               ++$countPosts;
          }

          return apply_filters('recipe_press_list', $post_list);
     }

}

if ( !function_exists('the_recipes_list') ) {

     /**
      * Display the list of recipes.
      *
      * @param string $term    The taxonomy name.
      * @param array $args     Display arguments.
      */
     function the_recipes_list($term, $args = array()) {
          echo get_recipes_list($term, $args);
     }

}

if ( !function_exists('get_random_posts_list') ) {

     /**
      * Get a random list of recipes.
      *
      * @param string $term    The taxonomy name.
      * @param array $args     Display arguments.
      * @return string
      */
     function get_random_posts_list($term, $args = array()) {
          $args['orderby'] = 'rand';
          return apply_filters('recipe_press_random_list', get_recipes_list($term, $args));
     }

}

if ( !function_exists('the_random_posts_list') ) {

     /**
      * Display a random list of recipes.
      *
      * @param string $term    The taxonomy name.
      * @param array $args     Display arguments.
      * @return string
      */
     function the_random_posts_list($term, $args = array()) {
          echo get_random_posts_list($term, $args);
     }

}

/**
 * The following functions and tags use the "Taxonomy Images" plugin by Michael
 * Fields to add images to categories and cuisines. For more information, visit
 * http://wordpress.mfields.org/plugins/taxonomy-images/
 */
if ( !function_exists('get_term_thumbnail') ) {

     /**
      * Uses the "Taxonomy Image Plugin" to retrieve the thumbnail for the term.
      *
      * @global object $taxonomy_images_plugin
      * @param <integer> $term   The term ID or the term object.
      * @param array $args
      * @return string
      */
     function get_term_thumbnail($term, $args=array()) {
          $defaults = array(
               'size' => 'thumbnail',
               'class' => 'taxonomy-image',
               'add_span' => true,
          );

          $args = wp_parse_args($args, $defaults);

          if ( !is_object($term) ) {
               $term = get_term($term);
          }

          if ($args['add_span']) {
               $prefix = '<span class="recipe-category-image '. $args['class'] . '">';
               $suffix = '</span>';
          } else {
               $prefix = $suffix = '';
          }

          global $taxonomy_images_plugin;

          if ( is_object($taxonomy_images_plugin) ) {
               $img = $taxonomy_images_plugin->get_image_html($args['size'], $term->term_taxonomy_id);
               return $prefix  . $img . $suffix;
          } else {
               return false;
          }
     }

}


if ( !function_exists('the_term_thumbnail') ) {

     /**
      * Displays the term thumbnail using get_term_thumbnail.
      *
      * @param <integer> $term   The term ID or the term object.
      * @param array $args
      */
     function the_term_thumbnail($term, $args = array()) {
          echo get_term_thumbnail($term, $args);
     }

}

if ( !function_exists('get_published_categories') ) {

     /**
      * Retrieves a list of taxonomy terms with at least one published recipe.
      * @param string $taxonomy
      * @return array
      */
     function get_published_categories($taxonomy) {
          $published_ids = array();

          $terms = get_terms($taxonomy, array('orderby' => 'name', 'number' => 0));

          foreach ( $terms as $term ) {
               $postArgs = array(
                    'post_type' => 'recipe',
                    'post_status' => 'publish',
                    $taxonomy => $term->name,
               );
               $posts = get_posts($postArgs);
               if ( count($posts) ) {
                    $published_ids[] = $term->term_id;
               }
          }

          return $published_ids;
     }

}

if ( !function_exists('get_recipe_categories') ) {

     /**
      * Replacement for wp_list_categories to return only categories with published reicpes.
      *
      * @param array $args
      */
     function get_recipe_categories($args = array()) {
          $defaults = array(
               'taxonomy' => 'recipe-category',
               'orderby' => 'name',
               'order' => 'asc',
               'show_count' => false,
               'pad_counts' => true,
               'hierarchical' => true,
               'title_li' => '',
               'hide_empty' => true,
               'current_category' => false
          );

          $args = wp_parse_args($args, $defaults);


          $args['include'] = get_published_categories($args['taxonomy']);
          $args['echo'] = false;

          return wp_list_categories($args);
     }

}

if ( !(function_exists('list_recipe_categories')) ) {

     /**
      * Replacement for wp_list_categories to display only categories with published reicpes.
      *
      * @param array $args
      */
     function list_recipe_categories($args = array()) {
          echo get_recipe_categories($args);
     }

}

if ( !function_exists('previous_taxonomies_link') ) {

     function previous_taxonomies_link($atts = array()) {
          global $pagination;

          $defaults = array(
               'title' => sprintf(__('<span class="meta-nav">&larr;</span> Previous %1$s', 'recipe-press-reloaded'), $pagination['taxonomy'])
          );

          $atts = wp_parse_args($atts, $defaults);

          if ( $pagination['current-page'] > 1 ) {
               $previous = $pagination['current-page'] - 1;
               echo '<a href="' . $pagination['url'] . '?page=' . $previous . '">' . $atts['title'] . '</a>';
          }
     }

}

if ( !function_exists('next_taxonomies_link') ) {

     function next_taxonomies_link($atts = array()) {
          global $pagination;

          $defaults = array(
               'title' => sprintf(__('Next %1$s <span class="meta-nav">&rarr;</span>', 'recipe-press-reloaded'), $pagination['taxonomy'])
          );

          $atts = wp_parse_args($atts, $defaults);
          $next = $pagination['current-page'] + 1;

          if ( $next <= $pagination['pages'] ) {
               echo '<a href="' . $pagination['url'] . '?page=' . $next . '">' . $atts['title'] . '</a>';
          }
     }

}
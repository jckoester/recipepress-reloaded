<?php

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * template_tags.php - Additional template tags for RecipePress
 *
 * @package RecipePress
 * @subpackage includes
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 1.0
 */
/**
 * Conditionals
 */
if ( !function_exists('use_recipe_taxonomy') ) {

     function use_recipe_taxonomy($tax = 'recipe-category') {
          global $RECIPEPRESSOBJ;
          return apply_filters('recipe_press_use_taxonomy', isset($RECIPEPRESSOBJ->rpr_options['taxonomies'][$tax]['active']) && $RECIPEPRESSOBJ->rpr_options['use_taxonomies']);
     }

}

if ( !function_exists('use_recipe_categories') ) {

     function use_recipe_categories() {
          global $RECIPEPRESSOBJ;
          return apply_filters('recipe_press_use_categories', $RECIPEPRESSOBJ->rpr_options['use_categories'] && !$RECIPEPRESSOBJ->rpr_options['use_post_categories']);
     }

}

if ( !function_exists('use_recipe_cuisines') ) {

     function use_recipe_cuisines() {
          global $RECIPEPRESSOBJ;
          return apply_filters('recipe_press_use_cuisines', $RECIPEPRESSOBJ->rpr_options['use_cuisines']);
     }

}

if ( !function_exists('use_recipe_seasons') ) {

     function use_recipe_seasons() {
          global $RECIPEPRESSOBJ;
          return apply_filters('recipe_press_use_seasons', $RECIPEPRESSOBJ->rpr_options['use_seasons']);
     }

}

if ( !function_exists('use_recipe_courses') ) {

     function use_recipe_courses() {
          global $RECIPEPRESSOBJ;
          return apply_filters('recipe_press_use_courses', $RECIPEPRESSOBJ->rpr_options['use_courses']);
     }

}

if ( !function_exists('use_recipe_times') ) {

     function use_recipe_times() {
          global $RECIPEPRESSOBJ;
          return apply_filters('recipe_press_use_times', $RECIPEPRESSOBJ->rpr_options['use_times']);
     }

}

if ( !function_exists('use_recipe_servings') ) {

     function use_recipe_servings() {
          global $RECIPEPRESSOBJ;
          return apply_filters('recipe_press_use_servings', $RECIPEPRESSOBJ->rpr_options['use_servings']);
     }

}

if ( !function_exists('use_recipe_comments') ) {

     function use_recipe_comments() {
          global $RECIPEPRESSOBJ;
          return apply_filters('recipe_press_use_comments', $RECIPEPRESSOBJ->rpr_options['use_comments']);
     }

}

if ( !function_exists('get_recipe_time') ) {

     /**
      * Get the recipe time.
      *
      * @global <object> $RECIPEPRESSOBJ
      * @param <array> $args         Display time: 'single' for single line, 'double' (default) for double line
      * @param <int/object> $post    Should be a post ID, NOT a post object.
      * @return <string>             Text including time and (minutes/hours)
      */
     function get_recipe_time($args = array(), $post = NULL) {
          global $RECIPEPRESSOBJ;

          if ( is_int($post) ) {
               $post = get_post($post);
          } elseif ( !is_object($post) ) {
               global $post;
          }

          /* Leave for backward compatibility */
          if ( !is_array($args) ) {
               $args = array($args);
          }

          /* Prep the arguments */
          $defaults = array(
               'time' => 'prep',
               'type' => $RECIPEPRESSOBJ->rpr_options['time_display_type'],
               'title' => __('Prep Time', 'recipe-press-reloaded'),
               'prefix' => ' : ',
               'suffix' => $RECIPEPRESSOBJ->rpr_options['minute_text'],
               'tag' => 'li',
               'class' => 'recipe-prep'
          );

          $args = wp_parse_args($args, $defaults);
          $output = '';

          if ( $time = get_post_meta($post->ID, '_recipe_' . $args['time'] . '_time_value', true) ) {
               if ( $args['tag'] ) {
                    $output.= '<' . $args['tag'] . ' id="recipe-' . $args['time'] . '-' . $post->ID . '" class="' . $args['class'] . '">';
               }
               $output.= '<span class="details-header details-header-prep">' . ucfirst($args['title']) . $args['prefix'] . '</span>';

               switch ($args['type']) {
                    case 'double':
                         $output.= '<br>' . $time . $args['suffix'];
                         break;
                    default:
                         $output.= ' ' . $time . $args['suffix'];
                         break;
               }

               if ( $args['tag'] ) {
                    $output.= '</' . $args['tag'] . '>';
               }

               return apply_filters('recipe-press-get_time', $output);
          }
     }

}

if ( !function_exists('get_recipe_prep_time') ) {

     /**
      * Get and display the recipe prep time.
      *
      * @param <array> $type     Dsiplay arguments.
      * @param <int> $post       ID of the post, NOT the post object.
      */
     function get_recipe_prep_time($args = array(), $post = NULL) {
          if ( !is_array($args) ) {
               $args = array('type' => $args);
          }

          $args['time'] = 'prep';
          $args['title'] = __('Prep Time', 'recipe-press-reloaded');
          return apply_filters('recipe_pres_prep_time', get_recipe_time($args, $post));
     }

}

if ( !function_exists('the_recipe_prep_time') ) {

     /**
      * Display the recipe prep time.
      *
      * @param <array> $args     Display arguments.
      * @param <id> $post        The post ID, NOT the post object.
      */
     function the_recipe_prep_time($args = array(), $post = NULL) {
          echo get_recipe_prep_time($args, $post);
     }

}

if ( !function_exists('get_recipe_cook_time') ) {

     /**
      * Get and display the recipe cooking time.
      *
      * @param <array> $args     Display arguments.
      * @param <int> $post       ID of post, NOT the post object.
      * @return <string>         The cook time plus (minutes/hours)
      */
     function get_recipe_cook_time($args = array(), $post = NULL) {
          if ( !is_array($args) ) {
               $args = array('type' => $args);
          }

          $args['time'] = 'cook';
          $args['title'] = __('Cook Time', 'recipe-press-reloaded');
          $args['class'] = 'cook-time';
          return apply_filters('recipe_press_cook_time', get_recipe_time($args, $post));
     }

}

if ( !function_exists('the_recipe_cook_time') ) {

     /**
      * Display the recipe cooking time.
      *
      * @param <array> $args     Display arguments.
      * @param <id> $post        The post ID, NOT the post object.
      */
     function the_recipe_cook_time($args = array(), $post = NULL) {
          echo get_recipe_cook_time($args, $post);
     }

}

if ( !function_exists('get_recipe_ready_time') ) {

     /**
      * Get the recipe ready time.
      *
      * @param <array> $args     Display arguments.
      * @param <int> $post       The post ID, NOT the post object.
      * @return <string>         The ready time.
      */
     function get_recipe_ready_time($args = array(), $post = NULL) {
          if ( !is_array($args) ) {
               $args = array('type' => $args);
          }

          $args['time'] = 'ready';
          $args['title'] = __('Ready Time', 'recipe-press-reloaded');
          $args['class'] = 'ready-time';

          if ( !isset($args['suffix']) ) {
               $args['suffix'] = '';
          }

          return apply_filters('recipe_press_ready_time', get_recipe_time($args, $post));
     }

}

if ( !function_exists('the_recipe_ready_time') ) {

     /**
      * Display the recipe ready time.
      *
      * @param <array> $args     Display arguments.
      * @param <type> $post      ID of the post, NOT the post object.
      */
     function the_recipe_ready_time($args = array(), $post = NULL) {
          echo get_recipe_ready_time($args, $post);
     }

}

if ( !function_exists('get_recipe_servings') ) {

     /**
      * Get the Recipe serving information.
      *
      * @global <object> $RECIPEPRESSOBJ
      * @param <array> $args         Display arguments
      * @param <int/ojbect> $post    Post ID or Object
      * @return <string>             Recipe serving text.
      */
     function get_recipe_servings($args = array(), $post = NULL) {
          global $RECIPEPRESSOBJ;

          if ( is_int($post) ) {
               $post = get_post($post);
          } elseif ( !is_object($post) ) {
               global $post;
          }

          $defaults = array(
               'tag' => 'span',
               'class' => 'recipe_servings_value'
          );

          $args = wp_parse_args($args, $defaults);

          if ( $servings = get_post_meta($post->ID, '_recipe_servings_value', true) ) {

               $size = get_post_meta($post->ID, '_recipe_serving_size_value', true);
               if ( (int) $size != 0 and $size != -1 ) {
                    $term = get_term_by('id', $size, 'recipe-serving');
                    if ( is_object($term) ) {
                         $size = $term->name;
                    }
               } elseif ( $size == -1 ) {
                    unset($size);
               }

               /* translators: Displayed before serving information on recipe display pages. */
               $output = '<' . $args['tag'] . ' class="' . $args['class'] . '">' . $servings . ' ';

               if ( isset($size) ) {
                    if ( calculateIngredientSize($servings) > 1 ) {
                         $output.= rpr_inflector::plural($size);
                    } else {
                         $output.= rpr_inflector::singular($size);
                    }
               }
               $output.= '</' . $args['tag'] . '>';

               return apply_filters('recipe_press_servings', $output);
          }
     }

}

if ( !function_exists('the_recipe_servings') ) {

     /**
      * Display the recipe serving information.
      *
      * @param <array> $args         Display arguments.
      * @param <int/object> $post    Post ID or Object
      */
     function the_recipe_servings($args = array(), $post = NULL) {
          echo get_recipe_servings($args, $post);
     }

}

if ( !function_exists('get_recipe_ingredients') ) {

     /**
      * Get the recipe ingredients text.
      *
      * @global <type> $RECIPEPRESSOBJ
      * @param <int/object> $post    Post ID or Object
      */
     function get_recipe_ingredients($post = NULL) {
          global $RECIPEPRESSOBJ;

          if ( is_int($post) ) {
               $post = get_post($post);
          } elseif ( !is_object($post) ) {
               global $post;
          }

		  //$post_meta = get_post_meta($post->ID);
          $ingredients = $RECIPEPRESSOBJ->getIngredients($post);

          $content = '<ul class="rp_ingredients">';

          foreach ( $ingredients as $ingredient ) {
               if ( $ingredient['size'] != 'divider' ) {
                    $term = get_term_by('id', $ingredient['item'], 'recipe-ingredient');

                    /* If empty ingredient, do not show line */
                    if ( isset($term->errors) ) {
                         continue;
                    } else {
                         $ingredient['name'] = $term->name;
                    }

					//Link ingredients if we have a general setting for that - or a special page or link defined for the special ingredient
					//special link has highest priority:
                    if ( isset($ingredient['page-link']) and $ingredient['page-link'] != 0 ) {
                         $link = get_page_link($ingredient['page-link']);
                         $target = "_top";
                    } elseif ( isset($ingredient['url']) and $ingredient['url'] != '' ) {
                         $link = $ingredient['url'];
                         $target = "_blank";
                    /*} elseif ($post_meta['_recipe_link_ingredients_value'][0] == 1) {
                    	var_dump($RECIPEPRESSOBJ->rpr_options['taxonomies']['recipe-ingredient']['page']);
                    	$link=get_page_link($ingredient['page-link']);*/
                    }else{
                    	unset($link);
                    }

                    if ( isset($ingredient['notes']) ) {
                         $notes = ' <small>' . stripslashes_deep($ingredient['notes']) . '</small>';
                    } else {
                         $notes = '';
                    }

                    if ( $ingredient['size'] == 'none' or $ingredient['size'] == -1 ) {
                         $ingredient['size'] = '';
                    } else {
                         if ( isset($ingredient['quantity']) ) {
                              $ingredient['total'] = calculateIngredientSize($ingredient['quantity']);
                         } else {
                              $ingredient['total'] = 1;
                         }

                         /* Convert taxonomy ID to name */
                         if ( (int) $ingredient['size'] != 0 ) {
                              $size = get_term_by('id', $ingredient['size'], 'recipe-size');
                              if ( is_object($size) ) {
                                   $ingredient['size'] = $size->name;
                              }
                         }

                         /* Convert size to plural if more than one */
                         if ( $ingredient['total'] <= 1 ) {
                              $ingredient['size'] = rpr_inflector::singular($ingredient['size'], $ingredient['total']);
                         } else {
                              $ingredient['size'] = rpr_inflector::plural($ingredient['size'], $ingredient['total']);
                         }
                    }
                    
                    if ( $ingredient['size'] == 'divider' ) {
                         $content.= '</ul><h4 class="recipe-section-title">' . $ingredient['name'] . '</h4><ul class="rp_ingredients">';
                    } elseif ( isset($link) ) {
                         $content.= '<li class="rp_ingredient">' . $ingredient['quantity'] . ' ' . $ingredient['size'] . ' <a href="' . $link . '" target="' . $target . '">' . $ingredient['name'] . '</a> ' . $notes . '</li>';
                    } elseif ( $RECIPEPRESSOBJ->rpr_options['link_ingredients'] == true ) {
                    //} elseif ( get_post_meta($post->ID, '_recipe_link_ingredients_value', true) ) {
                    	//if a page is set for ingredients display use this:
                    	if($RECIPEPRESSOBJ->rpr_options['taxonomies']['recipe-ingredient']['page'] >= 0):
                    		$link = get_page_link($RECIPEPRESSOBJ->rpr_options['taxonomies']['recipe-ingredient']['page']);
                    	else:
                    	//else use the taxonomy link
                    		$link = get_term_link($term, 'recipe-ingredient');
                    	endif;
                         $content.= '<li class="rp_ingredient">' . $ingredient['quantity'] . ' ' . $ingredient['size'] . ' <a href="' . $link . '">' . $ingredient['name'] . '</a>' . $notes . '</li>';
                    } else {
                         $content.= '<li class="rp_ingredient">' . $ingredient['quantity'] . ' ' . $ingredient['size'] . ' ' . $ingredient['name'] . $notes . '</li>';
                    }
               } elseif ( $ingredient['size'] == 'divider' ) {
                    $content.= '</ul>';
                    $ingredientDivider = '<h3 class="ingredient-divider">' . $ingredient['item'] . '</h3>';
                    $content.= apply_filters('ingredient-divider', $ingredientDivider);
                    $content.= '<ul class="rp_ingredients">';
               }
          }


          $content.= '</ul>';

          return apply_filters('recipe_press_ingredients', $content);
     }

}

if ( !function_exists('the_recipe_ingredients') ) {

     /**
      * Display the recipe ingredients.
      *
      * @param <int/object> $post    Post ID or Object.
      */
     function the_recipe_ingredients($post = NULL) {
          echo get_recipe_ingredients($post);
     }

}


if ( !function_exists('get_the_recipe_directions') ) {

     /**
      * Get the recipe directions.
      *
      * @global <type> $RECIPEPRESSOBJ
      * @param <int/object> $post    Post ID or Object.
      * @return <string>
      */
     function get_the_recipe_directions($post = NULL) {
          global $RECIPEPRESSOBJ;

          if ( is_int($post) ) {
               $post = get_post($post);
          } elseif ( !is_object($post) ) {
               global $post;
          }

          return apply_filters('recipe_press_directions', $post->post_content);
     }

}

if ( !function_exists('the_recipe_directions') ) {

     /**
      * Display the recipe directions.
      *
      * @param <int/object> $post    Post ID or Object
      */
     function the_recipe_directions($post = NULL) {
          echo get_the_recipe_directions($post);
     }

}

if ( !function_exists('get_the_recipe_introduction') ) {

     /**
      * Get the recipe introduction.
      *
      * @global <type> $RECIPEPRESSOBJ
      * @param <array> $args         Display arguments.
      * @param <int/object> $post    Post ID or Object.
      * @return <string>
      */
     function get_the_recipe_introduction($args = array(), $post = NULL) {
          global $RECIPEPRESSOBJ;

          if ( is_int($post) ) {
               $post = get_post($post);
          } elseif ( !is_object($post) ) {
               global $post;
          }

          $defaults = array(
               'length' => $RECIPEPRESSOBJ->rpr_options['default_excerpt_length'],
               'suffix' => '...'
          );

          $args = wp_parse_args($args, $defaults);

          return apply_filters('recipe_press_introduction', rpr_inflector::trim_excerpt($post->post_excerpt, $args['length'], $args['suffix']));
     }

}

if ( !function_exists('the_recipe_introduction') ) {

     /**
      * Display the recipe introduction.
      *
      * @param <array> $args         Display arguments.
      * @param <int/object> $post    Post ID or Object
      */
     function the_recipe_introduction($args = array(), $post = NULL) {
          echo get_the_recipe_introduction($args, $post);
     }

}

if ( !function_exists('get_the_recipe_taxonomy') ) {

     /**
      * Get information on a specific taxonomy.
      *
      * @global <object> $RECIPEPRESSOBJ
      * @param <string> $tax         The taxonomy name.
      * @param <array> $args         Display arguments.
      * @param <int/object> $post    Post ID or Object
      * @return <string>
      */
     function get_the_recipe_taxonomy($tax = NULL, $args = array(), $post = NULL) {
          global $RECIPEPRESSOBJ;

          if ( !taxonomy_exists($tax) ) {
               return false;
          }

          if ( is_int($post) ) {
               $post = get_post($post);
          } elseif ( !is_object($post) ) {
               global $post;
          }

          $defaults = array(
               'prefix' => $RECIPEPRESSOBJ->rpr_options['taxonomies'][$tax]['plural'] . ': ',
               'divider' => ', ',
               'before-category' => '',
               'after-category' => '',
               'suffix' => '',
          );

          $args = wp_parse_args($args, $defaults);

          if ( wp_get_object_terms($post->ID, 'recipe-category') ) {
               $cats = $args['prefix'] . get_the_term_list($post->ID, $tax, $args['before-category'], $args['divider'], $args['after-category']) . $args['suffix'];
               return apply_filters('recipe_press_taxonomy', $cats);
          }
     }

}

if ( !function_exists('get_the_recipe_category') ) {

     /**
      * Get the recipe category.
      *
      * @param <array> $args         Display arguments.
      * @param <int/object> $post    Post ID or Object.
      */
     function get_the_recipe_category($args = array(), $post = NULL) {
          if ( !isset($args['prefix']) ) {
               $args['prefix'] = __('Posted In: ', 'recipe-press-reloaded');
          }
          return apply_filters('recipe_press_categories', get_the_recipe_taxonomy('recipe-category', $args, $post));
     }

}

if ( !function_exists('the_recipe_category') ) {

     /**
      * Display the recipe category.
      *
      * @param <array> $args         Display arguments.
      * @param <int/object> $post    Post ID or Object.
      */
     function the_recipe_category($args = array(), $post = NULL) {
          echo get_the_recipe_category($args, $post);
     }

}

if ( !function_exists('get_the_recipe_cuisine') ) {

     /**
      * Get the recipe cuisines.
      *
      * @param <array> $args         Display arguments.
      * @param <int/object> $post    Post ID or Object.
      */
     function get_the_recipe_cuisine($args = array(), $post = NULL) {
          return apply_filters('recipe_press_cuisines', get_the_recipe_taxonomy('recipe-cuisine', $args, $post));
     }

}

if ( !function_exists('the_recipe_cuisine') ) {

     /**
      * Display the recipe cuisines.
      *
      * @param <array> $args         Display arguments.
      * @param <int/object> $post    Post ID or Object.
      */
     function the_recipe_cuisine($args = array(), $post = NULL) {
          echo get_the_recipe_cuisine($args, $post);
     }

}

if ( !function_exists('calculateIngredientSize') ) {

     /**
      * Calcuate the ingredient size for display
      *
      * @param <array> $ingredient
      * @return <string>
      */
     function calculateIngredientSize($size) {
          $total = 0;

          $sizeSplit = preg_split("/[\s,]+/", $size);

          foreach ( $sizeSplit as $sizePart ) {
               if ( preg_match("/[\/]+/", $sizePart) ) {
                    $args = preg_split("/[\/]+/", $sizePart);
                    $results = $args[0] / $args[1];
               } else {
                    $results = $sizePart;
               }

               $total += $results;
          }

          return apply_filters('recipe_press_ingredient_size', $total);
     }

}

if ( !function_exists('get_recipe_controls') ) {

     function get_recipe_controls($args = array(), $post_id = NULL) {
          global $RECIPEPRESSOBJ;
          $post_id = ( NULL === $post_id ) ? get_the_ID() : $post_id;

          $defaults = array(
               'print' => true, /* change to this when the print options tab is active - $RECIPEPRESSOBJ->rpr_options['use-print'], */
              // 'recipe-box' => $RECIPEPRESSOBJ->options['use-recipe-box'],
               'print-link-image' => false,
               'add-link-image' => false,
               'view-link-image' => false
          );

          $args = wp_parse_args($args, $defaults);

          $output = '';

          /* Get print link */
          if ( $args['print'] ) {
               $output.= get_recipe_print_link($args);
          }

          

          /* Get Recipe Box Link */
          /*if ( $args['recipe-box'] ) {
               $output.= get_recipe_box_link($args);
          }*/

          return apply_filters('recipe_controls', $output);
     }

}

if ( !function_exists('the_recipe_controls') ) {

     function the_recipe_controls($args = array(), $post_id = NULL) {
          echo get_recipe_controls($args, $post_id);
     }

}

if ( !function_exists('get_recipe_print_link') ) {

     /**
      * Returns the link to "print recipe".
      *
      * @global $RECIPEPRESSOBJ $RECIPEPRESSOBJ
      * @global object $post
      * @param array $args
      * @param int/object $post
      * @return string
      */
     function get_recipe_print_link($args = array(), $post_id = NULL) {
          global $RECIPEPRESSOBJ;

          $post_id = ( NULL === $post_id ) ? get_the_ID() : $post_id;

          $defaults = array(
               'title' => __('Print', 'recipe-press-reloaded'),
               'tag' => 'li',
               'class' => 'recipe-print-link',
               'target' => '_top',
               'popup' => true,
               'template' => '1',//$RECIPEPRESSOBJ->rpr_options['default-print-template'],
               'print-link-image' => false,
          );

          $args = wp_parse_args($args, $defaults);

          /* Check if we have an image for the add link */
          if ( $args['print-link-image'] ) {
               list($width, $height, $type, $attr) = getimagesize($args['print-link-image']);
               $printlink = '<img src="' . $args['print-link-image'] . '" ' . $attr . '>';
          } else {
               $printlink = $args['title'];
          }

          /* Check if pretty permalinks are in use and build appropriate links. */
          if ( get_option('permalink_structure') ) {
               $urldivider = '?';
          } else {
               $urldivider = '&';
          }

          $output = '<' . $args['tag'] . ' class="recipe-controls ' . $args['class'] . '">' . '<a href="' . get_permalink($post_id) . $urldivider . 'print=' . $args['template'] . '">' . $printlink . '</a></' . $args['tag'] . '>';

          return apply_filters('recipe_press_print_link', $output);
     }

}

if ( !function_exists('recipe_print_link') ) {

     /**
      * Display the link to "print recipe".
      * @param array $args
      * @param int/object $post
      */
     function recipe_print_link($args = array(), $post = NULL) {
          echo get_recipe_print_link($args, $post);
     }

}


if ( !function_exists('get_the_recipe_box_image') ) {

     /**
      * Retrieves the recipe image URL to use in style settings.
      *
      * @global object $post
      * @param integer/object $post
      * @return string
      */
     function get_the_recipe_box_image($post = NULL) {
          if ( is_int($post) ) {
               $post = get_post($post);
          } elseif ( !is_object($post) ) {
               global $post;
          }

          if ( $headerImage = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'recipe-press-image') ) {
               return 'style="background: url(' . $headerImage[0] . ') no-repeat top left" onclick="window.location = \'' . get_permalink($post->ID) . '\'" ';
          } else {
               return false;
          }
     }

}

if ( !function_exists('the_recipe_box_image') ) {

     /**
      * Gets the URL for the recipe image to use in style settings.
      * 
      * @param integer $post 
      */
     function the_recipe_box_image($post = NULL) {
          echo get_the_recipe_box_image($post);
     }

}

if ( !function_exists('get_the_next_recipe_link') ) {

     function get_the_next_recipe_link($args = array()) {
          global $recipes, $old_post;
          $current_page = get_query_var('page');

          $defaults = array(
               'link' => __('&larr; More Recipes', 'recipe-press-reloaded'),
          );

          $args = wp_parse_args($args, $defaults);

          if (get_option('permalink_structure')) {
               $urldivider = '?';
          } else {
               $urldivider = '&';
          }

          if ($current_page > 1) {
               $previous = --$current_page;
               echo '<a href="'. get_permalink($old_post->ID) . $urldivider . 'page=' . $previous . '">' . $args['link'] . '</a>';
          }
     }

}

if ( !function_exists('the_next_recipe_link') ) {

     function the_next_recipe_link($args = array()) {
          echo get_the_next_recipe_link($args);
     }

}

if ( !function_exists('get_the_previous_recipe_link') ) {

     function get_the_previous_recipe_link($args = array()) {
          global $recipes, $old_post;
          $current_page = get_query_var('page');

          $defaults = array(
               'link' => __('More Recipes &rarr;', 'recipe-press-reloaded'),
          );

          $args = wp_parse_args($args, $defaults);

          if (get_option('permalink_structure')) {
               $urldivider = '?';
          } else {
               $urldivider = '&';
          }

          if ($current_page < (int) $recipes->max_num_pages) {
               $next = max(++$current_page, 2);
               echo '<a href="'. get_permalink($old_post->ID) . $urldivider . 'page=' . $next . '">' . $args['link'] . '</a>';
          }
     }

}

if ( !function_exists('the_previous_recipe_link') ) {

     function the_previous_recipe_link($args = array()) {
          echo get_the_previous_recipe_link($args);
     }

}
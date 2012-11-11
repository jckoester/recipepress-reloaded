<?php

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * shortcodes.php - RecipePress Administration Class
 *
 * @package RecipePress
 * @subpackage classes
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 2.0.4
 */
class RPR_ShortCodes extends RPR_Core {

     static $instance;

     /**
      * Initialize the class.
      */
     function RPR_ShortCodes() {
          parent::RPR_Core();

          /* Add Shortcode */
          add_shortcode('recipe-list', array(&$this, 'recipe_list_shortcode'));
          add_shortcode('recipe-index', array(&$this, 'recipe_index_shortcode'));
          add_shortcode('recipe-show', array(&$this, 'recipe_show_shortcode'));
          add_shortcode('recipe-tax', array(&$this, 'recipe_tax_shortcode'));
          add_shortcode('recipe-box', array(&$this, 'recipe_box_shortcode'));
     }

     /**
      * Initialize the shortcodes.
      */
     static function initialize() {
          $instance = self::get_instance();
     }

     /**
      * Returns singleton instance of object
      *
      * @return instance
      */
     static function get_instance() {
          if ( is_null(self::$instance) ) {
               self::$instance = new RPR_ShortCodes;
          }
          return self::$instance;
     }

     /**
      * Display a list of recipes on a page using a shortcode.
      *
      * @param <array> $atts
      * @return <string>
      */
     function recipe_list_shortcode($atts) {
          global $recipes, $post, $old_post;
          $old_post = $post;

		  extract( shortcode_atts( array(
			   'template' => 'shortcode-list',
               'posts_per_page' => $this->rpr_options['recipe_count'],
               'featured' => false,
               'orderby' => 'title',
               'order' => 'ASC',
               'author' => NULL,
               'author_name' => NULL,
               'recipe_category' => NULL,
               'recipe_cuisine' => NULL,
               'recipe_course' => NULL,
               'recipe_season' => NULL,
               'tax_relation' => 'AND',
		  ), $atts ) );

          
          /*set page number if not set*/
          if( get_query_var('page') == 0 ):
          	$page = 1;
          	set_query_var('page', 1);
          else:
          	$page = get_query_var('page');
          endif;

          /* Arguments array for searching recipes. */
          $args = array(
               'post_type' => 'recipe',
               'posts_per_page' => $posts_per_page,
               'showposts' => $posts_per_page,
               'paged' => $page,
               'orderby' => $orderby,
               'order' => $order,
               'author' => $author,
               'author_name' => $author_name,
               'offset' => $page * $posts_per_page - $posts_per_page
          );

          if ( isset($recipe_category) ) {
          		$args['tax_query']['relation'] = 'OR';

                $args['tax_query'][] = array(
                	'taxonomy' => 'recipe-category',
                	'field' => 'slug',
                    'terms' => explode(',', $recipe_category)
                );
		  }

          if ( isset($recipe_cuisine) ) {
			$atts['tax_query']['relation'] = 'OR';

            $atts['tax_query'][] = array(
            	'taxonomy' => 'recipe-cuisine',
                'field' => 'slug',
                'terms' => explode(',', $recipe_cuisine)
            );
          }
               
          if ( isset($recipe_course) ) {
            $atts['tax_query']['relation'] = 'OR';

            $atts['tax_query'][] = array(
                'taxonomy' => 'recipe-course',
                'field' => 'slug',
            	'terms' => explode(',', $recipe_course)
            );
          }
               
          if ( isset($recipe_season) ) {
          	$atts['tax_query']['relation'] = 'OR';

            $atts['tax_query'][] = array(
            	'taxonomy' => 'recipe-season',
                'field' => 'slug',
                'terms' => explode(',', $recipe_season)
          	);
          }
          
          switch ($featured) {
               case 'only':
                    $args['meta_key'] = '_recipe_featured_value';
                    $args['meta_value'] = '1';
                    break;
               case 'hide':
                    $args['meta_key'] = '_recipe_featured_value';
                    $args['meta_value'] = '1';
                    $args['meta_compare'] = '!=';
                    break;
          }

          /* Get posts */
          $recipes = new WP_Query($args);

          ob_start();
          require($this->get_template($template));
          $output = ob_get_contents();
          ob_end_clean();

          wp_reset_query();

          $post = $old_post;
          return $output;
     }
     
     /**
      * Display an alphabetical index of recipes on a page using a shortcode.
      *
      * @param <array> $atts
      * @return <string>
      */
     function recipe_index_shortcode($atts) {
          global $recipes, $post, $old_post;
          $old_post = $post;

		  extract( shortcode_atts( array(
			   'template' => 'shortcode-index',
		  ), $atts ) );

          
          /*set page number if not set*/
          if( get_query_var('page') == 0 ):
          	$page = 1;
          	set_query_var('page', 1);
          else:
          	$page = get_query_var('page');
          endif;

          /* Arguments array for searching recipes. */
          $args = array(
               'post_type' => 'recipe',
               'orderby' => 'title',
               'order' => 'ASC',
          );

          /* Get posts */
          $recipes = new WP_Query($args);

          ob_start();
          require($this->get_template($template));
          $output = ob_get_contents();
          ob_end_clean();

          wp_reset_query();

          $post = $old_post;
          return $output;
     }

     function recipe_show_shortcode($atts) {
          global $wpdb, $post, $RECIPEPRESSOBJ;
          $tmp_post = $post;
          $RECIPEPRESSOBJ->in_shortcode = true;

          $defaults = array(
               'recipe' => NULL,
               'template' => 'recipe-single',
          );

          $atts = wp_parse_args($atts, $defaults);
          if ( !$atts['recipe'] ) {
               return __('Sorry, no recipes found.', 'recipe-press-reloaded');
          }

          $post = get_post($wpdb->get_var('select `id` from `' . $wpdb->prefix . 'posts` where `post_name` = "' . $atts['recipe'] . '" and `post_status` = "publish" limit 1'));
          setup_postdata($post);

          ob_start();
          include ($this->get_template($atts['template']));
          $output = ob_get_contents();
          ob_end_clean();

          $post = $tmp_post;
          return $output;
     }

     function recipe_tax_shortcode($atts) {
          global $wpdb, $post, $pagination, $RECIPEPRESSOBJ;
          $tmp_post = $post;
          $this->in_shortcode = true;
          $page = get_query_var('page');

		  extract( shortcode_atts( array(
			   'taxonomy' => 'recipe-category',
               'template' => 'recipe-taxonomy',
               'number' => 0,
               'offset' => 0,
               'orderby' => 'name',
               'order' => 'asc',
               'hide_empty' => true,
               'fields' => 'all',
               'slug' => false,
               'hierarchical' => true,
               'name__like' => '',
               'pad_counts' => false,
               'child_of' => NULL,
               'parent' => 0,
		  ), $atts ) );

          $include = get_published_categories($taxonomy);

          /* Count all terms */
          $fields = 'ids';
          $all_terms = get_terms($taxonomy, $atts);

          if ( $taxonomy == 'recipe-ingredient' ) {
               $pagination = array(
                    'total' => count($all_terms),
                    'pages' => ceil(count($all_terms) / $this->rpr_options['ingredients_per_page']),
                    'current-page' => max($page, 1),
                    'taxonomy' => __('Ingredients', 'recipe-press-reloaded'),
                    'url' => get_permalink($this->rpr_options['ingredient_page']),
                    'per-page' => $this->rpr_options['ingredients_per_page']
               );
          } else {
               $this->rpr_options['taxonomies'][$taxonomy] = $this->taxDefaults($this->rpr_options['taxonomies'][$taxonomy]);

               $pagination = array(
                    'total' => count($all_terms),
                    'pages' => ceil(count($all_terms) / $this->rpr_options['taxonomies'][$taxonomy]['per-page']),
                    'current-page' => max($page, 1),
                    'taxonomy' => $this->rpr_options['taxonomies'][$taxonomy]['plural'],
                    'url' => get_permalink($this->rpr_options['taxonomies'][$taxonomy]['page']),
                    'per-page' => $this->rpr_options['taxonomies'][$taxonomy]['per-page']
               );
          }

          unset($atts['fields']);

          $atts['number'] = $pagination['per-page'];

          if ( $page > 1 ) {
               $atts['offset'] = $page * $atts['number'] - $atts['number'];
          } else {
               $atts['offset'] = 0;
          }



          $terms = get_terms($taxonomy, $atts);

          ob_start();
          include ($this->get_template($template));
          $output = ob_get_contents();
          ob_end_clean();

          $post = $tmp_post;
          return $output;
     }

}
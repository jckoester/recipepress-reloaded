<?php

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * rpr_core.php - RecipePressReloaded Core Class
 *
 * @package RecipePress
 * @subpackage classes
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 2.0.4
 */
class RPR_Core {

     var $menuName = 'recipe-press-reloaded';
     var $pluginName = 'RecipePress reloaded';
     var $version = '0.2';
     var $optionsName = 'rpr-options';
     var $options = array();
     
     var $in_shortcode = false;


     /**
      * Initialize the plugin.
      */
     function RPR_Core() {
          /* Load Language Files */
          load_plugin_textdomain('recipe-press', false, dirname(dirname(plugin_basename(__FILE__))) . '/lang');

          /* Plugin Settings */
          /* translators: The name of the plugin, should be a translation of "RecipePress Reloaded" only! */
          $this->pluginName = __('RecipePress reloaded', 'recipe-press-reloaded');

          $this->loadSettings();

          /* Add custom images sizes for RecipePress */
          foreach ( $this->rpr_options['image_sizes'] as $image => $size ) {
               add_image_size('rpr-' . $image, $size['width'], $size['height'],true);
          }
     }

     /**
      * Load plugin settings.
      */
     function loadSettings() {
          $rpr_options_defaults = array(
  			'index_slug' => 'recipes',
  			'use_plugin_permalinks' => true,
  			'singular_name' => 'recipe',
  			'plural_name' => 'recipes',
  			'identifier' => 'recipe',
			'permalink_structure' => '%identifier%/%postname%',
			'use_categories' => true,
			'use_cuisines' => true,
			'use_servings' => true,
			'use_times' => true,
			'use_courses' => true,
			'use_seasons' => true,
			'use_thumbnails' => true,
			'use_featured' => true,
			'use_comments' => true,
			'use_trackbacks' => true,
			'use_custom_fields' => false,
			'use_revisions' => true,
			'use_post_categories' => false,
			'use_post_tags' => false,
			//taxonomies
			'taxonomies'=>array(
				'recipe-category' => array(
					'slug' => 'recipe-category',
					'singular_name' => __('Recipe Category', 'recipe-press-reloaded'),
					'plural_name' => __('Recipe Categories', 'recipe-press-reloaded'),
					'hierarchical' => true,
					'active' => true,
					'default' => false,
					'allow_multiple' => true,
					'page' => false,
					'builtin' => false,
					'per_page' => 10,
					'show_on_posts_list' => true,
					),
				'recipe-cuisine' => array(
					'slug' => 'recipe-cuisine',
					'singular_name' => __('Recipe Cuisine', 'recipe-press-reloaded'),
					'plural_name' => __('Recipe Cuisines', 'recipe-press-reloaded'),
					'hierarchical' => false,
					'active' => true,
					'default' => false,
					'allow_multiple' => true,
					'page' => false,
					'builtin' => false,
					'per_page' => 10,
					'show_on_posts_list' => true,
					),
				'recipe-course' => array(
					'slug' => 'recipe-course',
					'singular_name' => __('Course', 'recipe-press-reloaded'),
					'plural_name' => __('Courses', 'recipe-press-reloaded'),
					'hierarchical' => false,
					'active' => true,
					'default' => false,
					'allow_multiple' => true,
					'page' => false,
					'builtin' => false,
					'per_page' => 10,
					'show_on_posts_list' => true,
					),
				'recipe-season' => array(
					'slug' => 'recipe-season',
					'singular_name' => __('Season', 'recipe-press-reloaded'),
					'plural_name' => __('Seasons', 'recipe-press-reloaded'),
					'hierarchical' => false,
					'active' => true,
					'default' => false,
					'allow_multiple' => true,
					'page' => false,
					'builtin' => false,
					'per_page' => 10,
					'show_on_posts_list' => true,
					),
				'recipe-ingredient' => array(
					'slug' => 'recipe-ingredient',
					'singular_name' => __('Ingredient', 'recipe-press-reloaded'),
					'plural_name' => __('Ingredients', 'recipe-press-reloaded'),
					'hierarchical' => false,
					'active' => true,
					'default' => false,
					'allow_multiple' => true,
					'page' => false,
					'builtin' => false,
					'per_page' => 10,
					'show_on_posts_list' => false,
					),
				),
				/* Display Settings */
				'menu_position' => 5,
               	'default_excerpt_length' => 20,
               	'recipe_count' => get_option('posts_per_page'),
               	'recipe_orderby' => 'title',
               	'recipe_order' => 'asc',
               	'add_to_author_list' => false,
               	'disable_content_filter' => false,
               	'custom_css' => true,
               	'hour_text' => __(' hour', 'recipe-press-reloaded'),
               	'minute_text' => __(' min', 'recipe-press-reloaded'),
               	'time_display_type' => 'single',
               	'link_ingredients' => false,
               	//Image sizes (as a fallback if not provided by theme)
               	'image_sizes' => array(
               		'image' => array('name' => 'RPR Image', 'width' => 250, 'height' => 250, 'crop' => isset($this->rpr_options['image_sizes']['image']['crop']) ? $this->rpr_options['image_sizes']['image']['crop'] : true, 'builtin' => true),
                    'thumb' => array('name' => 'RPR Thumbnail', 'width' => 50, 'height' => 50, 'crop' => isset($this->rpr_options['image_sizes']['thumb']['crop']) ? $this->rpr_options['image_sizes']['thumb']['crop'] : true, 'builtin' => true),
               		),
               	// Non-Configurable Settings 
               'menu_icon' => RPR_URL . 'images/icons/small_logo.png',
               //To think about
               'ingredient_slug' => 'recipe-ingredients',
               'ingredients_per_page' => 10,
               'ingredient_page' => 0,
               'ingredients_fields'=>5,
               'plural_times' => true,
			);
		  $this->rpr_options_defaults = $rpr_options_defaults;
		  $this->rpr_options = wp_parse_args(get_option('rpr_options'), $rpr_options_defaults);
		  //Unfortunately wp_parse_args can't handle nested args so we do a little trick here:
		  foreach($this->rpr_options['taxonomies'] as $key => $options):
		  	$this->rpr_options['taxonomies'][$key] = wp_parse_args($options, $rpr_options_defaults['taxonomies'][$key]);
		  endforeach;

     }

     /**
      * Collect recipe details from front end form.
      *
      * @global <type> $current_user
      * @param <type> $object
      * @return <type>
      */
/*     function input($data = NULL) {
          global $current_user;
          get_currentuserinfo();

          if ( !$data ) {
               $data = $_POST;
          }

          if ( count($data) == 0 ) {
               return array('ingredients' => array());
          }
          $ingredients = array();

          if ( isset($data['ingredients']) ) {
               $ingredientArray = $data['ingredients'];

               if ( is_array($ingredientArray) ) {
                    foreach ( $ingredientArray as $id => $ingredient ) {
                         if ( $id != 'NULL' and (isset($ingredient['item']) or $ingredient['size'] == 'divider') ) {
                              $ingredients[$id] = $ingredient;
                         }
                    }
               }
          } else {
               $ingredients = array();
          }

          return array(
               'title' => @$data['title'],
               'user_id' => @$data['user_id'],
               'notes' => @$data['notes'],
               'prep_time' => @$data['prep_time'],
               'cook_time' => @$data['cook_time'],
               'ready_time' => @$this->readyTime(),
               'ready_time_raw' => @$this->readyTime(NULL, NULL, false),
               'recipe-category' => @$data['recipe-category'],
               'recipe-cuisine' => @$data['recipe-cuisine'],
               'ingredients' => @$ingredients,
               'instructions' => @$data['instructions'],
               'servings' => @$data['servings'],
               'serving_size' => @$data['serving-size'],
               'status' => @$data['status'],
               'submitter' => @$data['submitter'],
               'submitter_email' => @$data['submitter_email'],
               'updated' => time(),
          );
     }
     */

     /**
      * Method to populate default taxonomy settings.
      *
      * @param array $tax
      * @return array
      */
     function taxDefaults($tax) {
          $defaults = array(
               'default' => false,
               'hierarchical' => false,
               'active' => false,
               'delete' => false,
               'allow_multiple' => false,
               'page' => false,
               'per-page' => 10,
          );

          /* Make sure the taxonomy has the singular and plural names. */
          if ( $tax['singular'] == '' ) {
               $tax['singular'] = ucwords(rpr_inflector::humanize($tax['slug']));
          }

          if ( $tax['plural'] == '' ) {
               $tax['plural'] = rpr_inflector::plural(ucwords(rpr_inflector::humanize($tax['slug'])));
          }
          return wp_parse_args($tax, $defaults);
     }

     /**
      * Method to filter the output and add the recipe details.
      *
      * @global object $post
      * @global object $wp
      * @global object $current_user
      * @param string $content
      * @return string
      */
     function the_content_filter($content) {
          global $post, $wp, $current_user;
          get_currentuserinfo();

          $files = wp_get_theme(get_option('current_theme'));

          if ( is_single ( ) ) {
               $template_file = get_stylesheet_directory() . '/single-recipe.php';
          } elseif ( is_archive ( ) ) {
               $template_file = get_stylesheet_directory() . '/archive-recipe.php';
          } else {
               $template_file = get_stylesheet_directory() . '/index-recipe.php';
          }

          if ( $post->post_type != 'recipe' or in_array($template_file, $files['Template Files']) or $this->in_shortcode ) {
               return $content;
          }

          remove_filter('the_content', array(&$this, 'the_content_filter'));

          if ( is_archive ( ) ) {
               $template = $this->get_template('recipe-archive');
          } elseif ( is_single ( ) ) {
               $template = $this->get_template('recipe-single');
          } elseif ( $post->post_type == 'recipe' and in_the_loop() ) {
               $template = $this->get_template('recipe-loop');
          } else {
               return $content;
          }

          ob_start();
          require ($template);
          $content = ob_get_contents();
          ob_end_clean();

          add_filter('the_content', array(&$this, 'the_content_filter'));

          return $content;
     }
     
    

    

     /**
      * Retrieve a template file from either the theme or the plugin directory.
      *
      * @param <string> $template    The name of the template.
      * @return <string>             The full path to the template file.
      */
     function get_template($template = NULL, $ext = '.php', $type = 'path') {
          if ( $template == NULL ) {
               return false;
          }

          $themeFile = get_stylesheet_directory() . '/' . $template . $ext;
          $folder = '/';

          if ( !file_exists($themeFile) ) {
               $themeFile = get_stylesheet_directory() . '/recipe-press/' . $template . $ext;
               $folder = '/recipe-press/';
          }

          if ( file_exists($themeFile) and !$this->in_shortcode ) {
               if ( $type == 'url' ) {
                    $file = get_bloginfo('template_url') . $folder . $template . $ext;
               } else {
                    $file = get_stylesheet_directory() . $folder . $template . $ext;
               }
          } elseif ( $type == 'url' ) {
               $file = RPR_TEMPLATES_URL .'/'. $template . $ext;
          } else {
               $file = RPR_TEMPLATES_PATH .'/'. $template . $ext;
          }

          return $file;
     }
      
     /**
      * Get the ingredients stored in the post meta.
      *
      * @global <object> $post   If no ID is specified, use the preloaded post object.
      * @param <integer> $post   ID of the post, NOT the post object.
      * @return <array>
      */
     function getIngredients($post = NULL) {
          if ( !$post ) {
               global $post;
          }

          $ingredients = get_post_meta($post->ID, '_recipe_ingredient_value');

          if ( count($ingredients) < 1 ) {
               return $this->emptyIngredients($this->rpr_options['ingredients_fields']);
          } else {
               $ings = array();

               $defaults = array(
                    'quantity' => NULL,
                    'size' => 0,
                    'item' => 0,
                    'notes' => NULL,
                    'page-link' => NULL,
                    'url' => NULL,
                    'order' => 0
               );


               foreach ( $ingredients as $ingredient ) {
                    $ings[$ingredient['order']] = $ingredient;
                    wp_parse_args($ings[$ingredient['order']], $defaults);
               }


               ksort($ings);
               return $ings;
          }
     }

     /**
      * Return an empty array for creating ingredients form on new posts.
      *
      * @param <integer> $count
      * @return <array>
      */
     function emptyIngredients($count = 5) {
          $ingredients = array();
          for ( $ctr = 0; $ctr < $count; ++$ctr ) {
               $ingredients[$ctr]['size'] = 'none';
               $ingredients[$ctr]['item'] = 0;
          }

          return $ingredients;
     }

     /**
      * Calculate the ready time for a recipe.
      *
      * @param <integer> $prep   The prep time.
      * @param <integer> $cook   The cook time.
      * @return <string>         Formatted ready time.
      */
     function readyTime($prep = NULL, $cook = NULL, $formatted = true) {
          if ( !isset($prep) ) {
               $prep = isset($_POST['recipe_details']['recipe_prep_time']) ? $_POST['recipe_details']['recipe_prep_time'] : 0;
          }

          if ( !isset($cook) ) {
               $cook = isset($_POST['recipe_details']['recipe_cook_time']) ? $_POST['recipe_details']['recipe_cook_time'] : 0;
          }

          $hplural = '';
          $mplural = '';

          $total = $prep + $cook;

          if ( $total > 60 ) {
               $hours = floor($total / 60);

               if ( $hours > 1 and $this->rpr_options['plural_times'] )
                    $hplural = 's';
               else
                    $mplural = '';

               $hours = $hours . ' ' . $this->rpr_options['hour_text'] . $hplural . ', ';
          } else {
               $hours = '';
          }

          $mins = $total - ( $hours * 60);

          if ( $mins > 1 and $this->rpr_options['plural_times'] )
               $mplural = 's';
          else
               $mplural = '';

          if ($formatted) {
               return $hours . $mins . ' ' . $this->rpr_options['minute_text'] . $mplural;
          } else {
               return $total;
          }
     }

}

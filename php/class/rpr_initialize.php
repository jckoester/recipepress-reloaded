<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * rpr_initialize.php - Initialize the post types and taxonomies
 *
 * @package RecipePress
 * @subpackage includes
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 1.0
 */
class RPR_Init extends RPR_Core {

     static $instance;

     /**
      * Initialize the plugin
      */
	function RPR_Init() {
		global $wpdb;
        parent::RPR_Core();
		
		load_plugin_textdomain( 'recipe-press-reloaded', false, dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/language/' );
		
		if ( $this->rpr_options['use_categories'] ) {
			add_action('init', array($this, 'setup_categories'));
        }
		if ( $this->rpr_options['use_cuisines'] ) {  
			add_action('init', array($this, 'setup_cuisines'));
		}
		if ( $this->rpr_options['use_courses'] ) {
			add_action('init', array(&$this, 'setup_courses'));
		}
		if ( $this->rpr_options['use_seasons'] ) {
			add_action('init', array(&$this, 'setup_seasons'));
		}
               
		add_action('init', array(&$this, 'setup_ingredients'));
		add_action('init', array(&$this, 'setup_sizes'));
		add_action('init', array(&$this, 'setup_serving_sizes'));

		add_action('init', array(&$this, 'create_post_type'));

		/* WordPress Filters */
		add_filter('index_template', array($this, 'index_template'), 10, 1);
		add_filter('home_template', array($this, 'index_template'), 10, 1);
		add_filter('archive_template', array($this, 'archive_template'), 10, 1);

		/* Use built in categories and tags. */
		if ( $this->rpr_options['use_post_categories'] ) {
			register_taxonomy_for_object_type('post_categories', 'recipe');
		}

		if ( $this->rpr_options['use_post_tags'] ) {
			register_taxonomy_for_object_type('post_tag', 'recipe');
		}
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
               self::$instance = new RPR_Init;
          }
          return self::$instance;
     }

    

     /**
      * Create the post type.
      *
      * @global object $wp_rewrite
      */
     function create_post_type() {
          global $wp_version;

          $page = get_page($this->rpr_options['display_page']);
          $labels = array(
               'name' => $this->rpr_options['plural_name'],
               'singular_name' => $this->rpr_options['singular_name'],
               'add_new' => __('Add New', 'recipe-press-reloaded'),
               'add_new_item' => sprintf(__('Add New %1$s', 'recipe-press-reloaded'), $this->rpr_options['singular_name']),
               'edit_item' => sprintf(__('Edit %1$s', 'recipe-press-reloaded'), $this->rpr_options['singular_name']),
               'edit' => __('Edit', 'recipe-press-reloaded'),
               'new_item' => sprintf(__('New %1$s', 'recipe-press-reloaded'), $this->rpr_options['singular_name']),
               'view_item' => sprintf(__('View %1$s', 'recipe-press-reloaded'), $this->rpr_options['singular_name']),
               'search_items' => sprintf(__('Search %1$s', 'recipe-press-reloaded'), $this->rpr_options['singular_name']),
               'not_found' => sprintf(__('No %1$s found', 'recipe-press-reloaded'), $this->rpr_options['plural_name']),
               'not_found_in_trash' => sprintf(__('No %1$s found in Trash', 'recipe-press-reloaded'), $this->rpr_options['plural_name']),
               'view' => sprintf(__('View %1$s', 'recipe-press-reloaded'), $this->rpr_options['singular_name']),
               'parent_item' => sprintf(__('Parent %1$s', 'recipe-press-reloaded'), $this->rpr_options['singular_name']),
               'parent_item_colon' => sprintf(__('Parent %1$s:', 'recipe-press-reloaded'), $this->rpr_options['singular_name']),
          );
          $args = array(
               'labels' => $labels,
               'public' => true,
               'publicly_queryable' => true,
               'show_ui' => true,
               'query_var' => true,
               'capability_type' => 'page',
               'hierarchical' => false,
               'menu_position' => (int) $this->rpr_options['menu_position'],
               'menu_icon' => $this->rpr_options['menu_icon'],
               'supports' => array('title', 'editor', 'author', 'excerpt', 'page-attributes'),
               'register_meta_box_cb' => array(&$this, 'init_metaboxes'),
          );

          if ( $this->rpr_options['use_custom_fields'] ) {
               $args['supports'][] = 'custom-fields';
          }

          if ( $this->rpr_options['use_thumbnails'] ) {
               $args['supports'][] = 'thumbnail';
          }

          if ( $this->rpr_options['use_comments'] ) {
               $args['supports'][] = 'comments';
          }

          if ( $this->rpr_options['use_trackbacks'] ) {
               $args['supports'][] = 'trackbacks';
          }

          if ( $this->rpr_options['use_revisions'] ) {
               $args['supports'][] = 'revisions';
          }

          if ( $this->rpr_options['use_post_tags'] ) {
               $args['taxonomies'][] = 'post_tag';
          }

          if ( $this->rpr_options['use_post_categories'] ) {
               $args['taxonomies'][] = 'category';
          }

        //  if (  !$this->options['use-plugin-permalinks'] ) {
               $args['rewrite'] = true;
               $args['has_archive'] = $this->rpr_options['index_slug'];
         // }
          
          register_post_type('recipe', $args);
     }

     /**
      * Handle index pages for the recipe post type.
      *
      * @global object $post
      * @param string $template
      * @return string
      */
     public function index_template($template) {
          global $post, $wp_query, $taxonomy, $terms, $tax, $pagination, $recipeData, $current_user;

          /* Handle Taxonomy Page */
          if ( $taxonomy = get_query_var('recipe-taxonomy') ) {
               $page = get_query_var('page');

               $replacement_template = get_query_template('taxonomy-recipe');
               if ( file_exists($replacement_template) ) {
                    $atts = array(
                         'taxonomy' => $taxonomy,
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
                         'include' => get_published_categories($taxonomy)
                    );

                    $tax = get_taxonomy($taxonomy);

                    /* Count all terms */
                    $atts['fields'] = 'ids';
                    $all_terms = get_terms($atts['taxonomy'], $atts);

                    if ( $taxonomy == 'recipe-ingredient' ) {
                         $pagination = array(
                              'total' => count($all_terms),
                              'pages' => ceil(count($all_terms) / $this->rpr_options['ingredients_per_page']),
                              'current-page' => max($page, 1),
                              'taxonomy' => __('Ingredients', 'recipe-press-reloaded'),
                              'url' => get_option('home') . '/' . $this->rpr_options['ingredient_slug'],
                              'per-page' => $this->rpr_options['ingredients_per_page']
                         );
                    } else {
                         $this->rpr_options['taxonomies'][$taxonomy] = $this->taxDefaults($this->rpr_options['taxonomies'][$taxonomy]);

                         $pagination = array(
                              'total' => count($all_terms),
                              'pages' => ceil(count($all_terms) / $this->rpr_options['taxonomies'][$taxonomy]['per-page']),
                              'current-page' => max($page, 1),
                              'taxonomy' => $this->rpr_options['taxonomies'][$taxonomy]['plural'],
                              'url' => get_option('home') . '/' . $this->rpr_options['taxonomies'][$taxonomy]['slug'],
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

                    $terms = get_terms($atts['taxonomy'], $atts);
                    add_filter('wp_title', array(&$this, 'recipe_taxonomy_page_title'));
                    return $replacement_template;
               } else {
                    $taxonomy = get_query_var('recipe-taxonomy');

                    if ( $taxonomy == 'recipe-ingredient' ) {
                         $pageID = $this->rpr_options['ingredient_page'];
                    } else {
                         $pageID = $this->rpr_options['taxonomies'][$taxonomy]['page'];
                    }

                    if ( $pageID and get_page($pageID) ) {
                         wp_redirect(get_permalink($pageID));
                    } else {
                         wp_redirect(get_option('home') . '/' . $this->rpr_options['index_slug']);
                    }
               }
          }

          if ( is_object($post) and $post->post_type == 'recipe' and $replacement_template = get_query_template('index-recipe') ) {
               return $replacement_template;
          } else {
               return $template;
          }
     }

     /**
      * Handle archive pages for the recipe post type
      *
      * @global object $post
      * @param string $template
      * @return string
      */
     public function archive_template($template) {
          global $post;
          if ( is_object($post) and $post->post_type == 'recipe' and $replacement_template = get_query_template('archive-recipe') ) {
               return $replacement_template;
          } else {
               return $template;
          }
     }


     /**
      * Filter to correct the title on index pages when using template files.
      *
      * This function can be overriden by adding a function named recipe_taxonomy_page_title
      * in your themes function file. The function will receive the generated title as an
      * argument. You need to return the text to display in the title.
      *
      * @param string $title
      * @return string
      */
     function recipe_taxonomy_page_title($title) {
          if ( function_exists('recipe_taxonomy_page_title') ) {
               return recipe_taxonomy_page_title($title);
          } else {
               if ( get_query_var('recipe-taxonomy') == 'recipe-ingredient' ) {
                    $title = __('Recipe Ingredients', 'recipe-press-reloaded');
               } else {
                    $title = $this->rpr_options['taxonomies'][get_query_var('recipe-taxonomy')]['plural'];
               }
               return $title . ' | ' . get_bloginfo('name');
          }
     }


     /**
      * Permalink handling for post_type
      *
      * @param string $permalink
      * @param object $post
      * @param bool $leavename
      * @return string
      */
     public function post_link($permalink, $id, $leavename = false) {
          if ( is_object($id) && isset($id->filter) && 'sample' == $id->filter ) {
               $post = $id;
          } else {
               $post = &get_post($id);
          }

          if ( empty($post->ID) || $post->post_type != 'recipe' )
               return $permalink;

          $rewritecode = array(
               '%identifier%',
               '%year%',
               '%monthnum%',
               '%day%',
               '%hour%',
               '%minute%',
               '%second%',
               $leavename ? '' : '%postname%',
               '%post_id%',
               '%category%',
               '%author%',
               $leavename ? '' : '%pagename%',
          );

          $permastructure = array('identifier' => $this->rpr_options['identifier'], 'structure' => $this->rpr_options['permalink']);
          $identifier = $permastructure['identifier'];
          $permalink = $permastructure['structure'];
          if ( '' != $permalink && get_option('permalink_structure') && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
               $unixtime = strtotime($post->post_date);

               $category = '';
               if ( strpos($permalink, '%category%') !== false ) {
                    $cats = get_the_category($post->ID);
                    if ( $cats ) {
                         usort($cats, '_usort_terms_by_ID'); // order by ID
                         $category = $cats[0]->slug;
                         if ( $parent = $cats[0]->parent )
                              $category = get_category_parents($parent, false, '/', true) . $category;
                    }
                    /* show default category in permalinks, without having to assign it explicitly */
                    if ( empty($category) ) {
                         $default_category = get_category(get_option('default_category'));
                         $category = is_wp_error($default_category) ? '' : $default_category->slug;
                    }
               }

               $author = '';
               if ( strpos($permalink, '%author%') !== false ) {
                    $authordata = get_userdata($post->post_author);
                    $author = $authordata->user_nicename;
               }

               $date = explode(" ", date('Y m d H i s', $unixtime));
               $rewritereplace =
                       array(
                            $identifier,
                            $date[0],
                            $date[1],
                            $date[2],
                            $date[3],
                            $date[4],
                            $date[5],
                            $post->post_name,
                            $post->ID,
                            $category,
                            $author,
                            $post->post_name,
               );
               $permalink = home_url(str_replace($rewritecode, $rewritereplace, $permalink));
               $permalink = user_trailingslashit($permalink, 'single');
          } else {
               $permalink = home_url('?p=' . $post->ID . '&post_type=' . urlencode('recipe'));
          }
          return $permalink;
     }


     function taxonomy_rewrite_rules($taxonomy, $settings) {
          global $wp_rewrite;
          $type_query_var = $settings['slug'];
          //$structure = str_replace('%identifier%', $permastructure['identifier'], $structure);
          $rewrite_rules = $wp_rewrite->generate_rewrite_rules($settings['slug'], EP_NONE, true, true, true, true, true);
          $rewrite_rules[$settings['slug'] . '/?$'] = 'index.php?paged=1';

          foreach ( $rewrite_rules as $regex => $redirect ) {
               if ( strpos($redirect, 'attachment=') === false ) {
                    /* don't set the post_type for attachments */
                    $redirect .= '&post_type=recipe&recipe-taxonomy=' . $taxonomy;
               }

               if ( 0 < preg_match_all('@\$([0-9])@', $redirect, $matches) ) {
                    for ( $i = 0; $i < count($matches[0]); $i++ ) {
                         $redirect = str_replace($matches[0][$i], '$matches[' . $matches[1][$i] . ']', $redirect);
                    }
               }

               $redirect = str_replace('name=', $type_query_var . '=', $redirect);

               add_rewrite_rule($regex, $redirect, 'top');
          }
     }
     
      /**
      * Set up courses.
      */
     function setup_courses() {
		$rewrite = array('slug' => 'recipe-course', 'with_front' => true);
        
        $labels = array(
               'name' => __('Courses', 'recipe-press-reloaded'),
               'singular_name' => __('Course', 'recipe-press-reloaded'),
               'search_items' => __('Search Courses', 'recipe-press-reloaded'),
               'popular_items' => __('Popular Courses', 'recipe-press-reloaded'),
               'all_items' => __('All Courses', 'recipe-press-reloaded'),
               'parent_item' => __('Parent Course', 'recipe-press-reloaded'),
               'edit_item' => __('Edit Course', 'recipe-press-reloaded'),
               'update_item' => __('Update Course', 'recipe-press-reloaded'),
               'add_new_item' => __('Add Course', 'recipe-press-reloaded'),
               'new_item_name' => __('New Course', 'recipe-press-reloaded'),
               'add_or_remove_items' => __('Add or remove Courses', 'recipe-press-reloaded'),
               'choose_from_most_used' => __('Choose from the most used Courses', 'recipe-press-reloaded'),
          );
          $args = array(
               'hierarchical' => true,
              // 'allow_multiple'=>true,
               'label' => __('Courses', 'recipe-press-reloaded'),
               'labels' => $labels,
               'public' => true,
               'show_ui' => true,
               'update_count_callback' => '_update_post_term_count',
               'query_var'=>true,
               'rewrite' => array('slug' => 'recipe-course'),
          );

          register_taxonomy('recipe-course', array('recipe'), $args);
//                    $this->taxonomy_rewrite_rules($key, $taxonomy);
     }
     
     /**
      * Set up cuisines.
      */
     function setup_cuisines() {
		$rewrite = array('slug' => 'recipe-cuisines', 'with_front' => true);
        
        $labels = array(
               'name' => __('Cuisines', 'recipe-press-reloaded'),
               'singular_name' => __('Cuisine', 'recipe-press-reloaded'),
               'search_items' => __('Search Cuisines', 'recipe-press-reloaded'),
               'popular_items' => __('Popular Cuisines', 'recipe-press-reloaded'),
               'all_items' => __('All Cuisines', 'recipe-press-reloaded'),
               'parent_item' => __('Parent Cuisine', 'recipe-press-reloaded'),
               'edit_item' => __('Edit Cuisine', 'recipe-press-reloaded'),
               'update_item' => __('Update Cuisine', 'recipe-press-reloaded'),
               'add_new_item' => __('Add Cuisine', 'recipe-press-reloaded'),
               'new_item_name' => __('New Cuisine', 'recipe-press-reloaded'),
               'add_or_remove_items' => __('Add or remove Cuisines', 'recipe-press-reloaded'),
               'choose_from_most_used' => __('Choose from the most used Cuisines', 'recipe-press-reloaded'),
          );
          $args = array(
               'hierarchical' => true,
               'label' => __('Cuisines', 'recipe-press-reloaded'),
               'labels' => $labels,
               'public' => true,
               'show_ui' => true,
               'update_count_callback' => '_update_post_term_count',
               'query_var'=>true,
               'rewrite' => array('slug' => 'recipe-cuisine'),
          );

          register_taxonomy('recipe-cuisine', array('recipe'), $args);
     }
     
      /**
      * Set up catgories.
      */
     function setup_categories() {
		$rewrite = array('slug' => 'recipe-categories', 'with_front' => true);
        
        $labels = array(
               'name' => __('Categories', 'recipe-press-reloaded'),
               'singular_name' => __('Category', 'recipe-press-reloaded'),
               'search_items' => __('Search Categories', 'recipe-press-reloaded'),
               'popular_items' => __('Popular Category', 'recipe-press-reloaded'),
               'all_items' => __('All Categories', 'recipe-press-reloaded'),
               'parent_item' => __('Parent Category', 'recipe-press-reloaded'),
               'edit_item' => __('Edit Category', 'recipe-press-reloaded'),
               'update_item' => __('Update Category', 'recipe-press-reloaded'),
               'add_new_item' => __('Add Category', 'recipe-press-reloaded'),
               'new_item_name' => __('New Category', 'recipe-press-reloaded'),
               'add_or_remove_items' => __('Add or remove Categories', 'recipe-press-reloaded'),
               'choose_from_most_used' => __('Choose from the most used Categories', 'recipe-press-reloaded'),
          );
          $args = array(
               'hierarchical' => true,
               'label' => __('Categories', 'recipe-press-reloaded'),
               'labels' => $labels,
               'public' => true,
               'show_ui' => true,
               'allow_multiple'=>true,
               //'update_count_callback' => '_update_post_term_count',
               'query_var'=>true,
               'rewrite' => array('slug' => 'recipe-category'),
          );

          register_taxonomy('recipe-category', array('recipe'), $args);
     }
    
     /**
      * Set up seasons.
      */
     function setup_seasons() {
		$rewrite = array('slug' => 'recipe-season', 'with_front' => true);
        
        $labels = array(
               'name' => __('Seasons', 'recipe-press-reloaded'),
               'singular_name' => __('Season', 'recipe-press-reloaded'),
               'search_items' => __('Search Seasons', 'recipe-press-reloaded'),
               'popular_items' => __('Popular Seasons', 'recipe-press-reloaded'),
               'all_items' => __('All Seasons', 'recipe-press-reloaded'),
               'parent_item' => __('Parent Season', 'recipe-press-reloaded'),
               'edit_item' => __('Edit Season', 'recipe-press-reloaded'),
               'update_item' => __('Update Season', 'recipe-press-reloaded'),
               'add_new_item' => __('Add Season', 'recipe-press-reloaded'),
               'new_item_name' => __('New Seasons', 'recipe-press-reloaded'),
               'add_or_remove_items' => __('Add or remove Seasons', 'recipe-press-reloaded'),
               'choose_from_most_used' => __('Choose from the most used Seasons', 'recipe-press-reloaded'),
          );
          $args = array(
               'hierarchical' => false,
               'label' => __('Seasons', 'recipe-press-reloaded'),
               'labels' => $labels,
               'public' => true,
               'show_ui' => true,
               //'update_count_callback' => '_update_post_term_count',
               /*'capabilities' => array(
                    'assign_terms' => false
               ),*/
               'query_var'=>true,
               'rewrite' => array('slug' => 'recipe-season'),
          );

          register_taxonomy('recipe-season', array('recipe'), $args);
     }

     /**
      * Setup sizes taxonomy.
      */
     function setup_sizes() {
          $labels = array(
               'name' => __('Sizes', 'recipe-press-reloaded'),
               'singular_name' => __('Size', 'recipe-press-reloaded'),
               'search_items' => __('Search Sizes', 'recipe-press-reloaded'),
               'popular_items' => __('Popular Sizes', 'recipe-press-reloaded'),
               'all_items' => __('All Sizes', 'recipe-press-reloaded'),
               'parent_item' => __('Parent Size', 'recipe-press-reloaded'),
               'edit_item' => __('Edit Size', 'recipe-press-reloaded'),
               'update_item' => __('Update Size', 'recipe-press-reloaded'),
               'add_new_item' => __('Add Size', 'recipe-press-reloaded'),
               'new_item_name' => __('New Size', 'recipe-press-reloaded'),
               'add_or_remove_items' => __('Add or remove Sizes', 'recipe-press-reloaded'),
               'choose_from_most_used' => __('Choose from the most used Sizes', 'recipe-press-reloaded'),
          );

          $args = array(
               'hierarchical' => false,
               'label' => __('Sizes', 'recipe-press-reloaded'),
               'labels' => $labels,
               'public' => true,
               'show_ui' => true,
               'capabilities' => array(
                    'assign_terms' => false
               ),
               'rewrite' => array('slug' => 'recipe-size'),
          );

          register_taxonomy('recipe-size', array('recipe'), $args);
     }

     /**
      * Setup serving sizes taxonomy.
      */
     function setup_serving_sizes() {
          $labels = array(
               'name' => __('Serving Sizes', 'recipe-press-reloaded'),
               'singular_name' => __('Serving Size', 'recipe-press-reloaded'),
               'search_items' => __('Search Serving Sizes', 'recipe-press-reloaded'),
               'popular_items' => __('Popular Serving Sizes', 'recipe-press-reloaded'),
               'all_items' => __('All Serving Sizes', 'recipe-press-reloaded'),
               'parent_item' => __('Parent Serving Size', 'recipe-press-reloaded'),
               'edit_item' => __('Edit Serving Size', 'recipe-press-reloaded'),
               'update_item' => __('Update Serving Size', 'recipe-press-reloaded'),
               'add_new_item' => __('Add Serving Size', 'recipe-press-reloaded'),
               'new_item_name' => __('New Serving Size', 'recipe-press-reloaded'),
               'add_or_remove_items' => __('Add or remove Serving Sizes', 'recipe-press-reloaded'),
               'choose_from_most_used' => __('Choose from the most used Serving Sizes', 'recipe-press-reloaded'),
          );

          $args = array(
               'hierarchical' => false,
               'label' => __('Serving Sizes', 'recipe-press-reloaded'),
               'labels' => $labels,
               'public' => true,
               'show_ui' => true,
               'capabilities' => array(
                    'assign_terms' => false
               ),
               'rewrite' => array('slug' => 'recipe-serving'),
          );

          register_taxonomy('recipe-serving', array('recipe'), $args);

          return true;
     }

     /**
      * Setup ingredients taxonomy.
      */
     function setup_ingredients() {
          $labels = array(
               'name' => __('Ingredients', 'recipe-press-reloaded'),
               'singular_name' => __('Ingredient', 'recipe-press-reloaded'),
               'search_items' => __('Search Ingredients', 'recipe-press-reloaded'),
               'popular_items' => __('Popular Ingredients', 'recipe-press-reloaded'),
               'all_items' => __('All Ingredients', 'recipe-press-reloaded'),
               'parent_item' => __('Parent Ingredient', 'recipe-press-reloaded'),
               'edit_item' => __('Edit Ingredient', 'recipe-press-reloaded'),
               'update_item' => __('Update Ingredient', 'recipe-press-reloaded'),
               'add_new_item' => __('Add Ingredient', 'recipe-press-reloaded'),
               'new_item_name' => __('New Ingredient', 'recipe-press-reloaded'),
               'add_or_remove_items' => __('Add or remove Ingredients', 'recipe-press-reloaded'),
               'choose_from_most_used' => __('Choose from the most used Ingredients', 'recipe-press-reloaded'),
          );

          $args = array(
               'hierarchical' => false,
               'label' => __('Ingredients', 'recipe-press-reloaded'),
               'labels' => $labels,
               'public' => true,
               'show_ui' => true,
               'capabilities' => array(
                    'assign_terms' => false
               ),
               'rewrite' => array('slug' => 'ingredient'),
          );

          register_taxonomy('recipe-ingredient', array('recipe'), $args);
          $this->taxonomy_rewrite_rules('recipe-ingredient', array('slug' => $this->rpr_options['ingredient_slug']));

          return true;
     }


     
     /**
      * Adds additional meta boxes to the recipe edit screen.
      */
     function init_metaboxes() {
     	//Remove boxes for sizes, serving-sizes, ingredents from sidebar
     	  remove_meta_box('tagsdiv-recipe-size', 'recipe', 'side');
     	  remove_meta_box('tagsdiv-recipe-serving', 'recipe', 'side');
     	  remove_meta_box('tagsdiv-recipe-ingredient', 'recipe', 'side');
          add_meta_box('recipes_ingredients', __('Ingredients', 'recipe-press-reloaded'), array(&$this, 'ingredients_box'), 'recipe', 'advanced', 'high');
          add_meta_box('recipes_details', __('Details', 'recipe-press-reloaded'), array(&$this, 'details_box'), 'recipe', 'side', 'high');
     }

     /**
      * Sets up the box for entering ingredients.
      */
     function ingredients_box() {
          /* Use nonce for verification */
          echo '<input type="hidden" name="ingredients_noncename" id="ingredients_noncename" value="' . wp_create_nonce('recipe_press_ingredients') . '" />';
          include(RPR_PATH . 'php/form/ingredient-form.php');
     }

     /**
      * Sets up the box for the recipe details.
      *
      * @global object $post
      */
     function details_box() {
          global $post;
          /* Use nonce for verification */
          echo '<input type="hidden" name="details_noncename" id="details_noncename" value="' . wp_create_nonce('recipe_press_details') . '" />';
          include (RPR_PATH . 'php/form/details-form.php');
     }

     

}

<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/public
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Public {

    /**
     * The version of this plugin.
     *
     * @since    0.8.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;


    private $modules;
    /**
     * Initialize the class and set its properties.
     *
     * @since    0.8.0
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $version, $modules ) {

        $this->version = $version;

        $this->modules = $modules;

		/**
		 * @todo: Is this the right place?
		 */
		// Include Template Tags
		// include_once( dirname( __FILE__ ) . '/rpr_template_tags.php' );

		// Include the layout's functions.php
		// Get the layout chosen:
		$layout = AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'layout' ) , 'rpr_default' );
		// calculate the includepath for the layout:
		// Check if a global or local layout should be used:
		if( strpos( $layout, 'local') !== false ){
			//Local layout
			$includepath = get_stylesheet_directory() . '/rpr_layouts/'. preg_replace('/^local\_/', '', $layout ) . '/functions.php';
		} else {
			//Global layout
			$includepath = plugin_dir_path( __FILE__ )  . 'layouts/' . $layout . '/functions.php';
		}

		// Check if the layout file really exists
		if( file_exists( $includepath ) ){
			// Include the functions.php
			include_once( $includepath );
		}
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.8.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in RPR_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The RPR_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style( 'recipepress-reloaded', plugin_dir_url( __FILE__ ) . 'css/rpr-public.css', array (), $this->version, 'all' );

		/* Font Awesome style */
        wp_enqueue_style( 'recipepress-reloaded' . '-fa', plugin_dir_url( dirname( __FILE__ ) ) . 'libraries/font-awesome/css/font-awesome.min.css', array (), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    0.8.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in RPR_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The RPR_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script( 'recipepress-reloaded', plugin_dir_url( __FILE__ ) . 'js/rpr-public.js', array ( 'jquery' ), $this->version, true );
    }

    // Add Widgets
    /**
     *
     * @todo Documentation
     *
     */
    public function register_widgets() {
        if( AdminPageFramework::getOption( 'rpr_options', array( 'general', 'use_taxcloud_widget' ) , true ) ){
            require_once( dirname( __FILE__ ) . '/..' . '/widgets/class-rpr-widget-tag-cloud.php');
            register_widget( 'RPR_Widget_Tag_Cloud' );
            unregister_widget( 'WP_Widget_Tag_Cloud' );
        }
	if( AdminPageFramework::getOption( 'rpr_options', array( 'general', 'use_taxlist_widget' ) , true ) ){
            require_once( dirname( __FILE__ ) . '/..' .  '/widgets/class-rpr-widget-taxonomy-list.php');
            register_widget( 'RPR_Widget_Taxonomy_List' );
        }
    }

    /**
     * Manipulate the wordpress query to include recipes to homepage if set in options
     *
     * @since 0.8.0
     *
     * @param type $query Wordpress Querxy object
     */
    public function query_recipes( $query ) {
        // Don't change query on admin page
    	if (is_admin()){
    		return;
    	}

		// Check on all public pages
		if ( ! is_admin() && $query->is_main_query() ) {
			// Post archive page:
			if ( is_post_type_archive( 'rpr_recipe' ) ) {
				// set post type to only recipes
				$query->set('post_type', 'rpr_recipe' );
				return;
			}
			// Homepage
			if( AdminPageFramework::getOption( 'rpr_options', array( 'general', 'homepage_display' ) , true ) ){
				if( is_home() || $query->is_home() || $query->is_front_page() ){
					$this->add_recipe_to_query($query);
				}
			}
			// All other pages:
			if( is_category() || is_tag() || is_author() ){
				$this->add_recipe_to_query($query);
				return;
			}
		}
		return;
    }

	/**
	 * Function to savely change the query and add recipes to query object
	 *
	 * @since 0.8.0
	 *
	 * @param type $query
	 * @return type none
	 */
	private function add_recipe_to_query($query){
		// add post type to query
		$post_type = $query->get('post_type');

        if( is_array( $post_type ) && ! array_key_exists( 'rpr_recipe', $post_type ) ){
        	$post_type[] = 'rpr_recipe';
        } else {
        	$post_type = array( 'post', $post_type, 'rpr_recipe' );
        }

		$query->set( 'post_type', $post_type );
		return;
	}

	/**
	 * Manipulate the query for the rss feed to add recipes
	 *
	 * @since 0.8.0
	 *
	 * @param object $query
	 * @return object $query
	 */
	public function add_recipes_to_feed( $query ) {

		if( AdminPageFramework::getOption( 'rpr_options', array( 'general', 'homepage_display' ) , true ) ){
			if ( isset( $query['feed'] ) && !isset( $query['post_type'] ) ) {
	        	$query['post_type'] = array('post', 'rpr_recipe');
			}
		}
	    return $query;
	}

	/**
	 * Get the rendered excerpt of a recipe and forward it to the theme as the_excerpt()
	 * Same work is done by get_recipe_content, however some theme specifically include $post->excerpt,
	 * then content is renderd by this function
	 *
	 * @since 0.8.0
	 * @param string $content
	 * @return string $content
	 */
	public function get_recipe_excerpt( $content ) {
        if (!in_the_loop () || !is_main_query ()) {
            return $content;
        }

	/* Only render specifically if we have a recipe */
        if ( get_post_type() == 'rpr_recipe' ) {
            remove_filter('get_the_excerpt', array( $this, 'get_recipe_excerpt' ), 10);
            $recipe_post = get_post();

            $content = rpr_render_recipe_excerpt( $recipe_post );
            return $content;
            add_filter('get_the_excerpt', array( $this, 'get_recipe_excerpt' ), 10);
        } else {
            return $content;
        }
    }

    /**
     * Get the rendered content of a recipe and forward it to the theme as the_content()
     *
     * @since 0.8.0
     * @param string $content
     * @return string $content
     */
    public function get_recipe_content( $content ) {
	if ( !in_the_loop () || !is_main_query () ) {
            return $content;
        }

        /* Only render specifically if we have a recipe */
        if ( get_post_type() == 'rpr_recipe' ) {
            // Remove the filter
            remove_filter( 'the_content', array( $this, 'get_recipe_content' ) );

            // Do the stuff
            $recipe_post = get_post();
            $recipe = get_post_custom($recipe_post->ID);
            $GLOBALS['recipe_id'] = $recipe_post->ID;

            rpr_include_template_tags();

            if (is_single() || AdminPageFramework::getOption( 'rpr_options', array( 'general', 'archive_display' ) , true ) === 'full') {
                $content = rpr_render_recipe_content( $recipe_post );
            } else {

                $content = rpr_render_recipe_excerpt( $recipe_post );
            }

            // Add the filter again
            add_filter('the_content', array( $this, 'get_recipe_content' ), 10);

            // return the rendered content
            return $content;
        } else {
            return $content;
        }
    }

	/**
	 * Do the actual rendering using the excerpt.php file provided by the layout
	 *
	 * @since 0.8.0
	 * @param object $recipe_post
	 * @return string $content
	 */
/*	private function render_recipe_excerpt( $recipe_post ) {

            // Return if we are on a single post page:
            if( is_single() ) {return;}
		// Get the layot's includepath
		$includepath = $this->get_the_layout() . 'excerpt.php';

		// Check if the layout file really exists
		if( !file_exists( $includepath ) ){
			// If the layout does not provide an excerpt file, use the default one:
			$includepath = plugin_dir_path( __FILE__ ) . 'layouts/rpr_default/excerpt.php';
		}

		// Get the recipe data:
		$recipe = get_post_custom($recipe_post->ID);
		//$content = $this->get_recipes_content($recipe_post);

		// Start rendering
		ob_start();

                // Include the common template tags:
		include_once( dirname( __FILE__ ) . '/rpr_template_tags.php' );
                // include the module's template tags:
                $this->include_module_template_tags();
                // Include the excerpt file:
		include( $includepath);
		// and render the content using that file:
		$content = ob_get_contents();

		// Finish rendering
		ob_end_clean();

		// return the rendered content:
		return $content;

	}*/

	/**
	 * Do the actual rendering using the recipe.php file provided by the layout
	 *
	 * @since 0.8.0
	 * @param object $recipe_post
	 * @return string $content
	 */
	/*private function render_recipe_content( $recipe_post ) {
		// Get the layout's includepath
		$includepath = $this->get_the_layout() . 'recipe.php';

		if( !file_exists( $includepath ) ){
			// If the layout does not provide an recipe file, use the default one:
			// This NEVER should happen, but who knows...
			$includepath = plugin_dir_path( __FILE__ ) . 'layouts/rpr_default/recipe.php';
		}

		// Get the recipe data:
		$recipe = get_post_custom($recipe_post->ID);

                // Create structured data as json-LD
                // TODO: Comments are missing!
                $json = array(
                    '@context' => 'http://schema.org',
                    '@type' => 'Recipe',
                    'name' => get_the_title( $recipe_post ),
                    'author' => get_the_author()
                );
                foreach ($this->modules as $module ){
                    if( is_a( $module, 'RPR_Module_Metabox' ) ){
                        $modjson = $module->get_structured_data( $recipe_post->ID, $recipe );
                        if( is_array($modjson) && count($modjson) > 0 ){
                            $json = array_merge($json, $modjson);
                        }
                    }
                }
                echo '<script type="application/ld+json">' . wp_json_encode($json) . '</script>';
//
//                echo "<pre>";
//                //var_dump($json);
//                echo wp_json_encode($json);
//                echo "</pre>";

		// Start rendering
		ob_start();
		// Include the common template tags:
		include_once( dirname( __FILE__ ) . '/rpr_template_tags.php' );
                // include the module's template tags:
                $this->include_module_template_tags();
		// Include the recipe template file:
                include( $includepath );
		// and render the content using that file:
		$content = ob_get_contents();

		// Finish rendering
		ob_end_clean();

		// return the rendered content:
		return $content;
	}
*/
        /**
         * Include the template tags files from alle the modules
         * @since 1.0.0
         */
/*        private function include_module_template_tags() {
            foreach(  $this->modules as $module_id => $module ){
                    if(is_a($module, 'RPR_Module_Metabox' ) ){
                        $mod_includepath = plugin_dir_path( __FILE__ ) . '../modules/' . $module_id . '/template_tags.php';
                        if(file_exists( $mod_includepath ) ){
                            include_once $mod_includepath;
                        }
                    }
                }
        }
*/
  // /**
	//  * Render a list of all terms of a taxonomy using the layout's taxonomy.php file
	//  *
	//  * @since 0.8.0
	//  * @param type $taxonomy
	//  * @param type $headers
	//  * @return string $content
	//  */
	// private function render_taxlist( $taxonomy, $headers=false ) {
	// 	/**
	// 	 * Create empty output variable
	// 	 */
	// 	$output = '';
  //
	// 	// Get the layout's includepath
	// 	$includepath = $this->get_the_layout() . 'taxonomy.php';
  //
	// 	if( !file_exists( $includepath ) ){
	// 		// If the layout does not provide an taxonomy file, use the default one:
	// 		$includepath = plugin_dir_path( __FILE__ ) . 'layouts/rpr_default/taxonomy.php';
	// 	}
  //
	// 	/**
	// 	 * Set recipe_post to false for template tags
	// 	 */
	// 	$recipe_post = false;
  //
	// 	if( $taxonomy != 'n/a' && $taxonomy != '' ){
	// 		/**
	// 		 * get the terms of the selected taxonomy
	// 		 */
	// 		$terms = get_terms( $taxonomy, array( 'orderby'=> 'name', 'order' => 'ASC' ) );
	// 	} else {
	// 		/**
	// 		 * Set $terms to false for the layout and it's error messages
	// 		 */
	// 		$terms = false;
	// 	}
  //
  //               // Include the common template tags:
	// 	include_once( dirname( __FILE__ ) . '/rpr_template_tags.php' );
  //               // include the module's template tags:
  //               foreach(  $this->modules as $module_id => $module ){
  //                   $mod_includepath = plugin_dir_path( __FILE__ ) . '../modules/' . $module_id . '/template_tags.php';
  //                   if(file_exists( $mod_includepath ) ){
  //                       include_once $mod_includepath;
  //                   }
  //               }
	// 	// Include the taxonomy file:
	// 	include( $includepath );
	// 	// and render the content using that file:
	// 	$content = ob_get_contents();
  //
	// 	// Finish rendering
	// 	ob_end_clean();
  //
	// 	// return the rendered content:
	// 	return $content;
	// }
  //
	// /**
	//  * Render a list of all recipes alphabetically using the layout's recipe_index.php file
	//  *
	//  * @since 0.8.0
	//  * @param type $headers
	//  * @return string $content
	//  */
	// private function render_recipe_index( $headers=false ) {
	// 	/**
	// 	 * Create empty output variable
	// 	 */
	// 	$output = '';
  //
	// 	// Get the layout's includepath
	// 	$includepath = $this->get_the_layout() . 'recipe_index.php';
  //
	// 	if( !file_exists( $includepath ) ){
	// 		// If the layout does not provide an taxonomy file, use the default one:
	// 		$includepath = plugin_dir_path( __FILE__ ) . 'layouts/rpr_default/recipe_index.php';
	// 	}
  //
	// 	/**
	// 	 * Set recipe_post to false for template tags
	// 	 */
	// 	$recipe_post = false;
  //
	// 	/**
	// 	 * Get an alphabetically ordered list of all recipes
	// 	 */
	// 	$args = array(
  //           'post_type' => 'rpr_recipe',
  //           'post_status' => 'publish',
  //           'orderby' => 'post_title',
  //           'order' => 'ASC',
  //           'posts_per_page' => -1,
  //       );
  //       $query = new WP_Query( $args );
  //       $posts = array();
  //
  //       if( $query->have_posts() ) { //recipes found
  //           while( $query->have_posts() ) {
  //               $query->the_post();
  //               global $post;
  //               $posts[] = $post;
  //           }
  //       }
  //
	// 	// Include the taxonomy file:
	// 	include_once( dirname( __FILE__ ) . '/rpr_template_tags.php' );
	// 	include( $includepath );
	// 	// and render the content using that file:
	// 	$content = ob_get_contents();
  //
	// 	// Finish rendering
	// 	ob_end_clean();
  //
	// 	// return the rendered content:
	// 	return $content;
	// }

	/**
	 * Get the path to the layout file depending on the layout options
	 *
	 * @since 0.8.0
	 * @return string
	 */
	private function get_the_layout(){
		// Get the layout chosen:
		$layout = AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'layout' ) , 'rpr_default' );
		// calculate the includepath for the layout:
		// Check if a global or local layout should be used:
		if( strpos( $layout, 'local') !== false ){
			//Local layout
			$includepath = get_stylesheet_directory() . '/rpr_layouts/'. preg_replace('/^local\_/', '', $layout ) . '/';
		} else {
			//Global layout
			$includepath = plugin_dir_path( __FILE__ )  . 'layouts/' . $layout . '/';
		}

		return $includepath;
	}
	// -------------------- Do the Shortcodes ----------------------------------
	// /**
	//  * Render an embedded recipe by evaluating the rpr-recipe shortcode
	//  *
	//  * @since 0.8.0
	//  * @param mixed $options
	//  * @return string
	//  */
	// public function do_recipe_shortcode( $options ) {
	// 	/**
	// 	 * Set default values for options not set explicityly
	// 	 */
	// 	$options = shortcode_atts(array(
  //           'id' => 'n/a',
  //           'excerpt' => 0,
  //       ), $options);
  //
	// 	/**
	// 	 * Define variable for the post object
	// 	 */
  //       $recipe_post = null;
  //
  //
  //       if ($options['id'] != 'n/a') {
	// 		/**
	// 		 * Get random post
	// 		 */
  //           if( $options['id'] == 'random' ) {
  //
  //               $posts = get_posts(array(
  //                   'post_type' => 'rpr_recipe',
  //                   'nopaging' => true
  //               ));
  //
  //               $recipe_post = $posts[array_rand($posts)];
	// 		/**
	// 		 * Get post by id
	// 		 */
  //           } else {
  //               $recipe_post = get_post( intval($options['id'] ) );
  //           }
  //
	// 		if(!is_null( $recipe_post ) && $recipe_post->post_type === 'rpr_recipe' ) {
	// 			$recipe = get_post_custom($recipe_post->ID);
  //                               $GLOBALS['recipe_id'] = $recipe_post->ID;
  //
  //           //$taxonomies = get_option('rpr_taxonomies', array());
  //
  //
	// 		if( $options['excerpt'] == 0 ){
	// 			// Embed complete recipe
	// 			$output = $this->render_recipe_content( $recipe_post );
	// 		} elseif( $options['excerpt']== 1 ){
	// 			// Embed excerpt only
	// 			//$output = '<h2 class="rpr_title">'.$recipe_post->post_title.'</h2>';
	// 			//$output .= get_the_post_thumbnail($recipe_post->ID);
	// 			$output =  $this->render_recipe_excerpt( $recipe_post );
	// 		}
  //       } else {
  //           $output = '';
  //       }
  //
  //       return do_shortcode($output);
  //       }
	// }

	// /**
	//  * Do the shortcode 'rpr-taxlist' and render a list of all terms of a given
	//  * taxonomy
	//  *
	//  * @since 0.8.0
	//  * @param mixed $options
	//  */
	// public function do_taxlist_shortcode( $options ){
	// 	/**
	// 	 * Set default values for options not set explicityly
	// 	 */
	// 	$options = shortcode_atts(array(
  //           'headers' => 'false',
  //           'tax' => 'n/a',
  //       ), $options);
  //
	// 	// The actual rendering is done by a special function
	// 	$output = $this->render_taxlist( $options['tax'], $options['headers'] );
  //
	// 	return do_shortcode($output);
	// }
  //
	// /**
	//  * Do the shortcode 'rpr-index' and render a list of all recipes
	//  *
	//  * @since 0.8.0
	//  * @param mixed $options
	//  */
	// public function do_recipe_index_shortcode( $options ){
	// 	/**
	// 	 * Set default values for options not set explicityly
	// 	 */
	// 	$options = shortcode_atts(array($this
  //           'headers' => 'false',
  //       ), $options);
  //
	// 	// The actual rendering is done by a special function
	// 	$output = $this->render_recipe_index( $options['headers'] );
  //
	// 	return do_shortcode($output);
	// }

}

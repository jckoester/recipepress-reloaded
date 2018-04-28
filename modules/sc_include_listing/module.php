<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RPR_Module_SC_Include_Listing extends RPR_Module {

    /**
     * Load all files required for the module
     */
    public function load_module_dependencies() {
      require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/helper-layout.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the module.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_module_admin_hooks( $loader ){
        if( is_a( $loader, 'RPR_Loader' ) ){
          $loader->add_action( 'media_buttons', $this, 'add_button_scl' );
          $loader->add_action( 'in_admin_footer', $this, 'load_in_admin_footer_scl' );
          $loader->add_action( 'admin_enqueue_scripts', $this, 'load_ajax_scripts_scl' );
        }
    }

    /**
     * Register all of the hooks related to the public area functionality
     * of the modulinite.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_module_public_hooks( $loader ){
        if( is_a( $loader, 'RPR_Loader' ) ){
          add_shortcode( 'rpr-recipe-index', array( $this, 'do_recipe_index_shortcode' ));
          add_shortcode( 'rpr-tax-list', array( $this, 'do_taxlist_shortcode' ));
        }
    }

    public function get_path(){
        return dirname(__FILE__);
    }

    /**
  	 * Add a button for the shortcode dialog above the editor just as "Add Media"
  	 * @param type $editor_id
  	 * @return type
  	 */
  	public function add_button_scl( $editor_id = 'content' ) {
  		global $post_type;
  		if(!in_array($post_type, array( 'page' ) ) )
  		   return;

  		printf( '<a href="#" id="rpr-add-listings-button" class="rpr-icon button" data-editor="%s" title="%s">%s</a>',
  			esc_attr( $editor_id ),
  			esc_attr__( 'Add Listing', 'recipepress-reloaded' ),
  			esc_html__( 'Add Listing', 'recipepress-reloaded' )
  		);
  	}
  	/**
  	 * Function to load the modal overlay in the footer
  	 * @global type $post_type
  	 * @return type
  	 */
  	public function load_in_admin_footer_scl(){
  		global $post_type;
  		if(!in_array($post_type, array( 'page' ) ) )
  		   return;

  		include dirname( __FILE__ ) . '/view-rpr-modal-listings.php';
  	}
  	/**
  	 * Function to load the scripts needed for the ajax part in shortcode dialog
  	 * @global type $post_type
  	 * @param type $hookplugin_public
  	 * @return type
  	 */
  	public function load_ajax_scripts_scl( $hook ){
  		global $post_type;

  		// Only load on pages where it is necessary:
  		if(!in_array($post_type,array( 'page' ) ) )
  			return;

  		wp_enqueue_script('rpr_ajax_scl', plugin_dir_url( __FILE__ ) . 'rpr_ajax_scl.js', array('jquery') );
  		wp_localize_script('rpr_ajax_scl', 'rpr_vars', array(
  				'rpr_ajax_nonce' => wp_create_nonce( 'rpr-ajax-nonce' )
  			)
  		);
  		wp_localize_script( 'rpr_ajax_scl', 'rprListingsScL10n', array(
  			'noTitle' => __( 'No title', 'recipepress-reloaded' ),
  			'recipe' => __( 'Recipe', 'recipepress-reloaded' ),
  			'save' => __( 'Insert', 'recipepress-reloaded' ),
  			'update' => __( 'Insert', 'recipepress-reloaded' ),
  		) );
  	}

    /**
     * Do the shortcode 'rpr-taxlist' and render a list of all terms of a given
     * taxonomy
     *
     * @since 0.8.0
     * @param mixed $options
     */
    public function do_taxlist_shortcode( $options ){
      /**
       * Set default values for options not set explicityly
       */
      $options = shortcode_atts(array(
              'headers' => 'false',
              'tax' => 'n/a',
          ), $options);

      // The actual rendering is done by a special function
      $output = $this->render_taxlist( $options['tax'], $options['headers'] );

      return do_shortcode($output);
    }

    /**
     * Do the shortcode 'rpr-index' and render a list of all recipes
     *
     * @since 0.8.0
     * @param mixed $options
     */
    public function do_recipe_index_shortcode( $options ){
      /**
       * Set default values for options not set explicityly
       */
      $options = shortcode_atts(array(
              'headers' => 'false',
          ), $options);

      // The actual rendering is done by a special function
      $output = $this->render_recipe_index( $options['headers'] );

      return do_shortcode($output);
    }
    /**
  	 * Render a list of all terms of a taxonomy using the layout's taxonomy.php file
  	 *
  	 * @since 0.8.0
  	 * @param type $taxonomy
  	 * @param type $headers
  	 * @return string $content
  	 */
  	private function render_taxlist( $taxonomy, $headers=false ) {
  		/**
  		 * Create empty output variable
  		 */
  		$output = '';

  		// Get the layout's includepath
  		$includepath = rpr_get_the_layout() . 'taxonomy.php';

  		if( !file_exists( $includepath ) ){
  			// If the layout does not provide an taxonomy file, use the default one:
  			$includepath = rpr_get_the_layout('true') . 'taxonomy.php';
  		}

  		/**
  		 * Set recipe_post to false for template tags
  		 */
  		$recipe_post = false;

  		if( $taxonomy != 'n/a' && $taxonomy != '' ){
  			/**
  			 * get the terms of the selected taxonomy
  			 */
  			$terms = get_terms( $taxonomy, array( 'orderby'=> 'name', 'order' => 'ASC' ) );
  		} else {
  			/**
  			 * Set $terms to false for the layout and it's error messages
  			 */
  			$terms = false;
  		}

      // Include the common template tags:
  		include_once( rpr_get_common_template_tags() );

      // Include the module's template tags:
      $modules = rpr_get_modules_template_tags();
      if( count($modules) > 0 ){
        foreach( $modules as $module ){
          include_once( $module );
        }
      }

      // Start the output buffer
      ob_start();
      // Include the taxonomy file:
  		include( $includepath );
  		// and render the content using that file:
  		$content = ob_get_contents();

  		// Finish rendering
  		ob_end_clean();

  		// return the rendered content:
  		return $content;
  	}

  	/**
  	 * Render a list of all recipes alphabetically using the layout's recipe_index.php file
  	 *
  	 * @since 0.8.0
  	 * @param type $headers
  	 * @return string $content
  	 */
  	private function render_recipe_index( $headers=false ) {
  		/**
  		 * Create empty output variable
  		 */
  		$output = '';

      // TODO: try using locate_template to include local layouts
  		// Get the layout's includepath
  		$includepath = rpr_get_the_layout() . 'recipe_index.php';
  		if( !file_exists( $includepath ) ){
  			// If the layout does not provide an taxonomy file, use the default one:
  			$includepath = rpr_get_the_layout(true) . 'recipe_index.php';
  		}

  		/**
  		 * Set recipe_post to false for template tags
  		 */
  		$recipe_post = false;

  		/**
  		 * Get an alphabetically ordered list of all recipes
  		 */
  		$args = array(
              'post_type' => 'rpr_recipe',
              'post_status' => 'publish',
              'orderby' => 'post_title',
              'order' => 'ASC',
              'posts_per_page' => -1,
          );
          $query = new WP_Query( $args );
          $posts = array();

          if( $query->have_posts() ) { //recipes found
              while( $query->have_posts() ) {
                  $query->the_post();
                  global $post;
                  $posts[] = $post;
              }
          }

      ob_start();
  		// Include the taxonomy file:
      rpr_include_template_tags('common');
  		include( $includepath );
  		// and render the content using that file:
  		$content = ob_get_contents();

  		// Finish rendering
  		ob_end_clean();

  		// return the rendered content:
  		return $content;
  	}

}

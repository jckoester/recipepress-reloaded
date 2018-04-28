<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RPR_Module_SC_Include_Recipe extends RPR_Module {

    /**
     * Load all files required for the module
     */
    public function load_module_dependencies() {
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the module.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_module_admin_hooks( $loader ){
        if( is_a( $loader, 'RPR_Loader' ) ){
          $loader->add_action( 'media_buttons', $this, 'add_button_scr' );
          $loader->add_action( 'in_admin_footer', $this, 'load_in_admin_footer_scr' );
          $loader->add_action( 'admin_enqueue_scripts', $this, 'load_ajax_scripts_scr' );
          $loader->add_action( 'wp_ajax_rpr_get_results', $this, 'process_ajax_scr' );
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
            //echo "Got a valid loader";
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
  	public function add_button_scr( $editor_id = 'content' ) {
  		global $post_type;
  		if(!in_array($post_type, array( 'page', 'post' ) ) )
  		   return;

  		printf( '<a href="#" id="rpr-add-recipe-button" class="rpr-icon button" data-editor="%s" title="%s">%s</a>',
  			esc_attr( $editor_id ),
  			esc_attr__( 'Add Recipe', 'recipepress-reloaded' ),
  			esc_html__( 'Add Recipe', 'recipepress-reloaded' )
  		);
  	}
  	/**
  	 * Function to load the modal overlay in the footer
  	 * @global type $post_type
  	 * @return type
  	 */
  	public function load_in_admin_footer_scr(){
  		global $post_type;
  		if(!in_array($post_type, array( 'page', 'post' ) ) )
  		   return;

  		include dirname( __FILE__ ) . '/view-rpr-modal-recipe.php';
  	}
  	/**
  	 * Function to load the scripts needed for the ajax part in shortcode dialog
  	 * @global type $post_type
  	 * @param type $hook
  	 * @return type
  	 */
  	public function load_ajax_scripts_scr( $hook ){
  		global $post_type;

  		// Only load on pages where it is necessary:
  		if(!in_array($post_type,array( 'page', 'post' ) ) )
  			return;

  		wp_enqueue_script('rpr_ajax_scr', plugin_dir_url( __FILE__ ) . 'rpr_ajax_scr.js', array('jquery') );
  		wp_localize_script('rpr_ajax_scr', 'rpr_vars', array(
  				'rpr_ajax_nonce' => wp_create_nonce( 'rpr-ajax-nonce' )
  			)
  		);
  		wp_localize_script( 'rpr_ajax_scr', 'rprRecipeScL10n', array(
  			'noTitle' => __( 'No title', 'recipepress-reloaded' ),
  			'recipe' => __( 'Recipe', 'recipepress-reloaded' ),
  			'save' => __( 'Insert', 'recipepress-reloaded' ),
  			'update' => __( 'Insert', 'recipepress-reloaded' ),
  		) );
  	}
  	/**
  	 * Process the data from the shortcode include dialog
  	 *
  	 */
  	public function process_ajax_scr() {
  		check_ajax_referer( 'rpr-ajax-nonce', 'rpr_ajax_nonce' );

  		$args = array();

  		if ( isset( $_POST['search'] ) ){
  			$args['s'] = wp_unslash( $_POST['search'] );
  		} else {
  			$args['s'] = '';
  		}

  		$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

  		$query=array(
  			'posts_per_page' => 10,
  		);
  		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

  		$recipes = get_posts(array('s'=> $args['s'], 'post_type' => 'rpr_recipe', 'posts_per_page' => $query['posts_per_page'], 'offset'=> $query['offset'], 'orderby'=> 'post_date'));

  		$json = array();

  		foreach($recipes as $recipe){
  			array_push($json, array('id'=>$recipe->ID, 'title'=>$recipe->post_title));
  		}

  		wp_send_json($json);
  		die();
  	}

}

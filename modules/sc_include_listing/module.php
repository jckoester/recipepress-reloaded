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
  	 * @param type $hook
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

}

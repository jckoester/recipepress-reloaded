<?php
/*
Title: Demo Module
Category: Metadata
Author: Jan Köster
Author Mail: dasmaeh@cbjck.de
Author URL: https://dasmaeh.de
Documentation URL: https://rpr.dasmaeh.de/modules/demo
Version: 0.1
Description: This is a test module to develop the modules API and demonstrate it's functionality.
*/

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RPR_Module_Demo extends RPR_Module {

    /**
     * Load all files required for the module
     */
    public function load_dependencies() {
    }
	
    /**
     * Register all of the hooks related to the admin area functionality
     * of the module.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_admin_hooks( $loader ){
        if( is_a( $loader, 'RPR_Loader' ) ){
            //echo "Got a valid loader";
            // Add metabox for this module
            $loader->add_action( 'do_meta_boxes', $this, 'metabox_demo' );
            // Save this modules recipe data:
            $loader->add_action( 'save_post', $this, 'save_recipe_demo', 10, 2 );
            // Add option fields for this module
            //$loader->add_action( 'init', $this, 'add_module_options' );
        }
    }
	
    /**
     * Register all of the hooks related to the public area functionality
     * of the modulinite.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_public_hooks( $loader ){
        if( is_a( $loader, 'RPR_Loader' ) ){
            //echo "Got a valid loader";
        }
    }
    
    public function metabox_demo(){
        // Add advanced metabox for nutritional information
        add_meta_box(
    		'recipe_demo_meta_box',
    		__( 'Demo box', 'recipepress-reloaded' ),
    		array( $this, 'do_metabox_demo' ),
    		'rpr_recipe',
    		'normal',
    		'high'
    	);
    }
    
    public function do_metabox_demo(){
        // Get post:
        $recipe = get_post();
        // render view:
        include( 'view_rpr-metabox-demo.php');
    }
    
    
    /**
     * Procedure to save this module's data for the recipe
     * 
     * @param type $recipe_id
     * @param type $recipe
     */
    public function save_recipe_demo( $recipe_id, $recipe=NULL ){
        // Get the data submitted by the form:
        $data = $_POST;
        
        // Disable this action so can't be called twice in parallel
        remove_action( 'save_post', array($this, 'save_recipe_demo' ) );
        
        // run some global checks (like permissions, recipe-object); 
        // verify nonce, ...
        if( $this->check_before_saving( $recipe, $data['rpr_nonce_demo'], 'rpr_save_recipe_demo' ) ){
            // save the data
            $fields = array(
                'rpr_recipe_demo_value'
            );
            $this->save_fields( $fields, $data, $recipe );
        }
        
        // Re-enable this action so it can be called again:
        add_action('save_post', array( $this, 'save_recipe_demo' ) );

    }
    
    public function get_path(){
        return dirname(__FILE__);
    }

}
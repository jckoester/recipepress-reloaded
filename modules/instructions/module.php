<?php
/*
Title: Demo Module
Category: Metadata
Author: Jan Köster
Author Mail: dasmaeh@cbjck.de
Author URL: https://dasmaeh.de
Documentation URL: https://rpr.dasmaeh.de/modules/ingredients
Version: 0.1
Description: This is a test module to develop the modules API and demonstrate it's functionality.
*/

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RPR_Module_Instructions extends RPR_Module_Metabox {

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
            //// Load Styles and scripts
            $loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );
            // Add metabox for this module
            $loader->add_action( 'do_meta_boxes', $this, 'metabox_instructions', 10 );
            // Save this modules recipe data:
            $loader->add_action( 'save_post', $this, 'save_recipe_instructions', 10, 2 );
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
    
    /**
     * Load module specific CSS styles and scripts
     */
    public function enqueue_scripts( $hook ) {
        global $post;
        /* Admin styles */
       // wp_enqueue_style( 'rpr_module_ingredients', plugin_dir_url(__FILE__) . 'nutrition_admin.css', array(), '1.0', 'all');
        /* Admin script */
        if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
            if ( 'rpr_recipe' === $post->post_type ) {
                wp_enqueue_script( 'rpr_module_instructions', plugin_dir_url( __FILE__ ) . 'admin-ins-meta-table.js', array ( 'jquery' ), '1.0', false );

                $translations = array(
                    'ins_img_upload_title' => __('Insert instruction image', 'recipepress-reloaded'),
                    'ins_img_upload_text' => __('Insert image', 'recipepress-reloaded')
                );

                wp_localize_script( 'rpr_module_instructions', 'ins_trnsl', $translations);
            }
        }
    }
    
    public function metabox_instructions(){
        // Add advanced metabox for nutritional information
        add_meta_box(
    		'recipe_instructions_meta_box',
    		__( 'Instructions', 'recipepress-reloaded' ),
    		array( $this, 'do_metabox_instructions' ),
    		'rpr_recipe',
    		'normal',
    		'high'
    	);
    }
    
    public function do_metabox_instructions(){
        // Get post:
        $recipe = get_post();
        // render view:
        include( 'metabox_instructions.php');
    }
    
    /**
     * Procedure to save this module's data for the recipe
     * 
     * @param type $recipe_id
     * @param type $recipe
     */
    public function save_recipe_instructions( $recipe_id, $recipe=NULL ){
        // Get the data submitted by the form:
        $data = $_POST;
        
        // Disable this action so can't be called twice in parallel
        remove_action( 'save_post', array($this, 'save_recipe_instructions' ) );
        
        // run some global checks (like permissions, recipe-object); 
        // verify nonce, ...
        //var_dump($data);
        if( $this->check_before_saving( $recipe, $data['rpr_nonce_instructions'], 'rpr_save_recipe_instructions' ) ){
            $instructions = $data['rpr_recipe_instructions'];
            // A new array to contain all non empty line from the form
            $non_empty = array();
        
            foreach ( $instructions as $ins ){
                // Check if we have a instructions group or a instructions line
                if( isset( $ins['grouptitle'] ) ){
                    // we have a ingredient group title line
                    // We do nothing and will save this line as is to the recipe meta data
                    if( $ins['grouptitle'] != "") {
                        $non_empty[] = $ins;
                    }
                } else { 
                    // we have a single ingredient line
                    if( $ins['description'] != "" || $ins['image'] != '' ){
                        $non_empty[] = $ins;
                    }
                }
            }
        
            // Save the new meta data array:
            update_post_meta( $recipe_id, 'rpr_recipe_instructions', $non_empty );            
        }
        
        // Re-enable this action so it can be called again:
        add_action('save_post', array( $this, 'save_recipe_ingredients' ) );

    }
    
    public function get_path(){
        return dirname(__FILE__);
    }

     /**
     * Return the structured data related to this module encoded as an array
     * Core function will create JSON-LD schmema from this and other module's
     * data
     * For more infomration on structured data see: 
     * http://1.schemaorgae.appspot.com/NutritionInformation
     */
    public function get_structured_data( $recipe_id, $recipe ){
        $json = array();
        // Instructions
        if( isset( $recipe['rpr_recipe_instructions'][0] ) && count( $recipe['rpr_recipe_instructions'][0] ) > 0 ) {
            $instructions = unserialize( $recipe['rpr_recipe_instructions'][0] );
				
            $json["recipeInstructions"] = "";
            foreach( $instructions as $instruction ){
                if( !isset( $instruction['grouptitle'] ) ){
                    $json["recipeInstructions"] .= $instruction['description'];
                }
            }	
        }
        return $json;
    }
}
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

class RPR_Module_MB_Image extends RPR_Module_Metabox {

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
            // Add metabox for this module
            $loader->add_action( 'do_meta_boxes', $this, 'metabox_postimage', 10 );
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
    
    public function metabox_postimage(){
        remove_meta_box( 'postimagediv', 'rpr_recipe', 'side' );
        add_meta_box( 'postimagediv', __( 'Recipe photo', 'recipepress-reloaded' ), 'post_thumbnail_meta_box', 'rpr_recipe', 'side', 'high' );
    }
    
    /**
     * Procedure to save this module's data for the recipe
     * 
     * @param type $recipe_id
     * @param type $recipe
     */
    public function save_recipe_demo( $recipe_id, $recipe=NULL ){
        
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
    public function get_structured_data( $recipe_id, $recipe ){}
}
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

class RPR_Module_Ingredients extends RPR_Module_Metabox {

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
            $loader->add_action( 'do_meta_boxes', $this, 'metabox_ingredients', 10 );
            // Save this modules recipe data:
            $loader->add_action( 'save_post', $this, 'save_recipe_ingredients', 10, 2 );
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
                wp_enqueue_script( 'rpr_module_ingredients', plugin_dir_url( __FILE__ ) . 'admin-ing-meta-table.js', array ( 'jquery' ), '1.0', false );
                wp_enqueue_script( 'rpr_module_ingredients_link', plugin_dir_url( __FILE__ ) . 'admin-ing-meta-link.js', array ( 'jquery' ), '1.0', false );
                // Load jQuery Link script to add links to ingredients
                wp_enqueue_script( 'wp-link' );
            }
        }
    }
    
    public function metabox_ingredients(){
        // Remove default meta box from side
        remove_meta_box( 'tagsdiv-rpr_ingredient', 'rpr_recipe', 'side' );
        // Add advanced metabox for nutritional information
        add_meta_box(
    		'recipe_ingredients_meta_box',
    		__( 'Ingredients', 'recipepress-reloaded' ),
    		array( $this, 'do_metabox_ingredients' ),
    		'rpr_recipe',
    		'normal',
    		'high'
    	);
    }
    
    public function do_metabox_ingredients(){
        // Get post:
        $recipe = get_post();
        // render view:
        include( 'metabox_ingredients.php');
    }
    
    /**
     * Get a list of all available units from the options. 
     * @since 0.8.0
     */
    
    public function get_the_ingredient_unit_selection( $selected="" ) {
        $units = AdminPageFramework::getOption( 'rpr_options', array( 'units', 'ingredient_units') , false );
        $outp = "";
        
        foreach ( $units as $key=>$unit ){
            $outp .= '<option value="' . $unit . '"';
            if( $unit == $selected ) { $outp .= ' selected="selected" '; }
            $outp .= '>' . $unit . '</option>' . "\n";
        }
        if( ! in_array( $selected, $units )){
            $outp .= '<option value="' . $selected . '"  selected="selected" >' . $selected . '</option>\n';
        }
        return $outp;
    }
    
    public function the_ingredient_unit_selection( $selected="" ) {
        echo $this->get_the_ingredient_unit_selection( $selected );
    }
    
    // TODO: Adjust the savings procedure for ingredients!
    /**
     * Procedure to save this module's data for the recipe
     * 
     * @param type $recipe_id
     * @param type $recipe
     */
    public function save_recipe_ingredients( $recipe_id, $recipe=NULL ){
        // Get the data submitted by the form:
        $data = $_POST;
        
        // Disable this action so can't be called twice in parallel
        remove_action( 'save_post', array($this, 'save_recipe_ingredients' ) );
        
        // run some global checks (like permissions, recipe-object); 
        // verify nonce, ...
        //var_dump($data);
        if( $this->check_before_saving( $recipe, $data['rpr_nonce_ingredients'], 'rpr_save_recipe_ingredients' ) ){
            $ingredients = $data['rpr_recipe_ingredients'];
            // A new array to contain all non empty line from the form
            $non_empty = array();
            // An array of all ingredient term_ids to create a relation to the recipe
            $ingredient_tax = array();

            foreach ( $ingredients as $ing ){
                //var_dump($ing);
                // Check if we have a ingredients group or a ingredient
                if( isset( $ing['grouptitle'] ) ){
                    // we have a ingredient group title line
                    // We do nothing and will save this line as is to the recipe meta data
                    if( $ing['grouptitle'] != "") {
                        $non_empty[] = $ing;
                    }
                } else {
                    // we have a single ingredient line
                    if( $ing['ingredient'] != "" ){
                        // We need to find the term_id of the ingredient and add a taxonomy relation to the recipe
                        $term = term_exists($ing['ingredient'], 'rpr_ingredient');
                        if( $term === 0 || $term === null ){
                            // ingredient is not an existing term, create it:
                            $term = wp_insert_term($ing['ingredient'], 'rpr_ingredient');
                        }
                        // Now we have a valid term id!
                        $term_id = intval($term['term_id']);
                        // Set it to the ingredient array:
                        $ing['ingredient_id'] = $term_id;
                        // add it to the taxonomy list:
                        $ingredient_tax[] = $term_id;
                        // Add it to the save list:
                        $non_empty[] = $ing;
                    }
                }
            }
            
            // Save the recipe <-> ingredient taxonomy relation
            wp_set_post_terms( $recipe_id, $ingredient_tax, 'rpr_ingredient' );
            // Save the new meta data array:
            update_post_meta( $recipe_id, 'rpr_recipe_ingredients', $non_empty );               
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
        
        // Ingredients
	if( isset( $recipe['rpr_recipe_ingredients'][0] ) && count( $recipe['rpr_recipe_ingredients'][0] ) > 0 ) {
            $json["recipeIngredient"] = array();
            $ingredients = unserialize( $recipe['rpr_recipe_ingredients'][0] );

            foreach( $ingredients as $ingredient ){
                if( !isset( $ingredient['grouptitle'] ) ){
                    if( isset( $ingredient['ingredient_id'] ) ){
                        $term = get_term_by( 'id', $ingredient['ingredient_id'], 'rpr_ingredient' );
                    } else {
                        $term = get_term_by( 'name', $ingredient['ingredient'], 'rpr_ingredient' );
                    }

                    $ingstring = esc_html( $ingredient['amount'] ) . ' ' . esc_html( $ingredient['unit'] ) . ' ' . $term->name;
                    if( isset( $ingredient['notes'] ) && $ingredient['notes'] != '' ){
                        $ingstring .= ', ' . esc_html( $ingredient['notes'] );
                    }
                    
                    array_push($json["recipeIngredient"], $ingstring);
                }
            }
        }
        return $json;
    }
}
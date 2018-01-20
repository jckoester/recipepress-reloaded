<?php
/*
Title: Nutrition Information
Category: Metadata
Author: Jan Köster
Author Mail: dasmaeh@cbjck.de
Author URL: https://dasmaeh.de
Documentation URL: https://rpr.dasmaeh.de/modules/demo
Version: 0.1
Description: Adds fields for nutritional information to your recipes
*/

/**
 * @since 1.0.0
 */
class RPR_Module_Nutrition extends RPR_Module {

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
            // Load CSS
            $loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );
            // Add metabox for this module
            $loader->add_action( 'do_meta_boxes', $this, 'metabox_nutrition' );
            // Save this modules recipe data:
            $loader->add_action( 'save_post', $this, 'save_recipe_nutrition', 10, 2 );
        }
    }
	
    /**
     * Register all of the hooks related to the public area functionality
     * of the modulinite.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_public_hooks( $loader ){
    }
    
    /**
     * Load module specific CSS styles and scripts
     */
    public function enqueue_scripts() {
        /* Admin styles */
        wp_enqueue_style( 'rpr_module_nutrition', plugin_dir_url(__FILE__) . 'nutrition_admin.css', array(), '1.0', 'all');
        /* Admin script */
        wp_enqueue_script( 'rprp_module_nutrition', plugin_dir_url( __FILE__ ) . 'nutrition_admin.js', array ( 'jquery' ), '1.0', false );
    }
    
    public function metabox_nutrition(){
        // Add advanced metabox for nutritional information
        add_meta_box(
    		'recipe_nutrition_meta_box',
    		__( 'Nutritional information', 'recipepress-reloaded' ),
    		array( $this, 'do_metabox_nutrition' ),
    		'rpr_recipe',
    		'side',
    		'high'
    	);
    }
    
    public function do_metabox_nutrition(){
        // Get post:
        $recipe = get_post();
        // render view:
        include( 'metabox_nutrition.php');
    }
    
    
    /**
     * Procedure to save this module's data for the recipe
     * 
     * @param type $recipe_id
     * @param type $recipe
     */
    public function save_recipe_nutrition( $recipe_id, $recipe=NULL ){
        // Get the data submitted by the form:
        $data = $_POST;
        
        // Disable this action so can't be called twice in parallel
        remove_action( 'save_post', array($this, 'save_recipe_nutrition' ) );
        
        // run some global checks (like permissions, recipe-object); 
        // verify nonce, ...
        if( $this->check_before_saving( $recipe, $data['rpr_nonce_nutrition'], 'rpr_save_recipe_nutrition' ) ){
            // save the data
            $fields = array(
                'rpr_recipe_nutrition_per',
                'rpr_recipe_calorific_value',
                'rpr_recipe_carbohydrate',
                'rpr_recipe_sugar',
                'rpr_recipe_protein',
                'rpr_recipe_fat',
                'rpr_recipe_fat_unsaturated',
                'rpr_recipe_fat_saturated',
                'rpr_recipe_fat_trans',
                'rpr_recipe_cholesterol',
                'rpr_recipe_nutrition_sodium',
                'rpr_recipe_nutrition_fiber',
            );
            $this->save_fields( $fields, $data, $recipe );
        }
        
        // Re-enable this action so it can be called again:
        add_action('save_post', array( $this, 'save_recipe_nutrition' ) );

    }
    
    /**
     * Return the path to module's directory
     * @return string
     */
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
        $data = array();
        
        // Get all the fields:
        require_once 'nutrition_data.php';
        $nutridata = get_the_rpr_recipe_nutrition_fields();
        
        // Get the saved data as well:
        foreach ( $nutridata as $key => $value ){
            $dbkey = $value['dbkey'];
            if( isset( $recipe[$dbkey][0] ) && $recipe[$dbkey][0] !=''){// && $recipe[$value][0] != 0){
                $data[$value['json_ld_id']] = $recipe[$dbkey][0] .' ' . $value['json_ld_unit'];
            }
        }
        
        if( count( $data > 0 ) ){
            $data['@type'] = "NutritionInformation";
        
            switch ($recipe['rpr_recipe_nutrition_per'][0]) {
                case 'per_100g':
                    $data['servingSize'] = '100 grams';
                    break;
                case 'per_portion':
                    $data['servingSize'] = '1 portion';
                    break;
                case 'per_recipe':
                    $data['servingSize'] = '1 recipe';
                    break;
                default:
                    $data['servingSize'] = '100 grams';
            }
        }
        
        $json['nutrition'] = $data;
        return $json;
    }
}
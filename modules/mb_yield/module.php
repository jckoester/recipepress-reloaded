<?php

/*
  Title: Yield
  Category: Metadata
  Author: Jan Köster
  Author Mail: dasmaeh@cbjck.de
  Author URL: https://dasmaeh.de
  Version: 0.1
  Description: Adds fields for the yield of the recipe
 */

// TODO: Admin CSS

/**
 * @since 1.0.0
 */
class RPR_Module_MB_Yield extends RPR_Module_Metabox {

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
    public function define_module_admin_hooks($loader) {
        if (is_a($loader, 'RPR_Loader')) {
            // Add metabox for this module
            $loader->add_action('do_meta_boxes', $this, 'metabox_yield');
            // Save this modules recipe data:
            $loader->add_action('save_post', $this, 'save_recipe_yield', 10, 2);
        }
    }

    /**
     * Register all of the hooks related to the public area functionality
     * of the modulinite.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_module_public_hooks($loader) {
        
    }

    public function metabox_yield() {
        // Add advanced metabox for nutritional information
        add_meta_box(
                'recipe_yield_meta_box', __('Yield', 'recipepress-reloaded'), array(
            $this,
            'do_metabox_yield'
                ), 'rpr_recipe', 'side', 'high'
        );
    }

    public function do_metabox_yield() {
        // Get post:
        $recipe = get_post();
        // render view:
        include( 'metabox_yield.php');
    }

    /**
     * Procedure to save this module's data for the recipe
     * 
     * @param type $recipe_id
     * @param type $recipe
     */
    public function save_recipe_yield( $recipe_id, $recipe = NULL ) {
        // Get the data submitted by the form:
        $data = $_POST;

        // Disable this action so can't be called twice in parallel
        remove_action('save_post', array($this, 'save_recipe_yield'));

        // run some global checks (like permissions, recipe-object); 
        // verify nonce, ...
        if ($this->check_before_saving($recipe, $data['rpr_nonce_yield'], 'rpr_save_recipe_yield')) {
            // save the data
            $fields = array(
                'rpr_recipe_servings',
                'rpr_recipe_servings_type'
            );
            $this->save_fields($fields, $data, $recipe);
        }

        // Re-enable this action so it can be called again:
        add_action('save_post', array($this, 'save_recipe_yield'));
    }

            /**
         * Get a list of all available units from the options. 
         * @since 0.8.0
         */
        public function get_the_serving_unit_selection( $selected="" ) {
            $units = AdminPageFramework::getOption( 'rpr_options', array( 'units', 'serving_units') , false );
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
    
        public function the_serving_unit_selection( $selected="" ) {
            echo $this->get_the_serving_unit_selection( $selected );
        }
    
    /**
     * Return the path to module's directory
     * @return string
     */
    public function get_path() {
        return dirname(__FILE__);
    }

    /**
     * Return the structured data related to this module encoded as an array
     * Core function will create JSON-LD schmema from this and other module's
     * data
     * For more infomration on structured data see: 
     * http://1.schemaorgae.appspot.com/NutritionInformation
     */
    public function get_structured_data($recipe_id, $recipe) {
        $json = array();

        if( isset( $recipe['rpr_recipe_servings'][0] ) ){
            $json['recipeYield'] = $recipe['rpr_recipe_servings'][0] . ' ' .  $recipe['rpr_recipe_servings_type'][0];
	}


        return $json;
    }

}

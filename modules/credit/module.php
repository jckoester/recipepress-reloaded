<?php

/*
  Title: Recipe Credit
  Category: Metadata
  Author: Jan Köster
  Author Mail: dasmaeh@cbjck.de
  Author URL: https://dasmaeh.de
  Version: 0.1
  Description: Adds a field to credit a source for your recipe. This can be a link, a book, a restaurant, a friend, ...
 */

/**
 * @since 1.0.0
 */
class RPR_Module_Credit extends RPR_Module {

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
    public function define_admin_hooks($loader) {
        if (is_a($loader, 'RPR_Loader')) {
            // Add metabox for this module
            $loader->add_action('do_meta_boxes', $this, 'metabox_credit');
            // Save this modules recipe data:
            $loader->add_action('save_post', $this, 'save_recipe_credit', 10, 2);
        }
    }

    /**
     * Register all of the hooks related to the public area functionality
     * of the modulinite.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_public_hooks($loader) {
        
    }

    public function metabox_credit() {
        // Add advanced metabox for nutritional information
        add_meta_box(
                'recipe_credit_meta_box', 
                __('Credit', 'recipepress-reloaded'), 
                array(
                    $this,
                    'do_metabox_credit'
                ),
                'rpr_recipe',
                'normal',
                'high'
        );
    }

    public function do_metabox_credit() {
        // Get post:
        $recipe = get_post();
        // render view:
        include( 'metabox_credit.php');
    }

    /**
     * Procedure to save this module's data for the recipe
     * 
     * @param type $recipe_id
     * @param type $recipe
     */
    public function save_recipe_credit($recipe_id, $recipe = NULL) {
        // Get the data submitted by the form:
        $data = $_POST;

        // Disable this action so can't be called twice in parallel
        remove_action('save_post', array($this, 'save_recipe_credit'));

        // run some global checks (like permissions, recipe-object); 
        // verify nonce, ...
        if ($this->check_before_saving($recipe, $data['rpr_nonce_credit'], 'rpr_save_recipe_credit')) {
            // save the data
            $fields = array(
                'rpr_recipe_source',
                'rpr_recipe_source_link'
            );
            $this->save_fields($fields, $data, $recipe);
        }

        // Re-enable this action so it can be called again:
        add_action('save_post', array($this, 'save_recipe_credit'));
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
        
        // Get the data
        $source = get_post_meta( $recipe_id, "rpr_recipe_source", true );
        $source_link = get_post_meta( $recipe_id, "rpr_recipe_source_link", true );
        
        if( $source_link != ''){
            $json['isBasedOn'] = $source_link;
        } elseif( $source != '') {
            $json['isBasedOn'] = $source;
        }

        return $json;
    }

}
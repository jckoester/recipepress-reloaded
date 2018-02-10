<?php

/*
  Title: Durations
  Category: Metadata
  Author: Jan Köster
  Author Mail: dasmaeh@cbjck.de
  Author URL: https://dasmaeh.de
  Version: 0.1
  Description: Adds fields for preparation and cooking time.
 */

/**
 * @since 1.0.0
 */
class RPR_Module_MB_Durations extends RPR_Module_Metabox {

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
            $loader->add_action('do_meta_boxes', $this, 'metabox_durations');
            // Save this modules recipe data:
            $loader->add_action('save_post', $this, 'save_recipe_durations', 10, 2);
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

    public function metabox_durations() {
        // Add advanced metabox for nutritional information
        add_meta_box(
                'recipe_credit_meta_box', __('Time', 'recipepress-reloaded'), array(
            $this,
            'do_metabox_durations'
                ), 'rpr_recipe', 'normal', 'high'
        );
    }

    public function do_metabox_durations() {
        // Get post:
        $recipe = get_post();
        // render view:
        include( 'metabox_durations.php');
    }

    /**
     * Procedure to save this module's data for the recipe
     * 
     * @param type $recipe_id
     * @param type $recipe
     */
    public function save_recipe_durations($recipe_id, $recipe = NULL) {
        // Get the data submitted by the form:
        $data = $_POST;

        // Disable this action so can't be called twice in parallel
        remove_action('save_post', array($this, 'save_recipe_durations'));

        // run some global checks (like permissions, recipe-object); 
        // verify nonce, ...
        if ($this->check_before_saving($recipe, $data['rpr_nonce_durations'], 'rpr_save_recipe_durations')) {
            // save the data
            $fields = array(
                'rpr_recipe_prep_time',
                'rpr_recipe_cook_time',
                'rpr_recipe_passive_time'
            );
            $this->save_fields($fields, $data, $recipe);
        }

        // Re-enable this action so it can be called again:
        add_action('save_post', array($this, 'save_recipe_durations'));
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
        $prep_time = get_post_meta($recipe->ID, "rpr_recipe_prep_time", true);
        $cook_time = get_post_meta($recipe->ID, "rpr_recipe_cook_time", true);
        $passive_time = get_post_meta($recipe->ID, "rpr_recipe_passive_time", true);

        if ($prep_time != '' && $prep_time > 0) {
            $json['prepTime'] = rpr_format_time_xml($prep_time);
        }
        if ($cook_time != '' && $cook_time > 0) {
            $json['cookTime'] = rpr_format_time_xml($cook_time);
        }
        $total_time = + $perform_time + $cook_time + $passive_time;
        if ( $total_time != '' && $total_time > 0) {
            $json['totalTime'] = rpr_format_time_xml($total_time);
        }

        return $json;
    }

    /**
     * Formats a number of minutes to a machine readable xml time string
     * 
     * @param int $min
     * @return string
     */
    private function rpr_format_time_xml($min) {
        $hours = floor($min / 60);
        $minutes = $min % 60;
        if ($hours > 0 && $minutes > 0) {
            return sprintf('PT%1$dH%2$dM', $hours, $minutes);
        } elseif ($hours > 0 && $minutes === 0) {
            return sprintf('PT%dH', $hours);
        } else {
            return sprintf('PT%dM', $minutes);
        }
    }

}

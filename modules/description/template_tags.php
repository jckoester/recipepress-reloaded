<?php

if (!function_exists('get_the_rpr_recipe_description_headline')) {

    /**
     * Renders the headline for description.
     * Icons are optional, headline level depends on embedded or standalone 
     * recipe
     * 
     * @param boolean $icons
     * @return string
     */
    function get_the_rpr_recipe_description_headline($icons) {
        /**
         *  Get the recipe id
         */
        if (isset($GLOBALS['recipe_id']) && $GLOBALS['recipe_id'] != '') {
            $recipe_id = $GLOBALS['recipe_id'];
        } else {
            $recipe_id = get_post()->ID;
        }

        $recipe = get_post_custom($recipe_id);

        /**
         * Exit if recipe has no description:
         * isset returns true with empty strings, also check if description is empty
         */
        if (isset($recipe['rpr_recipe_description'][0]) && empty($recipe['rpr_recipe_description'][0])) {
            return;
        }

        /**
         *  Create an empty output string
         */
        $out = '';

        /**
         * Add a third level heading for embedded recipes or a second level 
         * heading for a standalone recipe
         */
        if (recipe_is_embedded()) {
            $out .= '<h3>';
        } else {
            $out .= '<h2>';
        }

        /**
         * Add icon if desired
         */
        if ($icons) {
            /**
             * @todo: Create an option for description icon
             */
            $out .= '<i class="fa fa-paperclip"></i>&nbsp;';
            //$out .= '<i class="' . AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'icon_class' ), 'fa fa-shoppingcart' ) . '"></i>&nbsp;';
        }
        
        $headline = AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'description_headline' ), __('Notes', 'recipepress-reloaded') );
        var_dump($headline);
        $out .= $headline;
        

        if (recipe_is_embedded()) {
            $out .= '</h3>';
        } else {
            $out .= '</h2>';
        }
        /**
         * Return the rendered headline
         */
        return $out;
    }

}

if (!function_exists('the_rpr_recipe_description_headline')) {

    /**
     * Outputs the rendered headline
     * 
     * @since 0.8.0
     * @param boolean $icons
     */
    function the_rpr_recipe_description_headline($icons = false) {
        echo get_the_rpr_recipe_description_headline($icons);
    }

}

if (!function_exists('get_the_rpr_recipe_description')) {

    /**
     * Renders the description. No output if no description saved.
     * 
     * @since 0.8.0
     * @return string
     */
    function get_the_rpr_recipe_description() {
        /**
         *  Get the recipe id
         */
        if (isset($GLOBALS['recipe_id']) && $GLOBALS['recipe_id'] != '') {
            $recipe_id = $GLOBALS['recipe_id'];
        } else {
            $recipe_id = get_post()->ID;
        }

        $recipe = get_post_custom($recipe_id);

        /**
         * Exit if recipe has no description:
         * isset returns true with empty strings, also check if description is empty
         */
        if (isset($recipe['rpr_recipe_description'][0]) && empty($recipe['rpr_recipe_description'][0])) {
            return;
        }

        /**
         *  Create an empty output string
         */
        $out = '';

        /**
         * Render the description only if it is not empty
         */
        if (isset($recipe['rpr_recipe_description'][0]) && strlen($recipe['rpr_recipe_description'][0]) > 0) {
            $out .= '<span class="rpr_description" >';
            $out .= apply_filters('the_content', $recipe['rpr_recipe_description'][0]);
            $out .= '</span>';
        }

        /**
         * Return the rendered description
         */
        return $out;
    }

}

if (!function_exists('the_rpr_recipe_description')) {

    /**
     * Outputs the rendered description
     * 
     * @since 0.8.0
     */
    function the_rpr_recipe_description() {
        echo get_the_rpr_recipe_description();
    }

}

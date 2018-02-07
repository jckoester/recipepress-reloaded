<?php

if (!function_exists('get_the_rpr_recipe_notes_headline')) {

    /**
     * Renders the headline for notes.
     * Icons are optional, headline level depends on embedded or standalone 
     * recipe
     * 
     * @param boolean $icons
     * @return string
     */
    function get_the_rpr_recipe_notes_headline($icons) {
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
         * Exit if recipe has no notes:
         * isset returns true with empty strings, also check if notes is empty
         */
        if (isset($recipe['rpr_recipe_notes'][0]) && empty($recipe['rpr_recipe_notes'][0])) {
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
             * @todo: Create an option for notes icon
             */
            $out .= '<i class="fa fa-paperclip"></i>&nbsp;';
            //$out .= '<i class="' . AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'icon_class' ), 'fa fa-shoppingcart' ) . '"></i>&nbsp;';
        }
        
        $headline = AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'notes_headline' ), __('Notes', 'recipepress-reloaded') );
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

if (!function_exists('the_rpr_recipe_notes_headline')) {

    /**
     * Outputs the rendered headline
     * 
     * @since 0.8.0
     * @param boolean $icons
     */
    function the_rpr_recipe_notes_headline($icons = false) {
        echo get_the_rpr_recipe_notes_headline($icons);
    }

}

if (!function_exists('get_the_rpr_recipe_notes')) {

    /**
     * Renders the notes. No output if no notes saved.
     * 
     * @since 0.8.0
     * @return string
     */
    function get_the_rpr_recipe_notes() {
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
         * Exit if recipe has no notes:
         * isset returns true with empty strings, also check if notes is empty
         */
        if (isset($recipe['rpr_recipe_notes'][0]) && empty($recipe['rpr_recipe_notes'][0])) {
            return;
        }

        /**
         *  Create an empty output string
         */
        $out = '';

        /**
         * Render the notes only if it is not empty
         */
        if (isset($recipe['rpr_recipe_notes'][0]) && strlen($recipe['rpr_recipe_notes'][0]) > 0) {
            $out .= '<span class="rpr_notes" >';
            $out .= apply_filters('the_content', $recipe['rpr_recipe_notes'][0]);
            $out .= '</span>';
        }

        /**
         * Return the rendered description
         */
        return $out;
    }

}

if (!function_exists('the_rpr_recipe_notes')) {

    /**
     * Outputs the rendered notes
     * 
     * @since 0.8.0
     */
    function the_rpr_recipe_notes() {
        echo get_the_rpr_recipe_notes();
    }

}

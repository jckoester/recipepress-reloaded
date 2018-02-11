<?php

if (!function_exists('get_the_rpr_recipe_servings')) {

    /**
     * Renders the serving size information
     * 
     * @since 0.8.0
     * @param boolean $icons
     * @return string
     */
    function get_the_rpr_recipe_servings($icons = false) {
        /**
         *  Get the recipe id
         */
        $recipe_id = get_recipe_id();
        $recipe = get_post_custom($recipe_id);

        /**
         * Return if no servings are saved
         */
        if (!isset($recipe['rpr_recipe_servings'][0])) {
            return;
        }

        /**
         *  Create an empty output string
         */
        $out = '';


        /**
         * Add servings in the correct structured data format
         */
        $out .= '<div class="rpr_servings">';

        /**
         * Add icon if set to do so:
         */
        if ($icons) {
            /**
             * @todo: add option for icon class
             */
            $out .= '<i class="fa fa-pie-chart"></i>&nbsp;';
        } else {
            $out .= __('For:', 'recipepress-reloaded');
            $out .= '&nbsp;';
        }

        $out .= '<span class="rpr_servings" >' . esc_html($recipe['rpr_recipe_servings'][0]) . '</span>&nbsp;';
        $out .= '<span class="rpr_servings_type" >' . esc_html($recipe['rpr_recipe_servings_type'][0]) . '</span>';

        $out .= '</div>';
        /**
         * Return the rendered servings data
         */
        return $out;
    }

}
if (!function_exists('the_rpr_recipe_servings')) {

    /**
     * Outputs the servings rendered above
     * 
     * @since 0.8.0
     * @param boolean $icons
     */
    function the_rpr_recipe_servings($icons = false) {
        echo get_the_rpr_recipe_servings($icons);
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* * ****************************************************************************
 * Recipe source
 */
if (!function_exists('get_the_rpr_recipe_credit')) {

    /**
     * Renders the source of a recipe if meta data is saved
     * @since 0.9.0
     */
    function get_the_rpr_recipe_credit() {
        /**
         *  Get the recipe id
         */
        $recipe_id = get_recipe_id();
        $recipe = get_post_custom($recipe_id);

        /**
         * Create empty output string
         */
        $out = '';

        /**
         * Only render the source if option is set so
         */
        $out .= '<cite class="rpr_source">';
        $out .= '<label for="rpr_source">' . __('Credit:', 'recipepress-reloaded') . ' </label>';

        /**
         * Get the data
         */
        $source = get_post_meta($recipe_id, "rpr_recipe_source", true);
        $source_link = get_post_meta($recipe_id, "rpr_recipe_source_link", true);

        if ($source_link !== '') {
            $out .= '<a href="' . esc_url($source_link) . '" target="_blank" >';
        }
        $out .= sanitize_text_field($source);
        if ($source_link != '') {
            $out .= '</a>';
        }

        $out .= '</cite>';


        return $out;
    }

}

if (!function_exists('the_rpr_recipe_credit')) {

    /**
     * Outputs the rendered data
     * @since 0.9.0
     */
    function the_rpr_recipe_credit() {
        echo get_the_rpr_recipe_credit();
    }

}


// LEGACY tepmlate tags
if (!function_exists('get_the_rpr_recipe_source')) {

    /**
     * Outputs the rendered data
     * @since 0.9.0
     */
    function get_the_rpr_recipe_source() {
        return get_the_rpr_recipe_credit();
    }

}

if (!function_exists('the_rpr_recipe_source')) {

    /**
     * Outputs the rendered data
     * @since 0.9.0
     */
    function the_rpr_recipe_source() {
        echo get_the_rpr_recipe_credit();
    }

}
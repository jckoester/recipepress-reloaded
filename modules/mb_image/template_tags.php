<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!function_exists('get_the_rpr_recipe_image')) {

    /**
     * Includes the recipe post image in embedded recipes and if is set in 
     * advanced options to fix the shortcomings of some recipes
     * 
     * @since 0.8.0
     * @return string
     */
    function get_the_rpr_recipe_image() {
        /**
         *  Get the recipe id
         */
        $recipe_id = get_recipe_id();
        $recipe = get_post_custom($recipe_id);

        /**
         *  Create an empty output string
         */
        $out = '';

        /**
         * Recipe image only needs to be included if  recipe is embedded into another post
         */
        if ( recipe_is_embedded()) {
            if (has_post_thumbnail($recipe_id)) {
//                if (AdminPageFramework::getOption('rpr_options', array('layout_general', 'images_link'), false)) {
//                    $out .= '<a href="' . get_the_post_thumbnail_url($recipe_id, 'full') . '" rel="lightbox" title="' . get_the_title($recipe_id) . '">';
//                }
                $out .= get_the_post_thumbnail($recipe_id, 'large');
//                if (AdminPageFramework::getOption('rpr_options', array('layout_general', 'images_link'), false)) {
//                    $out .= '</a>';
//                }
            }
        }
        /**
         * return the renderd output
         */
        return $out;
    }

}

if (!function_exists('the_rpr_recipe_image')) {

    /**
     * Outputs the post image rendered above
     * 
     * @since 0.8.0
     */
    function the_rpr_recipe_image() {
        echo get_the_rpr_recipe_image();
    }

}

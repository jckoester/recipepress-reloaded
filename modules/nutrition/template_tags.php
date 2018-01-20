<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!function_exists('get_the_rpr_recipe_nutrition_fields')) {
    /**
     * Returns a list of all available nutrtional data fields
     * @return array
     */
    function get_the_rpr_recipe_nutrition_fields(){
        return array(
            'calories' => array(
                        'dbkey' => 'rpr_recipe_calorific_value',
                        'label' => __('Calorific value:', 'recipepress-reloaded'),
                        'unit'  => 'kcal',
                        'value' => NULL
            ),
            'carbohydrate' => array(
                        'dbkey' => 'rpr_recipe_carbohydrate',
                        'label' => __('Carbohydrate:', 'recipepress-reloaded'),
                        'unit'  => 'g',
                        'value' => NULL
            ),
            'sugar' => array(
                        'dbkey' => 'rpr_recipe_sugar',
                        'label' => __('Sugar:', 'recipepress-reloaded'),
                        'unit'  => 'g',
                        'value' => NULL
            ),
            'protein' => array(
                        'dbkey' => 'rpr_recipe_protein',
                        'label' => __('Protein:', 'recipepress-reloaded'),
                        'unit'  => 'g',
                        'value' => NULL
            ),
            'fat' => array(
                        'dbkey' => 'rpr_recipe_fat',
                        'label' => __('Fat:', 'recipepress-reloaded'),
                        'unit'  => 'g',
                        'value' => NULL
            ),
            'fat_unsaturated' => array(
                        'dbkey' => 'rpr_recipe_fat_unsaturated',
                        'label' => __('Fat (unsaturated):', 'recipepress-reloaded'),
                        'unit'  => 'g',
                        'value' => NULL
            ),
            'fat_saturated' => array(
                        'dbkey' => 'rpr_recipe_fat_saturated',
                        'label' => __('Fat (saturated):', 'recipepress-reloaded'),
                        'unit'  => 'g',
                        'value' => NULL
            ),
            'fat_trans' => array(
                        'dbkey' => 'rpr_recipe_fat_trans',
                        'label' => __('Trans fat:', 'recipepress-reloaded'),
                        'unit'  => 'g',
                        'value' => NULL
            ),
            'cholesterol' => array(
                        'dbkey' => 'rpr_recipe_cholesterol',
                        'label' => __('Cholesterol:', 'recipepress-reloaded'),
                        'unit'  => 'mg',
                        'value' => NULL
            ),
            'sodium' => array(
                        'dbkey' => 'rpr_recipe_sodium',
                        'label' => __('Sodium:', 'recipepress-reloaded'),
                        'unit'  => 'mg',
                        'value' => NULL
            ),
            'fibre' => array(
                        'dbkey' => 'rpr_recipe_fibre',
                        'label' => __('Fibre:', 'recipepress-reloaded'),
                        'unit'  => 'g',
                        'value' => NULL
            ),
        );
    }
}

if (!function_exists('get_the_rpr_recipe_nutrition')) {

    /**
     * Renders the nutritional information
     * 
     * @since 0.8.0
     * @todo: Add icons if desired by option
     * 
     * @param boolean $icons
     * @return string
     */
    function get_the_rpr_recipe_nutrition($icons = false) {
        /**
         *  Get the recipe id
         */
        $recipe_id = get_recipe_id();
        $recipe = get_post_custom($recipe_id);

        /**
         * Get all the saved values
         */
        $nutridata= get_the_rpr_recipe_nutrition_fields();
        
        foreach ( $nutridata as $key => $value ){
            $dbkey = $value['dbkey'];
            if( isset( $recipe[$dbkey][0] ) && $recipe[$dbkey][0] !=''){// && $recipe[$value][0] != 0){
                $nutridata[$key]['value'] = $recipe[$dbkey][0] .' ' . $value['unit'];
            } else {
                unset( $nutridata[$key]);
            }
        }
        
        /**
         * Create entry for energy in kilojoules 
         */
        if( isset( $nutridata['calories'] ) ){
            $nutridata['calories']['value'] = $nutridata['calories']['value'] . ' kcal / '. round( 4.18 * $nutridata['calories']['value'] ) . ' kJ'; 
        }
        
        /**
         * Return if no nutritional data are saved
         */
        if ( count( $nutridata ) === 0 ) {
            return;
        }
       
        /**
         *  Create an empty output string
         */
        $out = '';
        
        /**
         * Start the nutritional information box:
         */
        $out .= '<div class="rpr_nutritional_data">';
        
        /**
         * Print the serving size (for nutritional data)
         */
        $out .= '<span class="nutrition_per">';

        switch ($recipe['rpr_recipe_nutrition_per'][0]) {
            case 'per_100g':
                $out .= __('Per 100g', 'recipepress-reloaded');
                break;
            case 'per_portion':
                $out .= __('Per portion', 'recipepress-reloaded');
                break;
            case 'per_recipe':
                $out .= __('Per recipe', 'recipepress-reloaded');
                break;
            default:
                $out .= __('Per 100g', 'recipepress-reloaded');
        }

        $out .= '</span>';

        /**
         * Print all other nutritional information
         */
        $out .= '<dl>';
        foreach ( $nutridata as $nutri ){
            $out .= '<dt>' . $nutri['label'] . '</dt>';
            $out .= '<dd>' . $nutri['value'] . '</dd>';
        }
        $out .= '</dl>';
        

        /**
         * Close the nutritional information box
         */
        $out .= '</div>';

        /**
         * Return the rendered data
         */
        return $out;
    }

}
if (!function_exists('the_rpr_recipe_nutrition')) {

    /**
     * Outputs the nutritional data rendered above
     * 
     * @since 0.8.0
     * @param boolean $icons
     */
    function the_rpr_recipe_nutrition($icons = false) {
        echo get_the_rpr_recipe_nutrition($icons);
    }

}

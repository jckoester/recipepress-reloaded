<?php

/*
 * Provides an array with all nutritional data fields
 */
if (!function_exists('get_the_rpr_recipe_nutrition_fields')) {

    function get_the_rpr_recipe_nutrition_fields() {

        return array(
            'calories' => array(
                'dbkey' => 'rpr_recipe_calorific_value',
                'label' => __('Calorific value:', 'recipepress-reloaded'),
                'unit' => 'kcal',
                'value' => NULL,
                'json_ld_id' => 'calories',
                'json_ld_unit' => 'calories'
            ),
            'carbohydrate' => array(
                'dbkey' => 'rpr_recipe_carbohydrate',
                'label' => __('Carbohydrate:', 'recipepress-reloaded'),
                'unit' => 'g',
                'value' => NULL,
                'json_ld_id' => 'carbohydrateContent',
                'json_ld_unit' => 'grams carbohydrates'
            ),
            'sugar' => array(
                'dbkey' => 'rpr_recipe_sugar',
                'label' => __('Sugar:', 'recipepress-reloaded'),
                'unit' => 'g',
                'value' => NULL,
                'json_ld_id' => 'sugarContent',
                'json_ld_unit' => 'grams sugar'
            ),
            'protein' => array(
                'dbkey' => 'rpr_recipe_protein',
                'label' => __('Protein:', 'recipepress-reloaded'),
                'unit' => 'g',
                'value' => NULL,
                'json_ld_id' => 'proteinContent',
                'json_ld_unit' => 'grams protein'
            ),
            'fat' => array(
                'dbkey' => 'rpr_recipe_fat',
                'label' => __('Fat:', 'recipepress-reloaded'),
                'unit' => 'g',
                'value' => NULL,
                'json_ld_id' => 'fatContent',
                'json_ld_unit' => 'grams fat'
            ),
            'fat_unsaturated' => array(
                'dbkey' => 'rpr_recipe_fat_unsaturated',
                'label' => __('Fat (unsaturated):', 'recipepress-reloaded'),
                'unit' => 'g',
                'value' => NULL,
                'json_ld_id' => 'unsaturatedFatContent',
                'json_ld_unit' => 'grams unsaturated fat'
            ),
            'fat_saturated' => array(
                'dbkey' => 'rpr_recipe_fat_saturated',
                'label' => __('Fat (saturated):', 'recipepress-reloaded'),
                'unit' => 'g',
                'value' => NULL,
                'json_ld_id' => 'saturatedFatContent',
                'json_ld_unit' => 'grams saturated fat'
            ),
            'fat_trans' => array(
                'dbkey' => 'rpr_recipe_fat_trans',
                'label' => __('Trans fat:', 'recipepress-reloaded'),
                'unit' => 'g',
                'value' => NULL,
                'json_ld_id' => 'transFatContent',
                'json_ld_unit' => 'grams tran fat'
            ),
            'cholesterol' => array(
                'dbkey' => 'rpr_recipe_cholesterol',
                'label' => __('Cholesterol:', 'recipepress-reloaded'),
                'unit' => 'mg',
                'value' => NULL,
                'json_ld_id' => 'cholesterolContent',
                'json_ld_unit' => 'milligrams cholesterol'
            ),
            'sodium' => array(
                'dbkey' => 'rpr_recipe_sodium',
                'label' => __('Sodium:', 'recipepress-reloaded'),
                'unit' => 'mg',
                'value' => NULL,
                'json_ld_id' => 'sodiumContent',
                'json_ld_unit' => 'milligrams sodium'
            ),
            'fibre' => array(
                'dbkey' => 'rpr_recipe_fibre',
                'label' => __('Fibre:', 'recipepress-reloaded'),
                'unit' => 'g',
                'value' => NULL,
                'json_ld_id' => 'fibreContent',
                'json_ld_unit' => 'grams fibre'
            ),
        );
    }

}

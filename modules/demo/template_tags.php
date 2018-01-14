<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if( !function_exists( 'demo_template_tag' ) ) {

    function demo_template_tag(){
        if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
                $recipe = get_post_custom( );

        $out = '<div class="rpr_module_demo">';
        $out .= '<h2>';
        $out .= __('The demo module is up an running.', 'recipepress-reloaded');
        $out .= '</h2>';
        $out .= '<label for="rpr_recipe_demo_value_field">rpr_recipe_demo_value</label>:&nbsp;';
        $out .= '<span id="rpr_recipe_demo_value_field">' . sanitize_post_field('rpr_recipe_demo_value', $recipe['rpr_recipe_demo_value'][0], $recipe_id) . '</span>';
        $out .= '</div>';
        echo $out;
    }
}
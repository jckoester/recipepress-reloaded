<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
        require_once 'nutrition_data.php';
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


if( !function_exists( 'get_the_rpr_recipe_nutrition_headline' ) ) {
	/**
	 * Render the headline for the recipe times
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_nutrition_headline( $icons=false ){
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Add a third level heading for embedded recipes or a second level 
		 * heading for a standalone recipe
		 */
		if( recipe_is_embedded() ){
			$out .= '<h3>';
		}else{
			$out .= '<h2>';
		}
		
		/**
		 * Add icon if desired
		 */
		if( $icons ){
            $out .= '<i class="fa fa-fire" title=' . __( 'Nutritional data', 'recipepress-reloaded' ) . '></i> ';
		}
		
		$out .= __( 'Nutritional data', 'recipepress-reloaded' );
		
		if( recipe_is_embedded() ){
			$out .= '</h3>';
		}else{
			$out .= '</h2>';
		}
		/**
		 * Return the rendered headline
		 */
		return $out;
	}
}
if( !function_exists( 'the_rpr_recipe_nutrition_headline' ) ) {
	/**
	 * Outputs the headline rendered above
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_nutrition_headline( $icons = false ){
		echo get_the_rpr_recipe_nutrition_headline( $icons );
	}
}
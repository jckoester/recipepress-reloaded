<?php

/**
 * The admin-specific nutrtional metadata functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The admin-specific nutritional metadata functionality of the plugin.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Admin_NutritionMeta {

    /**
     * The version of this plugin.
     *
     * @since    0.8.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    0.8.0
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $version ) {

        $this->version = $version;
    }
    
	/**
     * Add a metabox for details like serving size and times
     * 
     * @since 0.8.0
     */
    public function metabox_nutrition() {
        // Add advanced metabox for nutritional information
        add_meta_box(
    		'recipe_nutrition_meta_box',
    		__( 'Nutritional information', 'recipepress-reloaded' ),
    		array( $this, 'do_metabox_nutrition' ),
    		'rpr_recipe',
    		'normal',
    		'high'
    	);
    }
    
    public function do_metabox_nutrition( $recipe ) {
    	include( 'views/rpr-metabox-nutrition.php');
    }

	
	/**
     * Saves the nutritional meta data of a recipe to the database
     * 
     * @param int $recipe_id
     * @param array $ingredients
     * @since 0.8.0
     */
    public function save_nutritionalmeta( $recipe_id, $data, $recipe = NULL ){
		$fields = array(
			'rpr_recipe_calorific_value',
			'rpr_recipe_protein',
			'rpr_recipe_fat',
			'rpr_recipe_carbohydrate',
			'rpr_recipe_nutrition_per'
		);
		
		foreach( $fields as $key ){
			if( isset( $data[$key] ) ){
				$old = get_post_meta( $recipe_id, $key, true );
				$new = $data[$key];
				
				if ( $new != $old ){
	    			update_post_meta( $recipe_id, $key, $new );
	    		} elseif ( $new == '' && $old ) {
	    			delete_post_meta( $recipe_id, $key, $old );
	    		}
			}
		}
    }
}
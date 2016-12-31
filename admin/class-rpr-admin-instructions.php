<?php

/**
 * The admin-specific instruction functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The admin-specific indtruction functionality of the plugin.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Admin_Instructions {

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
     * Add the instructions meta box
     * 
     * @since 0.8.0
     */
    public function metabox_instructions() {
        add_meta_box(
            'recipe_instructions_meta_box',
            __( 'Instructions', 'recipepress-reloaded' ),
            array( $this, 'do_metabox_instructions' ),
            'rpr_recipe',
            'normal',
            'high'
    	);
    }
    
    public function do_metabox_instructions( $recipe ) {
        include 'views/rpr-metabox-instructions.php';
    }
    
    /**
     * Saves the ingredients of a recipe to the database
     * Ingredients are saved as an array of metadata containing amount unit, 
     * ingredient id, notes and link
     * Additionally a term relation is saved for each ingredient
     * 
     * @param int $recipe_id
     * @param array $ingredients
     * @since 0.8.0
     */
    public function save_instructions( $recipe_id, $instructions ){
        // A new array to contain all non empty line from the form
        $non_empty = array();
        
        foreach ( $instructions as $ins ){
            // Check if we have a instructions group or a instructions line
            if( isset( $ins['grouptitle'] ) ){
                // we have a ingredient group title line
                // We do nothing and will save this line as is to the recipe meta data
                if( $ins['grouptitle'] != "") {
                    $non_empty[] = $ins;
                }
            } else { 
                // we have a single ingredient line
                if( $ins['description'] != "" || $ins['image'] != '' ){
                    $non_empty[] = $ins;
                }
            }
        }
        
        // Save the new meta data array:
        update_post_meta( $recipe_id, 'rpr_recipe_instructions', $non_empty );     
    }
}
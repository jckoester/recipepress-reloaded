<?php

/**
 * The admin-specific ingredient functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The admin-specific ingredient functionality of the plugin.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Admin_Ingredients {

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
     * Hide the ingredients metabox (default) and add the special one
     * 
     * @since 0.8.0
     */
    public function metabox_ingredients() {
        // Remove default meta box from side
        remove_meta_box( 'tagsdiv-rpr_ingredient', 'rpr_recipe', 'side' );
        // Add advanced metabox for ingredients
        add_meta_box(
    		'recipe_ingredients_meta_box',
    		__( 'Ingredients', 'recipepress-reloaded' ),
    		array( $this, 'do_metabox_ingredients' ),
    		'rpr_recipe',
    		'normal',
    		'high'
    	);
    }
    
    public function do_metabox_ingredients( $recipe ) {
    	include( 'views/rpr-metabox-ingredients.php');
    }
    
    /**
     * Get a list of all available units from the options. 
     * @since 0.8.0
     */
    
    public function get_the_ingredient_unit_selection( $selected="" ) {
        $units = AdminPageFramework::getOption( 'rpr_options', array( 'units', 'ingredient_units') , false );
        $outp = "";
        
        foreach ( $units as $key=>$unit ){
            $outp .= '<option value="' . $unit . '"';
            if( $unit == $selected ) { $outp .= ' selected="selected" '; }
            $outp .= '>' . $unit . '</option>' . "\n";
        }
        if( ! in_array( $selected, $units )){
            $outp .= '<option value="' . $selected . '"  selected="selected" >' . $selected . '</option>\n';
        }
        return $outp;
    }
    
    public function the_ingredient_unit_selection( $selected="" ) {
        echo $this->get_the_ingredient_unit_selection( $selected );
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
    public function save_ingredients( $recipe_id, $ingredients ){
        // A new array to contain all non empty line from the form
        $non_empty = array();
        // An array of all ingredient term_ids to create a relation to the recipe
        $ingredient_tax = array();
        
        foreach ( $ingredients as $ing ){
            // Check if we have a ingredients group or a ingredient
            if( isset( $ing['grouptitle'] ) ){
                // we have a ingredient group title line
                // We do nothing and will save this line as is to the recipe meta data
                if( $ing['grouptitle'] != "") {
                    $non_empty[] = $ing;
                }
            } else {
                // we have a single ingredient line
                if( $ing['ingredient'] != "" ){
                    // We need to find the term_id of the ingredient and add a taxonomy relation to the recipe
                    $term = term_exists($ing['ingredient'], 'rpr_ingredient');
                    if( $term === 0 || $term === null ){
                        // ingredient is not an existing term, create it:
                        $term = wp_insert_term($ing['ingredient'], 'rpr_ingredient');
                    }
                    // Now we have a valid term id!
                    $term_id = intval($term['term_id']);
                    // Set it to the ingredient array:
                    $ing['ingredient_id'] = $term_id;
                    // add it to the taxonomy list:
                    $ingredient_tax[] = $term_id;
                    // Add it to the save list:
                    $non_empty[] = $ing;
                }
            }
        }
        // Save the recipe <-> ingredient taxonomy relation
        wp_set_post_terms( $recipe_id, $ingredient_tax, 'rpr_ingredient' );
        // Save the new meta data array:
        update_post_meta( $recipe_id, 'rpr_recipe_ingredients', $non_empty );     
    }
}
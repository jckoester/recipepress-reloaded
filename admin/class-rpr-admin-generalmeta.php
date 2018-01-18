<?php

/**
 * The admin-specific general metadata functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The admin-specific general metadata functionality of the plugin.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Admin_GeneralMeta {

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
     * Move post image metabox to the top
     * 
     * @since 0.8.0
     */
    public function metabox_postimage() {
        remove_meta_box( 'postimagediv', 'rpr_recipe', 'side' );
        add_meta_box( 'postimagediv', __( 'Recipe photo', 'recipepress-reloaded' ), 'post_thumbnail_meta_box', 'rpr_recipe', 'side', 'high' );
    }
	
    /**
     * Add a metabox for details like serving size and times
     * 
     * @since 0.8.0
     */
    public function metabox_details() {
        // Remove default meta box from side
        // remove_meta_box( 'tagsdiv-rpr_ingredient', 'rpr_recipe', 'side' );
        // Add advanced metabox for general information
        add_meta_box(
    		'recipe_details_meta_box',
    		__( 'General information', 'recipepress-reloaded' ),
    		array( $this, 'do_metabox_details' ),
    		'rpr_recipe',
    		'side',
    		'high'
    	);
    }
    
    public function do_metabox_details( $recipe ) {
    	include( 'views/rpr-metabox-details.php');
    }
    
	/**
	 * Add a metabox for description
	 * 
	 * @since 0.8.0
	 */
	public function metabox_description() {
		// Add editor metabox for description
        add_meta_box(
    		'recipe_description_meta_box',
    		__( 'Description', 'recipepress-reloaded' ),
    		array( $this, 'do_metabox_description' ),
    		'rpr_recipe',
    		'normal',
    		'high'
    	);
	}
	
	public function do_metabox_description( $recipe ) {
		$description = get_post_meta( $recipe->ID, "rpr_recipe_description", true );
		$options = array(
			'textarea_rows' => 4
		);
	    $options['media_buttons'] = true;

		wp_editor( $description, 'rpr_recipe_description',  $options );
	}
	
	/**
	 * Add a metabox for notes
	 * 
	 * @since 0.8.0
	 */
	public function metabox_notes() {
		// Add editor metabox for description
        add_meta_box(
    		'recipe_notes_meta_box',
    		__( 'Notes', 'recipepress-reloaded' ),
    		array( $this, 'do_metabox_notes' ),
    		'rpr_recipe',
    		'normal',
    		'high'
    	);
	}
	
	public function do_metabox_notes( $recipe ) {
		$description = get_post_meta( $recipe->ID, "rpr_recipe_notes", true );
		$options = array(
			'textarea_rows' => 4
		);
	    $options['media_buttons'] = true;

		wp_editor( $description, 'rpr_recipe_notes',  $options );
	}
        
        /**
         * Get a list of all available units from the options. 
         * @since 0.8.0
         */
        public function get_the_serving_unit_selection( $selected="" ) {
            $units = AdminPageFramework::getOption( 'rpr_options', array( 'units', 'serving_units') , false );
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
    
        public function the_serving_unit_selection( $selected="" ) {
            echo $this->get_the_serving_unit_selection( $selected );
        }

	
	/**
     * Saves the general meta data of a recipe to the database
     * 
     * @param int $recipe_id
     * @param array $ingredients
     * @since 0.8.0
     */
    public function save_generalmeta( $recipe_id, $data, $recipe = NULL ){
		$fields = array(
			'rpr_recipe_servings',
			'rpr_recipe_servings_type',
			'rpr_recipe_prep_time',
			'rpr_recipe_cook_time',
			'rpr_recipe_passive_time',
			'rpr_recipe_notes'
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
		
		// Saving description not only to the post_meta field but also to excerpt and content
		if( isset( $data['rpr_recipe_description'] ) ) {
			update_post_meta( $recipe_id, 'rpr_recipe_description', $data['rpr_recipe_description'] );
			// Set Excerpt:
			$recipe->post_content = $data['rpr_recipe_description'];
			$recipe->post_excerpt = $data['rpr_recipe_description'];
			wp_update_post($recipe);
			//die;
		}
    }
}
<?php

/**
 * The base class for modules to RecipePress reloaded
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      1.0.0
 *
 * @package    recipepress-reloaded
 */
abstract class RPR_Module {

    /**
     * Include required files
     * This method should be overwritten by the child classes
     * It's here as a fallback
     * @since 1.0.0
     */
    abstract public function load_dependencies();

    /**
     * Register all of the hooks related to the admin area functionality
     * of the module.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    abstract public function define_admin_hooks($loader);

    /**
     * Register all of the hooks related to the public area functionality
     * of the module.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    abstract public function define_public_hooks($loader);


    
    /**
     * A procedure to check, wether recipe data can be safed
     * 
     * @since 1.0.0
     * @todo Maybe better take the nonce by name from $_POST ?
     * @param type $recipe      The recipe post object
     * @param type $nonce       The nonce from post data
     * @param type $nonce_id    The nonce_id
     * @return boolean
     */
    protected function check_before_saving( $recipe=NULL, $nonce="", $nonce_id="" ) {
        $errors = false;

        // First check if there is a post object of the correct type
        if ( $recipe !== NULL && $recipe->post_type === 'rpr_recipe' ) {

            // check, if the user is allowed to save recipes:
            if (!current_user_can('edit_post', $recipe->ID)) {
                $errors = true;
                $this->report_error( "There was an error saving the recipe. No sufficient rights." );
            }
            
            // Check nonce
            if ( $nonce != "" && $nonce_id != "" && !wp_verify_nonce( $nonce, $nonce_id ) ) {
                $errors = true;
                $this->report_error( "There was an error saving the recipe. Nonce with id \'$nonce_id\' not verified" );
            }
            
            // Save error message, if any:
            if ( $errors ) {
                return false;
            } else {
                return true;
            }
        } else {
            // This is not a recipe! This class is not responsible to handle this!
            return false;
        } 
    }

    /**
     * A procedure to save post_meta for a recipe
     * 
     * @since 1.0.0
     * @todo: Verify the sanitation works also for urls, links, complex fields, ingredients...
     * @param type $fields  ids of all fieds that should be saved
     * @param type $data    Post data from form. Can be modified by calling function already
     * @param type $recipe  The recipe object
     */
    protected function save_fields( $fields = array(), $data=NULL, $recipe = NULL ){
        if( is_array( $data ) && $recipe !== NULL && $recipe->post_type === 'rpr_recipe' ){
            foreach( $fields as $key ){
		if( isset( $data[$key] ) ){
                    $old = get_post_meta( $recipe->ID, $key, true );
                    $new = sanitize_text_field( $data[$key] );
                    
                    if ( $new != $old ){
                        // Value has changed, update
                        update_post_meta( $recipe->ID, $key, $new );
                    } elseif ( $new == '' && $old ) {
                        // Value has vanished, delete!
	    		delete_post_meta( $recipe->ID, $key, $old );
                    }
		}
            }
        } else {
            $this->report_error( __( "There was an error while saving. Function 'save_fields' called without the neccessary data", 'recipepress-reloaded' ) );
        }
		
    }
    
    /**
     * A procedure to report an error and save it to the options.
     * Saved errors will be displayed in the backend by a core hook
     * 
     * @since 1.0.0
     * @param type $error
     */
    protected function report_error( $error = false ){
        // If we've got an error message:
        if( $error ){
            // Fetch saved errors
            $errors = get_option( 'rpr_admin_errors' );
            // append the new error
            $errors = $errors . "\n" . esc_html( $error );
            // Write back the error string
            update_option('rpr_admin_errors', $errors);
        }
    }
}

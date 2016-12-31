<?php

/**
 * Fired during plugin deinstallation
 *
 * @link       http://tech.cbjck.de/wp-plugins/rpr/
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/includes
 */

/**
 * Fired during plugin deinstallation.
 *
 * This class defines all code necessary to run during the plugin's deinstallation.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/includes
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Uninstaller {

    /**
     * Cleaning up wordpress on plugin deinstallation
     *
     * @since    0.8.0
     */
    public static function uninstall() {           
            // Remove all custom taxonomies:
            $options = get_option( 'rpr_options' );
            foreach( $options['tax_custom'] as $taxonomy ){
                self::delete_taxonomy($taxonomy['slug'] );
            }
            // Remove all ingredients:
            self::delete_taxonomy( 'rpr_ingredient' );
            
            // Remove all posts of type rpr_recipe:
             self::delete_recipes();
            
            // Remove all option values:
            delete_option( 'rpr_options' );
            delete_option( 'rpr_version' );
            delete_option( 'rpr_dbversion' );
            delete_option( 'rpr_update_needed' );
            delete_option( 'rpr_install_sample_data');
            
            return;
	}
    
    /**
     * Deletes all terms of a given taxonomy and unregisters the taxonomy itself.
     * 
     * @since 0.8.0
     * @param type $taxonomy
     * 
     */
    private static function delete_taxonomy( $taxonomy ) {
        if( taxonomy_exists( $taxonomy ) ){
            // First delete all terms of this taxonomy
            $terms = get_terms( array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ) );
            if( is_array( $terms ) ){
                foreach ( $terms as $term ){
                    wp_delete_term( $term->term_id, $taxonomy );
                }
            }
            // Then delete the taxonomy itself
            unregister_taxonomy( $taxonomy );
            
            return;
        }
    }
    
    /**
     * Deletes all posts of type rpr_recipe
     * 
     * @since 0.8.0
     * @global type $post
     * @return type none
     */
    private static function delete_recipes() {
        $args = array(
            'post_type' => 'rpr_recipe',
            'posts_per_page' => -1,
        );

        $query = new WP_Query( $args );

        if( $query->have_posts() ) { //recipes found
            while( $query->have_posts() ) {
                $query->the_post();
                global $post;
                wp_delete_post( $post->ID, true);
            }
        }

        return;
    }
    
    

}

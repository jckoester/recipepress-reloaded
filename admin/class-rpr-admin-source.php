<?php

/**
 * The admin-specific source metadata functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.9.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The admin-specific source metadata functionality of the plugin.
 *
 * @since      0.9.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Admin_Source {

    /**
     * The version of this plugin.
     *
     * @since    0.9.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.9.0
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $version ) {
        $this->version = $version;
    }

    /**
     * Add a metabox for source details (and link)
     * 
     * @since 0.9.0
     */
    public function metabox_source() {
        // Add advanced metabox for source information
        add_meta_box(
                'recipe_source_meta_box', 
                __( 'Source', 'recipepress-reloaded' ), 
                array ( $this, 'do_metabox_source' ), 
                'rpr_recipe', 
                'normal', 
                'high'
        );
    }

    public function do_metabox_source( $recipe ) {
        include( 'views/rpr-metabox-source.php');
    }

    /**
     * Saves the general meta data of a recipe to the database
     * 
     * @param int $recipe_id
     * @param array $ingredients
     * @since 0.9.0
     */
    public function save_sourcemeta( $recipe_id, $data, $recipe = NULL ) {
        $fields = array (
            'rpr_recipe_source',
            'rpr_recipe_source_link'
        );
        foreach ( $fields as $key ) {
            if ( isset( $data[ $key ] ) ) {
                $old = get_post_meta( $recipe_id, $key, true );
                $new = $data[ $key ];
                if ( $new != $old ) {
                    update_post_meta( $recipe_id, $key, $new );
                } elseif ( $new == '' && $old ) {
                    delete_post_meta( $recipe_id, $key, $old );
                }
            }
        }
    }

}

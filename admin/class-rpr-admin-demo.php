<?php

/**
 * The admin-specific functionality to install demo data.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The admin-specific migration functionality of the plugin to install demo data.
 *
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Admin_Demo {

    /**
     * The version of this plugin.
     *
     * @since   0.8.0
     * @access  private
     * @var     string  $version    The current version of this plugin.
     */
    private $version;

    /**
     * The database version of the plugin files. Used to compare with the
     * version number saved in the database to decide if a database update is 
     * necessary
     * 
     * @since   0.8.0
     * @access  private
     * @var     int   $dbversion  The database version of the plugin file.
     */
    private $dbversion;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.8.0
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $version, $dbversion ) {

        $this->version = $version;
        $this->dbversion = $dbversion;
    }

    /**
     * Create a demo data installation notice
     * We don't want to do a database change without the user knowing about it
     * 
     * @since 0.8.0
     */
    public function notice_demo() {
        if ( get_option( 'rpr_install_sample_data' ) ) {
            echo '<div class="updated notice is-dismissible"><p>';
            echo '<img src="' . plugins_url() . '/' . 'recipepress-reloaded/img/logo.png' . ' " width=48px" style="float:left; margin-right:8px;" />';
            echo '<b>';
            _e( 'Welcome to RecipePress reloaded.', 'recipepress-reloaded' );
            echo '</b><br/>';
            printf( __( 'It seems to be the first time you\'re using this plugin. Have a look at the <a href="%1$s" target="_blank">documentation</a> (currently being built up) to get some inspiration how to use this plugin. If you hit a problem don\'t hesitate to ask for help at the <a href="%2$s" target="_blank">support forum.</a>', 'recipepress-reloaded' ), 'https://github.com/dasmaeh/recipepress-reloaded/wiki', 'https://wordpress.org/support/plugin/recipepress-reloaded' );
            echo '<br/>';
            printf( '<a href="%1$s">' . __( 'Dismiss', 'recipepress-reloaded' ) . '</a>', '?post_type=rpr_recipe&rpr_do_install_samples=0' );
            /**
             * Temporary solution to disable the dialog again
             */
            /**
             * Extension of the dialog to provide installation of sample data
             * 
             * echo '<br/>';
              _e( 'To make your start easier, we also provide some sample data. You can use these data to play around and get used to the plugin.', 'recipepress-reloaded' );
              echo '<br/>';
              printf ( __( '<a href="%1$s">Install sample data</a>', 'recipepress-reloaded'), '?post_type=rpr_recipe&rpr_do_install_samples=1' );
              echo '&nbsp;|&nbsp;';
              printf ( __( '<a href="%1$s">No thanks</a>', 'recipepress-reloaded'), '?post_type=rpr_recipe&rpr_do_install_samples=0' );
             * 
             */
            echo '</p></div>';
        }
    }

    /**
     * Do the installation of sample data, if the user want's to
     * 
     * @since 0.9.0
     */
    public function rpr_do_install_samples() {
        // Check if the user has actively allowed the update:
        if ( isset( $_GET[ 'rpr_do_install_samples' ] ) ) {
            if ( $_GET[ 'rpr_do_install_samples' ] == '1' ) {
                // Do something here!
                /*
                 * IDEA: Set $_POST for a complete recipe, then send to the 
                 * normal save procedure
                 */
                // In the end: remove the dialog flag and set the database version
                //delete_option( 'rpr_install_sample_data' );
                //update_option( 'rpr_dbversion', $this->dbversion );
            } else {
                // Do nothing, but remove the dialog!
                delete_option( 'rpr_install_sample_data' );
                update_option( 'rpr_dbversion', $this->dbversion );
            }
        }
    }

    /**
     * Install some basic options and prediefined units and taxonomies on first activation
     * 
     * @since 0.8.0
     */
    public function do_install_base_options() {

        if ( get_option( 'rpr_first_install' ) && get_option( 'rpr_first_install' ) == 1 ) {
            $base_options = array (
                'general' => array (
                    'slug' => __( 'recipe', 'recipepress-reloaded' )
                ),
                'tax_custom' => array (
                    array (
                        'tab_title' => __( 'Course', 'recipepress-reloaded' ),
                        'singular' => __( 'Course', 'recipepress-reloaded' ),
                        'plural' => __( 'Courses', 'recipepress-reloaded' ),
                        'hierarchical' => true,
                        'filter' => false,
                        'table' => false,
                        'slug' => __( 'course', 'recipepress-reloaded' )
                    ),
                    array (
                        'tab_title' => __( 'Cuisine', 'recipepress-reloaded' ),
                        'singular' => __( 'Cuisine', 'recipepress-reloaded' ),
                        'plural' => __( 'Cuisines', 'recipepress-reloaded' ),
                        'hierarchical' => true,
                        'filter' => false,
                        'table' => false,
                        'slug' => __( 'cuisine', 'recipepress-reloaded' )
                    ),
                    array (
                        'tab_title' => __( 'Season', 'recipepress-reloaded' ),
                        'singular' => __( 'Season', 'recipepress-reloaded' ),
                        'plural' => __( 'Seasons', 'recipepress-reloaded' ),
                        'hierarchical' => false,
                        'filter' => false,
                        'table' => false,
                        'slug' => __( 'season', 'recipepress-reloaded' )
                    ),
                    array (
                        'tab_title' => __( 'Difficulty', 'recipepress-reloaded' ),
                        'singular' => __( 'Difficulty', 'recipepress-reloaded' ),
                        'plural' => __( 'Difficulties', 'recipepress-reloaded' ),
                        'hierarchical' => false,
                        'filter' => false,
                        'table' => false,
                        'slug' => __( 'difficulty', 'recipepress-reloaded' )
                    )
                ),
                'units' => array (
                    'ingredient_units' => array (
                        __( 'cup', 'recipepress-reloaded' ),
                        __( 'bottle', 'recipepress-reloaded' ),
                        __( 'can', 'recipepress-reloaded' ),
                        __( 'jar', 'recipepress-reloaded' ),
                        __( 'mL', 'recipepress-reloaded' ),
                        __( 'L', 'recipepress-reloaded' ),
                        __( 'g', 'recipepress-reloaded' ),
                        __( 'kg', 'recipepress-reloaded' ),
                        __( 'dash', 'recipepress-reloaded' ),
                        __( 'tsp.', 'recipepress-reloaded' ),
                        __( 'tbsp.', 'recipepress-reloaded' ),
                        __( 'some', 'recipepress-reloaded' )
                    ),
                    'serving_units' => array (
                        __( 'servings', 'recipepress-reloaded' ),
                        __( 'liter', 'recipepress-reloaded' ),
                        __( 'pieces', 'recipepress-reloaded' )
                    )
                )
            );
            update_option( 'rpr_options', $base_options );
            delete_option( 'rpr_first_install' );
        }
    }

}
